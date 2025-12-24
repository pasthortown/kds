<?php

//////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Worman Andrade//////////////////////
///////DESCRIPCION: Clase para crear Xml/////////////////////
///////TABLAS INVOLUCRADAS:	FACTURA, CLIENTE//////////////////
///////FECHA CREACION: 11-marzo-2014//////////////////////////
///////FECHA ULTIMA MODIFICACION:01/07/2014///////////////////
///////USUARIO MODIFICO: JOSE FERNANDEZ///////////////////////
///////DECRIPCION MODIFICACION:Se agrego la funcion////////////////// 
////////////////////////////////fn_inserta_canal_comprobante///////// 

class xmlGenera extends sql 
{
  //constructor de la clase
 function __construct()
   {
	//con herencia 
	parent::__construct();
   }

//////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////FUNCION CONSULTAR CABECERA FACTURA////////////////////////////////////////////////
function fn_consultarCabFactura($lc_fctId) 
{
	$lc_sql="exec [facturacion].[XML_CabeceraDocumento] '".$lc_fctId."'";
	return $data=$this->fn_ejecutarquery($lc_sql);
}


////////////////////////////FUNCION CONSULTAR DETALLE FACTURA////////////////////////////////////////////////
function fn_consultarDetFactura($lc_fctId) 
{
	$lc_sql="exec [facturacion].[XML_DetalleDocumento] '".$lc_fctId."'";
	return $data=$this->fn_ejecutarquery($lc_sql);
}

function fn_consultarImpuestos($lc_facimpuesto) 
{
	$lc_sql="exec [facturacion].[XML_impuestos]'$lc_facimpuesto'";
	return $data=$this->fn_ejecutarquery($lc_sql);
}


function fn_inserta_canal_comprobante($lc_claveAcceso,$lc_factura)
{
	$lc_sql="exec [facturacion].[IAE_Canal_Comprobante] '".$lc_claveAcceso."', '".$lc_factura."'";
	$result = $this->fn_ejecutarquery($lc_sql);
	if ($result){ return true; }else{ return false; };
}
///////////////////////////////////////////////FIN DE LA CLASE/////////////////////////////////////////

}


?>