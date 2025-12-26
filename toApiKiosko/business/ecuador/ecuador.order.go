package ecuador

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
	"new-order-store/internals/domain/business/ecuador/clientoptin"
	"new-order-store/internals/domain/business/ecuador/printservice"
	"new-order-store/internals/domain/business/ecuador/strips"
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
	"regexp"
	"runtime/debug"
	"strconv"
	"strings"
	"time"
	"unicode"

	"golang.org/x/text/runes"
	"golang.org/x/text/transform"
	"golang.org/x/text/unicode/norm"
)

// Constantes para IDs y validaciones. Usar constantes evita "magic strings".
const (
	ConsumidorFinalID = "9999999999" // 9999999999 es el RUC/Cédula de Consumidor Final en Ecuador (10 dígitos)
	RucSuffix         = "001"
	RucLength         = 13
	CedulaLength      = 10
)

var (
	// Regex para nombres de personas naturales (Cédula).
	// Permite letras (incluyendo Ñ), espacios y apóstrofes simples.
	// Ej: "JOSÉ ANDRÉS O'CONNOR"
	cedulaNameRegex = regexp.MustCompile(`^[a-zA-ZÑñ\s']+$`)

	// Regex para nombres de personas jurídicas (RUC - Razón Social).
	// Mucho más permisivo: permite letras, números, espacios y caracteres comunes en razones sociales.
	// Ej: "FERRETERÍA Y SERVICIOS S.A. & CIA. LTDA."
	rucNameRegex = regexp.MustCompile(`^[a-zA-Z0-9Ññ\s.&,'\-()\/]+$`)

	// Regex estándar para validar la estructura de un email.
	emailRegex = regexp.MustCompile(`^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$`)
)

// SanitizeString normaliza un string, eliminando tildes, diéresis y convirtiendo 'ñ' a 'n'.
// También lo convierte a minúsculas, ideal para emails.
func SanitizeString(input string) (string, error) {
	// La cadena de transformadores hace lo siguiente:
	// 1. norm.NFD: Descompone caracteres (ej: 'í' se convierte en 'i' + '´').
	// 2. runes.Remove: Elimina los caracteres de acento (como '´').
	// 3. norm.NFC: Recompone los caracteres a su forma normal.
	t := transform.Chain(norm.NFD, runes.Remove(runes.In(unicode.Mn)), norm.NFC)

	// Aplicamos la transformación y convertimos a minúsculas.
	output, _, err := transform.String(t, input)
	if err != nil {
		return "", err
	}

	// Adicionalmente, reemplazamos 'ñ' por 'n' que a veces no es cubierto por la normalización unicode.
	output = strings.ReplaceAll(output, "ñ", "n")
	output = strings.ReplaceAll(output, "Ñ", "N")

	return strings.ToLower(output), nil
}

// SanitizeNameForCedula limpia un string para nombres de cédula, reemplazando caracteres especiales (excepto letras, espacios y ') por un espacio.
// Mantiene 'ñ' y vocales con tilde.
func SanitizeNameForCedula(input string) string {
	var result []rune
	for _, r := range input {
		// Permitir letras (incluyendo ñ y tildes), espacios y apóstrofes
		if unicode.IsLetter(r) || unicode.IsSpace(r) || r == '\'' {
			result = append(result, r)
		} else {
			result = append(result, ' ')
		}
	}
	// Normaliza múltiples espacios a uno solo
	return strings.Join(strings.Fields(string(result)), " ")
}

type OrderStore struct {
	DatabaseCredential *credential.DatabaseCredential
	Order              *lib_gen_proto.Order
	StoreData          *maxpoint.StoreData
	Feature            *featureflag.FeatureFlag
	Promotions         *strips.Promotion
	NatsClient         *natsmodule.NatsStarter
	AutomaticCoupons   *strips.AutomaticCoupon
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
	dataStrip *models.EnvDataTirillas,
	regionalKiosk execute.RegionalKioskExecute,
) execute.OrderExecutorSql {
	promotion := strips.NewPromotion(dataStrip)
	automaticCoupon := strips.NewAutomaticCoupon()
	return &OrderStore{
		DatabaseCredential: DatabaseCredential,
		Order:              Order,
		StoreData:          StoreData,
		Feature:            Feature,
		Promotions:         promotion,
		AutomaticCoupons:   automaticCoupon,
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
	err = o.ValidateProduct(connection)
	if err != nil {
		return err
	}
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
	generateSecuencial := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "facturacion.NewKiosko_generarSecuencialFactura")
	generateSecuencial.AddValueParameterized("idRestaurante", o.StoreData.RestaurantId)
	rows, err := connection.IQuery(generateSecuencial.GetStoreProcedure(), generateSecuencial.GetValues())
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al ejecutar el sp facturacion.NewKiosko_generarSecuencialFactura: %v", err)
	}
	defer rows.Close()
	for rows.Next() {
		err = rows.Scan(&cfacId)
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al obtener los datos de NewKiosko_generarSecuencialFactura: %v", err)
		}
	}
	if cfacId == nil {
		return fmt.Errorf("[ecuador.order.go]Error - El cfacId generado del sp [facturacion].[NewKiosko_generarSecuencialFactura] esta vacio, por favor revisar")
	}
	idTypeDocument := o.StoreData.GetTipoDocumento(documentId)
	if idTypeDocument == nil {
		return fmt.Errorf("[ecuador.order.go]No se encontro el tipo de documento para el id %s", documentId)
	}
	if err = o.ValidateDataClient(idTypeDocument); err != nil {
		return err
	}
	/*
		if strings.EqualFold(idTypeDocument.Description, "CONSUMIDOR FINAL") {
			o.Order.Cabecera.Client.Email = "consumidor.final@kfc.com.ec"
			o.Order.Cabecera.Client.Address = "Quito"
			o.Order.Cabecera.Client.DocumentNumber = "9999999999"
			o.Order.Cabecera.Client.Phone = "2222222"
		}*/
	idUserPos, err := o.GetIdUserPos(connection, resultStation)
	if err != nil {
		return err
	}
	if idUserPos == nil {
		return fmt.Errorf("[ecuador.order.go]No se encontro el id del usuario para kiosko")
	}
	idControlStation, err := o.GetStationControl(connection, resultStation)
	if err != nil {
		return err
	}
	if idControlStation == nil {
		return fmt.Errorf("[ecuador.order.go]La estacion de kiosko no se encuentra activa")
	}
	idPeriod, err := o.GetOpenPeriod(connection)
	if err != nil {
		return err
	}
	if idPeriod == nil {
		return fmt.Errorf("[ecuador.order.go]No se encontro ningun periodo abierto")
	}
	/*idClient, err := o.GetIdClient(connection)
	if err != nil {
		return err
	}*/
	idMesa, err := o.GetIdMesa(connection, resultStation)
	if err != nil {
		return err
	}
	newTotalBill, _ := utils.StrToFloat32(o.Order.PaymentMethods.TotalBill)
	if newTotalBill > 0 {
		err = connection.CreateBegin()
		defer connection.Rollback()
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al crear BEGIN: %v", err.Error())
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
			return fmt.Errorf("[ecuador.order.go]Error al insertar el update BEGIN: %v", err.Error())
		}
		_, err = exec.RowsAffected()
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al actualizar la tabla Cabecera_Orden_Pedido %s", err.Error())
		}
		/*if affected < 1 {
			return fmt.Errorf("no se actualizo el estado de la tabla Cabecera_Orden_Pedido %v", queryUpdateOrderPedido)
		}*/

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
			return fmt.Errorf("[ecuador.order.go]Error al insertar los datos en la tabla Cabecera_Orden_Pedido %v : %v", insertCabeceraOrdenPedido, err.Error())
		}
		var idCabeceraOrdenPedido string
		err = insertData.Scan(&idCabeceraOrdenPedido)
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al obtener el id de la Cabecera_Orden_Pedido: %v", err.Error())
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
				return fmt.Errorf("[ecuador.order.go]Error al ejecutar el sp facturacion.IAE_Fac_InsertFactura_kioskoEfectivo:%v", err)
			}
		}

		//Se genera la respuesta de envio para el core-kiosco-api
		order := &lib_gen_proto.ResultStoreValidationData{}
		order.OrderId = orderId
		order.CfacId = *cfacId
		connection.Commit()
		//envio de data hacia el core-kiosko
		o.SendOrderResponse(order)
		o.SendOrderTurner(*cfacId)
		defer func() {
			errPrintService := o.WsPrintService(connection, *cfacId, idCabeceraOrdenPedido, *idUserPos, nil, resultStation)
			if errPrintService != nil {
				logger.Error.Println(errPrintService.Error())
			}
			o.ClientOptIn(connection)
			errKds := o.SendKds(connection, *cfacId, "apiKDSKioskoEfectivo")
			if errKds != nil {
				logger.Error.Println(errKds.Error())
			}
		}()
	}

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
	order := &lib_gen_proto.ResultStoreValidationData{}
	orderId := o.Order.Cabecera.OrderId
	statusMxp := o.StoreData.Status
	chainId := o.StoreData.ChainId
	restaurantId := o.StoreData.RestaurantId
	currentDate := time.Now()
	connection, err := sqlserver.NewConnectionSql(o.DatabaseCredential)
	if err != nil {
		return err
	}
	defer func() {
		connection.Close()
	}()
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
	documentId := o.Order.Cabecera.Client.DocumentId
	idTypeDocument := o.StoreData.GetTipoDocumento(documentId)
	if idTypeDocument == nil {
		return fmt.Errorf("[ecuador.order.go]No se encontro el tipo de documento para el id %s", documentId)
	}
	if err = o.ValidateDataClient(idTypeDocument); err != nil {
		return err
	}
	/*
		if strings.EqualFold(idTypeDocument.Description, "CONSUMIDOR FINAL") {
			o.Order.Cabecera.Client.Email = "consumidor.final@kfc.com.ec"
			o.Order.Cabecera.Client.Address = "Quito"
			o.Order.Cabecera.Client.DocumentNumber = "9999999999"
			o.Order.Cabecera.Client.Phone = "2222222"
		}*/
	idUserPos, err := o.GetIdUserPos(connection, resultStation)
	if err != nil {
		return err
	}
	if idUserPos == nil {
		return fmt.Errorf("[ecuador.order.go]No se encontro el id del usuario para kiosko")
	}
	idControlStation, err := o.GetStationControl(connection, resultStation)
	if err != nil {
		return err
	}
	if idControlStation == nil {
		return fmt.Errorf("[ecuador.order.go]La estacion de kiosko no se encuentra activa")
	}
	idPeriod, err := o.GetOpenPeriod(connection)
	if err != nil {
		return err
	}
	if idPeriod == nil {
		return fmt.Errorf("[ecuador.order.go]No se encontro ningun periodo abierto")
	}

	idMesa, err := o.GetIdMesa(connection, resultStation)
	if err != nil {
		return err
	}
	//Procesamiento de tarjeta
	err = connection.CreateBegin()
	defer connection.Rollback()
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al crear BEGIN: %v", err.Error())
	}
	idOrderKiosko, datailsKiosko, err := o.InsertOrderKiosko(connection, nil)
	if err != nil {
		return err
	}
	//valida el bin de las formas de pago
	validateBin := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[USP_verificaBinTarjetaFormaPago]")
	validateBin.AddValueParameterized("bin", o.Order.PaymentMethods.Bin)
	validateBin.AddValueParameterized("rst", restaurantId)
	validateBin.AddValueParameterized("user", "")
	validateBin.AddValueParameterized("ip", "")

	rowsBin, err := connection.IQuery(validateBin.GetStoreProcedure(), validateBin.GetValues())
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al escutar el sp [facturacion].[USP_verificaBinTarjetaFormaPago] para kiosko: %v", err.Error())
	}
	defer rowsBin.Close()
	for rowsBin.Next() {
		var idFormaPag, confirma, mensaje string
		err = rowsBin.Scan(&idFormaPag, &confirma, &mensaje)
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al obtener los datos de bin del sp [facturacion].[USP_verificaBinTarjetaFormaPago] para kiosko: %v", err.Error())
		}
		if confirma != "1" {
			return fmt.Errorf(mensaje)
		}
	}
	//Insercion de datos a la tabla CabeceraOrdenPedido
	var idOrderHeaderOrder string
	orderHeaderOrder := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[pedido].[ORD_configuracion_proceso_ordenpedido_Newkiosko]")
	orderHeaderOrder.AddValueParameterized("rst_id", restaurantId)
	orderHeaderOrder.AddValueParameterized("IDMesa", idMesa)
	orderHeaderOrder.AddValueParameterized("IDUsersPos", idUserPos)
	orderHeaderOrder.AddValueParameterized("IDEstacion", resultStation.IdStation)
	orderHeaderOrder.AddValueParameterized("num_Pers", 1)
	orderHeaderOrder.AddValueParameterized("idPedido", idOrderKiosko)
	orderHeaderOrder.AddValueParameterized("idOrdenPedido", sql.Out{Dest: &idOrderHeaderOrder})
	_, err = connection.IQuery(orderHeaderOrder.GetStoreProcedure(), orderHeaderOrder.GetValues())
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al ejecutar el sp [pedido].[ORD_configuracion_proceso_ordenpedido_Newkiosko]: %v", err)
	}
	if utils.IsEmpty(idOrderHeaderOrder) {
		return fmt.Errorf("[ecuador.order.go]Error al obtener el id de la tabla cabecera_orden_pedido")
	}
	//insercion de los productos a la tabla detalle_orden_pedido
	err = o.InsertDetailOrder(connection, datailsKiosko, idOrderHeaderOrder)
	if err != nil {
		return err
	}
	var cfacId string
	invoicing := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "facturacion.IAE_Fac_InsertFactura_Newkiosko")
	invoicing.AddValueParameterized("IDRestaurante", restaurantId)
	invoicing.AddValueParameterized("IDCabeceraOrdenPedido", idOrderHeaderOrder)
	invoicing.AddValueParameterized("IDUsersPos", idUserPos)
	invoicing.AddValueParameterized("numeroCuenta", 1)
	invoicing.AddValueParameterized("IDEstacion", resultStation.IdStation)
	invoicing.AddValueParameterized("IDPeriodo", idPeriod)
	invoicing.AddValueParameterized("IDControlEstacion", idControlStation)
	invoicing.AddValueParameterized("cfacID", sql.Out{Dest: &cfacId})
	_, err = connection.IQuery(invoicing.GetStoreProcedure(), invoicing.GetValues())
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al ejecutar el sp [facturacion].[IAE_Fac_InsertFactura_Newkiosko]: %v", err)
	}
	if utils.IsEmpty(cfacId) {
		return fmt.Errorf("[ecuador.order.go]Error al obtener el cfacId para el pedido %v", orderId)
	}
	//Actualizo la tabla kiosko_cabecera_pedidos con el cfacId
	updateKioskoHeader := fmt.Sprintf(`UPDATE dbo.kiosko_cabecera_pedidos
				SET cfac_id = @codFactura
				WHERE id = @idPedido`)
	rowsKioskoHeader, err := connection.Exec(updateKioskoHeader, sql.Named("codFactura", cfacId), sql.Named("idPedido", idOrderKiosko))
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al actualizar los datos de la tabla kiosko_cabecera_pedidos: %v", err)
	}
	affectedKioskoHeader, err := rowsKioskoHeader.RowsAffected()
	if err != nil {
		return err
	}
	if affectedKioskoHeader < 1 {
		return fmt.Errorf("[ecuador.order.go]No se ha podio actualizar los datos de la tabla kiosko_cabecera_pedidos")
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
		return fmt.Errorf("[ecuador.order.go]Error al actualizar los datos de la tabla cabecera_orden_pedido: %v", err)
	}
	affectedOrderHeader, err := rowsOrderHeader.RowsAffected()
	if err != nil {
		return err
	}
	if affectedOrderHeader < 1 {
		return fmt.Errorf("[ecuador.order.go]No se ha podio actualizar los datos de la tabla cabecera_orden_pedido")
	}
	//Obtengo el total de la factura
	var cfacTotal string
	query := "SELECT cfac_total FROM dbo.Cabecera_Factura WHERE cfac_id = @cfacId"
	rows, err := connection.Query(query, sql.Named("cfacId", cfacId))
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al ejectar el query %v: %v", query, err)
	}
	defer rows.Close()
	for rows.Next() {
		err = rows.Scan(&cfacTotal)
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al obtener el valor total de la factura pa el pedido %v: %v", orderId, err)
		}
	}
	var newBin string
	//Insercion de los datos de formapago_factura
	if len(o.Order.PaymentMethods.Card.NumberCard) > 5 {
		newBin = o.Order.PaymentMethods.Card.NumberCard[0:6]
	} else {
		newBin = o.Order.PaymentMethods.Bin
	}

	totalValue, _ := utils.ConvertNumberToString(o.Order.PaymentMethods.TotalBill)
	err = o.InsertPaymentMethodInvoicing(connection, cfacId, newBin, totalValue, cfacTotal, *idUserPos)
	if err != nil {
		return err
	}
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
	//INSERTA CLIENTE - FIN FACTURA
	invoicingClient := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[USP_FacturaCliente]")
	invoicingClient.AddValueParameterized("idCliente", document)
	invoicingClient.AddValueParameterized("IDFactura", cfacId)
	invoicingClient.AddValueParameterized("IDUserpos", idUserPos)
	_, err = connection.IQuery(invoicingClient.GetStoreProcedure(), invoicingClient.GetValues())
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al ejecutar el sp [facturacion].[USP_FacturaCliente]: %v", err)
	}
	//INFORMACIÓN SWT
	bintToString, _ := utils.StrToUint32(newBin)
	var idFormaPago, tarjetName *string

	newQuery := fmt.Sprintf(`SELECT COUNT(DISTINCT (fp.IDFormapago))
					FROM PaisColeccionDeDatos pd WITH (NOLOCK)
					INNER JOIN Formapago fp WITH (NOLOCK) ON CAST(pd.idIntegracion AS VARCHAR(40)) = CAST(fp.IDFormapago AS VARCHAR(40))
					WHERE @bin BETWEEN min AND max
						AND fp.cdn_id = @cdn_id`)
	rowCount := connection.QueryRow(newQuery, sql.Named("bin", bintToString), sql.Named("cdn_id", chainId))
	err = rowCount.Err()
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al ejecutar el query %v: %v", newQuery, err)
	}
	var count string
	err = rowCount.Scan(&count)
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al obtener la cantidad de las formas de pago: %v", err)
	}

	if count == "1" {
		queryFormaPago := fmt.Sprintf(`SELECT DISTINCT (CAST(fp.IDFormapago AS VARCHAR(36))),fp.fmp_descripcion
					FROM PaisColeccionDeDatos AS pd WITH (NOLOCK)
					INNER JOIN Formapago AS fp WITH (NOLOCK) ON CAST(pd.idIntegracion AS VARCHAR(40)) = CAST(fp.IDFormapago AS VARCHAR(40))
					WHERE @bin BETWEEN min AND max
						AND fp.cdn_id = @cdn_id`)
		rowFormaPago := connection.QueryRow(queryFormaPago, sql.Named("bin", bintToString), sql.Named("cdn_id", chainId))
		err = rowFormaPago.Err()
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al ejecutar el query %v: %v", queryFormaPago, err)
		}
		err = rowFormaPago.Scan(&idFormaPago, &tarjetName)
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al obtener el idFormaPago: %v", err)
		}
		if idFormaPago == nil {
			return fmt.Errorf("[ecuador.order.go]Error, no se encontro el id de la forma de pago para kiosko")
		}
	} else {
		queryFormaPago := fmt.Sprintf(`SELECT DISTINCT (CAST(fp.IDFormapago AS VARCHAR(36))),fp.fmp_descripcion
					FROM PaisColeccionDeDatos AS pd WITH (NOLOCK)
					INNER JOIN Formapago AS fp WITH (NOLOCK) ON CAST(pd.idIntegracion AS VARCHAR(40)) = CAST(fp.IDFormapago AS VARCHAR(40))
					WHERE (min >= @bin AND pd.max <= @bin)
						AND fp.cdn_id = @cdn_id`)
		rowFormaPago := connection.QueryRow(queryFormaPago, sql.Named("bin", bintToString), sql.Named("cdn_id", chainId))
		err = rowFormaPago.Err()
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al ejecutar el query %v: %v", queryFormaPago, err)
		}
		err = rowFormaPago.Scan(&idFormaPago, &tarjetName)
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al obtener el idFormaPago: %v", err)
		}
		if idFormaPago == nil {
			return fmt.Errorf("[ecuador.order.go]Error, no se encontro el id de la forma de pago para kiosko")
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
	authorizationRequirement.AddValue("IDFormapagoFactura", idFormaPago)
	authorizationRequirement.AddValue("IDEstacion", resultStation.IdStation)
	authorizationRequirement.AddValue("IDUsersPos", idUserPos)
	authorizationRequirement.AddValue("IDStatus", statusMxp.SesionesActivo)
	authorizationRequirement.AddValue("replica", 0)
	authorizationRequirement.AddValue("nivel", 0)
	_, err = connection.IQuery(authorizationRequirement.SqlGenerated(), authorizationRequirement.GetValues())
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al insertar los datos en la tabla dbo.SWT_Requerimiento_Autorizacion: %v", err)
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
		return fmt.Errorf("[ecuador.order.go]Error al insertar los datos en la tabla dbo.SWT_Respuesta_Autorizacion: %v", err)
	}
	if rowsResponseAuthorization.Err() != nil {
		return fmt.Errorf("[ecuador.order.go]Error al iterar el rows de la tabla dbo.SWT_Respuesta_Autorizacion: %v", rowsResponseAuthorization.Err())
	}
	var rsAutId int32
	err = rowsResponseAuthorization.Scan(&rsAutId)
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al obtener el identificador unico de la tabla dbo.SWT_Respuesta_Autorizacion: %v", err.Error())
	}
	var jsonInvoicing, jsonVoucher *string
	dataOrderPrintFastFood := make([]*models.KioskoImpresionOrdenPedidoFastFood, 0)
	//dataOrderVoucherComercio := make([]*models.KioskoImpresionVoucherComercio, 0)
	//Genereacion del Json de factura para impresion
	dynamicInvoicePrinting := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[dbo].[kiosko_USP_impresiondinamica_factura_kioskoCamposJson]")
	dynamicInvoicePrinting.AddValueParameterized("cfac_id", cfacId)
	dynamicInvoicePrinting.AddValueParameterized("tipo_comprobante", "F")
	rowInvoicePrinting, err := connection.IQuery(dynamicInvoicePrinting.GetStoreProcedure(), dynamicInvoicePrinting.GetValues())
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al ejecutar el sp [dbo].[kiosko_USP_impresiondinamica_factura_kioskoCamposJson]: %v", err)
	}
	defer rowInvoicePrinting.Close()
	for rowInvoicePrinting.Next() {
		err = rowInvoicePrinting.Scan(&jsonInvoicing)
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al obtener el json de impresion de la factura del sp [dbo].[kiosko_USP_impresiondinamica_factura_kioskoCamposJson]: %v", err)
		}
	}
	if jsonInvoicing == nil {
		return fmt.Errorf("[ecuador.order.go]Error, no se genero el json de impresion de factura")
	}
	//Genereacion del Json de voucher para impresion
	dynamicVoucherPrint := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[VOUCHER_USP_ImpresionDinamicaClienteKioskoCamposV2]")
	dynamicVoucherPrint.AddValueParameterized("rsaut_id", rsAutId)
	dynamicVoucherPrint.AddValueParameterized("usuario", resultStation.CashierName)
	dynamicVoucherPrint.AddValueParameterized("tipo", "CL")
	rowVoucherPrint, err := connection.IQuery(dynamicVoucherPrint.GetStoreProcedure(), dynamicVoucherPrint.GetValues())
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al ejecutar el sp [facturacion].[VOUCHER_USP_ImpresionDinamicaClienteKioskoCamposV2]: %v", err)
	}
	defer rowVoucherPrint.Close()
	for rowVoucherPrint.Next() {
		err = rowVoucherPrint.Scan(&jsonVoucher)
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al obtener el json de impresion del voucher del sp [facturacion].[VOUCHER_USP_ImpresionDinamicaClienteKioskoCamposV2]: %v", err)
		}
	}

	if jsonVoucher == nil {
		return fmt.Errorf("[ecuador.order.go]Error - no se genero el json del voucher para el cliente")
	}

	//Impresión de orden de pedido en impresora de línea
	orderPrintFastFood := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[pedido].[IAE_ImpresionOrdenPedidoFastFoodKiosko]")
	orderPrintFastFood.AddValueParameterized("idCabeceraOrdenPedido", idOrderHeaderOrder)
	orderPrintFastFood.AddValueParameterized("idCadena", chainId)
	orderPrintFastFood.AddValueParameterized("idRestaurante", restaurantId)
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
	/*if len(dataOrderPrintFastFood) == 0 {
		return fmt.Errorf("error, los valores para generar la impresion de los pedidos esta vacio")
	}*/
	//Impresión de voucher en impresora de línea con pedidos mayores al límite (15/40?)
	valuePrintVoucher := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[IAE_grabacanalMovimientoVoucherKiosko]")
	valuePrintVoucher.AddValueParameterized("idAutorizacion", rsAutId)
	valuePrintVoucher.AddValueParameterized("idCadena", chainId)
	valuePrintVoucher.AddValueParameterized("idRestaurante", restaurantId)
	valuePrintVoucher.AddValueParameterized("idOrdenPedido", idOrderHeaderOrder)
	rowPrintVoucher, err := connection.IQuery(valuePrintVoucher.GetStoreProcedure(), valuePrintVoucher.GetValues())
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al ejecutar el sp [facturacion].[IAE_grabacanalMovimientoVoucherKiosko]: %v", err)
	}
	defer rowPrintVoucher.Close()
	for rowPrintVoucher.Next() {
		var dataVoucherPrint models.KioskoImpresionOrdenPedidoFastFood
		err = rowPrintVoucher.Scan(&dataVoucherPrint.Tipo,
			&dataVoucherPrint.Impresora,
			&dataVoucherPrint.FormatoXML,
			&dataVoucherPrint.JsonData,
			&dataVoucherPrint.JsonRegistros)
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al obtener los datos para la impresion de la ordern de pedido: %v", err)
		}
		dataVoucherPrint.NumeroImpresiones = 1
		dataOrderPrintFastFood = append(dataOrderPrintFastFood, &dataVoucherPrint)
	}
	//Actualiza el medio de venta de la facura
	salesMedia := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[IAE_Medio_de_Venta_Integraciones]")
	salesMedia.AddValueParameterized("transaccion", cfacId)
	salesMedia.AddValueParameterized("sistema", "MaxPoint")
	salesMedia.AddValueParameterized("medio", "KIOSKO TARJETA")
	_, err = connection.IQuery(salesMedia.GetStoreProcedure(), salesMedia.GetValues())
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al ejecutar el sp [facturacion].[IAE_Medio_de_Venta_Integraciones]: %v", err)
	}
	//JsonPropina
	isTip := o.Feature.GetConfigFeatureFlag(featureflag2.PROPINA)
	if isTip {
		jsonTip, err := o.regionalKiosk.GetTips(connection, *idUserPos, *idControlStation)
		if err != nil {
			return err
		}
		if jsonTip != nil {
			order.Propina = jsonTip
		}
	}

	//obtencion de data para promociones de tirillas
	isPromotion := o.Feature.GetConfigFeatureFlag(featureflag2.TIRILLAS)
	if isPromotion {
		promotions, errPromotion := o.Promotions.Promotions(connection, o.Order.Items, chainId)
		if errPromotion != nil {
			logger.Error.Println(errPromotion.Error())
		}
		if len(promotions) > 0 {
			tmpPromotion, errConvertString := utils.InterfaceToString(promotions)
			if errConvertString != nil {
				logger.Error.Println(errConvertString.Error())
			} else {
				order.Promociones = &tmpPromotion
			}
		}
	}
	isCoupon := o.Feature.GetConfigFeatureFlag(featureflag2.CUPONES)
	if isCoupon {
		automaticCoupons, errCoupon := o.AutomaticCoupons.AutomaticCoupons(connection, o.StoreData, o.Order)
		if errCoupon != nil {
			logger.Error.Println(errCoupon.Error())
		}
		if automaticCoupons != nil && len(automaticCoupons) > 0 {
			tmpAutomaticCoupon, errConvertString := utils.InterfaceToString(automaticCoupons)
			if errConvertString != nil {
				logger.Error.Println(errConvertString.Error())
			} else {
				order.CuponesAutomaticos = &tmpAutomaticCoupon
			}
		}
	}
	connection.Commit()
	//Se genera la respuesta de envio para el core-kiosco-api
	order.IdOrdenPedido = idOrderHeaderOrder
	order.OrderId = orderId
	order.CfacId = cfacId
	order.Factura = *jsonInvoicing
	order.Voucher = *jsonVoucher

	o.SendOrderResponse(order)
	o.SendOrderTurner(cfacId)

	isTurnerEnabled := o.Feature.GetConfigFeatureFlag(featureflag2.TURNERO_DIGITAL)
	if isTurnerEnabled {
		go o.regionalKiosk.SendTurnerDigital(cfacId)
	}

	//Valido si el featureFlag para encuesta esta activo
	isSurvey := o.Feature.GetConfigFeatureFlag(featureflag2.ENCUESTA)
	if isSurvey {
		o.regionalKiosk.GetSurvey(dataTypeDocument, cfacId, *idClient)
	}

	//procesos de impresion de tarjeta

	defer func() {
		errPrintServices := o.WsPrintService(connection, cfacId, idOrderHeaderOrder, *idUserPos, dataOrderPrintFastFood, resultStation)
		if errPrintServices != nil {
			logger.Error.Println(errPrintServices.Error())
		}
		o.ClientOptIn(connection)
		errKds := o.SendKds(connection, idOrderHeaderOrder, "apiKDSKioskoTarjeta")
		if errKds != nil {
			logger.Error.Println(errKds.Error())
		}
	}()

	return nil
}

func (o *OrderStore) ValidateProduct(connection *sqlserver.DatabaseSql) error {
	for _, details := range o.Order.Items.Product {
		productExist := false
		productPluId := fmt.Sprintf(`select  plu_id, pr_pvp from precio_plu where IDCategoria in (select rst_categoria from restaurante) and plu_id= @productId`)

		rows, err := connection.Query(productPluId, sql.Named("productId", details.ProductId))
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al ejecutar el query %s: %s", productPluId, err.Error())
		}

		for rows.Next() {
			productExist = true
			var pluId, pvp *float32
			if err = rows.Scan(&pluId, &pvp); err != nil {
				return fmt.Errorf("[ecuador.order.go]Error al obtener los datos del query %v: %s", productPluId, err)
			}
			if utils.IsEmpty(pluId) {
				return fmt.Errorf("[ecuador.order.go]El producto %v seleccionado no existe en el local. Por favor acércate a la caja para realizar tu pedido. ¡Te pedimos disculpas por el inconveniente! ", details.NameProduct)
			}

			totalPrice := details.TotalPrice
			logger.Info.Printf("pvp %v", *pvp)
			logger.Info.Printf("payload price %v", totalPrice)
			// Comentado por HU: 94619 -el precio de payload puede ser diferente al de la base de datos
			// if utils.IsEmpty(pvp) || utils.IsEmpty(totalPrice) || *pvp != totalPrice {
			// 	return fmt.Errorf("[ecuador.order.go]Error de precios: El precio del producto %v(%v)  no coincide con el precio registrado en el sistema. ", details.NameProduct, details.ProductId)
			// }

		}
		if !productExist {
			return fmt.Errorf("[ecuador.order.go]El producto %v(%v) seleccionado no existe en el local. Por favor acércate a la caja para realizar tu pedido. ¡Te pedimos disculpas por el inconveniente! ", details.NameProduct, details.ProductId)

		}
		defer rows.Close()
		for _, modifiers := range details.ModifierGroups {
			modifierExist := false
			modifierPluId := modifiers.ProductId

			rows2, err := connection.Query(productPluId, sql.Named("productId", modifierPluId))
			if err != nil {
				return fmt.Errorf("[ecuador.order.go]Error al ejecutar el query %s: %s", productPluId, err.Error())
			}
			defer rows2.Close()
			for rows2.Next() {
				modifierExist = true
				var pluId, pvp *float32
				if err = rows2.Scan(&pluId, &pvp); err != nil {
					return fmt.Errorf("[ecuador.order.go]Error al obtener los datos del query %v: %s", productPluId, err)
				}
				if utils.IsEmpty(pluId) {
					return fmt.Errorf("[ecuador.order.go]El producto %v seleccionado no existe en el local. Por favor acércate a la caja para realizar tu pedido. ¡Te pedimos disculpas por el inconveniente! ", modifierPluId)
				}
				logger.Info.Printf("pvp %v", *pvp)
				// Comentado por HU: 94619 -el precio de payload puede ser diferente al de la base de datos
				// totalPrice := modifiers.TotalPrice
				// if utils.IsEmpty(pvp) || utils.IsEmpty(totalPrice) || *pvp != totalPrice {
				// 	return fmt.Errorf("[ecuador.order.go]Error de precios: El precio del producto %v(%v)  no coincide con el precio registrado en el sistema. ", modifiers.NameProduct, modifierPluId)
				// }

			}
			if !modifierExist {
				return fmt.Errorf("[ecuador.order.go]El producto %v(%v) seleccionado no existe en el local. Por favor acércate a la caja para realizar tu pedido. ¡Te pedimos disculpas por el inconveniente! ", modifiers.NameProduct, modifierPluId)
			}
		}
	}

	return nil
}
func (o *OrderStore) ValidateDataClient(typeDocument *maxpoint.DocumentType) error {
	// Usamos variables locales para mayor legibilidad
	clientDocumentNumber := o.Order.Cabecera.Client.DocumentNumber
	clientName := o.Order.Cabecera.Client.Name
	clientEmail := o.Order.Cabecera.Client.Email
	clientAddress := o.Order.Cabecera.Client.Address

	// --- CASO 1: VALIDACIÓN DE DIRECCIÓN PARA CONSUMIDOR FINAL ---
	// Si el cliente NO es "Consumidor Final", la dirección es obligatoria.
	// La validación se basa en el número de documento, no en la descripción del tipo.
	if !strings.EqualFold(o.Order.Cabecera.Client.Name, "CONSUMIDOR FINAL") {
		if strings.TrimSpace(clientAddress) == "" {
			return fmt.Errorf("[ecuador.order.go]Error, el campo cli_direccion no puede estar vacío")
		}
		// Limpiamos la dirección de caracteres no deseados
		o.Order.Cabecera.Client.Address = utils.CleanAddress(clientAddress)
	}
	// Si es Consumidor Final, se omite la validación de la dirección y su limpieza.

	// Verificación de montos para consumidor final. Esto se mantiene como estaba.
	total, err := strconv.ParseFloat(o.Order.PaymentInfo.Total, 64)
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error, no se pudo convertir el total de la orden a float")
	}

	if strings.EqualFold(typeDocument.Description, "CONSUMIDOR FINAL") {
		if float32(total) > float32(o.StoreData.BaseFactura) {
			return fmt.Errorf("[ecuador.order.go]Error: El monto de la factura supera el límite permitido para Consumidor Final. Se requiere incluir los datos del cliente")
		}
		// Si es consumidor final y el monto es válido, no se hacen más validaciones de cliente.
		// Si es consumidor final y el monto es válido, los campos de contacto deben ser opcionales, no forzados.
		return nil
	}

	// --- VALIDACIONES PARA CLIENTES QUE NO SON CONSUMIDOR FINAL ---

	// --- CASO 2: VALIDACIÓN DE NOMBRE (CÉDULA vs RUC) ---
	isRUC := len(clientDocumentNumber) == RucLength && strings.HasSuffix(clientDocumentNumber, RucSuffix)
	isCedula := len(clientDocumentNumber) == CedulaLength

	var processedClientName string = clientName // Usamos una variable para el nombre procesado

	if isCedula {
		processedClientName = SanitizeNameForCedula(clientName)
		o.Order.Cabecera.Client.Name = processedClientName // Actualizar el nombre en la orden
	}

	// Validamos la longitud del nombre primero (usando el nombre original o procesado si es cédula).
	if len(processedClientName) < 3 || len(processedClientName) > 100 {
		return fmt.Errorf("[ecuador.order.go]Error, el nombre del cliente debe tener entre 3 y 100 caracteres")
	}

	// Aplicamos el validador de formato.
	var nameValidator *regexp.Regexp
	if isRUC {
		nameValidator = rucNameRegex
	} else if isCedula {
		nameValidator = cedulaNameRegex
	}

	if nameValidator != nil && !nameValidator.MatchString(processedClientName) {
		return fmt.Errorf("[ecuador.order.go]Error, el nombre del cliente contiene caracteres no permitidos para el tipo de identificación")
	}

	// --- CASO 3: SANITIZACIÓN Y VALIDACIÓN DE EMAIL ---
	if strings.TrimSpace(clientEmail) != "" {
		sanitizedEmail, errSanitize := SanitizeString(clientEmail)
		if errSanitize != nil {
			// Este error sería muy raro, pero es bueno manejarlo.
			return fmt.Errorf("[ecuador.order.go]Error interno al procesar el email del cliente: %w", errSanitize)
		}

		// Actualizamos el email en la orden con la versión sanitizada.
		o.Order.Cabecera.Client.Email = sanitizedEmail

		if !emailRegex.MatchString(sanitizedEmail) {
			return fmt.Errorf("[ecuador.order.go]Error, el correo del cliente no cumple con el formato específico")
		}
	}

	return nil
}

func (o *OrderStore) GetOpenPeriod(connection *sqlserver.DatabaseSql) (*string, error) {
	query := fmt.Sprintf(`SELECT CAST(IDPeriodo AS VARCHAR(40))
			FROM dbo.Periodo
			WHERE IDStatus = @idStatus
				AND prd_fechacierre IS NULL`)
	rows, err := connection.Query(query, sql.Named("idStatus", o.StoreData.Status.PeriodoAperturaAbierto))
	if err != nil {
		return nil, fmt.Errorf("[ecuador.order.go]Error al ejecutar el query %s: %s", query, err.Error())
	}
	defer rows.Close()
	for rows.Next() {
		var result string
		err = rows.Scan(&result)
		if err != nil {
			return nil, fmt.Errorf("[ecuador.order.go]Error al obtener los datos del query %v: %s", query, err)
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
		return nil, fmt.Errorf("[ecuador.order.go]Error al ejecutar el query %v: %v", query, err)
	}
	defer rows.Close()
	for rows.Next() {
		var idControlStation *string
		err = rows.Scan(&idControlStation)
		if err != nil {
			return nil, fmt.Errorf("[ecuador.order.go]Error al obtener los datos del control estacion de kiosko: %s", err)
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
			return "", fmt.Errorf("[ecuador.order.go]Error al obtener el idMesa de kiosko %v: %s", query, errScan.Error())
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
			return nil, fmt.Errorf("[ecuador.order.go]Error al ejecutar el query %v: %v", query, err)
		}
		defer rows.Close()
		for rows.Next() {
			err = rows.Scan(&dataStation.IdStation, &dataStation.CashierName)
			if err != nil {
				return nil, fmt.Errorf("[ecuador.order.go]Error al obtener los datos de estacion del kiosko: %v", err)
			}
		}
		if dataStation.IdStation == nil && dataStation.CashierName == nil {
			return nil, fmt.Errorf("[ecuador.order.go]Error, no se encontro el id y el nombre del cajero de kiosko, por favor validar")
		}
		cacheStation.Set(ipKiosko, dataStation, 1*time.Hour)
	}
	return dataStation, nil
}
func (o *OrderStore) GetRestaurantCategory(connection *sqlserver.DatabaseSql) (string, error) {
	row := connection.QueryRow("select rst_categoria from restaurante")
	var restaurantCategory string
	err := row.Scan(&restaurantCategory)
	if err != nil {
		return "", err
	}
	return restaurantCategory, nil

}

func (o *OrderStore) InsertDetailOrder(connection *sqlserver.DatabaseSql, data []*models.KioskoPlus, idCabeceraOrdenPedido string) error {
	isUpsell := o.Feature.GetConfigFeatureFlag(featureflag2.UPSELLING)

	dataDetailsKiosko := make([]*models.KioskoPlus, 0)
	currentDate := time.Now()

	getRestaurantCategory, err := o.GetRestaurantCategory(connection)
	if err != nil {
		return err
	}

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
			sql.Named("idCategoria", getRestaurantCategory))
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al ejecutar el query %v: %v", query, err)
		}

		for rows.Next() {
			var dataProduct models.KioskoPlus
			var pluId *int32
			err = rows.Scan(&pluId, &dataProduct.ValorNeto, &dataProduct.ValorIva, &dataProduct.ValorBruto)
			if err != nil {
				rows.Close()
				return fmt.Errorf("[ecuador.order.go]Error al obtener los pluId de los productos: %v", err)
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

		// logica de procesar_precio_integracion
		isPpi := o.Feature.GetConfigFeatureFlag(featureflag2.PROCESAR_PRECIO_INTEGRACION)

		var subTotal, iva, total float64
		if !isPpi {
			getRestaurantCategory, errCat := o.GetRestaurantCategory(connection)
			if errCat != nil {
				return errCat
			}

			query := `select pr_valor_neto, pr_valor_iva, pr_pvp from Precio_Plu where plu_id = @pluId and idCategoria = @idCategoria`

			var valorNeto, valorIva, pvp float64
			row := connection.QueryRow(query, sql.Named("pluId", detailsInsert.PluId), sql.Named("idCategoria", getRestaurantCategory))
			errScan := row.Scan(&valorNeto, &valorIva, &pvp)
			if errScan != nil {
				if errors.Is(errScan, sql.ErrNoRows) {
					return fmt.Errorf("[ecuador.order.go] producto con plu_id %v no encontrado en Precio_Plu", detailsInsert.PluId)
				}
				return fmt.Errorf("[ecuador.order.go] error al escanear precios para el producto %v: %w", detailsInsert.PluId, errScan)
			}

			subTotal += valorNeto
			iva += valorIva
			total += pvp
		} else {

			// tax
			stot := o.Order.PaymentInfo.SubTotal
			tiva := o.Order.PaymentInfo.Iva

			// Convertir subtotal e iva (strings) a float64 antes de calcular el porcentaje de impuesto
			var tax float64
			if stotVal, err := strconv.ParseFloat(stot, 64); err == nil && stotVal != 0 {
				if tivaVal, err2 := strconv.ParseFloat(tiva, 64); err2 == nil {
					tax = (tivaVal / stotVal) * 100
				} else {
					// si no se puede parsear iva, dejar tax en 0
					tax = 0
				}
			} else {
				// si no se puede parsear subtotal o es 0, dejar tax en 0
				tax = 0
			}

			// PPI activado: usar los valores payload
			itemPrice, taxPercentage := findItemPrice(o.Order.Items.Product, detailsInsert.PluId)
			total = float64(itemPrice)
			total = utils.RoundToTwoDecimalPlaces(total) // Truncar total a 6 decimales antes de cálculos

			if taxPercentage > 0 {
				subTotal = total / (1 + float64(taxPercentage)/100)
				subTotal = utils.RoundToTwoDecimalPlaces(subTotal)
			} else {
				subTotal = total / (1 + float64(tax)/100)
				subTotal = utils.RoundToTwoDecimalPlaces(subTotal)
				iva = total - subTotal
			}

			//Truncamos el total a solo 2 decimales
			total = utils.TruncateFloatStrict(total, 2)
			subTotal = utils.TruncateFloatStrict(subTotal, 4)
			iva = utils.TruncateFloatStrict(iva, 5)

		}

		detailsOrder := sqlserver.NewSimpleInsertBuilderSQL(true, "dbo.Detalle_Orden_Pedido")
		detailsOrder.AddValue("idCabeceraOrdenPedido", idCabeceraOrdenPedido)
		detailsOrder.AddValue("plu_id", detailsInsert.PluId)
		detailsOrder.AddValue("dop_cantidad", detailsInsert.Quantity)
		detailsOrder.AddValue("dop_iva", iva)
		detailsOrder.AddValue("dop_precio", subTotal)
		detailsOrder.AddValue("dop_total", total)
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
			return fmt.Errorf("[ecuador.order.go]Error al insertar los datos en la tabla dbo.Detalle_Orden_Pedido %v: %v", detailsOrder.GetValues(), err)
		}
		if rows.Err() != nil {
			return fmt.Errorf("[ecuador.order.go]Error al iterar el rows: %v", rows.Err())
		}
		var idDetalleOrdenPedido string
		err = rows.Scan(&idDetalleOrdenPedido)
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al obtener el idDetalleOrdenpedido al momento de insertar los datos en la tabla dbo.Detalle_Orden_Pedido: %v", err)
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
				return fmt.Errorf("[ecuador.order.go]Error al actualizar los datos de la tabla Detalle_Orden_Pedido: %v", err)
			}
			rowsAfected, err := exec.RowsAffected()
			if err != nil {
				return fmt.Errorf("[ecuador.order.go]Error al identificar la actualizacion de los datos de la tabla Detalle_Orden_Pedido: %v", err)
			}
			if rowsAfected != 1 {
				return fmt.Errorf("[ecuador.order.go]No se actualizaron los registros de la tabla detalle_orden_pedido")
			}
		} else {
			exec, err := connection.Exec(updateDetails,
				sql.Named("isModifica", detailsInsert.Exchange),
				sql.Named("idDetalleOrdenPedido", detailsInsert.IdDetalleOrdenPedido),
				sql.Named("addSecond", i+1),
				sql.Named("idKioskoDetallePedido", idOrderToString),
				sql.Named("idCabeceraOrdenPedido", detailsInsert.IdCabeceraOrdenPedido))
			if err != nil {
				return fmt.Errorf("[ecuador.order.go]Error al actualizar los datos en la tabla Detalle_Orden_Pedido: %v", err)
			}
			rowsAfected, err := exec.RowsAffected()
			if err != nil {
				return fmt.Errorf("[ecuador.order.go]Error al identificar la actualizacion de los datos de la tabla Detalle_Orden_Pedido: %v", err)
			}
			if rowsAfected != 1 {
				return fmt.Errorf("[ecuador.order.go]No se actualizaron los registros de la tabla detalle_orden_pedido")
			}
		}

	}

	return nil

}

func findItemPrice(products []*lib_gen_proto.Product, pluId uint32) (price float32, tax float32) {
	for _, product := range products {
		if product.ProductId == pluId {
			return product.TotalPrice, product.TaxesPercentage
		}
	}
	return 0, 0
}

func (o *OrderStore) GetIdUserPos(connection *sqlserver.DatabaseSql, dataStation *models.StationKiosco) (*string, error) {
	query := fmt.Sprintf(`SELECT CAST(IDUsersPos AS VARCHAR(40)) FROM dbo.Users_Pos  WHERE  IDStatus IS NOT NULL AND Users_Pos.usr_usuario = @CashierName`)
	result, err := connection.Query(query, sql.Named("CashierName", dataStation.CashierName))
	if err != nil {
		return nil, fmt.Errorf("[ecuador.order.go]Error al ejecutar el query %v: %v", query, err)
	}
	defer result.Close()
	for result.Next() {
		var getIdUserPos string
		err = result.Scan(&getIdUserPos)
		if err != nil {
			return nil, fmt.Errorf("[ecuador.order.go]Error al obtener el id del Usuario %v: %v", dataStation.CashierName, err)
		}
		return &getIdUserPos, nil
	}
	return nil, nil
}
func (o *OrderStore) CheckIfAlreadyExistOrder(connection *sqlserver.DatabaseSql, codigoApp string) error {
	query := fmt.Sprintf(`SELECT count(*) as cantidad FROM dbo.kiosko_cabecera_pedidos WHERE codigo_app = @codigoApp`)
	result, err := connection.Query(query, sql.Named("codigoApp", codigoApp))
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al ejecutar el query %v: %v", query, err)
	}
	defer result.Close()
	for result.Next() {
		var countData int32
		err = result.Scan(&countData)
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al validar el codigoApp %v de la tabla kiosko_cabecera_pedidos: %v", codigoApp, err)
		}
		if countData > 0 {
			return fmt.Errorf("[ecuador.order.go]El codigo %v ya se encuentra registrado, por favor ingresar uno nuevo", codigoApp)
		}
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

	isPpi := o.Feature.GetConfigFeatureFlag(featureflag2.PROCESAR_PRECIO_INTEGRACION)
	var subTotal, iva, total float64
	var err error

	if !isPpi {
		getRestaurantCategory, errCat := o.GetRestaurantCategory(connection)
		if errCat != nil {
			return -1, nil, errCat
		}

		query := `select pr_valor_neto, pr_valor_iva, pr_pvp from Precio_Plu where plu_id = @pluId and idCategoria = @idCategoria`

		for _, item := range o.Order.Items.Product {
			var valorNeto, valorIva, pvp float64
			row := connection.QueryRow(query, sql.Named("pluId", item.ProductId), sql.Named("idCategoria", getRestaurantCategory))
			errScan := row.Scan(&valorNeto, &valorIva, &pvp)
			if errScan != nil {
				if errors.Is(errScan, sql.ErrNoRows) {
					return -1, nil, fmt.Errorf("[ecuador.order.go] producto con plu_id %v no encontrado en Precio_Plu", item.ProductId)
				}
				return -1, nil, fmt.Errorf("[ecuador.order.go] error al escanear precios para el producto %v: %w", item.ProductId, errScan)
			}
			subTotal += valorNeto * float64(item.Quantity)
			iva += valorIva * float64(item.Quantity)
			total += pvp * float64(item.Quantity)

			for _, modifier := range item.ModifierGroups {
				var modValorNeto, modValorIva, modPvp float64
				rowMod := connection.QueryRow(query, sql.Named("pluId", modifier.ProductId), sql.Named("idCategoria", getRestaurantCategory))
				errScanMod := rowMod.Scan(&modValorNeto, &modValorIva, &modPvp)
				if errScanMod != nil {
					if errors.Is(errScanMod, sql.ErrNoRows) {
						return -1, nil, fmt.Errorf("[ecuador.order.go] modificador con plu_id %v no encontrado en Precio_Plu", modifier.ProductId)
					}
					return -1, nil, fmt.Errorf("[ecuador.order.go] error al escanear precios para el modificador %v: %w", modifier.ProductId, errScanMod)
				}
				subTotal += modValorNeto * float64(modifier.Quantity)
				iva += modValorIva * float64(modifier.Quantity)
				total += modPvp * float64(modifier.Quantity)
			}
		}
	} else {
		subTotal, err = strconv.ParseFloat(o.Order.PaymentInfo.SubTotal, 64)
		if err != nil {
			return -1, nil, fmt.Errorf("[ecuador.order.go] error al convertir subtotal '%s' a float64: %w", o.Order.PaymentInfo.SubTotal, err)
		}
		iva, err = strconv.ParseFloat(o.Order.PaymentInfo.Iva, 64)
		if err != nil {
			return -1, nil, fmt.Errorf("[ecuador.order.go] error al convertir iva '%s' a float64: %w", o.Order.PaymentInfo.Iva, err)
		}
		total, err = strconv.ParseFloat(o.Order.PaymentInfo.Total, 64)
		if err != nil {
			return -1, nil, fmt.Errorf("[ecuador.order.go] error al convertir total '%s' a float64: %w", o.Order.PaymentInfo.Total, err)
		}
	}
	kioskoHeader := sqlserver.NewSimpleInsertBuilderSQL(true, "dbo.kiosko_cabecera_pedidos")
	kioskoHeader.AddValue("cli_nombres", o.Order.Cabecera.Client.Name)
	kioskoHeader.AddValue("IDTipoDocumento", o.Order.Cabecera.Client.DocumentId)
	kioskoHeader.AddValue("cli_documento", o.Order.Cabecera.Client.DocumentNumber)
	kioskoHeader.AddValue("cli_telefono", o.Order.Cabecera.Client.Phone)
	kioskoHeader.AddValue("cli_direccion", o.Order.Cabecera.Client.Address)
	kioskoHeader.AddValue("cli_email", o.Order.Cabecera.Client.Email)
	kioskoHeader.AddValue("cfac_subtotal", subTotal)
	kioskoHeader.AddValue("cfac_iva", iva)
	kioskoHeader.AddValue("cfac_total", total)
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
		return -1, nil, fmt.Errorf("[ecuador.order.go]Error al insertar los datos en la tabla dbo.kiosko_cabecera_pedidos: %v", err)
	}
	if rows.Err() != nil {
		return -1, nil, fmt.Errorf("[ecuador.order.go]Error al iterar las filas de la tabla dbo.kiosko_cabecera_pedidos: %v", rows.Err())
	}
	err = rows.Scan(&idOrder)
	if err != nil {
		return -1, nil, fmt.Errorf("[ecuador.order.go]Error al obtener el id de la tabla dbo.kiosko_cabecera_pedidos: %v", err.Error())
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
			return -1, nil, fmt.Errorf("[ecuador.order.go]Error al insertar los productos en la tabla dbo.kiosko_detalle_pedidos: %v", err)
		}
		if rowsDetails.Err() != nil {
			return -1, nil, fmt.Errorf("[ecuador.order.go]Error al iterar los datos de la tabla dbo.kiosko_detalle_pedidos: %v", rowsDetails.Err())
		}
		var detailsOrderId int32
		err = rowsDetails.Scan(&detailsOrderId)
		if err != nil {
			return -1, nil, fmt.Errorf("[ecuador.order.go]Error al obtener el identificador de la tabla dbo.kiosko_detalle_pedidos: %v", err.Error())
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
				return -1, nil, fmt.Errorf("[ecuador.order.go]Error al insertar los modificadores en la tabla dbo.kiosko_detalle_pedidos: %v", err)
			}
			var modifiersOrderId int32
			err = rowsModifiers.Scan(&modifiersOrderId)
			if err != nil {
				return -1, nil, fmt.Errorf("[ecuador.order.go]Error al obtener el id de la tabla dbo.kiosko_detalle_pedidos: %v", err.Error())
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
			return -1, nil, fmt.Errorf("[ecuador.order.go]Error al insertar los datos en la tabla dbo.kiosko_autorizaciones_switch: %v", err)
		}
	}

	//Insercion de forma Pago kiosko
	formaPagoTable := sqlserver.NewSimpleInsertBuilderSQL(false, "dbo.kiosko_forma_pagos")
	formaPagoTable.AddValue("idOrden", idOrder)
	formaPagoTable.AddValue("bin", o.Order.PaymentMethods.Bin)
	formaPagoTable.AddValue("fpf_total_pagar", total)
	formaPagoTable.AddValue("created_at", currentDate)
	formaPagoTable.AddValue("updated_at", currentDate)

	_, err = connection.IQuery(formaPagoTable.SqlGenerated(), formaPagoTable.GetValues())
	if err != nil {
		return -1, nil, fmt.Errorf("[ecuador.order.go]Error al insertar los datos en la tabla dbo.kiosko_forma_pagos: %v", err)
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
		return nil, nil, fmt.Errorf("[ecuador.order.go]Error al ejecutar el query %v: %v", query, err)
	}
	defer result.Close()
	for result.Next() {
		var idClient, docuement *string
		err = result.Scan(&idClient, &docuement)
		if err != nil {
			return nil, nil, fmt.Errorf("[ecuador.order.go]Error al validar los datos de Cliente: %v", err)
		}
		return idClient, docuement, nil
	}
	return nil, nil, nil
}
func (o *OrderStore) InsertPaymentMethodInvoicing(connection *sqlserver.DatabaseSql, cfacId, Bin, totalValue, OrderTotalValue, idUserPos string) error {
	newBin, _ := utils.StrToUint32(Bin)
	var idFormaPago *string
	currentDate := time.Now()
	query := fmt.Sprintf(`SELECT COUNT(DISTINCT (fp.IDFormapago))
					FROM PaisColeccionDeDatos pd WITH (NOLOCK)
					INNER JOIN Formapago fp WITH (NOLOCK) ON CAST(pd.idIntegracion AS VARCHAR(40)) = CAST(fp.IDFormapago AS VARCHAR(40))
					WHERE @bin BETWEEN min AND max
						AND fp.cdn_id = @cdn_id`)

	rowCount := connection.QueryRow(query, sql.Named("bin", newBin), sql.Named("cdn_id", o.StoreData.ChainId))
	err := rowCount.Err()
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al ejecutar el query %v: %v", query, err)
	}
	var count string
	err = rowCount.Scan(&count)
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al obtener la cantidad de los metodos de pago: %v", err)
	}
	if count == "1" {
		queryFormaPago := fmt.Sprintf(`SELECT DISTINCT (CAST(fp.IDFormapago AS VARCHAR(36)))
					FROM PaisColeccionDeDatos AS pd WITH (NOLOCK)
					INNER JOIN Formapago AS fp WITH (NOLOCK) ON CAST(pd.idIntegracion AS VARCHAR(40)) = CAST(fp.IDFormapago AS VARCHAR(40))
					WHERE @bin BETWEEN min AND max
						AND fp.cdn_id = @cdn_id`)
		rowFormaPago := connection.QueryRow(queryFormaPago, sql.Named("bin", newBin), sql.Named("cdn_id", o.StoreData.ChainId))
		err = rowFormaPago.Err()
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al ejecutar el query %v: %v", query, err)
		}
		err = rowFormaPago.Scan(&idFormaPago)
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al obtener el idFormaPago: %v", err)
		}
		if idFormaPago == nil {
			return fmt.Errorf("[ecuador.order.go]Error, no se encontro el id de la forma de pago para kiosko")
		}
	} else {
		queryFormaPago := fmt.Sprintf(`SELECT DISTINCT (CAST(fp.IDFormapago AS VARCHAR(36)))
					FROM PaisColeccionDeDatos AS pd WITH (NOLOCK)
					INNER JOIN Formapago AS fp WITH (NOLOCK) ON CAST(pd.idIntegracion AS VARCHAR(40)) = CAST(fp.IDFormapago AS VARCHAR(40))
					WHERE (min >= @bin AND pd.max <= @bin)
						AND fp.cdn_id = @cdn_id`)
		rowFormaPago := connection.QueryRow(queryFormaPago, sql.Named("bin", newBin), sql.Named("cdn_id", o.StoreData.ChainId))
		err = rowFormaPago.Err()
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al ejecutar el query %v: %v", query, err)
		}
		err = rowFormaPago.Scan(&idFormaPago)
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al obtener el idFormaPago: %v", err)
		}
		if idFormaPago == nil {
			return fmt.Errorf("[ecuador.order.go]Error, no se econtro el id de la forma de pago para kiosko")
		}
	}
	var dataTypeInvoicing *string
	queryTypeInvoicing := "SELECT facturacion.fn_coleccionFormaPago_tipoFacturacionImpresion(@idFormaPagoBin, @cdn_id)"
	rows, err := connection.Query(queryTypeInvoicing, sql.Named("idFormaPagoBin", idFormaPago), sql.Named("cdn_id", o.StoreData.ChainId))
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al ejecutar la funcion %v: %v", queryTypeInvoicing, err)
	}
	defer rows.Close()
	for rows.Next() {
		err = rows.Scan(&dataTypeInvoicing)
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al obtener los datos de la fn facturacion.fn_coleccionFormaPago_tipoFacturacionImpresion: %v", err)
		}
	}

	paymentMethodInvoicing := sqlserver.NewSimpleInsertBuilderSQL(false, "dbo.Formapago_Factura")
	paymentMethodInvoicing.AddValue("cfac_id", cfacId)
	paymentMethodInvoicing.AddValue("IDFormapago", idFormaPago)
	paymentMethodInvoicing.AddValue("fpf_num_seguridad", "")
	paymentMethodInvoicing.AddValue("fpf_valor_billete", totalValue)
	paymentMethodInvoicing.AddValue("fpf_total_pagar", OrderTotalValue)
	paymentMethodInvoicing.AddValue("fpf_propina", 0)
	paymentMethodInvoicing.AddValue("IDStatus", o.StoreData.Status.AnulacionActivo)
	paymentMethodInvoicing.AddValue("fpf_swt", o.StoreData.Status.IdPinpadUnired)
	paymentMethodInvoicing.AddValue("IDUsersPos", idUserPos)
	paymentMethodInvoicing.AddValue("fpf_date1", currentDate)
	paymentMethodInvoicing.AddValue("fpf_varchar1", o.Order.PaymentMethods.Card.Authorization)
	paymentMethodInvoicing.AddValue("fpf_varchar4", dataTypeInvoicing)
	_, err = connection.IQuery(paymentMethodInvoicing.SqlGenerated(), paymentMethodInvoicing.GetValues())
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al insertar los datos en la tabla dbo.Formapago_Factura: %v", err)
	}
	return nil
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
		return nil, fmt.Errorf("[ecuador.order.go]Error al ejectuar el sp [config].[IAE_Cliente] %v", err)
	}
	if !strings.EqualFold("U", accion) {
		defer rows.Close()
		for rows.Next() {
			var idClient *string
			err = rows.Scan(&idClient)
			if err != nil {
				return nil, fmt.Errorf("[ecuador.order.go]Error al obtener los datos del sp [config].[IAE_Cliente] %v", err)
			}
			return idClient, nil
		}
	}

	return nil, nil
}
func (o *OrderStore) ClientOptIn(connection *sqlserver.DatabaseSql) {
	clientOptin := clientoptin.NewClient(connection, o.StoreData)
	err := clientOptin.ClientOptIn(o.Order.Cabecera)
	if err != nil {
		logger.Error.Println(err)
	}
}
func (o *OrderStore) WsPrintService(
	connection *sqlserver.DatabaseSql,
	cfacId,
	IdOrderHeaderOrder,
	idUserPos string,
	dataOrderPrintFastFood []*models.KioskoImpresionOrdenPedidoFastFood,
	dataStation *models.StationKiosco) error {
	isAdvanceImpression := true
	//Llamo a al construcutor del apiService
	apiService := printservice.NewApiPrintService(connection, o.StoreData.RestaurantId)
	var resultPrint *string
	rstId := o.StoreData.RestaurantId
	query := fmt.Sprintf(`SELECT servicio_impresion FROM [config].[fn_ColeccionRestaurante_ServicioImpresion] (@idRestaurante, @idCadena, @idEstacion)`)
	rows, err := connection.Query(query, sql.Named("idRestaurante", rstId),
		sql.Named("idCadena", o.StoreData.ChainId),
		sql.Named("idEstacion", dataStation.IdStation))
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al ejecutar la funcion %v: %v", query, err)
	}
	defer rows.Close()
	for rows.Next() {
		err = rows.Scan(&resultPrint)
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al obtener los datos de la funcion %v: %v", query, err)
		}
	}
	if resultPrint == nil {
		return fmt.Errorf("[ecuador.order.go]error al obtner los datos de la impresora, por favor revisar")
	}
	//
	var printingServiceResponse models.PrintingServiceResponse
	err = json.Unmarshal([]byte(*resultPrint), &printingServiceResponse)
	if err != nil {
		return fmt.Errorf("[ecuador.order.go]Error al parsear los datos de %v: %v", resultPrint, err)
	}

	if strings.EqualFold("efectivo", o.Order.Cabecera.PaymentType) {
		//Validacion de impresion anticipada
		queryPolicies := fmt.Sprintf(`select [config].[fn_ColeccionRestaurante_ImpresionAnticipada] (%v)`, o.StoreData.RestaurantId)
		rowsPolicies, err := connection.Query(queryPolicies)
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]Error al ejecutar el query %v: %w", queryPolicies, err)
		}
		defer rowsPolicies.Close()

		var variableB *bool
		for rowsPolicies.Next() {
			err = rowsPolicies.Scan(&variableB)
			if err != nil {
				return fmt.Errorf("[ecuador.order.go]Error al obtener los datos del query %v: %w", queryPolicies, err)
			}
		}

		if variableB == nil {
			return errors.New("[ecuador.order.go]No existe politicas relacionadas a impresion anticipada")
		}

		isAdvanceImpression = *variableB

		//Actualizacion de la orden en la tabla detalle_orden_pedido
		update := "UPDATE dbo.Detalle_Orden_Pedido SET dop_impresion = 0 WHERE IDCabeceraOrdenPedido = @idCabeceraOrdenPedido and dop_cuenta = 1 and dop_anulacion = 1 and dop_impresion = -1"
		_, err = connection.Exec(update, sql.Named("idCabeceraOrdenPedido", IdOrderHeaderOrder))
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]No se pudo ejecutar el UPDATE %s con idCabeceraOrdenPedido:%s", update, IdOrderHeaderOrder)
		}
		update = "UPDATE dbo.Detalle_Orden_Pedido SET dop_impresion = -1 WHERE IDCabeceraOrdenPedido = @idCabeceraOrdenPedido and dop_cuenta = 1 and dop_anulacion = 1 and dop_impresion = 1"
		_, err = connection.Exec(update, sql.Named("idCabeceraOrdenPedido", IdOrderHeaderOrder))
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]No se pudo ejecutar el UPDATE %s con idCabeceraOrdenPedido:%s", update, IdOrderHeaderOrder)
		}
		update = "UPDATE dbo.Detalle_Orden_Pedido SET dop_estado = -1 WHERE IDCabeceraOrdenPedido = @idCabeceraOrdenPedido and dop_cuenta = 1 and dop_anulacion <> 0 and dop_impresion = 1"
		_, err = connection.Exec(update, sql.Named("idCabeceraOrdenPedido", IdOrderHeaderOrder))
		if err != nil {
			return fmt.Errorf("[ecuador.order.go]No se pudo ejecutar el UPDATE %s con idCabeceraOrdenPedido:%s", update, IdOrderHeaderOrder)
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
				return fmt.Errorf("[ecuador.order.go]Error al ejecutar el sp [pedido].[ORD_impresion_ordenpedido]: %v", err)
			}
			return nil
		} else {
			//Obtencion de datos para la impresion de orden
			dataOrderPrintFastFood = make([]*models.KioskoImpresionOrdenPedidoFastFood, 0)
			orderPrintFastFood := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[pedido].[ORD_impresion_ordenpedido]")
			orderPrintFastFood.AddValueParameterized("IDCabeceraOrdenPedido", IdOrderHeaderOrder)
			orderPrintFastFood.AddValueParameterized("IDUsersPos", idUserPos)
			orderPrintFastFood.AddValueParameterized("rst_id", rstId)
			orderPrintFastFood.AddValueParameterized("dop_cuenta", 1)
			orderPrintFastFood.AddValueParameterized("guardaOrden", 1)
			orderPrintFastFood.AddValueParameterized("todas", 0)
			rowOrderPrint, err := connection.IQuery(orderPrintFastFood.GetStoreProcedure(), orderPrintFastFood.GetValues())
			if err != nil {
				return fmt.Errorf("[ecuador.order.go]Error al ejecutar el sp [pedido].[ORD_impresion_ordenpedido]: %v", err)
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
					return fmt.Errorf("[ecuador.order.go]Error al obtener los datos para la impresion de la ordern de pedido :%v", err)
				}
				dataOrderPrintFastFood = append(dataOrderPrintFastFood, &dataOrderPrint)
			}
		}
	}

	//procesamiento por tarjeta
	if len(dataOrderPrintFastFood) > 0 {
		for _, orderPrint := range dataOrderPrintFastFood {
			if orderPrint.Confirmar != 1 {
				err = apiService.ApiPrint(printingServiceResponse, orderPrint, cfacId, IdOrderHeaderOrder, idUserPos, *dataStation.IdStation, isAdvanceImpression)
				if err != nil {
					return fmt.Errorf("[ecuador.order.go]Error al imprimir la orden del pedido: %v", err)
				}
			}

		}

	}

	return nil
}
func (o *OrderStore) SendKds(connection *sqlserver.DatabaseSql, idOrderHeaderOrderOrCfaID, method string) error {
	//Validar si el featureFlag de Kds esta activo
	isKds := o.Feature.GetConfigFeatureFlag(featureflag2.KDS)
	restaurantId := o.StoreData.RestaurantId
	if !isKds {
		return fmt.Errorf("[ecuador.send.kds]El featureFlag del kds esta inactivo")
	}

	//Variables
	getPoliciesKds := &models.DataPoliciesKds{}
	jsonData := &models.SendJsonKds{
		IdRestaurant: uint8(restaurantId),
		Method:       method,
		IdOrder:      idOrderHeaderOrderOrCfaID,
		Account:      -1,
	}

	policiesKds := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[dbo].[politica_kds]")
	policiesKds.AddValueParameterized("IDRestaurante", restaurantId)
	rows, err := connection.IQuery(policiesKds.GetStoreProcedure(), policiesKds.GetValues())
	if err != nil {
		return fmt.Errorf("[ecuador.send.kds]Error al ejecutar el sp [dbo].[politica_kds]: %v", err)
	}
	defer rows.Close()
	for rows.Next() {
		err = rows.Scan(&getPoliciesKds.Url, &getPoliciesKds.Intentos, &getPoliciesKds.Timeout, &getPoliciesKds.ApiKds, &getPoliciesKds.Url2)
		if err != nil {
			return fmt.Errorf("[ecuador.send.kds]Error al obtener los datos del sp [dbo].[politica_kds]: %v", err)
		}
	}
	if !utils.IsEmpty(getPoliciesKds.ApiKds) && *getPoliciesKds.ApiKds == 1 {
		if !utils.IsEmpty(getPoliciesKds.Url2) {
			// Configurar los headers
			headers := map[string]string{
				"Content-Type": "application/json",
				"Accept":       "application/json",
			}
			timeOut := time.Duration(*getPoliciesKds.Timeout) * time.Second
			// Crear un cliente HTTP con timeout
			client := &http.Client{
				Timeout: timeOut,
			}
			payload, err := json.Marshal(jsonData)
			if err != nil {
				return fmt.Errorf("[ecuador.send.kds]Error al convertir a JSON: %v", err)
			}

			for intento := 1; intento <= *getPoliciesKds.Intentos; intento++ {
				req, err := http.NewRequest("POST", *getPoliciesKds.Url2, bytes.NewBuffer(payload))
				if err != nil {
					return fmt.Errorf("[ecuador.send.kds]Error creando la solicitud: %v", err)
				}

				// Agregar los headers a la solicitud
				for key, value := range headers {
					req.Header.Set(key, value)
				}

				// Realizar la solicitud
				resp, err := client.Do(req)
				if err != nil {
					logger.Error.Printf("[ecuador.send.kds]Intento %d: Error en la solicitud: %v\n", intento, err)
					continue
				}
				defer resp.Body.Close()
				responseData, err := io.ReadAll(resp.Body)
				if err != nil {
					logger.Error.Printf("[ecuador.send.kds]Intento %d: Error leyendo la respuesta: %v\n", intento, err)
					continue
				}
				logger.Info.Printf("[ecuador.send.kds]Datos de respuesta del kds: %v", string(responseData))
				if resp.StatusCode == http.StatusOK || resp.StatusCode == http.StatusNotFound {
					return nil
				}
				logger.Error.Printf("[ecuador.send.kds]Intento %d: Código de estado no esperado: %d\n", intento, resp.StatusCode)
			}
			return nil
		}
		return nil
	}
	logger.Warning.Printf("[ecuador.send.kds]Advertencia, la politica para el envio de pedidos hacia el KDS no esta activa")
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
		return o.CreateOrderKioskEfectivo()
	}
	return o.CreateOrderKioskTarjeta()
}
