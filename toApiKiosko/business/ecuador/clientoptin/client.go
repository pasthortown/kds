package clientoptin

import (
	"bytes"
	"encoding/base64"
	"encoding/json"
	"fmt"
	"io"
	"lib-shared/protos/lib_gen_proto"
	"lib-shared/utils"
	"lib-shared/utils/logger"
	"log"
	"net/http"
	"new-order-store/internals/entity/enums"
	"new-order-store/internals/entity/maxpoint"
	"new-order-store/internals/entity/models"
	"new-order-store/internals/infrastructure/cache"
	"new-order-store/internals/infrastructure/sqlserver"
	"strconv"
	"strings"
	"time"
)

var cacheOptIn = cache.NewTTL[string, *models.TokenResponse]()

type Client struct {
	connection *sqlserver.DatabaseSql
	StoreData  *maxpoint.StoreData
}

func NewClient(connection *sqlserver.DatabaseSql, StoreData *maxpoint.StoreData) *Client {
	return &Client{connection: connection, StoreData: StoreData}
}

// GetTokenOptIn crea el token
func (o *Client) GetTokenOptIn(cdnId int) {
	_, exits := cacheOptIn.Get(enums.CLIENTOPTIN.String())
	if !exits {
		urlOptIn, err := o.GetDataOptIn(cdnId, "OPTIN KIOSKO", "URL API MDM")
		if err != nil {
			logger.Error.Println(err)
			return
		}

		dataNewToken, err := o.GenerateNewTokenClient(*urlOptIn)
		if err != nil {
			logger.Error.Println(err)
			return
		}
		cacheOptIn.Set(enums.CLIENTOPTIN.String(), dataNewToken, 1*time.Hour)
	}

}

// GenerateNewTokenClient Genera un nuevo token
func (o *Client) GenerateNewTokenClient(url string) (*models.TokenResponse, error) {
	cdnId := o.StoreData.ChainId
	var tokenResponse *models.TokenResponse
	generateTokenApi := &models.GenerateTokenApi{}
	apiClientId, err := o.GetDataOptIn(cdnId, "OPTIN KIOSKO", "APIMDMCLIENTID")
	if err != nil {
		return nil, err
	}
	clientSecret, err := o.GetDataOptIn(cdnId, "OPTIN KIOSKO", "APIMDMCLIENTSECRET")
	if err != nil {
		return nil, err
	}
	generateTokenApi.ClientID = *apiClientId
	generateTokenApi.ClientSecret = *clientSecret
	jsonPayload, err := json.Marshal(generateTokenApi)
	if err != nil {
		return nil, fmt.Errorf("[client.opt.in]Error marshalling generateTokenApi")
	}
	resp, err := http.Post(
		fmt.Sprintf("%s/api/auth/token/", url),
		"application/json",
		bytes.NewBuffer(jsonPayload),
	)
	if err != nil {
		return nil, fmt.Errorf("[client.opt.in]Error al hacer la solicitud HTTP %s/api/auth/token/: %s", url, err)
	}
	defer resp.Body.Close()
	if resp.StatusCode != http.StatusOK {
		return nil, fmt.Errorf("error en la respuesta HTTP: %s", resp.Status)
	}
	responseData, err := io.ReadAll(resp.Body)
	if err != nil {
		return nil, fmt.Errorf("[client.opt.in]Error en la respuesta HTTP %s/api/auth/token/: %s", url, err)
	}
	if err = json.Unmarshal(responseData, &tokenResponse); err != nil {
		return nil, fmt.Errorf("[client.opt.in]Error al decodificar la respuesta JSON: %s/api/auth/token/", url)
	}
	return tokenResponse, nil
}

func (o *Client) ClientOptIn(headerData *lib_gen_proto.Cabecera) error {
	cdnId := o.StoreData.ChainId
	client := headerData.Client
	dataClient := &models.ClientKioskos{}
	var applyOptIn *string
	query := fmt.Sprintf(`SELECT [config].[fn_ColeccionCadena_VariableB] (%v,'%s','%s')`, cdnId, "OPTIN KIOSKO", "APLICA OPTIN")
	rows := o.connection.QueryRow(query)
	err := rows.Err()
	if err != nil {
		return fmt.Errorf("[client.opt.in]Error al ejecutar el query %v: %v", query, err)
	}
	err = rows.Scan(&applyOptIn)
	if err != nil {
		return fmt.Errorf("[client.opt.in]Error al obtener los datos del query %v: %v", query, err)
	}
	if applyOptIn == nil {
		return fmt.Errorf("[client.opt.in]Error - no se obtuvos los dato del query %v", query)
	}
	typeDocument := o.StoreData.GetTipoDocumento(headerData.Client.DocumentId)
	if typeDocument == nil {
		return fmt.Errorf("[client.opt.in]Error al obtner los tipo de documentos")
	}
	if strings.EqualFold(typeDocument.Description, "CONSUMIDOR FINAL") {
		return fmt.Errorf("[client.opt.in]El Pedido es consumidor final y no se envio hacia MDM")
	}
	if strings.EqualFold("SI", *applyOptIn) {
		getUrlOptIn, err := o.GetDataOptIn(cdnId, "OPTIN KIOSKO", "URL API MDM")
		if err != nil {
			return err
		}
		dataToken, exits := cacheOptIn.Get(enums.CLIENTOPTIN.String())
		if !exits {
			dataNewToken, err := o.GenerateNewTokenClient(*getUrlOptIn)
			if err != nil {
				return err
			}
			cacheOptIn.Set(enums.CLIENTOPTIN.String(), dataNewToken, 1*time.Hour)
			dataToken = dataNewToken
		}
		dataClient.UrlOption = *getUrlOptIn
		dataClient.NewToken = dataToken.Token
		dataClient.TypeDescripcion = typeDocument.Description
		dataClient.CdnId = strconv.Itoa(o.StoreData.ChainId)
		validateConnectionApi := o.ValidateConnectApi(dataClient.UrlOption)
		if !validateConnectionApi {
			dataApiResponse, inErr := o.ConsultClientApi(dataClient, client)
			if inErr != nil {
				return inErr
			}
			if strings.EqualFold(dataApiResponse.ApiResponse, "cliente_no_encontrado") {
				inErr = o.CreateClient(headerData, dataClient)
				if inErr != nil {
					return inErr
				}
			} else {
				if &dataApiResponse.UserId != nil {
					inErr = o.UpdateClient(dataClient, headerData, dataApiResponse)
					if inErr != nil {
						return nil
					}
				} else {
					return fmt.Errorf("[client.opt.in]Error, no se obtuvo data del MDM")
				}
			}
		}
	}

	return nil
}

func (o *Client) ConsultClientApi(clientModel *models.ClientKioskos, client *lib_gen_proto.Client) (*models.ApiClientResponse, error) {
	var dataApiClient *models.ApiClientResponse
	token, err := o.ValidateToken(clientModel)
	if err != nil {
		return nil, err
	}
	urlApiMdm := fmt.Sprintf("%s/api/client/%v/%s", clientModel.UrlOption, clientModel.CdnId, client.DocumentNumber)
	clientHttp := &http.Client{}
	req, err := http.NewRequest("GET", urlApiMdm, nil)
	if err != nil {
		return nil, fmt.Errorf("[client.opt.in]Error al crear la solicitud HTTP %s: %s", urlApiMdm, err)
	}
	req.Header.Add("Authorization", "Bearer "+token.NewToken)
	resp, err := clientHttp.Do(req)
	if err != nil {
		return nil, fmt.Errorf("[client.opt.in]Error al hacer la solicitud HTTP %s: %s", urlApiMdm, err)
	}
	defer resp.Body.Close()
	responseData, err := io.ReadAll(resp.Body)
	if err != nil {
		return nil, fmt.Errorf("[client.opt.in]Error al leer la solicitud HTTP %s: %s", urlApiMdm, err)
	}
	if err = json.Unmarshal(responseData, &dataApiClient); err != nil {
		return nil, fmt.Errorf("[client.opt.in]Error al decodificar la respuesta JSON: %s", err)
	}
	apiResponse := o.ProcessApiResponse(dataApiClient)
	dataApiClient.ApiResponse = apiResponse
	if dataApiClient.Data != nil {
		dataClientResponse, err := utils.MapData(dataApiClient.Data)
		if err != nil {
			return nil, err
		}
		_, hasClient := dataClientResponse["cliente"]
		if hasClient {
			data := dataClientResponse["cliente"].(map[string]interface{})
			dataApiClient.UserId = data["_id"].(string)
		}
	}
	return dataApiClient, nil
}

func (o *Client) UpdateClient(clientModel *models.ClientKioskos, headerData *lib_gen_proto.Cabecera, data *models.ApiClientResponse) error {
	client := headerData.Client
	token, inErr := o.ValidateToken(clientModel)
	dataUpdate := &models.UpdateClientOptin{}
	if inErr != nil {
		return inErr
	}
	dataOrigin, err := o.GetDataOptIn(o.StoreData.ChainId, "OPTIN KIOSKO", "SISTEMA ORIGEN")
	if err != nil {
		return err
	}
	//
	now := time.Now()
	nowFormat := now.UTC().Format(time.RFC3339)
	dataUpdate.UserId = data.UserId
	dataUpdate.CdnId = clientModel.CdnId
	dataUpdate.Country = headerData.Country
	dataUpdate.SystemOrigin = *dataOrigin
	dataUpdate.TypeDocument = clientModel.TypeDescripcion
	dataUpdate.Email = client.Email
	dataUpdate.Phone = client.Phone
	dataUpdate.FirstName = client.Name
	dataUpdate.Surnames = " "
	dataUpdate.DateUpdate = nowFormat
	dataUpdate.DateAccepPrivacy = nowFormat
	dataUpdate.Authentication = headerData.AcceptPromotions
	dataUpdate.AcceptancePolicies = headerData.AcceptPromotions
	dataUpdate.ShippingCommercialCommunications = headerData.AcceptPromotions
	dataUpdate.ShippingCommercialCommunicationsPush = headerData.AcceptPromotions
	dataUpdate.DataAnalysisProfiles = headerData.CustomExperience
	dataUpdate.AssignmentDataNationalOperators = headerData.ShareNationalData
	dataUpdate.AssignmentDataInternationalCarriers = headerData.ShareInternationalData
	jsonData, err := json.Marshal(dataUpdate)
	if err != nil {
		return fmt.Errorf("[client.opt.in]Error al convertir datos a JSON: %s", err)
	}
	req, err := http.NewRequest("PUT", clientModel.UrlOption+"/api/client", bytes.NewBuffer(jsonData))
	if err != nil {
		return fmt.Errorf("[client.opt.in]Error al crear solicitud %s/api/client: %s", clientModel.UrlOption, err)
	}
	req.Header.Set("Content-Type", "application/json")
	req.Header.Set("Authorization", "Bearer "+token.NewToken)
	// Enviar la solicitud
	clientHttp := &http.Client{}
	resp, err := clientHttp.Do(req)
	if err != nil {
		return fmt.Errorf("[client.opt.in]Error al enviar solicitud %s/api/client: %s", clientModel.UrlOption, err)
	}
	defer resp.Body.Close()
	// Leer la respuesta
	responseData, err := io.ReadAll(resp.Body)
	if err != nil {
		return fmt.Errorf("[client.opt.in]Error al leer solicitud %s/api/client - %s", clientModel.UrlOption, err)
	}
	var response map[string]interface{}
	if err = json.Unmarshal(responseData, &response); err != nil {
		return fmt.Errorf("[client.opt.in]Error al decodificar JSON de respuesta %s/api/client - %s", clientModel.UrlOption, err)
	}
	mensaje := "Actualización de Cliente Exitosa en API MDM CLIENTE"
	toString, err := utils.InterfaceToString(response)
	if err != nil {
		return err
	}
	logger.Info.Printf("Mensaje: %s", mensaje)
	logger.Info.Println("Datos:", toString)
	return nil
}
func (o *Client) CreateClient(headerData *lib_gen_proto.Cabecera, clientModel *models.ClientKioskos) error {
	client := headerData.Client
	token, inErr := o.ValidateToken(clientModel)
	if inErr != nil {
		return inErr
	}
	dataCreate := &models.CreateClientOptin{}

	dataOrigin, err := o.GetDataOptIn(o.StoreData.ChainId, "OPTIN KIOSKO", "SISTEMA ORIGEN")
	if err != nil {
		return err
	}
	//
	now := time.Now()
	nowFormat := now.Format(time.RFC3339)
	dataCreate.CdnId = clientModel.CdnId
	dataCreate.Country = headerData.Country
	dataCreate.SystemOrigin = *dataOrigin
	dataCreate.Document = client.DocumentNumber
	dataCreate.TypeDocument = clientModel.TypeDescripcion
	dataCreate.Email = client.Email
	dataCreate.Phone = client.Phone
	dataCreate.FirstName = client.Name
	dataCreate.Surnames = ""
	dataCreate.DateAccepPrivacy = nowFormat
	dataCreate.AcceptancePolicies = headerData.AcceptPromotions
	dataCreate.Authentication = headerData.AcceptPromotions
	dataCreate.ShippingCommercialCommunications = headerData.AcceptPromotions
	dataCreate.ShippingCommercialCommunicationsPush = headerData.AcceptPromotions
	dataCreate.DataAnalysisProfiles = headerData.CustomExperience
	dataCreate.AssignmentDataNationalOperators = headerData.ShareNationalData
	dataCreate.AssignmentDataInternationalCarriers = headerData.ShareInternationalData
	// Convertir datos a JSON
	jsonData, err := json.Marshal(dataCreate)
	if err != nil {
		//return utilities_manager.GetCustomErrorBusinessValidate(err, "error al convertir datos a JSON")
		return fmt.Errorf("[client.opt.in]Error al convertir datos a JSON: %s", err)
	}

	logger.Info.Println("Datos a enviar:", jsonData)
	// Crear la solicitud PUT
	req, err := http.NewRequest("POST", clientModel.UrlOption+"/api/client", bytes.NewBuffer(jsonData))
	if err != nil {
		return fmt.Errorf("[client.opt.in]Error al crear solicitud %s/api/client - %s", clientModel.UrlOption, err)
	}
	// Configurar los encabezados
	req.Header.Set("Content-Type", "application/json")
	req.Header.Set("Authorization", "Bearer "+token.NewToken)
	// Enviar la solicitud
	clientHttp := &http.Client{}
	resp, err := clientHttp.Do(req)
	if err != nil {
		return fmt.Errorf("[client.opt.in]Error al enviar solicitud %s/api/client - %s", clientModel.UrlOption, err)
	}
	defer resp.Body.Close()
	// Leer la respuesta
	responseData, err := io.ReadAll(resp.Body)
	if err != nil {
		return fmt.Errorf("[client.opt.in]Error al leer solicitud %s/api/client - %s", clientModel.UrlOption, err)
	}
	// Decodificar la respuesta JSON
	var response map[string]interface{}
	if err = json.Unmarshal(responseData, &response); err != nil {
		return fmt.Errorf("[client.opt.in]Error al decodificar JSON de respuesta %s/api/client - %s", clientModel.UrlOption, err)
	}
	// Preparar mensaje de éxito
	mensaje := "Creación Cliente Exitosa en API MDM CLIENTE"
	toString, err := utils.InterfaceToString(response)
	if err != nil {
		return err
	}
	// Imprimir el resultado
	fmt.Printf("Mensaje: %s", mensaje)
	fmt.Println("Datos:", toString)
	fmt.Println("ErrorInfo: {}")

	return nil
}
func (o *Client) ValidateToken(clientModel *models.ClientKioskos) (*models.ClientKioskos, error) {
	client := &models.ClientKioskos{}
	isExpired, inErr := IsTokenExpired(clientModel.NewToken)
	if inErr != nil {
		return nil, inErr

	} else if isExpired {
		tokenClient, inErr := o.GenerateNewTokenClient(clientModel.UrlOption)
		if inErr != nil {
			return nil, inErr
		}
		client.NewToken = tokenClient.Token
		//Token.Token = tokenClient.Token
		return client, nil
	}
	return clientModel, nil
}
func (o *Client) ProcessApiResponse(datosCliente *models.ApiClientResponse) string {
	statusCode := datosCliente.StatusCode
	if &statusCode != nil {
		switch int(statusCode) {
		case 200:
			return "cliente_encontrado"

		case 422:
			log.Println("consultarClienteApi - RESPUESTA INCORRECTA 422")
			return "datos_invalidos"

		case 404:
			log.Println("consultarClienteApi - RESPUESTA INCORRECTA 404")
			return "cliente_no_encontrado"

		default:
			log.Printf("consultarClienteApi - RESPUESTA INCORRECTA %d", int(statusCode))
			return "error_desconocido"
		}
	}
	return "respuesta_vacia"
}

func (o *Client) ValidateConnectApi(urlOption string) bool {
	client := &http.Client{
		Timeout: 5 * time.Second, // Establece el tiempo límite en segundos
	}
	req, err := http.NewRequest("GET", urlOption, nil)
	if err != nil {
		return true
	}
	resp, err := client.Do(req)
	if err != nil {
		return true
	}
	defer resp.Body.Close()
	if resp.StatusCode >= 200 && resp.StatusCode < 300 {
		return false
	}
	return true
}

func IsTokenExpired(tokenString string) (bool, error) {
	// Partir el token en sus tres partes
	parts := strings.Split(tokenString, ".")
	if len(parts) != 3 {
		return false, fmt.Errorf("[client.opt.in]formato del token inválido")
	}

	// Decodificar el payload del token que es la segunda parte
	payload, err := base64.RawURLEncoding.DecodeString(parts[1])
	if err != nil {
		return false, fmt.Errorf("[client.opt.in]Error al decodificar el payload: %v", err)
	}

	// Extraer el campo 'exp' del payload
	var claims map[string]interface{}
	if err := json.Unmarshal(payload, &claims); err != nil {
		return false, fmt.Errorf("[client.opt.in]Error al parsear el payload: %v", err)
	}

	// Verificar si el campo 'exp' existe y es un número
	exp, ok := claims["exp"].(float64)
	if !ok {
		return false, fmt.Errorf("[client.opt.in]El campo 'exp' no está presente en el token o no es un número")
	}

	// Convertir 'exp' a un tiempo en Unix
	expirationTime := time.Unix(int64(exp), 0)

	// Verificar si el token ha expirado
	return time.Now().After(expirationTime), nil
}

func (o *Client) GetDataOptIn(cdnId int, collectionName, nameData string) (*string, error) {
	var dataResult *string
	query := fmt.Sprintf(`SELECT [config].[fn_ColeccionCadena_VariableV] (%v,'%s','%s')`, cdnId, collectionName, nameData)
	rows := o.connection.QueryRow(query)
	err := rows.Err()
	if err != nil {
		return nil, fmt.Errorf("[client.opt.in]Error al ejecutar el query %v: %v", query, err)
	}
	err = rows.Scan(&dataResult)
	if err != nil {
		return nil, fmt.Errorf("[client.opt.in]Error al obtener los datos del query %v: %v", query, err)
	}
	if dataResult == nil {
		return nil, fmt.Errorf("[client.opt.in]Error - no se obtuvo los dato del query %v", query)
	}
	return dataResult, nil
}
