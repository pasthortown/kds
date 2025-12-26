package printservice

import (
	"bytes"
	"encoding/base64"
	"fmt"
	"github.com/skip2/go-qrcode"
	"io"
	"lib-shared/utils"
	"lib-shared/utils/logger"
	"log"
	"net/http"
	"new-order-store/internals/entity/models"
	"new-order-store/internals/infrastructure/sqlserver"
	"strings"
	"time"
)

type policyResponse struct {
	parameter string
	valor     string
}

type ApiPrintService struct {
	connection *sqlserver.DatabaseSql
	rstId      int
}

func NewApiPrintService(connection *sqlserver.DatabaseSql, rstId int) *ApiPrintService {
	return &ApiPrintService{
		connection: connection,
		rstId:      rstId,
	}
}

func (o *ApiPrintService) ApiPrint(dataOrderPrint []*models.KioskoImpresionOrdenPedidoFastFood,
	cfacId,
	IdOrderHeaderOrder,
	idUserPos,
	idStation string) error {
	logger.Debug("Init Print Chile")
	for _, dataPrint := range dataOrderPrint {
		request := &models.PrintRequest{
			CfacId:             cfacId,
			IdOrderHeaderOrder: IdOrderHeaderOrder,
			IdStation:          idStation,
			IdUserPos:          idUserPos,
		}
		idBrand, err := o.printerTypeDescription(dataPrint.Impresora)
		if err != nil {
			return err
		}
		request.IdBrand = idBrand
		request.TypeDocument = *dataPrint.Tipo
		request.NumberPrint = dataPrint.NumeroImpresiones
		request.Printer = dataPrint.Impresora
		request.JsonData = *dataPrint.JsonData
		request.JsonRegistros = *dataPrint.JsonRegistros
		request.FormatoXML = *dataPrint.FormatoXML
		if err != nil {
			logger.DebugItem.Printf("[chile.go]Error al obtener la descripcion de la impresora de pedido: %s\n", err)
			return fmt.Errorf("[chile.go]Error al obtener la descripcion de la impresora de pedido: %s\n", err)
		}
		if request.IdBrand == nil {
			logger.DebugItem.Printf("[chile.go]Error no existen datos en la descripcion de la impresora de pedido\n")
			return fmt.Errorf("[chile.go]Error no existen datos en la descripcion de la impresora de pedido\n")
		}
		jsonData, _ := utils.ConvertirStringAMap(request.JsonData)
		jsonRegistros, _ := utils.StringToInterface(request.JsonRegistros)
		// Generar QR si aplica
		qrKeys := o.searchQRKeys(jsonData)
		for _, key := range qrKeys {
			if contentQR, hasContentQR := jsonData[key].(string); hasContentQR {
				qrCode, err := o.generateQR(contentQR)
				if err != nil {
					logger.Error.Printf("Error generando QR: %v", err)
					break
				}
				jsonData[key] = qrCode
			} else {
				break
			}

		}
		var replaceOrdenVcocina string
		var Orden string
		var lvCfacId string
		var medio string
		var numeroCuenta string
		var mesa string
		var fechaCreacion string
		if val, ok := jsonData["replace_orden_vcocina"].(string); ok {
			replaceOrdenVcocina = val
		}
		if val, ok := jsonData["orden"].(string); ok {
			Orden = val
		}
		if val, ok := jsonData["cfac_id"].(string); ok {
			lvCfacId = val
		}
		if val, ok := jsonData["medio"].(string); ok {
			medio = val
		}
		if val, ok := jsonData["numeroCuenta"].(string); ok {
			numeroCuenta = val
		}
		if val, ok := jsonData["mesa"].(string); ok {
			mesa = val
		}
		if val, ok := jsonData["odp_fecha_creacion"].(string); ok {
			fechaCreacion = val
		}
		request.FormatoXML = validateTemplateOrder(request.FormatoXML, replaceOrdenVcocina, Orden, lvCfacId, medio, numeroCuenta, mesa, fechaCreacion)
		if strings.Contains(*request.Printer, "VCOCINA") {
			if val, ok := jsonData["usr_usuario"].(string); ok {
				jsonData["usr_usuario"] = "<bkgrn>Cajero/a: " + val + "</bkgrn>"
			}
			if val, ok := jsonData["medio"].(string); ok {
				jsonData["medio"] = "<bkgrn>Medio: " + val + "</bkgrn>"
			}
			request.FormatoXML = validateTemplateOrderVCOCINA(request.FormatoXML)
		}

		// Generar QR si aplica

		dataPost := &models.DataPost{
			IdMarca:        *request.IdBrand,
			IdImpresora:    request.Printer,
			AplicaBalanceo: "0",
			IdPlantilla:    request.FormatoXML,
			Data:           jsonData,
			Registros:      jsonRegistros,
		}
		dataPolicyRestaurant, err := o.getPolicyRestaurantId()
		if err != nil {
			return err
		}
		ipPrint, err := o.GetIpPrint(request.Printer)
		if err != nil {
			return err
		}
		serviceRoute := o.getPolicy(dataPolicyRestaurant, "RUTA_SERVICIO")
		timeOut := o.getPolicy(dataPolicyRestaurant, "TIMEOUT")
		retries := o.getPolicy(dataPolicyRestaurant, "REINTENTOS")
		timeOutInt, _ := utils.StringToInt(timeOut)
		retriesInt, _ := utils.StringToInt(retries)
		if retriesInt == 0 {
			retriesInt = 1
		}
		urlApiImpresion := *ipPrint + serviceRoute
		o.consumePrinterRequest(
			urlApiImpresion,
			timeOutInt,
			dataPost.ToString(),
			request.TypeDocument,
			retriesInt,
			*dataPost.IdImpresora,
			idUserPos,
			idStation,
		)
	}
	logger.Debug("finalized Print Chile")
	return nil
}

func (o *ApiPrintService) printerTypeDescription(printer *string) (*string, error) {
	var dataValue *string

	dataValueRow := o.connection.QueryRow(fmt.Sprintf("SELECT [impresion].[fnServicioApiImpresionObtenerDescripcionTipoImpresora]('%v')", *printer))
	err := dataValueRow.Scan(&dataValue)
	if err != nil {
		return nil, err
	}
	if dataValue == nil {
		return nil, fmt.Errorf("[servicio.impresion.go]No se encontró datos para la query: SELECT [impresion].[fnServicioApiImpresionObtenerDescripcionTipoImpresora]('%v')\n", printer)
	}
	logger.DebugItem.Print(fmt.Sprintf(*printer))
	return dataValue, nil
}

func (o *ApiPrintService) searchQRKeys(data map[string]interface{}) []string {
	llavesEncontradas := make([]string, 0)
	// Manejo de errores dentro del bloque
	defer func() {
		if r := recover(); r != nil {
			logger.Info.Println("Error en BuscarLlavesInicio:", r)
		}
	}()
	// Iterar sobre el mapa
	for llave := range data {
		if strings.HasPrefix(llave, "imagenqr_") { // Verifica si la llave comienza con el prefijo
			llavesEncontradas = append(llavesEncontradas, llave)
		}
	}
	return llavesEncontradas
}

func (o *ApiPrintService) generateQR(content string) (string, error) {
	qr, err := qrcode.New(content, qrcode.Medium)
	if err != nil {
		return "", err
	}
	png, err := qr.PNG(256)
	if err != nil {
		return "", err
	}
	return base64.StdEncoding.EncodeToString(png), nil
}

func (o *ApiPrintService) GetIpPrint(printer *string) (*string, error) {
	var ip *string
	query := fmt.Sprintf("SELECT [impresion].[fn_ColeccionRestaurante_ServicioApiImpresionUrl]('%v')\n", *printer)
	if printer == nil {
		query = "SELECT [impresion].[fn_ColeccionRestaurante_ServicioApiImpresionUrl](null)"
	}
	dataValueRow := o.connection.QueryRow(query)
	err := dataValueRow.Scan(&ip)
	if err != nil {
		return nil, err
	}

	return ip, nil
}

func (o *ApiPrintService) getPolicyRestaurantId() ([]*policyResponse, error) {
	dataResponse := make([]*policyResponse, 0)
	query := fmt.Sprintf("SELECT * FROM [config].[fn_ColeccionRest_ServicioImpresion]('%v')", o.rstId)

	rows, err := o.connection.Query(query)
	if err != nil {
		return nil, fmt.Errorf("[chile.go]Error al ejecutar el query %v: %v", query, err.Error())
	}
	defer rows.Close()
	for rows.Next() {
		data := &policyResponse{}
		err = rows.Scan(&data.parameter, &data.valor)
		if err != nil {
			return nil, fmt.Errorf("[chile.go]Error al obtener los datos del query %v: %v", query, err.Error())
		}
		dataResponse = append(dataResponse, data)
	}
	return dataResponse, nil
}

func (o *ApiPrintService) getPolicy(policies []*policyResponse, parameter string) string {
	for _, policy := range policies {
		if policy.parameter == parameter {
			return policy.valor
		}
	}
	return ""
}

func (o *ApiPrintService) consumePrinterRequest(
	urlApiImpresion string,
	timeout int,
	dataString string,
	tipoDocumento string,
	reintentos int,
	idImpresora string,
	idUserPos,
	idStation string,
) {
	client := &http.Client{
		Timeout: time.Duration(timeout) * time.Second,
	}

	var responseBody string
	var statusCode int

	for intento := 1; intento <= reintentos; intento++ {
		resp, err := client.Post(urlApiImpresion, "application/json", bytes.NewBuffer([]byte(dataString)))
		if err != nil {
			logger.Error.Printf("Error en intento %d: %v", intento, err)
			if intento == reintentos {
				o.insertAudit(tipoDocumento, "ERROR", idImpresora, idStation, idUserPos, urlApiImpresion, dataString, err.Error())
			}
			continue
		}

		bodyBytes, readErr := io.ReadAll(resp.Body)
		resp.Body.Close()
		if readErr != nil {
			log.Printf("Error leyendo la respuesta: %v", readErr)
			err = readErr
			continue
		}

		responseBody = string(bodyBytes)
		statusCode = resp.StatusCode

		if statusCode == 200 || statusCode == 404 {
			o.insertAudit(
				tipoDocumento,
				"SUCCESS",
				idImpresora,
				idStation,
				idUserPos,
				urlApiImpresion,
				dataString,
				responseBody,
			)
			break
		}

		if intento == reintentos {
			o.insertAudit(
				tipoDocumento,
				"ERROR",
				idImpresora,
				idStation,
				idUserPos,
				urlApiImpresion,
				dataString,
				responseBody,
			)
		}
	}
}

func (o *ApiPrintService) insertAudit(typeDoc, status, printer, idStation, idUserPos, url, payload, response string) {
	if strings.EqualFold("ERROR CONEXION API NET CORE", status) {
		newResponse := "false"
		response = newResponse
	}

	insert := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[impresion].[IAE_AuditoriaApiImpresion]")
	insert.AddValueParameterized("tipo", &typeDoc)
	insert.AddValueParameterized("estado", &status)
	insert.AddValueParameterized("impresora", &printer)
	insert.AddValueParameterized("idEstacion", &idStation)
	insert.AddValueParameterized("idUsuario", &idUserPos)
	insert.AddValueParameterized("url", &url)
	insert.AddValueParameterized("payload", &payload)
	insert.AddValueParameterized("response", &response)

	_, err := o.connection.IQuery(insert.GetStoreProcedure(), insert.GetValues())
	if err != nil {
		logger.Error.Println("Error al ejecutar el sp [impresion].[IAE_AuditoriaApiImpresion]: ", err)
	}
}

func validateTemplateOrder(templateXML, replaceOrdenVcocina, orden, cfacID, medio, numeroCuenta, mesa, fechaCreacion string) string {

	if replaceOrdenVcocina == "" {
		templateXML = strings.ReplaceAll(templateXML, `<parametro estilo=\"bold\" alineacion=\"izquierda\">replace_orden_vcocina</parametro><salto/>`, "")
	}

	if orden == "" {
		templateXML = strings.ReplaceAll(templateXML, `<etiqueta estilo=\"bold\" alineacion=\"izquierda\">TRANSACCION #: </etiqueta><parametro estilo=\"bold\" sizeToMultiply=\"1\" alineacion=\"izquierda\">orden</parametro><salto/>`, "")
	}

	if cfacID == "" {
		templateXML = strings.ReplaceAll(templateXML, `<salto/><etiqueta estilo=\"default\" alineacion=\"izquierda\">FACTURA: </etiqueta><parametro estilo=\"default\" alineacion=\"izquierda\">cfac_id</parametro>`, "")
	}

	if medio == "" {
		templateXML = strings.ReplaceAll(templateXML, `<salto/><etiqueta estilo=\"default\" alineacion=\"izquierda\">MEDIO: </etiqueta><parametro estilo=\"default\" alineacion=\"izquierda\">medio</parametro>`, "")
	}

	if numeroCuenta == "" {
		templateXML = strings.ReplaceAll(templateXML, `<salto/><etiqueta estilo=\"default\" alineacion=\"izquierda\"># CUENTA: </etiqueta><parametro estilo=\"default\" alineacion=\"izquierda\">numeroCuenta</parametro>`, "")
	}

	if mesa == "" {
		templateXML = strings.ReplaceAll(templateXML, `<salto/><etiqueta estilo=\"bold\" alineacion=\"izquierda\">MESA: </etiqueta><parametro estilo=\"bold\" alineacion=\"izquierda\">mesa</parametro>`, "")
	}

	if fechaCreacion == "" {
		templateXML = strings.ReplaceAll(templateXML, `<salto/><etiqueta estilo=\"default\" alineacion=\"izquierda\">FECHA: </etiqueta><parametro estilo=\"default\" alineacion=\"izquierda\">odp_fecha_creacion</parametro>`, "")
	}

	return templateXML
}

func validateTemplateOrderVCOCINA(templateXMLVCOCINA string) string {
	// Reducir tamaño del producto
	templateXMLVCOCINA = strings.ReplaceAll(templateXMLVCOCINA, `<item alineacion=\"izquierda\" tamano=\"34\">Producto</item>`, `<item alineacion=\"izquierda\" tamano=\"28\">Producto</item>`)

	// Ocultar separadores
	templateXMLVCOCINA = strings.ReplaceAll(templateXMLVCOCINA, `<separador negrita=\"no\" alineacion=\"izquierda\">-</separador>`, "")

	// Ocultar nombre columnas
	templateXMLVCOCINA = strings.ReplaceAll(templateXMLVCOCINA, `<etiqueta negrita=\"si\" alineacion=\"izquierda\">CANT.   DESCRIPCION</etiqueta><salto/>`, "")

	// Ocultar medio y usuario
	templateXMLVCOCINA = strings.ReplaceAll(templateXMLVCOCINA, `<salto/><etiqueta estilo=\"default\" alineacion=\"izquierda\">MEDIO: </etiqueta><parametro estilo=\"default\" alineacion=\"izquierda\">medio</parametro>`, "")
	templateXMLVCOCINA = strings.ReplaceAll(templateXMLVCOCINA, `<salto/><etiqueta estilo=\"default\" alineacion=\"izquierda\">CAJERO/A: </etiqueta><parametro estilo=\"default\" alineacion=\"izquierda\">usr_usuario</parametro>`, "")

	// Agregar medio y usuario al final
	templateXMLVCOCINA = strings.ReplaceAll(templateXMLVCOCINA, `<salto/></plantilla>`, `<parametro estilo=\"default\" alineacion=\"izquierda\">usr_usuario</parametro><salto/><parametro estilo=\"default\" alineacion=\"izquierda\">nombre_estacion</parametro><salto/><parametro estilo=\"default\" alineacion=\"izquierda\">medio</parametro><salto/>`)
	templateXMLVCOCINA += `</plantilla>`

	return templateXMLVCOCINA
}
