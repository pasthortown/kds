<?php
///////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR JOSE FERNANDEZ////////////////////////////////////
///////////DESCRIPCION: PANTALLA DE APERTURA DE PERIODO////////////////////
////////////////TABLAS: PERIODO////////////////////////////////////////////
////////FECHA CREACION: 20/12/2013/////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
//session_start();
include "../system/conexion/clase_sql.php";
include_once "../clases/clase_apertura.php";

$apertura = new apertura();
$lc_servicio = /* $_SESSION['TipoServicio'] */1;
$lc_perfil = /* $_SESSION['perfil'] */1;
$failPage = "../index.php";

//echo $lc_ipestacion;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <!--<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />-->
        <title>Apertura</title>

        <script src="../js/jquery1.11.1.js"></script>
        <script src="../js/jquery-2.1.4.min.js"></script>
        <script language="javascript" type="text/javascript" src="../js/moment.js"></script>
        <script language="javascript" type="text/javascript" src="../js/ajax_apertura.js"></script>
        <script language="javascript1.1"  src="../js/alertify.js"></script>
        <script language="javascript"  src="../js/reloj_digital.js"></script>
        <script src="../bootstrap/js/bootstrap.min.js"></script>
        <script src="../bootstrap/js/bootstrap.js"></script>
        <script src="../js/jquery-ui.js"></script>


        <link rel="stylesheet" type="text/css" href="../css/est_reloj.css" />
        <link rel="stylesheet" href="../bootstrap/css/bootstrap.css" type="text/css"/>
        <link rel="stylesheet" href="../bootstrap/css/bootstrap-theme.min.css" type="text/css"/>
        <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css" type="text/css"/>
        <link rel="stylesheet" href="../css/style_index.css" type="text/css"/>
        <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css"/>
        <link rel="stylesheet" type="text/css" href="../css/alertify.core.css"/>
        <link rel="stylesheet" type="text/css" href="../css/alertify.default.css"/>
        <link rel="stylesheet" type="text/css" href="../css/style_home_seleccion.css"/>

    </head>

    <body>
        <?php
            if (htmlspecialchars(!isset($_GET['usr_usuario']))) {
                session_destroy();                
        ?>
                <script type="text/javascript">
                    alertify.alert("Debe ingresar al sistema de manera correcta, por favor digite sus credenciales");
                    document.location.href = "<?php echo $failPage ?>";
                </script> 
        <?php
            } else {
                
            }

            $lc_ipestacion = htmlspecialchars($_GET['est_ip']);
            $lc_usuario = htmlspecialchars($_GET['usr_usuario']);
            $opcion_apertura = htmlspecialchars($_GET['opcion_apertura']);
        ?>

        <br/><br/>

        <div class="col-md-10 col-md-offset-1 " id="login">
            <div>
                <div>
                    <p class="nombremax" style="text-align:center">Max<span class="nombrepoint">Point</span></p>
                </div>
            </div>

            <div class="panel-body">

                <div class="row">
                    <div class="">
                        <div id="fecha" align="center"> 	
                            <?php
                                date_default_timezone_set("America/Lima");
                                setlocale(LC_TIME, 'es_ES', 'Spanish_Spain', 'Spanish');
                                $timestamp = time();
                                if ($opcion_apertura != 1) {
                                    $periodo_secuancial = $apertura->fn_fechaPeriodoSecuencial(2, '');
                                    $fecha_periodo_secuencial = $periodo_secuancial["fecha_periodo_secuencial"];

                                    $timestamp = strtotime($fecha_periodo_secuencial);
                                    $lc_fecha = strftime("%A, %d de %B de %Y", $timestamp);

                                    echo '<p style="font-size:50px; color:#ffc107">Per&iacute;odo Secuencial</p>';
                                } else {
                                    $formatter = new IntlDateFormatter(
                                        'es_ES',
                                        IntlDateFormatter::FULL, // Usamos FULL para obtener el nombre completo del día de la semana
                                        IntlDateFormatter::NONE,
                                        'America/Lima', // Zona horaria
                                        IntlDateFormatter::GREGORIAN,
                                        "EEEE, dd MMMM 'de' yyyy" // Patrón personalizado para el formato de fecha
                                    );
                                    
                                    $lc_fecha = ucfirst($formatter->format($timestamp));
                                        
                                }
                            ?>
                            <p style="font-size:50px; color:#FFF"><?php echo utf8_encode($lc_fecha); ?></p>        
                        </div>
                    </div>  
                </div>

                <div class="row">
                    <div class="">
                        <div align="center">            
                            <table id="tabla_Logo"></table>
                        </div>
                    </div>  
                </div>

                <div id="cargando" class="overlayCargando" style="display: none;">
                            <div id="cargandoimg" class="modalCargando" style="display: none;"><img src="../imagenes/cargando.gif"/></div>
                </div>
                <div class="row">
                    <div class="">
                        <div id="horas" align="center" style="font-size:30px;">
                        </div>
                    </div>  
                </div> 

                <div class="row">
                    <div class="">
                        <div id="clock" class="light">
                            <div class="display">
                                <div class="weekdays"></div>
                                <div class="ampm"></div>
                                <div class="alarm"></div>
                                <div class="digits"></div>
                            </div>
                        </div>  
                    </div>  
                </div>

                <div class="row">
                    <div class="col-md-2"></div>
                    <div class=" form-group col-md-8">
                        <button class="btn btn-lg btn-primary btn-block iniciarsesion" style="height:100px" onclick="fn_grabaperiodo(<?php echo $opcion_apertura ?>)" id="btn_guardar_periodo"><h2>La Fecha y el Tiempo son Correctos</h2></button>
                    </div>                      
                </div>
            </div>
        </div>


        <input inputmode="none"  type="hidden" name="horitas" id="horitas"/>
        <input inputmode="none"  type="hidden" value="<?php echo $lc_servicio ?>" id="txt_tipoServicio"/>
        <input inputmode="none"  type="hidden" value="<?php echo $lc_perfil; ?>" id="txt_perfil"/>
        <input inputmode="none"  type="hidden" name="est_ip" value="<?php echo $lc_ipestacion ?>" id="est_ip"/>

        <?php if ($_GET['transf'] == 1) { ?>

            <input inputmode="none"  type="hidden" id="hid_cadena_des" value="<?php echo htmlspecialchars($_GET['cdn']) ?>" />
            <input inputmode="none"  type="hidden" id="hid_rst_des" value="<?php echo htmlspecialchars($_GET['rst']) ?>" />
            <input inputmode="none"  type="hidden" id="hid_bd_dest" value="<?php echo htmlspecialchars($_GET['b_des']) ?>" />
            <input inputmode="none"  type="hidden" id="hid_transf" value="<?php echo htmlspecialchars($_GET['transf']) ?>" />


        <?php } else { ?>

            <input inputmode="none"  type="hidden" id="hid_cadena_des" value="" />
            <input inputmode="none"  type="hidden" id="hid_rst_des" value="" />
            <input inputmode="none"  type="hidden" id="hid_bd_dest" value="" />
            <input inputmode="none"  type="hidden" id="hid_transf" value="<?php echo htmlspecialchars($_GET['transf']) ?>" />

        <?php } ?>

        <input inputmode="none"  type="hidden" id="hid_cadena"/>
        <input inputmode="none"  type="hidden" id="hid_rest" />
        <input inputmode="none"  type="hidden" id="hid_tiposervicio" />
        <input inputmode="none"  type="hidden" value="<?php echo $lc_usuario; ?>" id="hid_idusuario"/>
    </body>
</html>
