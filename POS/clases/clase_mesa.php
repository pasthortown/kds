<?php

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Clase para Carga de Combos///////////////
/////////////////////En las Mesas/////////////////////////////
///////FECHA CREACION: 27-01-2015/////////////////////////////
////////FECHA ULTIMA MODIFICACION: 22-12-2016 ////////////////
///////USUARIO QUE MODIFICO: Juan Estévez ////////////////////
///////DECRIPCION ULTIMO CAMBIO: Se agrego panel mesa ////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////

class mesa extends sql {

    //constructor de la clase
    function __construct() {
        //con herencia 
        parent::__construct();
    }

/////////////////////////////////////////CARGAR COMBOS////////////////////////////////////////

    function fn_consultar($lc_opcion, $lc_datos) {
        switch ($lc_opcion) {
            case 'cargarRestaurante':            //Combo Restaurante
                $lc_query = "EXECUTE [config].[USP_administracionmesas] $lc_datos[0], $lc_datos[1], $lc_datos[2], '" . $lc_datos[3] . "', '" . $lc_datos[4] . "'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "rst_id" => $row['rst_id'],
                            "rst_cod_tienda" => $row['rst_cod_tienda'],
                            "rst_descripcion" => utf8_encode($row['rst_descripcion'])
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'VerificarMisMesa':
                $lc_query = "select * from [config].[EstadoMesa] ('$lc_datos[0]', $lc_datos[1])";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "mensaje" => $row['mensaje'],
                            "ruta" => $row['ruta'],
                            "ruta_E" => $row['ruta_E']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'cargarPiso':             //Combo Piso
                $lc_query = "EXECUTE [config].[USP_administracionmesas] $lc_datos[0], $lc_datos[1], $lc_datos[2], '" . $lc_datos[3] . "', '" . $lc_datos[4] . "'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "pis_id" => $row['pis_id'],
                            "pis_numero" => $row['pis_numero']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'cargarArea':
                $lc_query = "EXECUTE [config].[USP_administracionmesas] $lc_datos[0], $lc_datos[1], $lc_datos[2], '" . $lc_datos[3] . "', '" . $lc_datos[4] . "'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "arp_id" => $row['arp_id'],
                            "arp_descripcion" => $row['arp_descripcion']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'cargarMesa':             //Para Mesas
                $lc_query = "EXECUTE [config].[USP_cnf_mesas] " . $lc_datos[0] . ", " . $lc_datos[1] . ", '" . $lc_datos[2] . "', '" . $lc_datos[3] . "' , '" . $lc_datos[4] . "', '" . $lc_datos[5] . "'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "mesa_id" => $row['mesa_id'],
                            "mesa_descripcion" => $row['mesa_descripcion'],
                            "tmes_descripcion" => $row['tmes_descripcion'],
                            "tmes_id" => $row['tmes_id'],
                            "std_id" => $row['std_id'],
                            "mesa_coordenadax" =>(float) $row['mesa_coordenadax'],
                            "mesa_coordenaday" =>(float) $row['mesa_coordenaday'],
                            "mesa_dimension" => $row['mesa_dimension'],
                            "tmes_ruta_imagen" => $row['tmes_ruta_imagen']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'cargaMesaModificar':
                $lc_query = "EXECUTE [config].[USP_cnf_mesas] 0, 0, '', '' , '" . $lc_datos[1] . "', '" . $lc_datos[0] . "'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['mesa_id'] = $row["mesa_id"];
                        $this->lc_regs['mesa_descripcion'] = utf8_encode(trim($row["mesa_descripcion"]));
                        $this->lc_regs['tmes_descripcion'] = utf8_encode(trim($row["tmes_descripcion"]));
                        $this->lc_regs['tmes_id'] = $row["tmes_id"];
                        $this->lc_regs['std_id'] = $row["std_id"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'cargarPanelMesa':
                $lc_query = "EXECUTE [config].[usp_cnf_mesas] '" . $lc_datos[0] . "','" . $lc_datos[1] . "','" . $lc_datos[2] . "','" . $lc_datos[3] . "','" . $lc_datos[4] . "','" . $lc_datos[5] . "'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "cantidad" => $row['cantidad'],
                            "std_descripcion" => $row['std_descripcion'],
                            "tmes_ruta_imagen" => $row['tmes_ruta_imagen']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case 'accionMenu':
                $lc_query = "EXECUTE [config].[IAE_administracionmesas]  " . $lc_datos[0] . ", '" . $lc_datos[1] . "', '" . utf8_decode(trim($lc_datos[2])) . "', '" . $lc_datos[3] . "', '" . $lc_datos[4] . "', '" . $lc_datos[5] . "', " . $lc_datos[6] . ", '" . $lc_datos[7] . "', " . $lc_datos[8] . ", '" . $lc_datos[9] . "'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "mesa_id" => $row['mesa_id'],
                            "mesa_descripcion" => $row['mesa_descripcion'],
                            "tmes_descripcion" => $row['tmes_descripcion'],
                            "tmes_id" => $row['tmes_id'],
                            "std_id" => $row['std_id'],
                            "mesa_coordenadax" =>(float) $row['mesa_coordenadax'],
                            "mesa_coordenaday" =>(float) $row['mesa_coordenaday']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'cargarCantidad':
                $lc_query = "EXECUTE [config].[USP_administracionmesas] $lc_datos[0], $lc_datos[1], $lc_datos[2], '" . $lc_datos[3] . "', '" . $lc_datos[4] . "'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "tmes_id" => $row['tmes_id'],
                            "tmes_descripcion" => $row['tmes_descripcion'],
                            "tmes_ruta_imagen" => $row['tmes_ruta_imagen']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'consultarListaMesas':
                $lc_query = "EXECUTE [config].[USP_cnf_mesas] " . $lc_datos[0] . ", " . $lc_datos[1] . ", '" . $lc_datos[2] . "', '" . $lc_datos[3] . "', '" . $lc_datos[4] . "', '" . $lc_datos[5] . "', '" . utf8_decode($lc_datos[6]) . "', " . $lc_datos[7] . ", " . $lc_datos[8] . ", " . $lc_datos[8];
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "mesa_id" => $row['mesa_id'],
                            "mesa_descripcion" => utf8_encode(trim($row['mesa_descripcion'])),
                            "tmes_descripcion" => utf8_encode(trim($row['tmes_descripcion'])),
                            "tmes_id" => $row['tmes_id'],
                            "std_id" => $row['std_id'],
                            "num_reg" => $row['num_reg']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'cargarTipoMesa':// nuevo//
                $lc_query = "EXECUTE [config].[USP_cargarTipoMesa] " . $lc_datos[0] . " , " . $lc_datos[1];
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "ID_ColeccionDeDatosRestaurante" => $row['ID_ColeccionDeDatosRestaurante'],
                            "Descripcion" => utf8_encode(trim($row['Descripcion'])),
                            "isActive" => utf8_encode(trim($row['isActive']))
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;
        }
    }

/////////////////////////////////////////////////FUNCION EJECUTAR//////////////////////////////////////////////////////////////////////////
    public function fn_ejecutar($lc_opcion, $lc_datos) {
        switch ($lc_opcion) {
            case 'guardarMesa':
                $lc_query = "EXECUTE [config].[IAE_mesas_coordenas] '" . $lc_datos[0] . "','" . $lc_datos[1] . "','" . $lc_datos[2] . "','" . $lc_datos[3] . "','" . $lc_datos[4] . "'";
                return json_encode($this->fn_ejecutarquery($lc_query));
        }
    }

}