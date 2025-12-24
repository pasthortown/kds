<?php
session_start();

include_once"../system/conexion/clase_sql.php";
include_once"../clases/clase_seguridades.php";
include_once"../seguridades/seguridad_niv2.inc";

$seguridades = new seguridades();

/////////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Jorge Tinoco ///////////////////////////////////////
///////DESCRIPCION: Resumen de Ventas ///////////////////////////////////////
///////FECHA CREACION: 20-10-2015 ///////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>Resumen Ventas</title>

        <!-- Librerias CSS -->
        <link rel="StyleSheet" href="../bootstrap/css/bootstrap.min.css" type="text/css"/>
        <link rel="stylesheet" type="text/css" href="../css/jquery-ui.css"/>
        <link rel="stylesheet" href="../css/jquery.jb.shortscroll.css" />
        <link rel="StyleSheet" href="../css/resumenVentas.css" type="text/css"/>

        <!-- Librerias JavaScript -->
        <script src="../js/jquery1.11.1.js"></script>
        <script src="../js/jquery-ui.js"></script>
        <script src="../bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="../js/scroll/mousewheel.js"></script>
        <script type="text/javascript" src="../js/jquery.jb.shortscroll.js"></script>
        <script src="../js/moment.js"></script>
        <script type="text/javascript" src="../js/ajax_resumenVentas_formaspago.js"></script>

    </head>
    <body>

<?php
$usr_nombre = $_SESSION['nombre'];
?>

        <div id="cntndr_pntll_rsmn_vnts" class="cntndr_pntll_rsmn_vnts">

            <div id="cntndr_cbcr_rsmn_vnts" class="row cntndr_cbcr_rsmn_vnts small">
                <br/>
                <div class="row sn_mrgn_pdng">
                    <div class="col-md-1"><b>Periodo:</b></div>
                    <div id="tqt_fch_prd" class="col-md-8"></div>
                    <div class="col-md-1"><b>Fecha:</b></div>
                    <div id="tqt_fch_ctl" class="col-md-2"></div>
                </div>
                <br/>
                <div class="row sn_mrgn_pdng">
                    <div class="col-md-1"><b>Usuario:</b></div>
                    <div class="col-md-2"><?php echo $usr_nombre; ?></div>
                </div>
            </div>

            <!-- Contenedor Detalle Resumen Ventas -->
            <div style="background: #fff; width: 1024; height:610px; ">

                <div id="cntndr_dtll_rsmn_vnts" class="row cntndr_dtll_rsmn_vnts">

                    <br/>
                    <br/>
                    <div class="col-md-6"><h5><b>VENTAS POR CAJERO</b></h5></div>
                    <div id="cntndr_dtll_rsmn_vnts" class="row sn_mrgn_pdng">

                        <!-- Detalle Ventas Cajeros -->
                        <div class="col-md-12">
                            <table id="tbl_rsm_vnts_prd" class="table table-bordered small">
                                <tr class="active">
                                    <th class="text-center">Cajero</th>
                                    <th class="text-center">Turno</th>
                                    <th class="text-center">Cupones</th>
                                    <th class="text-center">Efectivo</th>
                                    <th class="text-center">Tarjetas</th>
                                    <th class="text-center">Ret. Iva</th>
                                    <th class="text-center">Ret. Fuente</th>
                                    <th class="text-center">Cheques</th>
                                    <th class="text-center">PayPhone</th>
                                    <th class="text-center">Empleado</th>
                                    <th class="text-center">Trans.</th>
                                    <th class="text-center">Venta</th>
                                    <th class="text-center">Ticket Prom.</th>
                                </tr>
                            </table>
                        </div>

                    </div>

                </div>
            </div>

            <!-- Contenedor Totales -->
            <div id="cntndr_ttls_rsmn_vnts" class="row cntndr_ttls_rsmn_vnts sn_mrgn_pdng">
                <input inputmode="none"  type="button" id="boton_sidr" value="Menu" class="boton_Accion"/>
            </div>

            <!-- Fin id.cntndr_pntll_rsmn_vnts -->
        </div>

        <!-- SubMenu Opciones -->
        <div id="rdn_pdd_brr_ccns" class="menu_desplegable">
            <!--Lado Izquierdo <div id="cnt_mn_dsplgbl_pcns_zqrd" class="modal_opciones_zqd"></div> -->
            <div id="cnt_mn_dsplgbl_pcns_drch" class="modal_opciones_drc">
                <input inputmode="none"  type="button" id='funcionesGerente' onclick='fn_funcionesGerente()' class="boton_Opcion_Bloqueado" value="Funciones Gerente"/>
                <input inputmode="none"  type="button" id="nuevaorden" onclick="fn_obtenerMesa()" class="boton_Opcion" value="Orden Pedido"/>
                    <!-- <input inputmode="none"  type="button" id="btn_salirSistema" class="boton_Opcion_Bloqueado" onclick="fn_salirSistema()" value="Salir Sistema" disabled="disabled"/> -->
            </div>
        </div>

    </body>
</html>