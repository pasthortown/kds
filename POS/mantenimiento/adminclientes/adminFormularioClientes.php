<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////////////////////////
///////////DESCRIPCION: CREACION DE CLIENTES  //////////////////////////////////////////////////////////
////////////////TABLAS: Cliente ////////////////////////////////////////////////////////////////////////
////////FECHA CREACION: 23/08/2015//////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

session_start();
include_once "../../system/conexion/clase_sql.php";
include_once "../../clases/clase_seguridades.php";
include_once "../../clases/clase_menu.php";
include_once "../../seguridades/seguridad.inc";

require_once "datos_clientes.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1"/>
    <title>Clientes</title>
    <!---------------------------------------------------
                           ESTILOS
    ----------------------------------------------------->
    <link rel="stylesheet" href="../../css/jquery-ui.css" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css"/>
    <link rel="stylesheet" type="text/css" href="../../css/alertify.core.css"/>
    <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
    <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" type="text/css" href="../../css/chosen.css"/>


</head>

<body>
<input inputmode="none"  id="idUser" type="hidden" value="<?php echo $_SESSION['usuarioId']; ?>"/>
<div class="superior">
    <div class="menu" style="width: 500px;" align="center">
        <ul>
            <li>
            </li>
        </ul>
    </div>
    <div class="tituloPantalla">
        <h1>FORMULARIO DE CLIENTES</h1>
    </div>
</div>
<br/>
<div class="contenedor container">
    <div class="inferior">
        <?php if (true === $politicaPantallaClientes->estado && 0 < $politicaPantallaClientes->numRegistros) { ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="panel panel-primary" >
                                <div class="panel-heading">Campos disponibles</div>
                                <div class="panel-body" >
                                    <ul class="list-group" id="listadoCamposTablaCliente">
                                        <?php foreach($camposTablaCliente as $campo){ ?>
                                            <li class="list-group-item list-group-item-success campoTablaCliente" style="z-index: 10"><?php print($campo["COLUMN_NAME"]) ?></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="panel panel-primary">
                                <div class="panel-heading">Campos activos</div>
                                <div class="panel-body" id="camposActivos">
                                    <ul class="list-unstyled" id="listadoCamposActivos" style="z-index: 9">
                                        <?php foreach($camposConfigurados as $campoConfigurado){
                                            if($campoConfigurado["activo"]==1){?>
                                        <li class='campoActivoFormularioClientes' data-campo='<?php echo($campoConfigurado["campo"]) ?>' data-alias='<?php echo($campoConfigurado["alias"]) ?>' data-obligatorio='<?php echo($campoConfigurado["obligatorio"]) ?>'>
                                            <div class='panel panel-primary'>
                                            <div class='panel-body'>
                                                <div class='row'>
                                                    <div class='col-md-2'><h5><span class='label label-primary'> <?php echo($campoConfigurado["campo"]) ?> </span></h5></div>
                                                    <div class='col-md-7'>
                                                        <form class='form-inline'>
                                                            <div class='form-group'>
                                                                <label for='alias'>Alias:</label>
                                                                <input inputmode="none"  type='text' class='form-control inputAlias' name='alias' placeholder='Nombre para mostrar' value='<?php echo($campoConfigurado["alias"]) ?>'>
                                                            </div>
                                                            <div class='checkbox'>
                                                                <label>
                                                                    <input inputmode="none"  type='checkbox' class='chkObligatorio' <?php echo(($campoConfigurado['obligatorio']==1)?'checked':'') ?>> Obligatorio
                                                                </label>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="col-md-s3">
                                                        <div class="btn-group" role="group" aria-label="...">
                                                            <button type="button" class="btn btn-default btnOrdenarSubir"><span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span></button>
                                                            <button type="button" class="btn btn-default btnOrdenarBajar"><span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span></button>
                                                            <button type="button" class="btn btn-danger btnDesactivarCampo"><span class='glyphicon glyphicon-remove-circle' aria-hidden='true'></span></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <?php }} ?>
                                    </ul>
                                </div>
                                <div class="panel-footer">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <button id="btnguardarCamposFormulario" class="btn btn-info">Guardar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="panel panel-primary" >
                                <div class="panel-heading">Campos inactivos</div>
                                <div class="panel-body" >
                                    <ul class="list-group" id="listadoCamposInactivos">
                                        <?php foreach($camposConfigurados as $campoConfigurado){
                                        if($campoConfigurado["activo"]==0){?>
                                            <li class="list-group-item list-group-item-warning campoInactivoFormulario" style="z-index: 10"><?php print($campoConfigurado["campo"]) ?></li>
                                        <?php }} ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div>
                <span> La política de configuración necesaria (CAMPO FORMULARIO CLIENTES)  no existe</span>
                <span> <button id="crearPoliticaPantallaClientes" class="btn btn-info">Crear</button></span>
            </div>
        <?php } ?>
        <!-- Fin Contenedor Inferior -->
    </div>
    <!-- Fin Contenedor -->
</div>

<!---------------------------------------------------
                   JS
----------------------------------------------------->
<script type="text/javascript" src="../../js/jquery-2.1.4.min.js"></script>
<script type="text/javascript" src="../../js/jquery-ui.js"></script>
<script type="text/javascript" src="../../js/ajax_datatables.js"></script>
<script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
<script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
<script type="text/javascript" src="../../js/alertify.js"></script>
<script type="text/javascript" src="../../js/js_validaciones.js"></script>
<script type="text/javascript" src="../../js/chosen.jquery.js"></script>
<script type="text/javascript" src="../../js/chosen.jquery.min.js"></script>
<script type="text/javascript" src="../../js/chosen.proto.js"></script>
<script type="text/javascript" src="../../js/chosen.proto.min.js"></script>
<script type="text/javascript" src="../../js/ajax_admin_clientes.js"></script>
</body>
</html>