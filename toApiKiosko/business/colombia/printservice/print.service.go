package printservice

import (
	"bytes"
	"encoding/base64"
	"fmt"
	"github.com/skip2/go-qrcode"
	"io"
	"lib-shared/utils"
	"lib-shared/utils/logger"
	"net/http"
	"new-order-store/internals/entity/models"
	"new-order-store/internals/infrastructure/sqlserver"
	"os"
	"strings"
	"time"
)

type ApiPrintService struct {
	connection *sqlserver.DatabaseSql
}

func NewApiPrintService(connection *sqlserver.DatabaseSql) *ApiPrintService {
	return &ApiPrintService{
		connection: connection,
	}
}

func (o *ApiPrintService) ApiPrint(dataPrint models.PrintingServiceResponse,
	dataOrderPrintFastFood []*models.KioskoImpresionOrdenPedidoFastFood,
	cfacId,
	IdOrderHeaderOrder,
	idUserPos,
	idStation string) error {
	var typeDocument *string
	var numberPrint int32
	dataPost := &models.DataPost{}
	if len(dataOrderPrintFastFood) > 0 {
		for _, dataOrderPrint := range dataOrderPrintFastFood {
			if dataOrderPrint.Confirmar != 1 {
				//Obtengo la descripcion de la impresora
				idMarca, err := o.PrinterTypeDescription(dataOrderPrint.Impresora)
				if err != nil {
					return fmt.Errorf("[servicio.impresion.go]Error al obtener la descripcion de la impresora de pedido: %s", err.Error())
				}
				if idMarca == nil {
					return fmt.Errorf("[servicio.impresion.go]Error no existen datos en la descripcion de la impresora de pedido")
				}
				typeDocument = dataOrderPrint.Tipo
				jsonRegistrosNew, _ := utils.StringToInterface(*dataOrderPrint.JsonRegistros)
				data, _ := utils.ConvertirStringAMap(*dataOrderPrint.JsonData)
				//Para la generacion de ImagenQr
				SearchKey := o.SearchKeysHome(data, "imagenqr_")
				if len(SearchKey) > 0 {
					for _, key := range SearchKey {
						nameFile, hasNameFile := data["namefileQR"]
						if !hasNameFile {
							break
						}
						nameKey, hasKey := data[key]
						if !hasKey {
							break
						}
						keysFound, err := o.ImageBase64(nameKey.(string), nameFile.(string))
						if err != nil {
							logger.Error.Println(err)
							break
						}
						data[key] = keysFound
					}
				}

				numberPrint = dataOrderPrint.NumeroImpresiones
				dataPost.IdMarca = *idMarca
				dataPost.IdImpresora = dataOrderPrint.Impresora
				dataPost.AplicaBalanceo = "0"
				dataPost.IdPlantilla = *dataOrderPrint.FormatoXML
				dataPost.Data = data
				dataPost.Registros = jsonRegistrosNew
				url := dataPrint.URL + dataPrint.Ruta

				if dataPrint.URL == "0" || dataPrint.Ruta == "0" {
					dataString, _ := utils.InterfaceToString(dataPrint)
					msg := "Las politicas SERVICIO API IMPRESION no se encuentran configuradas, por favor comuniquese con soporte!!"
					o.insertAudit(typeDocument, &msg, nil, &idStation, &idUserPos, &url, nil, nil, &dataString, nil, nil)
					return nil
				}

				for i := 1; i <= int(numberPrint); i++ {
					urlApiPrint, err := o.GetIpPrint(dataPost.IdImpresora)
					if err != nil {
						return err
					}
					if !utils.IsEmpty(urlApiPrint) {
						url = *urlApiPrint + dataPrint.Ruta
					}
					err = o.CurlRetry(
						dataPost,
						dataPrint,
						url,
						cfacId,
						idUserPos,
						idStation,
						*typeDocument,
					)
					if err != nil {
						return err
					}
				}
			}
		}
	}
	return nil
}

func (o *ApiPrintService) CurlRetry(dataPost *models.DataPost,
	dataPrint models.PrintingServiceResponse,
	url,
	cfacId,
	idUserPos,
	idStation,
	typeDocument string) error {
	seconds := dataPrint.Timeout
	timeout := time.Duration(seconds) * time.Second
	dataPostString := dataPost.ToString()
	logger.Info.Printf("Datos a enviar al sercio de impresion " + dataPostString)
	var intento = 1
	for i := 0; i < dataPrint.Reintentos; i++ {
		msgStatus := "ERROR"
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
			logger.Info.Printf("[servicio.impresion.go]Error al comunicarse con el servicio de impresion de %v: %v", typeDocument, err)
			o.insertAudit(&typeDocument, &msgStatus, dataPost.IdImpresora, &idStation, &idUserPos, &url, &dataPostString, nil, nil, nil, &cfacId)
			continue
		}
		body, err := io.ReadAll(resp.Body)
		resp.Body.Close()
		if err != nil {
			logger.Info.Printf("[servicio.impresion.go]Error al leer la respuesta HTTP del servicio de impresion: %v", err)
			o.insertAudit(&typeDocument, &msgStatus, dataPost.IdImpresora, &idStation, &idUserPos, &url, &dataPostString, nil, nil, nil, &cfacId)
			continue
		}
		statusCode := resp.StatusCode
		dataBody := string(body)

		if statusCode == http.StatusOK || statusCode == http.StatusNotFound {
			msgStatus = "SUCCESS"
			logger.Info.Printf("[servicio.impresion.go]Conexion exitosa al servicio de impresion para %v - %v\n", typeDocument, *dataPost.IdImpresora)
			o.insertAudit(&typeDocument, &msgStatus, dataPost.IdImpresora, &idStation, &idUserPos, &url, &dataPostString, &dataBody, nil, nil, &cfacId)
			return nil
		}
		if intento == dataPrint.Reintentos {
			logger.Info.Printf("[servicio.impresion.go]Ha ocurrido un error al momento de imprimir, por favor comuniquese con soporte!!")
			o.insertAudit(&typeDocument, &msgStatus, dataPost.IdImpresora, &idStation, &idUserPos, &url, &dataPostString, &dataBody, nil, nil, &cfacId)
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
		logger.Info.Println("[servicio.impresion.go]Error al ejecutar el sp [impresion].[IAE_AuditoriaApiImpresion] ", err.Error())
		return
	}

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

func (o *ApiPrintService) PrinterTypeDescription(printer *string) (*string, error) {
	var dataValue *string
	dataValueRow := o.connection.QueryRow(fmt.Sprintf("SELECT [impresion].[fnServicioApiImpresionObtenerDescripcionTipoImpresora]('%v')", *printer))
	err := dataValueRow.Scan(&dataValue)
	if err != nil {
		return nil, err
	}
	if dataValue == nil {
		return nil, fmt.Errorf("[servicio.impresion.go]No se encontró datos para la query: SELECT [impresion].[fnServicioApiImpresionObtenerDescripcionTipoImpresora]('%v')", *printer)
	}
	return dataValue, nil
}

// BuscarLlavesInicio busca las claves en un mapa que comiencen con un prefijo específico
func (o *ApiPrintService) SearchKeysHome(array map[string]interface{}, inicio string) []string {
	var llavesEncontradas []string

	// Manejo de errores dentro del bloque
	defer func() {
		if r := recover(); r != nil {
			logger.Info.Println("Error en BuscarLlavesInicio:", r)
		}
	}()
	// Iterar sobre el mapa
	for llave := range array {
		if strings.HasPrefix(llave, inicio) { // Verifica si la llave comienza con el prefijo
			llavesEncontradas = append(llavesEncontradas, llave)
		}
	}
	return llavesEncontradas
}

// ImageBase64 genera un código QR a partir de un contenido y lo convierte en una cadena Base64
func (o *ApiPrintService) ImageBase64(contentQR, imageName string) (string, error) {
	// Directorio donde se almacenará temporalmente el QR
	dir := "public/qrcodes/"
	if _, err := os.Stat(dir); os.IsNotExist(err) {
		// Crear el directorio si no existe
		if err = os.MkdirAll(dir, os.ModePerm); err != nil {
			return "", fmt.Errorf("error creando el directorio: %w", err)
		}
	}

	// Ruta del archivo QR
	filename := fmt.Sprintf("%s%s.png", dir, imageName)

	// Generar el código QR
	const tamanio = 200
	const level = qrcode.High // Equivale al nivel 'H'
	err := qrcode.WriteFile(contentQR, level, tamanio, filename)
	if err != nil {
		return "", fmt.Errorf("error generando el código QR: %w", err)
	}

	// Leer el archivo generado
	contenidoBinario, err := os.ReadFile(filename)
	if err != nil {
		return "", fmt.Errorf("error leyendo el archivo QR: %w", err)
	}

	// Codificar el contenido a base64
	base64Str := base64.StdEncoding.EncodeToString(contenidoBinario)

	// Eliminar el archivo temporal
	if err = os.Remove(filename); err != nil {
		return "", fmt.Errorf("error eliminando el archivo temporal: %w", err)
	}

	// Retornar el string base64
	return base64Str, nil
}
