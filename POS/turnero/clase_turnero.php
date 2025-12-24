<?php
class Turnero extends sql {

    function getNumericValues($input) {
      $numbers = '';
      for ($i=0; $i < strlen($input) ; $i++){
        if (is_numeric($input[$i])) {
          $numbers .= $input[$i];
        }
      }
      return $numbers;
    }

    function getOrder() {
      $lc_sql = "EXEC pedido.TRANSACCIONES_cargar";
      try {
        $this->fn_ejecutarquery($lc_sql);
        while ($row = $this->fn_leerarreglo()) {
          $this->lc_regs[] = array(
            "id" => $row['transaccion']
            , "announce" => $this->getNumericValues($row['orden'])
            , "text" => $row['orden']
            , "status" => $row['estado']
          );
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
      } catch (Exception $e) {
        return $e;
      }
      return json_encode($this->lc_regs);
    }

    function getTheme() {
      $lc_sql = "EXEC config.Turnero_Tema 1";
      try {
        $this->fn_ejecutarquery($lc_sql);
        while ($row = $this->fn_leerarreglo()) {
          $this->lc_regs[] = array(
            "tema" => $row['variableV']
          );
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
      } catch (Exception $e) {
        return $e;
      }
      return json_encode($this->lc_regs);
    }

    function getTimesToMoveOrders() {
      $lc_sql = "EXEC [config].[Turnero_Tiempos]";
      try {
        $this->fn_ejecutarquery($lc_sql);
        while ($row = $this->fn_leerarreglo()) {
          $this->lc_regs[] = array(
            "tiempo" => $row['Descripcion'],
            "valor" => $row['variableI']
          );
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
      } catch (Exception $e) {
        return $e;
      }
      return json_encode($this->lc_regs);
    }

    function updateOrderState($id) {      
        $lc_sql = "EXEC pedido.TRANSACCIONES_actualizar @idFactura='$id'";

        $condicionConOrdenP = function($row) {
            return strpos($row["orden"], "P") !== false;
        };

        $estado_config = [
            'Entregada' => [
                'condicion' => $condicionConOrdenP,
                'webservice' => 'PICKUP TRADE',
                'ruta' => 'CONFIRMAR PEDIDO',
                'status_api' => 'Entregado'
            ],
            'Listo' => [
                'condicion' => $condicionConOrdenP,
                'webservice' => 'APP',
                'ruta' => 'CAMBIO ESTADO',
                'status_api' => 'En Camino'
            ]
        ];

        try {
            $i = 0;
            $this->fn_ejecutarquery($lc_sql);

            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "id" => $row['transaccion']
                    , "announce" => $this->getNumericValues($row['orden'])
                    , "text" => $row['orden']
                    , "status" => $row['estado']
                );

                if (isset($estado_config[$row['estado']]) && $estado_config[$row['estado']]['condicion']($row)) {
                    $config = $estado_config[$row['estado']];

                    // Obtener los parámetros cdn_id y rst_id desde la base de datos
                    $lc_sql_initial_params = "EXEC pedido.TRANSACCIONES_cargar_CadenaRestaurante 'iniciar'"; 

                    try { 
                        $this->fn_ejecutarquery($lc_sql_initial_params); 
                        $initial_params = $this->fn_leerarreglo(); 

                        if ($initial_params) { 
                            $cdn_id = $initial_params['idCadena']; 
                            $rst_id = $initial_params['idRestaurnte']; 
                        } else { 
                            throw new Exception("No se pudieron obtener los parámetros cdn_id y rst_id."); 
                        } 
                    } catch (Exception $e) { 
                        return json_encode(array("error" => "Error al obtener los parámetros iniciales: " . $e->getMessage()));
                    }

                    // Obtener los parámetros para la autenticación desde la base de datos 
                    $lc_sql_token_params = "EXEC pedido.SEGURIDAD_parametrosToken @cdn_id='$cdn_id', @rstId='$rst_id'"; 
                    try { 
                        $this->fn_ejecutarquery($lc_sql_token_params); 
                        $token_params = $this->fn_leerarreglo();

                        if ($token_params) { 
                            $token_url = $token_params['ws']; 
                            $grant_type = $token_params['grant_type']; 
                            $client_id = $token_params['client_id']; 
                            $client_secret = $token_params['client_secret']; 
                        } else { 
                            throw new Exception("No se pudieron obtener los parámetros de autenticación."); 
                        } 
                    } catch (Exception $e) { 
                        return json_encode(array("error" => "Error al obtener los parámetros de autenticación: " . $e->getMessage()));
                    }

                    // Obtener el token de autenticación 
                    $post_fields = json_encode([
                        'grant_type' => $grant_type,
                        'client_id' => $client_id,
                        'client_secret' => $client_secret
                    ]);

                    $ch = curl_init(); 
                    curl_setopt($ch, CURLOPT_URL, $token_url); 
                    curl_setopt($ch, CURLOPT_POST, true); 
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields); 
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' )); 
                    $response = curl_exec($ch); 
                    curl_close($ch);

                    $token_data = json_decode($response, true); 
                    if (!isset($token_data['access_token'])) { 
                        return json_encode(array("error" => "Error al obtener el token de autenticación.")); 
                    } 
                    $access_token = $token_data['access_token'];

                    // Obtener la URL del endpoint específico desde la base de datos 
                    $lc_sql_endpoint = "EXEC config.USP_Retorna_Direccion_Webservice @rst_id='$rst_id', @nombreWebService=N'" . $config['webservice'] . "', @nombreruta=N'" . $config['ruta'] . "', @secundario=0;";

                    try { 
                        $this->fn_ejecutarquery($lc_sql_endpoint); 
                        $endpoint_params = $this->fn_leerarreglo();

                        if ($endpoint_params) { 
                            $order_update_url = $endpoint_params['direccionws'];
                        } else { 
                            throw new Exception("No se pudo obtener la URL del endpoint específico."); 
                        } 
                    } catch (Exception $e) { 
                        return json_encode(array("error" => "Error al obtener la URL del endpoint específico: " . $e->getMessage()));
                    }

                    // Realizar la petición POST al endpoint específico con el token de autenticación 
                    $order_data = json_encode([
                        "order_id" => $row['codigo_app'],
                        "codigo_app" => $row['codigo_app'],
                        "status" => $config['status_api']
                    ]);

                    $ch = curl_init(); 
                    curl_setopt($ch, CURLOPT_URL, $order_update_url); 
                    curl_setopt($ch, CURLOPT_POST, true); 
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $order_data); 
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Authorization: Bearer ' . $access_token,
                        'Content-Type: application/json'
                    ]);
                    $response = curl_exec($ch); 
                    curl_close($ch); 

                    // Procesar la respuesta de la petición POST si es necesario 
                    $order_response = json_decode($response, true); 
                    $this->lc_regs[$i]['ws_status'] = $order_response;
                }

                $i++;
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }

        return json_encode($this->lc_regs);
    }

    function updateTheme($tema) {
      $lc_sql = "EXEC config.Turnero_Tema 2, '$tema'";
      try {
        $this->fn_ejecutarquery($lc_sql);
        while ($row = $this->fn_leerarreglo()) {
          $this->lc_regs[] = array(
            "status" => $row['status']
          );
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
      } catch (Exception $e) {
        return $e;
      }
      return json_encode($this->lc_regs);
    }

    function deleteOrder($id) {      
      $lc_sql = "pedido.TRANSACCIONES_eliminar @idFactura='$id'";
      try {
        $this->fn_ejecutarquery($lc_sql);
        while ($row = $this->fn_leerarreglo()) {
          $this->lc_regs[] = array(
            "id" => $row['idFactura']
            , "estado" => $row['estado']
            , "mensaje" => $row['mensaje']
          );
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
      } catch (Exception $e) {
        return $e;
      }
      return json_encode($this->lc_regs);
    }
}
