package colombia

import (
	"crypto/sha512"
	"database/sql"
	"encoding/hex"
	"encoding/json"
	"errors"
	"fmt"
	"lib-shared/protos/lib_gen_proto"
	"lib-shared/utils"
	"lib-shared/utils/logger"
	"new-order-store/internals/domain/business"
	"new-order-store/internals/domain/business/colombia/masterdataclient"
	"new-order-store/internals/domain/business/colombia/printservice"
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

var cacheStation = cache.NewTTL[string, *models.StationKiosco]()
var cacheIdMesa = cache.NewTTL[string, string]()

type OrderStore struct {
	DatabaseCredential *credential.DatabaseCredential
	Order              *lib_gen_proto.Order
	StoreData          *maxpoint.StoreData
	Feature            *featureflag.FeatureFlag
	NatsClient         *natsmodule.NatsStarter
	regionalKiosk      execute.RegionalKioskExecute
}

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
func (o *OrderStore) CreateOrderKioskEfectivo() (err error) {
	defer func() {
		if r := recover(); r != nil {
			stackTrace := string(debug.Stack())
			// Convertir el panic y el stack trace en un error detallado
			err = fmt.Errorf("se produjo un panic: %v\nStack trace:\n%s", r, stackTrace)
		}
	}()
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
	generateSecuencial := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "facturacion.KIOSKO_generarSecuencialFactura")
	generateSecuencial.AddValueParameterized("idRestaurante", o.StoreData.RestaurantId)
	rows, err := connection.IQuery(generateSecuencial.GetStoreProcedure(), generateSecuencial.GetValues())
	if err != nil {
		return fmt.Errorf("[colombia.order.go]Error al ejecutar el sp facturacion.KIOSKO_generarSecuencialFactura: %v", err.Error())
	}
	defer rows.Close()
	for rows.Next() {
		err = rows.Scan(&cfacId)
		if err != nil {
			return fmt.Errorf("[colombia.order.go]Error al obtener los datos de KIOSKO_generarSecuencialFactura: %v", err.Error())
		}
	}
	if cfacId == nil {
		return fmt.Errorf("[colombia.order.go]Error - El cfacId generado del sp [facturacion].[KIOSKO_generarSecuencialFactura] esta vacio, por favor revisar")
	}
	idTypeDocument := o.StoreData.GetTipoDocumento(documentId)
	if idTypeDocument == nil {
		return fmt.Errorf("[colombia.order.go]No se encontro el tipo de documento para el id %s", documentId)
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
		return fmt.Errorf("[colombia.order.go]No se encontro el id del usuario para kiosko")
	}
	idControlStation, err := o.GetStationControl(connection, resultStation)
	if err != nil {
		return err
	}
	if idControlStation == nil {
		return fmt.Errorf("[colombia.order.go]La estacion de kiosko no se encuentra activa")
	}
	idPeriod, err := o.GetOpenPeriod(connection)
	if err != nil {
		return err
	}
	if idPeriod == nil {
		return fmt.Errorf("[colombia.order.go]No se encontro ningun periodo abierto")
	}
	idMesa, err := o.GetIdMesa(connection, resultStation)
	if err != nil {
		return err
	}

	newTotalBill, _ := utils.StrToFloat32(o.Order.PaymentMethods.TotalBill)
	if !strings.EqualFold(o.Order.PaymentMethods.Bin, "efec") {
		if o.Order.PaymentMethods.Bin != "999999" {
			return errors.New("[colombia.order.go]La forma de pago de la transacción ingresada no es efectivo")
		}
	}
	if newTotalBill > 0 {
		err = connection.CreateBegin()
		defer connection.Rollback()
		if err != nil {
			return fmt.Errorf("[colombia.order.go]Error al crear BEGIN: %v", err.Error())
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
			return fmt.Errorf("[colombia.order.go]Error al insertar el update BEGIN: %v", err.Error())
		}
		_, err = exec.RowsAffected()
		if err != nil {
			return fmt.Errorf("[colombia.order.go]Error al actualizar la tabla Cabecera_Orden_Pedido %s", err.Error())
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
			sql.Named("idEstadoOrdenPendiente", statusMxp.OrdenPedidoCerrada))
		if err != nil {
			return fmt.Errorf("[colombia.order.go]Error al insertar los datos en la tabla Cabecera_Orden_Pedido %v : %v", insertCabeceraOrdenPedido, err.Error())
		}
		var idCabeceraOrdenPedido string
		err = insertData.Scan(&idCabeceraOrdenPedido)
		if err != nil {
			return fmt.Errorf("[colombia.order.go]Error al obtener el id de la Cabecera_Orden_Pedido: %v", err.Error())
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
				return fmt.Errorf("[colombia.order.go]Error al ejecutar el sp facturacion.IAE_Fac_InsertFactura_kioskoEfectivo:%v", err.Error())
			}
		}
		connection.Commit()

		order := &lib_gen_proto.ResultStoreValidationData{}
		order.OrderId = orderId
		order.CfacId = *cfacId
		o.SendOrderResponse(order)
		o.SendOrderTurner(*cfacId)

		// Enviar orden al KDS Regional
		kdsService := business.NewKDSRegionalService(connection, o.StoreData, o.Order, *resultStation.IdStation, *cfacId, *resultStation.CashierName)
		kdsService.SendOrderToKDSAsync()

		defer func() {
			errPrintService := o.WsPrintService(connection, *cfacId, idCabeceraOrdenPedido, *idUserPos, nil, resultStation)
			if errPrintService != nil {
				logger.Error.Println(errPrintService.Error())
			}
			errMasterDataClient := o.MasterDataClient(connection)
			if errMasterDataClient != nil {
				logger.Error.Println(errMasterDataClient.Error())
			}
		}()
		return nil
	}

	return fmt.Errorf("[colombia.order.go]El total de la transacción debe ser mayor a 0")
}
func (o *OrderStore) CreateOrderKioskTarjeta() (err error) {
	defer func() {
		if r := recover(); r != nil {
			stackTrace := string(debug.Stack())
			// Convertir el panic y el stack trace en un error detallado
			err = fmt.Errorf("se produjo un panic: %v\nStack trace:\n%s", r, stackTrace)
		}
	}()
	paymentType := o.Order.Cabecera.PaymentType
	orderId := o.Order.Cabecera.OrderId
	statusMxp := o.StoreData.Status
	order := &lib_gen_proto.ResultStoreValidationData{}
	//currentDate := time.Now()
	connection, err := sqlserver.NewConnectionSql(o.DatabaseCredential)
	if err != nil {
		return err
	}
	defer func() {
		connection.Close()
	}()
	idPeriod, err := o.GetOpenPeriod(connection)
	if err != nil {
		return err
	}
	if idPeriod == nil {
		return fmt.Errorf("[colombia.order.go]No se encontro ningun periodo abierto")
	}

	resultStation, err := o.GetStationKiosko(connection)
	if err != nil {
		return err
	}
	err = o.CheckIfAlreadyExistOrder(connection, orderId)
	if err != nil {
		return err
	}
	idControlStation, err := o.GetStationControl(connection, resultStation)
	if err != nil {
		return err
	}
	if idControlStation == nil {
		return fmt.Errorf("[colombia.order.go]La estacion de kiosko no se encuentra activa")
	}
	idMesa, err := o.GetIdMesa(connection, resultStation)
	if err != nil {
		return err
	}

	idUserPos, err := o.GetIdUserPos(connection, resultStation)
	if err != nil {
		return err
	}
	if idUserPos == nil {
		return fmt.Errorf("[colombia.order.go]No se encontro el id del usuario para kiosko")
	}

	//obtener el idFormaPago para tarjeta o para nequi
	var idPaymentMethod *string
	if strings.EqualFold(paymentType, "tarjeta") || strings.EqualFold(o.Order.PaymentMethods.Card.Processor, "DAVIPLATA") {
		queryPaymentMethod := fmt.Sprintf(`SELECT top 1
										CAST(fp.IDFormapago as varchar(40))  
									FROM 
									dbo.ColeccionCadena cd INNER JOIN dbo.ColeccionDeDatosCadena cdc ON cd.ID_ColeccionCadena=cdc.ID_ColeccionCadena
									INNER JOIN dbo.CadenaColeccionDeDatos ccd ON ccd.ID_ColeccionDeDatosCadena=cdc.ID_ColeccionDeDatosCadena
									INNER JOIN dbo.Formapago fp 
									ON CAST(fp.IDFormapago AS VARCHAR(40))=CAST(cdc.idIntegracion AS VARCHAR(40))
									WHERE 
									cd.Descripcion='TEXTO DE RESPUESTA PARA PAGOS CON TARJETA' AND 
									ccd.variableV=@tipoCuenta AND 
									cdc.Descripcion=@descripcionGrupoTarjeta AND
									fp.cdn_id=@cadena AND
									cd.cdn_id=@cadena`)
		rows, err := connection.Query(queryPaymentMethod,
			sql.Named("tipoCuenta", o.Order.PaymentMethods.Card.TypeAccount),
			sql.Named("descripcionGrupoTarjeta", getProcessorFranchise(o.Order.PaymentMethods.Card.Processor, o.Order.PaymentMethods.Card.Franchise)),
			sql.Named("cadena", o.StoreData.ChainId))
		if err != nil {
			return fmt.Errorf("[colombia.order.go]Error al ejecutar el query %v con parametros %v: %v",
				queryPaymentMethod,
				connection.GetArgs(
					sql.Named("tipoCuenta", o.Order.PaymentMethods.Card.TypeAccount),
					sql.Named("descripcionGrupoTarjeta", getProcessorFranchise(o.Order.PaymentMethods.Card.Processor, o.Order.PaymentMethods.Card.Franchise)),
					sql.Named("cadena", o.StoreData.ChainId)),
				err.Error())
		}
		defer rows.Close()
		for rows.Next() {
			err = rows.Scan(&idPaymentMethod)
			if err != nil {
				return fmt.Errorf("[colombia.order.go]Error al obtener el idFormaPago: %v", err)
			}
		}
	} else {
		queryPaymentMethod := fmt.Sprintf(`SELECT config.fn_GetIdFormaPagoQR(@descripcionGrupoTarjeta, @tipoCuenta) AS idFormaPagoQR`)
		rows, err := connection.Query(queryPaymentMethod,
			sql.Named("descripcionGrupoTarjeta", o.Order.PaymentMethods.Card.Franchise),
			sql.Named("tipoCuenta", o.Order.PaymentMethods.Card.TypeAccount),
		)
		if err != nil {
			return fmt.Errorf("[colombia.order.go]Error al ejecutar el query %v con parametros %v: %v",
				queryPaymentMethod,
				connection.GetArgs(
					sql.Named("descripcionGrupoTarjeta", o.Order.PaymentMethods.Card.Franchise),
					sql.Named("tipoCuenta", o.Order.PaymentMethods.Card.TypeAccount),
				),
				err)
		}
		defer rows.Close()
		for rows.Next() {
			err = rows.Scan(&idPaymentMethod)
			if err != nil {
				return fmt.Errorf("[colombia.order.go]Error al obtener el idFormaPago: %v", err)
			}
		}
	}
	if idPaymentMethod == nil || *idPaymentMethod == "0" {
		return fmt.Errorf("[colombia.order.go]Error validacion de idFormaPago %s - %s", o.Order.PaymentMethods.Card.Franchise, o.Order.PaymentMethods.Card.TypeAccount)
	}

	var status42 *string
	queryStatus42 := "SELECT CAST(CONVERT(UNIQUEIDENTIFIER, HASHBYTES('MD5','42')) AS VARCHAR(40))"
	err = connection.QueryRow(queryStatus42).Scan(&status42)
	if err != nil {
		return fmt.Errorf("[colombia.order.go]Error al ejecutar el query %v: %v", queryStatus42, err)
	}
	if status42 == nil {
		return errors.New("[colombia.order.go]Error, no se genero un status42 para la tabla la dbo.SWT_Respuesta_Autorizacion y el campo IDStatus")
	}
	//Procesamiento de tarjeta
	err = connection.CreateBegin()
	defer connection.Rollback()
	if err != nil {
		return fmt.Errorf("[colombia.order.go]Error al crear BEGIN: %v", err.Error())
	}
	documentId := o.Order.Cabecera.Client.DocumentId
	idTypeDocument := o.StoreData.GetTipoDocumento(documentId)
	if idTypeDocument == nil {
		return fmt.Errorf("[colombia.order.go]No se encontro el tipo de documento para el id %s", documentId)
	}
	if strings.EqualFold(idTypeDocument.Description, "CONSUMIDOR FINAL") {
		o.Order.Cabecera.Client.Email = "consumidor.final@kfc.com.ec"
		o.Order.Cabecera.Client.Address = "Quito"
		o.Order.Cabecera.Client.DocumentNumber = "9999999999"
		o.Order.Cabecera.Client.Phone = "2222222"
	}
	idOrderKiosko, datailsKiosko, err := o.InsertOrderKiosko(connection, nil)
	if err != nil {
		return err
	}

	//Variables de procesamiento
	dataInvoicing := &models.ResponseInvoicing{}
	currentDate := time.Now()
	var idCabeceraOrdenPedido string
	//Insercion de datos a la tabla CabeceraOrdenPedido

	insertHeaderOrder := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[pedido].[ORD_configuracion_proceso_ordenpedido_Newkiosko]")
	insertHeaderOrder.AddValueParameterized("rst_id", o.StoreData.RestaurantId)
	insertHeaderOrder.AddValueParameterized("IDMesa", idMesa)
	insertHeaderOrder.AddValueParameterized("IDUsersPos", idUserPos)
	insertHeaderOrder.AddValueParameterized("IDEstacion", resultStation.IdStation)
	insertHeaderOrder.AddValueParameterized("num_Pers", 1)
	insertHeaderOrder.AddValueParameterized("idOrdenPedido", sql.Out{Dest: &idCabeceraOrdenPedido})
	_, err = connection.IQuery(insertHeaderOrder.GetStoreProcedure(), insertHeaderOrder.GetValues())
	if err != nil {
		return fmt.Errorf("[colombia.order.go]Error al ejecutar el sp [pedido].[ORD_configuracion_proceso_ordenpedido_Newkiosko]: %v", err)
	}
	if utils.IsEmpty(idCabeceraOrdenPedido) {
		return fmt.Errorf("[colombia.order.go]Error al obtener el id de la tabla cabecera_orden_pedido")
	}

	//insercion de los productos a la tabla detalle_orden_pedido
	err = o.InsertDetailOrder(connection, datailsKiosko, idCabeceraOrdenPedido)
	if err != nil {
		return err
	}

	//Insercion de datos para la generacion de factura
	insertInvoicing := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[IAE_Fac_InsertFactura_co]")
	insertInvoicing.AddValueParameterized("IDRestaurante", o.StoreData.RestaurantId)
	insertInvoicing.AddValueParameterized("IDCabeceraOrdenPedido", idCabeceraOrdenPedido)
	insertInvoicing.AddValueParameterized("IDUsersPos", idUserPos)
	insertInvoicing.AddValueParameterized("numeroCuenta", 1)
	insertInvoicing.AddValueParameterized("IDEstacion", resultStation.IdStation)
	insertInvoicing.AddValueParameterized("IDPeriodo", idPeriod)
	insertInvoicing.AddValueParameterized("IDControlEstacion", idControlStation)
	insertInvoicing.AddValueParameterized("tipoBeneficioCupon", 0)
	if strings.EqualFold(paymentType, "tarjeta") {
		insertInvoicing.AddValueParameterized("canalKiosko", 1)
	}

	rowsInvoicing, err := connection.IQuery(insertInvoicing.GetStoreProcedure(), insertInvoicing.GetValues())
	if err != nil {
		return fmt.Errorf("[colombia.order.go]Error al ejecutar el sp [facturacion].[IAE_Fac_InsertFactura_co]: %v", err)
	}
	defer rowsInvoicing.Close()

	for rowsInvoicing.Next() {
		err = rowsInvoicing.Scan(&dataInvoicing.CdnId,
			&dataInvoicing.CdnTipoImpuesto,
			&dataInvoicing.RstDescripcion,
			&dataInvoicing.RstTipoServicio,
			&dataInvoicing.CfacId,
			&dataInvoicing.StdId,
			&dataInvoicing.UsrId,
			&dataInvoicing.EstId,
			&dataInvoicing.CfacFechaCreacion,
			&dataInvoicing.PluId,
			&dataInvoicing.DtfacCantidad,
			&dataInvoicing.DtfacPrecioUnitario,
			&dataInvoicing.DtfacIva,
			&dataInvoicing.DtfacTotal,
			&dataInvoicing.PluDescripcion,
			&dataInvoicing.PluImpuesto,
			&dataInvoicing.Totalizado,
			&dataInvoicing.Servicio,
			&dataInvoicing.CfacDescuentoEmpresa,
			&dataInvoicing.CodFactura,
			&dataInvoicing.BtnCancelPago,
			&dataInvoicing.DescProducto,
			&dataInvoicing.ValorFijo,
			&dataInvoicing.Porcentaje,
			&dataInvoicing.CanjePuntos,
			&dataInvoicing.Puntos,
			&dataInvoicing.TipoBeneficioCupon,
			&dataInvoicing.ColorBeneficioCupon,
		)
		if err != nil {
			return fmt.Errorf("[colombia.order.go]Error al obtener los datos del sp [facturacion].[IAE_Fac_InsertFactura_co]: %v", err)
		}
	}
	if dataInvoicing.CfacId == nil {
		return fmt.Errorf("[colombia.order.go]Error - El cfacId generado del sp [facturacion].[IAE_Fac_InsertFactura_co] esta vacio, por favor revisar")
	}
	cfacId := dataInvoicing.CfacId
	cfacTotal := dataInvoicing.DtfacTotal
	//Actualizo la tabla kiosko_cabecera_pedidos con el cfacId
	updateKioskoHeader := fmt.Sprintf(`UPDATE dbo.kiosko_cabecera_pedidos
				SET cfac_id = @codFactura
				WHERE id = @idPedido`)
	rowsKioskoHeader, err := connection.Exec(updateKioskoHeader, sql.Named("codFactura", cfacId), sql.Named("idPedido", idOrderKiosko))
	if err != nil {
		return fmt.Errorf("[colombia.order.go]Error al actualizar los datos de la tabla kiosko_cabecera_pedidos: %v", err)
	}
	affectedKioskoHeader, err := rowsKioskoHeader.RowsAffected()
	if err != nil {
		return err
	}
	if affectedKioskoHeader < 1 {
		return fmt.Errorf("[colombia.order.go]No se ha podio actualizar los datos de la tabla kiosko_cabecera_pedidos")
	}

	//Actualizo la tabla Cabecera_Factura para el Identificador kiosko tarjeta en cab factura
	kioskIdentifier := "KIOSKO TARJETA"
	/*if strings.EqualFold(paymentType, "nequi") {
		kioskIdentifier = "KIOSKO NEQUI"
	}*/
	updateInvoiceHeader := fmt.Sprintf(`UPDATE dbo.Cabecera_Factura
				SET Cabecera_FacturaVarchar2 = @kioskIdentifier
				WHERE cfac_id = @codFactura`)
	rowsInvoiceHeader, err := connection.Exec(
		updateInvoiceHeader,
		sql.Named("codFactura", cfacId),
		sql.Named("kioskIdentifier", kioskIdentifier),
	)
	if err != nil {
		return fmt.Errorf("[colombia.order.go]Error al actualizar los datos de la tabla Cabecera_Factura: %v", err)
	}
	affectedInvoiceHeader, err := rowsInvoiceHeader.RowsAffected()
	if err != nil {
		return err
	}
	if affectedInvoiceHeader < 1 {
		return fmt.Errorf("[colombia.order.go]No se ha podio actualizar los datos de la tabla Cabecera_Factura")
	}

	//Actualizo la tabla cabecera_orden_pedido
	UpdateOrderHeaderOrder := fmt.Sprintf(`UPDATE dbo.Cabecera_Orden_Pedido
				SET odp_observacion = @nombres + ' - ' + @tipo
				WHERE IDCabeceraOrdenPedido = @pedido`)

	rowsOrderHeader, err := connection.Exec(UpdateOrderHeaderOrder,
		sql.Named("nombres", o.Order.Cabecera.Client.Name),
		sql.Named("tipo", o.Order.Cabecera.TypeService),
		sql.Named("pedido", idCabeceraOrdenPedido))
	if err != nil {
		return fmt.Errorf("[colombia.order.go]Error al actualizar los datos de la tabla Cabecera_Orden_Pedido: %v", err)
	}
	affectedOrderHeader, err := rowsOrderHeader.RowsAffected()
	if err != nil {
		return err
	}
	if affectedOrderHeader < 1 {
		return fmt.Errorf("[colombia.order.go]No se ha podio actualizar los datos de la tabla cabecera_orden_pedido")
	}

	idClient, document, err := o.GetIdClient(connection)
	if err != nil {
		return err
	}
	dataTypeDocument := o.StoreData.GetTipoDocumento(o.Order.Cabecera.Client.DocumentId)
	if !strings.EqualFold(dataTypeDocument.Description, "CONSUMIDOR FINAL") {
		if strings.EqualFold(dataTypeDocument.Description, "NIT") {
			partes := strings.Split(o.Order.Cabecera.Client.DocumentNumber, "-")
			var digNit *string
			queryDv := fmt.Sprintf("SELECT [dbo].[ObtenerDigitoVerificador] (%v)", partes[0])
			rowsDv, err := connection.Query(queryDv)
			if err != nil {
				return fmt.Errorf("[colombia.order.go]Error al ejectuar la func %s: %w", queryDv, err)
			}
			defer rowsDv.Close()
			for rowsDv.Next() {
				err = rowsDv.Scan(&digNit)
				if err != nil {
					return fmt.Errorf("[colombia.order.go]Error al obtener los datos de la func %s: %w", queryDv, err)
				}
			}
			if digNit != nil {
				newDocument := partes[0]
				newDocument = newDocument + "-" + *digNit
				o.Order.Cabecera.Client.DocumentNumber = newDocument
			}
		}
		if idClient != nil {
			client := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[config].[IAE_Cliente]")
			client.AddValueParameterized("accion", "U")
			client.AddValueParameterized("tipoConsulta", "W")
			client.AddValueParameterized("tipoDocumeto", dataTypeDocument.Description)
			client.AddValueParameterized("documento", document)
			client.AddValueParameterized("descripcion", o.Order.Cabecera.Client.Name)
			client.AddValueParameterized("direccion", o.Order.Cabecera.Client.Address)
			client.AddValueParameterized("telefono", o.Order.Cabecera.Client.Phone)
			client.AddValueParameterized("correo", o.Order.Cabecera.Client.Email)
			client.AddValueParameterized("IDUsuario", idUserPos)
			client.AddValueParameterized("estadoWS", 0)
			client.AddValueParameterized("tipoCliente", "")
			_, err = connection.IQuery(client.GetStoreProcedure(), client.GetValues())
			if err != nil {
				return fmt.Errorf("[colombia.order.go]Error al ejectuar el sp [config].[IAE_Cliente] %w", err)
			}
		} else {
			insertClient := fmt.Sprintf(`INSERT INTO dbo.Cliente(IDCliente,
						IDTipoDocumento,
						cli_nombres, 
						cli_apellidos,
						cli_documento, 
						cli_telefono,
						cli_direccion, 
						cli_email, 
						UsrModifica, 
						FechaCreacion,
						FechaActualizacion,
						cli_varchar1)
						OUTPUT CAST(INSERTED.IDCliente AS NVARCHAR(40))
						VALUES (NEWID(),
						    @IDTipoDocumento,
						    @nombres,
						    ' ',
						    @identificacion,
						    @telefono, 
							@direccion, 
							@email, 
							@usuario, 
							@TimeInsert,
							@TimeInsert,
							'RC201')`)

			_, err = connection.Query(insertClient,
				sql.Named("IDTipoDocumento", dataTypeDocument.Id),
				sql.Named("nombres", o.Order.Cabecera.Client.Name),
				sql.Named("identificacion", document),
				sql.Named("telefono", o.Order.Cabecera.Client.Phone),
				sql.Named("direccion", o.Order.Cabecera.Client.Address),
				sql.Named("email", o.Order.Cabecera.Client.Email),
				sql.Named("usuario", idUserPos),
				sql.Named("TimeInsert", currentDate),
			)
			if err != nil {
				return fmt.Errorf("[colombia.order.go]Error al insertar los datos en la tabla Cliente %v con parametros %v: %w",
					insertClient,
					connection.GetArgs(
						sql.Named("IDTipoDocumento", dataTypeDocument.Id),
						sql.Named("nombres", o.Order.Cabecera.Client.Name),
						sql.Named("identificacion", o.Order.Cabecera.Client.DocumentNumber),
						sql.Named("telefono", o.Order.Cabecera.Client.Phone),
						sql.Named("direccion", o.Order.Cabecera.Client.Address),
						sql.Named("email", o.Order.Cabecera.Client.Email),
						sql.Named("usuario", idUserPos),
						sql.Named("TimeInsert", currentDate),
					),
					err)
			}

			idClient, document, err = o.GetIdClient(connection)
			if err != nil {
				return err
			}
		}
	}

	//Insercion a la tabla SWT_Requerimiento_Autorizacion
	authorizationRequirement := sqlserver.NewSimpleInsertBuilderSQL(false, "dbo.SWT_Requerimiento_Autorizacion")
	authorizationRequirement.AddValue("rqaut_fecha", currentDate)
	authorizationRequirement.AddValue("rqaut_ip", o.Order.Cabecera.IpKiosk)
	authorizationRequirement.AddValue("rqaut_puerto", "8080")
	authorizationRequirement.AddValue("rqaut_trama", cfacId)
	authorizationRequirement.AddValue("rqaut_movimiento", cfacId)
	authorizationRequirement.AddValue("tpenv_id", statusMxp.IdPinpadUnired)
	authorizationRequirement.AddValue("IDFormapagoFactura", idPaymentMethod)
	authorizationRequirement.AddValue("IDEstacion", resultStation.IdStation)
	authorizationRequirement.AddValue("IDUsersPos", idUserPos)
	authorizationRequirement.AddValue("IDStatus", statusMxp.SesionesActivo)
	authorizationRequirement.AddValue("replica", 0)
	authorizationRequirement.AddValue("nivel", 0)
	_, err = connection.IQuery(authorizationRequirement.SqlGenerated(), authorizationRequirement.GetValues())
	if err != nil {
		return fmt.Errorf("[colombia.order.go]Error al insertar los datos en la tabla dbo.SWT_Requerimiento_Autorizacion: %v", err)
	}
	//Insercion a la tabla SWT_Respuesta_Autorizacion
	fecha := currentDate.Format("20060102")
	hora := currentDate.Format("150405")
	binCol := o.Order.PaymentMethods.Bin
	responseAuthorization := sqlserver.NewSimpleInsertBuilderSQL(true, "dbo.SWT_Respuesta_Autorizacion")
	responseAuthorization.AddValue("rsaut_trama", cfacId)
	responseAuthorization.AddValue("rsaut_fecha", currentDate)
	responseAuthorization.AddValue("ttra_codigo", o.Order.PaymentMethods.Card.IdentifierTypeResponse)
	responseAuthorization.AddValue("cres_codigo", nil)
	responseAuthorization.AddValue("rsaut_respuesta", o.Order.PaymentMethods.Card.CodeResponseDescription)
	responseAuthorization.AddValue("rsaut_secuencial_transaccion", nil)
	responseAuthorization.AddValue("rsaut_hora_autorizacion", hora)
	responseAuthorization.AddValue("rsaut_fecha_autorizacion", fecha)
	responseAuthorization.AddValue("rsaut_numero_autorizacion", o.Order.PaymentMethods.Card.AuthorizationCode)
	responseAuthorization.AddValue("rsaut_terminal_id", o.Order.PaymentMethods.Card.NoBox)
	responseAuthorization.AddValue("rsaut_grupo_tarjeta", getProcessorFranchise(o.Order.PaymentMethods.Card.Processor, o.Order.PaymentMethods.Card.Franchise))
	responseAuthorization.AddValue("rsaut_red_adquiriente", "RED DATAFAST")
	responseAuthorization.AddValue("rsaut_movimiento", cfacId)
	responseAuthorization.AddValue("IDStatus", status42)
	responseAuthorization.AddValue("replica", 0)
	responseAuthorization.AddValue("nivel", 0)
	responseAuthorization.AddValue("SWT_Respuesta_AutorizacionVarchar3", o.Order.PaymentMethods.Card.ReceiptNumber)
	responseAuthorization.AddValue("SWT_Respuesta_AutorizacionVarchar4", o.Order.PaymentMethods.Card.TypeAccount)
	responseAuthorization.AddValue("SWT_Respuesta_AutorizacionVarchar2", binCol)
	responseAuthorization.AddValue("SWT_Respuesta_AutorizacionVarchar1", "APROBADA")
	responseAuthorization.AddValue("raut_observacion", o.Order.PaymentMethods.Card.CodeResponseDescription)

	rowsResponseAuthorization, err := connection.IQueryRow(responseAuthorization.SqlGeneratedIdNumberDynamic("rsaut_id"), responseAuthorization.GetValues())
	if err != nil {
		return fmt.Errorf("[colombia.order.go]Error al insertar los datos en la tabla dbo.SWT_Respuesta_Autorizacion: %v", err)
	}
	if rowsResponseAuthorization.Err() != nil {
		return fmt.Errorf("[colombia.order.go]Error al iterar el rows de la tabla dbo.SWT_Respuesta_Autorizacion: %v", rowsResponseAuthorization.Err())
	}
	var rsAutId int32
	err = rowsResponseAuthorization.Scan(&rsAutId)
	if err != nil {
		return fmt.Errorf("[colombia.order.go]Error al obtener el identificador unico de la tabla dbo.SWT_Respuesta_Autorizacion: %v", err.Error())
	}
	//Insercion de forma de pago
	spPaymentMethod := "[facturacion].[fac_insertaFormaPago]"
	if strings.EqualFold(paymentType, "nequi") {
		spPaymentMethod = "[facturacion].[fac_insertaFormaPago_Kiosko_QR]"
	}
	insertPaymentMethod := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, spPaymentMethod)
	insertPaymentMethod.AddValueParameterized("cfac", cfacId)
	insertPaymentMethod.AddValueParameterized("fmp_id", idPaymentMethod)
	insertPaymentMethod.AddValueParameterized("num", binCol)
	insertPaymentMethod.AddValueParameterized("valor", o.Order.PaymentMethods.TotalBill)
	insertPaymentMethod.AddValueParameterized("total", cfacTotal)
	insertPaymentMethod.AddValueParameterized("prop", 0)
	insertPaymentMethod.AddValueParameterized("swt", statusMxp.IdPinpadUnired)
	insertPaymentMethod.AddValueParameterized("usr_id", idUserPos)
	_, err = connection.IQuery(insertPaymentMethod.GetStoreProcedure(), insertPaymentMethod.GetValues())
	if err != nil {
		return fmt.Errorf("[colombia.order.go]Error al ejecutar el sp %v: %v", spPaymentMethod, err)
	}
	//INSERTA CLIENTE - FIN FACTURA
	invoicingClient := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[USP_FacturaCliente_co]")
	invoicingClient.AddValueParameterized("idCliente", document)
	invoicingClient.AddValueParameterized("IDFactura", cfacId)
	invoicingClient.AddValueParameterized("IDUserpos", idUserPos)
	invoicingClient.AddValueParameterized("TipoDocumento", dataTypeDocument.Description)
	_, err = connection.IQuery(invoicingClient.GetStoreProcedure(), invoicingClient.GetValues())
	if err != nil {
		return fmt.Errorf("[colombia.order.go]Error al ejecutar el sp [facturacion].[USP_FacturaCliente_co]: %v", err)
	}
	//Medio de venta
	updateMedio := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[IAE_Medio_de_Venta]")
	updateMedio.AddValueParameterized("transaccion", cfacId)
	_, err = connection.IQuery(updateMedio.GetStoreProcedure(), updateMedio.GetValues())
	if err != nil {
		return fmt.Errorf("[colombia.order.go]Error al ejecutar el sp [facturacion].[IAE_Medio_de_Venta]: %v", err.Error())
	}

	//Genereacion del Json de factura para impresion
	var jsonInvoicing *string
	dataOrderPrintFastFood := make([]*models.KioskoImpresionOrdenPedidoFastFood, 0)

	dynamicInvoicePrinting := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[kioskoColombia_impresiondinamica_factura_co]")
	dynamicInvoicePrinting.AddValueParameterized("cfac_id", cfacId)
	dynamicInvoicePrinting.AddValueParameterized("tipo_comprobante", "F")
	rowInvoicePrinting, err := connection.IQuery(dynamicInvoicePrinting.GetStoreProcedure(), dynamicInvoicePrinting.GetValues())
	if err != nil {
		return fmt.Errorf("[colombia.order.go]Error al ejecutar el sp [facturacion].[kioskoColombia_impresiondinamica_factura_co]: %v", err)
	}
	defer rowInvoicePrinting.Close()
	for rowInvoicePrinting.Next() {
		err = rowInvoicePrinting.Scan(&jsonInvoicing)
		if err != nil {
			return fmt.Errorf("[colombia.order.go]Error al obtener el json de impresion de la factura del sp [dbo].[kioskoColombia_impresiondinamica_factura_co]: %v", err)
		}
	}
	if jsonInvoicing == nil {
		return fmt.Errorf("[colombia.order.go]Error, no se genero el json de impresion de factura")
	}

	//Impresión de orden de pedido en impresora de línea
	orderPrintFastFood := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[pedido].[IAE_ImpresionOrdenPedidoFastFoodKiosko]")
	orderPrintFastFood.AddValueParameterized("idCabeceraOrdenPedido", idCabeceraOrdenPedido)
	orderPrintFastFood.AddValueParameterized("idCadena", o.StoreData.ChainId)
	orderPrintFastFood.AddValueParameterized("idRestaurante", o.StoreData.RestaurantId)
	rowOrderPrint, err := connection.IQuery(orderPrintFastFood.GetStoreProcedure(), orderPrintFastFood.GetValues())
	if err != nil {
		return fmt.Errorf("[colombia.order.go]Error al ejecutar el sp [pedido].[IAE_ImpresionOrdenPedidoFastFoodKiosko]: %v", err)
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
			return fmt.Errorf("[colombia.order.go]Error al obtener los datos para la impresion de la orden de pedido: %v", err)
		}
		dataOrderPrintFastFood = append(dataOrderPrintFastFood, &dataOrderPrint)
	}

	//Impresión de orden de pedido en impresora de línea kds
	orderPrintFastFoodKds := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[pedido].[IAE_ImpresionOrdenPedidoFastFoodKiosko_kds]")
	orderPrintFastFoodKds.AddValueParameterized("idCabeceraOrdenPedido", idCabeceraOrdenPedido)
	orderPrintFastFoodKds.AddValueParameterized("idCadena", o.StoreData.ChainId)
	orderPrintFastFoodKds.AddValueParameterized("idRestaurante", o.StoreData.RestaurantId)
	rowOrderPrintKds, err := connection.IQuery(orderPrintFastFoodKds.GetStoreProcedure(), orderPrintFastFoodKds.GetValues())
	if err != nil {
		return fmt.Errorf("[colombia.order.go]Error al ejecutar el sp [pedido].[IAE_ImpresionOrdenPedidoFastFoodKiosko_kds]: %v", err)
	}
	defer rowOrderPrintKds.Close()
	for rowOrderPrintKds.Next() {
		var dataOrderPrint models.KioskoImpresionOrdenPedidoFastFood
		err = rowOrderPrintKds.Scan(
			&dataOrderPrint.NumeroImpresiones,
			&dataOrderPrint.Tipo,
			&dataOrderPrint.Impresora,
			&dataOrderPrint.FormatoXML,
			&dataOrderPrint.JsonData,
			&dataOrderPrint.JsonRegistros)
		if err != nil {
			return fmt.Errorf("[colombia.order.go]Error al obtener los datos para la impresion de la orden de pedido: %v", err)
		}
		dataOrderPrintFastFood = append(dataOrderPrintFastFood, &dataOrderPrint)
	}
	connection.Commit()

	//Proceso de Cufe
	cufe := o.getCufe(connection, *cfacId)
	if cufe != nil {
		urlCufe := o.getDianQr(connection, *cufe)
		// Asignar valores al objeto `order`
		order.Cufe = cufe
		order.DianQr = urlCufe
	}
	//se envia la respuesta a nats
	order.IdOrdenPedido = idCabeceraOrdenPedido
	order.OrderId = orderId
	order.CfacId = *cfacId
	order.Factura = *jsonInvoicing

	o.SendOrderResponse(order)
	o.SendOrderTurner(*cfacId)

	isTurneroEnabled := o.Feature.GetConfigFeatureFlag(featureflag2.TURNERO_DIGITAL)
	if isTurneroEnabled {
		go o.regionalKiosk.SendTurnerDigital(*cfacId)
	}

	//Valido si el featureFlag para encuesta esta activo
	isSurvey := o.Feature.GetConfigFeatureFlag(featureflag2.ENCUESTA)
	if isSurvey {
		o.regionalKiosk.GetSurvey(dataTypeDocument, *cfacId, *idClient)
	}

	// Enviar orden al KDS Regional
	kdsService := business.NewKDSRegionalService(connection, o.StoreData, o.Order, *resultStation.IdStation, *cfacId, *resultStation.CashierName)
	kdsService.SendOrderToKDSAsync()

	//procesos de impresion de tarjeta
	defer func() {
		errPrintService := o.WsPrintService(connection,
			*cfacId,
			idCabeceraOrdenPedido,
			*idUserPos,
			dataOrderPrintFastFood,
			resultStation)
		if errPrintService != nil {
			logger.Error.Println(errPrintService.Error())
		}
		errMasterDataClient := o.MasterDataClient(connection)
		if errMasterDataClient != nil {
			logger.Error.Println(errMasterDataClient.Error())
		}
	}()

	return nil
}

func (o *OrderStore) CreateOrderKioskPaid() (err error) {
	defer func() {
		if r := recover(); r != nil {
			stackTrace := string(debug.Stack())
			// Convertir el panic y el stack trace en un error detallado
			err = fmt.Errorf("se produjo un panic: %v\nStack trace:\n%s", r, stackTrace)
		}
	}()
	orderResp := &lib_gen_proto.ResultStoreValidationData{}
	dataOrderPrintFastFood := make([]*models.KioskoImpresionOrdenPedidoFastFood, 0)
	orderId := o.Order.Cabecera.OrderId
	connection, err := sqlserver.NewConnectionSql(o.DatabaseCredential)
	if err != nil {
		return fmt.Errorf("[colombia.order.go]conectar sqlserver: %w", err)
	}

	defer func() {
		connection.Close()
	}()

	resultStation, err := o.GetStationKiosko(connection)
	if err != nil {
		return fmt.Errorf("[colombia.order.go]GetStationKiosko: %w", err)
	}

	if err = o.CheckIfAlreadyExistOrder(connection, orderId); err != nil {
		return fmt.Errorf("[colombia.order.go]CheckIfAlreadyExistOrder: %w", err)
	}

	idUserPos, err := o.GetIdUserPos(connection, resultStation)
	if err != nil {
		return fmt.Errorf("[colombia.order.go]GetIdUserPos: %w", err)
	}
	if idUserPos == nil {
		return errors.New("[colombia.order.go]no se encontro idUserPos")
	}

	idControlStation, err := o.GetStationControl(connection, resultStation)
	if err != nil {
		return fmt.Errorf("[colombia.order.go]GetStationControl: %w", err)
	}
	if idControlStation == nil {
		return errors.New("[colombia.order.go]estación de kiosko no activa")
	}

	idPeriod, err := o.GetOpenPeriod(connection)
	if err != nil {
		return fmt.Errorf("[colombia.order.go]GetOpenPeriod: %w", err)
	}
	if idPeriod == nil {
		return errors.New("[colombia.order.go]no existe periodo abierto")
	}

	err = connection.CreateBegin()
	defer connection.Rollback()
	if err != nil {
		return fmt.Errorf("[colombia.order.go]Error al crear BEGIN: %v", err.Error())
	}

	idOrder, _, err := o.InsertOrderKiosko(connection, nil)
	if err != nil {
		return fmt.Errorf("InsertOrderKiosko: %w", err)
	}

	logger.Info.Printf("idPedido insertado: %v", idOrder)
	logger.Info.Printf("IpKiosk insertado: %v", o.Order.Cabecera.IpKiosk)
	logger.Info.Printf("user insertado: %v", resultStation.CashierName)
	SPPaid := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[dbo].[kioskoColombia_USP_IntegracionInformacionJson_PAID]")
	SPPaid.AddValueParameterized("idPedido", idOrder)
	SPPaid.AddValueParameterized("ip", o.Order.Cabecera.IpKiosk)
	SPPaid.AddValueParameterized("user", resultStation.CashierName)

	rows2, err := connection.IQuery(SPPaid.GetStoreProcedure(), SPPaid.GetValues())
	if err != nil {
		return fmt.Errorf("[colombia.order.go]error al ejecutar SP kioskoColombia_USP_IntegracionInformacionJson_PAID: %w", err)
	}
	defer rows2.Close()

	// En el SP estás retornando 3 columnas: respuesta, respuestaOrdenPedido, respuestaOrdenPedidokds
	var respuestaSQL, respuestaOrdenPedidoSQL, respuestaOrdenPedidokdsSQL sql.NullString
	if rows2.Next() {
		if scanErr := rows2.Scan(&respuestaSQL, &respuestaOrdenPedidoSQL, &respuestaOrdenPedidokdsSQL); scanErr != nil {
			return fmt.Errorf("[colombia.order.go] error en el scan respuesta SP PAID: %w", scanErr)
		}
	} else {
		return fmt.Errorf("SP PAID no devolvió filas")
	}

	if respuestaSQL.Valid {
		trim := strings.TrimSpace(respuestaSQL.String)
		if strings.EqualFold(trim, "NO ES PAID") || strings.EqualFold(trim, "NO ES PAID") {
			return fmt.Errorf("[colombia.order.go]transacción no es PAID: %s", trim)
		}
	}
	//var dataOrderPrintKds models.KioskoImpresionOrdenPedidoFastFood
	if respuestaOrdenPedidoSQL.Valid && len(respuestaOrdenPedidoSQL.String) > 0 {
		var dataOrderPrint models.KioskoImpresionOrdenPedidoFastFood
		err = json.Unmarshal([]byte(respuestaOrdenPedidoSQL.String), &dataOrderPrint)
		if err != nil {
			return fmt.Errorf("[colombia.order.go]unmarshal respuesta SP PAID: %w", err)
		}
		dataOrderPrintFastFood = append(dataOrderPrintFastFood, &dataOrderPrint)
	}

	if respuestaOrdenPedidokdsSQL.Valid && len(respuestaOrdenPedidokdsSQL.String) > 0 {
		var dataOrderPrint models.KioskoImpresionOrdenPedidoFastFood
		err = json.Unmarshal([]byte(respuestaOrdenPedidokdsSQL.String), &dataOrderPrint)
		if err != nil {
			return fmt.Errorf("[colombia.order.go]unmarshal respuesta SP PAID: %w", err)
		}
		dataOrderPrintFastFood = append(dataOrderPrintFastFood, &dataOrderPrint)
	}

	var spResp models.ResponseSPKioskPaid
	if respuestaSQL.Valid && len(respuestaSQL.String) > 0 {
		err = json.Unmarshal([]byte(respuestaSQL.String), &spResp)
		if err != nil {
			return fmt.Errorf("[colombia.order.go]unmarshal respuesta SP PAID: %w", err)
		}
	}
	if utils.IsEmpty(spResp.Data) && utils.IsEmpty(spResp.Data.CfacID) {
		return errors.New("[colombia.order.go]error, no se encontro el cfacId del pedido")
	}
	if utils.IsEmpty(spResp.Data) && utils.IsEmpty(spResp.Data.IdOrdenPedido) {
		return errors.New("[colombia.order.go]error, no se encontro el id de la cabecera orden pedido")
	}

	connection.Commit()

	cfacId := spResp.Data.CfacID
	idCabeceraOrdenPedido := spResp.Data.IdOrdenPedido

	//Proceso de Cufe
	cufe := o.getCufe(connection, cfacId)
	if cufe != nil {
		urlCufe := o.getDianQr(connection, *cufe)
		// Asignar valores al objeto `order`
		orderResp.Cufe = cufe
		orderResp.DianQr = urlCufe
	}
	orderResp.OrderId = orderId
	orderResp.CfacId = cfacId
	orderResp.IdOrdenPedido = idCabeceraOrdenPedido

	if spResp.Data.Factura == nil {
		return errors.New("[colombia.order.go]factura vacia SP PAID")
	}

	if facturaBytes, err := json.Marshal(spResp.Data.Factura); err == nil {
		orderResp.Factura = string(facturaBytes)
	} else {
		return fmt.Errorf("[colombia.order.go]marshal factura SP PAID: %w", err)
	}

	// Enviar orden al KDS Regional
	kdsService := business.NewKDSRegionalService(connection, o.StoreData, o.Order, *resultStation.IdStation, cfacId, *resultStation.CashierName)
	kdsService.SendOrderToKDSAsync()

	//procesos de impresion de tarjeta
	defer func() {
		errPrintService := o.WsPrintService(connection,
			cfacId,
			idCabeceraOrdenPedido,
			*idUserPos,
			dataOrderPrintFastFood,
			resultStation)
		if errPrintService != nil {
			logger.Error.Println(errPrintService.Error())
		}
	}()
	o.SendOrderResponse(orderResp)

	return nil
}

func (o *OrderStore) InsertOrderKiosko(connection *sqlserver.DatabaseSql, cfacId *string) (int32, []*models.KioskoPlus, error) {
	var idOrder int32
	responseDetailsOrder := make([]*models.KioskoPlus, 0)
	isLocator := o.Feature.GetConfigFeatureFlag(featureflag2.LOCALIZADOR)
	isUpsell := o.Feature.GetConfigFeatureFlag(featureflag2.UPSELLING)
	isTip := o.Feature.GetConfigFeatureFlag(featureflag2.PROPINA)
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
	//Datos adicionales para kiosko
	if validatorsql.ColumnExitsDb(connection, "kiosko_cabecera_pedidos", "info_adicional") && !utils.IsEmpty(o.Order.AdditionalInfo) {
		kioskoHeader.AddValue("info_adicional", o.Order.AdditionalInfo)
	}
	if isTip && !utils.IsEmpty(o.Order.PaymentMethods.Tips) {
		newTip, _ := utils.StrToFloat32(o.Order.PaymentMethods.Tips)
		if newTip > 0 {
			kioskoHeader.AddValue("monto_total_propina", o.Order.PaymentMethods.Tips)
		}
	}
	rows, err := connection.IQueryRow(kioskoHeader.SqlGenerated(), kioskoHeader.GetValues())
	if err != nil {
		return -1, nil, fmt.Errorf("[colombia.order.go]Error al insertar los datos en la tabla dbo.kiosko_cabecera_pedidos: %v", err)
	}
	if rows.Err() != nil {
		return -1, nil, fmt.Errorf("[colombia.order.go]Error al iterar el rows: %v", rows.Err())
	}
	err = rows.Scan(&idOrder)
	if err != nil {
		return -1, nil, fmt.Errorf("[colombia.order.go]Error al obtener el id de la tabla dbo.kiosko_cabecera_pedidos: %v", err.Error())
	}

	//Insercion de detalles de kiosko
	for _, details := range o.Order.Items.Product {
		detailsKiosko := &models.KioskoPlus{}
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
			return -1, nil, fmt.Errorf("[colombia.order.go]Error al insertar los productos en la tabla dbo.kiosko_detalle_pedidos: %v", err)
		}
		if rowsDetails.Err() != nil {
			return -1, nil, fmt.Errorf("[colombia.order.go]Error al iterar los datos de la tabla dbo.kiosko_detalle_pedidos: %v", rowsDetails.Err())
		}
		var detailsOrderId int32
		err = rowsDetails.Scan(&detailsOrderId)
		if err != nil {
			return -1, nil, fmt.Errorf("[colombia.order.go]Error al obtener el identificador de la tabla dbo.kiosko_detalle_pedidos: %v", err.Error())
		}
		detailsKiosko.IdOrderDetail = detailsOrderId
		detailsKiosko.PluId = details.ProductId
		detailsKiosko.Quantity = details.Quantity
		responseDetailsOrder = append(responseDetailsOrder, detailsKiosko)
		for _, modifiers := range details.ModifierGroups {
			modifiersKiosko := &models.KioskoPlus{}
			kioskoDetails.Clear()
			kioskoDetails.AddValue("id_orden", idOrder)
			kioskoDetails.AddValue("plu_id", modifiers.ProductId)
			kioskoDetails.AddValue("dop_cantidad", modifiers.Quantity)
			kioskoDetails.AddValue("created_at", currentDate)
			kioskoDetails.AddValue("updated_at", currentDate)
			kioskoDetails.AddValue("modifica", detailsOrderId)
			rowsModifiers, err := connection.IQueryRow(kioskoDetails.SqlGenerated(), kioskoDetails.GetValues())
			if err != nil {
				return -1, nil, fmt.Errorf("[colombia.order.go]Error al insertar los modificadores en la tabla dbo.kiosko_detalle_pedidos: %v", err)
			}
			var modifiersOrderId int32
			err = rowsModifiers.Scan(&modifiersOrderId)
			if err != nil {
				return -1, nil, fmt.Errorf("[colombia.order.go]Error al obtener el id de la tabla dbo.kiosko_detalle_pedidos: %v", err.Error())
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
		paymentMethodTable.AddValue("json_autorizaciones", utils.ToString(o.Order.PaymentMethods.Card.JsonAuthorization, ""))
		paymentMethodTable.AddValue("created_at", currentDate)
		paymentMethodTable.AddValue("updated_at", currentDate)
		_, err = connection.IQuery(paymentMethodTable.SqlGenerated(), paymentMethodTable.GetValues())
		if err != nil {
			return -1, nil, fmt.Errorf("[colombia.order.go]error al insertar los datos en la tabla dbo.kiosko_autorizaciones_switch: %v", err)
		}
	}
	//Insercion de forma Pago kiosko
	formaPagoTable := sqlserver.NewSimpleInsertBuilderSQL(false, "dbo.kiosko_forma_pagos")
	formaPagoTable.AddValue("idOrden", idOrder)
	formaPagoTable.AddValue("bin", o.Order.PaymentMethods.Bin)
	formaPagoTable.AddValue("fpf_total_pagar", o.Order.PaymentMethods.TotalBill)
	formaPagoTable.AddValue("created_at", currentDate)
	formaPagoTable.AddValue("updated_at", currentDate)

	if validatorsql.ColumnExitsDb(connection, "kiosko_forma_pagos", "status") && !utils.IsEmpty(o.Order.PaymentMethods.Card.TransactionStatus) {
		formaPagoTable.AddValue("status", o.Order.PaymentMethods.Card.TransactionStatus)
	}

	_, err = connection.IQuery(formaPagoTable.SqlGenerated(), formaPagoTable.GetValues())
	if err != nil {
		return -1, nil, fmt.Errorf("[colombia.order.go]Error al insertar los datos en la tabla dbo.kiosko_forma_pagos: %v", err)
	}
	return idOrder, responseDetailsOrder, nil
}
func (o *OrderStore) GetOpenPeriod(connection *sqlserver.DatabaseSql) (*string, error) {
	query := fmt.Sprintf(`SELECT CAST(IDPeriodo AS VARCHAR(40))
			FROM dbo.Periodo
			WHERE IDStatus = @idStatus
				AND prd_fechacierre IS NULL`)
	rows, err := connection.Query(query, sql.Named("idStatus", o.StoreData.Status.PeriodoAperturaAbierto))
	if err != nil {
		return nil, fmt.Errorf("[colombia.order.go]Error al ejecutar el query %s: %s", query, err.Error())
	}
	defer rows.Close()
	for rows.Next() {
		var result string
		err = rows.Scan(&result)
		if err != nil {
			return nil, fmt.Errorf("[colombia.order.go]Error al obtener los datos del query %v: %s", query, err)
		}
		return &result, nil
	}
	return nil, nil
}
func (o *OrderStore) GetStationKiosko(connection *sqlserver.DatabaseSql) (*models.StationKiosco, error) {
	ipKiosko := o.Order.Cabecera.IpKiosk
	//valida si en cache existe el idEstacion y la Ip del kiosko caso contrario consulta y almaneca en cache
	dataStation, exits := cacheStation.Get(ipKiosko)
	if !exits {
		dataStation = &models.StationKiosco{}
		query := fmt.Sprintf(`SELECT	
		CAST(estacion.IDEstacion AS VARCHAR(40)),
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
			return nil, fmt.Errorf("[colombia.order.go]Error al ejecutar el query %v: %v", query, err)
		}
		defer rows.Close()
		for rows.Next() {
			err = rows.Scan(&dataStation.IdStation, &dataStation.CashierName)
			if err != nil {
				return nil, fmt.Errorf("[colombia.order.go]Error al obtener los datos de estacion del kiosko: %v", err)
			}
		}
		if dataStation.IdStation == nil && dataStation.CashierName == nil {
			return nil, fmt.Errorf("[colombia.order.go]Error, no se encontro el id y el nombre del cajero de kiosko, por favor validar")
		}
		cacheStation.Set(ipKiosko, dataStation, 1*time.Hour)
	}
	return dataStation, nil
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
		return nil, fmt.Errorf("[colombia.order.go]Error al ejecutar el query %v: %v", query, err)
	}
	defer rows.Close()
	for rows.Next() {
		var idControlStation *string
		err = rows.Scan(&idControlStation)
		if err != nil {
			return nil, fmt.Errorf("[colombia.order.go]Error al obtener los datos del control estacion de kiosko: %s", err)
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
		query := fmt.Sprintf(`SELECT cast(ecd.idIntegracion AS VARCHAR(40))
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
			return "", fmt.Errorf("[colombia.order.go]Error al obtener el idMesa de kiosko %v: %s", query, errScan.Error())
		}
		cacheIdMesa.Set(dataIdStation, idMesa, 1*time.Hour)
		return idMesa, nil
	}

	return dataIdMesa, nil
}
func (o *OrderStore) GetIdUserPos(connection *sqlserver.DatabaseSql, dataStation *models.StationKiosco) (*string, error) {
	query := fmt.Sprintf(`SELECT 
    				CAST(IDUsersPos AS VARCHAR(40)) 
					FROM dbo.Users_Pos 
					WHERE  IDStatus IS NOT NULL 
					  AND Users_Pos.usr_usuario = @CashierName`)
	result, err := connection.Query(query, sql.Named("CashierName", dataStation.CashierName))
	if err != nil {
		return nil, fmt.Errorf("[colombia.order.go]Error al ejecutar el query %v: %v", query, err)
	}
	defer result.Close()
	for result.Next() {
		var getIdUserPos string
		err = result.Scan(&getIdUserPos)
		if err != nil {
			return nil, fmt.Errorf("[colombia.order.go]Error al obtener el id del Usuario %v: %v", dataStation.CashierName, err.Error())
		}
		return &getIdUserPos, nil
	}
	return nil, nil
}
func (o *OrderStore) CheckIfAlreadyExistOrder(connection *sqlserver.DatabaseSql, codigoApp string) error {
	query := fmt.Sprintf(`SELECT count(*) as cantidad FROM dbo.kiosko_cabecera_pedidos WHERE codigo_app = @codigoApp`)
	result, err := connection.Query(query, sql.Named("codigoApp", codigoApp))
	if err != nil {
		return fmt.Errorf("[colombia.order.go]Error al ejecutar el query %v: %v", query, err.Error())
	}
	defer result.Close()
	for result.Next() {
		var countData int32
		err = result.Scan(&countData)
		if err != nil {
			return fmt.Errorf("[colombia.order.go]Error al validar el codigoApp %v de la tabla kiosko_cabecera_pedidos: %v", codigoApp, err.Error())
		}
		if countData > 0 {
			return fmt.Errorf("[colombia.order.go]El codigo %v ya se encuentra registrado, por favor ingresar uno nuevo", codigoApp)
		}
	}
	return nil
}
func (o *OrderStore) InsertDetailOrder(connection *sqlserver.DatabaseSql, data []*models.KioskoPlus, idCabeceraOrdenPedido string) error {
	isUpsell := o.Feature.GetConfigFeatureFlag(featureflag2.UPSELLING)
	dataDetailsKiosko := make([]*models.KioskoPlus, 0)
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
			return fmt.Errorf("[colombia.order.go]Error al ejecutar el query %v: %v", query, err)
		}
		for rows.Next() {
			var dataProduct models.KioskoPlus
			var pluId *int32
			err = rows.Scan(&pluId, &dataProduct.ValorNeto, &dataProduct.ValorIva, &dataProduct.ValorBruto)
			if err != nil {
				rows.Close()
				return fmt.Errorf("[colombia.order.go]Error al obtener los pluId de los productos: %v", err)
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
			return fmt.Errorf("[colombia.order.go]Error al insertar los datos en la tabla dbo.Detalle_Orden_Pedido %v: %v", detailsOrder.GetValues(), err)
		}
		if rows.Err() != nil {
			return fmt.Errorf("[colombia.order.go]Error al iterar el rows: %v", rows.Err())
		}
		var idDetalleOrdenPedido string
		err = rows.Scan(&idDetalleOrdenPedido)
		if err != nil {
			return fmt.Errorf("[colombia.order.go]Error al obtener el idDetalleOrdenpedido al momento de insertar los datos en la tabla dbo.Detalle_Orden_Pedido: %v", err)
		}
		//actualizo el objeto con el idDetalleOrdenPedido
		dataDetailsKiosko[i].IdDetalleOrdenPedido = idDetalleOrdenPedido
		dataDetailsKiosko[i].IsDetails = isProduct

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
		if detailsInsert.IsDetails > 0 {

			exec, err := connection.Exec(updateDetails2,
				sql.Named("addSecond", i+1),
				sql.Named("idKioskoDetallePedido", idOrderToString),
				sql.Named("idCabeceraOrdenPedido", detailsInsert.IdCabeceraOrdenPedido))
			if err != nil {
				return fmt.Errorf("[colombia.order.go]Error al actualizar los datos de la tabla Detalle_Orden_Pedido: %v", err.Error())
			}
			rowsAfected, err := exec.RowsAffected()
			if err != nil {
				return fmt.Errorf("[colombia.order.go]Error al identificar la actualizacion de los datos de la tabla Detalle_Orden_Pedido: %v", err.Error())
			}
			if rowsAfected != 1 {
				return fmt.Errorf("[colombia.order.go]No se actualizaron los registros de la tabla detalle_orden_pedido")
			}
		} else {
			exec, err := connection.Exec(updateDetails,
				sql.Named("isModifica", detailsInsert.Exchange),
				sql.Named("idDetalleOrdenPedido", detailsInsert.IdDetalleOrdenPedido),
				sql.Named("addSecond", i+1),
				sql.Named("idKioskoDetallePedido", idOrderToString),
				sql.Named("idCabeceraOrdenPedido", detailsInsert.IdCabeceraOrdenPedido))
			if err != nil {
				return fmt.Errorf("[colombia.order.go]Error al actualizar los datos en la tabla Detalle_Orden_Pedido: %v", err.Error())
			}
			rowsAfected, err := exec.RowsAffected()
			if err != nil {
				return fmt.Errorf("[colombia.order.go]Error al identificar la actualizacion de los datos de la tabla Detalle_Orden_Pedido: %v", err.Error())
			}
			if rowsAfected != 1 {
				return fmt.Errorf("[colombia.order.go]No se actualizaron los registros de la tabla detalle_orden_pedido")
			}
		}

	}

	return nil

}
func (o *OrderStore) GetIdClient(connection *sqlserver.DatabaseSql) (*string, *string, error) {
	dataTypeDocument := o.StoreData.GetTipoDocumento(o.Order.Cabecera.Client.DocumentId)
	identification := o.Order.Cabecera.Client.DocumentNumber
	if strings.EqualFold(dataTypeDocument.Description, "NIT") {
		partIdentification := strings.Split(identification, "-")
		if len(partIdentification) != 0 {
			identification = partIdentification[0]
		}
		query := fmt.Sprintf(`SELECT [dbo].[ObtenerDigitoVerificador] ('%v')`, identification)
		rows, err := connection.Query(query)
		if err != nil {
			return nil, nil, fmt.Errorf("[colombia.order.go]Error al ejecutar el query %v: %v", query, err.Error())
		}
		defer rows.Close()
		var dv *string
		for rows.Next() {
			err = rows.Scan(&dv)
			if err != nil {
				return nil, nil, fmt.Errorf("[colombia.order.go]Error al obtener el digito verificador del NIT: %v", err.Error())
			}
		}
		identification = identification + "-" + *dv
	}
	query := fmt.Sprintf(`SELECT CAST(IDCliente AS NVARCHAR(40)),cli_documento FROM dbo.Cliente WHERE cli_documento = @identificacion`)
	dataQuery := sql.Named("identificacion", identification)
	if strings.EqualFold("CONSUMIDOR FINAL", o.Order.Cabecera.Client.Name) {
		query = fmt.Sprintf(`SELECT CAST(IDCliente AS NVARCHAR(40)),cli_documento FROM dbo.Cliente WHERE IDTipoDocumento = @idTipoDocumento`)
		dataQuery = sql.Named("idTipoDocumento", o.Order.Cabecera.Client.DocumentId)
	}
	result, err := connection.Query(query, dataQuery)
	if err != nil {
		return nil, nil, fmt.Errorf("[colombia.order.go]Error al ejecutar el query %v: %v", query, err.Error())
	}
	defer result.Close()
	for result.Next() {
		var idClient, document *string
		err = result.Scan(&idClient, &document)
		if err != nil {
			return nil, nil, fmt.Errorf("[colombia.order.go]Error al validar los datos de Cliente: %v", err.Error())
		}
		return idClient, document, nil
	}
	return nil, &identification, nil
}
func (o *OrderStore) getDianQr(connection *sqlserver.DatabaseSql, cufe string) *string {
	var urlCufe *string
	queryCufe := fmt.Sprintf(`SELECT [config].[fn_ColeccionCadena_VariableV_CufeDian] (%v, 'DIAN') AS URLDIAN`, o.StoreData.ChainId)

	rowsUrlCufe, err := connection.Query(queryCufe)
	if err != nil {
		logger.Error.Printf("[colombia.order.go]Error al ejecutar el query %v: %v", queryCufe, err)
		return nil
	}
	defer rowsUrlCufe.Close()
	for rowsUrlCufe.Next() {
		err = rowsUrlCufe.Scan(&urlCufe)
		if err != nil {
			logger.Error.Printf("[colombia.order.go]Error al obtener los datos del query %v, %v", queryCufe, err)
			return nil
		}
	}
	if urlCufe == nil {
		logger.Error.Println("[colombia.order.go]Error, no se obtuvo la url de cufe")
		return nil
	}
	newUrl := *urlCufe + cufe
	return &newUrl
}
func (o *OrderStore) getCufe(connection *sqlserver.DatabaseSql, cfacId string) *string {
	var cufe *string
	queryCufe := fmt.Sprintf(`SELECT [config].[fn_cadenaCufe] ('%v')`, cfacId)

	rowsCufe, err := connection.Query(queryCufe)
	if err != nil {
		logger.Error.Printf("[colombia.order.go]Error al ejecutar el query %v: %v", queryCufe, err)
		return nil
	}
	defer rowsCufe.Close()
	for rowsCufe.Next() {
		err = rowsCufe.Scan(&cufe)
		if err != nil {
			logger.Error.Printf("[colombia.order.go]Error al obtener los datos del query %v, %v", queryCufe, err)
			return nil
		}
	}
	if cufe == nil {
		logger.Error.Printf("[colombia.order.go]Error, no se obtuvo el cufe para el cfacId %v", cfacId)
		return nil
	}
	// Hash en SHA-384 y convertir a hexadecimal
	hash := sha512.New384()
	hash.Write([]byte(*cufe))
	hashed := hash.Sum(nil)
	cufeHash := hex.EncodeToString(hashed)
	return &cufeHash
}
func (o *OrderStore) WsPrintService(
	connection *sqlserver.DatabaseSql,
	cfacId,
	IdOrderHeaderOrder,
	idUserPos string,
	dataOrderPrintFastFood []*models.KioskoImpresionOrdenPedidoFastFood,
	dataStation *models.StationKiosco,
) error {

	//Llamo al construcutor del apiService
	apiService := printservice.NewApiPrintService(connection)
	var resultPrint *string
	rstId := o.StoreData.RestaurantId
	query := fmt.Sprintf(`SELECT servicio_impresion FROM [config].[fn_ColeccionRestaurante_ServicioImpresion] (@idRestaurante, @idCadena, @idEstacion)`)
	rows, err := connection.Query(query, sql.Named("idRestaurante", rstId),
		sql.Named("idCadena", o.StoreData.ChainId),
		sql.Named("idEstacion", dataStation.IdStation))
	if err != nil {
		return fmt.Errorf("[colombia.order.go]Error al ejecutar la funcion %v: %v", query, err.Error())
	}
	defer rows.Close()
	for rows.Next() {
		err = rows.Scan(&resultPrint)
		if err != nil {
			return fmt.Errorf("[colombia.order.go]Error al obtener los datos de la funcion %v: %v", query, err.Error())
		}
	}
	if resultPrint == nil {
		return fmt.Errorf("[colombia.order.go]error al obtner los datos de la impresora, por favor revisar")
	}
	//
	var printingServiceResponse models.PrintingServiceResponse
	err = json.Unmarshal([]byte(*resultPrint), &printingServiceResponse)
	if err != nil {
		return fmt.Errorf("[colombia.order.go]Error al parsear los datos de %v: %v", resultPrint, err.Error())
	}

	if strings.EqualFold("efectivo", o.Order.Cabecera.PaymentType) && !strings.EqualFold(o.Order.PaymentMethods.Card.TransactionStatus, "paid") {
		//Validacion de impresion anticipada
		queryPolicies := fmt.Sprintf(`SELECT rcd.variableB
						from ColeccionRestaurante cr WITH(NOLOCK)
						INNER JOIN ColeccionDeDatosRestaurante cdr WITH(NOLOCK) ON cr.ID_ColeccionRestaurante = cdr.ID_ColeccionRestaurante
						INNER JOIN RestauranteColeccionDeDatos rcd WITH(NOLOCK) ON cdr.ID_ColeccionDeDatosRestaurante = rcd.ID_ColeccionDeDatosRestaurante
						where 
						cdr.Descripcion= 'IMPRESION ANTICIPADA' AND
						cr.Descripcion = 'CONFIGURACION IMPRESION ANTICIPADA' AND
						cdr.isActive= 1 AND
						CR.isActive = 1 AND
						rcd.isActive = 1 AND
						rcd.rst_id = %v AND
						cr.cdn_id= %v`, o.StoreData.RestaurantId, o.StoreData.ChainId)

		rowsPolicies, err := connection.Query(queryPolicies)
		if err != nil {
			return fmt.Errorf("[colombia.order.go]Error al ejecutar el query %v: %w", queryPolicies, err)
		}
		defer rowsPolicies.Close()
		var variableB *bool
		for rowsPolicies.Next() {
			err = rowsPolicies.Scan(&variableB)
			if err != nil {
				return fmt.Errorf("[colombia.order.go]Error al obtener los datos del query %v: %w", queryPolicies, err)
			}
		}
		if variableB == nil {
			return errors.New("[colombia.order.go]No existe politicas relacionadas a impresion anticipada")
		}
		if !*variableB {
			return errors.New("[colombia.order.go]No existe valores de configuracion de impresion anticipada")
		}

		//Actualizacion de la orden en la tabla detalle_orden_pedido
		update := "UPDATE dbo.Detalle_Orden_Pedido SET dop_impresion = 0 WHERE IDCabeceraOrdenPedido = @idCabeceraOrdenPedido and dop_cuenta = 1 and dop_anulacion = 1 and dop_impresion = -1"
		_, err = connection.Exec(update, sql.Named("idCabeceraOrdenPedido", IdOrderHeaderOrder))
		if err != nil {
			return fmt.Errorf("[colombia.order.go]No se pudo ejecutar el UPDATE %s con idCabeceraOrdenPedido:%s", update, IdOrderHeaderOrder)
		}
		update = "UPDATE dbo.Detalle_Orden_Pedido SET dop_impresion = -1 WHERE IDCabeceraOrdenPedido = @idCabeceraOrdenPedido and dop_cuenta = 1 and dop_anulacion = 1 and dop_impresion = 1"
		_, err = connection.Exec(update, sql.Named("idCabeceraOrdenPedido", IdOrderHeaderOrder))
		if err != nil {
			return fmt.Errorf("[colombia.order.go]No se pudo ejecutar el UPDATE %s con idCabeceraOrdenPedido:%s", update, IdOrderHeaderOrder)
		}
		update = "UPDATE dbo.Detalle_Orden_Pedido SET dop_estado = -1 WHERE IDCabeceraOrdenPedido = @idCabeceraOrdenPedido and dop_cuenta = 1 and dop_anulacion <> 0 and dop_impresion = 1"
		_, err = connection.Exec(update, sql.Named("idCabeceraOrdenPedido", IdOrderHeaderOrder))
		if err != nil {
			return fmt.Errorf("[colombia.order.go]No se pudo ejecutar el UPDATE %s con idCabeceraOrdenPedido:%s", update, IdOrderHeaderOrder)
		}

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
				return fmt.Errorf("[colombia.order.go]Error al ejecutar el sp [pedido].[ORD_impresion_ordenpedido]: %v", err.Error())
			}
			return nil
		} else {
			dataOrderPrintFastFood = make([]*models.KioskoImpresionOrdenPedidoFastFood, 0)
			orderPrintFastFood := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[pedido].[ORD_impresion_ordenpedido]")
			orderPrintFastFood.AddValueParameterized("IDCabeceraOrdenPedido", IdOrderHeaderOrder)
			orderPrintFastFood.AddValueParameterized("IDUsersPos", idUserPos)
			orderPrintFastFood.AddValueParameterized("rst_id", o.StoreData.RestaurantId)
			orderPrintFastFood.AddValueParameterized("dop_cuenta", 1)
			orderPrintFastFood.AddValueParameterized("guardaOrden", 1)
			orderPrintFastFood.AddValueParameterized("todas", 0)
			rowOrderPrint, err := connection.IQuery(orderPrintFastFood.GetStoreProcedure(), orderPrintFastFood.GetValues())
			if err != nil {
				return fmt.Errorf("[colombia.order.go]Error al ejecutar el sp [pedido].[ORD_impresion_ordenpedido]: %v", err.Error())
			}
			defer rowOrderPrint.Close()
			column, _ := rowOrderPrint.Columns()
			for rowOrderPrint.Next() {
				dataOrderPrint := &models.KioskoImpresionOrdenPedidoFastFood{}
				if len(column) == 1 {
					err = rowOrderPrint.Scan(&dataOrderPrint.Confirmar)
				} else {
					err = rowOrderPrint.Scan(
						&dataOrderPrint.NumeroImpresiones,
						&dataOrderPrint.Tipo,
						&dataOrderPrint.IdMarca,
						&dataOrderPrint.Impresora,
						&dataOrderPrint.FormatoXML,
						&dataOrderPrint.JsonData,
						&dataOrderPrint.JsonRegistros)
				}

				if err != nil {
					return fmt.Errorf("[colombia.order.go]Error al obtener los datos para la impresion de la ordern de pedido :%v", err.Error())
				}
				dataOrderPrintFastFood = append(dataOrderPrintFastFood, dataOrderPrint)
			}
		}
	}
	//procesamiento por tarjeta
	err = apiService.ApiPrint(
		printingServiceResponse,
		dataOrderPrintFastFood,
		cfacId,
		IdOrderHeaderOrder,
		idUserPos,
		*dataStation.IdStation)
	if err != nil {
		return fmt.Errorf("[colombia.order.go]Error al imprimir la orden del pedido: %v", err)
	}
	return nil
}

func (o *OrderStore) MasterDataClient(connection *sqlserver.DatabaseSql) error {
	clientOptin := masterdataclient.NewMasterDataClient(connection, o.StoreData)
	err := clientOptin.ApiMasterDataCliente(o.Order.Cabecera)
	if err != nil {
		return err
	}
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
		logger.Error.Println(err)
	}
}
func (o *OrderStore) SendOrderResponse(order *lib_gen_proto.ResultStoreValidationData) {
	msg := events.NewEvent("response.order", order)
	err := o.NatsClient.EventSender.Execute(msg)
	if err != nil {
		logger.Error.Println(err)
	}
}

func (o *OrderStore) Execute() error {
	//TODO implement me
	if strings.EqualFold(o.Order.Cabecera.PaymentType, "efectivo") {

		if strings.EqualFold(o.Order.PaymentMethods.Card.TransactionStatus, "paid") {
			return o.CreateOrderKioskPaid()
		}

		return o.CreateOrderKioskEfectivo()
	}
	return o.CreateOrderKioskTarjeta()
}

func getProcessorFranchise(processor, franchise string) string {
	switch strings.ToUpper(processor) {
	case "DAVIPLATA":
		return processor
	}
	return franchise
}
