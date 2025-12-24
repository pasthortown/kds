<?php
session_start();

/*
  DESARROLLADO POR			: Juan M�ndez
  DESCRIPCION					: Configuraci�n Men�
  TABLAS INVOLUCRADAS			:
  FECHA CREACION				: 13-12-2013
  -------------------------------------------------
  FECHA ULTIMA MODIFICACION	: 15-04-2014
  USUARIO QUE MODIFICO:  		Cristhian Castro
  DECRIPCION ULTIMO CAMBIO	: Mantenimiento Orden Pedido
  -------------------------------------------------
  FECHA ULTIMA MODIFICACION	: 03/06/2015
  USUARIO QUE MODIFICO		:  Jose Fernandez
  DECRIPCION ULTIMO CAMBIO	: Cambio de estilos, colocar imagen en boton eliminar
  -------------------------------------------------
  FECHA ULTIMA MODIFICACION	: 28/07/2015
  USUARIO QUE MODIFICO		: Daniel Llerena
  DECRIPCION ULTIMO CAMBIO	: Cambio de estilos, creacion de SP y auditorias
  -------------------------------------------------
  FECHA ULTIMA MODIFICACION	: 24/08/2015
  USUARIO QUE MODIFICO		:  Jose Fernandez
  DECRIPCION ULTIMO CAMBIO	: Nueva funcionalidad Drag and Drop
  -------------------------------------------------
  -------------------------------------------------
  FECHA ULTIMA MODIFICACION	: 10:33 2/3/2017
  USUARIO QUE MODIFICO		:  Mychael Castro
  DECRIPCION ULTIMO CAMBIO	: Controlar drag and drop  con el zoom + y - del navegador.
  -------------------------------------------------
 */

include_once"../../seguridades/seguridad.inc";
include_once"../../system/conexion/clase_sql.php";
include_once"../../clases/clase_seguridades.php";
include_once"../../clases/clase_tomapedidoMenu.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" /> 
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 
        <title>Administrador Toma de Pedido</title>
        <!-- ESTILOS -->
        <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css" />
        <link rel="StyleSheet" href="../../css/jquery-ui.css" type="text/css"/>
        <link rel="stylesheet" href="../../css/alertify.core.css" />
        <link rel="stylesheet" href="../../css/alertify.default.css" />
        <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
        <link rel="stylesheet" type="text/css" href="../../css/style_admin_tomaPedido.css"/>
    </head>
    <body>

        <input inputmode="none"  type="hidden" id="codigoCadena" value="<?php echo $_SESSION['cadenaId']; ?>"/>
        <input inputmode="none"  id="sess_usr_id" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
        <input inputmode="none"  type="hidden" id="menuId"/>
        <input inputmode="none"  type="hidden" id="hoverIDproducto"/>
        <input inputmode="none"  type="hidden" id="hoverIDcategoria"/>
        <input inputmode="none"  type="hidden" id="startLiProducto"/>
        <input inputmode="none"  type="hidden" id="startLiMenu"/>
        <input inputmode="none"  type="hidden" id="posStart"/>
        <input inputmode="none"  type="hidden" id="codigoCategoria"/>
        <input inputmode="none"  type="hidden" id="codigoAgrupacionProducto"/>

        <div class="superior" style="height:65px"><!-- titulo -->
            <div class="tituloPantalla">
                <h1>Administraci&oacute;n de Men&uacute;</h1>
            </div>
        </div>

        <div id="comboMenuCadena" class="row">
            <div class="col-sm-12 col-md-1"><h4 class="text-right">Menu</h4></div>
            <div class="col-sm-12 col-md-4">
                <div class="form-group">
                    <select id="menucadena" class="form-control"></select>
                </div>
            </div>
            
                <div class="col-sm-12 col-md-5">
                    <h4 class="text-right" id="desc_cat"></h4></div>
                </div>
            </div>
            
        </div>

        <div id="lista" role="tablist">
            <ul class="nav nav-tabs">
                <li role="presentation" class="active"><a href="#homeCategoria" aria-controls="homeCategoria" role="tab" data-toggle="tab"><b>Categor&iacute;a</b></a></li>
                <li role="presentation"><a href="#homeProductos" aria-controls="homeProductos" role="tab" data-toggle="tab"><b>Productos</b></a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="homeCategoria">
                    <div id="listadoCategorias">
                        <ul></ul>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="homeProductos">
                    <div id="listadoProductos">
                        <ul></ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="menuProducto" overflow="scroll"></div>

        <div  id="menuCategoria" overflow="scroll"></div>
        
        <div id="mdl_rdn_pdd_crgnd" class="modal_cargando" ng-show="cargando">
            <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
                <img src="../../imagenes/admin_resources/progressBar.gif" />
            </div>
        </div>

        <!-- JQUERY -->
        <script type="text/javascript" src="../../js/jquery1.11.1.js"></script>
        <script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
        <script type="text/javascript" src="../../js/jquery-ui.js"></script>
        <script type="text/javascript" src="../../js/jquery.sortable.js"></script>
        <script type="text/javascript" src="../../js/alertify.js"></script>    
        <script type="text/javascript" src="../../js/ajax_tomaPedido.js"></script>

    </body>
</html>