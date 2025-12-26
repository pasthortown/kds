package argentina

import (
	"database/sql"
	"encoding/json"
	"errors"
	"fmt"
	"lib-shared/protos/lib_gen_proto_folio"
	"lib-shared/utils"
	"lib-shared/utils/logger"
	"new-order-store/internals/entity/maxpoint"
	"new-order-store/internals/entity/models"
	"new-order-store/internals/infrastructure/grpc_foliador"
	"new-order-store/internals/infrastructure/sqlserver"
	"strconv"
	"strings"
)

type ElectronicBillingArg struct {
	connection      *sqlserver.DatabaseSql
	foliadorService *grpc_foliador.GrpcServiceFolio
	storeData       *maxpoint.StoreData
}

func NewElectronicBillingArg(
	connection *sqlserver.DatabaseSql,
	foliadorService *grpc_foliador.GrpcServiceFolio,
	storeData *maxpoint.StoreData,
) *ElectronicBillingArg {
	return &ElectronicBillingArg{
		connection:      connection,
		foliadorService: foliadorService,
		storeData:       storeData,
	}
}

func (e *ElectronicBillingArg) SendDataFolio(cfacId, CashierName, DocumentNumber, jsonInvoicing string) (*string, error) {
	documentoNro := "0"
	query := fmt.Sprintf(`select 
    				kcp.cfac_id,
				   kcp.cli_nombres,
				   CAST(td.IDTipoDocumento AS VARCHAR(40)),
				   td.tpdoc_codigo,
				   kcp.cli_documento,
				   kcp.cli_telefono,
				   kcp.cli_direccion,
				   kcp.cli_email,
				   kcp.cfac_subtotal,
				   kcp.cfac_iva,
				   kcp.cfac_total,
				   kcp.tipo_servicio,
				   kcp.est_ip,
				   kcp.estado_maxpoint 
			from kiosko_cabecera_pedidos kcp  WITH (NOLOCK)
				left join tipo_documento td  WITH (NOLOCK)on td.IDTipoDocumento = kcp.IDTipoDocumento 
			where cfac_id = @cfacId`)
	rows, err := e.connection.Query(query, sql.Named("cfacId", cfacId))
	if err != nil {
		return nil, fmt.Errorf("[argentina.folio.go]no se pudo ejecutare el QUERY %s con %s", query, cfacId)
	}
	orderHeaderKiosk := &models.KioskoCabeceraPedido{}
	defer rows.Close()
	for rows.Next() {
		err = rows.Scan(&orderHeaderKiosk.CfacId,
			&orderHeaderKiosk.ClientNames,
			&orderHeaderKiosk.IdTypeDocument,
			&orderHeaderKiosk.TpDocCodigo,
			&orderHeaderKiosk.ClientDocument,
			&orderHeaderKiosk.ClientPhone,
			&orderHeaderKiosk.ClientAddress,
			&orderHeaderKiosk.ClientEmail,
			&orderHeaderKiosk.Subtotal,
			&orderHeaderKiosk.Iva,
			&orderHeaderKiosk.Total,
			&orderHeaderKiosk.TypeService,
			&orderHeaderKiosk.IpStation,
			&orderHeaderKiosk.StatuusMaxpoint)
		if err != nil {
			return nil, fmt.Errorf("[argentina.folio.go]Error al obtener los resultados del QUERY %s con %s", query, cfacId)
		}
		if orderHeaderKiosk.IdTypeDocument == nil {
			return nil, fmt.Errorf("[argentina.folio.go]error - no se obtuvo el id del tipo de documento")
		}
	}
	if !strings.Contains("9999999999", *orderHeaderKiosk.ClientDocument) {
		documentoNro = DocumentNumber
	}
	//Se obtiene los valores del pedido
	cfacTotal, _ := orderHeaderKiosk.GetTotal()
	if cfacTotal == 0 {
		return nil, fmt.Errorf("[argentina.folio.go]error - el total del pedido es 0")
	}
	cfacSubtotal, _ := orderHeaderKiosk.GetSubtotal()
	if cfacSubtotal == 0 {
		return nil, fmt.Errorf("[argentina.folio.go]error - el subtotal del pedido es 0")
	}

	cfacIva, err := orderHeaderKiosk.GetIva()
	if cfacIva == 0 {
		return nil, fmt.Errorf("[argentina.folio.go]error - el iva del pedido es 0")
	}
	rstId := strconv.Itoa(e.storeData.RestaurantId)
	//Generacion de dato para envio de datos al folio
	requestFeArg := &lib_gen_proto_folio.RequestFeArg{
		CdnId:           strconv.Itoa(e.storeData.ChainId),
		RstId:           rstId,
		IpKiosco:        *orderHeaderKiosk.IpStation,
		NombreCajero:    CashierName,
		CfacId:          *orderHeaderKiosk.CfacId,
		TipoEmision:     "F",
		TipoComprobante: "6",
		DocumentoNro:    documentoNro,
		ImporteTotal:    cfacTotal,
		ImporteNeto:     cfacSubtotal,
		ImporteExcento:  0,
		ImporteIVA:      cfacIva,
		TipoDocumento:   *orderHeaderKiosk.TpDocCodigo,
	}

	//
	responseFeArg, err := e.foliadorService.NewFolio(requestFeArg)
	if err != nil {
		return nil, fmt.Errorf("[folio.go]Error al tratar de procesar el pedido en el FE: %v", err.Error())
	}

	//Mapeo de los datos obtenidos del folio
	if responseFeArg == nil || utils.IsEmpty(responseFeArg.Data) {
		return nil, errors.New("[argentina.folio.go]Error, los datos del foliador estan vacios")
	}
	dataResponseFe := &models.ResponseFeArg{}
	err = json.Unmarshal([]byte(responseFeArg.Data), &dataResponseFe)
	if err != nil {
		return nil, fmt.Errorf("[argentina.folio.go]Error al convertir los datos obtenidos del foleador: %v", err)
	}
	//Actualizacion de los datos de la factura

	typeVoucher := "FACTURA B"
	toStringVoucherQuery, err := utils.InterfaceToString(dataResponseFe.VoucherQuery)
	if err != nil {
		return nil, fmt.Errorf("[argentina.folio.go]Error al convertir los datos del voucher en json: %v", err.Error())
	}
	toStringVoucherResponse, err := utils.InterfaceToString(dataResponseFe.VoucherResponse)
	if err != nil {
		return nil, fmt.Errorf("[argentina.folio.go]Error al convertir los datos del voucher en json: %v", err.Error())
	}
	var result string
	if dataResponseFe.Status == "OK" {
		authorizationNumber := dataResponseFe.VoucherQuery.TransactionNumber
		urlAfip := dataResponseFe.VoucherResponse["urlAfip"].(string)
		transactionNumber := dataResponseFe.VoucherResponse["nroTransaccion"].(string)
		_, resultOk := dataResponseFe.ConfirmationResponse["result"]
		if resultOk {
			result = dataResponseFe.ConfirmationResponse["result"].(string)
		}
		code := dataResponseFe.ConfirmationResponse["code"].(float64)
		//caeFechaVto := b.feRespon.VoucherResponse["caeFechaVto"].(string)
		pointSale := dataResponseFe.VoucherResponse["puntoVenta"].(float64)
		voucherNumber := dataResponseFe.VoucherResponse["numeroComprobante"].(float64)
		dataTypeVoucherResponse := dataResponseFe.VoucherResponse["cae"].(string)
		typeVoucherResponse := "CAE"
		numberVoucherResponse := dataResponseFe.VoucherResponse["cae"].(string)
		if dataTypeVoucherResponse == "" {
			typeVoucherResponse = "CAEA"
			numberVoucherResponse = dataResponseFe.VoucherResponse["caea"].(string)
		}
		err = e.InsertAuthorizationRequest(requestFeArg.CfacId, typeVoucher, "01", toStringVoucherQuery, "", requestFeArg.IpKiosco)
		if err != nil {
			return nil, err
		}
		varchar2 := "FE NotaCredito"
		if strings.EqualFold(requestFeArg.TipoEmision, "F") {
			varchar2 = "FE Factura"
		}
		err = e.InsertAuthorizationReponse(requestFeArg.CfacId, toStringVoucherResponse, toStringVoucherResponse, transactionNumber, varchar2, &authorizationNumber, &urlAfip, requestFeArg)
		if err != nil {
			return nil, err
		}
		err = e.InsertAuthorizationRequest(requestFeArg.CfacId, typeVoucher, "02", toStringVoucherQuery, "", requestFeArg.IpKiosco)
		if err != nil {
			return nil, err
		}
		varchar2 = "FE NotaCredito Comprobacion"
		if strings.EqualFold(requestFeArg.TipoEmision, "F") {
			varchar2 = "FE Factura Comprobacion"
		}
		err = e.InsertAuthorizationReponse(requestFeArg.CfacId, result, strconv.Itoa(int(code)), "0", varchar2, &authorizationNumber, nil, requestFeArg)
		if err != nil {
			return nil, err
		}
		err = e.UpdateInvoiceHeader(requestFeArg.CfacId, transactionNumber, "SUCCESS", urlAfip, typeVoucherResponse, numberVoucherResponse, "2024-24-11", strconv.Itoa(int(pointSale)), strconv.Itoa(int(voucherNumber)), requestFeArg.TipoEmision, 0)
		if err != nil {
			return nil, err
		}
	} else {
		return nil, fmt.Errorf("[argentina.folio.go]Error al actualizar los datos del foleador: %v", dataResponseFe.Message)
		/*err = e.InsertAuthorizationRequest(requestFeArg.CfacId, typeVoucher, "01", toStringVoucherQuery, "", requestFeArg.IpKiosco)
		if err != nil {
			return nil, err
		}
		varchar2 := "FE NotaCredito"
		if strings.EqualFold(requestFeArg.TipoEmision, "F") {
			varchar2 = "FE Factura"
		}
		err = e.InsertAuthorizationReponse(requestFeArg.CfacId, toStringVoucherResponse, toStringVoucherResponse, "0", varchar2, nil, nil, requestFeArg)
		if err != nil {
			return nil, err
		}
		if dataResponseFe.ConfirmationQuery != nil {
			_, resultOk := dataResponseFe.ConfirmationResponse["result"]
			if resultOk {
				result = dataResponseFe.ConfirmationResponse["result"].(string)
			}
			code := dataResponseFe.ConfirmationResponse["code"].(float64)
			err = e.InsertAuthorizationRequest(requestFeArg.CfacId, typeVoucher, "02", toStringVoucherQuery, "", requestFeArg.IpKiosco)
			if err != nil {
				return nil, err
			}
			varchar2 = "FE NotaCredito Comprobacion"
			if strings.EqualFold(requestFeArg.TipoEmision, "F") {
				varchar2 = "FE Factura Comprobacion"
			}
			err = e.InsertAuthorizationReponse(requestFeArg.CfacId, result, strconv.Itoa(int(code)), "0", varchar2, nil, nil, requestFeArg)
			if err != nil {
				return nil, err
			}
		}
		err = e.UpdateInvoiceHeader(requestFeArg.CfacId, "", "ERROR", "", "", "", "", "", "", requestFeArg.TipoEmision, 0)
		if err != nil {
			return nil, err
		}*/
	}

	//Mapeo de la informacion para ser enviado al kiosko
	numeroTurno := cfacId[len(cfacId)-2:]

	//Obtencion del numero de serie
	fnRstSerie := "select [config].[fn_CodigoRestaurante](@p1) as numero"
	rowRstSerie, err := e.connection.Query(fnRstSerie, sql.Named("p1", rstId))
	if err != nil {
		return nil, fmt.Errorf("[argentina.folio.go]Error al ejecutar la funcion %v: %v", fnRstSerie, err)
	}
	var numberSerie *string
	defer rowRstSerie.Close()
	for rowRstSerie.Next() {
		err = rowRstSerie.Scan(&numberSerie)
		if err != nil {
			return nil, fmt.Errorf("[argentina.folio.go]Error al obtener los datos de la funcion %v con parametro %v: %v", fnRstSerie, rstId, err)
		}
	}
	//Obtencion de datos de la tabla cabecera_factura
	queryInvoiceHeader := fmt.Sprintf(`select
    	cfac_iva,
		cfac_fechacreacion,
		Cabecera_FacturaVarchar10,
		Cabecera_FacturaVarchar7,
		fe_datosAdicionales,
		Cabecera_FacturaVarchar9,
		Cabecera_FacturaVarchar4,
		Cabecera_FacturaVarchar8 
	from cabecera_factura 
	where cfac_id = @cfacId`)
	rowsInvoiceHeader, err := e.connection.Query(queryInvoiceHeader, sql.Named("cfacId", cfacId))
	if err != nil {
		return nil, fmt.Errorf("[argentina.folio.go]Error al ejecutar el query %v con parametro %v: %v", queryInvoiceHeader, cfacId, err)
	}
	invoiceHeader := &models.CabeceraFactura{}
	defer rowsInvoiceHeader.Close()
	for rowsInvoiceHeader.Next() {
		err = rowsInvoiceHeader.Scan(
			&invoiceHeader.CfacIva,
			&invoiceHeader.CfacFechacreacion,
			&invoiceHeader.CabeceraFacturaVarchar10,
			&invoiceHeader.CabeceraFacturaVarchar7,
			&invoiceHeader.FeDatosAdicionales,
			&invoiceHeader.CabeceraFacturaVarchar9,
			&invoiceHeader.CabeceraFacturaVarchar4,
			&invoiceHeader.CabeceraFacturaVarchar8)
		if err != nil {
			return nil, fmt.Errorf("[argentina.folio.go]Error al obtener los datos del query %v con parametro %v: %v", queryInvoiceHeader, cfacId, err)
		}
	}

	//Obtencion de la forma de pago
	queryFormPayment := fmt.Sprintf(`select 
    	f.fmp_descripcion 
	from Formapago_Factura ff 
	    inner join Formapago f on f.IDFormapago = ff.IDFormapago 
	where cfac_id = @cfacID`)
	rowsFormPayment, err := e.connection.Query(queryFormPayment, sql.Named("cfacID", cfacId))
	if err != nil {
		return nil, fmt.Errorf("[argentina.folio.go]Error al ejecutar el query %v con parametro %v: %v", queryFormPayment, cfacId, err)
	}
	var formPayment *string
	defer rowsFormPayment.Close()
	for rowsFormPayment.Next() {
		err = rowsFormPayment.Scan(&formPayment)
		if err != nil {
			return nil, fmt.Errorf("[argentina.folio.go]Error al obtener los datos del query %v con parametro %v: %v", queryFormPayment, cfacId, err)
		}
	}
	queryCompany := "select emp_tipo_contribuyente as contribuyente from empresa"
	rowsCompany, err := e.connection.Query(queryCompany)
	if err != nil {
		return nil, fmt.Errorf("[argentina.folio.go]Error al ejecutar el query %v: %v", queryCompany, err)
	}
	var responsableInscrito *string
	defer rowsCompany.Close()
	for rowsCompany.Next() {
		err = rowsCompany.Scan(&responsableInscrito)
		if err != nil {
			return nil, fmt.Errorf("[argentina.folio.go]Error al obtener los datos del query %v: %v", queryCompany, err)
		}
	}

	fecha, hora := invoiceHeader.GetFechaHora()

	dataInterface, err := utils.StringToInterface(jsonInvoicing)
	if err != nil {
		return nil, fmt.Errorf("[argentina.folio.go]Error al convertir los datos: %v", err)
	}
	if dataInterfaceMap, ok := dataInterface.(map[string]interface{}); ok {
		//Se mapea los datos
		functionCompany := "SELECT [config].[fn_ObtenerDetalleTotalEmpresa]((SELECT emp_id FROM empresa)) as nombre"
		rowsFnCompany, err := e.connection.Query(functionCompany)
		if err != nil {
			return nil, fmt.Errorf("[argentina.folio.go]Error al ejecutar la funcion %v: %v", functionCompany, err)
		}
		var cfacEmpresa *string
		defer rowsFnCompany.Close()
		for rowsFnCompany.Next() {
			err = rowsFnCompany.Scan(&cfacEmpresa)
			if err != nil {
				return nil, fmt.Errorf("[argentina.folio.go]Error al obtener los datos de la funcion %v: %v", functionCompany, err)
			}
		}

		numeroFormateado := utils.NumberFormat(*invoiceHeader.CfacIva, 2, ",", ".")
		iva_contenido := "IVA Contenido: " + numeroFormateado
		//Ma responseFeArg
		iResponseFeArgMap := dataResponseFe.VoucherResponse
		//if dataFactura, ok := dataInterfaceMap["data"].(map[string]interface{}); ok {

		if details, ok := dataInterfaceMap["detalles"].(map[string]interface{}); ok {
			if detFormaPago, ok := details["det_formadepago"].(map[string]interface{}); ok {
				if valor0, ok := detFormaPago["valor0"].(map[string]interface{}); ok {
					valor0["valor"] = formPayment
				}
			}
		}
		if empresa, ok := dataInterfaceMap["empresa"].(map[string]interface{}); ok {
			if iResponseFeArgMap != nil {
				empresa["puntoVenta"] = iResponseFeArgMap["puntoVenta"]
				empresa["cae"] = "CAE"
				numeroComprobante := fmt.Sprint(iResponseFeArgMap["numeroComprobante"])
				empresa["numeroComprobante"] = fmt.Sprintf("%08s", numeroComprobante)
				if _, ok := empresa["caeFechaVto"].(map[string]interface{}); ok {
					empresa["caeFechaVto"] = iResponseFeArgMap["caeFechaVto"]
				}
				numeroCae := iResponseFeArgMap["cae"]
				if numeroCae != "" {
					empresa["numeroCAE"] = iResponseFeArgMap["cae"]
				} else {
					empresa["numeroCAE"] = iResponseFeArgMap["caea"]
				}
			} else {
				empresa["cae"] = "CAEA"
			}
			empresa["tipoFactura"] = invoiceHeader.FeDatosAdicionales
			empresa["tipo_comprobante"] = "COD. 006"
			empresa["numeroSerie"] = *numberSerie
			empresa["numeroturno"] = fmt.Sprintf("N° DE TURNO: %s", numeroTurno)
			empresa["fecha"] = *fecha
			empresa["hora"] = *hora
			empresa["contribuyente"] = *responsableInscrito
			empresa["numeroFactura"] = cfacId
			empresa["legislacion"] = cfacEmpresa
			empresa["Iva_contenido"] = iva_contenido
		}
		if pie, ok := dataInterfaceMap["pie"].(map[string]interface{}); ok {
			if valor3, ok := pie["valor3"].(map[string]interface{}); ok {
				valor3["valor"] = iResponseFeArgMap["urlAfip"]
			}
		}

		jsonInvoicing, err = utils.InterfaceToString(dataInterfaceMap)
		if err != nil {
			return nil, errors.New("[argentina.folio.go]Error al convertir un interface{} en string")
		}
		return &jsonInvoicing, nil
	}
	return nil, fmt.Errorf("[argentina.folio.go]Error, no se pudo convertir el interface a map[string]interface{}: %v", err)
}

func (e *ElectronicBillingArg) InsertAuthorizationRequest(creditNoteId, typeVoucher, typeCollection, dataVoucher, UrlFE, ipKiosko string) error {
	authorizationRequest := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[IAE_SWT_InsertaRequerimientoAutorizacion]")
	authorizationRequest.AddValueParameterized("paramIp", ipKiosko)
	authorizationRequest.AddValueParameterized("paramTrama", dataVoucher)
	authorizationRequest.AddValueParameterized("paramCfacId", creditNoteId)
	authorizationRequest.AddValueParameterized("paramTipoCobro", typeCollection)
	authorizationRequest.AddValueParameterized("paramUrlConsumo", UrlFE)
	authorizationRequest.AddValueParameterized("paramMonto", 0)
	authorizationRequest.AddValueParameterized("paramTipoFactura", typeVoucher)
	authorizationRequest.AddValueParameterized("paramAfiArg", dataVoucher)
	_, err := e.connection.IQuery(authorizationRequest.GetStoreProcedure(), authorizationRequest.GetValues())
	if err != nil {
		return fmt.Errorf("error al ejecutar el sp [facturacion].[IAE_SWT_InsertaRequerimientoAutorizacion] - %v", err.Error())
	}
	logger.OK.Println("El sp [facturacion].[IAE_SWT_InsertaRequerimientoAutorizacion] se ejecuto correctamente")
	return nil
}

func (e *ElectronicBillingArg) InsertAuthorizationReponse(creditNoteId, autTrama, operationNumber, transactionNumber, varchar2 string, authorizationNumber, urlAfip *string, requestFeArg *lib_gen_proto_folio.RequestFeArg) error {
	authorizationResponse := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[config].[USP_SWT_Respuesta_Autorizacion]")
	authorizationResponse.AddValueParameterized("ip", requestFeArg.IpKiosco)
	authorizationResponse.AddValueParameterized("fac_id", creditNoteId)
	authorizationResponse.AddValueParameterized("user_id", requestFeArg.NombreCajero)
	authorizationResponse.AddValueParameterized("rsaut_trama", autTrama)
	authorizationResponse.AddValueParameterized("Varchar9", "Procesado desde el api kiosko")
	authorizationResponse.AddValueParameterized("Varchar10", "")
	authorizationResponse.AddValueParameterized("aut_fecha_autorizacion", "")
	authorizationResponse.AddValueParameterized("aut_hora_autorizacion", "")
	authorizationResponse.AddValueParameterized("glosaRespuesta", operationNumber)
	authorizationResponse.AddValueParameterized("monto", 0)
	authorizationResponse.AddValueParameterized("numeroOperacion", authorizationNumber)
	authorizationResponse.AddValueParameterized("codigoAutorizacion", authorizationNumber)
	authorizationResponse.AddValueParameterized("codigoAutorizador", authorizationNumber)
	authorizationResponse.AddValueParameterized("cadena", requestFeArg.CdnId)
	authorizationResponse.AddValueParameterized("rest", requestFeArg.RstId)
	authorizationResponse.AddValueParameterized("Varchar6", urlAfip)
	authorizationResponse.AddValueParameterized("Varchar2", varchar2)
	authorizationResponse.AddValueParameterized("rsaut_numero_autorizacion", transactionNumber)
	_, err := e.connection.IQuery(authorizationResponse.GetStoreProcedure(), authorizationResponse.GetValues())
	if err != nil {
		return fmt.Errorf("error al ejecutar el sp [config].[USP_SWT_Respuesta_Autorizacion] - %v", err.Error())
	}

	logger.OK.Println("El sp [config].[USP_SWT_Respuesta_Autorizacion] se ejecuto correctamente")
	return nil
}

func (e *ElectronicBillingArg) UpdateInvoiceHeader(lcFactId, lcNumBoleta, lcEstado, Varchar6, Varchar7, Varchar8, Varchar9, Varchar10, Varchar4, generador string, int1 uint16) error {
	updateInvoiceHeader := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[facturacion].[Actualizar_Cabecera_Factura_Argentina]")
	updateInvoiceHeader.AddValueParameterized("cfac_id", lcFactId)
	updateInvoiceHeader.AddValueParameterized("numero_boleta", lcNumBoleta)
	updateInvoiceHeader.AddValueParameterized("estado", lcEstado)
	updateInvoiceHeader.AddValueParameterized("Varchar6", Varchar6)
	updateInvoiceHeader.AddValueParameterized("Varchar7", Varchar7)
	updateInvoiceHeader.AddValueParameterized("Varchar8", Varchar8)
	updateInvoiceHeader.AddValueParameterized("Varchar9", Varchar9)
	updateInvoiceHeader.AddValueParameterized("Varchar10", Varchar10)
	updateInvoiceHeader.AddValueParameterized("Varchar4", Varchar4)
	updateInvoiceHeader.AddValueParameterized("generador", generador)
	updateInvoiceHeader.AddValueParameterized("int1", int1)

	_, err := e.connection.IQuery(updateInvoiceHeader.GetStoreProcedure(), updateInvoiceHeader.GetValues())
	if err != nil {
		return fmt.Errorf("error al ejecutar el sp [facturacion].[Actualizar_Cabecera_Factura_Argentina] - %v", err.Error())
	}
	logger.OK.Println("El sp [facturacion].[Actualizar_Cabecera_Factura_Argentina] se ejecuto correctamente")
	return nil
}
