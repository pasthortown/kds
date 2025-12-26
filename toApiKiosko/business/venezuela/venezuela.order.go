package venezuela

import (
	"bytes"
	"database/sql"
	"encoding/json"
	"errors"
	"fmt"
	"io"
	"lib-shared/protos/lib_gen_proto"
	"lib-shared/utils"
	"lib-shared/utils/logger"
	"net/http"
	"new-order-store/internals/domain/business/venezuela/printservice"
	"new-order-store/internals/domain/execute"
	featureflag2 "new-order-store/internals/entity/enums/featureflag"
	"new-order-store/internals/entity/maxpoint"
	"new-order-store/internals/entity/maxpoint/credential"
	"new-order-store/internals/entity/models"
	"new-order-store/internals/infrastructure/cache"
	"new-order-store/internals/infrastructure/featureflag"
	"new-order-store/internals/infrastructure/natsmodule"
	"new-order-store/internals/infrastructure/natsmodule/events"
	"new-order-store/internals/infrastructure/sqlserver"
	"new-order-store/internals/utils/validatorsql"
	"runtime/debug"
	"strings"
	"time"
)

type KioskoPlus struct {
	IdCabeceraOrdenPedido string
	IdDetalleOrdenPedido  string
	IdOrderDetail         int32  `json:"idDetalleOrden"` // idKOP
	PluId                 uint32 `json:"pluId"`
	Quantity              uint32 `json:"cantidad"`
	Exchange              *int32 `json:"modifica"`
	isDetails             int32
	ProductBase           *string `json:"productoBase"`
	Comment               *string `json:"comentario"`
	ValorNeto             *string
	ValorBruto            *string
	ValorIva              *string
	RelatedProductId      *string `json:"RelatedProductId"`
}

type OrderStore struct {
	DatabaseCredential *credential.DatabaseCredential
	Order              *lib_gen_proto.Order
	StoreData          *maxpoint.StoreData
	Feature            *featureflag.FeatureFlag
	NatsClient         *natsmodule.NatsStarter
	regionalKiosk      execute.RegionalKioskExecute
}

var cacheStation = cache.NewTTL[string, *models.StationKiosco]()
var cacheIdMesa = cache.NewTTL[string, string]()

func NewOrderStore(
	DatabaseCredential *credential.DatabaseCredential,
	Order *lib_gen_proto.Order,
	StoreData *maxpoint.StoreData,
	Feature *featureflag.FeatureFlag,
	NatsClient *natsmodule.NatsStarter,
	regionalKiosk execute.RegionalKioskExecute,
) execute.OrderExecutorSql {

	return &OrderStore{
		DatabaseCredential: DatabaseCredential,
		Order:              Order,
		StoreData:          StoreData,
		Feature:            Feature,
		NatsClient:         NatsClient,
		regionalKiosk:      regionalKiosk,
	}
}

func (o *OrderStore) CreateOrderKioskEfectivo() error {
	orderId := o.Order.Cabecera.OrderId
	statusMxp := o.StoreData.Status
	connection, err := sqlserver.NewConnectionSql(o.DatabaseCredential)
	if err != nil {
		return err
	}
	defer func() {
		connection.Close()
	}()
	var cfacId *string
	documentId := o.Order.Cabecera.Client.DocumentId
	//obtengo los datos de la estacion
	resultStation, err := o.GetStationKiosko(connection)
	if err != nil {
		return err
	}
	err = o.CheckIfAlreadyExistOrder(connection, orderId)
	if err != nil {
		return err
	}
	generateSecuencial := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "facturacion.Kiosko_generarSecuencialFactura")
	generateSecuencial.AddValueParameterized("idRestaurante", o.StoreData.RestaurantId)
	rows, err := connection.IQuery(generateSecuencial.GetStoreProcedure(), generateSecuencial.GetValues())
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al ejecutar el sp facturacion.NewKiosko_generarSecuencialFactura: %v", err)
	}
	defer rows.Close()
	for rows.Next() {
		err = rows.Scan(&cfacId)
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al obtener los datos de NewKiosko_generarSecuencialFactura: %v", err)
		}
	}
	if cfacId == nil {
		return fmt.Errorf("[venezuela.order.go]Error - El cfacId generado del sp [facturacion].[KIOSKO_generarSecuencialFactura] esta vacio, por favor revisar")
	}
	idTypeDocument := o.StoreData.GetTipoDocumento(documentId)
	if idTypeDocument == nil {
		return fmt.Errorf("[venezuela.order.go]No se encontro el tipo de documento para el id %s", documentId)
	}
	if strings.EqualFold(idTypeDocument.Description, "CONSUMIDOR FINAL") {
		o.Order.Cabecera.Client.Email = "consumidor.final@kfc.com.ec"
		o.Order.Cabecera.Client.Address = "Quito"
		o.Order.Cabecera.Client.DocumentNumber = "9999999999"
		o.Order.Cabecera.Client.Phone = "2222222"
	}

	// ECUADOR
	idUserPos, err := o.GetIdUserPos(connection, resultStation)
	if err != nil {
		return err
	}
	if idUserPos == nil {
		return fmt.Errorf("[venezuela.order.go]No se encontro el id del usuario para kiosko")
	}
	idControlStation, err := o.GetStationControl(connection, resultStation)
	if err != nil {
		return err
	}
	if idControlStation == nil {
		return fmt.Errorf("[venezuela.order.go]La estacion de kiosko no se encuentra activa")
	}
	idPeriod, err := o.GetOpenPeriod(connection)
	if err != nil {
		return err
	}
	if idPeriod == nil {
		return fmt.Errorf("[venezuela.order.go]No se encontro ningun periodo abierto")
	}
	idMesa, err := o.GetIdMesa(connection, resultStation)
	if err != nil {
		return err
	}
	newTotalBill, _ := utils.StrToFloat32(o.Order.PaymentMethods.TotalBill)
	if newTotalBill > 0 {
		err = connection.CreateBegin()
		defer connection.Rollback()
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al crear BEGIN: %v", err.Error())
		}
		_, datailsKiosko, err := o.InsertOrderKiosko(connection, cfacId)
		if err != nil {
			return err
		}
		queryUpdateOrderPedido := fmt.Sprintf(`UPDATE dbo.Cabecera_Orden_Pedido
								SET IDStatus = @idEstadoOrdenCerrada
								WHERE IDMesa = @idMesa
									AND IDEstacion = @idEstacion
									AND IDPeriodo = @idPeriodo
									AND IDStatus = @idEstadoOrdenPendiente`)
		exec, err := connection.Exec(queryUpdateOrderPedido,
			sql.Named("idEstadoOrdenCerrada", statusMxp.OrdenPedidoCerrada),
			sql.Named("idMesa", idMesa),
			sql.Named("idEstacion", resultStation.IdStation),
			sql.Named("idPeriodo", idPeriod),
			sql.Named("idEstadoOrdenPendiente", statusMxp.OrdenPedidoPendiente))
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al insertar el update BEGIN: %v", err.Error())
		}
		_, err = exec.RowsAffected()
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al actualizar la tabla Cabecera_Orden_Pedido %s", err.Error())
		}
		insertCabeceraOrdenPedido := fmt.Sprintf(`INSERT INTO 
    		dbo.Cabecera_Orden_Pedido (IDCabeceraOrdenPedido,
    		                           odp_total,
    		                           odp_fecha_creacion,
    		                           odp_observacion,
    		                           odp_varchar1,
    		                           odp_varchar2,
    		                           odp_varchar3,
    		                           odp_varchar4,
    		                           odp_date1,
    		                           odp_float1,
    		                           odp_num_personas,
    		                           IDMotivoAnulacion,
    		                           IDMesa,
    		                           IDUsersPos,
    		                           IDEstacion,
    		                           IDPeriodo,
    		                           IDStatus,
    		                           replica,
    		                           IDControlEstacion,
    		                           Cabecera_Orden_PedidoDecimal1,
    		                           Cabecera_Orden_PedidoDecimal2,
    		                           Cabecera_Orden_PedidoDecimal3,
    		                           Cabecera_Orden_PedidoDecimal4,
    		                           Cabecera_Orden_PedidoDate1,
    		                           Cabecera_Orden_PedidoDate2,
    		                           Cabecera_Orden_PedidoVarchar1,
    		                           Cabecera_Orden_PedidoVarchar2,
    		                           Cabecera_Orden_PedidoVarchar3,
    		                           Cabecera_Orden_PedidoVarchar4,
    		                           Cabecera_Orden_PedidoID1,
    		                           Cabecera_Orden_PedidoID2) 
    								OUTPUT CAST(INSERTED.IDCabeceraOrdenPedido AS NVARCHAR(40)) 
    								VALUES (NEWID(),
    								    1, 
    								    GETDATE(), @cliNombresKiosko + ' - ' + @tipoServicioKiosko, 
    								    NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 
    								    @idMesa, 
    								    @idUser, 
    								    @idEstacion, 
    								    @idPeriodo, 
    								    @idEstadoOrdenPendiente, 
    								    0, 
    								    NULL, NULL, NULL, NULL, NULL, NULL, NULL, @idUser, NULL, NULL, NULL, NULL, NULL)`)
		insertData := connection.QueryRow(insertCabeceraOrdenPedido,
			sql.Named("cliNombresKiosko", o.Order.Cabecera.Client.Name), sql.Named("tipoServicioKiosko", o.Order.Cabecera.TypeService),
			sql.Named("idMesa", idMesa), sql.Named("idUser", idUserPos),
			sql.Named("idEstacion", resultStation.IdStation), sql.Named("idPeriodo", idPeriod),
			sql.Named("idEstadoOrdenPendiente", statusMxp.OrdenPedidoPendiente))
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al insertar los datos en la tabla Cabecera_Orden_Pedido %v : %v", insertCabeceraOrdenPedido, err.Error())
		}
		var idCabeceraOrdenPedido string
		err = insertData.Scan(&idCabeceraOrdenPedido)
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al obtener el id de la Cabecera_Orden_Pedido: %v", err.Error())
		}

		//insercion de los productos a la tabla detalle_orden_pedido
		err = o.InsertDetailOrder(connection, datailsKiosko, idCabeceraOrdenPedido)
		if err != nil {
			return err
		}

		//Insercion a la factura para efectivo
		if o.Order.Discount != nil {

		} else {
			invoicing := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "facturacion.IAE_Fac_InsertFactura_kioskoEfectivo")
			invoicing.AddValueParameterized("cfac_id", cfacId)
			invoicing.AddValueParameterized("idRestaurante", o.StoreData.RestaurantId)
			invoicing.AddValueParameterized("idCabeceraOrdenPedido", idCabeceraOrdenPedido)
			invoicing.AddValueParameterized("idUserPos", idUserPos)
			invoicing.AddValueParameterized("idEstacion", resultStation.IdStation)
			invoicing.AddValueParameterized("idPeriodo", idPeriod)
			invoicing.AddValueParameterized("idControlEstacion", idControlStation)
			invoicing.AddValueParameterized("tipoOrden", "KIOSKO")
			_, err = connection.IQuery(invoicing.GetStoreProcedure(), invoicing.GetValues())
			if err != nil {
				return fmt.Errorf("[venezuela.order.go]Error al ejecutar el sp facturacion.IAE_Fac_InsertFactura_kioskoEfectivo:%v", err)
			}
		}

		//Actualizacion de la orden en la tabla detalle_orden_pedido
		update := "UPDATE dbo.Detalle_Orden_Pedido SET dop_impresion = 0 WHERE IDCabeceraOrdenPedido = @idCabeceraOrdenPedido and dop_cuenta = 1 and dop_anulacion = 1 and dop_impresion = -1"
		_, err = connection.Exec(update, sql.Named("idCabeceraOrdenPedido", idCabeceraOrdenPedido))
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]No se pudo ejecutar el UPDATE %s con idCabeceraOrdenPedido:%s", update, idCabeceraOrdenPedido)
		}
		update = "UPDATE dbo.Detalle_Orden_Pedido SET dop_impresion = -1 WHERE IDCabeceraOrdenPedido = @idCabeceraOrdenPedido and dop_cuenta = 1 and dop_anulacion = 1 and dop_impresion = 1"
		_, err = connection.Exec(update, sql.Named("idCabeceraOrdenPedido", idCabeceraOrdenPedido))
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]No se pudo ejecutar el UPDATE %s con idCabeceraOrdenPedido:%s", update, idCabeceraOrdenPedido)
		}
		update = "UPDATE dbo.Detalle_Orden_Pedido SET dop_estado = -1 WHERE IDCabeceraOrdenPedido = @idCabeceraOrdenPedido and dop_cuenta = 1 and dop_anulacion <> 0 and dop_impresion = 1"
		_, err = connection.Exec(update, sql.Named("idCabeceraOrdenPedido", idCabeceraOrdenPedido))
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]No se pudo ejecutar el UPDATE %s con idCabeceraOrdenPedido:%s", update, idCabeceraOrdenPedido)
		}

		//Obtencion de datos para la impresion de orden
		dataOrderPrintFastFood := make([]*models.KioskoImpresionOrdenPedidoFastFood, 0)
		orderPrintFastFood := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[pedido].[ORD_impresion_ordenpedido]")
		orderPrintFastFood.AddValueParameterized("IDCabeceraOrdenPedido", idCabeceraOrdenPedido)
		orderPrintFastFood.AddValueParameterized("IDUsersPos", idUserPos)
		orderPrintFastFood.AddValueParameterized("rst_id", o.StoreData.RestaurantId)
		orderPrintFastFood.AddValueParameterized("dop_cuenta", 1)
		orderPrintFastFood.AddValueParameterized("guardaOrden", 1)
		orderPrintFastFood.AddValueParameterized("todas", 0)
		rowOrderPrint, err := connection.IQuery(orderPrintFastFood.GetStoreProcedure(), orderPrintFastFood.GetValues())
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al ejecutar el sp [pedido].[ORD_impresion_ordenpedido]: %v", err)
		}
		defer rowOrderPrint.Close()
		column, _ := rowOrderPrint.Columns()
		for rowOrderPrint.Next() {
			var dataOrderPrint models.KioskoImpresionOrdenPedidoFastFood
			if len(column) == 1 {
				err = rowOrderPrint.Scan(&dataOrderPrint.Confirmar)
			} else {
				err = rowOrderPrint.Scan(&dataOrderPrint.NumeroImpresiones,
					&dataOrderPrint.Tipo,
					&dataOrderPrint.Impresora,
					&dataOrderPrint.FormatoXML,
					&dataOrderPrint.JsonData,
					&dataOrderPrint.JsonRegistros)
			}
			if err != nil {
				return fmt.Errorf("[venezuela.order.go]Error al obtener los datos para la impresion de la ordern de pedido :%v", err)
			}
			dataOrderPrintFastFood = append(dataOrderPrintFastFood, &dataOrderPrint)
		}

		//Se genera la respuesta de envio para el core-kiosco-api
		order := &lib_gen_proto.ResultStoreValidationData{}
		order.OrderId = orderId
		order.CfacId = *cfacId
		connection.Commit()
		//envio de data hacia el core-kiosko
		o.SendOrderResponse(order)
		o.SendOrderTurner(*cfacId)
		/*defer func() {
			err = o.WsPrintService(connection, *cfacId, idCabeceraOrdenPedido, *idUserPos, dataOrderPrintFastFood, nil, resultStation)
			if err != nil {
				logger.Error.Println(err)
			}
		}()*/
	}
	return nil
}

func (o *OrderStore) CreateOrderKioskTarjeta() error {
	orderId := o.Order.Cabecera.OrderId
	//statusMxp := o.StoreData.Status
	//currentDate := time.Now()
	connection, err := sqlserver.NewConnectionSql(o.DatabaseCredential)
	if err != nil {
		return err
	}
	defer func() {
		connection.Close()
	}()
	resultStation, err := o.GetStationKiosko(connection)
	if err != nil {
		return err
	}
	err = o.CheckIfAlreadyExistOrder(connection, orderId)
	if err != nil {
		return err
	}
	documentId := o.Order.Cabecera.Client.DocumentId
	idTypeDocument := o.StoreData.GetTipoDocumento(documentId)
	if idTypeDocument == nil {
		return fmt.Errorf("[venezuela.order.go]No se encontro el tipo de documento para el id %s", documentId)
	}
	if strings.EqualFold(idTypeDocument.Description, "CONSUMIDOR FINAL") {
		o.Order.Cabecera.Client.Email = "consumidor.final@kfc.com.ec"
		o.Order.Cabecera.Client.Address = "Quito"
		o.Order.Cabecera.Client.DocumentNumber = "9999999999"
		o.Order.Cabecera.Client.Phone = "2222222"
	}
	idUserPos, err := o.GetIdUserPos(connection, resultStation)
	if err != nil {
		return err
	}
	if idUserPos == nil {
		return fmt.Errorf("[venezuela.order.go]No se encontro el id del usuario para kiosko")
	}
	idControlStation, err := o.GetStationControl(connection, resultStation)
	if err != nil {
		return err
	}
	if idControlStation == nil {
		return fmt.Errorf("[venezuela.order.go]La estacion de kiosko no se encuentra activa")
	}
	idPeriod, err := o.GetOpenPeriod(connection)
	if err != nil {
		return err
	}
	if idPeriod == nil {
		return fmt.Errorf("[venezuela.order.go]No se encontro ningun periodo abierto")
	}
	idMesa, err := o.GetIdMesa(connection, resultStation)
	if err != nil {
		return err
	}

	//Procemiento de tarjeta
	err = connection.CreateBegin()
	defer connection.Rollback()
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al crear BEGIN: %v", err.Error())
	}
	idOrderKiosko, datailsKiosko, err := o.InsertOrderKiosko(connection, nil)
	if err != nil {
		return err
	}

	//valida el bin de las formas de pago
	validateBin := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[USP_verificaBinTarjetaFormaPago]")
	validateBin.AddValueParameterized("bin", o.Order.PaymentMethods.Bin)
	validateBin.AddValueParameterized("rst", o.StoreData.RestaurantId)
	validateBin.AddValueParameterized("user", "")
	validateBin.AddValueParameterized("ip", "")
	rowsBin, err := connection.IQuery(validateBin.GetStoreProcedure(), validateBin.GetValues())
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al escutar el sp [facturacion].[USP_verificaBinTarjetaFormaPago] para kiosko: %v", err.Error())
	}
	defer rowsBin.Close()
	for rowsBin.Next() {
		var idFormaPag, confirma, mensaje string
		err = rowsBin.Scan(&idFormaPag, &confirma, &mensaje)
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al obtener los datos de bin del sp [facturacion].[USP_verificaBinTarjetaFormaPago] para kiosko: %v", err.Error())
		}
	}

	//Insercion de datos a la tabla CabeceraOrdenPedido
	var idOrderHeaderOrder string
	rowsOdp, err := connection.Query("DECLARE @ordenPedido TABLE (existe			INT, tipoImpuesto	VARCHAR(40), tipoCantidad	INT, odp_id			VARCHAR(40), cat_id			VARCHAR(40), rst_tiempopedido INT, fecha_periodo	DATE, fecha			DATETIME, horaServidor	VARCHAR(10),nombreMesa		VARCHAR(20), observacion		VARCHAR(250), palabraDefault	VARCHAR(250), solicitarInicio VARCHAR(20) );INSERT INTO @ordenPedido EXEC pedido.ORD_configuracion_proceso_ordenpedido_kiosko @rst_id = @p1, @IDMesa = @p2, @IDUsersPos = @p3, @IDEstacion = @p4, @num_Pers = 1; SELECT odp_id FROM @ordenPedido;", sql.Named("p1", o.StoreData.RestaurantId), sql.Named("p2", idMesa), sql.Named("p3", idUserPos), sql.Named("p4", resultStation.IdStation))
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al ejecutar el sp [pedido].[ORD_configuracion_proceso_ordenpedido_kiosko]: %v", err)
	}
	defer rowsOdp.Close()
	for rowsOdp.Next() {
		err = rowsOdp.Scan(&idOrderHeaderOrder)
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al obtener al obtener datos del sp ORD_configuracion_proceso_ordenpedido_kiosko: %v", err.Error())
		}
	}
	if utils.IsEmpty(idOrderHeaderOrder) {
		return fmt.Errorf("[venezuela.order.go]Error al obtener el id de la tabla cabecera_orden_pedido")
	}

	//insercion de los productos a la tabla detalle_orden_pedido
	err = o.InsertDetailOrder(connection, datailsKiosko, idOrderHeaderOrder)
	if err != nil {
		return err
	}

	// creación de factura
	var cfacId, spInsertFacturaKiosko string
	if strings.EqualFold(o.Order.Cabecera.PaymentType, "pagoMovil") {
		spInsertFacturaKiosko = "DECLARE @factura TABLE (cdn_id INT, cdn_tipoimpuesto VARCHAR(25), rst_descripcion VARCHAR(100), rst_tipo_servicio VARCHAR(40), cfac_id VARCHAR(40), std_id VARCHAR(40), usr_id VARCHAR(40), est_id VARCHAR(40), cfac_fechacreacion DATETIME, plu_id INT, dtfac_cantidad FLOAT, dtfac_precio_unitario	FLOAT, dtfac_iva FLOAT, dtfac_total FLOAT, plu_descripcion VARCHAR(200), plu_impuesto INT, totalizado FLOAT, servicio FLOAT, cfac_descuento_empresa	FLOAT, cod_Factura VARCHAR(40), btn_cancel_pago INT, desc_producto FLOAT, valorFijo FLOAT, porcentaje FLOAT, canje_puntos FLOAT); INSERT INTO @factura EXEC facturacion.IAE_Fac_InsertFactura_kiosko_pagoMovil @IDRestaurante = @p1, @IDCabeceraOrdenPedido = @p2, @IDUsersPos = @p3, @numeroCuenta = 1, @IDEstacion = @p4, @IDPeriodo = @p5, @IDControlEstacion = @p6; SELECT TOP (1) cfac_id FROM @factura"
	} else {
		spInsertFacturaKiosko = "DECLARE @factura TABLE (cdn_id INT, cdn_tipoimpuesto VARCHAR(25), rst_descripcion VARCHAR(100), rst_tipo_servicio VARCHAR(40), cfac_id VARCHAR(40), std_id VARCHAR(40), usr_id VARCHAR(40), est_id VARCHAR(40), cfac_fechacreacion DATETIME, plu_id INT, dtfac_cantidad FLOAT, dtfac_precio_unitario	FLOAT, dtfac_iva FLOAT, dtfac_total FLOAT, plu_descripcion VARCHAR(200), plu_impuesto INT, totalizado FLOAT, servicio FLOAT, cfac_descuento_empresa	FLOAT, cod_Factura VARCHAR(40), btn_cancel_pago INT, desc_producto FLOAT, valorFijo FLOAT, porcentaje FLOAT, canje_puntos FLOAT); INSERT INTO @factura EXEC facturacion.IAE_Fac_InsertFactura_kiosko @IDRestaurante = @p1, @IDCabeceraOrdenPedido = @p2, @IDUsersPos = @p3, @numeroCuenta = 1, @IDEstacion = @p4, @IDPeriodo = @p5, @IDControlEstacion = @p6; SELECT TOP (1) cfac_id FROM @factura"
	}
	rowsFactura, err := connection.Query(spInsertFacturaKiosko, sql.Named("p1", o.StoreData.RestaurantId), sql.Named("p2", idOrderHeaderOrder), sql.Named("p3", idUserPos), sql.Named("p4", resultStation.IdStation), sql.Named("p5", idPeriod), sql.Named("p6", idControlStation))
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al ejecutar el sp de facturacion: %v", err)
	}
	defer rowsFactura.Close()
	for rowsFactura.Next() {
		err = rowsFactura.Scan(&cfacId)
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al obtener al obtener datos del sp %v: %v", spInsertFacturaKiosko, err.Error())
		}
	}

	//Actulaizo la tabla cabecera_orden_pedido
	UpdateOrderHeaderOrder := fmt.Sprintf(`UPDATE dbo.Cabecera_Orden_Pedido
				SET odp_observacion = @nombres + ' - ' + @tipo
				WHERE IDCabeceraOrdenPedido = @pedido`)

	rowsOrderHeader, err := connection.Exec(UpdateOrderHeaderOrder,
		sql.Named("nombres", o.Order.Cabecera.Client.Name),
		sql.Named("tipo", o.Order.Cabecera.TypeService),
		sql.Named("pedido", idOrderHeaderOrder))
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al actualizar los datos de la tabla cabecera_orden_pedido: %v", err)
	}
	affectedOrderHeader, err := rowsOrderHeader.RowsAffected()
	if err != nil {
		return err
	}
	if affectedOrderHeader < 1 {
		return fmt.Errorf("[venezuela.order.go]No se ha podio actualizar los datos de la tabla cabecera_orden_pedido")
	}
	// Insercion de los datos de formapago_factura
	idClient, document, err := o.GetIdClient(connection)
	if err != nil {
		return err
	}
	dataTypeDocument := o.StoreData.GetTipoDocumento(o.Order.Cabecera.Client.DocumentId)
	if !strings.EqualFold(dataTypeDocument.Description, "CONSUMIDOR FINAL") {
		if idClient != nil {
			_, err = o.InsertOrUpdateClient(connection, "U", dataTypeDocument.Description, *idUserPos)
			if err != nil {
				return err
			}
		} else {
			_, err = o.InsertOrUpdateClient(connection, "I", dataTypeDocument.Description, *idUserPos)
			if err != nil {
				return err
			}
			idClient, document, err = o.GetIdClient(connection)
			if err != nil {
				return err
			}
		}
	}

	//Actualizo la tabla kiosko_cabecera_pedidos con el cfacId
	updateKioskoHeader := fmt.Sprintf(`UPDATE dbo.kiosko_cabecera_pedidos
				SET cfac_id = @codFactura
				WHERE id = @idPedido`)
	rowsKioskoHeader, err := connection.Exec(updateKioskoHeader, sql.Named("codFactura", cfacId), sql.Named("idPedido", idOrderKiosko))
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al actualizar los datos de la tabla cabecera_orden_pedido: %v", err)
	}
	affectedKioskoHeader, err := rowsKioskoHeader.RowsAffected()
	if err != nil {
		return err
	}
	if affectedKioskoHeader < 1 {
		return fmt.Errorf("[venezuela.order.go]No se ha podio actualizar los datos de la tabla cabecera_orden_pedido")
	}
	_, err = connection.Exec("EXEC pedido.IAE_ImpresionOrdenPedidoFastFoodKiosko @p1, @p2, @p3", sql.Named("p1", idOrderHeaderOrder), sql.Named("p2", o.StoreData.ChainId), sql.Named("p3", o.StoreData.RestaurantId))
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al ejecutar el sp: %v", err)
	}

	// Actualiza el medio de venta de la facura Revisar Error de Subconsulta
	salesMedia := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[IAE_Medio_de_Venta_Integraciones]")
	salesMedia.AddValueParameterized("transaccion", cfacId)
	salesMedia.AddValueParameterized("sistema", "MaxPoint")
	if o.Order.Cabecera.PaymentType == "tarjeta" {
		salesMedia.AddValueParameterized("medio", "KIOSKO TARJETA")
	} else {
		salesMedia.AddValueParameterized("medio", "KIOSKO PAGO MOVIL")
	}
	_, err = connection.IQuery(salesMedia.GetStoreProcedure(), salesMedia.GetValues())
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al ejecutar el sp [facturacion].[IAE_Medio_de_Venta_Integraciones]: %v", err)
	}

	//Obtengo el total de la factura
	var cfacTotal string
	query := "SELECT cfac_total FROM dbo.Cabecera_Factura WHERE cfac_id = @cfacId"
	rows, err := connection.Query(query, sql.Named("cfacId", cfacId))
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al ejectar el query %v: %v", query, err)
	}
	defer rows.Close()
	for rows.Next() {
		err = rows.Scan(&cfacTotal)
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al obtener el valor total de la factura pa el pedido %v: %v", orderId, err)
		}
	}
	// insertar formas de pago dbo.kiosko_forma_pagos
	err = o.InsertKioskPayment(connection, idOrderKiosko, cfacId, cfacTotal, *idUserPos)
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al insertar formas de pago  dbo.kiosko_forma_pagos orderId = %s : %v", orderId, err)
	}

	//INSERTA CLIENTE - FIN FACTURA
	invoicingClient := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[USP_FacturaCliente]")
	invoicingClient.AddValueParameterized("idCliente", document)
	invoicingClient.AddValueParameterized("IDFactura", cfacId)
	invoicingClient.AddValueParameterized("IDUserpos", idUserPos)
	invoicingClient.AddValueParameterized("tipoDocumento", o.Order.Cabecera.Client.DocumentId)
	_, err = connection.IQuery(invoicingClient.GetStoreProcedure(), invoicingClient.GetValues())
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al ejecutar el sp [facturacion].[USP_FacturaCliente]: %v", err)
	}

	/*var idFormaPago, tarjetName *string
	//Insercion a la tabla SWT_Requerimiento_Autorizacion
	authorizationRequirement := sqlserver.NewSimpleInsertBuilderSQL(false, "dbo.SWT_Requerimiento_Autorizacion")
	authorizationRequirement.AddValue("rqaut_fecha", currentDate)
	authorizationRequirement.AddValue("rqaut_ip", o.Order.Cabecera.IpKiosk)
	authorizationRequirement.AddValue("rqaut_puerto", "8080")
	authorizationRequirement.AddValue("rqaut_trama", cfacId)
	authorizationRequirement.AddValue("rqaut_movimiento", cfacId)
	authorizationRequirement.AddValue("tpenv_id", statusMxp.IdPinpadUnired)
	authorizationRequirement.AddValue("IDFormapagoFactura", idFormaPago)
	authorizationRequirement.AddValue("IDEstacion", resultStation.IdStation)
	authorizationRequirement.AddValue("IDUsersPos", idUserPos)
	authorizationRequirement.AddValue("IDStatus", statusMxp.SesionesActivo)
	authorizationRequirement.AddValue("replica", 0)
	authorizationRequirement.AddValue("nivel", 0)
	_, err = connection.IQuery(authorizationRequirement.SqlGenerated(), authorizationRequirement.GetValues())
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al insertar los datos en la tabla dbo.SWT_Requerimiento_Autorizacion: %v", err)
	}

	//Insercion a la tabla SWT_Respuesta_Autorizacion
	subCardHolder := o.Order.PaymentMethods.Card.CardHolder
	if len(o.Order.PaymentMethods.Card.CardHolder) > 17 {
		subCardHolder = o.Order.PaymentMethods.Card.CardHolder[0:18]
	}
	fecha := currentDate.Format("20060102")
	hora := currentDate.Format("150405")
	responseAuthorization := sqlserver.NewSimpleInsertBuilderSQL(true, "dbo.SWT_Respuesta_Autorizacion")
	responseAuthorization.AddValue("rsaut_trama", cfacId)
	responseAuthorization.AddValue("rsaut_fecha", currentDate)
	responseAuthorization.AddValue("ttra_codigo", "01")
	responseAuthorization.AddValue("cres_codigo", o.Order.PaymentMethods.Card.CodeResponse)
	responseAuthorization.AddValue("rsaut_respuesta", o.Order.PaymentMethods.Card.MessageResponseAut)
	responseAuthorization.AddValue("rsaut_secuencial_transaccion", o.Order.PaymentMethods.Card.CodeResponse)
	responseAuthorization.AddValue("rsaut_hora_autorizacion", hora)
	responseAuthorization.AddValue("rsaut_fecha_autorizacion", fecha)
	responseAuthorization.AddValue("rsaut_numero_autorizacion", o.Order.PaymentMethods.Card.Authorization)
	responseAuthorization.AddValue("rsaut_terminal_id", o.Order.PaymentMethods.Card.TID)
	responseAuthorization.AddValue("rsaut_grupo_tarjeta", tarjetName)
	responseAuthorization.AddValue("rsaut_red_adquiriente", "RED DATAFAST")
	responseAuthorization.AddValue("rsaut_merchant_id", o.Order.PaymentMethods.Card.MID)
	responseAuthorization.AddValue("rsaut_numero_tarjeta", o.Order.PaymentMethods.Card.NumberCard)
	responseAuthorization.AddValue("rstaut_tarjetahabiente", subCardHolder)
	responseAuthorization.AddValue("rsaut_movimiento", cfacId)
	responseAuthorization.AddValue("IDStatus", statusMxp.SesionesActivo)
	responseAuthorization.AddValue("replica", 0)
	responseAuthorization.AddValue("nivel", 0)
	rowsResponseAuthorization, err := connection.IQueryRow(responseAuthorization.SqlGeneratedIdNumberDynamic("rsaut_id"), responseAuthorization.GetValues())
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al insertar los datos en la tabla dbo.SWT_Respuesta_Autorizacion: %v", err)
	}
	if rowsResponseAuthorization.Err() != nil {
		return fmt.Errorf("[venezuela.order.go]Error al iterar el rows de la tabla dbo.SWT_Respuesta_Autorizacion: %v", rowsResponseAuthorization.Err())
	}
	var rsAutId int32
	err = rowsResponseAuthorization.Scan(&rsAutId)
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al obtener el identificador unico de la tabla dbo.SWT_Respuesta_Autorizacion: %v", err.Error())
	}*/

	//Proceso de IFACT
	errPrintIFACT := o.printOrderIFACT(connection, cfacId)
	if errPrintIFACT != nil {
		logger.Error.Println("[venezuela.order.go]Error al generar la facturacion electronica: ", errPrintIFACT.Error())
		//return errors.New("[venezuela.order.go]Error al generar la facturacion electronica: " + err.Error())
	}

	//var jsonInvoicing, jsonVoucher *string
	dataOrderPrintFastFood := make([]*models.KioskoImpresionOrdenPedidoFastFood, 0)
	orderPrintFastFood := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[pedido].[IAE_ImpresionOrdenPedidoFastFoodKiosko]")
	orderPrintFastFood.AddValueParameterized("idCabeceraOrdenPedido", idOrderHeaderOrder)
	orderPrintFastFood.AddValueParameterized("idCadena", o.StoreData.ChainId)
	orderPrintFastFood.AddValueParameterized("idRestaurante", o.StoreData.RestaurantId)
	rowOrderPrint, err := connection.IQuery(orderPrintFastFood.GetStoreProcedure(), orderPrintFastFood.GetValues())
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al ejecutar el sp [pedido].[IAE_ImpresionOrdenPedidoFastFoodKiosko]: %v", err)
	}
	defer rowOrderPrint.Close()
	for rowOrderPrint.Next() {
		var dataOrderPrint models.KioskoImpresionOrdenPedidoFastFood
		err = rowOrderPrint.Scan(
			&dataOrderPrint.NumeroImpresiones,
			&dataOrderPrint.Tipo,
			&dataOrderPrint.Impresora,
			&dataOrderPrint.FormatoXML,
			&dataOrderPrint.JsonData,
			&dataOrderPrint.JsonRegistros)
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al obtener los datos para la impresion de la orden de pedido: %v", err)
		}
		dataOrderPrintFastFood = append(dataOrderPrintFastFood, &dataOrderPrint)
	}

	//Se genera la respuesta de envio para el core-kiosco-api
	order := &lib_gen_proto.ResultStoreValidationData{}
	order.IdOrdenPedido = idOrderHeaderOrder
	order.OrderId = orderId
	order.CfacId = cfacId
	order.Voucher = utils.ToString(o.Order.PaymentMethods.Card.MessageResponse, "")

	connection.Commit()
	o.SendOrderResponse(order)
	o.SendOrderTurner(cfacId)

	isTurneroEnabled := o.Feature.GetConfigFeatureFlag(featureflag2.TURNERO_DIGITAL)
	if isTurneroEnabled {
		go o.regionalKiosk.SendTurnerDigital(cfacId)
	}

	//Valido si el featureFlag para encuesta esta activo
	isSurvey := o.Feature.GetConfigFeatureFlag(featureflag2.ENCUESTA)
	if isSurvey {
		o.regionalKiosk.GetSurvey(dataTypeDocument, cfacId, *idClient)
	}

	//procesos de impresion de tarjeta
	/*defer func() {
		err = o.WsPrintService(connection, cfacId, idOrderHeaderOrder, *idUserPos, dataOrderPrintFastFood, nil, resultStation)
		if err != nil {
			logger.Error.Println(err.Error())
		}
	}()*/
	return nil
}

func (o *OrderStore) GetOpenPeriod(connection *sqlserver.DatabaseSql) (*string, error) {
	query := fmt.Sprintf(`SELECT CAST(IDPeriodo AS VARCHAR(40))
			FROM dbo.Periodo
			WHERE IDStatus = @idStatus
				AND prd_fechacierre IS NULL`)
	rows, err := connection.Query(query, sql.Named("idStatus", o.StoreData.Status.PeriodoAperturaAbierto))
	if err != nil {
		return nil, fmt.Errorf("[venezuela.order.go]Error al ejecutar el query %s: %s", query, err.Error())
	}
	defer rows.Close()
	for rows.Next() {
		var result string
		err = rows.Scan(&result)
		if err != nil {
			return nil, fmt.Errorf("[venezuela.order.go]Error al obtener los datos del query %v: %s", query, err)
		}
		return &result, nil
	}
	return nil, nil
}
func (o *OrderStore) GetStationControl(connection *sqlserver.DatabaseSql, dataStation *models.StationKiosco) (*string, error) {
	query := fmt.Sprintf(`SELECT TOP 1 CAST(cs.IDControlEstacion AS NVARCHAR(40)) 
			FROM dbo.Control_Estacion cs, Users_Pos up 
			WHERE cs.IDEstacion =@idStation
			    and up.usr_usuario = @cashierName
			    AND cs.IDUsersPos = up.IDUsersPos 
			    AND cs.IDStatus = @idStatus`)
	rows, err := connection.Query(query, sql.Named("idStation", dataStation.IdStation), sql.Named("cashierName", dataStation.CashierName), sql.Named("idStatus", o.StoreData.Status.SesionesActivo))
	if err != nil {
		return nil, fmt.Errorf("[venezuela.order.go]Error al ejecutar el query %v: %v", query, err)
	}
	defer rows.Close()
	for rows.Next() {
		var idControlStation *string
		err = rows.Scan(&idControlStation)
		if err != nil {
			return nil, fmt.Errorf("[venezuela.order.go]Error al obtener los datos del control estacion de kiosko: %s", err)
		}
		return idControlStation, nil
	}
	return nil, nil
}
func (o *OrderStore) GetIdMesa(connection *sqlserver.DatabaseSql, dataStation *models.StationKiosco) (string, error) {
	dataIdStation := *dataStation.IdStation
	dataIdMesa, exits := cacheIdMesa.Get(dataIdStation)
	if !exits {
		var idMesa string
		query := fmt.Sprintf(`SELECT cast(ecd.idIntegracion AS VARCHAR(36))
			FROM dbo.ColeccionEstacion AS ce
			INNER JOIN dbo.ColeccionDeDatosEstacion AS cde ON cde.ID_ColeccionEstacion = ce.ID_ColeccionEstacion
			INNER JOIN dbo.EstacionColeccionDeDatos AS ecd ON ecd.ID_ColeccionDeDatosEstacion = cde.ID_ColeccionDeDatosEstacion
			WHERE ce.cdn_id = @cadena
				AND ce.Descripcion = 'CONFIGURACION DE MESA FAST FOOD PREDETERMINADA'
				AND ecd.IDEstacion = @estacion
				AND ce.isactive = 1
				AND cde.isactive = 1
				AND ecd.isactive = 1`)
		row := connection.QueryRow(query, sql.Named("cadena", o.StoreData.ChainId), sql.Named("estacion", dataIdStation))
		errScan := row.Scan(&idMesa)
		if errScan != nil {
			return "", fmt.Errorf("[venezuela.order.go]Error al obtener el idMesa de kiosko %v: %s", query, errScan.Error())
		}
		cacheIdMesa.Set(dataIdStation, idMesa, 1*time.Hour)
		return idMesa, nil
	}
	return dataIdMesa, nil
}
func (o *OrderStore) GetStationKiosko(connection *sqlserver.DatabaseSql) (*models.StationKiosco, error) {
	ipKiosko := o.Order.Cabecera.IpKiosk
	//valida si en cache existe el idEstacion y la Ip del kiosko caso contrario consulta y almaneca en cache
	dataStation, exits := cacheStation.Get(ipKiosko)
	if !exits {
		dataStation = &models.StationKiosco{}
		query := fmt.Sprintf(`SELECT	
		CAST(estacion.IDEstacion AS VARCHAR(36)),
		CASE
			WHEN datos.variableV <> '' THEN datos.variableV
			ELSE CONVERT(VARCHAR(13), datos.variableB)
		END AS nombreCajero
		FROM ColeccionEstacion AS coleccion
		INNER JOIN ColeccionDeDatosEstacion AS parametros ON parametros.ID_ColeccionEstacion = coleccion.ID_ColeccionEstacion
		INNER JOIN EstacionColeccionDeDatos AS datos ON datos.ID_ColeccionDeDatosEstacion = parametros.ID_ColeccionDeDatosEstacion
		INNER JOIN dbo.Estacion AS Estacion ON estacion.IDEstacion = datos.IDEstacion
		WHERE coleccion.cdn_id = @cdnId
			AND coleccion.Descripcion = 'CONFIGURACION KIOSKO'
			AND coleccion.isActive = 1
			AND parametros.isActive = 1
			AND datos.isActive = 1
			AND Estacion.rst_id = @rstId
			and estacion.est_ip = @ip
			AND  Estacion.IDStatus = config.fn_estado('Estaciones', 'Activo')
			AND Parametros.Descripcion = 'KIOSKO NOMBRE CAJERO';
		`)
		rows, err := connection.Query(query, sql.Named("cdnId", o.StoreData.ChainId), sql.Named("rstId", o.StoreData.RestaurantId), sql.Named("ip", o.Order.Cabecera.IpKiosk))
		if err != nil {
			return nil, fmt.Errorf("[venezuela.order.go]Error al ejecutar el query %v: %v", query, err)
		}
		defer rows.Close()
		for rows.Next() {
			err = rows.Scan(&dataStation.IdStation, &dataStation.CashierName)
			if err != nil {
				return nil, fmt.Errorf("[venezuela.order.go]Error al obtener los datos de estacion del kiosko: %v", err)
			}
		}
		if dataStation.IdStation == nil && dataStation.CashierName == nil {
			return nil, fmt.Errorf("[venezuela.order.go]Error, no se encontro el id y el nombre del cajero de kiosko, por favor validar")
		}
		cacheStation.Set(ipKiosko, dataStation, 1*time.Hour)
	}
	return dataStation, nil
}
func (o *OrderStore) InsertDetailOrder(connection *sqlserver.DatabaseSql, data []*KioskoPlus, idCabeceraOrdenPedido string) error {
	isUpsell := o.Feature.GetConfigFeatureFlag(featureflag2.UPSELLING)
	dataDetailsKiosko := make([]*KioskoPlus, 0)
	currentDate := time.Now()
	query := fmt.Sprintf(`select 
    PrecioP.plu_id
    ,PrecioP.pr_valor_neto
    ,PrecioP.pr_valor_iva
	,PrecioP.pr_pvp
	from Precio_Plu PrecioP
	where PrecioP.plu_id = @pluId
	and PrecioP.idCategoria = @idCategoria`)
	for _, datails := range data {
		rows, err := connection.Query(query,
			sql.Named("pluId", datails.PluId),
			sql.Named("idCategoria", o.StoreData.RestaurantCategory))
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al ejecutar el query %v: %v", query, err)
		}
		for rows.Next() {
			var dataProduct KioskoPlus
			var pluId *int32
			err = rows.Scan(&pluId, &dataProduct.ValorNeto, &dataProduct.ValorIva, &dataProduct.ValorBruto)
			if err != nil {
				rows.Close()
				return fmt.Errorf("[venezuela.order.go]Error al obtener los pluId de los productos: %v", err)
			}
			dataProduct.IdOrderDetail = datails.IdOrderDetail
			dataProduct.PluId = datails.PluId
			dataProduct.Quantity = datails.Quantity
			dataProduct.IdCabeceraOrdenPedido = idCabeceraOrdenPedido
			dataProduct.RelatedProductId = datails.RelatedProductId
			if datails.Exchange != nil {
				dataProduct.Exchange = datails.Exchange
			}
			dataDetailsKiosko = append(dataDetailsKiosko, &dataProduct)
		}
		rows.Close()
	}

	//Insercion de los datos a la tabla detalle_orden_pedido
	var isProduct int32
	var tempIdDetalleOrdenPedido string
	var tmpPluId *int32
	for i, detailsInsert := range dataDetailsKiosko {
		isProduct = 1
		if detailsInsert.Exchange != nil {
			isProduct = 0
		}
		detailsOrder := sqlserver.NewSimpleInsertBuilderSQL(true, "dbo.Detalle_Orden_Pedido")
		detailsOrder.AddValue("idCabeceraOrdenPedido", idCabeceraOrdenPedido)
		detailsOrder.AddValue("plu_id", detailsInsert.PluId)
		detailsOrder.AddValue("dop_cantidad", detailsInsert.Quantity)
		detailsOrder.AddValue("dop_iva", detailsInsert.ValorIva)
		detailsOrder.AddValue("dop_precio", detailsInsert.ValorNeto)
		detailsOrder.AddValue("dop_total", detailsInsert.ValorBruto)
		detailsOrder.AddValue("dop_creacionfecha", currentDate)
		detailsOrder.AddValue("dop_cuenta", 1)
		detailsOrder.AddValue("dop_impresion", 1)
		detailsOrder.AddValue("dop_anulacion", 1)
		detailsOrder.AddValue("dop_estado", 1)
		detailsOrder.AddValue("dop_varchar1", detailsInsert.IdOrderDetail)
		detailsOrder.AddValue("dop_float1", isProduct)
		detailsOrder.AddValue("replica", 0)
		if isUpsell && !utils.IsEmpty(detailsInsert.RelatedProductId) {
			detailsOrder.AddValue("Detalle_Orden_PedidoVarchar2", detailsInsert.RelatedProductId)
		}
		rows, err := connection.IQueryRow(detailsOrder.SqlGeneratedIdDynamic("IDDetalleOrdenPedido"), detailsOrder.GetValues())
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al insertar los datos en la tabla dbo.Detalle_Orden_Pedido %v: %v", detailsOrder.GetValues(), err)
		}
		if rows.Err() != nil {
			return fmt.Errorf("[venezuela.order.go]Error al iterar el rows: %v", rows.Err())
		}
		var idDetalleOrdenPedido string
		err = rows.Scan(&idDetalleOrdenPedido)
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al obtener el idDetalleOrdenpedido al momento de insertar los datos en la tabla dbo.Detalle_Orden_Pedido: %v", rows.Err())
		}

		//actualizo el objeto con el idDetalleOrdenPedido
		dataDetailsKiosko[i].IdDetalleOrdenPedido = idDetalleOrdenPedido
		dataDetailsKiosko[i].isDetails = isProduct
		if tmpPluId != detailsInsert.Exchange {
			tmpPluId = &detailsInsert.IdOrderDetail
			tempIdDetalleOrdenPedido = dataDetailsKiosko[i-1].IdDetalleOrdenPedido
		}

		//valido si el pedio que esta ingresando tiene es de modificador y actalizo el idDetalleOrdenPedido
		if detailsInsert.Exchange != nil {
			dataDetailsKiosko[i].IdDetalleOrdenPedido = tempIdDetalleOrdenPedido

		}
	}

	//Actualizacion de los datos a la tabla detalle_orden_pedido con sus respectivos modificadores
	updateDetails := fmt.Sprintf(`UPDATE Detalle_Orden_Pedido
				SET Detalle_Orden_Pedido.dop_varchar1 = (
				    CASE WHEN @isModifica IS NULL
				        THEN Detalle_Orden_Pedido.IDDetalleOrdenPedido
				        ELSE ISNULL(CAST(@idDetalleOrdenPedido AS VARCHAR(40))
				    , Detalle_Orden_Pedido.IDDetalleOrdenPedido) END),
				    Detalle_Orden_Pedido.dop_creacionfecha = DATEADD(ss, @addSecond, Detalle_Orden_Pedido.dop_creacionfecha)
				    FROM Detalle_Orden_Pedido
				WHERE Detalle_Orden_Pedido.dop_varchar1 = @idKioskoDetallePedido
				    AND Detalle_Orden_Pedido.IDCabeceraOrdenPedido = @idCabeceraOrdenPedido`)
	updateDetails2 := fmt.Sprintf(`UPDATE Detalle_Orden_Pedido
			SET Detalle_Orden_Pedido.dop_varchar1 = Detalle_Orden_Pedido.IDDetalleOrdenPedido,
			    Detalle_Orden_Pedido.dop_creacionfecha = DATEADD(ss, @addSecond, Detalle_Orden_Pedido.dop_creacionfecha)
			    FROM Detalle_Orden_Pedido
			WHERE Detalle_Orden_Pedido.dop_varchar1 = @idKioskoDetallePedido
			    AND Detalle_Orden_Pedido.IDCabeceraOrdenPedido = @idCabeceraOrdenPedido`)
	for i, detailsInsert := range dataDetailsKiosko {
		idOrderToString, err := utils.ConvertNumberToString(detailsInsert.IdOrderDetail)
		if err != nil {
			return err
		}
		if detailsInsert.isDetails > 0 {
			exec, err := connection.Exec(updateDetails2,
				sql.Named("addSecond", i+1),
				sql.Named("idKioskoDetallePedido", idOrderToString),
				sql.Named("idCabeceraOrdenPedido", detailsInsert.IdCabeceraOrdenPedido))
			if err != nil {
				return fmt.Errorf("[venezuela.order.go]Error al actualizar los datos de la tabla Detalle_Orden_Pedido: %v", err)
			}
			rowsAfected, err := exec.RowsAffected()
			if err != nil {
				return fmt.Errorf("[venezuela.order.go]Error al identificar la actualizacion de los datos de la tabla Detalle_Orden_Pedido: %v", err)
			}
			if rowsAfected != 1 {
				return fmt.Errorf("[venezuela.order.go]No se actualizaron los registros de la tabla detalle_orden_pedido")
			}
		} else {
			exec, err := connection.Exec(updateDetails,
				sql.Named("isModifica", detailsInsert.Exchange),
				sql.Named("idDetalleOrdenPedido", detailsInsert.IdDetalleOrdenPedido),
				sql.Named("addSecond", i+1),
				sql.Named("idKioskoDetallePedido", idOrderToString),
				sql.Named("idCabeceraOrdenPedido", detailsInsert.IdCabeceraOrdenPedido))
			if err != nil {
				return fmt.Errorf("[venezuela.order.go]Error al actualizar los datos en la tabla Detalle_Orden_Pedido: %v", err)
			}
			rowsAfected, err := exec.RowsAffected()
			if err != nil {
				return fmt.Errorf("[venezuela.order.go]Error al identificar la actualizacion de los datos de la tabla Detalle_Orden_Pedido: %v", err)
			}
			if rowsAfected != 1 {
				return fmt.Errorf("[venezuela.order.go]No se actualizaron los registros de la tabla detalle_orden_pedido")
			}
		}
	}
	return nil
}
func (o *OrderStore) GetIdUserPos(connection *sqlserver.DatabaseSql, dataStation *models.StationKiosco) (*string, error) {
	query := fmt.Sprintf(`SELECT CAST(IDUsersPos AS VARCHAR(40)) FROM dbo.Users_Pos  WHERE  IDStatus IS NOT NULL AND Users_Pos.usr_usuario = @CashierName`)
	result, err := connection.Query(query, sql.Named("CashierName", dataStation.CashierName))
	if err != nil {
		return nil, fmt.Errorf("[venezuela.order.go]Error al ejecutar el query %v: %v", query, err)
	}
	defer result.Close()
	for result.Next() {
		var getIdUserPos string
		err = result.Scan(&getIdUserPos)
		if err != nil {
			return nil, fmt.Errorf("[venezuela.order.go]Error al obtener el id del Usuario %v: %v", dataStation.CashierName, err)
		}
		return &getIdUserPos, nil
	}
	return nil, nil
}
func (o *OrderStore) CheckIfAlreadyExistOrder(connection *sqlserver.DatabaseSql, codigoApp string) error {
	query := fmt.Sprintf(`SELECT count(*) as cantidad FROM dbo.kiosko_cabecera_pedidos WHERE codigo_app = @codigoApp`)
	result, err := connection.Query(query, sql.Named("codigoApp", codigoApp))
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al ejecutar el query %v: %v", query, err)
	}
	defer result.Close()
	for result.Next() {
		var countData int32
		err = result.Scan(&countData)
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al validar el codigoApp %v de la tabla kiosko_cabecera_pedidos: %v", codigoApp, err)
		}
		if countData > 0 {
			return fmt.Errorf("[venezuela.order.go]El codigo %v ya se encuentra registrado, por favor ingresar uno nuevo", codigoApp)
		}
	}
	return nil
}
func (o *OrderStore) InsertOrderKiosko(connection *sqlserver.DatabaseSql, cfacId *string) (int32, []*KioskoPlus, error) {
	var idOrder int32
	responseDetailsOrder := make([]*KioskoPlus, 0)
	isLocator := o.Feature.GetConfigFeatureFlag(featureflag2.LOCALIZADOR)
	isUpsell := o.Feature.GetConfigFeatureFlag(featureflag2.UPSELLING)
	isPhoneSMS := o.Feature.GetConfigFeatureFlag(featureflag2.TELEFONO_SMS)
	currentDate := time.Now()
	estadoMxp := "cerrado"
	if strings.EqualFold(o.Order.Cabecera.PaymentType, "efectivo") {
		estadoMxp = "ingresado"
	}
	kioskoHeader := sqlserver.NewSimpleInsertBuilderSQL(true, "dbo.kiosko_cabecera_pedidos")
	kioskoHeader.AddValue("cli_nombres", o.Order.Cabecera.Client.Name)
	kioskoHeader.AddValue("IDTipoDocumento", o.Order.Cabecera.Client.DocumentId)
	kioskoHeader.AddValue("cli_documento", o.Order.Cabecera.Client.DocumentNumber)
	kioskoHeader.AddValue("cli_telefono", o.Order.Cabecera.Client.Phone)
	kioskoHeader.AddValue("cli_direccion", o.Order.Cabecera.Client.Address)
	kioskoHeader.AddValue("cli_email", o.Order.Cabecera.Client.Email)
	kioskoHeader.AddValue("cfac_subtotal", o.Order.PaymentInfo.SubTotal)
	kioskoHeader.AddValue("cfac_iva", o.Order.PaymentInfo.Iva)
	kioskoHeader.AddValue("cfac_total", o.Order.PaymentInfo.Total)
	kioskoHeader.AddValue("tipo_servicio", o.Order.Cabecera.TypeService)
	kioskoHeader.AddValue("est_ip", o.Order.Cabecera.IpKiosk)
	kioskoHeader.AddValue("codigo_app", o.Order.Cabecera.OrderId)
	kioskoHeader.AddValue("created_at", currentDate)
	kioskoHeader.AddValue("updated_at", currentDate)
	kioskoHeader.AddValue("estado_maxpoint", estadoMxp)
	//Solo para efectivo se infresa el cfacId
	if cfacId != nil {
		kioskoHeader.AddValue("cfac_id", cfacId)
	}
	//descuento
	if o.Order.Discount != nil {
		kioskoHeader.AddValue("montoTotalDescuentos", o.Order.Discount.Total)
		kioskoHeader.AddValue("descuentoMontoFijo", o.Order.Discount.AmountDiscount)
		kioskoHeader.AddValue("descuentoPorcentaje", o.Order.Discount.PercentageDiscount)
	}
	//localizador
	if o.Order.Cabecera.SelectedBuzzer != nil && isLocator {
		kioskoHeader.AddValue("localizador_seleccionado", o.Order.Cabecera.SelectedBuzzer)
	}
	if isPhoneSMS && o.Order.Cabecera.Client.PhoneSmsNotification != nil {
		kioskoHeader.AddValue("telefono_notificacion_sms", o.Order.Cabecera.Client.PhoneSmsNotification)
	}
	//Datos adicionales para kiosko
	if validatorsql.ColumnExitsDb(connection, "kiosko_cabecera_pedidos", "info_adicional") && !utils.IsEmpty(o.Order.AdditionalInfo) {
		kioskoHeader.AddValue("info_adicional", o.Order.AdditionalInfo)
	}
	rows, err := connection.IQueryRow(kioskoHeader.SqlGenerated(), kioskoHeader.GetValues())
	if err != nil {
		return -1, nil, fmt.Errorf("[venezuela.order.go]Error al insertar los datos en la tabla dbo.kiosko_cabecera_pedidos: %v", err)
	}
	if rows.Err() != nil {
		return -1, nil, fmt.Errorf("[venezuela.order.go]Error al iterar las filas de la tabla dbo.kiosko_cabecera_pedidos: %v", rows.Err())
	}
	err = rows.Scan(&idOrder)
	if err != nil {
		return -1, nil, fmt.Errorf("[venezuela.order.go]Error al obtener el id de la tabla dbo.kiosko_cabecera_pedidos: %v", err.Error())
	}

	//Insercion de detalles de kiosko
	for _, details := range o.Order.Items.Product {
		detailsKiosko := &KioskoPlus{}
		kioskoDetails := sqlserver.NewSimpleInsertBuilderSQL(true, "dbo.kiosko_detalle_pedidos")
		kioskoDetails.AddValue("id_orden", idOrder)
		kioskoDetails.AddValue("plu_id", details.ProductId)
		kioskoDetails.AddValue("dop_cantidad", details.Quantity)
		kioskoDetails.AddValue("created_at", currentDate)
		kioskoDetails.AddValue("updated_at", currentDate)
		if o.Order.Discount != nil {
			kioskoDetails.AddValue("porcentajeIva", details.TaxesPercentage)
		}
		if details.AdditionalInfo != nil {
			if isUpsell && !utils.IsEmpty(details.AdditionalInfo.RelatedProductId) {
				kioskoDetails.AddValue("productoUpsell", details.AdditionalInfo.RelatedProductId)
				detailsKiosko.RelatedProductId = details.AdditionalInfo.RelatedProductId
			}
		}
		rowsDetails, err := connection.IQueryRow(kioskoDetails.SqlGenerated(), kioskoDetails.GetValues())
		if err != nil {
			return -1, nil, fmt.Errorf("[venezuela.order.go]Error al insertar los productos en la tabla dbo.kiosko_detalle_pedidos: %v", err)
		}
		if rowsDetails.Err() != nil {
			return -1, nil, fmt.Errorf("[venezuela.order.go]Error al iterar los datos de la tabla dbo.kiosko_detalle_pedidos: %v", rowsDetails.Err())
		}
		var detailsOrderId int32
		err = rowsDetails.Scan(&detailsOrderId)
		if err != nil {
			return -1, nil, fmt.Errorf("[venezuela.order.go]Error al obtener el identificador de la tabla dbo.kiosko_detalle_pedidos: %v", err.Error())
		}
		detailsKiosko.IdOrderDetail = detailsOrderId
		detailsKiosko.PluId = details.ProductId
		detailsKiosko.Quantity = details.Quantity
		responseDetailsOrder = append(responseDetailsOrder, detailsKiosko)
		for _, modifiers := range details.ModifierGroups {
			modifiersKiosko := &KioskoPlus{}
			kioskoDetails.Clear()
			kioskoDetails.AddValue("id_orden", idOrder)
			kioskoDetails.AddValue("plu_id", modifiers.ProductId)
			kioskoDetails.AddValue("dop_cantidad", modifiers.Quantity)
			kioskoDetails.AddValue("created_at", currentDate)
			kioskoDetails.AddValue("updated_at", currentDate)
			kioskoDetails.AddValue("modifica", detailsOrderId)
			rowsModifiers, err := connection.IQueryRow(kioskoDetails.SqlGenerated(), kioskoDetails.GetValues())
			if err != nil {
				return -1, nil, fmt.Errorf("[venezuela.order.go]Error al insertar los modificadores en la tabla dbo.kiosko_detalle_pedidos: %v", err)
			}
			var modifiersOrderId int32
			err = rowsModifiers.Scan(&modifiersOrderId)
			if err != nil {
				return -1, nil, fmt.Errorf("[venezuela.order.go]Error al obtener el id de la tabla dbo.kiosko_detalle_pedidos: %v", err.Error())
			}
			modifiersKiosko.IdOrderDetail = modifiersOrderId
			modifiersKiosko.Exchange = &detailsOrderId
			modifiersKiosko.PluId = modifiers.ProductId
			modifiersKiosko.Quantity = modifiers.Quantity
			responseDetailsOrder = append(responseDetailsOrder, modifiersKiosko)
		}
	}
	//Insecion de swtich kiosko
	if !strings.EqualFold(o.Order.Cabecera.PaymentType, "efectivo") {
		paymentMethodTable := sqlserver.NewSimpleInsertBuilderSQL(false, "dbo.kiosko_autorizaciones_switch")
		paymentMethodTable.AddValue("idOrden", idOrder)
		paymentMethodTable.AddValue("codigoComercio", utils.ToString(o.Order.PaymentMethods.Card.MID, ""))
		paymentMethodTable.AddValue("numeroTerminal", utils.ToString(o.Order.PaymentMethods.Card.TID, ""))
		paymentMethodTable.AddValue("lote", utils.ToString(o.Order.PaymentMethods.Card.Lote, ""))
		paymentMethodTable.AddValue("referencia", utils.ToString(o.Order.PaymentMethods.Card.Reference, ""))
		paymentMethodTable.AddValue("autorizacion", utils.ToString(o.Order.PaymentMethods.Card.Authorization, ""))
		paymentMethodTable.AddValue("tarjetaHabiente", utils.ToString(o.Order.PaymentMethods.Card.CardHolder, ""))
		paymentMethodTable.AddValue("numeroTarjeta", utils.ToString(o.Order.PaymentMethods.Card.NumberCard, ""))
		paymentMethodTable.AddValue("numAdquiriente", utils.ToString(o.Order.PaymentMethods.Card.CodeAcquirer, ""))
		paymentMethodTable.AddValue("codigoResultado", utils.ToString(o.Order.PaymentMethods.Card.CodeResponse, ""))
		paymentMethodTable.AddValue("mensajeResultado", utils.ToString(o.Order.PaymentMethods.Card.MessageResponseAut, ""))
		paymentMethodTable.AddValue("TipoMensaje", utils.ToString(o.Order.PaymentMethods.Card.TypeMessage, ""))
		paymentMethodTable.AddValue("CodigoRespuestaAut", utils.ToString(o.Order.PaymentMethods.Card.CodeResponseAut, ""))
		paymentMethodTable.AddValue("NombreRedAdquirente", utils.ToString(o.Order.PaymentMethods.Card.NameRedAcquirer, ""))
		paymentMethodTable.AddValue("HoraTransaccion", utils.ToString(o.Order.PaymentMethods.Card.TransactionTime, ""))
		paymentMethodTable.AddValue("FechaTransaccion", utils.ToString(o.Order.PaymentMethods.Card.TransactionDate, ""))
		paymentMethodTable.AddValue("Publicidad", utils.ToString(o.Order.PaymentMethods.Card.Publicity, ""))
		paymentMethodTable.AddValue("ModoLectura", utils.ToString(o.Order.PaymentMethods.Card.ReadMode, ""))
		paymentMethodTable.AddValue("AID", utils.ToString(o.Order.PaymentMethods.Card.AID, ""))
		paymentMethodTable.AddValue("IdentificacionAplicacion", utils.ToString(o.Order.PaymentMethods.Card.NameCard, ""))
		paymentMethodTable.AddValue("redAdquiriente", utils.ToString(o.Order.PaymentMethods.Card.RedAcquirer, ""))
		paymentMethodTable.AddValue("NombreGrupoTarjeta", utils.ToString(o.Order.PaymentMethods.Card.NameGroupCard, ""))
		paymentMethodTable.AddValue("created_at", currentDate)
		paymentMethodTable.AddValue("updated_at", currentDate)
		_, err = connection.IQuery(paymentMethodTable.SqlGenerated(), paymentMethodTable.GetValues())
		if err != nil {
			return -1, nil, fmt.Errorf("[venezuela.order.go]Error al insertar los datos en la tabla dbo.kiosko_autorizaciones_switch: %v", err)
		}
	}
	//Insercion de forma Pago kiosko
	formaPagoTable := sqlserver.NewSimpleInsertBuilderSQL(false, "dbo.kiosko_forma_pagos")
	formaPagoTable.AddValue("idOrden", idOrder)
	formaPagoTable.AddValue("bin", o.Order.PaymentMethods.Bin)
	formaPagoTable.AddValue("fpf_total_pagar", o.Order.PaymentMethods.TotalBill)
	formaPagoTable.AddValue("created_at", currentDate)
	formaPagoTable.AddValue("updated_at", currentDate)
	_, err = connection.IQuery(formaPagoTable.SqlGenerated(), formaPagoTable.GetValues())
	if err != nil {
		return -1, nil, fmt.Errorf("[venezuela.order.go]Error al insertar los datos en la tabla dbo.kiosko_forma_pagos: %v", err)
	}
	return idOrder, responseDetailsOrder, nil
}
func (o *OrderStore) GetIdClient(connection *sqlserver.DatabaseSql) (*string, *string, error) {
	query := fmt.Sprintf(`SELECT CAST(IDCliente AS NVARCHAR(40)),cli_documento FROM dbo.Cliente WHERE cli_documento = @identificacion`)
	dataQuery := sql.Named("identificacion", o.Order.Cabecera.Client.DocumentNumber)
	if strings.EqualFold("CONSUMIDOR FINAL", o.Order.Cabecera.Client.Name) {
		query = fmt.Sprintf(`SELECT CAST(IDCliente AS NVARCHAR(40)),cli_documento FROM dbo.Cliente WHERE IDTipoDocumento = @idTipoDocumento`)
		dataQuery = sql.Named("idTipoDocumento", o.Order.Cabecera.Client.DocumentId)
	}
	result, err := connection.Query(query, dataQuery)
	if err != nil {
		return nil, nil, fmt.Errorf("[venezuela.order.go]Error al ejecutar el query %v: %v", query, err)
	}
	defer result.Close()
	for result.Next() {
		var idClient, docuement *string
		err = result.Scan(&idClient, &docuement)
		if err != nil {
			return nil, nil, fmt.Errorf("[venezuela.order.go]Error al validar los datos de Cliente: %v", err)
		}
		return idClient, docuement, nil
	}
	return nil, nil, nil
}
func (o *OrderStore) InsertOrUpdateClient(connection *sqlserver.DatabaseSql, accion, tipoDocumento, idUserPos string) (*string, error) {
	estadoWs := 0
	if strings.EqualFold("U", accion) {
		estadoWs = 1
	}
	client := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[config].[IAE_Cliente]")
	client.AddValueParameterized("accion", accion)
	client.AddValueParameterized("tipoConsulta", "W")
	client.AddValueParameterized("tipoDocumeto", tipoDocumento)
	client.AddValueParameterized("documento", o.Order.Cabecera.Client.DocumentNumber)
	client.AddValueParameterized("descripcion", o.Order.Cabecera.Client.Name)
	client.AddValueParameterized("direccion", o.Order.Cabecera.Client.Address)
	client.AddValueParameterized("telefono", o.Order.Cabecera.Client.Phone)
	client.AddValueParameterized("correo", o.Order.Cabecera.Client.Email)
	client.AddValueParameterized("IDUsuario", idUserPos)
	client.AddValueParameterized("estadoWS", estadoWs)
	client.AddValueParameterized("tipoCliente", "")
	rows, err := connection.IQuery(client.GetStoreProcedure(), client.GetValues())
	if err != nil {
		return nil, fmt.Errorf("[venezuela.order.go]Error al ejectuar el sp [config].[IAE_Cliente] %v", err)
	}
	if !strings.EqualFold("U", accion) {
		defer rows.Close()
		for rows.Next() {
			var idClient *string
			err = rows.Scan(&idClient)
			if err != nil {
				return nil, fmt.Errorf("[venezuela.order.go]Error al obtener los datos del sp [config].[IAE_Cliente] %v", err)
			}
			return idClient, nil
		}
	}
	return nil, nil
}

func (o *OrderStore) WsPrintService(connection *sqlserver.DatabaseSql, cfacId, IdOrderHeaderOrder, idUserPos string, dataOrderPrintFastFood []*models.KioskoImpresionOrdenPedidoFastFood, dataOrderVoucherComercio []*models.KioskoImpresionVoucherComercio, dataStation *models.StationKiosco) error {
	//Llamo a al construcutor del apiService
	apiService := printservice.NewApiPrintService(connection)
	var resultPrint *string
	rstId := o.StoreData.RestaurantId
	query := fmt.Sprintf(`SELECT servicio_impresion FROM [config].[fn_ColeccionRestaurante_ServicioImpresion] (@idRestaurante, @idCadena, @idEstacion)`)
	rows, err := connection.Query(query, sql.Named("idRestaurante", rstId),
		sql.Named("idCadena", o.StoreData.ChainId),
		sql.Named("idEstacion", dataStation.IdStation))
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al ejecutar la funcion %v: %v", query, err)
	}
	defer rows.Close()
	for rows.Next() {
		err = rows.Scan(&resultPrint)
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al obtener los datos de la funcion %v: %v", query, err)
		}
	}
	if resultPrint == nil {
		return fmt.Errorf("[venezuela.order.go]error al obtner los datos de la impresora, por favor revisar")
	}
	//
	var printingServiceResponse models.PrintingServiceResponse
	err = json.Unmarshal([]byte(*resultPrint), &printingServiceResponse)
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al parsear los datos de %v: %v", resultPrint, err)
	}
	if strings.EqualFold("efectivo", o.Order.Cabecera.PaymentType) {
		if printingServiceResponse.AplicaEstacion != 1 && printingServiceResponse.AplicaTienda != 1 {
			printOrder := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[pedido].[ORD_impresion_ordenpedido]")
			printOrder.AddValueParameterized("IDCabeceraOrdenPedido", IdOrderHeaderOrder)
			printOrder.AddValueParameterized("IDUsersPos", idUserPos)
			printOrder.AddValueParameterized("rst_id", rstId)
			printOrder.AddValueParameterized("dop_cuenta", 1)
			printOrder.AddValueParameterized("guardaOrden", 0)
			printOrder.AddValueParameterized("todas", 0)
			_, err = connection.IQuery(printOrder.GetStoreProcedure(), printOrder.GetValues())
			if err != nil {
				return fmt.Errorf("[venezuela.order.go]Error al ejecutar el sp [pedido].[ORD_impresion_ordenpedido]: %v", err)
			}
			return nil

		}
	}
	//procesamiento por tarjeta
	if len(dataOrderPrintFastFood) > 0 {
		for _, orderPrint := range dataOrderPrintFastFood {
			if orderPrint.Confirmar != 1 {
				err = apiService.ApiPrint(printingServiceResponse, orderPrint, nil, cfacId, IdOrderHeaderOrder, idUserPos, *dataStation.IdStation)
				if err != nil {
					return fmt.Errorf("[venezuela.order.go]Error al imprimir la orden del pedido: %v", err)
				}
			}

		}

	}
	if len(dataOrderVoucherComercio) > 0 {
		for _, commerceVoucher := range dataOrderVoucherComercio {
			err = apiService.ApiPrint(printingServiceResponse, nil, commerceVoucher, cfacId, IdOrderHeaderOrder, idUserPos, *dataStation.IdStation)
			if err != nil {
				return fmt.Errorf("[venezuela.order.go]Error al imprimir el voucher del cliente: %v", err)
			}
		}

	}
	return nil
}
func (o *OrderStore) printOrderIFACT(connection *sqlserver.DatabaseSql, cfacId string) error {
	var ipIfact,
		urlIfact,
		jsonElectronicBilling *string

	cdnId := o.StoreData.ChainId
	query := fmt.Sprintf(`SELECT rcdd.variableV FROM ColeccionRestaurante cr
        INNER JOIN ColeccionDeDatosRestaurante cddr on cr.ID_ColeccionRestaurante=cddr.ID_ColeccionRestaurante
        INNER JOIN RestauranteColeccionDeDatos rcdd on cr.ID_ColeccionRestaurante=rcdd.ID_ColeccionRestaurante and cddr.ID_ColeccionRestaurante=rcdd.ID_ColeccionRestaurante
        where cr.Descripcion='CONFIGURACION KIOSKO IMPRESION IFACT'
        AND cddr.Descripcion='IP IMPRESION IFACT'
        AND cr.isActive='1'
        AND rcdd.isActive='1'
        AND cddr.isActive='1'`)
	rows, err := connection.Query(query)
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al ejecutar el query %v: %v", query, err.Error())
	}
	defer rows.Close()
	for rows.Next() {
		err = rows.Scan(&ipIfact)
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al obtener los datos del query %v: %v", query, err.Error())
		}
	}
	if utils.IsEmpty(ipIfact) {
		query = fmt.Sprintf(`SELECT ecdd.variableV FROM ColeccionEstacion ce
            INNER JOIN ColeccionDeDatosEstacion cdde on ce.ID_ColeccionEstacion=cdde.ID_ColeccionEstacion
            INNER JOIN EstacionColeccionDeDatos ecdd on ce.ID_ColeccionEstacion=ecdd.ID_ColeccionEstacion 
            and cdde.ID_ColeccionDeDatosEstacion=ecdd.ID_ColeccionDeDatosEstacion
            INNER JOIN Estacion e on e.IDEstacion = ecdd.IDEstacion
            where ce.Descripcion='CONFIGURACION KIOSKO IMPRESION IFACT'
            AND cdde.Descripcion='IP IMPRESION IFACT'
            AND ce.isActive='1'
            AND ecdd.isActive='1'
            AND cdde.isActive='1'
            AND e.est_ip='%v'`, o.Order.Cabecera.IpKiosk)
		rows, err = connection.Query(query)
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al ejecutar el query %v: %v", query, err.Error())
		}
		defer rows.Close()
		for rows.Next() {
			err = rows.Scan(&ipIfact)
			if err != nil {
				return fmt.Errorf("[venezuela.order.go]Error al obtener los datos del query %v: %v", query, err.Error())
			}
		}
	}
	if utils.IsEmpty(ipIfact) {
		return errors.New("[venezuela.order.go]Error, no encontro una ip para la generacion de la impresion IFACT")
	}
	fnUrl := fmt.Sprintf("SELECT [config].[fn_ColeccionCadena_NombreUrlIFAC](%v,'%v') as url", cdnId, *ipIfact)
	rowUrl, err := connection.Query(fnUrl)
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al ejecutar la funcion %v: %v", fnUrl, err.Error())
	}
	defer rowUrl.Close()
	for rowUrl.Next() {
		err = rowUrl.Scan(&urlIfact)
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al obtener los datos de la funcion %v: %v", fnUrl, err.Error())
		}
	}
	electronicBilling := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[Cabecera_Facturacion_Electronica_Venezuela]")
	electronicBilling.AddValueParameterized("cadena", cdnId)
	electronicBilling.AddValueParameterized("restaurante", o.StoreData.RestaurantId)
	electronicBilling.AddValueParameterized("cfac_id", cfacId)
	rowsJson, err := connection.IQuery(electronicBilling.GetStoreProcedure(), electronicBilling.GetValues())
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al ejecutar el sp facturacion.Cabecera_Facturacion_Electronica_Venezuela: %v", err.Error())
	}
	defer rowsJson.Close()
	for rowsJson.Next() {
		err = rowsJson.Scan(&jsonElectronicBilling)
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al obtener los datos del sp facturacion.Cabecera_Facturacion_Electronica_Venezuela: %v", err.Error())
		}

	}
	if jsonElectronicBilling == nil {
		return errors.New("[venezuela.order.go]Error, no se obtuvo el json de la facturacion electronica para el cfac_id " + cfacId)
	}
	cleanJSON := utils.RemoveInvalidChars(*jsonElectronicBilling)

	// Crear cliente con timeout
	client := &http.Client{
		Timeout: 40 * time.Second,
	}

	req, err := http.NewRequest("POST", *urlIfact, bytes.NewBufferString(cleanJSON))
	if err != nil {
		return fmt.Errorf(`{"status": "ERROR","codigoRespuesta": "", "mensaje":"%s", "mensajeRespuesta":"%s", "objetoRespuesta": null}`, err.Error(), err.Error())
	}
	req.Header.Set("Content-Type", "application/json")
	req.Header.Set("Accept", "application/json")

	resp, err := client.Do(req)
	if err != nil {
		return fmt.Errorf(`{"status": "ERROR","codigoRespuesta": "", "mensaje":"%s", "mensajeRespuesta":"%s", "objetoRespuesta": null}`, err.Error(), err.Error())
	}
	defer resp.Body.Close()

	body, err := io.ReadAll(resp.Body)
	if err != nil {
		return fmt.Errorf(`{"status": "ERROR","codigoRespuesta": "", "mensaje":"%s", "mensajeRespuesta":"%s", "objetoRespuesta": null}`, err.Error(), err.Error())
	}
	logger.Info.Println("Respuesta obtenida del api de facturacion electronica: " + string(body))
	return nil
}
func (o *OrderStore) SendOrderTurner(cfacId string) {
	turner := &lib_gen_proto.Turner{}
	order := cfacId[len(cfacId)-2:]
	var newChainId string
	var newRestaurantID string
	if strings.EqualFold("efectivo", o.Order.Cabecera.PaymentType) {
		turner.Status = "ingresado"
		turner.Rout = "order"
		newChainId = "0"
		newRestaurantID = "0"
	} else {
		turner.Status = "preparando"
		turner.Rout = "transaction"
		newChainId, _ = utils.ConvertNumberToString(o.StoreData.ChainId)
		newRestaurantID, _ = utils.ConvertNumberToString(o.StoreData.RestaurantId)

	}
	turner.ChainId = newChainId
	turner.RestaurantId = newRestaurantID
	turner.Type = "KIOSKO"
	turner.Order = order
	turner.Transaction = cfacId
	turner.ClientDocument = o.Order.Cabecera.Client.DocumentNumber
	turner.Client = o.Order.Cabecera.Client.Name
	msg := events.NewEvent("turner.order", turner)
	//envio hacia el turnero
	err := o.NatsClient.EventSender.Execute(msg)
	if err != nil {
		fmt.Println(err)
	}
}
func (o *OrderStore) SendOrderResponse(order *lib_gen_proto.ResultStoreValidationData) {
	msg := events.NewEvent("response.order", order)
	err := o.NatsClient.EventSender.Execute(msg)
	if err != nil {
		fmt.Println(err)
	}
}
func (o *OrderStore) Execute() (err error) {
	defer func() {
		if r := recover(); r != nil {
			stackTrace := string(debug.Stack())
			// Convertir el panic y el stack trace en un error detallado
			err = fmt.Errorf("se produjo un panic: %v\nStack trace:\n%s", r, stackTrace)
		}
	}()
	if strings.EqualFold(o.Order.Cabecera.PaymentType, "efectivo") {
		return o.CreateOrderKioskEfectivo()
	}
	return o.CreateOrderKioskTarjeta()
}

func (o *OrderStore) InsertKioskPayment(connection *sqlserver.DatabaseSql, idOrden int32, codFactura, cfacTotal, user string) error {
	rowsKas, err := connection.Query("SELECT SUBSTRING(numeroTarjeta, 1, 6), NombreGrupoTarjeta, autorizacion FROM kiosko_autorizaciones_switch WHERE idOrden = @p1", sql.Named("p1", idOrden))
	if err != nil {
		return fmt.Errorf("SELECT SUBSTRING(numeroTarjeta, 1, 6), NombreGrupoTarjeta, autorizacion FROM kiosko_autorizaciones_switch WHERE idOrden = @p1: %v", err)
	}
	var bin, authorizationNumber, fmpDescription, idFormaPagoBin, typeSwt *string
	defer rowsKas.Close()
	for rowsKas.Next() {
		err = rowsKas.Scan(&bin, &fmpDescription, &authorizationNumber)
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al escanear el resultado de SELECT SUBSTRING(numeroTarjeta, 1, 6), NombreGrupoTarjeta, autorizacion FROM kiosko_autorizaciones_switch WHERE idOrden = @p1: %v", err)
		}
	}
	rowsFp, err := connection.Query("SELECT CAST(IDFormapago AS VARCHAR(40)) FROM formaPago where fmp_descripcion=@p1", sql.Named("p1", fmpDescription))
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al ejecutar SELECT IDFormapago FROM formaPago where fmp_descripcion=@p1: %v", err)
	}
	defer rowsFp.Close()
	for rowsFp.Next() {
		err = rowsFp.Scan(&idFormaPagoBin)
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al escanear el resultado de  SELECT IDFormapago FROM formaPago where fmp_descripcion=@p1: %v", err)
		}
	}
	rowsTypeSwt, err := connection.Query("SELECT [config].[fn_obtieneIdSwitchPorNombre]('SITEF')")
	if err != nil {
		return fmt.Errorf("[venezuela.order.go]Error al ejecutar SELECT [config].[fn_obtieneIdSwitchPorNombre]('SITEF'): %v", err)
	}
	defer rowsTypeSwt.Close()
	for rowsTypeSwt.Next() {
		err = rowsTypeSwt.Scan(&typeSwt)
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al escanear el resultado de  SELECT [config].[fn_obtieneIdSwitchPorNombre]('SITEF'): %v", err)
		}
	}
	if idFormaPagoBin != nil && typeSwt != nil {
		var fcInsertarFormaPago string
		if strings.EqualFold(o.Order.Cabecera.PaymentType, "pagoMovil") {
			fcInsertarFormaPago = "facturacion.fac_insertaFormaPago_kiosko_pagoMovil"
		} else {
			fcInsertarFormaPago = "facturacion.fac_insertaFormaPago_kiosko_V2"
		}
		insertPaymentMethod := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, fcInsertarFormaPago)
		insertPaymentMethod.AddValueParameterized("cfac", codFactura)
		if strings.EqualFold(o.Order.Cabecera.PaymentType, "pagoMovil") {
			insertPaymentMethod.AddValueParameterized("fmp_id", "")
		} else {
			insertPaymentMethod.AddValueParameterized("fmp_id", idFormaPagoBin)
		}
		insertPaymentMethod.AddValueParameterized("num", bin)
		insertPaymentMethod.AddValueParameterized("valor", o.Order.PaymentMethods.TotalBill)
		insertPaymentMethod.AddValueParameterized("total", cfacTotal)
		insertPaymentMethod.AddValueParameterized("prop", 0)
		insertPaymentMethod.AddValueParameterized("swt", typeSwt)
		insertPaymentMethod.AddValueParameterized("usr_id", user)
		insertPaymentMethod.AddValueParameterized("autorizacion", authorizationNumber)
		_, err = connection.IQuery(insertPaymentMethod.GetStoreProcedure(), insertPaymentMethod.GetValues())
		if err != nil {
			return fmt.Errorf("[venezuela.order.go]Error al insertar los datos de laforma de pago %s: %v", fcInsertarFormaPago, err)
		}
	}
	return nil
}
