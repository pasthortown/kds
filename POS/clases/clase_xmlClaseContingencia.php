<?php

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Clase para Facturación///////////////////
/////////////////////En las Mesas/////////////////////////////
///////TABLAS INVOLUCRADAS: Cadena, Restaurante //////////////
////////////////////////////Pisos, AreaPisos, Mesas///////////
////////////////////////////Facturas//////////////////////////
///////FECHA CREACION: 28-Enero-2014//////////////////////////
///////FECHA ULTIMA MODIFICACION:21/04/2014///////////////////
///////USUARIO QUE MODIFICO: Jose Fernandez///////////////////
///////DECRIPCION ULTIMO CAMBIO: varios para mejorar /////////
////////////////////////////////funcionalidad/////////////////
////////////////////////////////////////////////////////////// //


class claves_contingencia_conti extends sql 
{
  //constructor de la clase
 function __construct()
   {
	//con herencia 
	parent::__construct();
   }


	function fn_consultarCabFacturaContingencia($lc_fctId)
	{
		$lc_sql="exec XML_CabeceraFacturaContingencia '".$lc_fctId."'";
		return $data=$this->fn_ejecutarquery($lc_sql);		
	}
	
	function fn_consultarCabAnulacion($lc_fctId)
	{
		$lc_sql="exec XML_CabeceraNotaCreditoContingencia '".$lc_fctId."'";
		return $data=$this->fn_ejecutarquery($lc_sql);		
	}
	
	
	function fn_consultarImpuestosFacturaContingencia($lc_facimpuesto)
	{
		$lc_sql="declare @cero as varchar(1)
				set @cero='0'
				SELECT
					sum(round((df.dtfac_cantidad*df.dtfac_precio_unitario),2)) as precioTotalSinImpuesto,
					2 as Impuestocodigo,
					case when p.plu_impuesto=1 then 2
						when p.plu_impuesto=0 then @cero
					end as ImpuestocodigoPorcentaje,	
					case when p.plu_impuesto=1 then 12
						when p.plu_impuesto=0 then @cero
					end as Impuestotarifa,	
					sum(round((df.dtfac_cantidad*df.dtfac_precio_unitario),2)) as ImpuestobaseImponible,
					sum(round((df.dtfac_iva* df.dtfac_cantidad),2))as Impuestovalor
				FROM Detalle_Factura df inner join Plus p on p.plu_id=df.plu_id
				WHERE df.cfac_id='$lc_facimpuesto'
				group by p.plu_impuesto";	
		return $data=$this->fn_ejecutarquery($lc_sql);		
	}
	
	
	function fn_consultarImpuestosAnulacion($lc_fimpuesto)
	{
		$lc_sql="declare @cero as varchar(1)
				set @cero='0'
				SELECT
					sum(round((df.dncre_precio_unitario*df.dncre_cantidad),2)) as precioTotalSinImpuesto,
					2 as Impuestocodigo,
					case when p.plu_impuesto=1 then 2
						when p.plu_impuesto=0 then @cero
					end as ImpuestocodigoPorcentaje,	
					case when p.plu_impuesto=1 then 12
						when p.plu_impuesto=0 then @cero
					end as Impuestotarifa,	
					sum(round((df.dncre_precio_unitario*df.dncre_cantidad),2)) as ImpuestobaseImponible,
					sum(round((df.dncre_iva* df.dncre_cantidad),2))as Impuestovalor
				FROM Detalle_nota_credito df inner join Plus p on p.plu_id=df.plu_id
				WHERE df.ncre_id='$lc_fimpuesto'
				group by p.plu_impuesto";	
		return $data=$this->fn_ejecutarquery($lc_sql);		
	}
	
	function fn_consultarDetFacturaContingencia($lc_fctId)
	{
		$lc_sql="exec XML_DetalleDocumentoContingencia '".$lc_fctId."'";
				return $data=$this->fn_ejecutarquery($lc_sql);		
	}
	function fn_consultarDetAnulacion($lc_anuId)
	{
		$lc_sql="exec XML_DetalleNotaCreditoContingencia '".$lc_anuId."'";
				return $data=$this->fn_ejecutarquery($lc_sql);		
	}
	function fn_inserta_canal_Fact_Contingencia($claveAcceso,$lc_fctId)
	{
		$lc_sql="declare @certificado varchar(100),  @pass varchar(20), @empresa int,@cadena int,@tipoFact int
				set @cadena=(select r.cdn_id from  Restaurante r  inner join Cabecera_Factura cf on 
								cf.rst_id=r.rst_id where 
								cf.cfac_id='".$lc_fctId."')
				set @empresa=(select emp_id from cadena where cdn_id=@cadena)
				set @certificado=(select emp_certificado from Empresa where emp_id=@empresa)
				set @pass=(select emp_pass_certificado from Empresa where emp_id=@empresa)
				insert into Canal_Movimiento_comprobante (cmp_fecha,cmp_nombre_comprobante,cmp_nombre_firma,cmp_clave,std_id)
				values(GETDATE(),'".$claveAcceso."',@certificado,@pass,50);";
				return $data=$this->fn_ejecutarquery($lc_sql);		
	}	
	function fn_inserta_canal_comprobanteAnulacion($claveAcce,$lc_fctIdA)
	{
		$lc_sql="declare @certificado varchar(100),  @pass varchar(20), @empresa int,@cadena int,@tipoFact int
	set @cadena=(select r.cdn_id from  Restaurante r  inner join cabecera_Nota_Credito cf on 
					cf.rst_id=r.rst_id where 
					cf.ncre_id='$lc_fctIdA')
	set @empresa=(select emp_id from cadena where cdn_id=@cadena)
	set @certificado=(select emp_certificado from Empresa where emp_id=@empresa)
	set @pass=(select emp_pass_certificado from Empresa where emp_id=@empresa)
				insert into Canal_Movimiento_comprobante (cmp_fecha,cmp_nombre_comprobante,cmp_nombre_firma,cmp_clave,std_id)
				values(GETDATE(),'$claveAcce',@certificado,@pass,50);";
				return $data=$this->fn_ejecutarquery($lc_sql);		
	}
}


?>