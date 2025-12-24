<?php

/* 
 * Daniel Llerena
 * 31/07/2018
 * Colleción de datos usuarios
 */


class ColeccionUsuarios extends sql {
    
    public function __construct() {
        parent::__construct();
    }
    
    function detalleColeccionUsuarios($accion, $idCadena, $idColeccionUsuario, $idUsuario) {
        $lc_sql = "EXEC [config].[USP_ColeccionDeDatosUsuario] ".$accion.", ".$idCadena.", '".$idColeccionUsuario."', '".$idUsuario."' ";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "ID_ColeccionUsuarios" => $row['ID_ColeccionUsuarios'],
                    "Descripcion" => utf8_encode($row['Descripcion'])
                );
            }
        }
        $this->lc_regs["str"] = $this->fn_numregistro();
        return json_encode($this->lc_regs);  
    }
    
    function detalleColeccionDeDatosUsuarios($accion, $idCadena, $idColeccionUsuario, $idUsuario) {
        $lc_sql = "EXEC [config].[USP_ColeccionDeDatosUsuario] ".$accion.", ".$idCadena.", '".$idColeccionUsuario."', '".$idUsuario."' ";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "ID_ColeccionUsuarios" => $row['ID_ColeccionUsuarios']
                    , "ID_ColeccionDeDatosUsuarios" => $row['ID_ColeccionDeDatosUsuarios'] 
                    , "Descripcion" => utf8_encode($row['Descripcion'])
                    , "especificarValor" => $row['especificarValor'] 
                    , "obligatorio" => $row['obligatorio'] 
                    , "tipodedato" => utf8_encode($row['tipodedato'])
                );
            }
        }
        $this->lc_regs["str"] = $this->fn_numregistro();
        return json_encode($this->lc_regs);  
    }
    
    function guardarUsuarioColeccionDeDatos($datos) {
        $lc_sql = "EXEC [config].[IAE_ColeccionDeDatosUsuario] ".$datos[0].", '".$datos[1]."', '".$datos[2]."', '".$datos[3]."', '".$datos[4]."', ".$datos[5].", '".$datos[6]."', ".$datos[7].", ".$datos[8].", '".$datos[9]."', '".$datos[10]."', ".$datos[11].", ".$datos[12].", ".$datos[13].", '".$datos[14]."'  ";
        if ($this->fn_ejecutarquery($lc_sql)) {
            $this->lc_regs["str"] = 1;
        } else {
            if (($errors = sqlsrv_errors()) != null) {
                foreach ($errors as $error) {
                    $this->lc_regs["str"] = utf8_encode($error["message"]);
                }
            }
        }
        return json_encode($this->lc_regs); 
    }
    
    function detalleUsuarioColeccionDeDatos($accion, $idCadena, $idColeccionUsuario, $idUsuario) {
        $lc_sql = "EXEC [config].[USP_ColeccionDeDatosUsuario] ".$accion.", ".$idCadena.", '".$idColeccionUsuario."', '".$idUsuario."' ";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "ID_ColeccionUsuarios" => $row['ID_ColeccionUsuarios']
                    , "coleccionDescripcion" => utf8_encode($row['coleccionDescripcion'])
                    , "ID_ColeccionDeDatosUsuarios" => $row['ID_ColeccionDeDatosUsuarios'] 
                    , "parametroDescripcion" => utf8_encode($row['parametroDescripcion'])
                    , "especificarValor" => $row['especificarValor'] 
                    , "obligatorio" => $row['obligatorio'] 
                    , "tipodedato" => utf8_encode($row['tipodedato'])
                    , "caracter" => utf8_encode($row['caracter'])
                    , "entero" => $row['entero']
                    , "fecha" => $row['fecha']
                    , "seleccion" => $row['seleccion']
                    , "numerico" => $row['numerico']
                    , "fechaIni" => $row['fechaIni']
                    , "fechaFin" => $row['fechaFin']
                    , "min" => $row['min']
                    , "max" => $row['max']
                    , "isActive" => $row['isActive']
                );
            }
        }
        $this->lc_regs["str"] = $this->fn_numregistro();
        return json_encode($this->lc_regs);  
    }
}
