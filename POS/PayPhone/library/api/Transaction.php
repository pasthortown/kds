<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'library/common/PayPhoneWebException.php';

include_once 'library/models/request/SetRequestModel.php';
include_once 'library/models/request/TokenRequestModel.php';
include_once 'library/models/request/RefreshTokenRequestModel.php';
include_once 'library/models/response/ErrorResponseModel.php';
include_once 'library/models/response/ResultResponseModel.php';
include_once 'library/models/request/StatusByAppRequestModel.php';

include_once 'library/security/PayPhoneEncrypt.php';
include_once 'library/security/PayPhoneDecrypt.php';

/**
 * Clase para contenedora de las funciones de conexion
 * a los servicios de PayPhone.
 * @copyright (c) 2015, PayPhone
 * @version 1.0
 */
class Transaction {

    /**
     * Devuelve las regiones disponibles por PayPhone.
     * 
     * @throws \PayPhoneWebException
     * @return \RegionsResponseModel
     */
    public function GetAvailableRegions() {
        try {
            $uri = '/api/Regions/0/es';
            $response = $this->get_call($uri);
            return $response;
        } catch (PayPhoneWebException $exc) {
            throw $exc;
        } catch (Exception $exc) {
            throw $this->ThrowException($exc);
        }
    }

    /**
     * Envía una transaccion a payphone y notifica al usuario del cobro solicitado.
     * 
     * @param \TransactionRequestModel $model
     * @param string $phone_number
     * @param string $country_code
     * @return \NumberVerifyResponseModel
     * @throws \PayPhoneWebException
     * @throws \Exception
     */
    public function SetAndSendTransaction($model, $phone_number, $country_code) {
        try {
            $uri = '/api/Transaction/SetAndSendTransaction';

            $request = $this->encrypt_model($model);
            $request->PhoneNumber = $phone_number;
            $request->CountryCode = $country_code;

            $post_data = json_encode($request);

            $response = $this->post_call($uri, $post_data);

            return $response;
        } catch (PayPhoneWebException $exc) {
            throw $exc;
        } catch (Exception $exc) {
            throw $this->ThrowException();
        }
    }

    /**
     * Reserva una transacción en PayPhone pero aun no se le notifica al usuario
     * para notificar al usuario debe utilizar el método \SetTransaction
     * @param \TransactionRequestModel $model
     * @param string $phone_number
     * @param string $country_code
     * @return \NumberVerifyResponseModel
     * @throws \PayPhoneWebException
     * @throws \PayPhoneWebException
     */
    public function SendTransaction($model, $phone_number, $country_code) {
        try {

            $uri = '/api/transaction/NumberVerify';

            $request = $this->encrypt_model($model);
            $request->PhoneNumber = $phone_number;
            $request->CountryCode = $country_code;

            $post_data = json_encode($request);

            $response = $this->post_call($uri, $post_data);

            return $response;
        } catch (PayPhoneWebException $exc) {
            throw $exc;
        } catch (Exception $exc) {
            var_dump($exc);
            throw $this->ThrowException();
        }
    }

    /**
     * Ejecuta una Transacción en payphone con la cual se le notifica al usuario.
     * @param long $id
     * @throws \PayPhoneWebException
     * @throws \PayPhoneWebException
     */
    public function SetTransaction($id) {
        try {
            $uri = '/api/transaction/TransactionB2C';

            $request = new SetRequestModel();
            $request->TransactionId = $id;

            $post_data = json_encode($request);

            $response = $this->post_call($uri, $post_data);

            return $response;
        } catch (PayPhoneWebException $exc) {
            throw $exc;
        } catch (Exception $exc) {
            throw $this->ThrowException();
        }
    }

    /**
     * Obtiene el estado de la transaccion por su identificador. 
     * El identificador es entregado por PayPhone.
     * 
     * @param long $id
     * @return \BaseStatusResponseModel
     * @throws \Exception
     * @throws \PayPhoneWebException
     */
    public function GetStatus($id) {
        try {
            $uri = '/api/Transaction/GetStatus/' . $id;

            $response = $this->get_call($uri);

            if ($response->Status === 1) {
                return $response;
            }

            $result = $this->decrypt_model($response->Data);
            $data = new ResultResponseModel();
            $data->Status = $response->Status;
            $data->Success = $response->Success;
            $data->Message = $result;
            return $data;
        } catch (PayPhoneWebException $exc) {
            throw $exc;
        } catch (Exception $exc) {
            throw $this->ThrowException($exc);
        }
    }

    /**
     * Obtiene el estado de la anulación.
     * Cuando la anulación se aprobada se entrega los detalles de la transacción 
     * que se envio a anular.
     * @param long $id
     * @return \BaseStatusResponseModel
     * @throws PayPhoneWebException
     * @throws PayPhoneWebException
     */
    public function GetAnnulmentStatus($id) {
        try {
            $uri = '/api/Transaction/AnnulmentStatus/' . $id;

            $response = $this->get_call($uri);

            if ($response->Status === 1) {
                return $response;
            }

            $result = $this->decrypt_model($response->Data);
            $data = new ResultResponseModel();
            $data->Status = $response->Status;
            $data->Success = $response->Success;
            $data->Message = $result;
            return $data;
        } catch (PayPhoneWebException $exc) {
            throw $exc;
        } catch (Exception $exc) {
            throw $this->ThrowException($exc);
        }
    }

    /**
     * 
     * @param long $id
     * @return \BaseStatusResponseModel
     * @throws \PayPhoneWebException
     * @throws \PayPhoneWebException
     */
    public function GetReimbursementStatus($id) {
        try {
            $uri = '/api/Transaction/ReimbursementStatus/' . $id;

            $response = $this->get_call($uri);

            if ($response->Status === 1) {
                return $response;
            }

            $result = $this->decrypt_model($response->Data);
            $data = new ResultResponseModel();
            $data->Status = $response->Status;
            $data->Success = $response->Success;
            $data->Message = $result;
            return $data;
        } catch (PayPhoneWebException $exc) {
            throw $exc;
        } catch (Exception $exc) {
            throw $this->ThrowException($exc);
        }
    }

    /**
     * Cancela una transaccion dado el identificador entregado por PayPhone
     * @param long $id
     * @return boolean
     * @throws PayPhoneWebException
     * @throws PayPhoneWebException
     */
    public function Cancel($id) {
        try {
            $uri = '/api/Transaction/Cancel/' . $id;

            $response = $this->get_call($uri);

            return $response;
        } catch (PayPhoneWebException $exc) {
            throw $exc;
        } catch (Exception $exc) {
            throw $this->ThrowException($exc);
        }
    }

    /**
     * Reversa la transaccion dada un identificador entregado por PayPhone
     * este devuelve el identificador correspondiente al reverso que se desea realizar.
     * @param long $id
     * @return \ReimbursementResponseModel
     * @throws PayPhoneWebException
     * @throws PayPhoneWebException
     */
    public function Reimbursement($id) {
        try {
            $uri = '/api/Transaction/SetReimbursement/' . $id;

            $response = $this->get_call($uri);

            return $response;
        } catch (PayPhoneWebException $exc) {
            throw $exc;
        } catch (Exception $exc) {
            throw $this->ThrowException($exc);
        }
    }

    /**
     * Reversa la transaccion dado el ClientTransactionId entregado a PayPhone
     * este devuelve el identificador correspondiente al reverso que se desea realizar.
     * @param long $clientTransactionId
     * @return \ReimbursementResponseModel
     * @throws PayPhoneWebException
     * @throws PayPhoneWebException
     */
    public function ReimbursementByClientId($clientTransactionId) {
        try {
            $uri = '/api/Transaction/ReimbursementByClient';

            $model = new ReimbursementByClientRequestModel();
            $model->TransactionClientId = $clientTransactionId;

            $request = $this->encrypt_model($model);
            $post_data = json_encode($request);
            
            $response = $this->post_call($uri, $post_data);

            return $response;
        } catch (PayPhoneWebException $exc) {
            throw $exc;
        } catch (Exception $exc) {
            throw $this->ThrowException($exc);
        }
    }

    /**
     * Solicita anular una transacción. Este método retorna el identificador de 
     * la anulación.
     * @param \AnnulmentRequestModel $model
     * @return \AnnulmentResponseModel
     * @throws PayPhoneWebException
     * @throws PayPhoneWebException
     */
    public function SendAnnulment($model) {
        try {
            $uri = '/api/Transaction/Annulment';

            $request = $this->encrypt_model($model);

            $post_data = json_encode($request);

            $response = $this->post_call($uri, $post_data);

            return $response;
        } catch (PayPhoneWebException $exc) {
            throw $exc;
        } catch (Exception $exc) {
            throw $this->ThrowException();
        }
    }

    /**
     * 
     * @param string $id
     * @return \ResultResponseModel
     * @throws \PayPhoneWebException
     * @throws type \PayPhoneWebexception
     */
    public function GetTransactionStatusByAppId($id) {
        try {

            $uri = '/api/Transaction/GetStatusByApp';
            $config = ConfigurationManager::Instance();

            $model = new StatusByAppRequestModel();
            $model->ApplicationId = $config->ApplicationId;
            $model->ClientTransactionId = $id;

            $post_data = json_encode($model);

            $response = $this->post_call($uri, $post_data);
            echo 'dentro ' . $response;
            if ($response->Status === 1) {
                return $response;
            }

            $result = $this->decrypt_model($response->Data);
            $data = new ResultResponseModel();
            $data->Status = $response->Status;
            $data->Success = $response->Success;
            $data->Message = $result;
            return $data;
        } catch (PayPhoneWebException $exc) {
            throw $exc;
        } catch (Exception $exc) {
            throw $this->ThrowException($exc);
        }
    }

    /**
     * Obtiene el token de autenticacion por las credenciales
     * @param string $companyCode
     * @return type
     * @throws PayPhoneWebException
     * @throws PayPhoneWebException
     */
    public function GetToken($companyCode) {
        try {
            $uri = "/token";
            $config = ConfigurationManager::Instance();

            $model = new TokenRequestModel();
            $model->client_id = $config->ClientId;
            $model->client_secret = $config->ClientSecret;
            $model->company_code = $companyCode;
            $model->grant_type = "client_credentials";

            $post_data = $model->ToString();

            return $this->post_call($uri, $post_data);
        } catch (PayPhoneWebException $exc) {
            throw $exc;
        } catch (Exception $exc) {
            throw $this->ThrowException($exc);
        }
    }

    /**
     * Obtiene el token de autenticación por el refresh token
     * @param string $companyCode
     * @return type
     * @throws PayPhoneWebException
     * @throws PayPhoneWebException
     */
    public function GetTokenByRefreshToken($companyCode) {
        try {
            $uri = "/token";
            $config = ConfigurationManager::Instance();

            $model = new RefreshTokenRequestModel();
            $model->client_id = $config->ClientId;
            $model->refresh_token = $config->RefreshToken;
            $model->company_code = $companyCode;
            $model->grant_type = "refresh_token";

            $post_data = $model->ToString();

            return $this->post_call($uri, $post_data);
        } catch (PayPhoneWebException $exc) {
            throw $exc;
        } catch (Exception $exc) {
            throw $this->ThrowException($exc);
        }
    }

    /**
     * Ejecuta un POST a la url especificada
     * @param string $uri
     * @param \stdClass $post_data
     * @return \stdClass
     * @throws PayPhoneWebException
     */
    private function post_call($uri, $post_data, $content_type = "application/json") {

        $config = ConfigurationManager::Instance();
        $curl = curl_init($config->ApiPath . $uri);
        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $config->Token;
        $headers[] = 'Content-Type: ' . $content_type;
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);

        $curl_response = curl_exec($curl);

        $info = curl_getinfo($curl);
        curl_close($curl);

        $errors = array();
        switch ($info['http_code']) {
            case 200:
                return json_decode($curl_response);
            case 0:
                $temp = new ErrorResponseModel();
                $temp->Message = 'Lo sentimos por favor verifique su internet o cadena de conexi&oacute;n.';
                $errors[] = $temp;
                throw new PayPhoneWebException(null, $info['http_code'], $errors);
            default :
                $errors = json_decode($curl_response);
                if (!is_array($errors)) {
                    $temp = $errors;
                    $errors = array();
                    $errors[] = $temp;
                }
                throw new PayPhoneWebException(null, $info['http_code'], $errors);
        }
    }

    /**
     * Ejecuta un GET a la url especificada
     * @param string $uri
     * @return \stdClass
     * @throws PayPhoneWebException
     */
    private function get_call($uri) {
        $config = ConfigurationManager::Instance();

        $curl = curl_init($config->ApiPath . $uri);
        $headers = array();
        $headers[] = 'Authorization: Bearer ' . $config->Token;
        $headers[] = 'Content-Type: application/json';
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $curl_response = curl_exec($curl);

        $info = curl_getinfo($curl);
        curl_close($curl);

        $errors = array();
        switch ($info['http_code']) {
            case 200:
                return json_decode($curl_response);
            case 0:
                $temp = new ErrorResponseModel();
                $temp->Message = 'Lo sentimos por favor verifique su internet o cadena de conexi&oacute;n.';
                $errors[] = $temp;
                throw new PayPhoneWebException(null, $info['http_code'], $errors);
            default :
                $errors = json_decode($curl_response);
                if (!is_array($errors)) {
                    $temp = $errors;
                    $errors = array();
                    $errors[] = $temp;
                }
                throw new PayPhoneWebException(null, $info['http_code'], $errors);
        }
    }

    /**
     * Codifica los datos a enviar
     * 
     * @param TransactionRequestModel $model
     * @return \DataSend
     */
    private function encrypt_model($model) {
        $config = ConfigurationManager::Instance();
        $encrypt = new PayPhoneEncrypt($config->ApplicationPublicKey);

        $data = $encrypt->Execute($model, $config->ApplicationId);

        return $data;
    }

    private function decrypt_model($model) {
        $config = ConfigurationManager::Instance();
        $decrypt = new PayPhoneDecrypt($config->PrivateKey);

        $data = $decrypt->Execute($model);

        return $data;
    }

    /**
     * Transaforma una excepción genérica a la excepción que entrega PayPhone
     * @param Exception $e
     * @return \PayPhoneWebException
     */
    private function ThrowException(Exception $e) {
        $errors = array();

        $error = new ErrorResponseModel();
        $error->Message = $e->Message;

        $errors[] = $error;

        return new PayPhoneWebException(null, "500", $errors);
    }

}
