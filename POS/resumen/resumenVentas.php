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
        <link rel="stylesheet" type="text/css" href="../css/movimientos.css"/>
        <!-- Librerias JavaScript -->
        <script src="../js/jquery1.11.1.js"></script>
        <script src="../js/jquery-ui.js"></script>
        <script src="../bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="../js/scroll/mousewheel.js"></script>
        <script type="text/javascript" src="../js/jquery.jb.shortscroll.js"></script>
        <script src="../js/moment.js"></script>
        <script type="text/javascript" src="../js/chartjs.js"></script>
        <script type="text/javascript" src="../js/utils.js"></script>
        <script type="text/javascript" src="../js/ajax_resumenVentas.js"></script>

    </head>
    <body>

        <?php
        $usr_nombre = $_SESSION['nombre'];
        $bloqueado  = $_SESSION['bloqueoacceso'];
        ?>
        <input inputmode="none"  type="hidden" id="txt_bloqueado" value="<?php echo $bloqueado ?>"/>
                
        
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
                                    <th class="text-center">Transacciones</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Ticket Prom.</th>
                                </tr>
                            </table>
                        </div>

                    </div>

                </div>
            </div>

            

            <!-- Fin id.cntndr_pntll_rsmn_vnts -->
        </div>

        
        
        <div id="cntFormulario"></div>
        
        <div id="div_Opciones" class="cntMovimientos" align="center" style="width: 600px; height: 400px ">

                <div class="cntTituloMovimiento" >
                    <h3>Resumen de Ventas..</h3>
                </div>
                
            <button type="button" class="btn btn-primary btn-lg btn-block" style="height: 125px; width: 300px; margin-top: 30px;font-size: 35px;" onclick="fn_cargaVentaCajero(event);" >Cajero</button>
            <button type="button" class="btn btn-primary btn-lg btn-block" style="height: 125px; width: 300px; margin-top: 30px;font-size: 35px;" onclick="fn_cargaVentaTotal(event);" >Total</button>

            </div>
        
        
        <!-- Contenedor Totales -->
        <div id="cntndr_ttls_rsmn_vnts" class="row cntndr_ttls_rsmn_vnts sn_mrgn_pdng" style="position: fixed !important;bottom: 0 !important;width: 100% !important; z-index: 99999 !important;">
                <input inputmode="none"  type="button" id="boton_sidr" value="Menu" class="boton_Accion"/>
            </div>
        
        <div id="cntndr_pntll_rsmn_vnts_totales" class="cntndr_pntll_rsmn_vnts">
        

            <div id="cntndr_cbcr_rsmn_vnts" class="row cntndr_cbcr_rsmn_vnts small">
                <br/>
                <div class="row sn_mrgn_pdng">
                    <div class="col-md-1"><b>Periodo:</b></div>
                    <div id="tqt_fch_prd_totales" class="col-md-8"></div>
                    <div class="col-md-1"><b>Fecha:</b></div>
                    <div id="tqt_fch_ctl_totales" class="col-md-2"></div>
                </div>
                <br/>
                <div class="row sn_mrgn_pdng">
                    <div class="col-md-1"><b>Usuario:</b></div>
                    <div class="col-md-2"><?php echo $usr_nombre; ?></div>
                </div>
            </div>

            <!-- Contenedor Detalle Resumen Ventas -->
            <style>
		#canvas-holder {
				width: 100%;
				margin-top: 30px;
				text-align: center;   
                                margin-left: 200px;
		}
		#chartjs-tooltip {
			opacity: 1;
			position: absolute;
			background: rgba(0, 0, 0, .7);
			color: white;
			border-radius: 3px;
			-webkit-transition: all .1s ease;
			transition: all .1s ease;
			pointer-events: none;
			-webkit-transform: translate(-50%, 0);
			transform: translate(-50%, 0);
                        
		}

		.chartjs-tooltip-key {
			display: inline-block;
			width: 10px;
			height: 10px;
			margin-right: 10px;
                        
		}
		</style>
            <div style="background: #fff; width: 1024; height:610px; ">

                <div id="cntndr_dtll_rsmn_vnts" class="row cntndr_dtll_rsmn_vnts">

                    <br/>
                    <br/>
                   
                    <div id="cntndr_dtll_rsmn_vnts" class="row sn_mrgn_pdng">
                            
                        
                        <div id="canvas-holder" style="width: 500px;">
                        <canvas id="chart-area" width="500" height="500"></canvas>
                        <div id="chartjs-tooltip">
                                <table></table>
                        </div>
                        </div>

                        
                        <!-- Detalle Ventas Cajeros -->
                        <div class="col-md-12">
                            <table id="tbl_rsm_vnts_prd_totales" class="table table-bordered small">
                                <!--<tr class="active">
                                    <th class="text-center">Cajero</th>
                                    <th class="text-center">Turno</th>                                    
                                </tr>-->
                            </table>
                        </div>

                    </div>

                </div>
            </div>

            

            <!-- Fin id.cntndr_pntll_rsmn_vnts -->
        </div>
        
        
        <!-- SubMenu Opciones -->
        <div id="rdn_pdd_brr_ccns" class="menu_desplegable">
            <!--Lado Izquierdo <div id="cnt_mn_dsplgbl_pcns_zqrd" class="modal_opciones_zqd"></div> -->
            <div id="cnt_mn_dsplgbl_pcns_drch" class="modal_opciones_drc">
                <input inputmode="none"  type="button" id='funcionesGerente' onclick='fn_funcionesGerente()' class="boton_Opcion" value="Funciones Gerente"/>
                <!--<input inputmode="none"  type="button" id="nuevaorden" onclick="fn_obtenerMesa()" class="boton_Opcion" value="Orden Pedido"/>-->
                    <!-- <input inputmode="none"  type="button" id="btn_salirSistema" class="boton_Opcion_Bloqueado" onclick="fn_salirSistema()" value="Salir Sistema" disabled="disabled"/> -->
            </div>
        </div>

    </body>
</html>