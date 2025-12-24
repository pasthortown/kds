<?php

require_once ROOT_PATH."/system/conexion/clase_sql.php";

class ServicioMasterdata extends sql
{

    public function getConfigApi($idCadena)
    {
        try {
            $sql_url_base = "SELECT [config].[fn_ColeccionCadena_VariableV] ($idCadena, 'DATOS PERSONALES', 'URL') AS url;";
            $this->fn_ejecutarquery($sql_url_base);
            $arreglo_url_base = $this->fn_leerarreglo();
            $urlAPIMDM = $arreglo_url_base['url'];
    
            $sql_idCliente = "SELECT [config].[fn_ColeccionCadena_VariableV] ($idCadena, 'DATOS PERSONALES', 'CLIENTID') AS idApi;";
            $this->fn_ejecutarquery($sql_idCliente);
            $arreglo_idCliente = $this->fn_leerarreglo();
            $api_clientID = $arreglo_idCliente['idApi'];
    
            $sql_secretCliente = "SELECT [config].[fn_ColeccionCadena_VariableV] ($idCadena, 'DATOS PERSONALES', 'CLIENTSECRET') AS secretApi;";
            $this->fn_ejecutarquery($sql_secretCliente);
            $arreglo_secretCliente  = $this->fn_leerarreglo();
            $api_secretCliente = $arreglo_secretCliente['secretApi'];
    
            $result = [
                "url"=>$urlAPIMDM,
                "clientid"=>$api_clientID,
                "clientsecret"=>$api_secretCliente
            ];
            return $result;
        } catch (PDOException $th) {
            throw new Exception("Failed clase_masterdatacliente in getConfigApi: " . $th->getMessage());
        } catch (Exception $th) {
            throw new Exception("Failed clase_masterdatacliente in getConfigApi: " . $th->getMessage());
        }
    }

    public function insertLogApi($usr_id,$rst_id,$descripcion,$accion_audit)
    {
        try {
            if ($descripcion != null AND is_array($descripcion) AND isset($descripcion['response'])) 
                $descripcion['response'] = str_replace("'", "", $descripcion['response']);
            $response = json_encode($descripcion);
            $lc_sql = "EXEC [seguridad].[IAE_Audit_registro] 'i','$usr_id',$rst_id,'API MASTERDATACLIENTE','$response',$accion_audit";
            return $this->fn_ejecutarquery($lc_sql);
        } catch (PDOException $th) {
            throw new Exception("Failed clase_masterdatacliente in getConfigApi: " . $th->getMessage());
        } catch (Exception $th) {
            throw new Exception("Failed clase_masterdatacliente in getConfigApi: " . $th->getMessage());
        }
    }
}