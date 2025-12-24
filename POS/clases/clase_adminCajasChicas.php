<?php

$pathToken="../tokens/MainApiToken.php";

if (!file_exists($pathToken)) {
    $pathToken = "../$pathToken";
} 

include_once $pathToken;

////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: ANDRES ROMERO/////////////////////////////////////////////
///////////DESCRIPCION: DES-RELACIONAR CAJAS CHICAS ////////////////////////////////
////////////////API: servicios web sir /////////////////////////////////////////////
////////FECHA CREACION: 03/02/2022//////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////

class cajasChicas extends sql {
    function __construct() {
        parent ::__construct();
    }

    function fn_consultar($lc_sqlQuery, $lc_datos) {

        $token = apiTokenIntegracion($_SESSION['cadenaId'],'TokenOrdenPedido');
        $tokenType = trim(apiTokenIntegracion($_SESSION['cadenaId'],'TokenTypeOrdenPedido'));
        $tokenHeader = "Authorization: ".$tokenType." ".$token;

        switch ($lc_sqlQuery) {
            case "ruta_servidor":
                $lc_sql = "EXEC [config].[USP_Retorna_Direccion_Webservice] $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', $lc_datos[3]";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
							"estado" => $row[0],
                            "urlwebservice" => utf8_decode($row[1])
						);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
            break;

            case "consultar_localizacion":
                $lc_sql = "SELECT rst_localizacion as localizacion FROM Restaurante WHERE rst_id = '$lc_datos[0]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
							"localizacion" => $row['localizacion']
                        );
                    }
                }
                // $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
            break;
                
            case "cargarListaCajasChicas":
                //api= /api/caja-chica/consulta
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $lc_datos[2].'?desdeFecha='.$lc_datos[0].'&hastaFecha='.$lc_datos[1].'&localizacion='.$lc_datos[2] ,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', $tokenHeader)
                ));
                $response = curl_exec($curl);
                $estado = curl_getinfo($curl);
                curl_close($curl);

                if ($estado['http_code'] != 200) {

                    apiTokenIntegracion($_SESSION['cadenaId'],'CrearToken');
                    $token = apiTokenIntegracion($_SESSION['cadenaId'],'TokenOrdenPedido');
                    $tokenType = trim(apiTokenIntegracion($_SESSION['cadenaId'],'TokenTypeOrdenPedido'));
                    $tokenHeader = "Authorization: ".$tokenType." ".$token;

                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                    CURLOPT_URL => $lc_datos[2].'?desdeFecha='.$lc_datos[0].'&hastaFecha='.$lc_datos[1].'&localizacion='.$lc_datos[2] ,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', $tokenHeader)
                ));

                    $response = curl_exec($curl);
                }

                return $response;
			break;

            case "desrelacionarCajasChicas":
                //api= /api/caja-chica/desrelacionar
                $curl = curl_init();
                $body = '{"listaCodCierreChica":"'.$lc_datos[1].'","listaCajasChicas":"'.$lc_datos[2].'","listaMovInv":"'.$lc_datos[3].'","localizacion":"'.$lc_datos[4].'"}';
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $lc_datos[0],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_FAILONERROR => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    // CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_POST=>1,
                    CURLOPT_POSTFIELDS=>$body,
                    CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', $tokenHeader)
                ));
                $response = curl_exec($curl);
                $estado = curl_getinfo($curl);


                if ($estado['http_code'] != 200) {

                    apiTokenIntegracion($_SESSION['cadenaId'],'CrearToken');
                    $token = apiTokenIntegracion($_SESSION['cadenaId'],'TokenOrdenPedido');
                    $tokenType = trim(apiTokenIntegracion($_SESSION['cadenaId'],'TokenTypeOrdenPedido'));
                    $tokenHeader = "Authorization: ".$tokenType." ".$token;

                    $curl = curl_init();
                    $body = '{"listaCodCierreChica":"'.$lc_datos[1].'","listaCajasChicas":"'.$lc_datos[2].'","listaMovInv":"'.$lc_datos[3].'","localizacion":"'.$lc_datos[4].'"}';
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $lc_datos[0],
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_FAILONERROR => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        // CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_POST=>1,
                        CURLOPT_POSTFIELDS=>$body,
                        CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', $tokenHeader)
                    ));

                    $response = curl_exec($curl);
                }

                if (curl_error($curl)) {
                    $response = trigger_error('Curl Error:' . curl_error($curl));
                }
                curl_close($curl);


                return $response;
            break;


            case "cajasChicasSIR":
                //api= /api/caja-chica/eliminar
                $url = "$lc_datos[0]?accion=$lc_datos[1]&cod_cajero=$lc_datos[2]&cod_restaurante=$lc_datos[3]&fecha=$lc_datos[4]&localizacion=$lc_datos[5]";
                
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', $tokenHeader));
                curl_setopt($curl, CURLOPT_TIMEOUT, 5);
                curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);

                $response = curl_exec($curl);
                $estado = curl_getinfo($curl);

                if ($estado['http_code'] != 200) {

                    apiTokenIntegracion($_SESSION['cadenaId'],'CrearToken');
                    $token = apiTokenIntegracion($_SESSION['cadenaId'],'TokenOrdenPedido');
                    $tokenType = trim(apiTokenIntegracion($_SESSION['cadenaId'],'TokenTypeOrdenPedido'));
                    $tokenHeader = "Authorization: ".$tokenType." ".$token;
                                    
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', $tokenHeader));
                    curl_setopt($curl, CURLOPT_TIMEOUT, 5);
                    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);

                    $response = curl_exec($curl);
                }

                curl_close($curl);
                return $response;
			break;
            
        }
    }       
    
}