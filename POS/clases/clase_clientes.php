<?php

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Clase para CLIENTES//////////////////////
/////////////////////En las Mesas/////////////////////////////
///////TABLAS INVOLUCRADAS: CLIENTES/////////// //////////////
//////////////////////////////////////////////////////////////
///////FECHA CREACION: 19-Febrero-2014////////////////////////
//////////////////////////////////////////////////////////////

class cliente extends sql {

    //constructor de la clase
    function __construct() {
        //con herencia 
        parent::__construct();
    }

    function fn_buscarCliente($documento, $revocado = 0, $tipoDocumento = '') {
        $parametroTipoDocumento = ($tipoDocumento == '') ? 'NULL' : $tipoDocumento;
        $this->lc_regs = array();
        $lc_sql = "EXEC [facturacion].[CLIENTE_USP_BusquedaCliente] 'B', '$documento', 0, $parametroTipoDocumento, '$revocado'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs = array(
                    "IDCliente" => $row['IDCliente'],
                    "documento" => $row['documento'],
                    "descripcion" => strtoupper(utf8_encode(trim($row['descripcion']))),
                    "direccion" => strtoupper(utf8_encode(trim($row['direccion']))),
                    "telefono" => trim($row['telefono']),
                    "email" => utf8_encode(trim($row['email'])),
                    "tipoCliente" => $row['tipoCliente']);
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return json_encode($e);
        }
        return $this->lc_regs;
    }

    function fn_clienteRepetido($documento) {
        $lc_sql = "EXEC [facturacion].[CLIENTE_USP_BusquedaCliente] 'V', '$documento[0]', 0";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs["existe"] = $row["existe"];
            }
            $this->lc_regs["str"] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    function fn_consultabasefactura($cadena) {
        $lc_sql = "EXEC [facturacion].[CLIENTE_USP_BusquedaCliente] 'F', '0', $cadena";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['pais_base_factura'] = $this->ifNum($row["pais_base_factura"]);
                $this->lc_regs['Cliente'] = $row["Cliente"];
                $this->lc_regs['Documento'] = $row["Documento"];
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return $this->lc_regs;
    }

    function fn_registrarClienteWS($accion, $tipoConsulta, $tipoDocumento, $documento, $descripcion, $direccion, $telefono, $correo, $usuario, $estadoWS, $tipoCliente) {

        $lc_sql = "EXEC [config].[IAE_Cliente] $accion, $tipoConsulta, '$tipoDocumento', '$documento', '$descripcion', '$direccion', '$telefono', '$correo', '$usuario', $estadoWS, '$tipocliente'";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();

            $data = array("IDCliente" => '');
            if(isset($row['IDCliente'])){
                $data = array("IDCliente" => $row['IDCliente']);
            }

            $this->lc_regs = $data;
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return false;
        }
        return $this->lc_regs;
    }

    function fn_registroClienteLocal($lc_cliente) {
        $lc_sql = "EXEC [config].[IAE_Cliente] '$lc_cliente[0]', 'L', '$lc_cliente[1]', '$lc_cliente[2]', '$lc_cliente[3]', '$lc_cliente[4]', '$lc_cliente[5]', '$lc_cliente[6]', '$lc_cliente[7]', $lc_cliente[8], '$lc_cliente[9]','".$_SESSION['rstId']."'";
        try {
            $result = $this->fn_ejecutarquery($lc_sql);
            if ($result){ return true; }else{ return false; };
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    function fn_registroClienteLocalPayphone($lc_cliente , $json) {
        $lc_sql = "EXEC [config].[IAE_ClientePayphone] '$lc_cliente[0]',  '$lc_cliente[1]', '$lc_cliente[2]', '$lc_cliente[3]', '$lc_cliente[4]', '$lc_cliente[5]', '$lc_cliente[6]', '$lc_cliente[7]', '$lc_cliente[8]', '$lc_cliente[9]', '$lc_cliente[10]', '$json'";
        try {
            $result = $this->fn_ejecutarquery($lc_sql);
            if ($result){ return true; }else{ return false; };
            return true;
        }catch (\Exception $e){
            return false;
        }
    }

    function fn_solicitaCredencialesAdministrador($lc_cliente) {
        $lc_sql = "EXECUTE [facturacion].[USP_ClienteSolicitaCredenciales] $lc_cliente[0], '$lc_cliente[1]', '$lc_cliente[2]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs["solicita"] = $row["solicita"];
            }
            $this->lc_regs["str"] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    function fn_actualizarEstado_WS($accion, $documento, $dato) {
        $lc_sql = "EXEC [config].[USP_consultaIngresaCliente] " . $accion . ", '',  '',  '$documento',  '',  '',  '',  '',  '',  0,  '$dato',  '$dato' ";
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array("IDCliente" => $row['IDCliente']);
            $this->lc_regs['str'] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
    }

    function fn_cargaDatosClienteKiosko($cfac_id) {
        $lc_sql = "EXEC [dbo].[kiosko_obtenerDatosClienteTransaccion] '". $cfac_id."'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs = array(
                    "tipoDocumento" => $row['tipoDocumento'],
                    "idCliente" => $row['idCliente'],
                    "documento" => $row['documento'],
                    "nombres" => utf8_encode($row['nombres']),
                    "direccion" => utf8_encode($row['direccion']),
                    "telefono" => $row['telefono'],
                    "email" => utf8_encode($row['email'])
                );
            }
            $this->lc_regs["str"] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }

    function fn_guardaActualizaCliente($clienteTipoDoc, $clienteDocumento, $clienteDescripcion, $clienteDireccion, $clienteTelefono, $clienteCorreo, $usuario) {
        $this->lc_regs = array();
        $lc_sql = "EXEC [config].[insertaActualizaCliente] '$clienteTipoDoc', '$clienteDocumento', '$clienteDescripcion', '$clienteDireccion', '$clienteTelefono', '$clienteCorreo', '$usuario'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            $row = $this->fn_leerarreglo();
                $this->lc_regs = array("idCliente" => $row['idCliente']);
            return json_encode($this->lc_regs);
        }else{
            return json_encode($this->lc_regs);
        }
        
    }
  
    function rutaBinarioPHP( $ID_Restaurante )
    {
        $this->lc_regs = array();

        $lc_sql = "EXEC	[config].[USP_ObtenerRutaEjecutablePHP] '$ID_Restaurante';";

        if( $this->fn_ejecutarquery( $lc_sql ) ) 
        {
            $row = $this->fn_leerarreglo();
            if ($row !== null){
                $this->lc_regs["rutaBinarioPHP"] = $row["rutaEjecutablePHP"];
            }                                                    
            $this->lc_regs["str"] = $this->fn_numregistro();
        }
        else 
        {
            $this->lc_regs["str"] = 0;
        }

        return $this->lc_regs;
    }
  
}

?>
