<?php

class TirillaPromocionOTP {

    public static function validacionCodigoOTP($idCadena, $idRestaurante, $codigo_otp)
    {
        $sql = new sql();
        $query = "EXEC config.USP_Retorna_Direccion_Webservice " . $idRestaurante . ", 'OTP API', 'TIRILLA PROMOCION', 0";

        try {
            $authResponse = self::autenticacion($idCadena, $idRestaurante);
            $result = json_decode($authResponse, true);

            if (!isset($result['access_token'])) {
                return json_encode(array(
                    "status" => 401,
                    "message" => "Error en autenticación: No se recibió el token de acceso",
                ));
            }

            $token = $result['access_token']; // Obtener el token de acceso

            $sql->fn_ejecutarquery($query);
            $row = $sql->fn_leerarreglo();
            set_time_limit(120); // 2 MINS

            $objeto_otp = array(
                'restaurantName' => strval($idRestaurante),
                'code'      => $codigo_otp
            );

            $url = $row['direccionws'] . '/api/v1/totp-promotion/retrieve-data';
            $url = str_replace("http://https://", "https://", $url);
            $url = str_replace("http://http://", "http://", $url);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $token
            ));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($objeto_otp));

            $result = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Comprobar si ocurrió algún error
            if (curl_errno($ch) || $status == 0) {
                curl_close($ch);
                return json_encode(array(
                    "status"   => $status,
                    "response" => $result,
                    "message"  => "Ocurrió un error en CURL o estado 0: " . curl_error($ch),
                    "payload"  => $objeto_otp
                ));
            }

            curl_close($ch);

            $result = json_decode($result, true);
            $responseArray = array(
                "status"    => $status,
                "response"  => $result,
                "message"   => array_key_exists('message', $result) && $result['message'] !== null ? $result['message'] : '',
                "payload"   => $objeto_otp
            );

            return json_encode($responseArray);

        } catch (Exception $e) {
            return json_encode(array(
                "status"  => 500,
                "message" => "Error: " . $e->getMessage()
            ));
        }
    }

    public static function consumoCodigoOTP($idCadena, $idRestaurante, $codigo_otp, $codigo_factura, $idUsuario)
    {
        $sql = new sql();
        $query = "EXEC config.USP_Retorna_Direccion_Webservice " . $idRestaurante . ", 'OTP API', 'TIRILLA PROMOCION', 0";

        try {
            $authResponse = self::autenticacion($idCadena, $idRestaurante);
            $result = json_decode($authResponse, true);

            if (!isset($result['access_token'])) {
                return json_encode(array(
                    "status" => 401,
                    "message" => "Error en autenticación: No se recibió el token de acceso",
                ));
            }

            $token = $result['access_token'];

            $sql->fn_ejecutarquery($query);
            $row = $sql->fn_leerarreglo();
            set_time_limit(120); // 2 MINS

            $objeto_otp = array(
                'restaurantName' => strval($idRestaurante),
                'code'      => json_decode($codigo_otp),
                'internalReference'    => $codigo_factura
            );

            $url = $row['direccionws'] . '/api/v1/totp-promotion/apply-used';
            $url = str_replace("http://https://", "https://", $url);
            $url = str_replace("http://http://", "http://", $url);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $token
            ));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($objeto_otp));

            $result = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Comprobar si ocurrió algún error
            if (curl_errno($ch) || $status == 0) {
                $description = 'Consumo OTP: ' . $codigo_otp . ' Factura: ' . $codigo_factura . json_encode(array("mensaje" => "Ocurrió un error con la petición CURL", "respuesta" => "ocurrio un error CURL o estado 0: " . curl_error($ch)), true);
                self::insertaAuditoriaTirillaOTP($idRestaurante, 'FACTURACION', $description, 'INFO', $idUsuario);
                curl_close($ch);
                return json_encode(array(
                    "status"   => $status,
                    "message"  => "Ocurrió un error CURL o estado 0: " . curl_error($ch),
                    "payload"  => $objeto_otp
                ));
            }

            curl_close($ch);
            $description = 'Consumo OTP: ' . $codigo_otp . ' Factura: ' . $codigo_factura . ' Status: ' . $status . ' Resultado: ' . $result;
            self::insertaAuditoriaTirillaOTP($idRestaurante, 'FACTURACION', $description, 'INFO', $idUsuario);

            $query = "INSERT INTO dbo.Auditoria_Transaccion (rst_id, atran_fechaaudit, atran_modulo, atran_descripcion, atran_accion, IDUsersPos)
                    VALUES ('$idRestaurante', GETDATE(), 'FACTURACION', '$description', 'info', '$idUsuario')";
            $sql->fn_ejecutarquery($query);

            $result = json_decode($result, true);
            $message = $status == 200 ? 'El código OTP se aplicó correctamente' : '';
            $responseArray = array(
                "status"    => $status,
                "message"   => array_key_exists('message', $result) && $result['message'] !== null ? $result['message'] : $message,
                "payload"   => $objeto_otp
            );

            return json_encode($responseArray);

        } catch (Exception $e) {
            return json_encode(array(
                "status"  => 500,
                "message" => "Error: " . $e->getMessage()
            ));
        }
    }


    public static function autenticacion($idCadena, $idRestaurante)
    {
        $sql = new sql();
        
        try {

            $query = "EXEC config.USP_Retorna_Direccion_Webservice ".$idRestaurante.", 'OTP API', 'TIRILLA PROMOCION', 0";
            $sql->fn_ejecutarquery($query);
            $row = $sql->fn_leerarreglo();
            set_time_limit(120);

            $query = "EXEC [dbo].[getConfigPoliticaRestaurante] '$idCadena', $idRestaurante, 'TIRILLAS CONFIGS', 'CLIENT_ID'";
            $sql->fn_ejecutarquery($query);
            $rowClientId = $sql->fn_leerarreglo();
            $client_id = $rowClientId['variableV'];

            $query = "EXEC [dbo].[getConfigPoliticaRestaurante] '$idCadena', $idRestaurante, 'TIRILLAS CONFIGS', 'CLIENT_SECRET'";
            $sql->fn_ejecutarquery($query);
            $rowClientSecret = $sql->fn_leerarreglo();
            $client_secret = $rowClientSecret['variableV'];

            $query = "EXEC [dbo].[getConfigPoliticaRestaurante] '$idCadena', $idRestaurante, 'TIRILLAS CONFIGS', 'GRANT_TYPE'";
            $sql->fn_ejecutarquery($query);
            $rowGrantType = $sql->fn_leerarreglo();
            $grant_type = $rowGrantType['variableV'];

            $data = array(
                'client_id'     => $client_id,
                'client_secret' => $client_secret,
                'grant_type'    => $grant_type
            );

            $postfields = http_build_query($data);

            $url = $row['direccionws'] . '/api/v1/auth/token';
            $url = str_replace("http://https://", "https://", $url);
            $url = str_replace("http://http://", "http://", $url);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded', 'Accept: application/json'));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);

            $result = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch) || $status == 0) {
                return json_encode(array(
                    "status"   => $status,
                    "message"  => "Ocurrió un error CURL o estado 0: " . curl_error($ch),
                    "payload"  => $data
                ));
            }

            curl_close($ch);
            $result = json_decode($result, true);

            $accessToken = isset($result['access_token']) ? $result['access_token'] : "NO EXISTE";
            $expiresIn = isset($result['expires_in']) ? $result['expires_in'] : null;
            $tokenType = isset($result['token_type']) ? $result['token_type'] : null;

            return json_encode(array(
                "access_token" => $accessToken,
                "expires_in"   => $expiresIn,
                "token_type"   => $tokenType
            ));

        } catch (Exception $e) {
            return json_encode(array(
                "status"  => 500,
                "message" => "Error: " . $e->getMessage()
            ));
        }
    }

    public static function insertaAuditoriaTirillaOTP($rst_id, $atran_modulo, $atran_descripcion,$atran_accion,$IDUsersPos) {
        $sql = new sql();
        $query = "INSERT INTO dbo.Auditoria_Transaccion ( rst_id, atran_fechaaudit, atran_modulo, atran_descripcion, atran_accion, IDUsersPos)
            VALUES ( '$rst_id',  GETDATE(), '$atran_modulo', '$atran_descripcion', '$atran_accion', '$IDUsersPos')";

        return $sql->fn_ejecutarquery($query);
    }

    public static function obtenerProducto($idRestaurante, $productId, $odp_id, $cat_id, $menuId) {
        $sql = new sql();
        $query = "EXEC [pedido].[getProducto]  $idRestaurante,'$productId','$odp_id','$cat_id','$menuId'";
        try {
            $sql->fn_ejecutarquery($query);
            $data = $sql->fn_leerarreglo();
            return $data;
        } catch (Exception $e) {
            return $e;
        }
    }
}

