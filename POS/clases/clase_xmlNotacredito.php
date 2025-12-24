<?php

//////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Jose Fernadnez//////////////////////
///////DESCRIPCION: Clase para crear Xml de nota de credito///
///////TABLAS INVOLUCRADAS:	cabecera_nota_credito, cliente////
///////FECHA CREACION: 09/07/2014//////////////////////////
//////////////////////////////////////////////////////////////
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
	$lc_sql="exec XML_CabeceraNotaCredito '".$lc_fctId."'";
	return $data=$this->fn_ejecutarquery($lc_sql);
}


////////////////////////////FUNCION CONSULTAR DETALLE FACTURA////////////////////////////////////////////////
function fn_consultarDetFactura($lc_fctId) 
{
	$lc_sql="exec XML_DetalleNotaCredito '".$lc_fctId."'";
	return $data=$this->fn_ejecutarquery($lc_sql);
}

function fn_consultarImpuestosAnulacion($lc_facimpuesto) 
{
	$lc_sql="exec XML_impuestosAnulacion '$lc_facimpuesto'";
	return $data=$this->fn_ejecutarquery($lc_sql);
}

function fn_inserta_canal_comprobante($lc_claveAcceso,$lc_factura)
{
	$lc_sql="declare @certificado varchar(100),  @pass varchar(20), @empresa int,@cadena int,@tipoFact int
			set @cadena=(select r.cdn_id from  Restaurante r  inner join Cabecera_Factura cf on cf.rst_id=r.rst_id where 
						 cf.ncre_id='".$lc_factura."')
			set @empresa=(select emp_id from cadena where cdn_id=@cadena)
			set @certificado=(select emp_certificado from Empresa where emp_id=@empresa)
			set @pass=(select emp_pass_certificado from Empresa where emp_id=@empresa)
			set @tipoFact=(select r.rst_tipo_facturacion from Restaurante r inner join Cabecera_Factura cf on r.rst_id=cf.rst_id 	
						   where cf.ncre_id='".$lc_factura."')
			if( @tipoFact = 1)
			begin
				insert into Canal_Movimiento_comprobante (cmp_fecha,cmp_nombre_comprobante,cmp_nombre_firma,cmp_clave,std_id)
				values(GETDATE(),'".$lc_claveAcceso."',@certificado,@pass,50);
			end
			
	--insert into Canal_Movimiento_comprobante values(GETDATE(),'".$lc_claveAcceso."',@certificado,@pass,48);
	update Cabecera_Nota_Credito set ncre_claveAcceso='".$lc_claveAcceso."' where ncre_id='".$lc_factura."'";
	$result = $this->fn_ejecutarquery($lc_sql);
	if ($result){ return true; }else{ return false; };
}
///////////////////////////////////////////////FIN DE LA CLASE/////////////////////////////////////////

}


?>