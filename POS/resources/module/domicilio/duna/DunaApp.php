<?php

$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__) . $ds . "..") . $ds;

include_once "{$base_dir}{$ds}../../../system{$ds}conexion{$ds}clase_sql.php";
include_once "{$base_dir}{$ds}clases/clase_duna.php";

class DunaApp extends sql
{
    public $duna;
    public $codFactura;
    public $cdn_id;
    public $rst_id;
    public $cabecera_factura;

    function __construct($cdn_id, $rst_id)
    {
        $this->duna =  new Duna();
        $this->cdn_id = $cdn_id;
        $this->rst_id = $rst_id;
    }


    function crearOrden($urlConsumo, $codigoFactura, $codigoApp)
    {

        $this->cabecera_factura = [];


        try {
            $lc_rowdatos = [];
            
            $lc_sql = "EXEC dbo.App_cargar_pedido_duna $this->cdn_id, $this->rst_id, '$codigoApp'";
            
            try {
                $this->duna->fn_ejecutarquery($lc_sql);
                while ($row = $this->duna->fn_leerarreglo()) {
                    $arr = array_map('utf8_encode', $row);
                    $lc_rowdatos["json_response"] = json_decode($arr[0],true);
                }
                $lc_rowdatos['registros'] = $this->duna->fn_numregistro();
            } catch (Exception $e) {
                return $e;
            }

            if ($lc_rowdatos["json_response"] == NULL) {
                $this->duna->insertaAuditoriaDuna($urlConsumo, 'Crear Orden: '.$codigoFactura , 500, 'Error, no se pudo cargar el json desde cabecera_app');
                return 'Error, no se pudo cargar el json desde cabecera_app';
            }


            if (array_key_exists('registros', $lc_rowdatos) && $lc_rowdatos['registros'] > 0) {

                $autentication = [];
                $autentication["identity_token"] = '';
                $lc_sql = "EXEC config.USP_ObtenerIdentityTokenDuna $this->cdn_id";

                try {
                    $this->duna->fn_ejecutarquery($lc_sql);
                    while ($row = $this->duna->fn_leerarreglo()) {
                        $autentication["identity_token"] = $row['identity_token'];
                    }
                    $autentication['registros'] = $this->duna->fn_numregistro();
                } catch (Exception $e) {
                    return $e;
                }

                $content = json_encode($lc_rowdatos['json_response']);

                $ch = curl_init($urlConsumo);

                $headers = array(
                    'Content-Type:application/json',
                    'Authorization:' . $autentication['identity_token']
                );

                curl_setopt_array($ch, array(
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $content,
                    CURLOPT_HTTPHEADER => $headers,
                    CURLOPT_RETURNTRANSFER => true,
                ));

                $result = curl_exec($ch);
                $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                curl_close($ch);

                $response = (json_decode($result, true));
                if ($status == 200) {
                    $idOrden = $response["data"]["order_id"];
                    $lat = $response["data"]["customer_lat"];
                    $lng = $response["data"]["customer_lng"];
                    $this->updateOrden($idOrden, $lat, $lng, $codigoFactura, $codigoApp, $result);
                    
                    $this->duna->insertaAuditoriaDuna($urlConsumo, 'Crear Orden: '.$codigoFactura.' '.$content, $status, json_encode($response));
                    return $result;
                } else {
                    $this->duna->insertaAuditoriaDuna($urlConsumo, 'Crear orden: ' . $codigoFactura, $status, $result);
                    return json_encode('Error, Status:'.$status);
                }
            } else {
                $this->duna->insertaAuditoriaDuna('', 'Crear orden: ' . $codigoFactura, 'ERROR', 'NO APLICA');
                return json_encode('NO APLICA');
            }
        } catch (Exception $e) {

            $this->duna->insertaAuditoriaDuna('', 'Crear orden: ' . $codigoFactura, 'ERROR', json_encode($e));
            return $e;
        }
    }

    /**
     * Permite crear la orden del motorizado
     * @fn crearOrdenMotorizado
     * @author Jean Meza
     * @param $urlConsumo url para notificar al motorizado
     * @param $codigo codigo del app
     * @cambio se creó esta funcion que permite asignar el motorizado de una y guardar en auditoria
     */
    function crearOrdenMotorizado($urlConsumo, $codigo)
    {
        try {
            $ch = curl_init();

            curl_setopt_array($ch, array(
                CURLOPT_URL => $urlConsumo,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'PUT',
            ));

            $result = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            $response = (json_decode($result, true));
            if ($status == 200 || $status == 204) { 
                $this->duna->insertaAuditoriaDuna($urlConsumo, 'Crear Orden Motorizado: '.$codigo, $status, json_encode($response));
                return $result;
            } else {
                $this->duna->insertaAuditoriaDuna($urlConsumo, 'Crear orden Motorizado: ' . $codigo, $status, $result);
                return json_encode('Error, Status:'.$status);
            }
        } catch (Exception $e) {
            $this->duna->insertaAuditoriaDuna('', 'Crear orden Motorizado: ' . $codigo, 'ERROR', json_encode($e));
            return $e;
        }
    }

    function updateOrden($idOrden, $lat, $lng, $codFactura, $codigoApp, $tramaDuna)
    {
        $lc_sql = "EXEC config.[IAE_UpdateCabeceraAppDuna] '1',$this->rst_id,'$codigoApp','$idOrden','$lat','$lng','$codFactura','$tramaDuna'";

        try {
            return $this->duna->fn_ejecutarquery($lc_sql);
        } catch (Exception $e) {
            return $e;
        }
    }


    function anularOrden($urlConsumo, $idDuna, $codFactura)
    {

        if ($idDuna != null) {
            $data = array(
                'order' => $idDuna,
                'status' => "anulado"
            );

            $content = json_encode($data);

            $url = $urlConsumo;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, ($url));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $content);

            $result = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $response = json_decode($result);
            if ($response->code === "success") {
                $this->duna->insertaAuditoriaDuna($url, 'Cancelar orden: ' . $codFactura, $status, $response->success);
                return 'OK';
            } else {
                $this->duna->insertaAuditoriaDuna($url, 'Cancelar orden: ' . $codFactura, $status, $result);
                return 'error';
            }
        }
    }
}
