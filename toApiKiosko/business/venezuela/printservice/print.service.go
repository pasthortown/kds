package printservice

import (
	"bytes"
	"fmt"
	"io"
	"lib-shared/utils"
	"net/http"
	"new-order-store/internals/entity/enums"
	"new-order-store/internals/entity/models"
	"new-order-store/internals/infrastructure/cache"
	"new-order-store/internals/infrastructure/sqlserver"
	"strings"
	"time"
)

var cachePrinter = cache.NewTTL[string, string]()

type ApiPrintService struct {
	connection *sqlserver.DatabaseSql
}

func NewApiPrintService(connection *sqlserver.DatabaseSql) *ApiPrintService {
	return &ApiPrintService{
		connection: connection,
	}
}

func (o *ApiPrintService) ApiPrint(dataPrint models.PrintingServiceResponse,
	dataOrderPrint *models.KioskoImpresionOrdenPedidoFastFood,
	dataCommercePrint *models.KioskoImpresionVoucherComercio,
	cfacId,
	IdOrderHeaderOrder,
	idUserPos,
	idStation string) error {
	var typeDocument *string
	var numberPrint int32
	var printer *string
	var dataDistribution *models.DataDistribution
	dataPost := &models.DataPost{}

	if dataCommercePrint != nil {
		printer = dataCommercePrint.Impresora
		idMarca, err := o.PrinterTypeDescription(dataCommercePrint.Impresora)
		if err != nil {
			return fmt.Errorf("[servicio.impresion.go]Error al obtner la descripcion de la impresora de voucher: %s\n", err)
		}
		if idMarca == nil {
			return fmt.Errorf("[servicio.impresion.go]Error no existen datos en la descripcion de la impresora de voucher\n")
		}
		numberPrint = dataCommercePrint.NumeroImpresiones
		typeDocument = dataCommercePrint.Tipo
		//Formareo del jsonRegistors del voucher
		jsonRegistros := *dataCommercePrint.JsonRegistros
		if strings.EqualFold(*typeDocument, "VOUCHER_KIOSKO") {
			jsonRegistros = fmt.Sprintf(`[{"registrosDesgloseImpuestos": %s}]`, jsonRegistros)
		}
		data, _ := utils.StringToInterface(*dataCommercePrint.JsonData)
		jsonRegistrosNew, _ := utils.StringToInterface(jsonRegistros)
		dataPost.IdMarca = *idMarca
		dataPost.IdImpresora = dataCommercePrint.Impresora
		dataPost.AplicaBalanceo = "0"
		dataPost.IdPlantilla = *dataCommercePrint.FormatoXML
		dataPost.Data = data
		dataPost.Registros = jsonRegistrosNew
	} else {
		//Obtengo la descripcion de la impresora
		printer = dataOrderPrint.Impresora
		idMarca, err := o.PrinterTypeDescription(dataOrderPrint.Impresora)
		if err != nil {
			return fmt.Errorf("[servicio.impresion.go]Error al obtener la descripcion de la impresora de pedido: %s\n", err)
		}
		if idMarca == nil {
			return fmt.Errorf("[servicio.impresion.go]Error no existen datos en la descripcion de la impresora de pedido\n")
		}
		typeDocument = dataOrderPrint.Tipo
		jsonRegistrosNew, _ := utils.StringToInterface(*dataOrderPrint.JsonRegistros)
		data, _ := utils.StringToInterface(*dataOrderPrint.JsonData)
		numberPrint = dataOrderPrint.NumeroImpresiones
		dataPost.IdMarca = *idMarca
		dataPost.IdImpresora = dataOrderPrint.Impresora
		dataPost.AplicaBalanceo = "0"
		dataPost.IdPlantilla = *dataOrderPrint.FormatoXML
		dataPost.Data = data
		dataPost.Registros = jsonRegistrosNew
	}

	url := dataPrint.URL + dataPrint.Ruta
	if dataPrint.URL == "0" || dataPrint.Ruta == "0" {
		dataString, _ := utils.InterfaceToString(dataPrint)
		msg := "ERROR CONEXION API NET CORE"
		o.insertAudit(typeDocument, &msg, nil, &idStation, &idUserPos, &url, nil, nil, &dataString, nil, nil)
		return nil
	}

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

	for i := 1; i <= int(numberPrint); i++ {
		urlApiPrint, err := o.GetIpPrint(dataPost.IdImpresora)
		if err != nil {
			return err
		}
		if !utils.IsEmpty(urlApiPrint) {
			url = *urlApiPrint + dataPrint.Ruta
		}

		err = o.CurlRetry(dataPost,
			dataPrint,
			dataDistribution,
			url,
			dataPrint.Ruta,
			cfacId,
			idUserPos,
			idStation, *typeDocument)
		return err
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
	dataError := &models.ResultError{}
	seconds := dataPrint.Timeout
	timeout := time.Duration(seconds) * time.Second
	dataPostString := dataPost.ToString()
	responseErr := false
	var intento = 1
	for i := 0; i < dataPrint.Reintentos; i++ {
		msgStatus := "ERROR CONEXION API NET CORE"
		req, err := http.NewRequest("POST", url, bytes.NewBuffer([]byte(dataPostString)))
		if err != nil {
			return err
		}
		req.Header.Set("Content-Type", "application/json")
		req.Header.Set("Accept", "application/json")
		client := &http.Client{
			Timeout: timeout,
		}
		resp, err := client.Do(req)
		if err != nil {
			fmt.Printf("[servicio.impresion.go]Error al comunicarse con el servicio de impresion de %v:%v\n", typeDocument, err)

			o.insertAudit(&typeDocument, &msgStatus, dataPost.IdImpresora, &idStation, &idUserPos, &url, &dataPostString, nil, nil, nil, &cfacId)

			intento++
			break
		}
		body, err := io.ReadAll(resp.Body)
		if err != nil {
			responseErr = true
		}
		statusCode := resp.StatusCode
		dataBody := string(body)
		defer resp.Body.Close()

		if statusCode == http.StatusOK || statusCode == http.StatusNotFound {
			if !strings.EqualFold(typeDocument, "reimpresionFinDelDia") && !strings.EqualFold(typeDocument, "reimpresionDesmontadoCajero") {
				msgStatus = "SUCCESS CONEXION API NET CORE"
				fmt.Printf("[servicio.impresion.go]Conexion exitosa al servicio de impresion para %v - %v\n", typeDocument, *dataPost.IdImpresora)
				o.insertAudit(&typeDocument, &msgStatus, dataPost.IdImpresora, &idStation, &idUserPos, &url, &dataPostString, &dataBody, nil, nil, &cfacId)
			}
			break
		} else {

			if dataDistribution != nil {
				if *dataDistribution.AplicarEjecucion == "1" {
					fmt.Println("[servicio.impresion.go]Ha ocurrido un error al momento de imprimir, por favor comuniquese con soporte!!")
					o.insertAudit(&typeDocument, &msgStatus, dataPost.IdImpresora, &idStation, &idUserPos, &url, &dataPostString, &dataBody, nil, nil, &cfacId)
					if intento == dataPrint.Reintentos {
						fmt.Println("[servicio.impresion.go]Ha ocurrido un error al momento de imprimir, por favor comuniquese con soporte!!")
						break
					}
				}
			}
			if intento == dataPrint.Reintentos {
				o.insertAudit(&typeDocument, &msgStatus, dataPost.IdImpresora, &idStation, &idUserPos, &url, &dataPostString, &dataBody, nil, nil, &cfacId)
				break
			}
		}

		intento++
		if responseErr {
			if dataDistribution != nil {
				if *dataDistribution.AplicarEjecucion == "1" {
					//cambiar de impresora
					var newPrinter *string
					query := fmt.Sprintf(`SELECT * FROM [impresion].[cambiar_impresora_distribucion]('%s') as nombreImpresora`, dataPost.IdImpresora)
					rows := o.connection.QueryRow(query)
					if rows.Err() != nil {
						return fmt.Errorf("[servicio.impresion.go]Error al ejecutar el query %v: %v\n", query, err)
					}
					err = rows.Scan(&newPrinter)
					if err != nil {
						return fmt.Errorf("[servicio.impresion.go]Error al obtener los datos del query %v: %v\n", query, err)
					}
					dataPost.IdImpresora = newPrinter
					dataPostString = dataPost.ToString()
					//obtener la nuepa Ip de la impresora
					ipPrint, err := o.GetIpPrint(newPrinter)
					if err != nil {
						return err
					}
					if !utils.IsEmpty(ipPrint) {
						url = *ipPrint + route
					}
					req, err = http.NewRequest("POST", url, bytes.NewBuffer([]byte(dataPostString)))
					if err != nil {
						return err
					}
					req.Header.Set("Content-Type", "application/json")
					req.Header.Set("Accept", "application/json")
					client = &http.Client{
						Timeout: timeout,
					}
					resp, err = client.Do(req)
					if err != nil {
						return err
					}
					body, _ = io.ReadAll(resp.Body)

					statusCode = resp.StatusCode
					dataBody = string(body)
					defer resp.Body.Close()
					if statusCode == http.StatusOK || statusCode == http.StatusNotFound {
						msgStatus = "SUCCESS CONEXION API NET CORE"
						o.insertAudit(&typeDocument, &msgStatus, dataPost.IdImpresora, &idStation, &idUserPos, &url, &dataPostString, &dataBody, nil, nil, &cfacId)
						break
					} else {
						dataError.Error = true
						dataError.Message = "[servicio.impresion.go]Ha ocurrido un error al momento de imprimir, por favor comuniquese con soporte!!"
						stringDataError, _ := utils.InterfaceToString(dataError)
						o.insertAudit(&typeDocument, &msgStatus, dataPost.IdImpresora, &idStation, &idUserPos, &url, &dataPostString, &stringDataError, nil, nil, &cfacId)
						if intento != dataPrint.Reintentos {
							intento++
						}
						break
					}
				}
			}
		}
	}

	return nil
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
		fmt.Println("[servicio.impresion.go]Error al ejecutar el sp [impresion].[IAE_AuditoriaApiImpresion] ", err)
		return
	}

}
func (o *ApiPrintService) PrinterTypeDescription(printer *string) (*string, error) {
	var dataValue *string
	dataValueRow := o.connection.QueryRow(fmt.Sprintf("SELECT [impresion].[fnServicioApiImpresionObtenerDescripcionTipoImpresora]('%v')", printer))
	err := dataValueRow.Scan(&dataValue)
	if err != nil {
		return nil, err
	}
	if dataValue == nil {
		return nil, fmt.Errorf("[servicio.impresion.go]No se encontró datos para la query: SELECT [impresion].[fnServicioApiImpresionObtenerDescripcionTipoImpresora]('%v')\n", printer)
	}
	return dataValue, nil
}

func (o *ApiPrintService) GetIpPrint(namePrinter *string) (*string, error) {
	var dataValue *string
	query := fmt.Sprintf("SELECT [impresion].[fn_ColeccionRestaurante_ServicioApiImpresionUrl]('%v')\n", namePrinter)
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

func (o *ApiPrintService) DistributionPrinting(printer, idHeaderOrder *string) (*models.DataDistribution, error) {
	data := &models.DataDistribution{}
	distribution := sqlserver.NewStoreProcedureBuilderSQLWithParam(true, "[impresion].[USP_distribucionImpresion]")
	distribution.AddValueParameterized("impresoraActual", printer)
	distribution.AddValueParameterized("IDCabeceraOrdenPedido", idHeaderOrder)
	rows, err := o.connection.IQuery(distribution.GetStoreProcedure(), distribution.GetValues())
	if err != nil {
		return nil, fmt.Errorf("[servicio.impresion.go]Error al ejecutar el sp [impresion].[USP_distribucionImpresion]: %v\n", err)
	}
	for rows.Next() {
		err = rows.Scan(&data.ImpresoraEnviar, &data.AplicarEjecucion, &data.ImpresoraBalancear)
		if err != nil {
			return nil, fmt.Errorf("[servicio.impresion.go]Error al obtener los datos del [impresion].[USP_distribucionImpresion]: %v\n", err)
		}
		return data, nil
	}
	return nil, nil
}
