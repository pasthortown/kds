<?php

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Ximena Celi/////////////////////////
////////DESCRIPCION: Clase que permite verificar /////////////
/////////////////// los usuarios existentes y los    /////////
/////////////////// posibles casos que necesite este /////////
///////TABLAS INVOLUCRADAS: Perfil_pos, Users_pos ////////////
///////////////////        AuditLog,AuditTrans ///////////////
///////////////////     la base de datos en SQLServer2005/////  
///////FECHA CREACION: 27-04-2009/////////////////////////////
///////FECHA MODIFICACION: 04-06-2012 ////////////////////////
///////USUARIO QUE MODIFICO: Andres Guerron  /////////////////
//////////////////////////////////////////////////////////////
///////FECHA MODIFICACION: 12-12-2013 ////////////////////////
///////USUARIO QUE MODIFICO: Worman Andrade  /////////////////
///////DECRIPCION ULTIMO CAMBIO: ReadecuaciÃ³n de clase para//
/////////////////////////////////validaciÃ³n de usuario///////
/////////////////////////////////y cadena/////////////////////
//////////////////////////////////////////////////////////////
///////FECHA MODIFICACION: 15-07-2014 ////////////////////////
///////USUARIO QUE MODIFICO: Jorge Tinoco ////////////////////
///////DECRIPCION ULTIMO CAMBIO: Consulta a la base sobre ////
///////permisos de perfil de los usuario /////////////////////
////////////////////////////////////////////////////////////// 
///////FECHA MODIFICACION: 02-04-2015 ////////////////////////
///////USUARIO QUE MODIFICO: Jimmy Cazaro ////////////////////
///////DECRIPCION ULTIMO CAMBIO: Anexo en la función /////////
///////fn_getUsr la estación /////////////////////////////////
////////////////////////////////////////////////////////////// 
//-- =================================================================
//--FECHA ULTIMA MODIFICACION	: 11:53 10/1/2017
//--USUARIO QUE MODIFICO	: Mychael Castro
//--DECRIPCION ULTIMO CAMBIO	:  Agregar campo en consulta SQl dentro
//-- del caso de 'cadena_x_restaurante'  para mostrar el nombre de la cadena
//-- =================================================================
//-- =================================================================
//--FECHA ULTIMA MODIFICACION	: 16:52 11/4/2017
//--USUARIO QUE MODIFICO	: Mychael Castro
//--DECRIPCION ULTIMO CAMBIO	:  Agregar campo en consulta SQl dentro
//-- del caso de 'cadena_x_restaurante'  para mostrar el nombre de la cadena
//-- =================================================================
class seguridades extends sql {

    function __construct() {
        parent::__construct();
    }

/////////////////////////////Obtener informacion del usuario///////////////////////////////////////////////////////////////////

    function fn_consultar($lc_opcion, $lc_datos) {
        switch ($lc_opcion) {
            case "cargarPermisos":
                $lc_sql = "EXEC [config].[USP_cargarPermisos] '$lc_datos[0]', '$lc_datos[1]' ";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "id_acceso" => $row['IDAccesoPos'], 
                            "descripcion" => $row['acc_descripcion']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "cargarUsuario":
                $lc_sql = "EXEC [config].[USP_Cargar_usuario] '$lc_datos[0]' ";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("perfil" => $row['perfil']
                            , "usuario" => $row['usuario']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
    }

    function fn_getUsr($lc_clave, $lc_campo) {
        switch ($lc_campo) {
            case 'Usuario_Id':
                $lc_campo = 'usrId';
                break;
            case 'Usuario':
                $lc_campo = 'usrUsuario';
                break;
            case 'Usuario_Nombre':
                $lc_campo = 'usrNombre';
                break;
            case 'Usuario_Estado':
                $lc_campo = 'usrEstado';
                break;
            case 'Perfil_Id':
                $lc_campo = 'prfId';
                break;
            case 'Perfil_Descripcion':
                $lc_campo = 'prfDescripcion';
                break;
            case 'Resturante_Id':
                $lc_campo = 'rstId';
                break;
            case 'Resturante_CodTienda':
                $lc_campo = 'rstCodigo';
                break;
            case 'Resturante_Nombre':
                $lc_campo = 'rstNombre';
                break;
            case 'TipoServicio':
                $lc_campo = 'rst_tipo_servicio';
                break;
            default:
                $lc_campo = '*';
                break;
        }

        $lc_tabla = "V_acceso";

        $lc_query = "declare @parametro varchar(50)
				SET @parametro = '$lc_clave'
				SELECT " . $lc_campo . " FROM " . $lc_tabla . "
                                WHERE ((PWDCOMPARE(@parametro,usrClave)=1) OR (PWDCOMPARE(@parametro,usr_tarjeta)=1)) AND (usrEstado in (52,13))";

        if ($lc_datos = $this->fn_ejecutarquery($lc_query)) {
            $lc_numreg = $this->fn_numregistro();
            while ($lc_row = $this->fn_leerobjeto()) {
                if ($lc_numreg > 0) {//fn_numregistro
                    if ($lc_campo != '*') {
                        return $lc_row->$lc_campo;
                    } else {
                        echo 'Debe digitar el campo a obtener la informacion';
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }
    }

    function fn_getUsrAdmin($lc_usuario, $lc_clave, $lc_campo) {
        switch ($lc_campo) {
            case 'Usuario_Id':
                $lc_campo = 'usrId';
                break;
            case 'Usuario':
                $lc_campo = 'usrUsuario';
                break;
            case 'Usuario_Nombre':
                $lc_campo = 'usrNombre';
                break;
            case 'Usuario_Estado':
                $lc_campo = 'usrEstado';
                break;
            case 'Perfil_Id':
                $lc_campo = 'prfId';
                break;
            case 'Perfil_Descripcion':
                $lc_campo = 'prfDescripcion';
                break;
            case 'Resturante_Id':
                $lc_campo = 'rstId';
                break;
            case 'Resturante_CodTienda':
                $lc_campo = 'rstCodigo';
                break;
            case 'Resturante_Nombre':
                $lc_campo = 'rstNombre';
                break;
            default:
                $lc_campo = '*';
                break;
        }

        $lc_tabla = "V_acceso";

        $lc_query = "SELECT " . $lc_campo . " FROM " . $lc_tabla . " WHERE (usrUsuario='" . $lc_usuario . "')  AND (PWDCOMPARE('" . $lc_clave . "',usrClave)=1) AND (usrEstado in ((select config.fn_estado('Seguridades','Admin')),(select config.fn_estado('Seguridades','Activo')), (SELECT config.fn_estado('Seguridades','Restablecer'))))";


        if ($lc_datos = $this->fn_ejecutarquery($lc_query)) {
            $lc_numreg = $this->fn_numregistro();
            while ($lc_row = $this->fn_leerobjeto()) {
                if ($lc_numreg > 0) {//fn_numregistro
                    if ($lc_campo != '*') {
                        return $lc_row->$lc_campo;
                    } else {
                        echo 'Debe digitar el campo a obtener la informacion';
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }
    }

    /////////////////////////////////////////////////////Obtener informacion de la CADENA//////////////////////////////////////////////////
    function fn_getCdn($lc_rstId, $lc_campo) {
        switch ($lc_campo) {
            case 'Resturante_NumPiso':
                $lc_campo = 'numPiso';
                break;
            case 'Resturante_NumMesa':
                $lc_campo = 'numMesa';
                break;
            case 'Cadena_Id':
                $lc_campo = 'cdnId';
                break;
            case 'Cadena_Nombre':
                $lc_campo = 'cndNombre';
                break;
            case 'Logotipo':
                $lc_campo = 'logo';
                break;
            default:
                $lc_campo = '*';
                break;
        }

        $lc_tabla = "V_rstCdn";
        $lc_query = 'SELECT ' . $lc_campo . ' FROM ' . $lc_tabla . ' WHERE (rstId = ' . $lc_rstId . ')';
        if ($lc_datos = $this->fn_ejecutarquery($lc_query)) {
            $lc_numreg = $this->fn_numregistro();
            while ($lc_row = $this->fn_leerobjeto()) {
                if ($lc_numreg > 0) {//fn_numregistro
                    if ($lc_campo != '*') {
                        if (($lc_campo == "numPiso") && ($lc_row->$lc_campo == ""))
                            $lc_row->$lc_campo = 1;

                        if (($lc_campo == "numMesa") && ($lc_row->$lc_campo == ""))
                            $lc_row->$lc_campo = 20;

                        if (($lc_campo == "logo") && ($lc_row->$lc_campo == ""))
                            $lc_row->$lc_campo = 'sinLogo.jpg';
                        return $lc_row->$lc_campo;
                    }
                    else {
                        echo 'Debe digitar el campo a obtener la informacion';
                        return false;
                    }
                } else {
                    return false;
                }
            } fn_liberarecurso();
        }
    }

    //////////////////////////////////////////////CONTROL DE ESTACION///////////////////////////////////////////////////////////////
    function fn_controlEstacion($lc_usrId) {
        $lc_tabla = "V_validaSesion";
        $lc_campo = '*';
        $lc_query = "SELECT " . $lc_campo . " FROM " . $lc_tabla . " WHERE ((usrId = " . $lc_usrId . ") AND (estadoDescripcion= 'Activo'))";
        $lc_datos = $this->fn_ejecutarquery($lc_query);
        $lc_numreg = $this->fn_numregistro();
        if ($lc_numreg > 0) {
            return 'Activo';
        } else {
            $this->fn_insertarSesion($lc_rst, $lc_std, $lc_usrId);
            return 'Inactivo';
        }
    }

    /////////////////////////////////////////Insertar los datos de sesion////////////////////////////////////////////////////////////
    function fn_insertarSesion($lc_rst, $lc_std, $lc_usr) {
        $lc_campos = "rst_id, std_id, usr_id";
        //echo "SQL LOG".
        $lc_query = "INSERT INTO Control_Estacion(" . $lc_campos . ") VALUES (" . $lc_rst . ", " . $lc_std . ", " . $lc_usr . ")";
        $lc_insertar = $this->fn_ejecutarquery($lc_query);
        if (!$lc_insertar) {
            return true;
        } else {
            return false;
        }
    }

    //////////////////////////////////Selecciona Informacion de Control Estacion //////////////////////////////////////////////////////
    function fn_SeleccionaEstacion($lc_ip, $lc_columna) {
        switch ($lc_columna) {
            case 'Estacion_Nombre':
                $lc_columna = "COALESCE( b.est_nombre, '') AS Estacion";
                break;
            case 'Estacion_Codigo':
                $lc_columna = "COALESCE( b.est_id, '') AS Estacion_Cod";
                break;
            default:
                $lc_columna = '*';
                break;
        }
        $lc_query = "DECLARE @ip varchar(20)
				SET @ip = '" . $lc_ip . "'
				--EXEC ind_verifica_usuario_logueado @ip
				declare @activo int, @modulo int
				set @modulo=(select mdl_id from Modulo where mdl_descripcion LIKE 'Sesiones')
				set @activo=(SELECT std_id from Status where mdl_id=@modulo AND std_descripcion like 'Activo')				
				SELECT " . $lc_columna . "
				--COALESCE( a.usr_usuario, '') AS usr_usuario, COALESCE( b.est_nombre, '') AS Estacion, COALESCE( b.est_id, '') AS Estacion_Cod
				FROM
				(								
					SELECT u.usr_usuario, @ip AS usr_ip
					FROM Control_Estacion AS c
					INNER JOIN Users_Pos AS u ON c.usr_id = u.usr_id
					--INNER JOIN Estacion AS e ON c.est_id = e.est_id AND c.rst_id = e.rst_id AND e.est_ip = @ip
					WHERE c.est_id = (SELECT est_id FROM Estacion WHERE est_ip = @ip) AND c.std_id = @activo
					GROUP BY u.usr_usuario	
				) AS a																			
				LEFT JOIN
				(
					SELECT est_ip, est_nombre, est_id
					FROM Estacion 
					WHERE est_ip = @ip
					GROUP BY est_ip, est_nombre, est_id
				) AS b ON a.usr_ip = b.est_ip				
				";
        if ($lc_datos = $this->fn_ejecutarquery($lc_query)) {
            $lc_numreg = $this->fn_numregistro();
            while ($lc_row = $this->fn_leerobjeto()) {
                if ($lc_numreg > 0) {
                    if ($lc_columna != '*') {
                        if ($lc_columna == "COALESCE( b.est_nombre, '') AS Estacion") {
                            return $lc_row->Estacion;
                        } else if ($lc_columna == "COALESCE( b.est_id, '') AS Estacion_Cod") {
                            return $lc_row->Estacion_Cod;
                        }
                    } else {
                        echo 'Debe digitar el campo a obtener la informacion estacion';
                        return false;
                    }
                } else {
                    return false;
                }
            }
            //fn_liberarecurso();
        }
    }

    ///////////////////////////////PANTALLAS POR PERFIL (BOTONES PARA ACCESO)//////////////////////////////////////////////////////////
    function fn_permisoPantalla($lc_perfil) {
        $lc_path = '../imagenes/menuAdmin/';
        $failPage = '../mantenimiento/index.php';
        $lc_query = 'SELECT A.pnt_id, A.pnt_Nombre_Mostrar, A.pnt_Nombre_Formulario, A.pnt_Ruta, A.pnt_Imagen, B.acc_id 
					FROM Pantalla_Pos A, Permisos_Perfil_Pos B 
					WHERE ((A.pnt_id=B.pnt_id) AND(B.prf_id=' . $lc_perfil . '))';
        $lc_datos = $this->fn_ejecutarquery($lc_query);
        $lc_numreg = $this->fn_numregistro();
        if ($lc_numreg > 0) {
            echo '<table align="center" border="0" width="950px"><tr  height="30px">';
            echo '<td align="center" width="100px">
								<a href="inicio.php" target="frmContenido" ><img src="../imagenes/menuAdmin/inicio.png" width="50" height="50" alt="Inicio" /><br/>Inicio</a></td>';
            while ($row = $this->fn_leerarreglo()) {
                echo '<td align="center" width="100px">
								<a href="' . trim($row["pnt_Ruta"]) . '/' . trim($row["pnt_Nombre_Formulario"]) . '?inicio=1"
									title="' . trim($row["pnt_Nombre_Mostrar"]) . '" 
									target="frmContenido" ><img src="' . $lc_path . trim($row["pnt_Imagen"]) . '" width="50" height="50" /><br/>' . trim($row["pnt_Nombre_Mostrar"]) .
                '</a></td>';
            }
            echo '<td align="center" width="100px">
								<a href="index.php?cerrarSesion=1" target="_top" ><img src="../imagenes/menuAdmin/exit.png" width="50" height="50" alt="Salir" /><br/>Salir</a></td>';
            echo '</tr></table>';

            $this->fn_liberarecurso();
        } else {
            ?>
            <table align="center" border="0" width="900px"><tr  height="30px">
                    <td align="center" width="150px">
                        <a href="../mantenimiento/index.php"
                           title="Acceso al modulo Admitrativo" 
                           target="_top" ><img src="../imagenes/menuAdmin/login.png" width="50px" height="50px" /><br/>Ingresar al m&oacute;dulo</a></td>
                </tr>
            </table>

            <?php

            die;
        }
    }

    /////////////////////////////////////////////CERRAR SESION DE USUARIO//////////////////////////////////////
    function fn_salir() {
        session_start();
        unset($_SESSION['validado']);
        unset($_SESSION["usuarioId"]);
        unset($_SESSION['nombre']);
        unset($_SESSION["usuarioId"]);
        unset($_SESSION['perfil']);
        unset($_SESSION["direccionIp"]);
        session_destroy();
        header("Location: ../mantenimiento/index.php");
        exit;
    }

    ///////////////////////////////////////////////////MENU//////////////////////////////////////////////////////
    function fn_menu($lc_perfil) {
        $lc_sql = "EXEC [seguridad].[menu_administracion] '$lc_perfil'";
        $result = $this->fn_ejecutarquery($lc_sql);
        if ($result){ return true; }else{ return false; };
    }

    //===========================================================================================================
    //===========================================================================================================
    //funcion para devolver solo usuarios que estan registrados y autorizados
    function fn_validarAdm($lc_usuario, $lc_clave) {
        $lc_campos = "Cod_Usuario,Usuario";
        $lc_tabla = "Users";
        /* $lc_query = "select $lc_campos from $lc_tabla where Usuario='$lc_usuario' and Clave='$lc_clave' and    Estado=1"; */
        $lc_query = "select Cod_Usuario,Usuario from users where Usuario='$lc_usuario' and Estado=1 and PWDCOMPARE('$lc_clave',clave) = 1
	or Usuario='$lc_usuario' and Estado=3 and PWDCOMPARE('$lc_clave',clave) = 1
	";
        if ($lc_datos = $this->fn_ejecutarquery($lc_query)) {
            $lc_numreg = $this->fn_numregistro();
            while ($lc_row = $this->fn_leerobjeto()) {
                if ($lc_numreg > 0) {//fn_numregistro
                    return $lc_row->Cod_Usuario;
                } else {
                    return false;
                }
            }
        }
    }

    //funcion para devolver el perfil del usuario encontrado
    function fn_buscarperfil($lc_idusu) {
        $lc_query = "select a.Usuario,b.Nombre,b.Cod_Perfil from Users a, Perfil b where a.Cod_Perfil=b.Cod_Perfil and Cod_Usuario=$lc_idusu";
        //con herencia
        if ($lc_datos = $this->fn_ejecutarquery($lc_query)) {
            $lc_numreg = $this->fn_numregistro();
            while ($lc_row = $this->fn_leerobjeto()) {
                if ($lc_numreg > 0) {
                    return $lc_row->Nombre;
                } else {
                    return false;
                }
            }
        }
    }

    //Devuelve el nombre del usuario que ingreso una vez registrado su usuario y password
    function fn_nombreusu($lc_usuario, $lc_clave) {
        $lc_idusu = $this->fn_validarusu($lc_usuario, $lc_clave);
        $lc_perfil = $this->fn_buscarperfil($lc_idusu);

        $lc_query = "select Descripcion from Users a, Perfil b 
                    where a.Usuario='$lc_usuario' and PWDCOMPARE('$lc_clave',a.Clave) = 1
                    and a.Estado=1 and a.Cod_Perfil=b.Cod_Perfil and b.Nombre='$lc_perfil'
                    or
                    a.Usuario='$lc_usuario' and PWDCOMPARE('$lc_clave',a.Clave) = 1
                    and a.Estado=3 and a.Cod_Perfil=b.Cod_Perfil and b.Nombre='$lc_perfil'
                    ";
        if ($lc_datos = $this->fn_ejecutarquery($lc_query)) {
            $lc_numreg = $this->fn_numregistro();
            while ($lc_row = $this->fn_leerobjeto()) {
                if ($lc_numreg > 0) {
                    return $lc_row->Descripcion;
                } else {
                    return false;
                }
            }
        }
    }

    //funcion para retornar el porcentaje del iva
    function fn_ivapais() {
        $lc_query = "Select Porcentaje from Impuestos where Descripcion='IVA'";
        //con herencia
        if ($lc_datos = $this->fn_ejecutarquery($lc_query)) {
            $lc_numreg = $this->fn_numregistro();
            while ($lc_row = $this->fn_leerobjeto()) {
                if ($lc_numreg > 0) {
                    return $lc_row->Porcentaje;
                } else {
                    return false;
                }
            }
        }
    }

    //Insertar los datos en la tabla users
    function fn_insertarusu($lc_campos, $lc_clave, $lc_datos) {
        $lc_query = "declare @var varbinary(256) set @var=pwdencrypt('$lc_clave'); insert into Users($lc_campos,Clave) values ($lc_datos,@var)";

        $lc_insertar = $this->fn_ejecutarquery($lc_query);

        if ($lc_insertar) {
            return true;
        } else {
            return false;
        }
    }

    //Actualizar los datos en la tabla users
    function fn_actualizarclaveusu($lc_clave, $lc_iduser) {
        $lc_query = "declare @var varbinary(256)
					set @var=pwdencrypt('$lc_clave') 
					update users set Clave = @var where cod_usuario=$lc_iduser";
        $lc_actualizar = $this->fn_ejecutarquery($lc_query);
        if ($lc_actualizar) {
            return true;
        } else {
            return false;
        }
    }

    //Insertar los datos para los logs  AuditLog
    function fn_insertarlog($lc_datos) {
        $lc_campos = "Cod_Restaurante,Cod_Usuario,Accion,Modificador";
        //echo "SQL LOG".
        $lc_query = "insert into AuditLog(Cod_Log,$lc_campos,Fecha_Log) values (newid(),$lc_datos,getdate())";
        $lc_insertar = $this->fn_ejecutarquery($lc_query);
        if (!$lc_insertar) {
            return true;
        } else {
            return false;
        }
    }

    //Insertar los datos para los logs  AuditTrans
    function fn_insertaraudit($lc_datos) {
        $lc_campos = "Cod_Restaurante,Cod_Usuario,Modulo,Descripcion,Accion";
        //echo "SQL AUDIT".
        $lc_query = "insert into AuditTrans(Cod_Audit,$lc_campos,Fecha_Audit) values (newid(),$lc_datos,getdate())";
        $lc_insertar = $this->fn_ejecutarquery($lc_query);
        if (!$lc_insertar) {
            return true;
        } else {
            return false;
        }
    }

    //// Botones 
    function fn_botones($idusuario, $pantalla, $acceso) {
        $lc_query = "select Acceso_Pos.acc_descripcion 
                    from Permisos_Perfil_Pos inner join Users_Pos on Permisos_Perfil_Pos.prf_id=Users_Pos.prf_id 
					  inner join Acceso_Pos on Permisos_Perfil_Pos.acc_id=acceso_pos.acc_id
					  inner join Pantalla_Pos on Permisos_Perfil_Pos.pnt_id=Pantalla_Pos.pnt_id
					  where Users_Pos.usr_id=" . $idusuario . " and Pantalla_Pos.pnt_Nombre_Formulario ='" . $pantalla . "' and Acceso_Pos.acc_Nombre like '%" . $acceso . "%'
		 /*select Acceso.Descripcion from PermisosPerfil inner join users on PermisosPerfil.Cod_Perfil=users.Cod_perfil 
					  inner join acceso on PermisosPerfil.Cod_acceso=acceso.Cod_Acceso 
					  inner join pantalla on PermisosPerfil.Cod_Pantalla=Pantalla.Cod_Pantalla 
					  where Users.Cod_Usuario=\"$idusuario\" and Pantalla.Nombre_Formulario = \"$pantalla\" and Acceso.Nombre like \"%$acceso%\"*/";
        if ($lc_datos = $this->fn_ejecutarquery($lc_query)) {
            $lc_res = $this->fn_numregistro();
            if ($lc_res > 0) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    function fn_aperturainv($lc_restaurante, $lc_codtoma, $lc_idusuario) {
        $lc_query = "select Cod_Perfil from Users where Cod_Usuario=$lc_idusuario and Estado=1 and Cod_perfil=61";
        $lc_datos = $this->fn_ejecutarquery($lc_query);
        if ($this->fn_numregistro() > 0) {
            $lc_query = "select t.Cod_Restaurante from Apertura_Inventario ai
						inner join TomaFisica t on t.Cod_Toma_Fisica=ai.Cod_Toma 
						where t.Cod_Restaurante=$lc_restaurante and ai.Cod_toma='$lc_codtoma' 
						and ai.Contador>0 and DATEDIFF(DAY,t.Fecha,getdate())<6";
            if ($lc_datos = $this->fn_ejecutarquery($lc_query)) {
                $lc_res = $this->fn_numregistro();
                if ($lc_res > 0) {
                    return 1;
                } else {
                    if ($lc_codtoma == '') {
                        return 1;
                    } else {
                        return 0;
                    }
                }
            }
        } else {
            return 1;
        }
    }

    //// Nombre Restaurante
    function fn_nombrelocal($lc_restaurante) {
        $lc_query = "select r.rst_descripcion as Descripcion, r.rst_cod_tienda as Cod_Tienda from Restaurante r where  r.rst_id=$lc_restaurante";
        if ($lc_datos = $this->fn_ejecutarquery($lc_query)) {
            $lc_row = $this->fn_leerobjeto();
            $lc_numreg = $this->fn_numregistro();
            if ($lc_numreg > 0) {
                return $lc_row->Cod_Tienda . " _ " . $lc_row->Descripcion;
            }
        }
    }

    function fn_nombrelocalporcadena($lc_codcad, $lc_usr) {
        $lc_query = "select distinct top 1 r.rst_descripcion as Descripcion, r.rst_cod_tienda as Cod_Tienda  from Restaurante r inner join Users_Restaurante_Pos urp  on r.rst_id=urp.rst_id where  r.cdn_id=$lc_codcad and urp.IDUsersPos='$lc_usr'";
        if ($lc_datos = $this->fn_ejecutarquery($lc_query)) {
            $lc_row = $this->fn_leerobjeto();
            $lc_numreg = $this->fn_numregistro();
            if ($lc_numreg > 0) {
                return $lc_row->Cod_Tienda . " _ " . $lc_row->Descripcion;
            }
        }
    }

    //// Nombre Cadena //sabiendo su codigo
    function fn_nombrecadena($lc_codcad) {
        $lc_query = "select Descripcion from cadena where Cod_Cadena=$lc_codcad";
        if ($lc_datos = $this->fn_ejecutarquery($lc_query)) {
            $lc_numreg = $this->fn_numregistro();
            if ($lc_numreg > 0) {
                while ($lc_row = $this->fn_leerobjeto()) {
                    return $lc_row->Descripcion;
                }
            }
        }
    }

    /// LOGO
    function fn_logo($lc_restaurante) {
        $lc_query = "select cad.cdn_logotipo as Logo from Restaurante res,Cadena cad where res.cdn_id=cad.cdn_id and  rst_id=$lc_restaurante";
        if ($lc_datos = $this->fn_ejecutarquery($lc_query)) {
            $lc_row = $this->fn_leerobjeto();
            $lc_res = $this->fn_numregistro();
            if ($lc_res > 0) {
                return $lc_row->Logo;
            } else {
                return 0;
            }
        }
    }

    /// LOGO POR CADENA//
    function fn_logoCadena($lc_cadena) {
        $lc_query = "SELECT cdn_logotipo as Logo from Cadena  where  cdn_id = $lc_cadena";
        if ($lc_datos = $this->fn_ejecutarquery($lc_query)) {
            $lc_row = $this->fn_leerobjeto();
            $lc_res = $this->fn_numregistro();
            if ($lc_res > 0) {
                return $lc_row->Logo;
            } else {
                return 0;
            }
        }
    }

    /// CADENA
    function fn_cadena($lc_restaurante) {
        $lc_query = "select cad.descripcion as Cadena from Restaurante res,Cadena cad where res.Cod_Cadena=cad.Cod_Cadena and  Cod_Restaurante=$lc_restaurante";
        if ($lc_datos = $this->fn_ejecutarquery($lc_query)) {
            $lc_row = $this->fn_leerobjeto();
            $lc_res = $this->fn_numregistro();
            if ($lc_res > 0) {
                return $lc_row->Cadena;
            } else {
                return 0;
            }
        }
    }

    ///////////////////////////
    ///Si se ingreso el Inventario Final en ese mes ///
    ////////////
    function fn_periodoInv($cod_Bodega, $fecha) {
        $lc_query = "set dateformat dmy select  Cod_Toma_Fisica from dbo.TomaFisica tom
    inner join dbo.Bodegas bod on bod.Cod_bodega=tom.Cod_bodega and bod.Tipo='Principal'
    where (month(tom.fecha)=month('$fecha') and year(tom.fecha)=year('$fecha')) 
     and tom.Tipo_Toma='Mensual' and tom.Estado=3 and tom.cod_restaurante=$cod_Bodega";

        if ($lc_datos = $this->fn_ejecutarquery($lc_query)) {
            $lc_res = $this->fn_numregistro();
            if ($lc_res > 0) {
                return 1;
            } else {
                return 0;
            }
        }
        return $lc_query;
    }

    /// Pantalla
    function fn_pantalla($pantalla, $usuario) {
        $lc_query = "select count(*) as Registros
	from users
	inner join Perfil 
	on users.cod_perfil=perfil.cod_perfil
	inner join permisosperfil 
	on permisosperfil.cod_perfil=perfil.Cod_Perfil
	inner join pantalla on permisosperfil.cod_pantalla=pantalla.cod_pantalla
	where users.cod_usuario='$usuario'
	and pantalla.descripcion='$pantalla'";
        if ($lc_datos = $this->fn_ejecutarquery($lc_query)) {
            $lc_row = $this->fn_leerobjeto();
            $lc_res = $this->fn_numregistro();
            if ($lc_res > 0) {
                return $lc_row->Registros;
            } else {
                return 0;
            }
        }
    }

    /// DECIMALES ACTIVOS FIJOS
    function fn_decimales_activos() {
        return 3;
    }

    function fn_decimales_cajas() {
        return 3;
    }

    /// Correo
    function Env_CorreoGmail($correo, $mensaje, $titulo) {
        $acorreo = explode(',', $correo);
        include_once("../system/email/config_email.php");
        $this->email = new PHPMailer();
        //habilitamos smtp y las seguridades respectivas
        $this->email->IsSMTP();
        $this->email->SMTPAuth = true;
        $this->email->SMTPSecure = 'tls';
        $this->email->SMTPKeepAlive = true;
        $this->email->Mailer = 'smtp';
        $this->email->Host = "smtp.gmail.com";
        $this->email->Port = 465;
        //el usuario y correo el cual va a enviar el mensaje
        $this->email->Username = "sgerente@kfc.com.ec";
        $this->email->Password = "gerente*88";
        $this->email->From = "sgerente@kfc.com.ec";
        $this->email->FromName = "Sistema Gerente";
        //titulo y cuerpo del mensaje
        $this->email->Subject = $titulo;
        $this->email->MsgHTML($mensaje . '<br><br> Por favor no responder este correo<br><br>');
        //destinatario
        foreach ($acorreo as $destinatario) {
            $this->email->AddAddress($destinatario);
        }
        $this->email->Send();
    }

    ///Impuestos///
    function fn_impuesto($tipo) {
        $lc_query = "select Porcentaje from Impuestos where Descripcion='$tipo' and Estado=1";
        if ($lc_datos = $this->fn_ejecutarquery($lc_query)) {
            $lc_row = $this->fn_leerobjeto();
            $lc_res = $this->fn_numregistro();
            if ($lc_res > 0) {
                return $lc_row->Porcentaje;
            } else {
                return 0;
            }
        }
    }

    /// PLANTAS
    function fn_Planta($lc_restaurante) {
        $lc_query = "select planta from restaurante where cod_restaurante =$lc_restaurante";
        if ($lc_datos = $this->fn_ejecutarquery($lc_query)) {
            $lc_row = $this->fn_leerobjeto();
            $lc_res = $this->fn_numregistro();
            if ($lc_res > 0) {
                return $lc_row->planta;
            } else {
                return 0;
            }
        }
    }

    //Insertar los datos para los logs  AuditTrans
    function fn_insertarauditcup($lc_datos) {
        $lc_campos = "Cod_Restaurante,Cod_Usuario,Modulo,Descripcion,Accion";
        //echo "SQL AUDIT".
        $lc_query = "insert into AuditCupon(Cod_Audit,$lc_campos,Fecha_Audit) values (newid(),$lc_datos,getdate())";
        $lc_insertar = $this->fn_ejecutarquery($lc_query);
        if (!$lc_insertar) {
            return true;
        } else {
            return false;
        }
    }

    //Querys para el manejo Periodos DM
    public function fn_armarquery($lc_opcion, $lc_condiciones) {
        switch ($lc_opcion) {
            case 'periodo':
                $lc_query = "select Cod_Config_Periodo,Descripcion,Orden from Config_Periodo order by Orden";
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'usuario_x_restaurante':
                $lc_query = "select distinct ur.IDUsersPos as usr_id, c.cdn_id,c.cdn_descripcion,cdn_logotipo
									from Users_Pos u, Users_Restaurante_Pos ur, Restaurante r, Cadena c
									where u.IDUsersPos = ur.IDUsersPos and ur.rst_id=r.rst_id and c.cdn_id=r.cdn_id and  			           						  
									u.IDUsersPos='$lc_condiciones[0]'  and r.IDStatus =(select config.fn_estado('Restaurante','Activo'))
									order by c.cdn_descripcion";

            $result = $this->fn_ejecutarquery($lc_query);
            if ($result){ return true; }else{ return false; };

            case 'cadena_x_restaurante':
                $lc_query = "select cdn_id,cdn_logotipo , cdn_descripcion from Cadena where cdn_id=" . $lc_condiciones[0];
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'ruta_x_periodo':
                $lc_query = "select Ruta, Descripcion from Config_Periodo where Cod_Config_Periodo=" . $lc_condiciones[0];
                $result = $this->fn_ejecutarquery($lc_query);
                if ($result){ return true; }else{ return false; };

            case 'path_actual':
                $lc_query = "select SUBSTRING(ruta,1,len(Ruta)-4)+'_seleccion.php' Ruta from Config_Periodo where Orden = 1";
                $this->fn_ejecutarquery($lc_query);
                while ($lc_row = $this->fn_leerobjeto()) {
                    return $lc_row->Ruta;
                }
                break;
        }
    }

    ///Recupera el codigo de la cadena
    function fn_cadenacodigo($lc_restaurante) {
        $lc_query = "select cad.cod_cadena as Cadena from Restaurante res,Cadena cad where res.Cod_Cadena=cad.Cod_Cadena and  Cod_Restaurante=$lc_restaurante";
        if ($lc_datos = $this->fn_ejecutarquery($lc_query)) {
            $lc_row = $this->fn_leerobjeto();
            $lc_res = $this->fn_numregistro();
            if ($lc_res > 0) {
                return $lc_row->Cadena;
            } else {
                return 0;
            }
        }
    }

    /* ----------------------------------------------------------------------------------------------------
      Verificar los permisos del perfil asignados a un usuario
      Función de llamada: fn_accesoPermisosPerfil()
      Cambio: se hizo una correccion en la declaración de botones que tenia tipo texto y era un array 08/07/2022
      ----------------------------------------------------------------------------------------------------- */

    function fn_accesoPermisosPerfilBotones($usr_id, $pnt_nombre_mostrar) {
        $botones = [];
        $lc_sql = "SELECT ap.IDAccesoPos, ap.acc_descripcion
                FROM Acceso_Pos AS ap
                INNER JOIN Permisos_Perfil_Pos AS ppp ON ap.IDAccesoPos = ppp.IDAccesoPos
                INNER JOIN Pantalla_Pos AS pp ON ppp.IDPantallaPos = pp.IDPantallaPos
                INNER JOIN Status AS e ON pp.IDStatus = e.IDStatus
                INNER JOIN Modulo AS m ON e.mdl_id = m.mdl_id
                INNER JOIN Users_Pos AS up ON ppp.IDPerfilPos = up.IDPerfilPos	
                WHERE pp.pnt_Nombre_Mostrar LIKE '%" . $pnt_nombre_mostrar . "%' AND up.IDUsersPos = '$usr_id'	
                GROUP BY ap.IDAccesoPos, ap.acc_descripcion";
        if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
            for ($i = 0; $lc_row = $this->fn_leerarreglo(); $i++) {
                $botones[$i] = utf8_decode($lc_row['acc_descripcion']);
            }
        } else {
            return NULL;
        }
        return $botones;
    }
    function fn_politicaEliminarTodo($IDCadena, $IDRestaurante, $DescripcionParametro)
    {
        $lc_sql = "exec [pedido].[USP_ColeccionRestauranteOrdenPedidoBorrarTodos]  '$IDCadena', '$IDRestaurante', '$DescripcionParametro'";
        $this->fn_ejecutarquery($lc_sql);
        $row = $this->fn_leerarreglo();
        return $row['isActive'];
    }
    function fn_obtenerNombreUsuario($usr_id) {
        $botones = "";
        $lc_sql = "select usr_descripcion From Users_Pos where IDUsersPos = '" . $usr_id . "'";
        if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
            $lc_row = $this->fn_leerarreglo();
            $descripcion = $lc_row['usr_descripcion'];
        } else {
            return NULL;
        }
        return $descripcion;
    }

    // appedir
    

    function fn_politicaAppedir($idRestaurante, $idCadena){
        $lc_sql = "SELECT * FROM [config].[fn_ColeccionFidelizacion_Appedir] ('$idRestaurante', '$idCadena')";
        if($this->fn_ejecutarquery($lc_sql)){
            $row = $this->fn_leerarreglo();
            $data = (object)[
                "active" => ($row['Aplica'] == 'S') ? true : false,
                "limiteCaracter" => $row['LimiteCaracter']
            ];
    
            return $data;
        }

        return (object)[
            "active" => "N",
            "limiteCaracter" => 0
        ];
    }

    // politicas masivo

    function fn_politicaApiKeyMaisivo($idCadena){
        
        $lc_sql = "SELECT [config].[fn_ColeccionCadena_VariableV] ($idCadena, 'WS CONFIGURACIONES', 'MASIVO API') AS api";
        $this->fn_ejecutarquery($lc_sql);
        $row = $this->fn_leerarreglo();
        if(isset($row['api']) || !empty($row['api'])){
            $data = (object)[
                "api" => $row['api']
            ];
            return $data;
        }

        return false;

    }

    // politica url autorizacion

    function fn_politicUrlAutorizacion($idCadena){
        $lc_sql = "SELECT [config].[fn_ColeccionCadena_VariableV] ($idCadena, 'WS RUTA SERVICIO', 'MASIVO OBTENER BARER') AS barer";
        $this->fn_ejecutarquery($lc_sql);
        $row = $this->fn_leerarreglo();

        if(isset($row['barer']) || !empty($row['barer'])){
            $data = (object)[
                "url" => $row['barer']
            ];
            return $data;
        }
        return false;

    }

    // autorization api masivo

    function getAutorizacionApiMasivo(){

        $sql = "SELECT * FROM cadena";

        $cadena=0;

        if($this->fn_ejecutarquery($sql)){
            while ($row = $this->fn_leerarreglo()) {
                $cadena = $row['cdn_id'] * 1;
            }
        }

        if($cadena > 0){
            $data = $this->fn_politicUrlAutorizacion($cadena);
            $data2 = $this->fn_politicaApiKeyMaisivo($cadena);

            if($data && $data2){ 
                $array_data=json_decode($data2->api,true);
                if (is_array($array_data)) {
                    $array_return=[];
                    foreach ($array_data as $key => $value) {
						$curl = curl_init();
                        
                        curl_setopt_array($curl, [
                            CURLOPT_URL => $data->url,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_SSL_VERIFYHOST => false,
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "GET",
                            CURLOPT_HTTPHEADER => [
                                "x-api-key: ".$value['api-key']
                            ],
                        ]);
                
                        $response = json_decode(curl_exec($curl));
                        $err = curl_error($curl);
                
                        curl_close($curl);
                
                        if (!$err) {
                            if(isset($response->data)){
                                $array_return[$key]=array(
                                    "token"=>$response->data,
                                    "marca"=>$value['marca'],
                                    "activo"=>$value['activo']
                                );
                            }
                        }
                    }
                    return json_encode($array_return);
                }else {
                    return 'Error al decodificar el JSON.';
                }
            }

            /*$curl = curl_init();
            if($data && $data2){    
                curl_setopt_array($curl, [
                    CURLOPT_URL => $data->url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => [
                        "x-api-key: $data2->api"
                    ],
                ]);
        
                $response = json_decode(curl_exec($curl));
                $err = curl_error($curl);
        
                curl_close($curl);
        
                if (!$err) {
                    if(isset($response->data)){
                        return $response->data;
                    }
                    return 'Problemas con autorización masivo';
                }
        
                return $err;
            }*/
        }

        return 'La cadena no existe';
    }
}

///////////////////////////////////////////////////////GUARDAR IMAGEN///////////////////////////////////////////////////////////
function fn_guardarImg($lc_nomImg, $lc_area) {
    $lc_query = "UPDATE AreaPiso SET arp_imagen= '" . $lc_nomImg . "' WHERE arp_id=" . $lc_area;
    $lc_datos = $this->fn_ejecutarquery($lc_query);
}

///////////////////////////////////////////////////////GUARDAR IMAGEN///////////////////////////////////////////////////////////
function fn_obtenerImg($lc_area) {
    $lc_query = "SELECT arp_imagen FROM AreaPiso WHERE arp_id=" . $lc_area;
    $lc_datos = $this->fn_ejecutarquery($lc_query);
    while ($row = $this->fn_leerarreglo()) {
        return $row['arp_imagen'];
    }
}

class seguridadesUsuarioPerfilPeriodo extends sql {
    //private $lc_regs;
    function __construct() {
        parent ::__construct();
    }

    function fn_consultar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {
            case 'ordenPedidoPuntos':
                $lc_sql = "exec [fidelizacion].[ORD_ordenPedidoPuntos] '$lc_datos[0]'";
                if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("grupoAmigos" => $row['grupoAmigos'],
                            "estado" => $row['estado'],
                            "documento" => $row['documento']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'cargaTeclasEmail':
                $lc_sql = "exec [facturacion].[USP_cargaTeclasEmail] $lc_datos[0]";
                if ($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("descripcionEmail" => $row['descripcionEmail']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case "CRUD_Cliente":
                $lc_sql = " exec [config].[USP_consultaIngresaCliente]  '$lc_datos[0]' ,'', '$lc_datos[2]' ,'$lc_datos[3]','$lc_datos[1]', '$lc_datos[5]','$lc_datos[4]','$lc_datos[6]','$lc_datos[7]',1, '$lc_datos[8]', '$lc_datos[9]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {

                        $_SESSION['fdznDocumento'] = $row['documento'];
                        $_SESSION['fdznNombres'] = $row['descripcion'];
                        $_SESSION['fdznDireccion'] = $row['direccion'];

                        $this->lc_regs[] = array(
                            "IDCliente" => $row['IDCliente'],
                            "documento" => $row['documento'],
                            "descripcion" => utf8_encode($row['descripcion']),
                            "direccion" => utf8_encode($row['direccion']),
                            "telefono" => $row['telefono'],
                            "email" => utf8_encode($row['email']),
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "ConusltarExistente":
                $lc_sql = "EXEC [facturacion].[CLIENTE_USP_BusquedaCliente] '$lc_datos[0]' ,'$lc_datos[1]', '$lc_datos[2]', null, 1 ;";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $_SESSION['fdznDocumento'] = $row['documento'];
                        $_SESSION['fdznNombres'] = $row['descripcion'];
                        $_SESSION['fdznDireccion'] = $row['direccion'];

                        $this->lc_regs[] = array(
                            "IDCliente" => $row['IDCliente'],
                            "documento" => $row['documento'],
                            "descripcion" => utf8_encode($row['descripcion']),
                            "direccion" => utf8_encode($row['direccion']),
                            "telefono" => $row['telefono'],
                            "estado" => $row['estado'],
                            "email" => utf8_encode($row['email']),
                            "jsonDatosAdicionales" => utf8_encode($row['jsonDatosAdicionales']),
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "FidelizacionActiva":
                $lc_sql = "EXECUTE config.USP_FIDELIZACION_ConsultaEstado   '$lc_datos[0]';";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {

                        $_SESSION['fidelizacionActiva'] = $row['estado'];
                        $this->lc_regs[] = array("estado" => $row['estado']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "obtieneMesaPredeterminada":
                $lc_sql = "EXECUTE config.USP_MESA_area   2, '$lc_datos[0]' ,'$lc_datos[1]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("IDMesa" => $row['IDMesa'],
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
            case "kioskoActivo":
                $lc_sql = "EXECUTE config.USP_kioskoActivo '$lc_datos[0]', '$lc_datos[1]';";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("activo" => $row['activo']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "consultarClavePerfil":
                $lc_sql = "EXECUTE config.USP_consultarClavePerfil  '$lc_datos[0]' ,'$lc_datos[1]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "Perfil" => $row['Perfil'],
                            "nivel" => $row['nivel']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "consultarEstacionTomaPedido":
                $lc_sql = "EXECUTE config.USP_consultarEstacionTomaPedido  '$lc_datos[0]' ";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "tomaPedido" => $row['tomaPedido'],
                            "perfilesAutorizados" => $row['perfilesAutorizados']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "validaUsuarioPerfil":
                $lc_sql = "EXECUTE seguridad.USP_validausuarioclaveperfil $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                                "existeusuario" =>(int) $row['existeusuario'],
                                "cedulaCajero" => $row['cedulaCajero'],
                                "rst_id" =>(int) $row['rst_id'],
                                "fecha" => $row['fecha']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "traerUsuario":
                $lc_sql = "EXECUTE seguridad.USP_validausuarioclaveperfil $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['usr_id'] = $row["usr_id"];
                        $this->lc_regs['usr_usuario'] = $row["usr_usuario"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case "validaAccesoPerfil":
                $lc_sql = "EXECUTE seguridad.USP_validausuarioclaveperfil $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['accesoperfil'] =(int) $row["accesoperfil"];
                        $this->lc_regs['cadena'] = $this->ifNum($row["cadena"]);
                        $this->lc_regs['restaurante'] = $this->ifNum($row["restaurante"]);
                        $this->lc_regs['bd_destino'] = $this->ifNum($row["bd_destino"]);
                        $this->lc_regs['transferencia'] = $this->ifNum($row["transferencia"]);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case "validaestacionenuso":
                $lc_sql = "EXECUTE seguridad.USP_validaestacionenuso $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['estacionenuso'] = (int) $row["estacionenuso"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case "grabacontrolestacion":
                $lc_sql = "EXECUTE seguridad.IAE_validaestacionenuso $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', $lc_datos[3], '$lc_datos[4]'";
                try {
                    $this->fn_ejecutarquery($lc_sql);
                    try {
                        var_dump($this->fn_leerarreglo());
                        while ($row = $this->fn_leerarreglo()) {
                            $this->lc_regs['accion'] = $row["accion"];
                        }
                        $this->lc_regs['str'] = $this->fn_numregistro();
                        return json_encode($this->lc_regs);
                    }catch (Exception $e) {
                        return true;
                    }
                }catch (Exception $e) {
                    return false;
                }

            case "tiposervicio":
                $lc_sql = "EXECUTE seguridad.USP_validaipperfilperiodo $lc_datos[0], '$lc_datos[1]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['rst_tipo_servicio'] = (int) $row["rst_tipo_servicio"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case "validaEstacionEnUsoUsuario":
                $lc_sql = "EXECUTE seguridad.USP_validaestacionenuso $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['estacionenusousuario'] = (int) $row["estacionenusousuario"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case "validaUsuarioLogueadoEstacion":
                $lc_sql = "EXECUTE seguridad.USP_validaestacionenuso $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['estacionusuariologueado'] = (int) $row["estacionusuariologueado"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case "verificaCajaAsignada":
                $lc_sql = "EXECUTE seguridad.USP_validaestacionenuso $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['cajaasignada'] = $this->ifNum($row["cajaasignada"]);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case "verificaUsuarioCorrecto":
                $lc_sql = "EXECUTE seguridad.USP_validaestacionenuso $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['mismousuario'] = $this->ifNum($row["mismousuario"]);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case "validafondo":
                $lc_sql = "EXECUTE [seguridad].[USP_Confirmar_Fondo] $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['fondo'] = (int) $row["fondo"];
                        $this->lc_regs['cantidad'] = (int) $row["cantidad"];
                        $this->lc_regs['simbolo'] = $row["simbolo"];
                        $this->lc_regs['tiene_venta'] = $row["tiene_venta"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case 'confirmarfondo':
                $lc_sql = "EXEC [seguridad].[IAE_Confirmacion_Fondo] $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]'";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result){ return true; }else{ return false; };

            case "validaclaveadmin":
                $lc_sql = "EXECUTE [seguridad].[USP_Verifica_Clave_Admin] $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['perfil'] = (int) $row["perfil"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case "obtenerMesa":
                $lc_sql = "EXEC pedido.ORD_asignar_mesaordenpedido " . $lc_datos[0] . ", '" . $_SESSION["estacionId"] . "', '" . $_SESSION["usuarioId"] . "'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['respuesta'] = $row['respuesta'];
                        $this->lc_regs['IDFactura'] = $row['IDFactura'];
                        $this->lc_regs['IDOrdenPedido'] = $row['IDOrdenPedido'];
                        $this->lc_regs['IDMesa'] = $row['IDMesa'];
                        $this->lc_regs['numeroCuenta'] = $row['numeroCuenta'];
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

                 case "validaAperturaPeriodo":
                $lc_sql = "EXEC seguridad.USP_validaAperturaPeriodo " . $lc_datos[0] . ", '" . $lc_datos[1] . "', '" . $lc_datos[2] . "'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['respuesta'] = (int) $row['respuesta'];
                        $this->lc_regs['mensajeHoraAtencion'] = $row['mensajeHoraAtencion'];
                        $this->lc_regs['horas'] = (int) $row['horas'];
                        $this->lc_regs['horaInicio'] = $row['horaInicio'];
                        $this->lc_regs['fechaAperturaPeriodo'] = $row['fechaAperturaPeriodo'];
                        $this->lc_regs['cierrePeriodo'] = $row['cierrePeriodo'];
                        $this->lc_regs['mensajeCierrePeriodo'] = $row['mensajeCierrePeriodo']; 
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }


    }

    function fn_getDatosUser($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {
            case 'variablesSesion':
                $lc_sql = "EXECUTE seguridad.USP_variablessesion $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', $lc_datos[4]";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $_SESSION['cargoPisoArea'] = "No";
                        $_SESSION['validado'] = TRUE;
                        $_SESSION['usuarioId'] = $this->lc_regs['usr_id'] = $row["usr_id"];
                        $_SESSION['usuario'] = $this->lc_regs['usr_descripcion'] = utf8_encode($row["usr_descripcion"]);
                        $_SESSION['usuarioIdAdmin'] = $this->lc_regs['usr_id_admin'] = $row["usr_id_admin"];
                        $_SESSION['nombre'] = $this->lc_regs['usr_usuario'] = $row["usr_usuario"];
                        $_SESSION['perfil'] = $this->lc_regs['prf_id'] = $row["prf_id"];
                        $this->lc_regs['prf_descripcion'] = utf8_encode($row["prf_descripcion"]);
                        $_SESSION['rstId'] = $this->lc_regs['rst_id'] =(int) $row["rst_id"];
                        $_SESSION['rstCodigoTienda'] = $this->lc_regs['rst_cod_tienda'] = $row["rst_cod_tienda"];
                        $_SESSION['rstNombre'] = $this->lc_regs['rst_descripcion'] = utf8_encode($row["rst_descripcion"]);
                        $_SESSION['TipoServicio'] = $this->lc_regs['rst_tipo_servicio'] = (int) $row["rst_tipo_servicio"];
                        $_SESSION['EstacionNombre'] = $this->lc_regs['est_nombre'] = $row["est_nombre"];
                        $_SESSION['estacionId'] = $this->lc_regs['est_id'] = $row["est_id"];
                        $_SESSION['direccionIp'] = $this->lc_regs['est_ip'] = $row["est_ip"];
                        $_SESSION['numPiso'] = $this->lc_regs['rst_numpiso'] = (int) $row["rst_numpiso"];
                        $_SESSION['numMesa'] = $this->lc_regs['rst_num_mesas'] = (int) $row["rst_num_mesas"];
                        $_SESSION['cadenaId'] = $this->lc_regs['cdn_id'] = (int) $row["cdn_id"];
                        $_SESSION['cadenaNombre'] = $this->lc_regs['cdn_descripcion'] = utf8_encode($row["cdn_descripcion"]);
                        $_SESSION['logo'] = $this->lc_regs['cdn_logotipo'] = $row["cdn_logotipo"];
                        $_SESSION['simboloMoneda'] = $this->lc_regs['pais_moneda_simbolo'] = $row["pais_moneda_simbolo"];
                        $_SESSION['bloqueoacceso'] = $this->lc_regs['bloqueoacceso'] =(int) $row["bloqueoacceso"];
                        $_SESSION['sesionbandera'] = $this->lc_regs['sesion_bandera'] = $row["sesion_bandera"];
                        $_SESSION['tiempoEsperaTarjetas'] = $this->lc_regs['tiempoespera'] = $row["tiempoespera"];
                        $_SESSION['IDPeriodo'] = $this->lc_regs['IDPeriodo'] = $row["IDPeriodo"];
                        $_SESSION['fecha_prd'] = $this->lc_regs['fecha_prd'] = $row["fecha_prd"];
                        $_SESSION['IDControlEstacion'] = $this->lc_regs['IDControlEstacion'] = $row["IDControlEstacion"];
                        $_SESSION['paisIsoAlfa2'] = $this->lc_regs['pais_iso_alfa2'] = $row["pais_iso_alfa2"];
                        $_SESSION['ValidacionRucCodigo'] = $this->lc_regs['ValidacionRucCodigo'] = trim($row['ValidacionRucCodigo']);
                        $_SESSION['ValidacionRucCodigoMensaje'] = $this->lc_regs['ValidacionRucCodigoMensaje'] = utf8_encode(trim($row['ValidacionRucCodigoMensaje']));
                        $_SESSION['ValidacionErrorRUC'] = $this->lc_regs['ValidacionErrorRUC'] = utf8_encode(trim($row['ValidacionErrorRUC']));
                        $_SESSION['HabilitarValidacionRUC'] = $this->lc_regs['HabilitarValidacionRUC'] = utf8_encode(trim($row['HabilitarValidacionRUC']));
                        $_SESSION['ValidacionRUCintento'] = $this->lc_regs['ValidacionRUCintento'] = utf8_encode(trim($row['ValidacionRUCintento']));
                        $_SESSION['ValidacionRUCdirectoN'] = $this->lc_regs['ValidacionRUCdirectoN'] = utf8_encode(trim($row['ValidacionRUCdirectoN']));
                        $_SESSION['ValidacionRUCdirecto'] = $this->lc_regs['ValidacionRUCdirecto'] = utf8_encode(trim($row['ValidacionRUCdirecto']));
                        $_SESSION['servicioApiImpresion'] = $this->lc_regs['servicioApiImpresion'] = (trim($row['servicio_impresion']));
                        $_SESSION['unificacion_transferencia_de_venta'] = $this->lc_regs['unificacion_transferencia_de_venta'] = utf8_encode(trim($row['unificacion_transferencia_de_venta']));
                        $_SESSION['ValidacionAnulacionFacturaTiempoApp'] = $this->lc_regs['ValidacionAnulacionFacturaTiempoApp'] = isset($row['ValidacionAnulacionFacturaTiempoApp'])? trim($row['ValidacionAnulacionFacturaTiempoApp']) : 0;
                        $_SESSION['ValidacionAnulacionFacturaTiempoFast'] = $this->lc_regs['ValidacionAnulacionFacturaTiempoFast'] = isset($row['ValidacionAnulacionFacturaTiempoFast'])? trim($row['ValidacionAnulacionFacturaTiempoFast']) : 0;
                        $_SESSION['canje_v2'] = $this->lc_regs['canje_v2'] = utf8_encode(trim($row['canje_v2']));
                        $_SESSION['cambio_caje_v1_v2'] = $this->lc_regs['cambio_caje_v1_v2'] = utf8_encode(trim($row['cambio_caje_v1_v2']));
                        $_SESSION['aplicaDesmontadoCajeroV2'] = $this->lc_regs['aplicaDesmontadoCajeroV2'] = utf8_encode(trim($row['aplicaDesmontadoCajeroV2']));
                        $this->lc_regs['userAdminPos'] = utf8_encode(trim($row['userAdminPos']));
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    
                    return json_encode($this->lc_regs);
                }
                break;

            case "inicioVariablesSesionDesmontarCajero":
                $lc_sql = "EXECUTE seguridad.USP_variablessesion $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]'";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $_SESSION['cargoPisoArea'] = "No";
                        $_SESSION['validado'] = TRUE;
                        $_SESSION['usuarioIdAdmin'] = $this->lc_regs['usr_id_admin'] = $row["usr_id_admin"];
                        $_SESSION['usuarioId'] = $this->lc_regs['usr_id_cajero'] = $row["usr_id_cajero"];
                        $_SESSION['usuario'] = $this->lc_regs['usr_descripcion'] = utf8_encode($row["usr_descripcion"]);
                        $_SESSION['nombre'] = $this->lc_regs['usr_usuario'] = $row["usr_usuario"];
                        $_SESSION['perfil'] = $this->lc_regs['prf_id'] = $row["prf_id"];
                        $_SESSION['rstId'] = $this->lc_regs['rst_id'] = $this->ifNum($row["rst_id"]);
                        $_SESSION['rstCodigoTienda'] = $this->lc_regs['rst_cod_tienda'] = $row["rst_cod_tienda"];
                        $_SESSION['rstNombre'] = $this->lc_regs['rst_descripcion'] = utf8_encode($row["rst_descripcion"]);
                        $_SESSION['TipoServicio'] = $this->lc_regs['rst_tipo_servicio'] = $this->ifNum($row["rst_tipo_servicio"]);
                        $_SESSION['EstacionNombre'] = $this->lc_regs['est_nombre'] = $row["est_nombre"];
                        $_SESSION['estacionId'] = $this->lc_regs['est_id'] = $row["est_id"];
                        $_SESSION['direccionIp'] = $this->lc_regs['est_ip'] = $row["est_ip"];
                        $_SESSION['numPiso'] = $this->lc_regs['rst_numpiso'] = $this->ifNum($row["rst_numpiso"]);
                        $_SESSION['numMesa'] = $this->lc_regs['rst_num_mesas'] = $this->ifNum($row["rst_num_mesas"]);
                        $_SESSION['cadenaId'] = $this->lc_regs['cdn_id'] = $this->ifNum($row["cdn_id"]);
                        $_SESSION['cadenaNombre'] = $this->lc_regs['cdn_descripcion'] = utf8_encode($row["cdn_descripcion"]);
                        $_SESSION['logo'] = $this->lc_regs['cdn_logotipo'] = $row["cdn_logotipo"];
                        $_SESSION['simboloMoneda'] = $this->lc_regs['pais_moneda_simbolo'] = $row["pais_moneda_simbolo"];
                        $_SESSION['bloqueoacceso'] = $this->lc_regs['bloqueoacceso'] = $this->ifNum($row["bloqueoacceso"]);
                        $_SESSION['sesionbandera'] = $this->lc_regs['sesion_bandera'] = $row["sesion_bandera"];
                        $_SESSION['IDPeriodo'] = $this->lc_regs['IDPeriodo'] = $row["IDPeriodo"];
                        $_SESSION['fecha_prd'] = $this->lc_regs['fecha_prd'] = $row["fecha_prd"];
                        $_SESSION['IDControlEstacion'] = $this->lc_regs['IDControlEstacion'] = $row["IDControlEstacion"];
                        $_SESSION['tiempoEsperaTarjetas'] = $this->lc_regs['tiempoespera'] = $row["tiempoespera"];
                        $_SESSION['paisIsoAlfa2'] = $this->lc_regs['pais_iso_alfa2'] = $row["pais_iso_alfa2"];
                        $_SESSION['ValidacionRucCodigo'] = $this->lc_regs['ValidacionRucCodigo'] = trim($row['ValidacionRucCodigo']);
                        $_SESSION['ValidacionRucCodigoMensaje'] = $this->lc_regs['ValidacionRucCodigoMensaje'] = utf8_encode(trim($row['ValidacionRucCodigoMensaje']));
                        $_SESSION['ValidacionErrorRUC'] = $this->lc_regs['ValidacionErrorRUC'] = utf8_encode(trim($row['ValidacionErrorRUC']));
                        $_SESSION['HabilitarValidacionRUC'] = $this->lc_regs['HabilitarValidacionRUC'] = utf8_encode(trim($row['HabilitarValidacionRUC']));
                        $_SESSION['ValidacionRUCintento'] = $this->lc_regs['ValidacionRUCintento'] = utf8_encode(trim($row['ValidacionRUCintento']));
                        $_SESSION['ValidacionRUCdirectoN'] = $this->lc_regs['ValidacionRUCdirectoN'] = utf8_encode(trim($row['ValidacionRUCdirectoN']));
                        $_SESSION['ValidacionRUCdirecto'] = $this->lc_regs['ValidacionRUCdirecto'] = utf8_encode(trim($row['ValidacionRUCdirecto']));
                        $_SESSION['servicioApiImpresion'] = $this->lc_regs['servicioApiImpresion'] = (trim($row['servicio_impresion']));
                        $_SESSION['unificacion_transferencia_de_venta'] = $this->lc_regs['unificacion_transferencia_de_venta'] = utf8_encode(trim($row['unificacion_transferencia_de_venta']));
                        $_SESSION['ValidacionAnulacionFacturaTiempoApp'] = $this->lc_regs['ValidacionAnulacionFacturaTiempoApp'] = isset($row['ValidacionAnulacionFacturaTiempoApp'])? trim($row['ValidacionAnulacionFacturaTiempoApp']) : 0;
                        $_SESSION['ValidacionAnulacionFacturaTiempoFast'] = $this->lc_regs['ValidacionAnulacionFacturaTiempoFast'] = isset($row['ValidacionAnulacionFacturaTiempoFast'])? trim($row['ValidacionAnulacionFacturaTiempoFast']) : 0;
                        $_SESSION['canje_v2'] = $this->lc_regs['canje_v2'] = utf8_encode(trim($row['canje_v2']));
                        $_SESSION['cambio_caje_v1_v2'] = $this->lc_regs['cambio_caje_v1_v2'] = utf8_encode(trim($row['cambio_caje_v1_v2']));

                        $_SESSION['aplicaDesmontadoCajeroV2'] = $this->lc_regs['aplicaDesmontadoCajeroV2'] = utf8_encode(trim($row['aplicaDesmontadoCajeroV2']));
                        $this->lc_regs['userAdminPos'] = utf8_encode(trim($row['userAdminPos']));

                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            //inicio variables de session desde la pantalla fin del dia
            case "inicioVariablesSesionFinDia":
                $lc_sql = "EXECUTE seguridad.USP_variablessesion $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', $lc_datos[3], $lc_datos[4]";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $_SESSION['cargoPisoArea'] = "No";
                        $_SESSION['validado'] = TRUE;
                        $_SESSION['usuarioIdAdmin'] = $this->lc_regs['usr_id_admin'] = $row["usr_id_admin"];
                        $_SESSION['usuarioId'] = $this->lc_regs['usr_id_cajero'] = $row["usr_id_cajero"];
                        $_SESSION['usuario'] = $this->lc_regs['usr_descripcion'] = utf8_encode($row["usr_descripcion"]);
                        $_SESSION['nombre'] = $this->lc_regs['usr_usuario'] = $row["usr_usuario"];
                        $_SESSION['perfil'] = $this->lc_regs['prf_id'] = $row["prf_id"];
                        $this->lc_regs['prf_descripcion'] = utf8_encode($row["prf_descripcion"]);
                        $_SESSION['rstId'] = $this->lc_regs['rst_id'] = $row["rst_id"];
                        $_SESSION['rstCodigoTienda'] = $this->lc_regs['rst_cod_tienda'] = $row["rst_cod_tienda"];
                        $_SESSION['rstNombre'] = $this->lc_regs['rst_descripcion'] = utf8_encode($row["rst_descripcion"]);
                        $_SESSION['TipoServicio'] = $this->lc_regs['rst_tipo_servicio'] = $row["rst_tipo_servicio"];
                        $_SESSION['EstacionNombre'] = $this->lc_regs['est_nombre'] = $row["est_nombre"];
                        $_SESSION['estacionId'] = $this->lc_regs['est_id'] = $row["est_id"];
                        $_SESSION['direccionIp'] = $this->lc_regs['est_ip'] = $row["est_ip"];
                        $_SESSION['numPiso'] = $this->lc_regs['rst_numpiso'] = $row["rst_numpiso"];
                        $_SESSION['numMesa'] = $this->lc_regs['rst_num_mesas'] = $row["rst_num_mesas"];
                        $_SESSION['cadenaId'] = $this->lc_regs['cdn_id'] = $row["cdn_id"];
                        $_SESSION['cadenaNombre'] = $this->lc_regs['cdn_descripcion'] = utf8_encode($row["cdn_descripcion"]);
                        $_SESSION['logo'] = $this->lc_regs['cdn_logotipo'] = $row["cdn_logotipo"];
                        $_SESSION['simboloMoneda'] = $this->lc_regs['pais_moneda_simbolo'] = $row["pais_moneda_simbolo"];
                        $_SESSION['sesionbandera'] = $this->lc_regs['sesion_bandera'] = $row["sesion_bandera"];
                        $_SESSION['IDPeriodo'] = $this->lc_regs['IDPeriodo'] = $row["IDPeriodo"];
                        $_SESSION['fecha_prd'] = $this->lc_regs['fecha_prd'] = $row["fecha_prd"];
                        $_SESSION['IDControlEstacion'] = $this->lc_regs['IDControlEstacion'] = $row["IDControlEstacion"];
                        $_SESSION['paisIsoAlfa2'] = $this->lc_regs['pais_iso_alfa2'] = $row["pais_iso_alfa2"];
                        $_SESSION['ValidacionRucCodigo'] = $this->lc_regs['ValidacionRucCodigo'] = trim($row['ValidacionRucCodigo']);
                        $_SESSION['ValidacionRucCodigoMensaje'] = $this->lc_regs['ValidacionRucCodigoMensaje'] = utf8_encode(trim($row['ValidacionRucCodigoMensaje']));
                        $_SESSION['ValidacionErrorRUC'] = $this->lc_regs['ValidacionErrorRUC'] = utf8_encode(trim($row['ValidacionErrorRUC']));
                        $_SESSION['HabilitarValidacionRUC'] = $this->lc_regs['HabilitarValidacionRUC'] = utf8_encode(trim($row['HabilitarValidacionRUC']));
                        $_SESSION['ValidacionRUCintento'] = $this->lc_regs['ValidacionRUCintento'] = utf8_encode(trim($row['ValidacionRUCintento']));
                        $_SESSION['ValidacionRUCdirectoN'] = $this->lc_regs['ValidacionRUCdirectoN'] = utf8_encode(trim($row['ValidacionRUCdirectoN']));
                        $_SESSION['ValidacionRUCdirecto'] = $this->lc_regs['ValidacionRUCdirecto'] = utf8_encode(trim($row['ValidacionRUCdirecto']));
                        $_SESSION['servicioApiImpresion'] = $this->lc_regs['servicioApiImpresion'] = (trim($row['servicio_impresion']));
                        $_SESSION['unificacion_transferencia_de_venta'] = $this->lc_regs['unificacion_transferencia_de_venta'] = utf8_encode(trim($row['unificacion_transferencia_de_venta']));
                        $_SESSION['ValidacionAnulacionFacturaTiempoApp'] = $this->lc_regs['ValidacionAnulacionFacturaTiempoApp'] = isset($row['ValidacionAnulacionFacturaTiempoApp'])? trim($row['ValidacionAnulacionFacturaTiempoApp']) : 0;
                        $_SESSION['ValidacionAnulacionFacturaTiempoFast'] = $this->lc_regs['ValidacionAnulacionFacturaTiempoFast'] = isset($row['ValidacionAnulacionFacturaTiempoFast'])? trim($row['ValidacionAnulacionFacturaTiempoFast']) : 0;
                        $_SESSION['canje_v2'] = $this->lc_regs['canje_v2'] = utf8_encode(trim($row['canje_v2']));
                        $_SESSION['cambio_caje_v1_v2'] = $this->lc_regs['cambio_caje_v1_v2'] = utf8_encode(trim($row['cambio_caje_v1_v2']));
                        $_SESSION['aplicaDesmontadoCajeroV2'] = $this->lc_regs['aplicaDesmontadoCajeroV2'] = utf8_encode(trim($row['aplicaDesmontadoCajeroV2']));
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case "inicioVariablesDeSesionUserReportes":
                $lc_sql = "EXECUTE seguridad.USP_variablessesion $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', $lc_datos[3], $lc_datos[4]";
                if ($result = $this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $_SESSION['cargoPisoArea'] = "No";
                        $_SESSION['validado'] = TRUE;
                        $_SESSION['usuarioId_Visita'] = $this->lc_regs['usr_id'] = $row["usr_id"];
                        $_SESSION['usuario'] = $this->lc_regs['usr_descripcion'] = utf8_encode($row["usr_descripcion"]);
                        $_SESSION['nombre'] = $this->lc_regs['usr_usuario'] = $row["usr_usuario"];
                        $_SESSION['perfil'] = $this->lc_regs['prf_id'] = $row["prf_id"];
                        $_SESSION['rstId'] = $this->lc_regs['rst_id'] = $row["rst_id"];
                        $_SESSION['rstCodigoTienda'] = $this->lc_regs['rst_cod_tienda'] = $row["rst_cod_tienda"];
                        $_SESSION['rstNombre'] = $this->lc_regs['rst_descripcion'] = utf8_encode($row["rst_descripcion"]);
                        $_SESSION['TipoServicio'] = $this->lc_regs['rst_tipo_servicio'] = $row["rst_tipo_servicio"];
                        $_SESSION['EstacionNombre'] = $this->lc_regs['est_nombre'] = $row["est_nombre"];
                        $_SESSION['estacionId'] = $this->lc_regs['est_id'] = $row["est_id"];
                        $_SESSION['direccionIp'] = $this->lc_regs['est_ip'] = $row["est_ip"];
                        $_SESSION['numPiso'] = $this->lc_regs['rst_numpiso'] = $row["rst_numpiso"];
                        $_SESSION['numMesa'] = $this->lc_regs['rst_num_mesas'] = $row["rst_num_mesas"];
                        $_SESSION['cadenaId'] = $this->lc_regs['cdn_id'] = $row["cdn_id"];
                        $_SESSION['cadenaNombre'] = $this->lc_regs['cdn_descripcion'] = utf8_encode($row["cdn_descripcion"]);
                        $_SESSION['logo'] = $this->lc_regs['cdn_logotipo'] = $row["cdn_logotipo"];
                        $_SESSION['simboloMoneda'] = $this->lc_regs['pais_moneda_simbolo'] = $row["pais_moneda_simbolo"];
                        $_SESSION['IDPeriodo'] = $this->lc_regs['IDPeriodo'] = $row["IDPeriodo"];
                        $_SESSION['fecha_prd'] = $this->lc_regs['fecha_prd'] = $row["fecha_prd"];
                        $_SESSION['IDControlEstacion'] = $this->lc_regs['IDControlEstacion'] = $row["IDControlEstacion"];
                        $_SESSION['paisIsoAlfa2'] = $this->lc_regs['pais_iso_alfa2'] = $row["pais_iso_alfa2"];
                        $_SESSION['ValidacionRucCodigo'] = $this->lc_regs['ValidacionRucCodigo'] = trim($row['ValidacionRucCodigo']);
                        $_SESSION['ValidacionRucCodigoMensaje'] = $this->lc_regs['ValidacionRucCodigoMensaje'] = utf8_encode(trim($row['ValidacionRucCodigoMensaje']));
                        $_SESSION['ValidacionErrorRUC'] = $this->lc_regs['ValidacionErrorRUC'] = utf8_encode(trim($row['ValidacionErrorRUC']));
                        $_SESSION['HabilitarValidacionRUC'] = $this->lc_regs['HabilitarValidacionRUC'] = utf8_encode(trim($row['HabilitarValidacionRUC']));
                        $_SESSION['ValidacionRUCintento'] = $this->lc_regs['ValidacionRUCintento'] = utf8_encode(trim($row['ValidacionRUCintento']));
                        $_SESSION['ValidacionRUCdirectoN'] = $this->lc_regs['ValidacionRUCdirectoN'] = utf8_encode(trim($row['ValidacionRUCdirectoN']));
                        $_SESSION['ValidacionRUCdirecto'] = $this->lc_regs['ValidacionRUCdirecto'] = utf8_encode(trim($row['ValidacionRUCdirecto']));
                        $_SESSION['servicioApiImpresion'] = $this->lc_regs['servicioApiImpresion'] = (trim($row['servicio_impresion']));
                        $_SESSION['unificacion_transferencia_de_venta'] = $this->lc_regs['unificacion_transferencia_de_venta'] = utf8_encode(trim($row['unificacion_transferencia_de_venta']));
                        $_SESSION['ValidacionAnulacionFacturaTiempoApp'] = $this->lc_regs['ValidacionAnulacionFacturaTiempoApp'] = isset($row['ValidacionAnulacionFacturaTiempoApp'])? trim($row['ValidacionAnulacionFacturaTiempoApp']) : 0;
                        $_SESSION['ValidacionAnulacionFacturaTiempoFast'] = $this->lc_regs['ValidacionAnulacionFacturaTiempoFast'] = isset($row['ValidacionAnulacionFacturaTiempoFast'])? trim($row['ValidacionAnulacionFacturaTiempoFast']) : 0;
                        $_SESSION['canje_v2'] = $this->lc_regs['canje_v2'] = utf8_encode(trim($row['canje_v2']));
                        $_SESSION['cambio_caje_v1_v2'] = $this->lc_regs['cambio_caje_v1_v2'] = utf8_encode(trim($row['cambio_caje_v1_v2']));
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;
        }
    }
    
    function obtenerLimitesRecarga($idCadena) {
        $lc_sql = "EXECUTE [recargas].[obtenerLimitesRecarga] $idCadena";
        if ($this->fn_ejecutarquery($lc_sql)) {
            $row = $this->fn_leerarreglo();
            $this->lc_regs['min'] = $row['min'];
            $this->lc_regs['max'] = $row['max'];
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }


    function obtenerClavesSeguridad($opcion, $descripcion, $idCadena)
    {
        $lc_sql = "EXECUTE [seguridad].[POLITICAS_clavesSeguridad] $opcion,'','$descripcion','',$idCadena,''";
        if ($this->fn_ejecutarquery($lc_sql)) {
            $row = $this->fn_leerarreglo();
            $this->lc_regs['id'] = $row['id'];
            $this->lc_regs['descripcion'] = $row['descripcion'];
            $this->lc_regs['clave'] = $row['clave'];
            $this->lc_regs['str'] = $this->fn_numregistro();
        }
        return json_encode($this->lc_regs);
    }
}