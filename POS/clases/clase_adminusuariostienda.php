<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////////////////////////
///////////DESCRIPCION: ADMINISTRACION DE USUARIOS POR TIENDA, CREACION DE PERFILES CAJEROS ////////////
////////////////TABLAS: Users_Pos, Perfil_Pos //////////////////////////////////////////////////////////
////////FECHA CREACION: 27/07/2015//////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

class configuracionUsuariosTienda extends sql{
	//private $lc_regs;
	//constructor de la clase
	function __construct(){
            parent ::__construct();
	}

    function fn_administracionUsuariosTienda($lc_datos)
    {
        $lc_sql = "EXECUTE config.USP_administracionusuariostienda $lc_datos[0], $lc_datos[1], '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', '$lc_datos[5]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("usr_id" => utf8_encode($row['usr_id']), "usr_descripcion" => utf8_encode($row['usr_descripcion']),
                    "usr_nombre_en_pos" => utf8_encode($row['usr_nombre_en_pos']), "usr_usuario" => utf8_encode($row['usr_usuario']),
                    "usr_iniciales" => utf8_encode($row['usr_iniciales']), "prf_descripcion" => utf8_encode($row['prf_descripcion']),
                    "usr_telefono" => utf8_encode($row['usr_telefono']), "usr_email" => utf8_encode($row['usr_email']),
                    "usr_fecha_ingreso" => utf8_encode($row['usr_fecha_ingreso']), "usr_fecha_salida" => utf8_encode($row['usr_fecha_salida']),
                    "std_id" => intval($row['std_id']),
                    "prf_acceso" => utf8_encode($row['prf_acceso']),
                    "prf_id" => utf8_encode($row['prf_id']),
                    "usr_direccion" => utf8_encode($row['usr_direccion']),
                    "usr_tarjeta" => utf8_encode($row['usr_tarjeta']),
                    "usr_cedula" => utf8_encode($row['usr_cedula']),
                    "RestauranteAsignado" => utf8_encode($row['RestauranteAsignado']));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }
        
        function fn_cargarLocales($lc_datos){
            $lc_sql = "EXECUTE config.USP_administracionusuariostienda $lc_datos[0],$lc_datos[1],'$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]'";
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()) {
                    $this->lc_regs[] = array("rst_id"=>$row['rst_id'],
                                             "rst_descripcion"=>utf8_encode($row['rst_descripcion']));
                }
            }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
        
        function fn_cargarPerfiles($lc_datos){
            $lc_sql = "EXECUTE config.USP_administracionusuariostienda $lc_datos[0],$lc_datos[1],'$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]'";
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()) {
                    $this->lc_regs[] = array("prf_id"=>$row['prf_id'],
                                             "prf_descripcion"=>utf8_encode($row['prf_descripcion']));
                }
            }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
        
        function fn_verificarUsuarioSistema($lc_datos){
            $lc_sql = "EXECUTE config.USP_administracionusuariostienda $lc_datos[0],$lc_datos[1],'$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]'";
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()) {
                    $this->lc_regs[] = array("Continuar"=>$row['Continuar']);
                }
            }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
        
        function fn_validarPais($lc_datos){
            $lc_sql = "EXECUTE config.USP_administracionusuariostienda $lc_datos[0],$lc_datos[1],'$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]'";
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()) {
                    $this->lc_regs[] = array("Pais"=>rtrim($row['Pais']));
                }
            }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
        
        function fn_guardarUsuario($lc_datos){
            $lc_sql = "EXECUTE config.IAE_administracionusuariostienda $lc_datos[0],'$lc_datos[1]',$lc_datos[2],'$lc_datos[3]','$lc_datos[4]','$lc_datos[5]','$lc_datos[6]','$lc_datos[7]','$lc_datos[8]','$lc_datos[9]','$lc_datos[10]','$lc_datos[11]','$lc_datos[12]','$lc_datos[13]','$lc_datos[14]','$lc_datos[15]','$lc_datos[16]','$lc_datos[17]',$lc_datos[18],'$lc_datos[19]'";
            //$lc_sql = "EXECUTE config.IAE_administracionusuariostienda $lc_datos[0],'$lc_datos[1]',$lc_datos[2],'$lc_datos[3]','$lc_datos[4]','$lc_datos[5]','$lc_datos[6]','$lc_datos[7]','$lc_datos[8]','$lc_datos[9]','$lc_datos[10]','$lc_datos[11]','$lc_datos[12]','$lc_datos[13]','$lc_datos[14]','$lc_datos[15]','$lc_datos[16]','$lc_datos[17]',$lc_datos[18]";
//            echo $lc_sql;
//            die();
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()) {
                    $this->lc_regs[] = array("Nuevo"=>rtrim($row['Nuevo']),
                                             "existe"=>rtrim($row['existe']));
                }
            }
                //$this->lc_regs['str'] = $this->fn_numregistro();
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
        
        function fn_guardaUserRestaurante($lc_datos){
            $lc_sql = "EXECUTE config.IAE_administracionusuariostienda $lc_datos[0],'$lc_datos[1]',$lc_datos[2],'$lc_datos[3]','$lc_datos[4]','$lc_datos[5]','$lc_datos[6]','$lc_datos[7]','$lc_datos[8]','$lc_datos[9]',$lc_datos[10],$lc_datos[11],'$lc_datos[12]','$lc_datos[13]','$lc_datos[14]','$lc_datos[15]','$lc_datos[16]','$lc_datos[17]',$lc_datos[18],'$lc_datos[19]'";
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()) {
                    $this->lc_regs[] = array("Nuevo"=>rtrim($row['Nuevo']),
                                             "existe"=>rtrim($row['existe']));
                }
            }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
        
        function fn_traerDatosUsuario($lc_datos){
            $lc_sql = "EXECUTE config.USP_administracionusuariostienda $lc_datos[0],$lc_datos[1],'$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]'";
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()) {
                    $this->lc_regs[] = array("usr_id"=>$row['usr_id'], "usr_descripcion"=>utf8_encode($row['usr_descripcion']), 
                                             "usr_nombre_en_pos"=>utf8_encode($row['usr_nombre_en_pos']), "usr_usuario"=>utf8_encode($row['usr_usuario']),
                                             "usr_iniciales"=>utf8_encode($row['usr_iniciales']), "prf_descripcion"=>utf8_encode($row['prf_descripcion']),
                                             "usr_telefono"=>$row['usr_telefono'], "usr_email"=>$row['usr_email'],
                                             "usr_fecha_ingreso"=>$row['usr_fecha_ingreso'], "usr_fecha_salida"=>$row['usr_fecha_salida'],
                                             "std_id"=>$row['std_id'],
                                             "prf_acceso"=>$row['prf_acceso'],
                                             "prf_id"=>$row['prf_id'],
                                             "usr_direccion"=>utf8_encode($row['usr_direccion']),
                                             "usr_tarjeta"=>utf8_encode($row['usr_tarjeta']),
                                             "usr_cedula"=>$row['usr_cedula']);
                }
            }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
        
        function fn_traerRestauranteUser($lc_datos){
            $lc_sql = "EXECUTE config.USP_administracionusuariostienda $lc_datos[0],$lc_datos[1],'$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]'";
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()) {
                    $this->lc_regs[] = array("rst_id"=>$row['rst_id']);
                }
            }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
        
        function fn_restablecerClaveUsuario($lc_datos) {
//            $lc_sql = "EXECUTE config.IAE_usr_actualizaClave '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]'";
            $lc_sql = "EXECUTE config.IAE_usr_actualizaClave '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]'";
//            echo $lc_sql;
//            die();
            if($this->fn_ejecutarquery($lc_sql)){ 
            while($row = $this->fn_leerarreglo()){					
                $this->lc_regs['existe'] = $row["existe"];																	
            }	
                $this->lc_regs['str'] = $this->fn_numregistro();                                    }
                return json_encode($this->lc_regs);
        }
        
        function fn_ValidaDocumento($lc_datos) {
            $lc_sql = "EXEC [config].[USP_ValidaCedulaUsuario] ".$lc_datos[0].", ".$lc_datos[1].", '".$lc_datos[2]."', '".$lc_datos[3]."'";
            
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()) {
                    if ($row['continuar']==1) {                    
                        $this->lc_regs[] = array("continuar"=>$row['continuar'],
                                                "usuario"=>$row['usuario'],
                                                "perfil"=>$row['perfil'],
                                                "tienda"=>$row['tienda']);                  
                    }
                    else {                 
                        $this->lc_regs[] = array("continuar"=>$row['continuar']);                    
                    }
                }
            }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
        
        function fn_ValidaUsuario($lc_datos){
            $lc_sql = "EXEC [config].[USP_ValidaCedulaUsuario] ".$lc_datos[0].", ".$lc_datos[1].", '".$lc_datos[2]."', '".$lc_datos[3]."'";
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()) {
                    $this->lc_regs[] = array("continuar"=>$row['continuar']);
                }
            }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
}
