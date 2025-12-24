<?php

class PlugThem extends sql {
    
    function __construct() {
        parent::__construct();
    }
    
    public function aplicaPlugThem($param) {
        $lc_sql = "EXECUTE [facturacion].[USP_PlugThem_VOC] '$param[0]', '$param[1]', ''";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "aplicaCadena" => $this->ifNum($row['aplicaCadena'])
                    , "aplicaRestaurante" => $this->ifNum($row['aplicaRestaurante'])
                );
            }
        }
        
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);        
    } 
    
    public function logPlugThem($idRestaurante, $estado, $estadoHTTP, $error, $transaccion, $ipEstacion) {
     
        $lc_sql="insert into Auditoria_Transaccion(rst_id,atran_fechaaudit,atran_modulo,atran_descripcion,atran_accion,atran_varchar1) values(".$idRestaurante.",getdate(),'FACTURACION','".$error."','ERROR','".$transaccion."')";
      
        $result = $this->fn_ejecutarquery($lc_sql);
        if ($result){ return true; }else{ return false; };
    }
    
    public function valorConfiguracionPlugThem($param) {
        $lc_sql = "EXECUTE [facturacion].[USP_PlugThem_VOC] '$param[0]', '$param[1]', ''";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "valor" => $row['valor'] 
                    , "aplica" => $row['aplica']
                    , "contador" => $row['contador']
                );
            }
        }
        
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);        
    }

    public function datosClientePlugThemPost($param) {
         $lc_sql = "select cf.cfac_id as idFactura,cli_nombres+''+cli.cli_apellidos as nombre,cli_documento as cedula,cli_telefono as telefono, cli_email as email  from Cabecera_Factura cf inner join Cliente cli on cf.IDCliente=cli.IDCliente where cfac_id= '$param[0]'";

        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(

                 "idFactura"=>$row['idFactura']
                ,"nombre" => utf8_encode($row['nombre'])
                , "cedula" => $row['cedula']
                , "telefono" => $row['telefono']
                , "email" => utf8_encode($row['email'])

                );
            }

        }

        $this->lc_regs["str"] = $this->fn_numregistro();

        return (json_encode($this->lc_regs));
    }


    public function datosPlugThemPost($param) {
        $lc_sql = "EXECUTE [facturacion].[USP_PlugThem_VOC] '$param[0]', '$param[1]', '$param[2]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "BrandId" => $row['BrandId']
                    , "idCajero" => $row['idCajero']
                    , "EmpId" => $row['EmpId']
                    , "EmpName" => utf8_encode($row['EmpName'])
                    , "SiteId" => $row['SiteId']
                    , "SiteName" => utf8_encode($row['SiteName']) 
                    , "ShiftManagerId" => $row['ShiftManagerId']
                    , "ShiftManagerName" => utf8_encode($row['ShiftManagerName'])                    
                );
            }
        }
        
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);        
    } 
    
    public function tokenLogin($param) {
       $lc_sql = "EXECUTE [facturacion].[USP_PlugThem_VOC] '$param[0]', '$param[1]', '$param[2]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "token_type" => $row['token_type'] 
                    , "access_token" => $row['access_token']
                );
            }
        }
        
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);   
    }
    
    public function guardarCodigoQR($transaccion, $codigoQR) {
        $lc_sql = "EXEC [facturacion].[IAE_PlugThem_VOC] 1, '".$transaccion."', '".$codigoQR."'";
        $result = $this->fn_ejecutarquery($lc_sql);
        if ($result){ return true; }else{ return false; };
    }

    public function parametrosEncuestaProductos ($id_cadena, $transaccion) {
        $lc_sql = "EXECUTE [facturacion].[USP_PlugThem_Productos] $id_cadena, '$transaccion'";
        
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "int_channel" => $row['int_channel'] 
                    , "survey_type" => $row['survey_type']
                    , "product" => $row['product']
                );
            }
        }
         
        return ($this->lc_regs);   
    }
}