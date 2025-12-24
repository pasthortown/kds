<?php
include_once("datos_promociones.php");

use Maxpoint\Mantenimiento\promociones\Clases\PromocionesController;

$color1 = '#337ab7';
$color2 = '#5cb85c';
$color3 = '#ffc107';
$color4 = '#17a2b8';
$color5 = '#df013a';

$cdn_id = $_SESSION['cadenaId'];

$conexionTienda = $conexionDinamica->conexionTienda();
$promocionesControllerObj = new PromocionesController($conexionTienda);
$cargarCategoria = $promocionesControllerObj->buscarCategoriasCupon($cdn_id);
$categorias = $cargarCategoria['datos'];
$longitud = count($categorias);

$categoriasClases = [
    "USUARIO" => "restriccion-usuario",
    "TIPO VENTA" => "restriccion-tipoventa",
    "NUMERICO" => "restriccion-numericas",
    "TIEMPO" => "resticcion-tiempo",
    "OTRA" => "",
];
?>
<!DOCTYPE html>
<html lang="es">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Nuevo cupón</title>
    <!-- ESTILOS -->
    <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="../../bootstrap/css/switch.css"/>
    <link rel="stylesheet" type="text/css" href="../../bootstrap/templete/css/plugins.css"/>
    <link rel="stylesheet" type="text/css" href="../../bootstrap/css/bootstrap-editable.css"/>

    <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css"/>
    <link rel="stylesheet" type="text/css" href="../../css/alertify.default.css"/>
    <link rel="stylesheet" type="text/css" href="../../css/select2.css">
    <link rel="stylesheet" type="text/css" href="../../css/list.css">
    <link rel="stylesheet" type="text/css" href="../../css/bootstrap-datetimepicker.css"/>
    <link rel="stylesheet" type="text/css" href="../../css/chosen.css"/>
    <style>
        .circulo {
            height: 35px;
            width: 35px;
            display: table-cell;
            text-align: center;
            font-size: 14px;
            color: #FFFFFF;
            vertical-align: middle;
            border-radius: 50%;
            background: #777;
        }
        .rowmb{
            margin-bottom:1em;
        }
    </style>


    </head>
    <body>
<div class="superior">
    <div class="tituloPantalla">
        <h1>NUEVO CUPÓN</h1>
    </div>
</div>

<br/><br/>
<div class="col-md-10">
                    <!-- Nav tabs -->
    <ul class="nav nav-tabs">
        <li role="presentation" class="active">
            <a href="#PromocionGeneral" aria-controls="home" role="tab" data-toggle="tab">
                <h4 class="panel-title">1.- Datos Generales</h4>
            </a>
        </li>
        <li role="presentation">
            <a href="#ProductosRequeridosPromocion" aria-controls="profile" role="tab" data-toggle="tab">
                <h4 class="panel-title">2.- Productos Requeridos</h4>
            </a>
        </li>
        <li role="presentation">
            <a href="#RestaurantesAplicaPromocion" aria-controls="profile" role="tab" data-toggle="tab">
                <h4 class="panel-title">3.- Restaurantes Requeridos</h4>
            </a>
        </li>
        <li role="presentation">
            <a href="#BeneficiosPromocion" aria-controls="profile" role="tab" data-toggle="tab">
                <h4 class="panel-title">4.- Beneficios Promoción</h4>
            </a>
        </li>
                    </ul>
    <!-- Inicio Tab -->
                    <div class="tab-content">
        <br>
        <!-- Inicio Tab promociones -->
                        <div role="tabpanel" class="tab-pane active" id="PromocionGeneral">

            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-md-1"><span class="circulo">1</span></div>
                        <div class="col-md-11"><h4>Datos Generales</h4></div>
                    </div>
                </div>
                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-12">
                            <h4><span class="label label-default"> Datos Vigencia </span></h4>

                            <input inputmode="none"  id="Id_Promociones" value="" type="hidden">
                            <input inputmode="none"  id="cadenaId" value="<?php echo $_SESSION["cadenaId"]; ?>" type="hidden">
                            <input inputmode="none"  id="usuarioId" value="<?php echo $_SESSION['usuarioId']; ?>" type="hidden">
                            <input inputmode="none"  id="totalCategorias" value="<?php echo $longitud; ?>" type="hidden">

                            <div class="form-group">

                                <div class="col-md-3">
                                    <div class="text-left"><h5><font color="#990000">(*)</font> Nombre :</h5></div>
                                    <input inputmode="none"  id="Nombre" value=""
                                           onKeyUp="document.getElementById(this.id).value=document.getElementById(this.id).value.toUpperCase()"
                                           data-toggle="tooltip" title="Nombre General" placeholder="Nombre General"
                                           class="form-control" type="text">
                                </div>

                                <div class="col-md-3">
                                    <div class="text-left"><h5>Nombre Imprimible :</h5></div>
                                    <input inputmode="none"  id="Nombre_imprimible"
                                           onKeyUp="document.getElementById(this.id).value=document.getElementById(this.id).value.toUpperCase()"
                                           value="" data-toggle="tooltip" title="Nombre Imprimible"
                                           placeholder="Nombre Imprimible" class="form-control" type="text">
                                </div>

                                <div class="col-md-3">
                                    <div class="text-left"><h5><font color="#990000">(*)</font> Desde :</h5></div>
                                    <div class='input-group date'>
                                        <input inputmode="none"  type='text' class="form-control" id='Activo_desde'
                                               data-date-format="YYYY-MM-DD" placeholder="Desde" value=""/>
                                        <span class="input-group-addon">
                                             		  <span class="glyphicon glyphicon-calendar"></span>
                                          			</span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="text-left"><h5><font color="#990000">(*)</font> Hasta :</h5></div>
                                    <div class='input-group date'>
                                        <input inputmode="none"  type='text' class="form-control" id='Activo_Hasta' placeholder="Hasta"
                                               data-date-format="YYYY-MM-DD" value=""/>
                                        <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                                <p class="navbar-text"><b><font color="#990000">(*)</font></b> Campos Obligatorios </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- List group -->
                <ul class="list-group">
                    <li class="list-group-item" style="background-color:#FFFFFF">
                        <p>
                        <h4><span class="label label-default"> Tipo de Cupón  </span></h4>
                        <div align="center" id="categoriasRestricciones">
                            <?php
                            $longitud = count($categorias);
                            $seccionesMostrar = [];
                            for ($i = 0; $i < $longitud; $i++) {
                                $idCategoriaActual=$categorias[$i]['ID_ColeccionDeDatosCadena'];
                                $chequeado="";
                                $fjs = str_replace(' ', '', $categorias[$i]['Descripcion']);
                                ?>
                                <div class="btn-group btn-group-lg" role="group">
                                    <h3>
                                        <input inputmode="none"  type="checkbox"
                                               id="<?php echo $aa = $categorias[$i]['ID_ColeccionDeDatosCadena']; ?>"

                                               data-idcategoria="<?php echo $aa ?>" class="checkCategoria"
                                               data-divcategoria="<?php print($categoriasClases[$categorias[$i]['Descripcion']]); ?>"
                                            <?php print($chequeado) ?>
                                        >
                                        <span class="label text-white"
                                              style="background-color:<?php echo "#" . dechex($categorias[$i]['variableI']); ?>">
									            <?php echo $categorias[$i]['variableV']; ?>
									        </span>
                                    </h3>
                                </div>
                            <?php } ?>
                        </div>
                        </p>
                    </li>
                </ul>
                            </div>

            <div class="panel panel-default" id="resticcion-tiempo" style="display:none;">
                <!-- Default panel contents -->
                <div class="panel-heading" style="background-color:<?php echo $color1; ?>">
                    <h4><span class="label text-white">Restricciones de Tiempo</span></h4>
                </div>

                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4><span class="label" style="background-color:<?php echo $color1; ?>"> Dias  </span></h4>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="text-left"><h5>Habilitar / Deshabilitar :
                                            <input inputmode="none"  id="Requiere_dias" onClick="enableRequiereDias();" type="checkbox" class="alert-info"></h5>
                                                    </div>
                                    <table class="table table-bordered table-sm m-0">
                                        <thead class="">
                                        <tr align="center">
                                            <th colspan="2" align="center">Día</th>
                                            <th colspan="2">Día</th>
                                            <th colspan="2">Día</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td align="center">
                                                <label class="custom-control custom-checkbox">
                                                    <input inputmode="none"  type="checkbox" disabled="disabled" id="1" class="custom-control-input">
                                                    <span class="custom-control-indicator"></span>
                                                </label>
                                            </td>
                                            <td>Lunes</td>
                                            <td align="center">
                                                <label class="custom-control custom-checkbox">
                                                    <input inputmode="none"  type="checkbox" disabled="disabled" id="2"
                                                           class="custom-control-input">
                                                    <span class="custom-control-indicator"></span>
                                                </label>
                                            </td>
                                            <td>Martes</td>
                                            <td align="center">
                                                <label class="custom-control custom-checkbox">
                                                    <input inputmode="none"  type="checkbox" disabled="disabled" id="3"
                                                           class="custom-control-input">
                                                    <span class="custom-control-indicator"></span>
                                                </label>
                                            </td>
                                            <td>Miércoles</td>
                                        </tr>
                                        <tr>
                                            <td align="center">
                                                <label class="custom-control custom-checkbox">
                                                    <input inputmode="none"  type="checkbox" disabled="disabled" id="4"
                                                           class="custom-control-input">
                                                    <span class="custom-control-indicator"></span>
                                                </label>
                                            </td>
                                            <td>Jueves</td>
                                            <td align="center">
                                                <label class="custom-control custom-checkbox">
                                                    <input inputmode="none"  type="checkbox" id="5" disabled="disabled"
                                                           class="custom-control-input">
                                                    <span class="custom-control-indicator"></span>
                                                </label>
                                            </td>
                                            <td>Viernes</td>
                                            <td align="center">
                                                <label class="custom-control custom-checkbox">
                                                    <input inputmode="none"  type="checkbox" disabled="disabled" id="6"
                                                           class="custom-control-input">
                                                    <span class="custom-control-indicator"></span>
                                                </label>
                                            </td>
                                            <td>Sábado</td>
                                        </tr>
                                        <tr>
                                            <td align="center">
                                                <label class="custom-control custom-checkbox">
                                                    <input inputmode="none"  type="checkbox" disabled="disabled" id="7"
                                                           class="custom-control-input">
                                                    <span class="custom-control-indicator"></span>
                                                </label>
                                            </td>
                                            <td colspan="5">Domingo</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                                </div>
                                            </div>
                                        </div>
                        <div class="col-md-6">
                            <h4><span class="label" style="background-color:<?php echo $color1; ?>"> Horarios  </span></h4>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="text-left">
                                        <h5>Habilitar / Deshabilitar :
                                            <input inputmode="none"  id="Requiere_horario" onclick="enableRequiereHorario();"
                                                   type="checkbox" class="alert-info">
                                        </h5>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-left"><h5>Desde :</h5></div>
                                        <input inputmode="none"  class="form-control horario" id="horarioDesde" disabled="disabled"
                                               placeholder="Desde">

                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-left"><h5>Hasta :</h5></div>
                                        <input inputmode="none"  class="form-control horario" id="horarioHasta" disabled="disabled"
                                               placeholder="Hasta">
                                                </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <br>
                    <!--
					    <div class="col-md-12">
								<h4><span class="label" style="background-color:<?php //echo $color1;?>"> Vigencia </span></h4>
								<div class="form-group">
									<div class="col-md-4">
										<div class="text-left"><h5>Habilitar / Deshabilitar :
										   <input inputmode="none"  id="Caduca_con_tiempo" onClick="enableRequiereTiempo();" type="checkbox" class="alert-info">	</h5>						
								    	</div>
									</div>

									<div class="col-md-4">
										<div class="text-left"><h5>Unidad de Tiempo :</h5></div>
										<div class="input-group mb-3">
												  <select
													class="form-control" id="Unidad_Tiempo_validez" disabled="disabled">
													<option value="0">Escoja la Unidad de Tiempo</option>
													<option value="horas">horas</option>
													<option value="dias">dias</option>
													<option value="meses">meses</option>
												  </select>
												</div>
									</div>
									<div class="col-md-4">
										<div class="text-left"><h5>Tiempo :</h5></div>
										<input inputmode="none"  id="Tiempo_validez" class="form-control" disabled="disabled" type="text" placeholder="Tiempo" value="" onKeyPress="return justNumbers(event);">
									</div>
							  </div>
						</div>	-->
                </div>
                <br>
            </div>


            <div class="panel panel-default" id="restriccion-numericas" style="display:none;">
                <!-- Default panel contents -->
                <div class="panel-heading" style="background-color:<?php echo $color2; ?>">
                    <h4><span class="label text-white">Restricciones Numéricas</span></h4>
                </div>
                <div class="panel-body">
                    <div class="col-md-12">
                        <h4><span class="label label-success"> Numéricas </span></h4>
                        <div class="form-group">
                            <div class="col-md-6">
                                <div class="text-left"><h5>Límite Canjes Total :</h5></div>
                                <input inputmode="none"  class="form-control horario" id="Limite_canjes_total" value=""
                                       placeholder="Límite Canjes Total" onKeyPress="return justNumbers(event);">
                            </div>
                            <div class="col-md-6">
                                <div class="text-left"><h5>Límite Canjes Cliente :</h5></div>
                                <input inputmode="none"  class="form-control horario" id="Limite_canjes_cliente" value=""
                                       placeholder="Límite Canjes Cliente" onKeyPress="return justNumbers(event);">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6">
                                <div class="text-left"><h5>Bruto Minimo Factura :</h5></div>
                                <input inputmode="none"  class="form-control horario" id="Bruto_minimo_factura" value=""
                                       placeholder="Bruto Minimo Factura" onKeyPress="return justNumbers(event);">
                            </div>
                            <div class="col-md-6">
                                <div class="text-left"><h5>Bruto Maximo Factura:</h5></div>
                                <input inputmode="none"  class="form-control horario" id="Bruto_maximo_factura" value=""
                                       placeholder="Bruto Maximo Factura" onKeyPress="return justNumbers(event);">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6">
                                <div class="text-left"><h5>Cantidad Minima Productos Factura :</h5></div>
                                <input inputmode="none"  class="form-control horario" id="Cantidad_minima_productos_factura" value=""
                                       placeholder="Cantidad Minima Productos Factura"
                                       onKeyPress="return justNumbers(event);">
                            </div>
                            <div class="col-md-6">
                                <div class="text-left"><h5>Maximo Canje Multiple:</h5></div>
                                <input inputmode="none"  class="form-control horario" id="Maximo_canje_multiple" value=""
                                       placeholder="Maximo Canje Multiple" onKeyPress="return justNumbers(event);">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="panel panel-default" id="restriccion-usuario" style="display:none;">
                <!-- Default panel contents -->
                <div class="panel-heading" style="background-color:<?php echo $color3; ?>">
                    <h4><span class="label text-white">Restricciones Usuario</span></h4>
                </div>
                <div class="panel-body">
                    <div class="col-md-12">
                        <h4><span class="label" style="background-color:<?php echo $color3; ?>"> Edad Usuarios </span></h4>
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="text-left"><h5>Habilitar / Deshabilitar :
                                            <input inputmode="none"  id="Requiere_rango_edad" onClick="enableRequiereRangoEdad();"
                                                   type="checkbox" class="alert-info"></h5>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="text-left"><h5>Desde :</h5></div>
                                        <input inputmode="none"  disabled="disabled" class="form-control horario" id="edadDesde"
                                               placeholder="Edad Desde" onKeyPress="return justNumbers(event);"
                                               onKeyUp="sumaEdad1(this);">
                                    </div>

                                    <div class="col-md-6">
                                        <div class="text-left"><h5>Hasta :</h5></div>
                                        <input inputmode="none"  disabled="disabled" class="form-control horario" id="edadHasta"
                                               placeholder="Edad Hasta" onKeyPress="return justNumbers(event);">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>

            <div class="panel panel-default" id="restriccion-tipoventa" style="display:none;">
                <!-- Default panel contents -->
                <div class="panel-heading" style="background-color:<?php echo $color4; ?>">
                    <h4><span class="label text-white">Restricciones Tipo Venta</span></h4>
                </div>
                <div class="panel-body">
                    <div class="col-md-12">
                        <h4><span class="label" style="background-color:<?php echo $color4; ?>"> Canal </span></h4>
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <div class="text-left"><h5>Habilitar / Deshabilitar :
                                            <input inputmode="none"  id="Requiere_canal" onClick="enableRequiereCanal();" type="checkbox"
                                                   class="alert-info"></h5>
                                                    </div>
                                    <div class="form-group">
                                        <label for="exampleFormControlSelect2">Seleccione el Canal</label>
                                        <select id="idsCanal" multiple class="form-control" disabled="disabled"
                                                name="states[]">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Restriccion otro tipo -->
            <div class="panel panel-default" id="restriccion-numericas" style="display:block;" >
                <div class="panel-heading" style="background-color:<?php echo $color5; ?>">
                    <h4><span class="label text-white">Restricciones Otro Tipo</span></h4>
                </div>
                <div class="panel-body">
                    <div class="col-md-12">
                        <h4><span class="label label-danger"> Otro tipo </span></h4>
                        <div class="form-group">
                            <div class="row rowmb">
                                <label class="col-sm-5 form-label" for="promoSobrePromo">
                                    Permitir promoción sobre promoción:
                                </label>
                                <div class="col-sm-offset-1 col-sm-6">
                                    <input inputmode="none"  class="switch-bts" name="promoSobrePromo" type="checkbox" data-off-text="NO" data-on-text="SI" id="promoSobrePromo" />
                                </div>
                            </div>
                            <div class="row rowmb">
                                <label class="col-sm-5 form-label" for="descSobreDesc">
                                    Permitir descuento sobre descuento:
                                </label>
                                <div class="col-sm-offset-1 col-sm-6">
                                    <input inputmode="none"  class="switch-bts" name="descSobreDesc" type="checkbox" data-off-text="NO" data-on-text="SI" id="descSobreDesc" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
            <!-- Fin restriccion otro tipo -->
        </div>

        <!-- Fin Tab promociones -->
        <!-- Inicio Tab Beneficios -->

        <div role="tabpanel" class="tab-pane" id="BeneficiosPromocion">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-md-1"><span class="circulo">4</span></div>
                        <div class="col-md-11"><h4>Beneficios</h4></div>
                    </div>
                </div>
                <div class="panel-body">
                    <pre class="control-label" style="background-color:#F5F5F5"><h5><span
                                    class="glyphicon glyphicon-tag" aria-hidden="true"></span> Ingrese al menos 1 (un) beneficio. </h5></pre>
                    <div class="panel panel-default">
                        <div class="panel-body">


                            <div class="row">
                                <!--<div class="col-md-12">
                                       <h4><span class="label label-default"> Fidelización</span></h4>
                                       <div class="form-group">
                                           <div class="col-md-6">
                                               <div class="text-left"><h5>Puntos Acumulables :</h5></div>
                                                         <input inputmode="none"  class="form-control horario" id="Puntos_Acumulables" value=""
                                                              placeholder="Puntos Acumulables" onKeyPress="return justNumbers(event);">
                                           </div>
                                           <div class="col-md-6">
                                               <div class="text-left"><h5>Saldo Acumulable :</h5></div>
                                                       <input inputmode="none"  class="form-control horario" id="Saldo_Acumulable" value=""
                                                              placeholder="Saldo Acumulable" onKeyPress="return justNumbers(event);">
                                           </div>
                                       </div>
                                     </div>
                                -->
                                <div class="col-md-12">
                                    <h4><span class="label label-default"> Tipo Beneficio</span></h4>
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <div class="form-check">
                                                <input inputmode="none"  class="form-check-input" type="radio" name="exampleRadios"
                                                       id="anadirProductos" value="option1" onClick="verificarCombo();">
                                                <label class="form-check-label" for="exampleRadios1">
                                                    Añadir Productos
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-offset-1 col-sm-11" id="verProductos" style="display:none;">
                                            <div class="row rowmb">
                                                <label class="col-sm-3 form-label" for="tipofacturacion">
                                                    Forma facturación:
                                                </label>
                                                <div class="col-sm-offset-1 col-sm-8">
                                                    <input inputmode="none"  class="switch-bts" name="tipofacturacion" type="checkbox" checked data-off-text="Autoconsumo" data-on-text="Nuevo PLU" id="tipofacturacion" />
                                                </div>
                                            </div>
                                            <div class="row rowmb">
                                                <label class="col-sm-3" for="cantidadProductoBeneficio">PLU: </label>
                                                <div class="col-sm-offset-1  col-sm-8">
                                                    <select id="select-plus-cadena">
                                                        <option value="0">---- Seleccione un producto</option>
                                                        <?php foreach ($productosCadena as $productoCadena) {
                                                            $descripcionPLU = $productoCadena["plu_descripcion"];
                                                            ?>
                                                            <option value=<?php print($productoCadena["plu_id"]) ?> data-plu_num_plu="<?php print($productoCadena["plu_num_plu"]); ?>"
                                                                    data-plu_descripcion="<?php print($descripcionPLU); ?>"
                                                                    data-plu_id="<?php print($productoCadena["plu_id"]); ?>">
                                                                <?php print($productoCadena["plu_num_plu"] . ' | ' . $descripcionPLU) ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row rowmb">
                                                <div class="form-group form-inline">
                                                    <label class="col-sm-3" for="cantidadProductoBeneficio">Cantidad: </label>
                                                    <div class="col-sm-offset-1 col-sm-8">
                                                    <input inputmode="none"  class="form-control" id="cantidadProductoBeneficio"
                                                           onKeyPress="return justNumbers(event);"
                                                           onKeyUp="sumaEdad(this);" placeholder="Cantidad"
                                                           type="text">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row rowmb">
                                                <div class="col-sm-6">
                                                    <button type="button"
                                                            class="btn btn-success btn-agregar-plu">Agregar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-check">
                                                <input inputmode="none"  class="form-check-input" type="radio" name="exampleRadios"
                                                       id="anadirDescuentos" value="option2"
                                                       onClick="verificarCombo();">
                                                <label class="form-check-label" for="exampleRadios2">
                                                    Añadir Descuentos
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                   <div class="form-group">
                                        <div class="col-md-12">
                                            <!-- Productos/Categorias -->
                                            <div role="tabpanel" class="tab-pane" id="procat">
                                                <div id="dsct_apld_id">
                                                    <div class="row">
                                                        <div class="col-sm-offset-1 col-sm-8" id="verDescuentos" style="display:none;">
                                                            <div class="row rowmb" >
                                                                <div class="col-sm-12">
                                                                    <select id="select-plus-descuento">
                                                                        <option value="0">---- Seleccione un descuento</option>
                                                                        <?php foreach ($descuentos as $descuentos) {
                                                                            $descuentoPLU = $descuentos["dsct_descripcion"];
                                                                            ?>
                                                                            <option value=<?php print($descuentos["dsct_id"]) ?> data-plu_num_plu="<?php print($descuentos["dsct_valor"]); ?>"
                                                                                    data-plu_descripcion="<?php print($descuentoPLU); ?>"
                                                                                    data-plu_id="<?php print($descuentos["dsct_id"]); ?>">
                                                                                <?php print($descuentos["dsct_valor"] . ' | ' . $descuentoPLU) ?>
                                                                            </option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="row rowmb">
                                                                <div class="col-sm-6">
                                                                    <button type="button" class="btn btn-success btn-agregar-plu">
                                                                        Agregar
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <h3>Beneficios: </h3>
                                    <hr class="rowmb">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <ul id="listado-plus-agregardos-descuento" class="list-group"></ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
            </div>
        </div>

        <!-- FIN TAB Beneficios -->

        <!-- Inicio Tab Productos Requeridos -->

        <div role="tabpanel" class="tab-pane" id="ProductosRequeridosPromocion">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-md-1"><span class="circulo">2</span></div>
                        <div class="col-md-11"><h4>Productos</h4></div>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">

                            <pre class="control-label" style="background-color:#F5F5F5"><h5><span
                                            class="glyphicon glyphicon-ok" aria-hidden="true"></span> Habilitar / <span
                                            class="glyphicon glyphicon-remove" aria-hidden="true"></span> Deshabilitar : <input
                                            id="Requiere_productos" onClick="enableRequiereProductos()" type="checkbox">	 </h5></pre>


                            <div class="panel panel-default" id="panel-productos-requeridos" style="display:none">
                                <div class="panel-body">

                                    <!-- Inicia PRODUCTOS REQUERIDOS -->
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <h4><span class="label label-default"> Productos </span></h4>
                                            <div class="form-group">
                                                <div class="col-md-12">
                                                    <!-- Productos/Categorias -->
                                                    <div role="tabpanel" class="tab-pane" id="procat"
                                                         style="min-height: 80px">
                                                        <div id="dsct_apld_id">
                                                            <div class="row">
                                                                <div class="col-sm-8">
                                                                    <select id="IdsProductosRequeridosPromocion"
                                                                            class="form-control">
                                                                        <option value="0">---- Seleccione un producto
                                                                        </option>
                                                                        <?php foreach ($productosCadena as $productoCadena) {
                                                                            $descripcionPLU = $productoCadena["plu_descripcion"];
                                                                            ?>
                                                                            <option value=<?php print($productoCadena["plu_id"]) ?> data-plu_num_plu="<?php print($productoCadena["plu_num_plu"]); ?>"
                                                                                    data-plu_descripcion="<?php print($descripcionPLU); ?>"
                                                                                    data-plu_id="<?php print($productoCadena["plu_id"]); ?>">
                                                                                <?php print($productoCadena["plu_num_plu"] . ' | ' . $descripcionPLU) ?>
                                                                            </option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-sm-2">
                                                                    <input inputmode="none"  type="text" class="form-control"
                                                                           id="cantidadProductoRequerido"
                                                                           onKeyPress="return justNumbers(event);"
                                                                           onKeyUp="sumaEdad(this);"
                                                                           placeholder="Cantidad">
                                                                </div>
                                                                <div class="col-sm-2">
                                                                    <button type="button"
                                                                            class="btn btn-sm btn-success btn-block"
                                                                            id="btn-agregar-plu-requerido"
                                                                            onClick="btn_agregar_producto_requerido();">
                                                                        Agregar
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <ul id="listado-plus-agregardos-requeridos" class="list-group"></ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- fIN PRODUCTOS REQUERIDOS -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FIN TAB Productos Requeridos -->
        <!-- Inicio Tab Restaurantes Productos Requeridos -->


        <div role="tabpanel" class="tab-pane" id="RestaurantesAplicaPromocion">
            <div class="panel panel-default">
                <div class="panel-heading">

                    <div class="row">
                        <div class="col-md-1"><span class="circulo">3</span></div>
                        <div class="col-md-11"><h4>Restaurantes</h4></div>

                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">

                            <pre class="control-label" style="background-color:#F5F5F5">
                                <h5><span
                                            class="glyphicon glyphicon-ok" aria-hidden="true"></span> Habilitar / <span
                                            class="glyphicon glyphicon-remove" aria-hidden="true"></span> Deshabilitar : <input
                                            id="Requiere_restaurante" onClick="enableRequiereRestaurantes()"
                                            type="checkbox">	 </h5>
                            </pre>


                            <div class="panel panel-default" id="panel-restaurantes-requeridos" style="display:none">
                                <div class="panel-body">

                                    <div role="tabpanel" class="tab-pane">


                                        <div class="row"><br>
                                            <div class="col-md-12">
                                                <h4><span class="label label-default"> Restaurantes </span></h4>

                                                <div class="form-group">


                                                    <hr>
                                                    <div class="col-md-6">
                                                        <div class="group-result" align="center">
                                                            <div id="btnAgregarTodosRestaurantes"
                                                                 class="btn btn-success"
                                                                 onClick="verTodosRestaurantes();">Ver Todos &gt;&gt;
                                                            </div>
                                                            <div id="btnAgregarTodosRestaurantes" class="btn btn-info "
                                                                 onClick="verProvinciasRestaurantes();">Ver Region &gt;&gt;
                                                            </div>
                                                            <div id="btnAgregarTodosRestaurantes"
                                                                 class="btn btn-primary "
                                                                 onClick="verRegionesRestaurantes();">Ver Ciudades &gt;&gt;
                                                            </div>
                                                        </div>
                                                        <hr>

                                                        <div id="RestaurantesTodos" class="col-lg-12" style="display:block;">
                                                            <!-- Busqueda -->
                                                            <div class="control-group">
                                                                <div class="controls">
                                                                    <div class="input-prepend input-group">
                                                                        <span class="add-on input-group-addon"
                                                                              id="icono_buscar_restaurantes"><i
                                                                                    class="glyphicon glyphicon-search fa fa-calendar"></i></span>
                                                                        <input inputmode="none"  id="buscar_Restaurantes" type="text"
                                                                               class="form-control" placeholder="Buscar"
                                                                               aria-describedby="sizing-addon1">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div id="contenedor_lst_rest_no_activos">
                                                                <ul id="lst_rst_id" class="list-group">
                                                                </ul>
                                                            </div>
                                                        </div>


                                                        <div id="RestaurantesCiudad" class="col-lg-12"
                                                             style="display:none;">
                                                            <!-- Busqueda -->
                                                            <div class="control-group">
                                                                <div class="controls">
                                                                    <div class="input-prepend input-group">
                                                                        <span class="add-on input-group-addon"
                                                                              id="icono_buscar_restaurantes"><i
                                                                                    class="glyphicon glyphicon-search fa fa-calendar"></i></span>
                                                                        <input inputmode="none"  id="buscar_Ciudad" type="text"
                                                                               class="form-control" placeholder="Buscar"
                                                                               aria-describedby="sizing-addon1">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div id="contenedor_lst_rest_no_activos">
                                                                <ul id="lst_rstCiu_id" class="list-group">
                                                                </ul>
                                                            </div>
                                                        </div>


                                                        <div id="RestaurantesProvincia" class="col-lg-12"
                                                             style="display:none;">
                                                            <!-- Busqueda -->
                                                            <div class="control-group">
                                                                <div class="controls">
                                                                    <div class="input-prepend input-group">
                                                                        <span class="add-on input-group-addon"
                                                                              id="icono_buscar_restaurantes"><i
                                                                                    class="glyphicon glyphicon-search fa fa-calendar"></i></span>
                                                                        <input inputmode="none"  id="buscar_Region" type="text"
                                                                               class="form-control" placeholder="Buscar"
                                                                               aria-describedby="sizing-addon1">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div id="contenedor_lst_rest_no_activos">
                                                                <ul id="lst_rstRegion_id" class="list-group">

                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div id="btnQuitarTodosRestaurantes"
                                                             class="btn btn-warning btn-block">&lt;&lt; Quitar Todos
                                                        </div>
                                                        <div id="btnQuitarTodosRestaurantesCiudades"
                                                             class="btn btn-info btn-block" style="display:none">
                                                            Restaurantes por Región
                                                        </div>
                                                        <div id="btnQuitarTodosRestaurantesRegiones"
                                                             class="btn btn-primary btn-block" style="display:none">
                                                            Restaurantes por Ciudad
                                                        </div>
                                                        <hr>
                                                        <div id="contenedorRestaurante">
                                                            <div id="contenedor_lst_rest_activos">
                                                                <div id="lst_rst_dscto" class="list-group">

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <div class="col-sm-12">
                                                        <ul id="listado-restaurantes-agregardos-requeridos"
                                                            class="list-group"></ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- copia -->
    <!-- fin copia -->
    <!-- FIN TAB Restaurantes Productos Requeridos -->
    <!-- Inicio Tab -->
    <button type="button" class="btn btn-primary" onClick="verificarDatos();"> Crear Promoción</button>
    <button type="button" class="btn btn-primary" id="back" onClick="verPromociones();"> Listar Promociones</button>

</div>

<div id="mdl_rdn_pdd_crgnd" class="modal_cargando" style="display:none">
    <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
        <img src="../../imagenes/admin_resources/progressBar.gif"/>
    </div>
</div>

<script type="text/javascript" src="../../js/jquery1.11.1.js"></script>
<script type="text/javascript" src="../../js/ajax_datatables.js"></script>
<script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
<script type="text/javascript" src="../../bootstrap/js/moment.js"></script>

<script type="text/javascript" src="../../bootstrap/js/query.bootstrap-growl.min.js"></script>
<script type="text/javascript" src="../../js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="../../bootstrap/js/switch.js"></script>
<script type="text/javascript" src="../../js/chosen.jquery.js"></script>
<script type="text/javascript" src="../../js/ajax_Crearpromociones.js"></script>

    </body>
</html>