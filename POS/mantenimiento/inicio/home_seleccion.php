<?php
///////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Christian Pinto//////////////////////////
////////DESCRIPCION: P�gina que muestra los restaurantes //////////
/////////////////// a los que puede ingresar  /////////////////////
///////TABLAS INVOLUCRADAS: Users_Pos, Users_Restaurante_Pos //////
///////////////////////     Restaurante  //////////////////////////
///////FECHA CREACION: 25-05-2015//////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 25/05/2015 //////////////////////////////////
///////USUARIO QUE MODIFICO: Darwin Mora///////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Cambio de estilos ////////////////////////////
///////////////////////////////////////////////////////////////////////////////

session_start();
require_once'../../system/conexion/clase_sql.php';
include_once'../../clases/clase_seguridades.php';
include_once'../../clases/clase_direccion.php';
$lc_perfil = $_SESSION['perfil'];
$pantalla = new seguridades();

$lc_usuario = $_SESSION['usuario'];
$lc_idusuario = $_SESSION['usuarioId'];
$lc_perfil = $_SESSION['perfil'];
$lc_nombre = $_SESSION['nombre'];

//Para definir el periodo
$obj_periodo = new seguridades();
$obj_restaurante = new seguridades();
?>
<HTML>
    <HEAD>
        <TITLE>Max Point</TITLE>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=iso-8859-1">

        <link rel="stylesheet" href="../../bootstrap/css/bootstrap.css" type="text/css"/>
        <link rel="stylesheet" href="../../css/jquery-ui.css" type="text/css"/>
        <link rel="stylesheet" type="text/css" href="../../css/style_home_seleccion.css">
        <script src="../../js/jquery1.11.1.js"></script>
        <script src="../../bootstrap/js/bootstrap.js"></script>
        <script src="../../js/jquery-ui.js"></script>
        <script language="javascript1.1" src="../../js/alertify.js"></script>
        <script type="text/javascript" src="../../js/ajax_ingreso_admin.js"></script>

        <style type="text/css">
            #contenidoImagenes   div   {
                text-align: center;
                margin: 2% 2% 1% 2.7%;
                font-size: 8px;
            }            
            #contenidoImagenes   div.imagen  {
                float: left;
                width: 100px;
                height: 64px;
            }
            #contenidoImagenes   div.imagen img  {
                width: 70px;
                height: 55px;
                cursor: pointer;
            } 
            .clearbot{}
        </style>

    </HEAD>
    <body class="fondobarra">
        <div>
            <div>
                <p class="nombremax" style="text-align:center">Max<span class="nombrepoint">Point</span></p>
            </div>
        </div>
        <strong>
            <div class="panel panel-default col-xs-8 col-xs-offset-2 centered">
                <div class="panel-body" style="    width: 100%;    margin: auto;">
                    <form name="frmAccesoUser" method="POST" action="../../seguridades/asignar_restaurante.php">
                        <div class="form-group"   >
                            <div class="input-group">
                                <!--<input inputmode="none"  type="text" class="form-control input-lg" id='txtUsuario' name="txtUsuario" placeholder="<?php echo $lc_nombre; ?>" readonly >-->

                                <label for="uLogin" class="input-group-addon glyphicon glyphicon-user text-left"> <?php echo $lc_nombre; ?></label>
                                <!--<label for="uLogin" class="input-group-addon glyphicon glyphicon-user"></label>-->
                            </div>
                        </div> <!-- /.form-group -->
                        <div id="contenidoImagenes">
                            <?php
                            $lc_condiciones[0] = $lc_idusuario;
                            if ($obj_restaurante->fn_armarquery('usuario_x_restaurante', $lc_condiciones)) {
                                while ($lc_rows = $obj_restaurante->fn_leerObjeto()) {
                                    ?>

                                    <div class="imagen">
                                        <img src="../../imagenes/Logos/<?php echo $lc_rows->cdn_logotipo; ?>" class="botonhabilitado  img-circle" alt="Cinque Terre" onClick="envia_home(<?php echo $lc_rows->cdn_id; ?>)">
                                        <div> <?php echo utf8_decode($lc_rows->cdn_descripcion); ?> </div>
                                    </div>
                                    <!--&nbsp;&nbsp;&nbsp;-->



                                    <?php
                                }
                            }
                            ?> 
                        </div>
                </div>
                <div id="clearbot"></div>

                <div align="center">
                    <input inputmode="none"  class="btn btn-primary btn-lg" name="" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Salir&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" onClick="retornar_index()" type="button" width="100%"/>
                </div>
                <br/>
                <input inputmode="none"  type="hidden" value="" id="selrestaurante" name="selrestaurante" >
                </form>
            </div>
        </div>
    </strong>
</body>
</HTML>