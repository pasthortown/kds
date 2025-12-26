package chile

import (
	"bytes"
	"database/sql"
	"encoding/json"
	"errors"
	"fmt"
	"lib-shared/protos/lib_gen_proto"
	"lib-shared/protos/lib_gen_proto_folio"
	"lib-shared/utils"
	"lib-shared/utils/logger"
	"new-order-store/internals/domain/business/chile/printservice"
	"new-order-store/internals/domain/execute"
	featureflag2 "new-order-store/internals/entity/enums/featureflag"
	"new-order-store/internals/entity/maxpoint"
	"new-order-store/internals/entity/maxpoint/credential"
	"new-order-store/internals/entity/models"
	"new-order-store/internals/infrastructure/cache"
	"new-order-store/internals/infrastructure/featureflag"
	"new-order-store/internals/infrastructure/natsmodule"
	"new-order-store/internals/infrastructure/natsmodule/events"
	"new-order-store/internals/infrastructure/natsmodulefolder"
	events2 "new-order-store/internals/infrastructure/natsmodulefolder/events"
	"new-order-store/internals/infrastructure/services"
	"new-order-store/internals/infrastructure/sqlserver"
	"new-order-store/internals/utils/validatorsql"
	"runtime/debug"
	"strings"
	"time"
)

var cacheStation = cache.NewTTL[string, *models.StationKiosco]()
var cacheIdMesa = cache.NewTTL[string, string]()

type responseFe struct {
	Status           string        `json:"status"`
	Mensaje          *string       `json:"mensaje"`
	ObjetoRespuesta  string        `json:"objetoRespuesta"`
	ObjetoRespuesta2 string        `json:"objetoRespuesta2"`
	Facturacion      feFacturacion `json:"facturacion"`
	Messages         int32         `json:"messages"`
}

type feFacturacion struct {
	Folio     string `json:"folio"`
	IdFactura string `json:"idFactura"`
	Fecha     string `json:"fecha"`
	Monto     int32  `json:"monto"`
	Id        int32  `json:"id"`
}

type OrderStore struct {
	DatabaseCredential *credential.DatabaseCredential
	Order              *lib_gen_proto.Order
	StoreData          *maxpoint.StoreData
	Feature            *featureflag.FeatureFlag
	NatsClient         *natsmodule.NatsStarter
	NatsFolder         *natsmodulefolder.NatsStarter
	ServicesChannel    *services.ChannelManager
	regionalKiosk      execute.RegionalKioskExecute
}

func (o *OrderStore) Execute() error {
	if strings.EqualFold(o.Order.Cabecera.PaymentType, "efectivo") {
		return o.CreateOrderKioskEfectivo()
	}
	return o.CreateOrderKioskTarjeta()
}

func NewOrderStore(
	DatabaseCredential *credential.DatabaseCredential,
	Order *lib_gen_proto.Order,
	StoreData *maxpoint.StoreData,
	Feature *featureflag.FeatureFlag,
	NatsClient *natsmodule.NatsStarter,
	NatsFolder *natsmodulefolder.NatsStarter,
	ServicesChannel *services.ChannelManager,
	regionalKiosk execute.RegionalKioskExecute,
) execute.OrderExecutorSql {
	return &OrderStore{
		DatabaseCredential: DatabaseCredential,
		Order:              Order,
		StoreData:          StoreData,
		Feature:            Feature,
		NatsClient:         NatsClient,
		NatsFolder:         NatsFolder,
		ServicesChannel:    ServicesChannel,
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
	connection, err := sqlserver.NewConnectionSql(o.DatabaseCredential)
	if err != nil {
		return err
	}
	defer func() {
		connection.Close()
	}()
	//variables
	isEarlyPrint := o.Feature.GetConfigFeatureFlag(featureflag2.IMPRESION_ANTICIPADA)
	orderId := o.Order.Cabecera.OrderId
	statusMxp := o.StoreData.Status
	var cfacId *string
	var idCabeceraOrdenPedido string
	documentId := o.Order.Cabecera.Client.DocumentId

	//Inicio de proceso
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
		return fmt.Errorf("[chile.order.go]Error al ejecutar el sp facturacion.KIOSKO_generarSecuencialFactura: %v", err)
	}
	defer rows.Close()
	for rows.Next() {
		err = rows.Scan(&cfacId)
		if err != nil {
			return fmt.Errorf("[chile.order.go]Error al obtener los datos de KIOSKO_generarSecuencialFactura: %v", err)
		}
	}

	if cfacId == nil {
		return fmt.Errorf("[chile.order.go]Error - El cfacId generado del sp [facturacion].[KIOSKO_generarSecuencialFactura] esta vacio, por favor revisar")
	}

	idTypeDocument := o.StoreData.GetTipoDocumento(documentId)
	if idTypeDocument == nil {
		return fmt.Errorf("[chile.order.go]No se encontro el tipo de documento para el id %s", documentId)
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
		return fmt.Errorf("[chile.order.go]No se encontro el id del usuario para kiosko")
	}

	idControlStation, err := o.GetStationControl(connection, resultStation)
	if err != nil {
		return err
	}
	if idControlStation == nil {
		return fmt.Errorf("[chile.order.go]La estacion de kiosko no se encuentra activa")
	}

	idPeriod, err := o.GetOpenPeriod(connection)
	if err != nil {
		return err
	}
	if idPeriod == nil {
		return fmt.Errorf("[chile.order.go]No se encontro ningun periodo abierto")
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
			return fmt.Errorf("[chile.order.go]Error al crear BEGIN: %v", err.Error())
		}
		_, datailsKiosk, err := o.InsertOrderKiosko(connection, cfacId)
		if err != nil {
			return err
		}
		if isEarlyPrint {
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
				return fmt.Errorf("[chile.order.go]Error al insertar el update BEGIN: %v", err.Error())
			}
			_, err = exec.RowsAffected()
			if err != nil {
				return fmt.Errorf("[chile.order.go]Error al actualizar la tabla Cabecera_Orden_Pedido %s", err.Error())
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
				return fmt.Errorf("[chile.order.go]Error al insertar los datos en la tabla Cabecera_Orden_Pedido %v : %v", insertCabeceraOrdenPedido, err.Error())
			}

			err = insertData.Scan(&idCabeceraOrdenPedido)
			if err != nil {
				return fmt.Errorf("[chile.order.go]Error al obtener el id de la Cabecera_Orden_Pedido: %v", err.Error())
			}

			//insercion de los productos a la tabla detalle_orden_pedido
			err = o.InsertDetailOrder(connection, datailsKiosk, idCabeceraOrdenPedido)
			if err != nil {
				return err
			}

			//Insercion a la factura para efectivo
			if o.Order.Discount != nil {
				return errors.New("[chile.order.go]Error, aun no esta disponible descuento para efectivo, por favor revisar")
			} else {
				invoicing := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "facturacion.IAE_Fac_InsertFactura_kioskoEfectivo")
				invoicing.AddValueParameterized("cfac_id", cfacId)
				invoicing.AddValueParameterized("idRestaurante", o.StoreData.RestaurantId)
				invoicing.AddValueParameterized("idCabeceraOrdenPedido", idCabeceraOrdenPedido)
				invoicing.AddValueParameterized("idUserPos", idUserPos)
				invoicing.AddValueParameterized("idEstacion", resultStation.IdStation)
				invoicing.AddValueParameterized("idPeriodo", idPeriod)
				invoicing.AddValueParameterized("idControlEstacion", idControlStation)
				//invoicing.AddValueParameterized("tipoOrden", "KIOSKO")
				_, err = connection.IQuery(invoicing.GetStoreProcedure(), invoicing.GetValues())
				if err != nil {
					return fmt.Errorf("[chile.order.go]Error al ejecutar el sp facturacion.IAE_Fac_InsertFactura_kioskoEfectivo:%v", err)
				}
			}

			//Actualizacion de la orden en la tabla detalle_orden_pedido
			update := "UPDATE dbo.Detalle_Orden_Pedido SET dop_impresion = 0 WHERE IDCabeceraOrdenPedido = @idCabeceraOrdenPedido and dop_cuenta = 1 and dop_anulacion = 1 and dop_impresion = -1"
			_, err = connection.Exec(update, sql.Named("idCabeceraOrdenPedido", idCabeceraOrdenPedido))
			if err != nil {
				return fmt.Errorf("[chile.order.go]No se pudo ejecutar el UPDATE %s con idCabeceraOrdenPedido:%s", update, idCabeceraOrdenPedido)
			}
			update = "UPDATE dbo.Detalle_Orden_Pedido SET dop_impresion = -1 WHERE IDCabeceraOrdenPedido = @idCabeceraOrdenPedido and dop_cuenta = 1 and dop_anulacion = 1 and dop_impresion = 1"
			_, err = connection.Exec(update, sql.Named("idCabeceraOrdenPedido", idCabeceraOrdenPedido))
			if err != nil {
				return fmt.Errorf("[chile.order.go]No se pudo ejecutar el UPDATE %s con idCabeceraOrdenPedido:%s", update, idCabeceraOrdenPedido)
			}
			update = "UPDATE dbo.Detalle_Orden_Pedido SET dop_estado = -1 WHERE IDCabeceraOrdenPedido = @idCabeceraOrdenPedido and dop_cuenta = 1 and dop_anulacion <> 0 and dop_impresion = 1"
			_, err = connection.Exec(update, sql.Named("idCabeceraOrdenPedido", idCabeceraOrdenPedido))
			if err != nil {
				return fmt.Errorf("[chile.order.go]No se pudo ejecutar el UPDATE %s con idCabeceraOrdenPedido:%s", update, idCabeceraOrdenPedido)
			}
		}
	}

	order := &lib_gen_proto.ResultStoreValidationData{}
	order.OrderId = orderId
	order.CfacId = *cfacId
	connection.Commit()
	o.SendOrderResponse(order)
	o.SendOrderTurner(*cfacId)
	defer func() {
		errPrintService := o.WsPrintService(connection, *cfacId, idCabeceraOrdenPedido, *idUserPos, nil, resultStation)
		if errPrintService != nil {
			logger.Error.Println(errPrintService.Error())
		}

	}()
	return nil
}
func (o *OrderStore) CreateOrderKioskTarjeta() (err error) {
	defer func() {
		if r := recover(); r != nil {
			stackTrace := string(debug.Stack())
			// Convertir el panic y el stack trace en un error detallado
			err = fmt.Errorf("se produjo un panic: %v\nStack trace:\n%s", r, stackTrace)
		}
	}()
	orderId := o.Order.Cabecera.OrderId
	statusMxp := o.StoreData.Status
	var idPaymentMethod *string
	currentDate := time.Now()
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
		return fmt.Errorf("[chile.order.go]No se encontro ningun periodo abierto")
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
		return fmt.Errorf("[chile.order.go]La estacion de kiosko no se encuentra activa")
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
		return fmt.Errorf("[chile.order.go]No se encontro el id del usuario para kiosko")
	}

	//Se obtiene el estado42
	var status42 *string
	queryStatus42 := "SELECT CAST(CONVERT(UNIQUEIDENTIFIER, HASHBYTES('MD5','42')) AS VARCHAR(40))"
	err = connection.QueryRow(queryStatus42).Scan(&status42)
	if err != nil {
		return fmt.Errorf("[chile.order.go]Error al ejecutar el query %v: %v", queryStatus42, err)
	}

	//obtener el idFormaPago para tarjeta
	queryPaymentMethod := fmt.Sprintf(`SELECT TOP 1 CAST(FormaPago.IDFormapago AS VARCHAR(40)) FROM ColeccionCadena
									INNER JOIN ColeccionDeDatosCadena ON ColeccionCadena.ID_ColeccionCadena = ColeccionDeDatosCadena.ID_ColeccionCadena
									INNER JOIN CadenaColeccionDeDatos ON CadenaColeccionDeDatos.ID_ColeccionCadena = ColeccionCadena.ID_ColeccionCadena AND CadenaColeccionDeDatos.ID_ColeccionDeDatosCadena= ColeccionDeDatosCadena.ID_ColeccionDeDatosCadena
									INNER JOIN FormaPago ON  CAST(Formapago.IDFormapago AS VARCHAR(40)) = CAST(CadenaColeccionDeDatos.idIntegracion AS VARCHAR(40))
									WHERE ColeccionCadena.Descripcion='TEXTO DE RESPUESTA PARA PAGOS CON TARJETA'
									AND CadenaColeccionDeDatos.cdn_id = @cadena
									AND ColeccionCadena.cdn_id = @cadena
									AND CadenaColeccionDeDatos.variableV = @tipoCuenta`)
	rows, err := connection.Query(queryPaymentMethod,
		sql.Named("tipoCuenta", getTypeAccount(o.Order.PaymentMethods.Card.Processor, o.Order.PaymentMethods.Card.TypeAccount)),
		sql.Named("cadena", o.StoreData.ChainId))
	if err != nil {
		return fmt.Errorf("[chile.order.go]Error al ejecutar el query %v con parametros %v: %v",
			queryPaymentMethod,
			connection.GetArgs(
				sql.Named("tipoCuenta", o.Order.PaymentMethods.Card.TypeAccount),
				sql.Named("cadena", o.StoreData.ChainId)),
			err)
	}
	defer rows.Close()
	for rows.Next() {
		err = rows.Scan(&idPaymentMethod)
		if err != nil {
			return fmt.Errorf("[chile.order.go]Error al obtener el idFormaPago: %v", err)
		}
	}
	if idPaymentMethod == nil {
		return fmt.Errorf("[chile.order.go]Error la forma de pago de pago es vacio")
	}

	//Procesamiento de tarjeta
	err = connection.CreateBegin()
	defer connection.Rollback()
	if err != nil {
		return fmt.Errorf("[chile.order.go]Error al crear BEGIN: %v", err.Error())
	}
	documentId := o.Order.Cabecera.Client.DocumentId
	idTypeDocument := o.StoreData.GetTipoDocumento(documentId)
	if idTypeDocument == nil {
		return fmt.Errorf("[chile.order.go]No se encontro el tipo de documento para el id %s", documentId)
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
	var idCabeceraOrdenPedido string
	dataInvoicing := &models.ResponseInvoicing{}
	orderHeaderOrder := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[pedido].[ORD_configuracion_proceso_ordenpedido_Newkiosko]")
	orderHeaderOrder.AddValueParameterized("rst_id", o.StoreData.RestaurantId)
	orderHeaderOrder.AddValueParameterized("IDMesa", idMesa)
	orderHeaderOrder.AddValueParameterized("IDUsersPos", idUserPos)
	orderHeaderOrder.AddValueParameterized("IDEstacion", resultStation.IdStation)
	orderHeaderOrder.AddValueParameterized("num_Pers", 1)
	//orderHeaderOrder.AddValueParameterized("idPedido", idOrderKiosko)
	orderHeaderOrder.AddValueParameterized("idOrdenPedido", sql.Out{Dest: &idCabeceraOrdenPedido})

	_, err = connection.IQuery(orderHeaderOrder.GetStoreProcedure(), orderHeaderOrder.GetValues())
	if err != nil {
		return fmt.Errorf("[chile.order.go]Error al ejecutar el sp [pedido].[ORD_configuracion_proceso_ordenpedido_Newkiosko]: %v", err)
	}
	if utils.IsEmpty(idCabeceraOrdenPedido) {
		return fmt.Errorf("[chile.order.go]Error al obtener el id de la tabla cabecera_orden_pedido")
	}

	//insercion de los productos a la tabla detalle_orden_pedido
	err = o.InsertDetailOrder(connection, datailsKiosko, idCabeceraOrdenPedido)
	if err != nil {
		return err
	}

	// ANTES de ejecutar el SP, verifica que existan productos en la orden
	checkQuery := `SELECT COUNT(*) FROM dbo.Detalle_Orden_Pedido 
				WHERE IDCabeceraOrdenPedido = @id AND dop_cuenta = 1 AND dop_anulacion = 1`
	var count int
	err = connection.QueryRow(checkQuery, sql.Named("id", idCabeceraOrdenPedido)).Scan(&count)
	if err != nil {
		return fmt.Errorf("error verificando productos: %v", err)
	}
	logger.DebugItem.Printf("Productos encontrados en Detalle_Orden_Pedido: %d", count)

	if count == 0 {
		return fmt.Errorf("no hay productos en la orden de pedido %s", idCabeceraOrdenPedido)
	}

	var cfacId, cfacTotal *string
	isDiscountCouponsBines := o.Feature.GetConfigFeatureFlag(featureflag2.DESCUENTO_CUPONES_BINES)
	//Insercion de datos en caso de descuento
	if o.Order.Discount != nil && isDiscountCouponsBines {
		var lvCfacId string
		var lvCfacTotal string
		var cfacTotalFormaPago string
		insertInvoiceDiscount := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "dbo.App_IngresarInfoFacturaCabeceraDescuentos")
		insertInvoiceDiscount.AddValueParameterized("codigo_app", orderId)
		insertInvoiceDiscount.AddValueParameterized("IDRestaurante", o.StoreData.RestaurantId)
		insertInvoiceDiscount.AddValueParameterized("IDCabeceraOrdenPedido", idCabeceraOrdenPedido)
		insertInvoiceDiscount.AddValueParameterized("IDUsersPos", idUserPos)
		insertInvoiceDiscount.AddValueParameterized("IDEstacion", resultStation.IdStation)
		insertInvoiceDiscount.AddValueParameterized("IDPeriodo", idPeriod)
		insertInvoiceDiscount.AddValueParameterized("IDControlEstacion", idControlStation)
		insertInvoiceDiscount.AddValueParameterized("IDCliente", nil)
		insertInvoiceDiscount.AddValueParameterized("Medio", "")
		insertInvoiceDiscount.AddValueParameterized("origen", "KIOSKO")
		insertInvoiceDiscount.AddValueParameterized("IDCabeceraFactura", sql.Out{Dest: &lvCfacId})
		insertInvoiceDiscount.AddValueParameterized("TotalFactura", sql.Out{Dest: &lvCfacTotal})
		insertInvoiceDiscount.AddValueParameterized("TotalFormaPago", sql.Out{Dest: &cfacTotalFormaPago})
		_, err = connection.IQuery(insertInvoiceDiscount.GetStoreProcedure(), insertInvoiceDiscount.GetValues())
		if err != nil {
			return fmt.Errorf("[chile.order.go]Error al ejecutar el sp dbo.App_IngresarInfoFacturaCabeceraDescuentos: %v", err.Error())
		}
		if utils.IsEmpty(lvCfacId) {
			return fmt.Errorf("[chile.order.go]Error al obtener el cfac_id del sp dbo.App_IngresarInfoFacturaCabeceraDescuentos")
		}
		if utils.IsEmpty(lvCfacTotal) {
			return fmt.Errorf("[chile.order.go]Error al obtener el valor total de la factura del sp dbo.App_IngresarInfoFacturaCabeceraDescuentos")
		}
		cfacId = &lvCfacId
		cfacTotal = &lvCfacTotal
		//Insercion de los datos de descuento a la tabla descuento_factura
		var valorDiscount float32
		valorDiscount = o.Order.Discount.AmountDiscount
		if o.Order.Discount.PercentageDiscount > 0 {
			valorDiscount = float32(o.Order.Discount.PercentageDiscount)
		}

		if !utils.IsEmpty(o.Order.Discount.ExternalId) && utils.IsEmpty(o.Order.Discount.IdDataMasterDiscount) {
			logInvoiceDiscount := sqlserver.NewSimpleInsertBuilderSQL(false, "dbo.Descuento_Factura")
			logInvoiceDiscount.AddValue("cfac_id", cfacId)
			logInvoiceDiscount.AddValue("IDDescuentos", o.Order.Discount.ExternalId)
			logInvoiceDiscount.AddValue("desf_valor", valorDiscount)
			logInvoiceDiscount.AddValue("replica", 0)
			logInvoiceDiscount.AddValue("IDUsersPos", idUserPos)
			logInvoiceDiscount.AddValue("IDStatus", "8B039503-85CF-E511-80C6-000D3A3261F3")
			logInvoiceDiscount.AddValue("numeroLoteReplicacion", 0)
			_, err = connection.IQuery(logInvoiceDiscount.SqlGenerated(), logInvoiceDiscount.GetValues())
			if err != nil {
				return fmt.Errorf("[chile.order.go]Error al insertar los datos en la tabla dbo.Descuento_Factura: %v", err)
			}
		}

	} else {
		//Insercion de datos para la generacion de factura
		insertInvoicing := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[IAE_Fac_InsertFactura_Kiosko_Tarjeta]")
		insertInvoicing.AddValueParameterized("IDRestaurante", o.StoreData.RestaurantId)
		insertInvoicing.AddValueParameterized("IDCabeceraOrdenPedido", idCabeceraOrdenPedido)
		insertInvoicing.AddValueParameterized("IDUsersPos", idUserPos)
		insertInvoicing.AddValueParameterized("numeroCuenta", 1)
		insertInvoicing.AddValueParameterized("IDEstacion", resultStation.IdStation)
		insertInvoicing.AddValueParameterized("IDPeriodo", idPeriod)
		insertInvoicing.AddValueParameterized("IDControlEstacion", idControlStation)
		insertInvoicing.AddValueParameterized("tipoBeneficioCupon", 0)
		rowsInvoicing, err := connection.IQuery(insertInvoicing.GetStoreProcedure(), insertInvoicing.GetValues())
		if err != nil {
			return fmt.Errorf("[chile.order.go]Error al ejecutar el sp [facturacion].[IAE_Fac_InsertFactura_Kiosko_Tarjeta]: %v", err)
		}
		defer rowsInvoicing.Close()
		for rowsInvoicing.Next() {
			err = rowsInvoicing.Scan(
				&dataInvoicing.CdnId,
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
				&dataInvoicing.PorcentajeImpuestos,
			)
			if err != nil {
				return fmt.Errorf("[chile.order.go]Error al obtener los datos del sp [facturacion].[IAE_Fac_InsertFactura_Kiosko_Tarjeta]: %v", err)
			}
		}
		if dataInvoicing.CfacId == nil {
			return fmt.Errorf("[chile.order.go]Error - El cfacId generado del sp [facturacion].[IAE_Fac_InsertFactura_Kiosko_Tarjeta] esta vacio, por favor revisar")
		}
		cfacId = dataInvoicing.CfacId
		cfacTotal = dataInvoicing.DtfacTotal
	}

	//Actualizo la tabla kiosko_cabecera_pedidos con el cfacId
	updateKioskoHeader := fmt.Sprintf(`UPDATE dbo.kiosko_cabecera_pedidos
				SET cfac_id = @codFactura
				WHERE id = @idPedido`)
	rowsKioskoHeader, err := connection.Exec(updateKioskoHeader, sql.Named("codFactura", cfacId), sql.Named("idPedido", idOrderKiosko))
	if err != nil {
		return fmt.Errorf("[chile.order.go]Error al actualizar los datos de la tabla kiosko_cabecera_pedidos: %v", err)
	}
	affectedKioskoHeader, err := rowsKioskoHeader.RowsAffected()
	if err != nil {
		return err
	}
	if affectedKioskoHeader < 1 {
		return fmt.Errorf("[chile.order.go]No se ha podio actualizar los datos de la tabla kiosko_cabecera_pedidos")
	}
	updateInvoiceHeader := fmt.Sprintf(`UPDATE dbo.Cabecera_Factura
				SET Cabecera_FacturaVarchar2 = 'KIOSKO TARJETA'
				WHERE cfac_id = @codFactura`)
	rowsInvoiceHeader, err := connection.Exec(
		updateInvoiceHeader,
		sql.Named("codFactura", cfacId),
	)
	if err != nil {
		return fmt.Errorf("[chile.order.go]Error al actualizar los datos de la tabla Cabecera_Factura: %v", err)
	}
	affectedInvoiceHeader, err := rowsInvoiceHeader.RowsAffected()
	if err != nil {
		return err
	}
	if affectedInvoiceHeader < 1 {
		return fmt.Errorf("[chile.order.go]No se ha podio actualizar los datos de la tabla Cabecera_Factura")
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
		return fmt.Errorf("[chile.order.go]Error al actualizar los datos de la tabla Cabecera_Orden_Pedido: %v", err)
	}
	affectedOrderHeader, err := rowsOrderHeader.RowsAffected()
	if err != nil {
		return err
	}
	if affectedOrderHeader < 1 {
		return fmt.Errorf("[chile.order.go]No se ha podio actualizar los datos de la tabla cabecera_orden_pedido")
	}

	//Actualizar cliente
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
		}
		idClient, document, err = o.GetIdClient(connection)
		if err != nil {
			return err
		}
	}
	//
	if o.Order.Discount != nil &&
		isDiscountCouponsBines &&
		!utils.IsEmpty(o.Order.Discount.ExternalId) &&
		!utils.IsEmpty(o.Order.Discount.IdDataMasterDiscount) {

		/*insertRedeemedPromotions := sqlserver.NewSimpleInsertBuilderSQL(false, "dbo.promociones_canjeados")
		insertRedeemedPromotions.AddValue("id_PromocionesCajeados", "NEWID()")
		insertRedeemedPromotions.AddValue("Id_Promociones", o.Order.Discount.ExternalId)
		insertRedeemedPromotions.AddValue("cfac_id", cfacId)
		insertRedeemedPromotions.AddValue("Id_cliente", idClient)
		insertRedeemedPromotions.AddValue("Fecha_Canje", currentDate)
		insertRedeemedPromotions.AddValue("Cantidad", 1)
		insertRedeemedPromotions.AddValue("LastUser", idUserPos)
		insertRedeemedPromotions.AddValue("LastUpdate", currentDate)
		insertRedeemedPromotions.AddValue("IDCabeceraOrdenPedido", idCabeceraOrdenPedido)
		insertRedeemedPromotions.AddValue("NumeroCuenta", 1)
		insertRedeemedPromotions.AddValue("IDCanjeMasterData", o.Order.Discount.IdDataMasterDiscount)
		insertRedeemedPromotions.AddValue("IDStatus", "B05F0AD7-BE99-4ED6-8ECB-969E14A30BD5")
		insertRedeemedPromotions.AddValue("Tipo_aplica", "FACTURA")
		_, err = connection.IQuery(insertRedeemedPromotions.SqlGenerated(), insertRedeemedPromotions.GetValues())
		if err != nil {
			return fmt.Errorf("[chile.order.go]Error al insertar los datos en la tabla dbo.promociones_canjeados: %w", err)
		}*/
		insertRedeemedPromotions := fmt.Sprintf(`INSERT INTO	dbo.promociones_canjeados
										(
										id_PromocionesCajeados,
										Id_Promociones,
										cfac_id,
										Id_cliente,
										Fecha_Canje,
										Cantidad,
										LastUser,
										LastUpdate,
										IDCabeceraOrdenPedido,
										NumeroCuenta,
										IDCanjeMasterData,
										IDStatus,
										Tipo_aplica
										)VALUES(
										NEWID(),
										@IdPromociones,
										@cfacid,
										@idCliente,
										@fechaCanje,
										1,
										@lastUser,
										@lastUpdate,
										@iDCabeceraOrdenPedido,
										1,
										@iDCanjeMasterData,
										'B05F0AD7-BE99-4ED6-8ECB-969E14A30BD5',
											'FACTURA'
											)`)
		_, err = connection.Query(insertRedeemedPromotions,
			sql.Named("IdPromociones", o.Order.Discount.ExternalId),
			sql.Named("cfacid", cfacId),
			sql.Named("idCliente", idClient),
			sql.Named("fechaCanje", currentDate),
			sql.Named("lastUser", idUserPos),
			sql.Named("lastUpdate", currentDate),
			sql.Named("iDCabeceraOrdenPedido", idCabeceraOrdenPedido),
			sql.Named("iDCanjeMasterData", o.Order.Discount.IdDataMasterDiscount),
		)
		if err != nil {
			return fmt.Errorf("[chile.order.go]Error al insertar los datos en la tabla dbo.Promociones_Canjeados: %w", err)
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
		return fmt.Errorf("[chile.order.go]Error al insertar los datos en la tabla dbo.SWT_Requerimiento_Autorizacion: %v", err)
	}

	//Insercion a la tabla SWT_Respuesta_Autorizacion
	fecha := currentDate.Format("20060102")
	hora := currentDate.Format("150405")
	binCol := o.Order.PaymentMethods.Card.Bin
	responseAuthorization := sqlserver.NewSimpleInsertBuilderSQL(true, "dbo.SWT_Respuesta_Autorizacion")
	responseAuthorization.AddValue("rsaut_trama", cfacId)
	responseAuthorization.AddValue("rsaut_fecha", currentDate)
	responseAuthorization.AddValue("ttra_codigo", o.Order.PaymentMethods.Card.IdentifierTypeResponse)
	responseAuthorization.AddValue("cres_codigo", o.Order.PaymentMethods.Card.CodeResponse)
	responseAuthorization.AddValue("rsaut_respuesta", o.Order.PaymentMethods.Card.CodeResponseDescription)
	responseAuthorization.AddValue("rsaut_secuencial_transaccion", o.Order.PaymentMethods.Card.CodeResponse)
	responseAuthorization.AddValue("rsaut_hora_autorizacion", hora)
	responseAuthorization.AddValue("rsaut_fecha_autorizacion", fecha)
	responseAuthorization.AddValue("rsaut_numero_autorizacion", o.Order.PaymentMethods.Card.AuthorizationCode)
	responseAuthorization.AddValue("rsaut_terminal_id", o.Order.PaymentMethods.Card.NoBox)
	responseAuthorization.AddValue("rsaut_grupo_tarjeta", o.Order.PaymentMethods.Card.Franchise)
	responseAuthorization.AddValue("rsaut_red_adquiriente", "RED DATAFAST")
	responseAuthorization.AddValue("rsaut_merchant_id", o.Order.PaymentMethods.Card.MID)
	responseAuthorization.AddValue("rsaut_numero_tarjeta", o.Order.PaymentMethods.Card.NumberCard)
	responseAuthorization.AddValue("rstaut_tarjetahabiente", o.Order.PaymentMethods.Card.CardHolder)
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
		return fmt.Errorf("[chile.order.go]Error al insertar los datos en la tabla dbo.SWT_Respuesta_Autorizacion: %v", err)
	}
	if rowsResponseAuthorization.Err() != nil {
		return fmt.Errorf("[chile.order.go]Error al iterar el rows de la tabla dbo.SWT_Respuesta_Autorizacion: %v", rowsResponseAuthorization.Err())
	}
	var rsAutId int32
	err = rowsResponseAuthorization.Scan(&rsAutId)
	if err != nil {
		return fmt.Errorf("[chile.order.go]Error al obtener el identificador unico de la tabla dbo.SWT_Respuesta_Autorizacion: %v", err.Error())
	}

	insertPaymentMethod := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "facturacion.fac_insertaFormaPago")
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
		return fmt.Errorf("[chile.order.go]Error al ejecutar el sp facturacion.fac_insertaFormaPago: %v", err)
	}
	//INSERTA CLIENTE - FIN FACTURA
	invoicingClient := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[USP_FacturaCliente]")
	invoicingClient.AddValueParameterized("idCliente", document)
	invoicingClient.AddValueParameterized("IDFactura", cfacId)
	invoicingClient.AddValueParameterized("IDUserpos", idUserPos)
	invoicingClient.AddValueParameterized("TipoDocumento", dataTypeDocument.Description)
	_, err = connection.IQuery(invoicingClient.GetStoreProcedure(), invoicingClient.GetValues())
	if err != nil {
		return fmt.Errorf("[chile.order.go]Error al ejecutar el sp [facturacion].[USP_FacturaCliente]: %v", err)
	}

	//Impresion de la factura y de la orden del pedido
	var jsonInvoicing *string
	dataOrderPrintFastFood := make([]*models.KioskoImpresionOrdenPedidoFastFood, 0)

	dynamicInvoicePrinting := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[kioskoColombia_impresiondinamica_factura_ch]")
	dynamicInvoicePrinting.AddValueParameterized("cfac_id", cfacId)
	dynamicInvoicePrinting.AddValueParameterized("tipo_comprobante", "F")
	rowInvoicePrinting, err := connection.IQuery(dynamicInvoicePrinting.GetStoreProcedure(), dynamicInvoicePrinting.GetValues())
	if err != nil {
		return fmt.Errorf("[chile.order.go]Error al ejecutar el sp [facturacion].[kioskoColombia_impresiondinamica_factura_ch]: %v", err)
	}
	defer rowInvoicePrinting.Close()
	for rowInvoicePrinting.Next() {
		err = rowInvoicePrinting.Scan(&jsonInvoicing)
		if err != nil {
			return fmt.Errorf("[chile.order.go]Error al obtener el json de impresion de la factura del sp [dbo].[kioskoColombia_impresiondinamica_factura_ch]: %v", err)
		}
	}
	if jsonInvoicing == nil {
		return fmt.Errorf("[chile.order.go]Error, no se genero el json de impresion de factura")
	}
	//Impresión de orden de pedido en impresora de línea
	orderPrintFastFood := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[pedido].[IAE_ImpresionOrdenPedidoFastFoodKiosko]")
	orderPrintFastFood.AddValueParameterized("idCabeceraOrdenPedido", idCabeceraOrdenPedido)
	orderPrintFastFood.AddValueParameterized("idCadena", o.StoreData.ChainId)
	orderPrintFastFood.AddValueParameterized("idRestaurante", o.StoreData.RestaurantId)
	rowOrderPrint, err := connection.IQuery(orderPrintFastFood.GetStoreProcedure(), orderPrintFastFood.GetValues())
	if err != nil {
		return fmt.Errorf("[chile.order.go]Error al ejecutar el sp [pedido].[IAE_ImpresionOrdenPedidoFastFoodKiosko]: %v", err)
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
			return fmt.Errorf("[chile.order.go]Error al obtener los datos para la impresion de la orden de pedido: %v", err)
		}
		dataOrderPrintFastFood = append(dataOrderPrintFastFood, &dataOrderPrint)
	}
	connection.Commit()
	//
	order := &lib_gen_proto.ResultStoreValidationData{}
	order.IdOrdenPedido = idCabeceraOrdenPedido
	order.OrderId = orderId
	order.CfacId = *cfacId
	order.Factura = *jsonInvoicing

	dataFe, errElectronicInvoicing := o.ElectronicInvoicingCl(connection, *cfacId)
	if errElectronicInvoicing != nil {
		logger.Error.Println(errElectronicInvoicing.Error())
	}
	if !utils.IsEmpty(dataFe.String()) {
		o.ServicesChannel.CreateChannel(*cfacId)
		o.SendOrderFolio(dataFe)

		rawResponse := o.ServicesChannel.ReceiveFromChannel(*cfacId)
		if rawResponse != nil {
			responseFolio, ok := rawResponse.(*lib_gen_proto_folio.ResponseFeChile)
			if ok {
				responseDataFe := responseFe{
					Status:           responseFolio.Status,
					Mensaje:          responseFolio.Mensaje,
					ObjetoRespuesta:  responseFolio.ObjetoRespuesta,
					ObjetoRespuesta2: responseFolio.ObjetoRespuesta2,
					Facturacion: feFacturacion{
						Folio:     responseFolio.Facturacion.Folio,
						IdFactura: responseFolio.Facturacion.IdFactura,
						Fecha:     responseFolio.Facturacion.Fecha,
						Monto:     responseFolio.Facturacion.Monto,
						Id:        responseFolio.Facturacion.Id,
					},
				}
				// Convertir a JSON string sin afectar el xml de facturacion electronica
				var jsonStr bytes.Buffer
				encoder := json.NewEncoder(&jsonStr)
				encoder.SetEscapeHTML(false) // <- evita los \u003c y \u003e
				errEncode := encoder.Encode(responseDataFe)
				if errEncode != nil {
					logger.Error.Println("[chile.fe.go]Error al convertir la respuesta json en string: ", errEncode.Error())
				} else {
					order.FacturacionElectronica = jsonStr.String()
					logger.Info.Println("[chile.fe.go]la respuesta del fe de chile es: " + jsonStr.String())
				}

			}
		}

	}

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
	}()
	return nil
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
			return nil, fmt.Errorf("[chile.order.go]Error al ejecutar el query %v: %v", query, err)
		}
		defer rows.Close()
		for rows.Next() {
			err = rows.Scan(&dataStation.IdStation, &dataStation.CashierName)
			if err != nil {
				return nil, fmt.Errorf("[chile.order.go]Error al obtener los datos de estacion del kiosko: %v", err)
			}
		}
		if dataStation.IdStation == nil && dataStation.CashierName == nil {
			return nil, fmt.Errorf("[chile.order.go]Error, no se encontro el id y el nombre del cajero de kiosko, por favor validar")
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
		return nil, fmt.Errorf("[chile.order.go]Error al ejecutar el query %v: %v", query, err)
	}
	defer rows.Close()
	for rows.Next() {
		var idControlStation *string
		err = rows.Scan(&idControlStation)
		if err != nil {
			return nil, fmt.Errorf("[chile.order.go]Error al obtener los datos del control estacion de kiosko: %s", err)
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
			return "", fmt.Errorf("[chile.order.go]Error al obtener el idMesa de kiosko %v: %s", query, errScan.Error())
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
		return nil, fmt.Errorf("[chile.order.go]Error al ejecutar el query %v: %v", query, err)
	}
	defer result.Close()
	for result.Next() {
		var getIdUserPos string
		err = result.Scan(&getIdUserPos)
		if err != nil {
			return nil, fmt.Errorf("[chile.order.go]Error al obtener el id del Usuario %v: %v", dataStation.CashierName, err)
		}
		return &getIdUserPos, nil
	}
	return nil, nil
}
func (o *OrderStore) GetOpenPeriod(connection *sqlserver.DatabaseSql) (*string, error) {
	query := fmt.Sprintf(`SELECT CAST(IDPeriodo AS VARCHAR(40))
			FROM dbo.Periodo
			WHERE IDStatus = @idStatus
				AND prd_fechacierre IS NULL`)
	rows, err := connection.Query(query, sql.Named("idStatus", o.StoreData.Status.PeriodoAperturaAbierto))
	if err != nil {
		return nil, fmt.Errorf("[chile.order.go]Error al ejecutar el query %s: %s", query, err.Error())
	}
	defer rows.Close()
	for rows.Next() {
		var result string
		err = rows.Scan(&result)
		if err != nil {
			return nil, fmt.Errorf("[chile.order.go]Error al obtener los datos del query %v: %s", query, err)
		}
		return &result, nil
	}
	return nil, nil
}
func (o *OrderStore) CheckIfAlreadyExistOrder(connection *sqlserver.DatabaseSql, codigoApp string) error {
	query := fmt.Sprintf(`SELECT count(*) as cantidad FROM dbo.kiosko_cabecera_pedidos WHERE codigo_app = @codigoApp`)
	result, err := connection.Query(query, sql.Named("codigoApp", codigoApp))
	if err != nil {
		return fmt.Errorf("[chile.order.go]Error al ejecutar el query %v: %v", query, err)
	}
	defer result.Close()
	for result.Next() {
		var countData int32
		err = result.Scan(&countData)
		if err != nil {
			return fmt.Errorf("[chile.order.go]Error al validar el codigoApp %v de la tabla kiosko_cabecera_pedidos: %v", codigoApp, err)
		}
		if countData > 0 {
			return fmt.Errorf("[chile.order.go]El codigo %v ya se encuentra registrado, por favor ingresar uno nuevo", codigoApp)
		}
	}
	return nil
}
func (o *OrderStore) InsertOrderKiosko(connection *sqlserver.DatabaseSql, cfacId *string) (int32, []*models.KioskoPlus, error) {
	var idOrder int32
	responseDetailsOrder := make([]*models.KioskoPlus, 0)
	isLocator := o.Feature.GetConfigFeatureFlag(featureflag2.LOCALIZADOR)
	isUpsell := o.Feature.GetConfigFeatureFlag(featureflag2.UPSELLING)
	isDiscountCouponsBines := o.Feature.GetConfigFeatureFlag(featureflag2.DESCUENTO_CUPONES_BINES)
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
		if isDiscountCouponsBines && !utils.IsEmpty(o.Order.Discount.ExternalId) {
			kioskoHeader.AddValue("descuentoExternalId", o.Order.Discount.ExternalId)
		}
		if isDiscountCouponsBines && !utils.IsEmpty(o.Order.Discount.IdDataMasterDiscount) {
			kioskoHeader.AddValue("idCanjeMasterData", o.Order.Discount.IdDataMasterDiscount)
		}
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
		return -1, nil, fmt.Errorf("[chile.order.go]Error al insertar los datos en la tabla dbo.kiosko_cabecera_pedidos: %v", err)
	}
	if rows.Err() != nil {
		return -1, nil, fmt.Errorf("[chile.order.go]Error al iterar el rows: %v", rows.Err())
	}
	err = rows.Scan(&idOrder)
	if err != nil {
		return -1, nil, fmt.Errorf("[chile.order.go]Error al obtener el id de la tabla dbo.kiosko_cabecera_pedidos: %v", err.Error())
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
			return -1, nil, fmt.Errorf("[chile.order.go]Error al insertar los productos en la tabla dbo.kiosko_detalle_pedidos: %v", err)
		}
		if rowsDetails.Err() != nil {
			return -1, nil, fmt.Errorf("[chile.order.go]Error al iterar los datos de la tabla dbo.kiosko_detalle_pedidos: %v", rowsDetails.Err())
		}
		var detailsOrderId int32
		err = rowsDetails.Scan(&detailsOrderId)
		if err != nil {
			return -1, nil, fmt.Errorf("[chile.order.go]Error al obtener el identificador de la tabla dbo.kiosko_detalle_pedidos: %v", err.Error())
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
				return -1, nil, fmt.Errorf("[chile.order.go]Error al insertar los modificadores en la tabla dbo.kiosko_detalle_pedidos: %v", err)
			}
			var modifiersOrderId int32
			err = rowsModifiers.Scan(&modifiersOrderId)
			if err != nil {
				return -1, nil, fmt.Errorf("[chile.order.go]Error al obtener el id de la tabla dbo.kiosko_detalle_pedidos: %v", err.Error())
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
			return -1, nil, fmt.Errorf("[chile.order.go]error al insertar los datos en la tabla dbo.kiosko_autorizaciones_switch: %v", err)
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
		return -1, nil, fmt.Errorf("[chile.order.go]Error al insertar los datos en la tabla dbo.kiosko_forma_pagos: %v", err)
	}
	return idOrder, responseDetailsOrder, nil
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
			return fmt.Errorf("[chile.order.go]Error al ejecutar el query %v: %v", query, err)
		}
		for rows.Next() {
			var dataProduct models.KioskoPlus
			var pluId *int32
			err = rows.Scan(&pluId, &dataProduct.ValorNeto, &dataProduct.ValorIva, &dataProduct.ValorBruto)
			if err != nil {
				rows.Close()
				return fmt.Errorf("[chile.order.go]Error al obtener los pluId de los productos: %v", err)
			}
			if pluId == nil {
				return fmt.Errorf("[chile.order.go]Error el pluId %v no exite, por favor revisar", datails.PluId)
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
			return fmt.Errorf("[chile.order.go]Error al insertar los datos en la tabla dbo.Detalle_Orden_Pedido %v: %v", detailsOrder.GetValues(), err)
		}
		if rows.Err() != nil {
			return fmt.Errorf("[chile.order.go]Error al iterar el rows: %v", rows.Err())
		}
		var idDetalleOrdenPedido string
		err = rows.Scan(&idDetalleOrdenPedido)
		if err != nil {
			return fmt.Errorf("[chile.order.go]Error al obtener el idDetalleOrdenpedido al momento de insertar los datos en la tabla dbo.Detalle_Orden_Pedido: %v", err)
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
				return fmt.Errorf("[chile.order.go]Error al actualizar los datos de la tabla Detalle_Orden_Pedido: %v", err)
			}
			rowsAfected, err := exec.RowsAffected()
			if err != nil {
				return fmt.Errorf("[chile.order.go]Error al identificar la actualizacion de los datos de la tabla Detalle_Orden_Pedido: %v", err)
			}
			if rowsAfected != 1 {
				return fmt.Errorf("[chile.order.go]No se actualizaron los registros de la tabla detalle_orden_pedido")
			}
		} else {
			exec, err := connection.Exec(updateDetails,
				sql.Named("isModifica", detailsInsert.Exchange),
				sql.Named("idDetalleOrdenPedido", detailsInsert.IdDetalleOrdenPedido),
				sql.Named("addSecond", i+1),
				sql.Named("idKioskoDetallePedido", idOrderToString),
				sql.Named("idCabeceraOrdenPedido", detailsInsert.IdCabeceraOrdenPedido))
			if err != nil {
				return fmt.Errorf("[chile.order.go]Error al actualizar los datos en la tabla Detalle_Orden_Pedido: %v", err)
			}
			rowsAfected, err := exec.RowsAffected()
			if err != nil {
				return fmt.Errorf("[chile.order.go]Error al identificar la actualizacion de los datos de la tabla Detalle_Orden_Pedido: %v", err)
			}
			if rowsAfected != 1 {
				return fmt.Errorf("[chile.order.go]No se actualizaron los registros de la tabla detalle_orden_pedido")
			}
		}

	}

	return nil

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
		return nil, nil, fmt.Errorf("[chile.order.go]Error al ejecutar el query %v: %v", query, err)
	}
	defer result.Close()
	for result.Next() {
		var idClient, docuement *string
		err = result.Scan(&idClient, &docuement)
		if err != nil {
			return nil, nil, fmt.Errorf("[chile.order.go]Error al validar los datos de Cliente: %v", err)
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
		return nil, fmt.Errorf("[chile.order.go]Error al ejectuar el sp [config].[IAE_Cliente] %v", err)
	}
	if !strings.EqualFold("U", accion) {
		defer rows.Close()
		for rows.Next() {
			var idClient *string
			err = rows.Scan(&idClient)
			if err != nil {
				return nil, fmt.Errorf("[chile.order.go]Error al obtener los datos del sp [config].[IAE_Cliente] %v", err)
			}
			return idClient, nil
		}
	}

	return nil, nil
}
func (o *OrderStore) ElectronicInvoicingCl(connection *sqlserver.DatabaseSql, cfacId string) (*lib_gen_proto_folio.RequestFolio, error) {
	//Variables
	dataHeaderInvoicing := &models.HeaderFolder{}
	dataDetailsInvoicing := make([]*models.DetailsFolder, 0)
	//Obtencion de la cabecera factura
	headerInvoicing := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[Cabecera_Facturacion_Electronica_Chile]")
	headerInvoicing.AddValueParameterized("cadena", o.StoreData.ChainId)
	headerInvoicing.AddValueParameterized("restaurante", o.StoreData.RestaurantId)
	headerInvoicing.AddValueParameterized("cfac_id", cfacId)
	rowsHeaderInvoicing, err := connection.IQuery(headerInvoicing.GetStoreProcedure(), headerInvoicing.GetValues())
	if err != nil {
		return nil, fmt.Errorf("[chile.order.FE.go]Error al ejectuar el sp [facturacion].[Cabecera_Facturacion_Electronica_Chile] %v", err)
	}
	defer rowsHeaderInvoicing.Close()
	for rowsHeaderInvoicing.Next() {
		var fecha *time.Time
		err = rowsHeaderInvoicing.Scan(
			&dataHeaderInvoicing.Funciona,
			&dataHeaderInvoicing.IDUserPos,
			&dataHeaderInvoicing.UrlAcepta,
			&dataHeaderInvoicing.UrlInterno,
			&dataHeaderInvoicing.GiroNegocio,
			&dataHeaderInvoicing.CorreoElectronicoEmisor,
			&dataHeaderInvoicing.AsuntoCorreoElectronico,
			&dataHeaderInvoicing.CodigoSll,
			&dataHeaderInvoicing.Ruc,
			&dataHeaderInvoicing.RazonSocial,
			&dataHeaderInvoicing.Direccion,
			&dataHeaderInvoicing.Ciudad,
			&dataHeaderInvoicing.IdentificacionComprador,
			&dataHeaderInvoicing.ClienteApellidos,
			&dataHeaderInvoicing.ClienteNombres,
			&dataHeaderInvoicing.ClienteDireccion,
			&dataHeaderInvoicing.ClienteTelefono,
			&dataHeaderInvoicing.ClienteEmail,
			&dataHeaderInvoicing.ClienteCiudad,
			&dataHeaderInvoicing.IdFactura,
			&dataHeaderInvoicing.Secuencial,
			&fecha,
			&dataHeaderInvoicing.TotalSinImpuestos,
			&dataHeaderInvoicing.TotalDescuento,
			&dataHeaderInvoicing.BaseImponible,
			&dataHeaderInvoicing.ValorIva,
			&dataHeaderInvoicing.ImporteTotal,
			&dataHeaderInvoicing.MontoExcento,
			&dataHeaderInvoicing.IdTipoDocumento,
			&dataHeaderInvoicing.NombreTipoDocumento,
			&dataHeaderInvoicing.EstadoFacturacionElectronica,
			&dataHeaderInvoicing.FolioNumFacturacionElectronica,
			&dataHeaderInvoicing.MensajeFacturacionElectronica,
			&dataHeaderInvoicing.CodigoTienda,
			&dataHeaderInvoicing.CodigoTiendaDescipcion,
			&dataHeaderInvoicing.EncargadoTienda,
		)
		if err != nil {
			return nil, fmt.Errorf("[chile.order.FE.go]Error al obtener los datos del sp [facturacion].[Cabecera_Facturacion_Electronica_Chile] %v", err)
		}
		strDate := fecha.Format("2006-01-02")
		dataHeaderInvoicing.FechaEmision = &strDate
	}

	//Detalle de facturacion electronica
	detailsInvoicing := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[Detalle_Facturacion_Electronica_Chile]")
	detailsInvoicing.AddValueParameterized("cfac_id", cfacId)
	rowsDetailInvoicing, err := connection.IQuery(detailsInvoicing.GetStoreProcedure(), detailsInvoicing.GetValues())
	if err != nil {
		return nil, fmt.Errorf("[chile.order.FE.go]Error al ejectuar el sp [facturacion].[Detalle_Facturacion_Electronica_Chile] %v", err)
	}
	defer rowsDetailInvoicing.Close()
	for rowsDetailInvoicing.Next() {
		detailInvoicing := &models.DetailsFolder{}
		err = rowsDetailInvoicing.Scan(
			&detailInvoicing.CodigoPrincipal,
			&detailInvoicing.CodigoAuxiliar,
			&detailInvoicing.Descripcion,
			&detailInvoicing.Cantidad,
			&detailInvoicing.Descuento,
			&detailInvoicing.MontoItem,
			&detailInvoicing.ImpuestoCodigo,
			&detailInvoicing.ImpuestoCodigoPorcentaje,
			&detailInvoicing.ImpuestoTarifa,
			&detailInvoicing.ImpuestoBaseImponible,
			&detailInvoicing.ImpuestoValor,
			&detailInvoicing.PrcItem,
		)
		if err != nil {
			return nil, fmt.Errorf("[chile.order.FE.go]Error al obtener los datos del sp [facturacion].[Detalle_Facturacion_Electronica_Chile] %v", err)
		}
		dataDetailsInvoicing = append(dataDetailsInvoicing, detailInvoicing)
	}
	//Mapeo de la informacion para el envio de informacion del FE
	if utils.IsEmpty(dataHeaderInvoicing.UrlAcepta) || utils.IsEmpty(dataHeaderInvoicing.UrlInterno) {
		return nil, errors.New("[chile.order.FE.go]Error, faltan datos para facturacion electronica")
	}
	jsonInvoicing, err := models.NewJsonFactura(dataHeaderInvoicing, dataDetailsInvoicing)
	if err != nil {
		return nil, fmt.Errorf("[chile.order.FE.go]Error, %v", err.Error())
	}
	/*requestFolio := &lib_gen_proto_folio.RequestFolio_FolioChile{
		FolioChile: &lib_gen_proto_folio.RequestFeChile{
			UrlServidorAcepta:  *dataHeaderInvoicing.UrlAcepta,
			UrlServidorInterno: *dataHeaderInvoicing.UrlInterno,
			JsonFactura:        jsonInvoicing,
		},
	}9*/
	requestFolio := &lib_gen_proto_folio.RequestFolio{
		Request: &lib_gen_proto_folio.RequestFolio_FolioChile{
			FolioChile: &lib_gen_proto_folio.RequestFeChile{
				UrlServidorAcepta:  *dataHeaderInvoicing.UrlAcepta,
				UrlServidorInterno: *dataHeaderInvoicing.UrlInterno,
				JsonFactura:        jsonInvoicing,
			},
		},
	}
	return requestFolio, nil
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
func (o *OrderStore) SendOrderFolio(dataFe *lib_gen_proto_folio.RequestFolio) {
	msg := events2.NewEvent("order.request.foliador", dataFe)
	err := o.NatsFolder.EventSender.Execute(msg)
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
func (o *OrderStore) WsPrintService(connection *sqlserver.DatabaseSql, cfacId, IdOrderHeaderOrder, idUserPos string, dataOrderPrintFastFood []*models.KioskoImpresionOrdenPedidoFastFood, dataStation *models.StationKiosco) error {
	apiService := printservice.NewApiPrintService(connection, o.StoreData.RestaurantId)
	var resultPrint *string
	rstId := o.StoreData.RestaurantId
	query := fmt.Sprintf(`SELECT servicio_impresion FROM [config].[fn_ColeccionRestaurante_ServicioImpresion] (@idRestaurante, @idCadena, @idEstacion)`)
	rows, err := connection.Query(query, sql.Named("idRestaurante", rstId),
		sql.Named("idCadena", o.StoreData.ChainId),
		sql.Named("idEstacion", dataStation.IdStation))
	if err != nil {
		return fmt.Errorf("[chile.order.go]Error al ejecutar la funcion %v: %v", query, err)
	}
	defer rows.Close()
	for rows.Next() {
		err = rows.Scan(&resultPrint)
		if err != nil {
			return fmt.Errorf("[chile.order.go]Error al obtener los datos de la funcion %v: %v", query, err)
		}
	}
	if resultPrint == nil {
		return fmt.Errorf("[chile.order.go]error al obtner los datos de la impresora, por favor revisar")
	}
	var printingServiceResponse models.PrintingServiceResponse
	err = json.Unmarshal([]byte(*resultPrint), &printingServiceResponse)
	if err != nil {
		return fmt.Errorf("[chile.order.go]Error al parsear los datos de %v: %v", resultPrint, err)
	}
	if strings.EqualFold("efectivo", o.Order.Cabecera.PaymentType) {

		printOrder := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[pedido].[ORD_impresion_ordenpedido]")
		printOrder.AddValueParameterized("IDCabeceraOrdenPedido", IdOrderHeaderOrder)
		printOrder.AddValueParameterized("IDUsersPos", idUserPos)
		printOrder.AddValueParameterized("rst_id", rstId)

		if printingServiceResponse.AplicaEstacion != 1 && printingServiceResponse.AplicaTienda != 1 {
			printOrder.AddValueParameterized("dop_cuenta", 1)
			printOrder.AddValueParameterized("guardaOrden", 0)
			printOrder.AddValueParameterized("todas", 0)
			_, err = connection.IQuery(printOrder.GetStoreProcedure(), printOrder.GetValues())
			if err != nil {
				return fmt.Errorf("[chile.order.go]Error al ejecutar el sp [pedido].[ORD_impresion_ordenpedido]: %v", err)
			}
			return nil
		}
		printOrder.AddValueParameterized("dop_cuenta", 1)
		printOrder.AddValueParameterized("guardaOrden", 1)
		printOrder.AddValueParameterized("todas", 0)

		rowOrderPrint, err := connection.IQuery(printOrder.GetStoreProcedure(), printOrder.GetValues())
		dataOrderPrintFastFood = make([]*models.KioskoImpresionOrdenPedidoFastFood, 0)
		defer rowOrderPrint.Close()
		column, _ := rowOrderPrint.Columns()
		for rowOrderPrint.Next() {
			var dataOrderPrint models.KioskoImpresionOrdenPedidoFastFood
			if len(column) == 1 {
				err = rowOrderPrint.Scan(&dataOrderPrint.Confirmar)
			} else if len(column) == 7 {
				err = rowOrderPrint.Scan(&dataOrderPrint.NumeroImpresiones,
					&dataOrderPrint.Tipo,
					&dataOrderPrint.IdMarca,
					&dataOrderPrint.Impresora,
					&dataOrderPrint.FormatoXML,
					&dataOrderPrint.JsonData,
					&dataOrderPrint.JsonRegistros)

			} else {
				err = rowOrderPrint.Scan(&dataOrderPrint.NumeroImpresiones,
					&dataOrderPrint.Tipo,
					&dataOrderPrint.Impresora,
					&dataOrderPrint.FormatoXML,
					&dataOrderPrint.JsonData,
					&dataOrderPrint.JsonRegistros)
			}

			if err != nil {
				return fmt.Errorf("[chile.order.go]Error al obtener los datos para la impresion de la ordern de pedido :%v", err)
			}
			dataOrderPrintFastFood = append(dataOrderPrintFastFood, &dataOrderPrint)
		}

	}
	if len(dataOrderPrintFastFood) > 0 {
		err = apiService.ApiPrint(dataOrderPrintFastFood, cfacId, IdOrderHeaderOrder, idUserPos, *dataStation.IdStation)
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al imprimir la orden del pedido: %v", err)
		}
	}
	return nil
}

func getTypeAccount(processor, typeAccount string) string {

	switch processor {
	case "EDENRED", "SODEXO":
		return processor
	}

	return typeAccount
}
