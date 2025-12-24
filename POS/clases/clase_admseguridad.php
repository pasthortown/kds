<?php

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de menú /////////////////////////
///////TABLAS INVOLUCRADAS: Menu, /////////////////////////////////////////////
///////FECHA CREACION: 07-06-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 26/07/2016 
///////USUARIO QUE MODIFICO: Daniel Llerena
///////DECRIPCION ULTIMO CAMBIO: Se crea una función para cada consulta 
/////// y ejecución.
///////////////////////////////////////////////////////////////////////////////


class seguridad extends sql{
	//private $lc_regs;
	//constructor de la clase
	function __construct()
        {
            parent ::__construct();
	}
	//funcion que permite armar la sentencia sql de consulta
	       
        function fn_cargarUsuarios($lc_datos)
        {
            $lc_sql = "EXEC usp_seg_cargainformacionseguridades ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", ".$lc_datos[3];
            if($this->fn_ejecutarquery($lc_sql)){
                    while($row = $this->fn_leerarreglo()) {
                            $this->lc_regs[] = array("usr_id"=>$row['usr_id'],
                                                    "rst_id"=>$row['rst_id'],
                                                    "rst_descripcion"=>utf8_encode($row['rst_descripcion']),
                                                    "prf_id"=>$row['prf_id'],
                                                    "prf_descripcion"=>utf8_encode($row['prf_descripcion']),
                                                    "usr_usuario"=>$row['usr_usuario'],
                                                    "usr_iniciales"=>$row['usr_iniciales'],
                                                    "usr_descripcion"=>$row['usr_descripcion'],
                                                    "usr_tarjeta"=>$row['usr_tarjeta'],
                                                    "std_id"=>$row['std_id']);
                    }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        
        function fn_cargarPerfiles($lc_datos)
        {
            $lc_sql = "EXEC usp_seg_cargainformacionseguridades ".$lc_datos[0].", ".$lc_datos[1].", ".$lc_datos[2].", ".$lc_datos[3];
            if($this->fn_ejecutarquery($lc_sql)){
                    while($row = $this->fn_leerarreglo()) {
                            $this->lc_regs[] = array("prf_id"=>$row['prf_id'],
                                                    "prf_descripcion"=>utf8_encode($row['prf_descripcion']),
                                                    "prf_nivel"=>$row['prf_nivel'],
                                                    "std_id"=>$row['std_id']);
                    }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
                
        function fn_actualizarPerfil($lc_datos)
        {
            $lc_sql = "EXEC usp_seg_iae_perfil ".$lc_datos[0].", ".$lc_datos[1].", '".utf8_decode($lc_datos[2])."', ".$lc_datos[3].", ".$lc_datos[4].", ".$lc_datos[5];
            if($this->fn_ejecutarquery($lc_sql)){
                while($row = $this->fn_leerarreglo()){
                    $this->lc_regs[] = array("prf_id"=>$row['prf_id'],
                                            "prf_descripcion"=>utf8_encode($row['prf_descripcion']),
                                            "prf_nivel"=>$row['prf_nivel'],
                                            "std_id"=>$row['std_id']);
                }
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
}
			