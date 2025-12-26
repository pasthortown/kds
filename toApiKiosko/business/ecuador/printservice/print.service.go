package printservice

import (
	"bytes"
	"fmt"
	"io"
	"lib-shared/utils"
	"lib-shared/utils/logger"
	"net/http"
	"new-order-store/internals/entity/enums"
	"new-order-store/internals/entity/models"
	"new-order-store/internals/infrastructure/cache"
	"new-order-store/internals/infrastructure/sqlserver"
	"new-order-store/internals/utils/validatorsql"
	"strings"
	"time"
)

var cachePrinter = cache.NewTTL[string, string]()

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

func (o *ApiPrintService) ApiPrint(dataPrint models.PrintingServiceResponse,
	dataOrderPrint *models.KioskoImpresionOrdenPedidoFastFood,
	cfacId,
	IdOrderHeaderOrder,
	idUserPos,
	idStation string,
	isAdvanceImpression bool,
) error {
	var dataDistribution *models.DataDistribution
	dataPost := &models.DataPost{}

	printer := dataOrderPrint.Impresora
	idMarca, err := o.PrinterTypeDescription(dataOrderPrint.Impresora)
	if err != nil {
		return fmt.Errorf("[servicio.impresion.go]Error al obtener la descripcion de la impresora de pedido: %v", err)
	}
	if idMarca == nil {
		return fmt.Errorf("[servicio.impresion.go]Error no existen datos en la descripcion de la impresora de pedido")
	}
	numberPrint := dataOrderPrint.NumeroImpresiones
	typeDocument := dataOrderPrint.Tipo
	if dataOrderPrint.Tipo == nil {
		return fmt.Errorf("[servicio.impresion.go]Error,el tipo de documento a imprimir esta vacio, por favor revisar")
	}
	if dataOrderPrint.JsonRegistros == nil {
		return fmt.Errorf("[servicio.impresion.go]Error, el campo JsonRegistros esta vacio, por favor revisar")
	}
	if dataOrderPrint.JsonData == nil {
		return fmt.Errorf("[servicio.impresion.go]Error, el campo JsonData esta vacio, por favor revisar")
	}
	//Formateo del jsonRegistros
	jsonRegistros := *dataOrderPrint.JsonRegistros
	if strings.EqualFold(*typeDocument, "VOUCHER_KIOSKO") {
		jsonRegistros = fmt.Sprintf(`[{"registrosDesgloseImpuestos": %s}]`, jsonRegistros)
	}
	data, _ := utils.StringToInterface(*dataOrderPrint.JsonData)
	jsonRegistrosNew, _ := utils.StringToInterface(jsonRegistros)
	dataPost.IdMarca = *idMarca
	dataPost.IdImpresora = dataOrderPrint.Impresora
	dataPost.AplicaBalanceo = "0"
	dataPost.IdPlantilla = *dataOrderPrint.FormatoXML
	dataPost.Data = data
	dataPost.Registros = jsonRegistrosNew

	url := dataPrint.URL + dataPrint.Ruta
	if dataPrint.URL == "0" || dataPrint.Ruta == "0" {
		dataString, _ := utils.InterfaceToString(dataPrint)
		msg := "ERROR CONEXION API NET CORE"
		o.insertAudit(typeDocument, &msg, nil, &idStation, &idUserPos, &url, nil, nil, &dataString, nil, nil)
		return nil
	}

	//Se llama la funcion para fn_ColeccionRestaurante_ImprimeKDS el cual valida la impresion de los pedidos en kds
	isPrintKds := o.printKds(o.rstId, IdOrderHeaderOrder)
	if strings.EqualFold(*typeDocument, "orden_pedido") {
		dataPrinter, exits := cachePrinter.Get(enums.IMPRESORA.String())
		if exits {
			printer = &dataPrinter
		}
		tmpDataDistribution, err := o.DistributionPrinting(dataPost.IdImpresora, &IdOrderHeaderOrder)
		if err != nil {
			return err
		}
		if tmpDataDistribution != nil {
			if *tmpDataDistribution.AplicarEjecucion == "1" {
				if exits {
					if printer != tmpDataDistribution.ImpresoraEnviar {
						cachePrinter.Set(enums.IMPRESORA.String(), *tmpDataDistribution.ImpresoraEnviar, 10*time.Minute)
					}
				} else {
					cachePrinter.Set(enums.IMPRESORA.String(), *tmpDataDistribution.ImpresoraEnviar, 10*time.Minute)
				}
				idNewMarca, err := o.PrinterTypeDescription(tmpDataDistribution.ImpresoraEnviar)
				if err != nil {
					return fmt.Errorf("[servicio.impresion.go]Error al obtner la descripcion de la impresora de pedido: %s\n", err)
				}
				dataPost.IdImpresora = tmpDataDistribution.ImpresoraEnviar
				dataPost.AplicaBalanceo = "1"
				dataPost.IdMarca = *idNewMarca
			} else {
				cachePrinter.Remove(enums.IMPRESORA.String())
			}
		}
		dataDistribution = tmpDataDistribution
	}
	if strings.EqualFold(*typeDocument, "VOUCHER_KIOSKO") {
		dataPrinter, exits := cachePrinter.Get(enums.IMPRESORA.String())
		if exits {
			printer = &dataPrinter
		}
		tmpDataDistribution, err := o.DistributionPrinting(printer, &IdOrderHeaderOrder)
		if err != nil {
			return err
		}
		if tmpDataDistribution != nil {
			if *tmpDataDistribution.AplicarEjecucion == "1" {

				idNewMarca, err := o.PrinterTypeDescription(printer)
				if err != nil {
					return fmt.Errorf("[servicio.impresion.go]Error al obtner la descripcion de la impresora de pedido: %s\n", err)
				}
				dataPost.IdImpresora = printer
				dataPost.AplicaBalanceo = "1"
				dataPost.IdMarca = *idNewMarca
			}
		}
		dataDistribution = tmpDataDistribution
	}

	logger.Info.Printf("printer to print: %v", *dataPost.IdImpresora)
	logger.Info.Printf("isPrintKds: %v - isAdvanceImpression: %v", isPrintKds, isAdvanceImpression)

	for i := 1; i <= int(numberPrint); i++ {
		urlApiPrint, err := o.GetIpPrint(dataPost.IdImpresora)
		if err != nil {
			return err
		}
		if !utils.IsEmpty(urlApiPrint) {
			url = *urlApiPrint + dataPrint.Ruta
		}

		if strings.EqualFold(*typeDocument, "orden_pedido") && isPrintKds {
			dataString := dataPost.ToString()
			status := "PENDIENTE"
			o.insertAudit(
				typeDocument,
				&status,
				dataPost.IdImpresora,
				&idStation,
				&idUserPos,
				&url,
				&dataString,
				nil,
				&IdOrderHeaderOrder,
				nil,
				&IdOrderHeaderOrder,
			)
			return nil
		}

		if !isAdvanceImpression {
			return nil
		}

		err = o.CurlRetry(dataPost,
			dataPrint,
			dataDistribution,
			url,
			dataPrint.Ruta,
			cfacId,
			idUserPos,
			idStation, *typeDocument)
		if err != nil {
			return err
		}
	}
	return nil
}

func (o *ApiPrintService) CurlRetry(dataPost *models.DataPost,
	dataPrint models.PrintingServiceResponse,
	dataDistribution *models.DataDistribution,
	url,
	route,
	cfacId,
	idUserPos,
	idStation,
	typeDocument string) error {

	dataPostString := dataPost.ToString()
	logger.Debug("JSON de servicio de impresion a enviar: ", dataPostString)
	timeout := time.Duration(dataPrint.Timeout) * time.Second
	client := &http.Client{Timeout: timeout}
	success := false
	var msgStatus string

	for intento := 1; intento <= dataPrint.Reintentos; intento++ {
		msgStatus = "ERROR CONEXION API NET CORE"

		req, err := http.NewRequest("POST", url, bytes.NewBuffer([]byte(dataPostString)))
		if err != nil {
			return err
		}
		req.Header.Set("Content-Type", "application/json")
		req.Header.Set("Accept", "application/json")

		resp, err := client.Do(req)
		if err != nil {
			logger.Error.Printf("[servicio.impresion.go]Error al comunicarse con el servicio de impresion de %v: %v", typeDocument, err)
			o.insertAudit(&typeDocument, &msgStatus, dataPost.IdImpresora, &idStation, &idUserPos, &url, &dataPostString, nil, nil, nil, &cfacId)
			continue
		}

		func() {
			defer resp.Body.Close()
			body, err := io.ReadAll(resp.Body)
			if err != nil {
				return
			}
			statusCode := resp.StatusCode
			dataBody := string(body)

			if statusCode == http.StatusOK || statusCode == http.StatusNotFound {
				success = true
				if !strings.EqualFold(typeDocument, "reimpresionFinDelDia") &&
					!strings.EqualFold(typeDocument, "reimpresionDesmontadoCajero") {
					msgStatus = "SUCCESS CONEXION API NET CORE"
					logger.Info.Printf("[servicio.impresion.go]Conexion exitosa al servicio de impresion para %v - %v", typeDocument, *dataPost.IdImpresora)
					o.insertAudit(&typeDocument, &msgStatus, dataPost.IdImpresora, &idStation, &idUserPos, &url, &dataPostString, &dataBody, nil, nil, &cfacId)
				}
				return
			}

			// Intentar cambiar impresora
			if dataDistribution != nil && *dataDistribution.AplicarEjecucion == "1" {
				logger.Error.Println("[servicio.impresion.go]Error al imprimir, ejecutando cambio de impresora...")
				if o.handleChangePrinter(client, dataPost, &url, route, typeDocument, idStation, idUserPos, cfacId) {
					success = true
					return
				}
			}

			o.insertAudit(&typeDocument, &msgStatus, dataPost.IdImpresora, &idStation, &idUserPos, &url, &dataPostString, &dataBody, nil, nil, &cfacId)
		}()

		if success {
			break
		}
	}

	// Si ningún intento tuvo éxito
	if !success {
		dataError := &models.ResultError{
			Error:   true,
			Message: "[servicio.impresion.go]Todos los intentos fallaron al comunicarse con el servicio de impresión.",
		}
		stringDataError, _ := utils.InterfaceToString(dataError)
		o.insertAudit(&typeDocument, &msgStatus, dataPost.IdImpresora, &idStation, &idUserPos, &url, &dataPostString, &stringDataError, nil, nil, &cfacId)
	}

	return nil
}

func (o *ApiPrintService) handleChangePrinter(
	client *http.Client,
	dataPost *models.DataPost,
	url *string,
	route, typeDocument, idStation, idUserPos, cfacId string,
) bool {
	msgStatus := "ERROR CONEXION API NET CORE"
	query := fmt.Sprintf(`SELECT * FROM [impresion].[cambiar_impresora_distribucion]('%s') as nombreImpresora`, *dataPost.IdImpresora)
	rows := o.connection.QueryRow(query)

	var newPrinter *string
	if rows.Err() != nil || rows.Scan(&newPrinter) != nil {
		logger.Error.Printf("[servicio.impresion.go]Error al obtener nueva impresora con query: %v", query)
		return false
	}
	if newPrinter == nil {
		logger.Error.Println("[servicio.impresion.go]No se pudo obtener el id de la impresora nueva para el balanceo")
		return false
	}
	logger.Info.Println("[servicio.impresion.go]El nuevo id de la impresora para el balanceo es: ", *newPrinter)
	dataPost.IdImpresora = newPrinter
	newDataPostStr := dataPost.ToString()

	ipPrint, err := o.GetIpPrint(newPrinter)
	if err != nil || utils.IsEmpty(ipPrint) {
		logger.Error.Println("[servicio.impresion.go]No se pudo obtener la IP de la impresora nueva")
		return false
	}

	newURL := *ipPrint + route
	*url = newURL

	req, err := http.NewRequest("POST", newURL, bytes.NewBuffer([]byte(newDataPostStr)))
	if err != nil {
		logger.Error.Println("[servicio.impresion.go]Error creando nueva request para la impresora cambiada")
		return false
	}
	req.Header.Set("Content-Type", "application/json")
	req.Header.Set("Accept", "application/json")

	resp, err := client.Do(req)
	if err != nil {
		logger.Error.Printf("[servicio.impresion.go]Error ejecutando nueva request: %v", err)
		return false
	}
	defer resp.Body.Close()

	body, _ := io.ReadAll(resp.Body)
	statusCode := resp.StatusCode
	dataBody := string(body)

	if statusCode == http.StatusOK || statusCode == http.StatusNotFound {
		msgStatus = "SUCCESS CONEXION API NET CORE"
		o.insertAudit(&typeDocument, &msgStatus, dataPost.IdImpresora, &idStation, &idUserPos, &newURL, &newDataPostStr, &dataBody, nil, nil, &cfacId)
		return true
	} else {
		dataError := &models.ResultError{
			Error:   true,
			Message: "[servicio.impresion.go]Ha ocurrido un error al momento de imprimir, por favor comuniquese con soporte!!",
		}
		stringDataError, _ := utils.InterfaceToString(dataError)
		o.insertAudit(&typeDocument, &msgStatus, dataPost.IdImpresora, &idStation, &idUserPos, &newURL, &newDataPostStr, &stringDataError, nil, nil, &cfacId)
		return false
	}
}

func (o *ApiPrintService) insertAudit(typeDocument, status, print, idStation,
	idUserPos, url, payload, response, additionalData, codeApp, codeInvoicing *string) {
	if strings.EqualFold("ERROR CONEXION API NET CORE", *status) {
		newResponse := "false"
		response = &newResponse
	}
	insert := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[impresion].[IAE_AuditoriaApiImpresion]")
	insert.AddValueParameterized("tipo", typeDocument)
	insert.AddValueParameterized("estado", status)
	insert.AddValueParameterized("impresora", print)
	insert.AddValueParameterized("idEstacion", idStation)
	insert.AddValueParameterized("idUsuario", idUserPos)
	insert.AddValueParameterized("url", url)
	insert.AddValueParameterized("payload", payload)
	insert.AddValueParameterized("response", response)
	insert.AddValueParameterized("datosAdicionales", additionalData)
	insert.AddValueParameterized("accion", "INSERT")
	insert.AddValueParameterized("codigo_app", codeApp)
	insert.AddValueParameterized("cfac_id", codeInvoicing)
	_, err := o.connection.IQuery(insert.GetStoreProcedure(), insert.GetValues())
	if err != nil {
		logger.Error.Println("[servicio.impresion.go]Error al ejecutar el sp [impresion].[IAE_AuditoriaApiImpresion] ", err)
		return
	}

}
func (o *ApiPrintService) PrinterTypeDescription(printer *string) (*string, error) {
	var dataValue *string
	dataValueRow := o.connection.QueryRow(fmt.Sprintf("SELECT [impresion].[fnServicioApiImpresionObtenerDescripcionTipoImpresora]('%v')", *printer))
	err := dataValueRow.Scan(&dataValue)
	if err != nil {
		return nil, err
	}
	if dataValue == nil {
		return nil, fmt.Errorf("[servicio.impresion.go]No se encontró datos para la query: SELECT [impresion].[fnServicioApiImpresionObtenerDescripcionTipoImpresora]('%v')\n", *printer)
	}
	return dataValue, nil
}

func (o *ApiPrintService) GetIpPrint(namePrinter *string) (*string, error) {
	var dataValue *string
	query := fmt.Sprintf("SELECT [impresion].[fn_ColeccionRestaurante_ServicioApiImpresionUrl]('%v')\n", *namePrinter)
	if namePrinter == nil {
		query = "SELECT [impresion].[fn_ColeccionRestaurante_ServicioApiImpresionUrl](null)"
	}
	dataValueRow := o.connection.QueryRow(query)
	err := dataValueRow.Scan(&dataValue)
	if err != nil {
		return nil, err
	}
	/*if dataValue == nil {
		return nil, fmt.Errorf("No se encontró datos para la query: %v\n", query)
	}*/
	return dataValue, nil
}

func (o *ApiPrintService) printKds(rst int, IdOrderHeaderOrder string) bool {
	objectExits := validatorsql.ObjectExitsDb(o.connection, "FUNCTION", "config", "fn_ColeccionRestaurante_ImprimeKDS")
	if !objectExits {
		logger.Error.Println("[ecuador.print.kds]Error, el objeto config.fn_ColeccionRestaurante_ImprimeKDS no existe, por favor revisar")
		return false
	}
	/* 	query := fmt.Sprintf("SELECT [config].[fn_ColeccionRestaurante_ImprimeKDS]('%v', '%v')", rst, IdOrderHeaderOrder)*/
	query := fmt.Sprintf("SELECT [config].[fn_ColeccionRestaurante_ImprimeKDS]('%v')", rst)
	rows, err := o.connection.Query(query)
	if err != nil {
		logger.Error.Printf("[ecuador.print.kds]Error al ejecutar la funcion %v: %v", query, err)
		return false
	}

	defer rows.Close()
	for rows.Next() {
		var validate bool
		err = rows.Scan(&validate)
		if err != nil {
			logger.Error.Printf("[ecuador.print.kds]Error, no se pudo obtener los datos de la funcion %v: %v", query, err)
			return false
		}
		return validate
	}
	return true
}

func (o *ApiPrintService) DistributionPrinting(printer, idHeaderOrder *string) (*models.DataDistribution, error) {
	data := &models.DataDistribution{}
	distribution := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[impresion].[USP_distribucionImpresion]")
	distribution.AddValueParameterized("impresoraActual", printer)
	distribution.AddValueParameterized("IDCabeceraOrdenPedido", idHeaderOrder)
	rows, err := o.connection.IQuery(distribution.GetStoreProcedure(), distribution.GetValues())
	if err != nil {
		return nil, fmt.Errorf("[servicio.impresion.go]Error al ejecutar el sp [impresion].[USP_distribucionImpresion]: %v", err)
	}
	for rows.Next() {
		err = rows.Scan(&data.ImpresoraEnviar, &data.AplicarEjecucion, &data.ImpresoraBalancear)
		if err != nil {
			return nil, fmt.Errorf("[servicio.impresion.go]Error al obtener los datos del [impresion].[USP_distribucionImpresion]: %v", err)
		}
		return data, nil
	}
	return nil, nil
}
