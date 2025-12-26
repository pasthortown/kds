package masterdataclient

import (
	"bytes"
	"crypto/tls"
	"encoding/json"
	"errors"
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

const (
	ENDPOINT_TOKEN      = "/api/auth/token/"
	ENDPOINT_GETCLIENT  = "/api/client/"
	ENDPOINT_POSTCLIENT = "/api/client/"
	ENDPOINT_PUTCLIENT  = "/api/client/"
	ENDPOINT_TIMEOUT    = 30 * time.Second
)

var cacheMasterDataClient = cache.NewTTL[string, *models.TokenResponse]()

type ApiResponse struct {
	StatusCode string `json:"statusCode"`
	Success    string `json:"success"`
	Response   string `json:"response"`
}

type PoliciesMasterDataClient struct {
	UrlPolicies          string
	KioskApplyPolicies   string
	ClientIdPolicies     string
	ClientSecretPolicies string
	Token                string
}

type MasterDataClient struct {
	connection *sqlserver.DatabaseSql
	StoreData  *maxpoint.StoreData
}

func NewMasterDataClient(
	connection *sqlserver.DatabaseSql,
	storeData *maxpoint.StoreData) *MasterDataClient {
	return &MasterDataClient{
		connection: connection,
		StoreData:  storeData,
	}
}

func (m *MasterDataClient) ApiMasterDataCliente(headerData *lib_gen_proto.Cabecera) error {
	document := headerData.Client.DocumentNumber
	dataPolicies, err := m.getPolicies()
	if err != nil {
		return err
	}
	if strings.EqualFold("si", dataPolicies.KioskApplyPolicies) {
		typeDocument := m.StoreData.GetTipoDocumento(headerData.Client.DocumentId)
		if typeDocument == nil {
			return fmt.Errorf("[master.data.client.co]Error al obtener los tipo de documentos")
		}
		documentDescription := typeDocument.Description
		if strings.EqualFold(documentDescription, "CONSUMIDOR FINAL") {
			return fmt.Errorf("[master.data.client.co]El Pedido es consumidor final y no se envio hacia MDM")
		}
		responseApi := m.getApi(dataPolicies.UrlPolicies)
		if strings.EqualFold(documentDescription, "NIT") {
			document, err = utils.ValidarDocumentCo(document)
			if err != nil {
				return err
			}
		}
		if responseApi.StatusCode == "200" {
			dataClient, errClient := m.consultClientApi(dataPolicies, document)
			if errClient != nil {
				return errClient
			}
			if dataClient.StatusCode == 200 {
				errClient = m.updateClient(dataPolicies, dataClient, headerData, documentDescription)
				if errClient != nil {
					logger.Error.Println("APIMASTERDATACLIENTE -- Problemas al actualizar: UPDATE " + document)
					return errClient
				}
				logger.Info.Println("APIMASTERDATACLIENTE -- Datos actualizados: UPDATE " + document)
				return nil
			}
			//Crea el cliente
			errClient = m.createClient(dataPolicies, headerData, documentDescription, document)
			if errClient != nil {
				logger.Error.Println("APIMASTERDATACLIENTE -- Problemas al guardar: SAVE " + document)
				return errClient
			}
			logger.Info.Println("APIMASTERDATACLIENTE -- Datos guardado: SAVE " + document)
			return nil
		}
		return nil
	}
	return errors.New("APIMASTERDATACLIENTE -- La politica API MASTERDATACLIENTE, APLICA KIOSKO no esta activa")
}
func (m *MasterDataClient) getPolicies() (*PoliciesMasterDataClient, error) {
	dataPolicies := &PoliciesMasterDataClient{}
	url, err := m.getCollectionStringVariableV("API MASTERDATACLIENTE", "URL")
	if err != nil {
		return nil, err
	}
	applyService, err := m.getCollectionStringVariableB("API MASTERDATACLIENTE", "APLICA KIOSKO")
	if err != nil {
		return nil, err
	}
	apiClientId, err := m.getCollectionStringVariableV("API MASTERDATACLIENTE", "CLIENTID")
	if err != nil {
		return nil, err
	}
	clientSecret, err := m.getCollectionStringVariableV("API MASTERDATACLIENTE", "CLIENTSECRET")
	if err != nil {
		return nil, err
	}
	dataPolicies.UrlPolicies = *url
	dataPolicies.KioskApplyPolicies = *applyService
	dataPolicies.ClientIdPolicies = *apiClientId
	dataPolicies.ClientSecretPolicies = *clientSecret
	return dataPolicies, nil
}

func (m *MasterDataClient) getApi(url string) ApiResponse {
	client := &http.Client{
		Timeout: ENDPOINT_TIMEOUT,
		Transport: &http.Transport{
			TLSClientConfig: &tls.Config{InsecureSkipVerify: true}, // No recomendado para producción
		},
	}
	// Hacer la solicitud GET
	resp, err := client.Get(url)
	if err != nil {
		log.Printf("APIMASTERDATACLIENTE -- Problemas HTTP GET _getApi: %v", err.Error())
		return ApiResponse{
			StatusCode: "500",
			Success:    "false",
			Response:   fmt.Sprintf("Error al responder el servicio _getApi ProteccionDatosClientesEC %v", err),
		}
	}
	defer resp.Body.Close()

	// Leer el cuerpo de la respuesta
	body, err := io.ReadAll(resp.Body)
	if err != nil {
		log.Printf("APIMASTERDATACLIENTE -- Error leyendo respuesta _getApi: %v", err.Error())
		return ApiResponse{
			StatusCode: "500",
			Success:    "false",
			Response:   "Error al leer la respuesta del servicio",
		}
	}
	logger.Info.Printf("APIMASTERDATACLIENTE -- Response data _getApi: %s", string(body))
	return ApiResponse{
		StatusCode: "200",
		Success:    "true",
		Response:   string(body),
	}

}

func (m *MasterDataClient) consultClientApi(data *PoliciesMasterDataClient, documentNumber string) (*models.ApiClientResponse, error) {
	dataApiClient := &models.ApiClientResponse{}
	dataToken, exits := cacheMasterDataClient.Get(enums.MASTERDATACLIENT.String())
	if !exits {
		dataNewToken, err := m.generateNewTokenClient(data)
		if err != nil {
			return nil, err
		}
		cacheMasterDataClient.Set(enums.MASTERDATACLIENT.String(), dataNewToken, 32*time.Hour)
		dataToken = dataNewToken
	}
	data.Token = dataToken.Token
	data, err := m.validateToken(data)
	if err != nil {
		return nil, err
	}
	urlApi := data.UrlPolicies + ENDPOINT_GETCLIENT
	urlApiMdm := fmt.Sprintf("%s%v/%s", urlApi, m.StoreData.ChainId, documentNumber)
	clientHttp := &http.Client{
		Timeout: ENDPOINT_TIMEOUT,
	}
	req, err := http.NewRequest("GET", urlApiMdm, nil)
	if err != nil {
		return nil, fmt.Errorf("[master.data.client.co]Error al crear la solicitud HTTP %s: %s", urlApiMdm, err.Error())
	}
	req.Header.Add("Authorization", "Bearer "+data.Token)
	resp, err := clientHttp.Do(req)
	if err != nil {
		return nil, fmt.Errorf("[master.data.client.co]Error al hacer la solicitud HTTP %s: %s", urlApiMdm, err.Error())
	}
	defer resp.Body.Close()
	responseData, err := io.ReadAll(resp.Body)
	if err != nil {
		return nil, fmt.Errorf("[master.data.client.co]Error al leer la solicitud HTTP %s: %s", urlApiMdm, err.Error())
	}
	if err = json.Unmarshal(responseData, &dataApiClient); err != nil {
		return nil, fmt.Errorf("[master.data.client.co]Error al decodificar la respuesta JSON: %s", err.Error())
	}

	return dataApiClient, nil

}

func (m *MasterDataClient) updateClient(
	dataPolicies *PoliciesMasterDataClient,
	apiClientResponse *models.ApiClientResponse,
	headerData *lib_gen_proto.Cabecera,
	documentDescription string,
) error {
	client := headerData.Client
	dataUpdate := &models.UpdateClientOptin{}
	token, err := m.validateToken(dataPolicies)
	if err != nil {
		return err
	}
	dataClientResponse, err := utils.MapData(apiClientResponse.Data)
	if err != nil {
		return err
	}
	if valueClient, hasClient := dataClientResponse["cliente"]; hasClient {
		valueMap := valueClient.(map[string]interface{})
		if value, hasValue := valueMap["_id"]; hasValue {
			dataUpdate.UserId = value.(string)
		}
		//
		if value, hasValue := valueMap["aceptacionPoliticas"]; hasValue {

			newData := value.(bool)
			dataUpdate.Authentication = newData
			dataUpdate.AcceptancePolicies = newData
			dataUpdate.ShippingCommercialCommunications = newData
			dataUpdate.ShippingCommercialCommunicationsPush = newData
			dataUpdate.DataAnalysisProfiles = newData
			dataUpdate.AssignmentDataNationalOperators = newData
			dataUpdate.AssignmentDataInternationalCarriers = newData
		}

	}

	now := time.Now()
	nowFormat := now.UTC().Format("2006-01-02T15:04:05Z")

	dataUpdate.CdnId = strconv.Itoa(m.StoreData.ChainId)
	dataUpdate.Country = "COL"
	dataUpdate.SystemOrigin = "1"
	dataUpdate.TypeDocument = documentDescription
	dataUpdate.Email = client.Email
	dataUpdate.Phone = client.Phone
	dataUpdate.FirstName = client.Name
	dataUpdate.Surnames = ""
	dataUpdate.Address = client.Address
	dataUpdate.DateUpdate = nowFormat
	dataUpdate.DateAccepPrivacy = nowFormat

	jsonData, err := json.Marshal(dataUpdate)
	if err != nil {
		return fmt.Errorf("[master.data.client.co]Error al convertir datos a JSON: %s", err.Error())
	}
	//
	tr := &http.Transport{
		TLSClientConfig: &tls.Config{InsecureSkipVerify: true},
	}
	clientHttp := &http.Client{
		Transport: tr,
		Timeout:   ENDPOINT_TIMEOUT,
	}
	// Crear solicitud PUT
	url := dataPolicies.UrlPolicies + ENDPOINT_PUTCLIENT
	req, err := http.NewRequest("PUT", url, bytes.NewBuffer(jsonData))
	if err != nil {
		return fmt.Errorf("[master.data.client.co]Error al crear solicitud %s: %s", url, err.Error())
	}
	// Headers
	req.Header.Set("Content-Type", "application/json")
	req.Header.Set("Accept", "application/json")
	req.Header.Set("Authorization", "Bearer "+token.Token)
	resp, err := clientHttp.Do(req)
	if err != nil {
		return fmt.Errorf("[master.data.client.co]Error al enviar solicitud %s: %s", url, err.Error())
	}
	defer resp.Body.Close()
	// Leer la respuesta
	responseData, err := io.ReadAll(resp.Body)
	if err != nil {
		return fmt.Errorf("[master.data.client.co]Error al leer solicitud %s: %s", url, err.Error())
	}
	logger.Info.Println("APIMASTERDATACLIENTE -- Response data _putApiModify: " + string(responseData))
	return nil
}

func (m *MasterDataClient) createClient(
	dataPolicies *PoliciesMasterDataClient,
	headerData *lib_gen_proto.Cabecera,
	documentDescription,
	document string,
) error {
	client := headerData.Client
	dataCreate := &models.CreateClientOptin{}
	token, inErr := m.validateToken(dataPolicies)
	if inErr != nil {
		return inErr
	}

	now := time.Now()
	nowFormat := now.Format(time.RFC3339)
	dataCreate.CdnId = strconv.Itoa(m.StoreData.ChainId)
	dataCreate.Country = "COL"
	dataCreate.SystemOrigin = "1"
	dataCreate.Document = document
	dataCreate.TypeDocument = documentDescription
	dataCreate.Email = client.Email
	dataCreate.Phone = client.Phone
	dataCreate.FirstName = client.Name
	dataCreate.Surnames = ""
	dataCreate.Address = client.Address
	dataCreate.DateAccepPrivacy = nowFormat
	dataCreate.AcceptancePolicies = headerData.AcceptPromotions
	dataCreate.Authentication = false
	dataCreate.ShippingCommercialCommunications = false
	dataCreate.ShippingCommercialCommunicationsPush = false
	dataCreate.DataAnalysisProfiles = false
	dataCreate.AssignmentDataNationalOperators = false
	dataCreate.AssignmentDataInternationalCarriers = false
	// Convertir datos a JSON
	jsonData, err := json.Marshal(dataCreate)
	if err != nil {
		return fmt.Errorf("[master.data.client.co]Error al convertir datos a JSON: %s", err.Error())
	}
	logger.Info.Println("Datos a enviar:", string(jsonData))

	// Configuración para evitar verificación SSL (equivale a CURLOPT_SSL_VERIFYPEER = false)
	tr := &http.Transport{
		TLSClientConfig: &tls.Config{InsecureSkipVerify: true},
	}
	clientHttp := &http.Client{
		Transport: tr,
		Timeout:   ENDPOINT_TIMEOUT,
	}

	// Crear solicitud POST
	url := dataPolicies.UrlPolicies + ENDPOINT_POSTCLIENT
	req, err := http.NewRequest(http.MethodPost, url, bytes.NewBuffer(jsonData))
	if err != nil {
		return fmt.Errorf("[master.data.client.co]Error al crear solicitud %s: %s", url, err.Error())
	}
	// Configurar los encabezados
	req.Header.Set("Content-Type", "application/json")
	req.Header.Set("Accept", "application/json")
	req.Header.Set("Authorization", "Bearer "+token.Token)
	// Enviar la solicitud
	resp, err := clientHttp.Do(req)
	if err != nil {
		return fmt.Errorf("[master.data.client.co]Error al enviar solicitud %s: %s", url, err.Error())
	}
	defer resp.Body.Close()
	// Leer la respuesta
	responseData, err := io.ReadAll(resp.Body)
	if err != nil {
		return fmt.Errorf("[master.data.client.co]Error al leer solicitud %s: %s", url, err.Error())
	}

	var response map[string]interface{}
	if err = json.Unmarshal(responseData, &response); err != nil {
		return fmt.Errorf("[master.data.client.co]Error al decodificar JSON de respuesta %s: %s", url, err.Error())
	}
	if value, hasValue := response["_id"]; hasValue {
		clientId := value.(string)
		if utils.IsEmpty(clientId) {
			return errors.New("[master.data.client.co]No se genero un id al cliente")
		}
	}
	logger.Info.Println("APIMASTERDATACLIENTE -- Response data _postApiSave: " + string(responseData))
	return nil
}
func (m *MasterDataClient) validateToken(dataPolicies *PoliciesMasterDataClient) (*PoliciesMasterDataClient, error) {
	isExpired, err := utils.IsTokenExpired(dataPolicies.Token)
	if err != nil {
		return nil, err
	}

	if isExpired {
		tokenClient, err := m.generateNewTokenClient(dataPolicies)
		if err != nil {
			return nil, err
		}
		cacheMasterDataClient.Set(enums.MASTERDATACLIENT.String(), tokenClient, 32*time.Hour)
		dataPolicies.Token = tokenClient.Token
	}

	return dataPolicies, nil
}
func (m *MasterDataClient) generateNewTokenClient(dataPolicies *PoliciesMasterDataClient) (*models.TokenResponse, error) {

	var tokenResponse *models.TokenResponse
	generateTokenApi := &models.GenerateTokenApi{}

	generateTokenApi.ClientID = dataPolicies.ClientIdPolicies
	generateTokenApi.ClientSecret = dataPolicies.ClientSecretPolicies
	jsonPayload, err := json.Marshal(generateTokenApi)
	if err != nil {
		return nil, fmt.Errorf("[master.data.client.co]Error marshalling generateTokenApi")
	}
	urlToken := dataPolicies.UrlPolicies + ENDPOINT_TOKEN
	resp, err := http.Post(
		urlToken,
		"application/json",
		bytes.NewBuffer(jsonPayload),
	)
	if err != nil {
		return nil, fmt.Errorf("[master.data.client.co]Error al hacer la solicitud HTTP %s: %s", urlToken, err.Error())
	}
	defer resp.Body.Close()
	if resp.StatusCode != http.StatusOK {
		return nil, fmt.Errorf("error en la respuesta HTTP: %s", resp.Status)
	}
	responseData, err := io.ReadAll(resp.Body)
	if err != nil {
		return nil, fmt.Errorf("[master.data.client.co]Error en la respuesta HTTP %s: %s", urlToken, err.Error())
	}
	if err = json.Unmarshal(responseData, &tokenResponse); err != nil {
		return nil, fmt.Errorf("[master.data.client.co]Error al decodificar la respuesta JSON: %s", urlToken)
	}
	return tokenResponse, nil
}

func (m *MasterDataClient) getCollectionStringVariableB(collectionName, dataName string) (*string, error) {
	var value *string
	query := fmt.Sprintf("SELECT [config].[fn_ColeccionCadena_VariableB] (%v, '%v', '%v') AS aplica", m.StoreData.ChainId, collectionName, dataName)
	rows, err := m.connection.Query(query)
	if err != nil {
		return nil, fmt.Errorf("[master.data.client.co]Error al ejecutar la funcion %v: %v", query, err.Error())
	}
	defer rows.Close()
	for rows.Next() {
		err = rows.Scan(&value)
		if err != nil {
			return nil, fmt.Errorf("[master.data.client.co]Error al obtener los datos de la funcion %v: %v", query, err.Error())
		}
	}
	return value, nil
}

func (m *MasterDataClient) getCollectionStringVariableV(collectionName, dataName string) (*string, error) {
	var value *string
	query := fmt.Sprintf("SELECT [config].[fn_ColeccionCadena_VariableV] (%v, '%v', '%v') AS aplica", m.StoreData.ChainId, collectionName, dataName)
	rows, err := m.connection.Query(query)
	if err != nil {
		return nil, fmt.Errorf("[master.data.client.co]Error al ejecutar la funcion %v: %v", query, err.Error())
	}
	defer rows.Close()
	for rows.Next() {
		err = rows.Scan(&value)
		if err != nil {
			return nil, fmt.Errorf("[master.data.client.co]Error al obtener los datos de la funcion %v: %v", query, err.Error())
		}
	}
	return value, nil
}
