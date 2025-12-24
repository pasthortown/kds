<?php

///////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Daniel Llerena////////////////////////////////////
///////DESCRIPCION	   : Archivo de configuracion del Modulo Retiro Fondo /
////////FECHA CREACION : 10/11/2015 ///////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////MODIFICADO POR  :  /////////////////////////////////////////////////
///////DESCRIPCION	   :  /////////////////////////////////////////////////
///////TABLAS		   : //////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

class retiroFondo extends sql {

    public function fn_consultar($lc_opcion, $lc_datos) {
        switch ($lc_opcion) {
            case 'CargaDetallesFondoAsignado':
                $lc_sql = "EXECUTE [seguridad].[USP_Retiro_FondoAsignado] $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("fondoasignado" => htmlentities($row['fondoasignado']),
                            "supervisorasignar" => htmlentities($row['supervisorasignar']),
                            "cajeroasignado" => htmlentities($row['cajeroasignado']),
                            "fechaconfirmacion" => htmlentities($row['fechaconfirmacion']),
                            "supervisorretiro" => htmlentities($row['supervisorretiro']),
                            "fondoretirado" => $row['fondoretirado'],
                            "fecharetiro" => htmlentities($row['fecharetiro']),
                            "periodo" => htmlentities($row['periodo']));
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);
                break;

            case 'validarUsuarioAdministrador':
                $lc_sql = "EXECUTE seguridad.USP_validaUsuarioAdmin $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]','$lc_datos[3]','$lc_datos[4]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['admini'] = $this->ifNum($row["admini"]);
                        $this->lc_regs['moneda'] = $row["moneda"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);
                break;

            case 'ConsultaFondoAsignado':
                $lc_sql = "EXECUTE [seguridad].[USP_Retiro_FondoAsignado] $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['fondo'] = $this->ifNum($row["fondo"]);
                        $this->lc_regs['cajero'] = htmlspecialchars($row["cajero"]);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);
                break;

            case 'ValidaFondoRetirado':
                $lc_sql = "EXECUTE [seguridad].[USP_Retiro_FondoAsignado] $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['valida'] = $this->ifNum($row["valida"]);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);
                break;

            case 'RetirarFondoAsignado':
                $lc_sql = "EXECUTE [seguridad].[USP_Retiro_FondoAsignado] $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case 'impresion_retiroFondo':
                $lc_sql = "EXECUTE [seguridad].[USP_impresiondinamica_RetiroFondo] '$lc_datos[0]'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };
                break;

            case "obtenerMesa":
                $lc_sql = "EXEC pedido.ORD_asignar_mesaordenpedido ".$lc_datos[0].", '".$lc_datos[1]."', '".$lc_datos[2]."'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['respuesta'] = $row['respuesta'];
                        $this->lc_regs['IDFactura'] = $row['IDFactura'];
                        $this->lc_regs['IDOrdenPedido'] = $row['IDOrdenPedido'];
                        $this->lc_regs['IDMesa'] = $row['IDMesa'];
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;
        }
    }

}

	
