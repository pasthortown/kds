<?php
include_once("datos_promociones.php");
use Maxpoint\Mantenimiento\promociones\Clases\PromocionesController;
use Carbon\Carbon;
if (
    empty($_SESSION['rstId'])
    OR empty($_SESSION['usuarioId'])
    OR empty($_SESSION['cadenaId'])
) {
    die(json_encode((object)[
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}
$requestGET = (object)(array_map('utf8_decode', $_GET));

$conexionTienda=$conexionDinamica->conexionTienda();
$promocionesControllerObj = new PromocionesController($conexionTienda);
$idPromocion   = $requestGET->idPromocion;
$cargarPromocion=$promocionesControllerObj->buscarPromocionPorID($idPromocion);
$promocion = $cargarPromocion['datos'][0];



?>
 <!-- ESTILOS -->
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="../../bootstrap/css/switch.css"/>
    <link rel="stylesheet" href="../../bootstrap/templete/css/plugins.css"/>
    <link rel="stylesheet" href="../../css/chosen.css"/>
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap-editable.css"/>
	  <link rel="stylesheet" type="text/css" href="../../css/est_administracionPantalla.css" />
    <link rel="stylesheet" href="../../css/alertify.default.css"/>
    <link rel="stylesheet" href="../../css/select2.css">
    <link rel="stylesheet" href="../../css/list.css">
    <link rel="stylesheet" href="../../css/bootstrap-datetimepicker.css"/>

<div class="superior">
    <div class="tituloPantalla">
        <h1>EDITAR PROMOCION</h1>
    </div>
</div>



<br /><br />
<div class="col-md-10">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs">
<li role="presentation" class="active"><a href="#PromocionGeneral" aria-controls="home" role="tab" data-toggle="tab"><h4 class="panel-title">Crear Promoción</h4></a></li>
<li role="presentation"><a href="#BeneficiosPromocion" aria-controls="profile" role="tab" data-toggle="tab"><h4 class="panel-title">Beneficios Promoción</h4></a></li>
<li role="presentation"><a href="#ProductosRequeridosPromocion" aria-controls="profile" role="tab" data-toggle="tab"><h4 class="panel-title">Productos Requeridos</h4></a></li>
<li role="presentation"><a href="#RestaurantesAplicaPromocion" aria-controls="profile" role="tab" data-toggle="tab"><h4 class="panel-title">Restaurantes Requeridos</h4></a></li>
            </ul>
         
		     <!-- Inicio Tab -->
            <div class="tab-content">
			    
				
				<!-- Inicio Tab promociones -->
                <div role="tabpanel" class="tab-pane active" id="PromocionGeneral">
                    
					<div class="row">
                        <div class="col-md-12"><br>
								<span class="label label-success"> Datos Generales </span>
							
								<input inputmode="none"  id="Id_Promociones" value="<?php echo $idPromocion;?>" type="hidden">
								<input inputmode="none"  id="cadenaId" value="<?php echo $_SESSION["cadenaId"]; ?>" type="hidden">
								<input inputmode="none"  id="usuarioId" value="<?php echo $_SESSION['usuarioId']; ?>" type="hidden">
								
								<div class="form-group">
									
									<div class="col-md-3">
										<div class="text-left"><h5>Nombre :</h5></div>
										<input inputmode="none"  id="Nombre" value="<?php echo $promocion['Nombre'];?>" class="form-control" type="text">
									</div>
									
									<div class="col-md-3">
										<div class="text-left"><h5>Nombre Imprimible :</h5></div>
										<input inputmode="none"  id="Nombre_imprimible" value="<?php echo $promocion['Nombre_imprimible']; ?>"  class="form-control" type="text">
									</div>
									
									<div class="col-md-3">
										<div class="text-left"><h5>Código Externo :</h5></div>
										<input inputmode="none"  id="Codigo_externo" value="<?php echo $promocion['Codigo_externo'];  ?>" class="form-control" type="text">
									</div>
								
									<div class="col-md-3">
										<div class="text-left"><h5>Código Amigable :</h5></div>
										<input inputmode="none"  id="Codigo_amigable" value="<?php echo $promocion['Codigo_amigable'];  ?>" class="form-control" type="text">
									</div>
							  </div>
						</div>	
                    </div>
					<hr>             
					
					
					
                    <div class="row">
                        <div class="col-md-6">
									<span class="label label-success"> Fechas Validez </span>
								
								<div class="form-group">
									
									<div class="col-md-6">
										<div class="text-left"><h5>Desde :</h5></div>
										 		<div class='input-group date' >
                                                    <input inputmode="none"  type='text' class="form-control" id='Activo_desde'  data-date-format="YYYY-MM-DD" placeholder="Desde" value="<?php echo $promocion['Activo_desde'];  ?>"/>
                                                    <span class="input-group-addon">
                                             		  <span class="glyphicon glyphicon-calendar"></span>
                                          			</span>
                                                </div>
									</div>
									
									<div class="col-md-6">
										<div class="text-left"><h5>Hasta :</h5></div>
												<div class='input-group date' >
                                                    <input inputmode="none"  type='text' class="form-control" id='Activo_Hasta' placeholder="Hasta" data-date-format="YYYY-MM-DD" value="<?php echo $promocion['Activo_Hasta'];  ?>"/>
                                                    <span class="input-group-addon">
                                             		  <span class="glyphicon glyphicon-calendar"></span>
                                          			</span>
                                                </div>
									</div>
								</div>
							
                        </div>
						
						 <div class="col-md-6">
								<span class="label label-success"> Restricción Horarios  </span>
								
								<div class="form-group">
									<div class="col-md-12">
										<div class="text-left">
												<h5>Habilitar / Deshabilitar :
											   <input inputmode="none"  id="Requiere_horario" onclick="enableRequiereHorario();" <?php if($promocion['Requiere_horario']==1){ echo "checked='checked'";  } ?> type="checkbox"  class="alert-info">
											   </h5>						
										 </div>
									
									<div class="col-md-6">
										<div class="text-left"><h5>Desde :</h5></div><?php   //var_dump($promocion['Horario_canjeable']); ?>
										 		 <input inputmode="none"  class="form-control horario" id="horarioDesde"  <?php if($promocion['Requiere_horario']==0){ echo "disabled='disabled'";  } ?> value="<?php 

			$horarioDesde = $promocion['Horario_canjeable'].substr(1, 5);
			echo $horarioDesde;
			
			?>"
                                                       placeholder="Desde">
								
									</div>
									<div class="col-md-6">
									
										<div class="text-left"><h5>Hasta :</h5></div>
												<input inputmode="none"  class="form-control horario" id="horarioHasta" <?php if($promocion['Requiere_horario']==0){ echo "disabled='disabled'";  } ?> value="<?php 
			$horarioHasta = $promocion['Horario_canjeable'].substr(7, 5);
			echo $horarioHasta;
			?>"
                                                       placeholder="Hasta">
										</div>
								</div>
							
                        </div>
                    </div>
					</div>
					
					<hr>   
					
					
					
					    <div class="row">
                        <div class="col-md-6">
								<span class="label label-success"> Restricción Dias  </span>								
								
								<div class="form-group">
									
									<div class="col-md-12">
									<div class="text-left"><h5>Habilitar / Deshabilitar :
										   <input inputmode="none"  id="Requiere_dias" onclick="enableRequiereDias();" <?php if($promocion['Requiere_dias']==1){ echo "checked='checked'";  } ?> type="checkbox" class="alert-info">	</h5>						
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
                                    <input inputmode="none"  type="checkbox" 
									<?php 
										  if($promocion['Requiere_dias']==0)
										  { 
										  echo "disabled='disabled'"; 
										  } 
										  
										  if(strpos($promocion['Dias_canjeable'],'1') !== false)
										  {
										  echo "checked='checked'";  
										  }
										  
										  
									?> id="1" class="custom-control-input">
                                    <span class="custom-control-indicator"></span>
                                </label>
                            </td>
                            <td>Lunes</td>
							<td align="center">
                                <label class="custom-control custom-checkbox">
                                    <input inputmode="none"  type="checkbox" <?php 
										  if($promocion['Requiere_dias']==0)
										  { 
										  echo "disabled='disabled'"; 
										  } 
										  
										  if(strpos($promocion['Dias_canjeable'],'2') !== false)
										  {
										  echo "checked='checked'";  
										  }
										  
										  
									?> id="2" class="custom-control-input">
                                    <span class="custom-control-indicator"></span>
                                </label>
                            </td>
                            <td>Martes</td>
							<td align="center">
                                <label class="custom-control custom-checkbox">
                                    <input inputmode="none"  type="checkbox" <?php 
										  if($promocion['Requiere_dias']==0)
										  { 
										  echo "disabled='disabled'"; 
										  } 
										  
										  if(strpos($promocion['Dias_canjeable'],'3') !== false)
										  {
										  echo "checked='checked'";  
										  }
										  
										  
									?> id="3" class="custom-control-input">
                                    <span class="custom-control-indicator"></span>
                                </label>
                            </td>
                            <td>Miércoles</td>
							
                        </tr>
                        <tr>
                            <td align="center">
                                <label class="custom-control custom-checkbox">
                                    <input inputmode="none"  type="checkbox" <?php 
										  if($promocion['Requiere_dias']==0)
										  { 
										  echo "disabled='disabled'"; 
										  } 
										  
										  if(strpos($promocion['Dias_canjeable'],'4') !== false)
										  {
										  echo "checked='checked'";  
										  }
										  
										  
									?> id="4" class="custom-control-input">
                                    <span class="custom-control-indicator"></span>
                                </label>
                            </td>
                            <td>Jueves</td>
							<td align="center">
                                <label class="custom-control custom-checkbox">
                                    <input inputmode="none"  type="checkbox" <?php 
										  if($promocion['Requiere_dias']==0)
										  { 
										  echo "disabled='disabled'"; 
										  } 
										  
										  if(strpos($promocion['Dias_canjeable'],'5') !== false)
										  {
										  echo "checked='checked'";  
										  }
										  
										  
									?> id="5" class="custom-control-input">
                                    <span class="custom-control-indicator"></span>
                                </label>
                            </td>
                            <td>Viernes</td>
							<td align="center">
                                <label class="custom-control custom-checkbox">
                                    <input inputmode="none"  type="checkbox" <?php 
										  if($promocion['Requiere_dias']==0)
										  { 
										  echo "disabled='disabled'"; 
										  } 
										  
										  if(strpos($promocion['Dias_canjeable'],'6') !== false)
										  {
										  echo "checked='checked'";  
										  }
										  
										  
									?> id="6" class="custom-control-input">
                                    <span class="custom-control-indicator"></span>
                                </label>
                            </td>
                            <td>Sábado</td>
                        </tr>
                        <tr>
							<td align="center">
                                <label class="custom-control custom-checkbox">
                                    <input inputmode="none"  type="checkbox" <?php 
										  if($promocion['Requiere_dias']==0)
										  { 
										  echo "disabled='disabled'"; 
										  } 
										  
										  if(strpos($promocion['Dias_canjeable'],'7') !== false)
										  {
										  echo "checked='checked'";  
										  }
										  
										  
									?> id="7" class="custom-control-input">
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
								<span class="label label-success"> Restricciones Numéricas </span>
								
								<div class="form-group">
									
									<div class="col-md-6">
										<div class="text-left"><h5>Límite Canjes Total :</h5></div>
										 		 <input inputmode="none"  class="form-control horario" id="Limite_canjes_total" value="<?php echo $promocion['Limite_canjes_total'];  ?>"
                                                       placeholder="Límite Canjes Total" onKeyPress="return justNumbers(event);">
									</div>
									
									<div class="col-md-6">
										<div class="text-left"><h5>Límite Canjes Cliente :</h5></div>
												<input inputmode="none"  class="form-control horario" id="Limite_canjes_cliente" value="<?php echo $promocion['Limite_canjes_cliente'];  ?>"
                                                       placeholder="Límite Canjes Cliente" onKeyPress="return justNumbers(event);">
									</div>
								</div>
								
								
								<div class="form-group">
									
									<div class="col-md-6">
										<div class="text-left"><h5>Puntos Acumulables :</h5></div>
										 		 <input inputmode="none"  class="form-control horario" id="Puntos_Acumulables" value="<?php echo $promocion['Puntos_Acumulables'];  ?>"
                                                       placeholder="Puntos Acumulables" onKeyPress="return justNumbers(event);">
									</div>
									
									<div class="col-md-6">
										<div class="text-left"><h5>Saldo Acumulable :</h5></div>
												<input inputmode="none"  class="form-control horario" id="Saldo_Acumulable" value="<?php echo $promocion['Saldo_Acumulable'];  ?>"
                                                       placeholder="Saldo Acumulable" onKeyPress="return justNumbers(event);">
									</div>
								</div>
								
								<div class="form-group">
									
									<div class="col-md-6">
										<div class="text-left"><h5>Bruto Minimo Factura :</h5></div>
										 		 <input inputmode="none"  class="form-control horario" id="Bruto_minimo_factura" value="<?php echo $promocion['Bruto_minimo_factura'];  ?>"
                                                       placeholder="Bruto Minimo Factura" onKeyPress="return justNumbers(event);">
									</div>
									
									<div class="col-md-6">
										<div class="text-left"><h5>Bruto Maximo Factura:</h5></div>
												<input inputmode="none"  class="form-control horario" id="horarioHasta" value="<?php echo $promocion['Bruto_maximo_factura'];  ?>"
                                                       placeholder="Bruto Maximo Factura" onKeyPress="return justNumbers(event);">
									</div>
								</div>
								
								<div class="form-group">
									
									<div class="col-md-6">
										<div class="text-left"><h5>Cantidad Minima Productos Factura :</h5></div>
										 		 <input inputmode="none"  class="form-control horario" id="Cantidad_minima_productos_factura" value="<?php echo $promocion['Cantidad_minima_productos_factura'];  ?>"
                                                       placeholder="Cantidad Minima Productos Factura" onKeyPress="return justNumbers(event);">
									</div>
									
									<div class="col-md-6">
										<div class="text-left"><h5>Maximo Canje Multiple:</h5></div>
												<input inputmode="none"  class="form-control horario" id="Maximo_canje_multiple" value="<?php echo $promocion['Maximo_canje_multiple'];  ?>"
                                                       placeholder="Maximo Canje Multiple" onKeyPress="return justNumbers(event);">
									</div>
								</div>
							
                        </div>
                    </div>
					<hr>   
					
					<div class="row">
                        <div class="col-md-12">

								<span class="label label-success"> Restricción de Tiempo </span>
								
								<div class="form-group">
									
									<div class="col-md-4">
										<div class="text-left"><h5>Habilitar / Deshabilitar :
										   <input inputmode="none"  id="Caduca_con_tiempo" onclick="enableRequiereTiempo();" type="checkbox" <?php 
										  if($promocion['Caduca_con_tiempo']==1)
										  { 
										  echo "checked='checked'"; 
										  } 
										  
									?> class="alert-info">	</h5>						
								    	</div>
									</div>
									
									<div class="col-md-4">
										<div class="text-left"><h5>Unidad de Tiempo :</h5></div> 
										<div class="input-group mb-3">
												  
												  <select  
												<?php 
												if($promocion['Caduca_con_tiempo']==0)
												{ 
												echo "disabled='disabled'"; 
												} 
												
												?>  
													class="form-control" id="Unidad_Tiempo_validez">
													<option value="0">Escoja la Unidad de Tiempo</option>
													<option value="horas" <?php if($promocion['Unidad_Tiempo_validez']=='horas'){ echo "selected='selected'"; }?> >horas</option>
													<option value="dias"  <?php if($promocion['Unidad_Tiempo_validez']=='dias'){ echo "selected='selected'"; }?>>dias</option>
													<option value="meses" <?php if($promocion['Unidad_Tiempo_validez']=='meses'){ echo "selected='selected'"; }?>>meses</option>
												  </select>
												</div>
									</div>
									
									<div class="col-md-4">
										<div class="text-left"><h5>Tiempo :</h5></div>
										<input inputmode="none"  id="Tiempo_validez"  <?php 
										  if($promocion['Caduca_con_tiempo']==0)
										  { 
										  echo "disabled='disabled'"; 
										  } 
										  
									?>  class="form-control" type="text" placeholder="Tiempo" value="<?php echo $promocion['Tiempo_validez']; ?>" onKeyPress="return justNumbers(event);">
									</div>
								
									
							  </div>
						</div>	
                    </div>
					<hr>             
					
				<div class="row">
                       <div class="col-md-6">

								<span class="label label-success"> Restricción Canal </span>
								
								<div class="form-group">
									
									<div class="col-md-12">
								
										 
										 <div class="col-md-12">
										 <div class="text-left"><h5>Habilitar / Deshabilitar :
										 <?php 
										// echo $promocion['Requiere_canal'];
										 ?>
											   <input inputmode="none"   id="Requiere_canal" onclick="enableRequiereCanal();" 
											   
											<?php 
											if($promocion['Requiere_canal']==1)
											{ 
											echo "checked='checked'"; 
											} 
											
											?>
									type="checkbox" class="alert-info">	</h5>						
										 </div>
										 
										 
										 
										  <div class="form-group">
											<label for="exampleFormControlSelect2">Seleccione el Canal</label>
											<select id="idsCanal" multiple class="form-control" disabled="disabled">
												<option value="0D049503-85CF-E511-80C6-000D3A3261F3">Salón</option>
                                          		<option value="0E049503-85CF-E511-80C6-000D3A3261F3">Levar</option>
											</select>
										  </div>
										 
										 
										 
										 
										 
										 <select
										 <?php 
										  if($promocion['Requiere_canal']==0)
										  { 
										  echo "disabled='disabled'"; 
										  } 
											?>
										  id="idsCanal" class="js-example-basic-multiple col-sm-12" name="states[]"
                                                multiple="multiple" placeholder="Seleccione un canal de compra">
    										  <option value="0D049503-85CF-E511-80C6-000D3A3261F3">Salón</option>
                                            <option value="0E049503-85CF-E511-80C6-000D3A3261F3">Levar</option>

                                        </select>
										 
										 </div>		
									   </div>
									
									
								</div>
							
                        </div>
						
						
						
							<div class="col-md-6">
								<span class="label label-success"> Restricción Edad </span>
								
								<div class="form-group">
								<div class="text-left"><h5>Habilitar / Deshabilitar :
											   <input inputmode="none"  id="Requiere_rango_edad"<?php 
										  if($promocion['Requiere_rango_edad']==1)
										  { 
										  echo "checked='checked'"; 
										  } 
										  
									?>  onclick="enableRequiereRangoEdad();" type="checkbox"  class="alert-info">	</h5>						
										 </div>
									
									<div class="col-md-6">
										<div class="text-left"><h5>Desde :</h5></div>
										 		 <input inputmode="none"  disabled="disabled" class="form-control horario" id="edadDesde" value="<?php 

			$edadDesde = $promocion['Rango_edad'].substr(1, 2);
			echo $edadDesde;
			
			?>"
                                                       placeholder="Edad Desde" onKeyPress="return justNumbers(event);">
									</div>
									
									<div class="col-md-6">
										<div class="text-left"><h5>Hasta :</h5></div>
												<input inputmode="none"  disabled="disabled" class="form-control horario" id="edadHasta" value="<?php 

			$edadHasta = $promocion['Rango_edad'].substr(4, 2);
			echo $edadHasta;
			
			?>"
                                                       placeholder="Edad Hasta" onKeyPress="return justNumbers(event);">
									</div>
								</div>
							
                        </div>
						
						
                    </div>
					<hr>   
					
                </div>
				
				<!-- Fin Tab promociones -->
				<!-- Inicio Tab Beneficios -->
			
				 <div role="tabpanel" class="tab-pane" id="BeneficiosPromocion">
                    
					<div class="row"><br>
                        <div class="col-md-12">
								<span class="label label-success"> Beneficios Promoción</span>
								
								<div class="form-group">
									
									<div class="col-md-6">
										<div class="text-left"><h5>Beneficios :</h5></div>
										 <select class="js-example-basic-multiple col-sm-12" name="states[]"
                                                multiple="multiple" placeholder="Seleccione los beneficios">
											<option value="AL">BIFE DE CHORIZO (PURE DE PAPA) AFTER OFFICE PROMO</option>
											<option value="WY">MOUSSE DE CHOCOLATE ALMENDRO MOD</option>
											<option value="WYA">FILETE DE POLLO BBQ (PAPA FRITA) TARJETON R003</option>
											<option value="WY3">COMBO NAVIDENO P CHAUPI</option>
											<option value="WYAS">JUGO GUANABANA</option>
											<option value="WY09">PICANA (PURE DE PAPA)</option>
											<option value="WY09">CERVEZA MILLER DRAFT</option>
											<option value="WY09">AGRANDA COLA 22 oz</option>
											<option value="WY09">BONY 50 GR</option>
											<option value="WY09">LOMO FINO (PAPA CHAUPI) TARJETON R003</option>																																										
                                        </select>
									</div>
									
									<div class="col-md-6">
										<div class="text-left"><h5>Concatenador :</h5></div>
										<select class="form-control" id="Concatenador_beneficios">
													<option value="0">Escoja un concatenador</option>
													<option value="1">Todos los Productos</option>
													<option value="2">Al menos un producto</option>
												  </select>
									</div>
									
								
							  </div>
						</div>	
                    </div>
         
					
                    
					
                </div>
			
				<!-- FIN TAB Beneficios -->
				
						<!-- Inicio Tab Productos Requeridos -->
			
				 <div role="tabpanel" class="tab-pane" id="ProductosRequeridosPromocion">
                    
						
						
							<div class="row"><br>
                        <div class="col-md-12">
								<span class="label label-success"> Productos Requeridos </span>
								
								<div class="form-group">
									
									<div class="col-md-6">
										<div class="text-left"><h5>Habilitar / Deshabilitar :
											   <input inputmode="none"  id="Requiere_productos" onClick="enableRequiereProductos()" type="checkbox" class="alert-info">	</h5>						
										 </div>
										 	 <select id="IdsProductosRequeridosPromocion" disabled="disabled" class="js-example-basic-multiple col-sm-12" name="states[]"
                                            multiple="multiple" placeholder="Seleccione un producto">
											<option value="AL">BIFE DE CHORIZO (PURE DE PAPA) AFTER OFFICE PROMO</option>
											<option value="WY">MOUSSE DE CHOCOLATE ALMENDRO MOD</option>
											<option value="WYA">FILETE DE POLLO BBQ (PAPA FRITA) TARJETON R003</option>
											<option value="WY3">COMBO NAVIDENO P CHAUPI</option>
											<option value="WYAS">JUGO GUANABANA</option>
											<option value="WY09">PICANA (PURE DE PAPA)</option>
											<option value="WY09">CERVEZA MILLER DRAFT</option>
											<option value="WY09">AGRANDA COLA 22 oz</option>
											<option value="WY09">BONY 50 GR</option>
											<option value="WY09">LOMO FINO (PAPA CHAUPI) TARJETON R003</option>
                                   			 </select>
									</div>
									
									<div class="col-md-6">
										<div class="text-left"><h5>Concatenador :</h5></div>
										<select disabled="disabled" class="form-control" id="Concatenador_plus_promocion">
													<option value="0">Escoja un concatenador</option>
													<option value="1">Todos los Productos</option>
													<option value="2">Al menos un producto</option>
												  </select>
									</div>
									
								
							  </div>
						</div>	
                    </div>
					<hr>  
             
                    
					
                </div>
			
				<!-- FIN TAB Productos Requeridos -->
				<!-- Inicio Tab Restaurantes Productos Requeridos -->
			
				 <div role="tabpanel" class="tab-pane" id="RestaurantesAplicaPromocion">
                    
					
					  <div class="row"><br>

								<span class="label label-success"> Restaurantes Requeridos </span>
								
								<div class="form-group">
									
									<div class="col-md-12">
								
										 
										 <div class="col-md-12">
										 <div class="text-left"><h5>Habilitar / Deshabilitar :
											   <input inputmode="none"  id="Requiere_restaurante" onClick="enableRequiereRestaurantes()" type="checkbox" class="alert-info">	</h5>						
										 </div>
										 
										 <select id="IdsRestaurantesRequeridosPromocion" disabled="disabled" class="js-example-basic-multiple col-sm-12" name="states[]"
                                            multiple="multiple" placeholder="Seleccione los Restaurantes">
											<option value="1">FONTANA</option>
											<option value="2">CCI</option>
											<option value="3">MANTA</option>
											<option value="4">PLAZA DE LAS AMERICAS</option>
											<option value="5">QUICENTRO </option>
											<option value="6">CITY MALL</option>
											<option value="7">QUICENTRO SUR PLAZA PANECILLO</option>
											<option value="8">LA MALTERIA</option>
											<option value="9">PASEO SAN FRANCISCO</option>
											<option value="10">ARRECIFE UIO</option>
											<option value="11">SAN LUIS FULL</option>
											<option value="12">LAGUNA MALL IBARRA</option>
											<option value="13">RIOCENTRO EL DORADO</option>
                                    </select>
										 
										 </div>		
									   </div>
									
									
								</div>
							
                        </div>
					
                </div>
			<hr>
				<!-- FIN TAB Restaurantes Productos Requeridos -->
				
				
		    <!-- Inicio Tab -->
            </div>


	<button type="submit" class="btn btn-primary" id="saveP" onClick="#"> Actualizar Promoción </button>	
	<button type="button" class="btn btn-primary" id="back" onClick="verPromociones()"> Listar Promociones </button>	

<!-- <div class="contenedor">
    <div class="inferior">
        <div class="panel panel-default">
            <div class="panel-heading">

            </div>
            <div class="panel-body">
                <button>Volver a la administración</button>
            </div>
        </div>
    </div>
</div> -->
<div  id="mdl_rdn_pdd_crgnd" class="modal_cargando" style="display:none">
    <div id="mdl_pcn_rdn_pdd_crgnd" class="modal_cargando_contenedor">
        <img src="../../imagenes/admin_resources/progressBar.gif" />
    </div>
</div>
<script>
    $("button").on("click",function(){
        $("#incluirPagina").load('../promociones/adminPromociones.php');
    });
	
	
</script>


<script type="text/javascript" src="../../js/jquery-1.11.3.min.js"></script>
		<script type="text/javascript" src="../../js/jquery-ui.js"></script>
		<script type="text/javascript" src="../../js/ajax_datatables.js"></script>
		<script type="text/javascript" src="../../bootstrap/js/bootstrap.js"></script>
		<script type="text/javascript" src="../../bootstrap/js/moment.js"></script>
		<script type="text/javascript" src="../../bootstrap/js/DanGrossmanDateRangePicker/daterangepicker.js"></script>
		<script type="text/javascript" src="../../bootstrap/js/bootstrap-dataTables.js"></script>
		<script type="text/javascript" src="../../bootstrap/js/bootstrap-editable.min.js"></script>
		
		<script type="text/javascript" src="../../js/jquery.treetable.js"></script>
		<script type="text/javascript" src="../../js/chosen.proto.min.js"></script>
		<script type="text/javascript" src="../../js/alertify.js"></script>
		<script type="text/javascript" src="../../js/moment-2.22.2.js"></script>
		
		<script type="text/javascript" src="../../js/bootstrap-datetimepicker.js"></script>
		<script type="text/javascript" src="../../js/ajax_select2.js"></script>
		<script type="text/javascript" src="../../js/ajax_promociones.js"></script>
