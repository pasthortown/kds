package argentina

import (
	"database/sql"
	"errors"
	"fmt"
	"lib-shared/protos/lib_gen_proto"
	"lib-shared/utils"
	"lib-shared/utils/logger"
	"new-order-store/internals/domain/business"
	"new-order-store/internals/domain/execute"
	featureflag2 "new-order-store/internals/entity/enums/featureflag"
	"new-order-store/internals/entity/maxpoint"
	"new-order-store/internals/entity/maxpoint/credential"
	"new-order-store/internals/entity/models"
	"new-order-store/internals/infrastructure/cache"
	"new-order-store/internals/infrastructure/featureflag"
	"new-order-store/internals/infrastructure/grpc_foliador"
	"new-order-store/internals/infrastructure/natsmodule"
	"new-order-store/internals/infrastructure/natsmodule/events"
	"new-order-store/internals/infrastructure/natsmodulefolder"
	"new-order-store/internals/infrastructure/sqlserver"
	"new-order-store/internals/utils/validatorsql"
	"runtime/debug"
	"strconv"
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
	NatsFolder         *natsmodulefolder.NatsStarter
	FoliadorService    *grpc_foliador.GrpcServiceFolio
	regionalKiosk      execute.RegionalKioskExecute
}

func NewOrderStore(

	DatabaseCredential *credential.DatabaseCredential,
	Order *lib_gen_proto.Order,
	StoreData *maxpoint.StoreData,
	Feature *featureflag.FeatureFlag,
	NatsClient *natsmodule.NatsStarter,
	NatsFolder *natsmodulefolder.NatsStarter,
	FoliadorService *grpc_foliador.GrpcServiceFolio,
	regionalKiosk execute.RegionalKioskExecute,
) execute.OrderExecutorSql {
	return &OrderStore{
		DatabaseCredential: DatabaseCredential,
		Order:              Order,
		StoreData:          StoreData,
		Feature:            Feature,
		NatsClient:         NatsClient,
		NatsFolder:         NatsFolder,
		FoliadorService:    FoliadorService,
		regionalKiosk:      regionalKiosk,
	}
}

func (o *OrderStore) Execute() error {
	//TODO implement me
	if strings.EqualFold(o.Order.Cabecera.PaymentType, "efectivo") {
		return o.CreateOrderKioskEfectivo()
	}
	return o.CreateOrderKioskTarjeta()
}

func (o *OrderStore) CreateOrderKioskEfectivo() (err error) {
	defer func() {
		if r := recover(); r != nil {
			stackTrace := string(debug.Stack())
			// Convertir el panic y el stack trace en un error detallado
			err = fmt.Errorf("se produjo un panic: %v\nStack trace:\n%s", r, stackTrace)
		}
	}()
	//variables
	orderId := o.Order.Cabecera.OrderId
	var cfacId *string
	documentId := o.Order.Cabecera.Client.DocumentId

	connection, err := sqlserver.NewConnectionSql(o.DatabaseCredential)
	if err != nil {
		return err
	}
	defer func() {
		connection.Close()
	}()
	//Inicio de proceso
	//o.Order.Items.Product = o.AdjustModifier(o.Order.Items.Product)
	err = o.ValidateProduct(connection)
	if err != nil {
		return err
	}
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
		return fmt.Errorf("[argentina.order.go]Error al ejecutar el sp facturacion.KIOSKO_generarSecuencialFactura: %v", err)
	}
	defer rows.Close()
	for rows.Next() {
		err = rows.Scan(&cfacId)
		if err != nil {
			return fmt.Errorf("[argentina.order.go]Error al obtener los datos de KIOSKO_generarSecuencialFactura: %v", err)
		}
	}
	if cfacId == nil {
		return fmt.Errorf("[argentina.order.go]Error - El cfacId generado del sp [facturacion].[KIOSKO_generarSecuencialFactura] esta vacio, por favor revisar")
	}
	typeDocument := o.StoreData.GetTipoDocumento(documentId)
	if typeDocument == nil {
		return fmt.Errorf("[argentina.order.go]No se encontro el tipo de documento para el id %s", documentId)
	}
	if err = o.ValidateDataClient(typeDocument); err != nil {
		return err
	}

	err = connection.CreateBegin()
	defer connection.Rollback()
	if err != nil {
		return fmt.Errorf("[argentina.order.go]Error al crear BEGIN: %v", err.Error())
	}
	_, _, err = o.InsertOrderKiosko(connection, cfacId)
	if err != nil {
		return err
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
	//paymentType := o.Order.Cabecera.PaymentType
	orderId := o.Order.Cabecera.OrderId
	statusMxp := o.StoreData.Status
	currentDate := time.Now()
	connection, err := sqlserver.NewConnectionSql(o.DatabaseCredential)
	if err != nil {
		return err
	}
	defer func() {
		connection.Close()
	}()
	//o.Order.Items.Product = o.AdjustModifier(o.Order.Items.Product)
	err = o.ValidateProduct(connection)
	if err != nil {
		return err
	}

	idPeriod, err := o.GetOpenPeriod(connection)
	if err != nil {
		return err
	}
	if idPeriod == nil {
		return fmt.Errorf("[argentina.order.go]No se encontro ningun periodo abierto")
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
		return fmt.Errorf("[argentina.order.go]La estacion de kiosko no se encuentra activa")
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
		return fmt.Errorf("[argentina.order.go]No se encontro el id del usuario para kiosko")
	}
	//Obtencion del menu
	var idMenu *string
	queryMenu := "SELECT CAST(IDMenu AS VARCHAR(40)) FROM menu WHERE menu_Nombre LIKE '%KIOSCO ' + @typeService"
	rowsMenu, err := connection.Query(queryMenu, sql.Named("typeService", o.Order.Cabecera.TypeService))
	if err != nil {
		return errors.New("[argentina.order.go]Error al ejecutar el query: " + err.Error())
	}
	defer rowsMenu.Close()
	for rowsMenu.Next() {
		err = rowsMenu.Scan(&idMenu)
		if err != nil {
			return errors.New("[argentina.order.go]Error al obtener los datos del menu de kiosko: " + err.Error())
		}
	}
	//
	newBin := o.Order.PaymentMethods.Card.NumberCard
	if len(o.Order.PaymentMethods.Card.NumberCard) > 5 {
		newBin = o.Order.PaymentMethods.Card.NumberCard[0:6]
	}

	idFormaPago, cardName, err := o.GetIdPaymentMethod(connection, newBin)
	if err != nil {
		return err
	}
	idClient, document, err := o.GetIdClient(connection)
	if err != nil {
		return err
	}
	//Procesamiento de tarjeta
	err = connection.CreateBegin()
	defer connection.Rollback()
	if err != nil {
		return fmt.Errorf("[argentina.order.go]Error al crear BEGIN: %v", err.Error())
	}
	documentId := o.Order.Cabecera.Client.DocumentId
	typeDocument := o.StoreData.GetTipoDocumento(documentId)
	if typeDocument == nil {
		return fmt.Errorf("[argentina.order.go]No se encontro el tipo de documento para el id %s", documentId)
	}
	if err = o.ValidateDataClient(typeDocument); err != nil {
		return err
	}
	idOrderKiosko, datailsKiosko, err := o.InsertOrderKiosko(connection, nil)
	if err != nil {
		return err
	}
	//
	validateBin := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[USP_verificaBinTarjetaFormaPago]")
	validateBin.AddValueParameterized("bin", o.Order.PaymentMethods.Bin)
	validateBin.AddValueParameterized("rst", o.StoreData.RestaurantId)
	validateBin.AddValueParameterized("user", "")
	validateBin.AddValueParameterized("ip", "")

	rowsBin, err := connection.IQuery(validateBin.GetStoreProcedure(), validateBin.GetValues())
	if err != nil {
		return fmt.Errorf("[argentina.order.go]Error al escutar el sp [facturacion].[USP_verificaBinTarjetaFormaPago] para kiosko: %v", err.Error())
	}
	defer rowsBin.Close()
	for rowsBin.Next() {
		var idFormaPag, confirma, mensaje string
		err = rowsBin.Scan(&idFormaPag, &confirma, &mensaje)
		if err != nil {
			return fmt.Errorf("[argentina.order.go]Error al obtener los datos de bin del sp [facturacion].[USP_verificaBinTarjetaFormaPago] para kiosko: %v", err.Error())
		}
	}

	//Insercion de datos a la tabla CabeceraOrdenPedido
	var idOrderHeaderOrder string
	orderHeaderOrder := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[pedido].[ORD_configuracion_proceso_ordenpedido_Newkiosko]")
	orderHeaderOrder.AddValueParameterized("rst_id", o.StoreData.RestaurantId)
	orderHeaderOrder.AddValueParameterized("IDMesa", idMesa)
	orderHeaderOrder.AddValueParameterized("IDUsersPos", idUserPos)
	orderHeaderOrder.AddValueParameterized("IDEstacion", resultStation.IdStation)
	orderHeaderOrder.AddValueParameterized("num_Pers", 1)
	orderHeaderOrder.AddValueParameterized("idPedido", idOrderKiosko)
	orderHeaderOrder.AddValueParameterized("idOrdenPedido", sql.Out{Dest: &idOrderHeaderOrder})
	_, err = connection.IQuery(orderHeaderOrder.GetStoreProcedure(), orderHeaderOrder.GetValues())
	if err != nil {
		return fmt.Errorf("[argentina.order.go]Error al ejecutar el sp [pedido].[ORD_configuracion_proceso_ordenpedido_Newkiosko]: %v", err)
	}
	if utils.IsEmpty(idOrderHeaderOrder) {
		return fmt.Errorf("[argentina.order.go]Error al obtener el id de la tabla cabecera_orden_pedido")
	}

	//actualiza la tabla Cabecera_Orden_Pedido con el id del menu
	updateOrderHeaderOrder := fmt.Sprintf("update Cabecera_Orden_Pedido set Cabecera_Orden_PedidoVarchar6 = @menu where IDCabeceraOrdenPedido = @idCabeceraOrdenPedido")
	_, err = connection.Exec(
		updateOrderHeaderOrder,
		sql.Named("menu", idMenu),
		sql.Named("idCabeceraOrdenPedido", idOrderHeaderOrder),
	)
	if err != nil {
		return fmt.Errorf("[argentina.order.go]No se pudo ejecutar el UPDATE %s con idCabeceraOrdenPedido:%s", updateOrderHeaderOrder, idOrderHeaderOrder)
	}

	//insercion de los productos a la tabla detalle_orden_pedido
	err = o.InsertDetailOrder(connection, datailsKiosko, idOrderHeaderOrder)
	if err != nil {
		return err
	}

	//Insercion a las tablas de facturacion
	var cfacId *string
	spInsertKisoKInvoice := fmt.Sprintf(`
		DECLARE @factura TABLE (
			cdn_id INT,
			cdn_tipoimpuesto VARCHAR(25),
			rst_descripcion VARCHAR(100), 
			rst_tipo_servicio		VARCHAR(40), 
			cfac_id VARCHAR(40), 
			std_id VARCHAR(40), 
			usr_id VARCHAR(40), 
			est_id VARCHAR(40), 
			cfac_fechacreacion		DATETIME, 
			plu_id INT, 
			dtfac_cantidad FLOAT, 
			dtfac_precio_unitario	FLOAT, 
			dtfac_iva 	FLOAT, 
			dtfac_total 	FLOAT, 
			plu_descripcion VARCHAR(200), 
			plu_impuesto INT, 
			totalizado 	FLOAT, 
			servicio 	FLOAT, 
			cfac_descuento_empresa	FLOAT, 
			cod_Factura 	VARCHAR(40), 
			btn_cancel_pago INT, 
			desc_producto FLOAT, 
			valorFijo 	FLOAT, 
			porcentaje 	FLOAT, 
			canje_puntos FLOAT
		);
		INSERT INTO @factura
			EXEC facturacion.IAE_Fac_InsertFactura_kiosko @IDRestaurante = @p1 ,@IDCabeceraOrdenPedido = @p2 ,@IDUsersPos = @p3 ,@numeroCuenta = 1 ,@IDEstacion = @p4 ,@IDPeriodo = @p5 ,@IDControlEstacion = @p6; 
		SELECT TOP (1) CAST(cfac_id AS VARCHAR(40)) FROM @factura;`)
	rowsInvoice, err := connection.Query(spInsertKisoKInvoice, sql.Named("p1", o.StoreData.RestaurantId), sql.Named("p2", idOrderHeaderOrder), sql.Named("p3", idUserPos), sql.Named("p4", resultStation.IdStation), sql.Named("p5", idPeriod), sql.Named("p6", idControlStation))
	if err != nil {
		return fmt.Errorf("[argentina.order.go]Error al ejecutar el sp [pedido].[ORD_configuracion_proceso_ordenpedido]: %v", err)
	}

	defer rowsInvoice.Close()
	for rowsInvoice.Next() {
		err = rowsInvoice.Scan(&cfacId)
		if utils.IsEmpty(cfacId) {
			return fmt.Errorf("[argentina.order.go]Error al obtener el cfacId para el pedido %v", orderId)
		}
	}

	//Actualizo la tabla kiosko_cabecera_pedidos con el cfacId
	updateKioskoHeader := fmt.Sprintf(`UPDATE dbo.kiosko_cabecera_pedidos
				SET cfac_id = @codFactura
				WHERE id = @idPedido`)
	rowsKioskoHeader, err := connection.Exec(updateKioskoHeader, sql.Named("codFactura", cfacId), sql.Named("idPedido", idOrderKiosko))
	if err != nil {
		return fmt.Errorf("[argentina.order.go]Error al actualizar los datos de la tabla kiosko_cabecera_pedidos: %v", err)
	}
	affectedKioskoHeader, err := rowsKioskoHeader.RowsAffected()
	if err != nil {
		return err
	}
	if affectedKioskoHeader < 1 {
		return fmt.Errorf("[argentina.order.go]No se ha podio actualizar los datos de la tabla kiosko_cabecera_pedidos")
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
		return fmt.Errorf("[argentina.order.go]Error al actualizar los datos de la tabla cabecera_orden_pedido: %v", err)
	}
	affectedOrderHeader, err := rowsOrderHeader.RowsAffected()
	if err != nil {
		return err
	}
	if affectedOrderHeader < 1 {
		return fmt.Errorf("[argentina.order.go]No se ha podio actualizar los datos de la tabla cabecera_orden_pedido")
	}

	//Obtengo el total de la factura
	var cfacTotal string
	query := "SELECT cfac_total FROM dbo.Cabecera_Factura WHERE cfac_id = @cfacId"
	rows, err := connection.Query(query, sql.Named("cfacId", cfacId))
	if err != nil {
		return fmt.Errorf("[argentina.order.go]Error al ejectar el query %v: %v", query, err)
	}
	defer rows.Close()
	for rows.Next() {
		err = rows.Scan(&cfacTotal)
		if err != nil {
			return fmt.Errorf("[argentina.order.go]Error al obtener el valor total de la factura pa el pedido %v: %v", orderId, err)
		}
	}
	typeSwt := statusMxp.IdPinpadUnired
	if strings.EqualFold(o.Order.PaymentMethods.Card.AID, "QR") {
		typeSwt = -1
	}

	//Insertar forma de pago
	insertPaymentMethod := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "facturacion.fac_insertaFormaPago_kiosko")
	insertPaymentMethod.AddValueParameterized("cfac", cfacId)
	insertPaymentMethod.AddValueParameterized("fmp_id", "")
	insertPaymentMethod.AddValueParameterized("num", newBin)
	insertPaymentMethod.AddValueParameterized("valor", o.Order.PaymentMethods.TotalBill)
	insertPaymentMethod.AddValueParameterized("total", cfacTotal)
	insertPaymentMethod.AddValueParameterized("prop", 0)
	insertPaymentMethod.AddValueParameterized("swt", typeSwt)
	insertPaymentMethod.AddValueParameterized("usr_id", idUserPos)
	insertPaymentMethod.AddValueParameterized("autorizacion", o.Order.PaymentMethods.Card.Authorization)
	insertPaymentMethod.AddValueParameterized("forma", o.Order.PaymentMethods.Card.AID)
	_, err = connection.IQuery(insertPaymentMethod.GetStoreProcedure(), insertPaymentMethod.GetValues())
	if err != nil {
		return fmt.Errorf("[argentina.order.go]Error al ejecutar el sp facturacion.fac_insertaFormaPago_kiosko: %v", err)
	}
	//Actualizacion o Insercion de un cliente en la base de datos
	dataTypeDocument := o.StoreData.GetTipoDocumento(o.Order.Cabecera.Client.DocumentId)
	if !strings.EqualFold(dataTypeDocument.Description, "CONSUMIDOR FINAL") {
		if idClient != nil {
			_, err = o.InsertOrUpdateClient(connection, "U", dataTypeDocument.Description, *idUserPos)
			if err != nil {
				return err
			}
		} else {
			idClient, err = o.InsertOrUpdateClient(connection, "I", dataTypeDocument.Description, *idUserPos)
			if err != nil {
				return err
			}
			document = &o.Order.Cabecera.Client.DocumentNumber
		}
	}
	//Actualiza los datos de la factura del cliente
	invoicingClient := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[USP_FacturaCliente_ar]")
	invoicingClient.AddValueParameterized("idCliente", document)
	invoicingClient.AddValueParameterized("IDFactura", cfacId)
	invoicingClient.AddValueParameterized("IDUserpos", idUserPos)
	_, err = connection.IQuery(invoicingClient.GetStoreProcedure(), invoicingClient.GetValues())
	if err != nil {
		return fmt.Errorf("[argentina.order.go]Error al ejecutar el sp [facturacion].[USP_FacturaCliente_ar]: %v", err)
	}

	//Insercion a la tabla SWT_Requerimiento_Autorizacion
	authorizationRequirement := sqlserver.NewSimpleInsertBuilderSQL(false, "dbo.SWT_Requerimiento_Autorizacion")
	authorizationRequirement.AddValue("rqaut_fecha", currentDate)
	authorizationRequirement.AddValue("rqaut_ip", o.Order.Cabecera.IpKiosk)
	authorizationRequirement.AddValue("rqaut_puerto", "8080")
	authorizationRequirement.AddValue("rqaut_trama", cfacId)
	authorizationRequirement.AddValue("rqaut_movimiento", cfacId)
	authorizationRequirement.AddValue("tpenv_id", typeSwt)
	authorizationRequirement.AddValue("IDFormapagoFactura", idFormaPago)
	authorizationRequirement.AddValue("IDEstacion", resultStation.IdStation)
	authorizationRequirement.AddValue("IDUsersPos", idUserPos)
	authorizationRequirement.AddValue("IDStatus", statusMxp.SesionesActivo)
	authorizationRequirement.AddValue("replica", 0)
	authorizationRequirement.AddValue("nivel", 0)
	_, err = connection.IQuery(authorizationRequirement.SqlGenerated(), authorizationRequirement.GetValues())
	if err != nil {
		return fmt.Errorf("[argentina.order.go]Error al insertar los datos en la tabla dbo.SWT_Requerimiento_Autorizacion: %v", err)
	}
	//Insercion a la tabla SWT_Respuesta_Autorizacion
	subCardHolder := o.Order.PaymentMethods.Card.CardHolder
	if len(o.Order.PaymentMethods.Card.CardHolder) > 17 {
		subCardHolder = o.Order.PaymentMethods.Card.CardHolder[0:18]
	}

	subNumTerminal := o.Order.PaymentMethods.Card.TID
	if len(o.Order.PaymentMethods.Card.TID) > 8 {
		subNumTerminal = o.Order.PaymentMethods.Card.TID[0:8]
	}

	fecha := currentDate.Format("20060102")
	hora := currentDate.Format("150405")
	responseAuthorization := sqlserver.NewSimpleInsertBuilderSQL(true, "dbo.SWT_Respuesta_Autorizacion")
	responseAuthorization.AddValue("rsaut_trama", cfacId)
	responseAuthorization.AddValue("rsaut_fecha", currentDate)
	responseAuthorization.AddValue("ttra_codigo", "01")
	responseAuthorization.AddValue("cres_codigo", o.Order.PaymentMethods.Card.ResultCode)
	responseAuthorization.AddValue("rsaut_respuesta", o.Order.PaymentMethods.Card.MessageResponseAut)
	responseAuthorization.AddValue("rsaut_secuencial_transaccion", o.Order.PaymentMethods.Card.ResultCode)
	responseAuthorization.AddValue("rsaut_hora_autorizacion", hora)
	responseAuthorization.AddValue("rsaut_fecha_autorizacion", fecha)
	responseAuthorization.AddValue("rsaut_numero_autorizacion", o.Order.PaymentMethods.Card.Authorization)
	responseAuthorization.AddValue("rsaut_terminal_id", subNumTerminal)
	responseAuthorization.AddValue("rsaut_grupo_tarjeta", cardName)
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
		return fmt.Errorf("[argentina.order.go]Error al insertar los datos en la tabla dbo.SWT_Respuesta_Autorizacion: %v", err)
	}
	if rowsResponseAuthorization.Err() != nil {
		return fmt.Errorf("[argentina.order.go]Error al iterar el rows de la tabla dbo.SWT_Respuesta_Autorizacion: %v", rowsResponseAuthorization.Err())
	}
	var rsAutId int32
	err = rowsResponseAuthorization.Scan(&rsAutId)
	if err != nil {
		return fmt.Errorf("[argentina.order.go]Error al obtener el identificador unico de la tabla dbo.SWT_Respuesta_Autorizacion: %v", err.Error())
	}

	var jsonInvoicing, jsonVoucher *string
	//Genereacion del Json de factura para impresion
	dynamicInvoicePrinting := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[dbo].[kiosko_USP_impresiondinamica_factura_kioskoCamposJson]")
	dynamicInvoicePrinting.AddValueParameterized("cfac_id", cfacId)
	dynamicInvoicePrinting.AddValueParameterized("tipo_comprobante", "F")
	rowInvoicePrinting, err := connection.IQuery(dynamicInvoicePrinting.GetStoreProcedure(), dynamicInvoicePrinting.GetValues())
	if err != nil {
		return fmt.Errorf("[argentina.order.go]Error al ejecutar el sp [dbo].[kiosko_USP_impresiondinamica_factura_kioskoCamposJson]: %v", err)
	}
	defer rowInvoicePrinting.Close()
	for rowInvoicePrinting.Next() {
		err = rowInvoicePrinting.Scan(&jsonInvoicing)
		if err != nil {
			return fmt.Errorf("[argentina.order.go]Error al obtener el json de impresion de la factura del sp [dbo].[kiosko_USP_impresiondinamica_factura_kioskoCamposJson]: %v", err)
		}
	}
	if jsonInvoicing == nil {
		return fmt.Errorf("[argentina.order.go]Error, no se genero el json de impresion de factura")
	}

	//Genereacion del Json de voucher para impresion
	dynamicVoucherPrint := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[VOUCHER_USP_ImpresionDinamicaClienteKioskoCamposV2]")
	dynamicVoucherPrint.AddValueParameterized("rsaut_id", rsAutId)
	dynamicVoucherPrint.AddValueParameterized("usuario", resultStation.CashierName)
	dynamicVoucherPrint.AddValueParameterized("tipo", "CL")
	rowVoucherPrint, err := connection.IQuery(dynamicVoucherPrint.GetStoreProcedure(), dynamicVoucherPrint.GetValues())
	if err != nil {
		return fmt.Errorf("[argentina.order.go]Error al ejecutar el sp [facturacion].[VOUCHER_USP_ImpresionDinamicaClienteKioskoCamposV2]: %v", err)
	}
	defer rowVoucherPrint.Close()
	for rowVoucherPrint.Next() {
		err = rowVoucherPrint.Scan(&jsonVoucher)
		if err != nil {
			return fmt.Errorf("[argentina.order.go]Error al obtener el json de impresion del voucher del sp [facturacion].[VOUCHER_USP_ImpresionDinamicaClienteKioskoCamposV2]: %v", err)
		}
	}

	if jsonVoucher == nil {
		return fmt.Errorf("[argentina.order.go]Error - no se genero el json del voucher para el cliente")
	}

	//Impresión de orden de pedido en impresora de línea
	orderPrintFastFood := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[pedido].[IAE_ImpresionOrdenPedidoFastFoodKiosko]")
	orderPrintFastFood.AddValueParameterized("idCabeceraOrdenPedido", idOrderHeaderOrder)
	orderPrintFastFood.AddValueParameterized("idCadena", o.StoreData.ChainId)
	orderPrintFastFood.AddValueParameterized("idRestaurante", o.StoreData.RestaurantId)
	_, err = connection.IQuery(orderPrintFastFood.GetStoreProcedure(), orderPrintFastFood.GetValues())
	if err != nil {
		return fmt.Errorf("[argentina.order.go]Error al ejecutar el sp [pedido].[IAE_ImpresionOrdenPedidoFastFoodKiosko]: %v", err)
	}

	//Envio de dato al foleo de Argentina
	newJsonInvoicing, err := o.Folio(connection, *cfacId, *resultStation.CashierName, *jsonInvoicing)
	if err != nil {
		logger.Error.Printf(err.Error())
		return err
	}
	errCommit := connection.Commit()
	if errCommit != nil {
		logger.Error.Printf("[argentina.order.go]Error al realizar el commit: %v", errCommit.Error())
	}
	//Envio de respuesta al core por nats
	order := &lib_gen_proto.ResultStoreValidationData{}
	order.OrderId = orderId
	order.IdOrdenPedido = idOrderHeaderOrder
	order.CfacId = *cfacId
	order.Factura = *jsonInvoicing
	if newJsonInvoicing != nil {
		order.Factura = *newJsonInvoicing
	}
	order.Voucher = *jsonVoucher

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

	return nil
}
func (o *OrderStore) AdjustModifier(products []*lib_gen_proto.Product) []*lib_gen_proto.Product {
	for _, product := range products {
		if product.ModifierGroups != nil {
			if len(product.ModifierGroups) > 0 {
				for _, modifier := range product.ModifierGroups {
					modifier.Quantity = product.Quantity * modifier.Quantity
				}
			}
		}
	}
	return products
}
func (o *OrderStore) ValidateProduct(connection *sqlserver.DatabaseSql) error {
	for _, details := range o.Order.Items.Product {
		productExist := false
		productPluId := fmt.Sprintf(`select  plu_id, pr_pvp from precio_plu where IDCategoria in (select rst_categoria from restaurante) and plu_id= @productId`)

		rows, err := connection.Query(productPluId, sql.Named("productId", details.ProductId))
		if err != nil {
			return fmt.Errorf("[argentina.order.go]Error al ejecutar el query %s: %s", productPluId, err.Error())
		}

		for rows.Next() {
			productExist = true
			var pluId, pvp *int
			if err = rows.Scan(&pluId, &pvp); err != nil {
				return fmt.Errorf("[argentina.order.go]Error al obtener los datos del query %v: %s", productPluId, err)
			}
			if utils.IsEmpty(pluId) {
				return fmt.Errorf("[argentina.order.go]El producto %v seleccionado no existe en el local. Por favor acércate a la caja para realizar tu pedido. ¡Te pedimos disculpas por el inconveniente! ", details.NameProduct)
			}

			totalPrice := int(details.TotalPrice)
			if utils.IsEmpty(pvp) || utils.IsEmpty(totalPrice) || *pvp != totalPrice {
				return fmt.Errorf("[argentina.order.go]Error de precios: El precio del producto %v(%v)  no coincide con el precio registrado en el sistema. ", details.NameProduct, details.ProductId)
			}
		}
		if !productExist {
			return fmt.Errorf("[argentina.order.go]El producto %v(%v) seleccionado no existe en el local. Por favor acércate a la caja para realizar tu pedido. ¡Te pedimos disculpas por el inconveniente! ", details.NameProduct, details.ProductId)

		}
		defer rows.Close()
		for _, modifiers := range details.ModifierGroups {
			modifierExist := false
			modifierPluId := modifiers.ProductId

			rows2, err := connection.Query(productPluId, sql.Named("productId", modifierPluId))
			if err != nil {
				return fmt.Errorf("[argentina.order.go]Error al ejecutar el query %s: %s", productPluId, err.Error())
			}
			defer rows2.Close()
			for rows2.Next() {
				modifierExist = true
				var pluId, pvp *int
				if err = rows2.Scan(&pluId, &pvp); err != nil {
					return fmt.Errorf("[argentina.order.go]Error al obtener los datos del query %v: %s", productPluId, err)
				}
				if utils.IsEmpty(pluId) {
					return fmt.Errorf("[argentina.order.go]El producto %v seleccionado no existe en el local. Por favor acércate a la caja para realizar tu pedido. ¡Te pedimos disculpas por el inconveniente! ", modifierPluId)
				}
				totalPrice := int(modifiers.TotalPrice)
				if utils.IsEmpty(pvp) || utils.IsEmpty(totalPrice) || *pvp != totalPrice {
					return fmt.Errorf("[ecuador.order.go]Error de precios: El precio del producto %v(%v)  no coincide con el precio registrado en el sistema. ", modifiers.NameProduct, modifierPluId)
				}
			}
			if !modifierExist {
				return fmt.Errorf("[argentina.order.go]El producto %v(%v) seleccionado no existe en el local. Por favor acércate a la caja para realizar tu pedido. ¡Te pedimos disculpas por el inconveniente! ", modifiers.NameProduct, modifierPluId)
			}
		}
	}

	return nil
}
func (o *OrderStore) ValidateDataClient(typeDocument *maxpoint.DocumentType) error {
	total, err := strconv.Atoi(o.Order.PaymentInfo.Total)
	if err != nil {
		return fmt.Errorf("[argentina.order.go]Error, error al convertir el total string a int ")
	}
	if strings.EqualFold(typeDocument.Description, "CONSUMIDOR FINAL") && total > o.StoreData.BaseFactura {
		return fmt.Errorf("[argentina.order.go]Error: El monto de la factura supera el límite permitido. Se requiere incluir los datos del cliente")
	} else if strings.EqualFold(typeDocument.Description, "CONSUMIDOR FINAL") && total <= o.StoreData.BaseFactura {
		o.Order.Cabecera.Client.Email = "consumidor.final@kfc.com.ec"
		o.Order.Cabecera.Client.Address = "Quito"
		o.Order.Cabecera.Client.DocumentNumber = "9999999999"
		o.Order.Cabecera.Client.Phone = "2222222"
	} else {
		if utils.Between(o.Order.Cabecera.Client.Name, 3, 50) {
			return fmt.Errorf("[argentina.order.go]Error, el nombre del cliente no cumple con el formato especifico de min 3 - max 50 ")
		}
		if utils.StringRegex(o.Order.Cabecera.Client.Name, `^[\pL\s\-]+$`) {
			return fmt.Errorf("[argentina.order.go]Error, el nombre del cliente no cumple con el formato especifico")
		}
		if utils.StringRegex(o.Order.Cabecera.Client.Email, `^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$`) {
			return fmt.Errorf("[argentina.order.go]Error, el correo del cliente no cumple con el formato especifico")
		}
		o.Order.Cabecera.Client.Email = strings.ReplaceAll(o.Order.Cabecera.Client.Email, " ", "")
		if o.Order.Cabecera.Client.Address == "" {
			return fmt.Errorf("[argentina.order.go]Error, el campo cli_direccion no puede estar vacío")
		}
		o.Order.Cabecera.Client.Address = utils.CleanAddress(o.Order.Cabecera.Client.Address)
	}
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
		return -1, nil, fmt.Errorf("[argentina.order.go]Error al insertar los datos en la tabla dbo.kiosko_cabecera_pedidos: %v", err)
	}
	if rows.Err() != nil {
		return -1, nil, fmt.Errorf("[argentina.order.go]Error al iterar el rows: %v", rows.Err())
	}
	err = rows.Scan(&idOrder)
	if err != nil {
		return -1, nil, fmt.Errorf("[argentina.order.go]Error al obtener el id de la tabla dbo.kiosko_cabecera_pedidos: %v", err.Error())
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
			return -1, nil, fmt.Errorf("[argentina.order.go]Error al insertar los productos en la tabla dbo.kiosko_detalle_pedidos: %v", err)
		}
		if rowsDetails.Err() != nil {
			return -1, nil, fmt.Errorf("[argentina.order.go]Error al iterar los datos de la tabla dbo.kiosko_detalle_pedidos: %v", rowsDetails.Err())
		}
		var detailsOrderId int32
		err = rowsDetails.Scan(&detailsOrderId)
		if err != nil {
			return -1, nil, fmt.Errorf("[argentina.order.go]Error al obtener el identificador de la tabla dbo.kiosko_detalle_pedidos: %v", err.Error())
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
				return -1, nil, fmt.Errorf("[argentina.order.go]Error al insertar los modificadores en la tabla dbo.kiosko_detalle_pedidos: %v", err)
			}
			var modifiersOrderId int32
			err = rowsModifiers.Scan(&modifiersOrderId)
			if err != nil {
				return -1, nil, fmt.Errorf("[argentina.order.go]Error al obtener el id de la tabla dbo.kiosko_detalle_pedidos: %v", err.Error())
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
		paymentMethodTable.AddValue("codigoResultado", utils.ToString(o.Order.PaymentMethods.Card.ResultCode, ""))
		paymentMethodTable.AddValue("mensajeResultado", utils.ToString(o.Order.PaymentMethods.Card.MessageResponseAut, ""))
		paymentMethodTable.AddValue("TipoMensaje", utils.ToString(o.Order.PaymentMethods.Card.TypeMessage, ""))
		paymentMethodTable.AddValue("CodigoRespuestaAut", utils.ToString(o.Order.PaymentMethods.Card.CodeResponseAut, ""))
		paymentMethodTable.AddValue("NombreRedAdquirente", utils.ToString(o.Order.PaymentMethods.Card.NameRedAcquirer, ""))
		paymentMethodTable.AddValue("HoraTransaccion", utils.ToString(o.Order.PaymentMethods.Card.TransactionTime, ""))
		paymentMethodTable.AddValue("FechaTransaccion", utils.ToString(o.Order.PaymentMethods.Card.TransactionDate, ""))
		paymentMethodTable.AddValue("Publicidad", utils.ToString(o.Order.PaymentMethods.Card.Publicity, ""))
		paymentMethodTable.AddValue("ModoLectura", utils.ToString(o.Order.PaymentMethods.Card.ReadMode, ""))
		paymentMethodTable.AddValue("AID", utils.ToString(o.Order.PaymentMethods.Card.AID, ""))
		paymentMethodTable.AddValue("IdentificacionAplicacion", utils.ToString(o.Order.PaymentMethods.Card.IdentificationApplication, ""))
		paymentMethodTable.AddValue("redAdquiriente", utils.ToString(o.Order.PaymentMethods.Card.RedAcquirer, ""))
		paymentMethodTable.AddValue("NombreGrupoTarjeta", utils.ToString(o.Order.PaymentMethods.Card.NameGroupCard, ""))
		paymentMethodTable.AddValue("json_autorizaciones", utils.ToString(o.Order.PaymentMethods.Card.JsonAuthorization, ""))
		paymentMethodTable.AddValue("created_at", currentDate)
		paymentMethodTable.AddValue("updated_at", currentDate)
		_, err = connection.IQuery(paymentMethodTable.SqlGenerated(), paymentMethodTable.GetValues())
		if err != nil {
			return -1, nil, fmt.Errorf("[argentina.order.go]error al insertar los datos en la tabla dbo.kiosko_autorizaciones_switch: %v", err)
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
		return -1, nil, fmt.Errorf("[argentina.order.go]Error al insertar los datos en la tabla dbo.kiosko_forma_pagos: %v", err)
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
		return nil, fmt.Errorf("[argentina.order.go]Error al ejecutar el query %s: %s", query, err.Error())
	}
	defer rows.Close()
	for rows.Next() {
		var result string
		err = rows.Scan(&result)
		if err != nil {
			return nil, fmt.Errorf("[argentina.order.go]Error al obtener los datos del query %v: %s", query, err)
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
			return nil, fmt.Errorf("[argentina.order.go]Error al ejecutar el query %v: %v", query, err)
		}
		defer rows.Close()
		for rows.Next() {
			err = rows.Scan(&dataStation.IdStation, &dataStation.CashierName)
			if err != nil {
				return nil, fmt.Errorf("[argentina.order.go]Error al obtener los datos de estacion del kiosko: %v", err)
			}
		}
		if dataStation.IdStation == nil && dataStation.CashierName == nil {
			return nil, fmt.Errorf("[argentina.order.go]Error, no se encontro el id y el nombre del cajero de kiosko, por favor validar")
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
		return nil, fmt.Errorf("[argentina.order.go]Error al ejecutar el query %v: %v", query, err)
	}
	defer rows.Close()
	for rows.Next() {
		var idControlStation *string
		err = rows.Scan(&idControlStation)
		if err != nil {
			return nil, fmt.Errorf("[argentina.order.go]Error al obtener los datos del control estacion de kiosko: %s", err)
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
			return "", fmt.Errorf("[argentina.order.go]Error al obtener el idMesa de kiosko %v: %s", query, errScan.Error())
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
		return nil, fmt.Errorf("[argentina.order.go]Error al ejecutar el query %v: %v", query, err)
	}
	defer result.Close()
	for result.Next() {
		var getIdUserPos string
		err = result.Scan(&getIdUserPos)
		if err != nil {
			return nil, fmt.Errorf("[argentina.order.go]Error al obtener el id del Usuario %v: %v", dataStation.CashierName, err)
		}
		return &getIdUserPos, nil
	}
	return nil, nil
}
func (o *OrderStore) CheckIfAlreadyExistOrder(connection *sqlserver.DatabaseSql, codigoApp string) error {
	query := fmt.Sprintf(`SELECT count(*) as cantidad FROM dbo.kiosko_cabecera_pedidos WHERE codigo_app = @codigoApp`)
	result, err := connection.Query(query, sql.Named("codigoApp", codigoApp))
	if err != nil {
		return fmt.Errorf("[argentina.order.go]Error al ejecutar el query %v: %v", query, err)
	}
	defer result.Close()
	for result.Next() {
		var countData int32
		err = result.Scan(&countData)
		if err != nil {
			return fmt.Errorf("[argentina.order.go]Error al validar el codigoApp %v de la tabla kiosko_cabecera_pedidos: %v", codigoApp, err)
		}
		if countData > 0 {
			return fmt.Errorf("[argentina.order.go]El codigo %v ya se encuentra registrado, por favor ingresar uno nuevo", codigoApp)
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
			return fmt.Errorf("[argentina.order.go]Error al ejecutar el query %v: %v", query, err)
		}
		for rows.Next() {
			var dataProduct models.KioskoPlus
			var pluId *int32
			err = rows.Scan(&pluId, &dataProduct.ValorNeto, &dataProduct.ValorIva, &dataProduct.ValorBruto)
			if err != nil {
				rows.Close()
				return fmt.Errorf("[argentina.order.go]Error al obtener los pluId de los productos: %v", err)
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
			return fmt.Errorf("[argentina.order.go]Error al insertar los datos en la tabla dbo.Detalle_Orden_Pedido %v: %v", detailsOrder.GetValues(), err)
		}
		if rows.Err() != nil {
			return fmt.Errorf("[argentina.order.go]Error al iterar el rows: %v", rows.Err())
		}
		var idDetalleOrdenPedido string
		err = rows.Scan(&idDetalleOrdenPedido)
		if err != nil {
			return fmt.Errorf("[argentina.order.go]Error al obtener el idDetalleOrdenpedido al momento de insertar los datos en la tabla dbo.Detalle_Orden_Pedido: %v", err)
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
				return fmt.Errorf("[argentina.order.go]Error al actualizar los datos de la tabla Detalle_Orden_Pedido: %v", err)
			}
			rowsAfected, err := exec.RowsAffected()
			if err != nil {
				return fmt.Errorf("[argentina.order.go]Error al identificar la actualizacion de los datos de la tabla Detalle_Orden_Pedido: %v", err)
			}
			if rowsAfected != 1 {
				return fmt.Errorf("[argentina.order.go]No se actualizaron los registros de la tabla detalle_orden_pedido")
			}
		} else {
			exec, err := connection.Exec(updateDetails,
				sql.Named("isModifica", detailsInsert.Exchange),
				sql.Named("idDetalleOrdenPedido", detailsInsert.IdDetalleOrdenPedido),
				sql.Named("addSecond", i+1),
				sql.Named("idKioskoDetallePedido", idOrderToString),
				sql.Named("idCabeceraOrdenPedido", detailsInsert.IdCabeceraOrdenPedido))
			if err != nil {
				return fmt.Errorf("[argentina.order.go]Error al actualizar los datos en la tabla Detalle_Orden_Pedido: %v", err)
			}
			rowsAfected, err := exec.RowsAffected()
			if err != nil {
				return fmt.Errorf("[argentina.order.go]Error al identificar la actualizacion de los datos de la tabla Detalle_Orden_Pedido: %v", err)
			}
			if rowsAfected != 1 {
				return fmt.Errorf("[argentina.order.go]No se actualizaron los registros de la tabla detalle_orden_pedido")
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
		return nil, nil, fmt.Errorf("[argentina.order.go]Error al ejecutar el query %v: %v", query, err)
	}
	defer result.Close()
	for result.Next() {
		var idClient, document *string
		err = result.Scan(&idClient, &document)
		if err != nil {
			return nil, nil, fmt.Errorf("[argentina.order.go]Error al validar los datos de Cliente: %v", err)
		}
		return idClient, document, nil
	}
	return nil, nil, nil
}
func (o *OrderStore) GetIdPaymentMethod(connection *sqlserver.DatabaseSql, newBin string) (*string, *string, error) {
	//INFORMACIÓN SWT
	binToString, _ := utils.StrToUint32(newBin)
	var idFormaPago, cardName *string

	newQuery := fmt.Sprintf(`SELECT COUNT(DISTINCT (fp.IDFormapago))
					FROM PaisColeccionDeDatos pd WITH (NOLOCK)
					INNER JOIN Formapago fp WITH (NOLOCK) ON CAST(pd.idIntegracion AS VARCHAR(40)) = CAST(fp.IDFormapago AS VARCHAR(40))
					WHERE @bin BETWEEN min AND max
						AND fp.cdn_id = @cdn_id`)
	rowCount := connection.QueryRow(newQuery, sql.Named("bin", binToString), sql.Named("cdn_id", o.StoreData.ChainId))
	err := rowCount.Err()
	if err != nil {
		return nil, nil, fmt.Errorf("[argentina.order.go]Error al ejecutar el query %v: %v", newQuery, err)
	}
	var count string
	err = rowCount.Scan(&count)
	if err != nil {
		return nil, nil, fmt.Errorf("[argentina.order.go]Error al obtener la cantidad de las formas de pago: %v", err)
	}

	if count == "1" {
		queryFormaPago := fmt.Sprintf(`SELECT DISTINCT (CAST(fp.IDFormapago AS VARCHAR(36))),fp.fmp_descripcion
					FROM PaisColeccionDeDatos AS pd WITH (NOLOCK)
					INNER JOIN Formapago AS fp WITH (NOLOCK) ON CAST(pd.idIntegracion AS VARCHAR(40)) = CAST(fp.IDFormapago AS VARCHAR(40))
					WHERE @bin BETWEEN min AND max
						AND fp.cdn_id = @cdn_id`)
		rowFormaPago := connection.QueryRow(queryFormaPago, sql.Named("bin", binToString), sql.Named("cdn_id", o.StoreData.ChainId))
		err = rowFormaPago.Err()
		if err != nil {
			return nil, nil, fmt.Errorf("[argentina.order.go]Error al ejecutar el query %v: %v", queryFormaPago, err)
		}
		errPaymentMethod := rowFormaPago.Scan(&idFormaPago, &cardName)
		if errPaymentMethod != nil {
			if errors.Is(errPaymentMethod, sql.ErrNoRows) {
				logger.Error.Printf("[argentina.order.go] No se encontraron formas de pago para BIN: %v, ChainId: %v", binToString, o.StoreData.ChainId)
			} else {
				return nil, nil, fmt.Errorf("[argentina.order.go]Error al obtener el idFormaPago: %v", errPaymentMethod)
			}
		}

	} else {
		queryFormaPago := fmt.Sprintf(`SELECT DISTINCT (CAST(fp.IDFormapago AS VARCHAR(36))),fp.fmp_descripcion
					FROM PaisColeccionDeDatos AS pd WITH (NOLOCK)
					INNER JOIN Formapago AS fp WITH (NOLOCK) ON CAST(pd.idIntegracion AS VARCHAR(40)) = CAST(fp.IDFormapago AS VARCHAR(40))
					WHERE (min >= @bin AND pd.max <= @bin)`)
		rowFormaPago := connection.QueryRow(queryFormaPago, sql.Named("bin", binToString))
		err = rowFormaPago.Err()
		if err != nil {
			return nil, nil, fmt.Errorf("[argentina.order.go]Error al ejecutar el query %v: %v", queryFormaPago, err)
		}
		errPaymentMethod := rowFormaPago.Scan(&idFormaPago, &cardName)
		if errPaymentMethod != nil {
			if errors.Is(errPaymentMethod, sql.ErrNoRows) {
				logger.Warning.Printf("[argentina.order.go] No se encontraron formas de pago para BIN: %v, ChainId: %v", binToString, o.StoreData.ChainId)
				return nil, nil, nil
			} else {
				return nil, nil, fmt.Errorf("[argentina.order.go]Error al obtener el idFormaPago: %v", errPaymentMethod)
			}
		}
	}
	return idFormaPago, cardName, nil
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
		return nil, fmt.Errorf("[argentina.order.go]Error al ejectuar el sp [config].[IAE_Cliente] %v", err)
	}
	if !strings.EqualFold("U", accion) {
		defer rows.Close()
		for rows.Next() {
			var idClient *string
			err = rows.Scan(&idClient)
			if err != nil {
				return nil, fmt.Errorf("[argentina.order.go]Error al obtener los datos del sp [config].[IAE_Cliente] %v", err)
			}
			return idClient, nil
		}
	}

	return nil, nil
}
func (o *OrderStore) Folio(connection *sqlserver.DatabaseSql, cfacId, CashierName, jsonInvoicing string) (*string, error) {
	electronicBilling := NewElectronicBillingArg(connection, o.FoliadorService, o.StoreData)
	folio, err := electronicBilling.SendDataFolio(cfacId, CashierName, o.Order.Cabecera.Client.DocumentNumber, jsonInvoicing)
	if err != nil {
		return nil, err
	}
	return folio, nil
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
