package strips

import (
	"bytes"
	"encoding/json"
	"errors"
	"fmt"
	"io"
	"lib-shared/utils"
	"lib-shared/utils/logger"
	"mime/multipart"
	"net/http"
	"new-order-store/internals/entity/models"
	"new-order-store/internals/infrastructure/cache"
	"time"
)

var CacheTokenStrips = cache.NewTTL[string, *models.TokenTirilla]()

type TotpStrip struct {
	DataStrip *models.EnvDataTirillas
}

func NewTotpStrip(DataStrip *models.EnvDataTirillas) *TotpStrip {
	return &TotpStrip{DataStrip: DataStrip}
}

func (totp *TotpStrip) GetGenerateTokenStrip() (*models.TokenTirilla, error) {
	var requestBody bytes.Buffer
	dataStrip := &models.TokenTirilla{}

	writer := multipart.NewWriter(&requestBody)

	err := writer.WriteField("client_id", totp.DataStrip.ClientIdTirillas)
	if err != nil {
		return nil, fmt.Errorf("[tirillas]Error interno al crear la solicitud de envio hacia el TOTP de tirillas: %v", err.Error())
	}

	err = writer.WriteField("client_secret", totp.DataStrip.ClientSecretTirillas)
	if err != nil {
		return nil, fmt.Errorf("[tirillas]Error interno al crear la solicitud de envio hacia el TOTP de tirillas: %v", err.Error())
	}

	err = writer.WriteField("grant_type", totp.DataStrip.GrantTypeTirillas)
	if err != nil {
		return nil, fmt.Errorf("[tirillas]Error interno al crear la solicitud de envio hacia el TOTP de tirillas: %v", err.Error())
	}

	// Finalizar la escritura del multipart
	err = writer.Close()
	if err != nil {
		return nil, fmt.Errorf("[tirillas]Error al finalizar la escritura del multipart de solicitud de envio hacia el FE-Kiosco: %v", err)
	}
	client := &http.Client{
		Timeout: 15 * time.Second,
	}

	req, err := http.NewRequest("POST", fmt.Sprintf("%v/auth/token", totp.DataStrip.UrlTotpTirillas), &requestBody) // replace nil with appropriate body if needed
	if err != nil {
		return nil, fmt.Errorf("[tirillas]Error creating request: %v", err)
	}
	// Establecer encabezados personalizados
	req.Header.Set("Accept", "application/json")
	req.Header.Set("Content-Type", writer.FormDataContentType())

	resp, err := client.Do(req)
	if err != nil {
		return nil, fmt.Errorf("[tirillas]Error, el servicio TOTP de tirillas no responde: %v", err)
	}
	defer resp.Body.Close()

	body, err := io.ReadAll(resp.Body)
	if err != nil {
		return nil, fmt.Errorf("[tirillas]Error, el servicio TOTP de tirillas no disponible: %v", err)
	}
	if err = json.Unmarshal(body, &dataStrip); err != nil {

		return nil, fmt.Errorf("[tirillas]Error, el servicio TOTP de tirillas no disponible: %v", err)
	}
	if dataStrip == nil || utils.IsEmpty(dataStrip.AccessToken) {
		return nil, errors.New("[tirillas]Error, no se genero un token al momento de consultar el servicio TOTP")
	}
	CacheTokenStrips.Set("strip", dataStrip, 1*time.Hour)
	return dataStrip, nil
}

func (totp *TotpStrip) CreateTotp(data *models.TirillasPromotionsKiosko) (*models.TirillasPromotionsKiosko, error) {
	// Obtener token desde cache o generar uno
	dataStrip, exists := CacheTokenStrips.Get("strip")
	if !exists {
		dataResponseStrip, err := totp.GetGenerateTokenStrip()
		if err != nil {
			return nil, err
		}
		dataStrip = dataResponseStrip
	} else {
		validateToken, err := utils.IsTokenExpired(dataStrip.AccessToken)
		if err != nil {
			return nil, fmt.Errorf("[tirillas]Error al validar el token de totp tirillas: %v", err)
		}
		if validateToken {
			dataStrip, err = totp.GetGenerateTokenStrip()
			if err != nil {
				return nil, err
			}
		}
	}

	// Función auxiliar para enviar la solicitud
	sendRequest := func(token string) (*http.Response, error) {
		jsonData, err := json.Marshal(data)
		if err != nil {
			return nil, fmt.Errorf("[tirillas]Error al convertir datos a JSON: %v", err)
		}
		logger.Debug("json a enviar a tirillas: ", string(jsonData))
		req, err := http.NewRequest("POST", fmt.Sprintf("%v/totp-promotion/create-totp", totp.DataStrip.UrlTotpTirillas), bytes.NewBuffer(jsonData))
		if err != nil {
			return nil, fmt.Errorf("[tirillas]Error al crear request: %v", err)
		}
		req.Header.Set("Content-Type", "application/json")
		req.Header.Set("Authorization", "Bearer "+token)
		clientHttp := &http.Client{}
		return clientHttp.Do(req)
	}

	// Primer intento
	resp, err := sendRequest(dataStrip.AccessToken)
	if err != nil {
		return nil, fmt.Errorf("[tirillas]Error al enviar la solicitud: %v", err)
	}

	// Si es 401, cerrar primer Body, regenerar token y reintentar
	if resp.StatusCode == http.StatusUnauthorized {
		resp.Body.Close()
		dataStrip, err = totp.GetGenerateTokenStrip()
		if err != nil {
			return nil, err
		}
		resp, err = sendRequest(dataStrip.AccessToken)
		if err != nil {
			return nil, fmt.Errorf("[tirillas]Error al reintentar la solicitud: %v", err)
		}
		if resp.StatusCode == http.StatusUnauthorized {
			resp.Body.Close()
			return nil, fmt.Errorf("[tirillas]token sigue siendo no autorizado (401) después de reintentar")
		}
	}

	defer resp.Body.Close()
	// Procesar respuesta
	responseData, err := io.ReadAll(resp.Body)
	if err != nil {
		return nil, fmt.Errorf("[tirillas]Error al leer la respuesta: %v", err)
	}
	logger.Debug("Resultado obtenido de tirillas: ", string(responseData))
	var response map[string]interface{}
	if err = json.Unmarshal(responseData, &response); err != nil {
		return nil, fmt.Errorf("[tirillas]Error al decodificar JSON de respuesta: %v", err)
	}

	totpValue, hasTotp := response["totp"]
	if !hasTotp {
		return nil, fmt.Errorf("[tirillas]Error, la respuesta del servicio no contiene 'totp'")
	}

	data.Code = fmt.Sprintf("%v", totpValue)
	return data, nil
}
