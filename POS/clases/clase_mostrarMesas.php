<?php

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Clase para Carga de Combos///////////////
/////////////////////En las Mesas/////////////////////////////
///////TABLAS INVOLUCRADAS: Cadena, Restaurante //////////////
////////////////////////////Pisos, AreaPisos, Mesas///////////
///////FECHA CREACION: 16-Enero-2014//////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 



class combo extends sql 
{
  //constructor de la clase
 function __construct()
   {
	//con herencia 
	parent::__construct();
   }
	var $code = "";

/////////////////////////////////////////CARGAR CADENA///////////////////////////////////////////////////////
	function cargarCadena()
	{
		$lc_query="SELECT cdn_id,cdn_descripcion FROM Cadena ORDER BY cdn_descripcion ASC";
		echo $lc_query;
		$lc_datos=$this->fn_ejecutarquery($lc_query);
		$lc_numreg = $this->fn_numregistro();
		if($lc_numreg>0)
		{
			while($row= $this->fn_leerarreglo())
			{echo '<option value="'.$row["cdn_id"].'">'.$row["cdn_descripcion"].'</option>'; }
		}
	}

/////////////////////////////////////////CARGAR RESTAURANTE////////////////////////////////////////////////////
	function cargarRestaurante($lc_code)
	{
		$lc_query="SELECT rst_id, rst_cod_tienda, rst_descripcion FROM Restaurante WHERE cdn_id = ".$lc_code." ORDER BY rst_cod_tienda ASC";
		
		echo $lc_query;
		
		$lc_datos=$this->fn_ejecutarquery($lc_query);
		$lc_numreg = $this->fn_numregistro();
		if($lc_numreg>0)
		{
			while($row= $this->fn_leerarreglo())
				{echo '<option value="'.$row["rst_id"].'">'.$row["rst_cod_tienda"].'&nbsp;'.$row["rst_descripcion"].'</option>'; }
		}
	}

/////////////////////////////////////////CARGAR PISO DEL RESTAURANTE//////////////////////////////////////////
	function cargarPiso($lc_code)
	{
		$lc_query = "SELECT pis_id, pis_numero FROM Pisos WHERE rst_id = ".$lc_code;

		echo $lc_query;
		
		$lc_datos=$this->fn_ejecutarquery($lc_query);
		$lc_numreg = $this->fn_numregistro();
		if($lc_numreg>0)
		{
			while($row= $this->fn_leerarreglo())
				{echo '<option value="'.$row["pis_id"].'">'.$row["pis_numero"].'</option>'; }
		}
		else
			{echo '<option value="1">1</option>'; }
	}

/////////////////////////////////////////CARGAR AREA DEL RESTAURANTE////////////////////////////////////////
	function cargarArea($lc_code)
	{
		$lc_query = 'SELECT arp_id, arp_descripcion FROM AreaPiso WHERE pis_id = '.$lc_code;
		
		echo $lc_query;
		
		$lc_datos=$this->fn_ejecutarquery($lc_query);
		$lc_numreg = $this->fn_numregistro();
		if($lc_numreg>0)
		{
			$area = array();
			while($row= $this->fn_leerarreglo())
				{echo '<option value="'.$row["arp_id"].'">'.$row["arp_descripcion"].'</option>'; }
		}
		else
			{echo '<option value="1">Primario</option>'; }
	}

/////////////////////////////////////////CARGAR COMBOS USUARIO////////////////////////////////////////

function fn_consultar($lc_opcion, $lc_datos)
 {
  
  switch($lc_opcion)
  {
  
 
   case 'cargarPiso':
  $lc_query="SELECT pis_id, pis_numero FROM Pisos WHERE rst_id = $lc_datos[0] ORDER BY pis_numero"; 
 
   if($this->fn_ejecutarquery($lc_query))
   {
     while($row = $this->fn_leerarreglo())
     {
      
     $this->lc_regs[] = array("pis_id"=>$row['pis_id'],"pis_numero"=>$row['pis_numero']);        
      }

    $this->lc_regs['str']=$this->fn_numregistro();
    return json_encode($this->lc_regs); 
   }
   $this->fn_liberarecurso();
   break;
   
	case 'CargarArea':
   $lc_query="SELECT arp_id, arp_descripcion FROM AreaPiso WHERE pis_id = ".$lc_datos[0]." ORDER BY arp_descripcion"; 
 
   if($this->fn_ejecutarquery($lc_query))
   {
     while($row = $this->fn_leerarreglo())
     {
      
     $this->lc_regs[] = array("arp_id"=>$row['arp_id'],"arp_descripcion"=>$row['arp_descripcion']);        
      }

    $this->lc_regs['str']=$this->fn_numregistro();
    return json_encode($this->lc_regs); 
   }
   $this->fn_liberarecurso();
   break;
   
 case 'CargarMesa':
   $lc_query="SELECT m.mesa_descripcion, m.mesa_coordenadax, m.mesa_coordenaday,e.std_descripcion
						FROM Restaurante r inner join Pisos p on r.rst_id=p.rst_id
							  inner join AreaPiso ap on p.pis_id=ap.pis_id
							  inner join Mesa m on m.arp_id =ap.arp_id
							  inner join Estado e on m.std_id=e.std_id
							  WHERE r.rst_id=".$lc_datos[0]." and p.pis_id=".$lc_datos[1]." and ap.arp_id=".$lc_datos[2]." AND m.mesa_coordenadax!=NULL"; 
 
   if($this->fn_ejecutarquery($lc_query))
   {
     while($row = $this->fn_leerarreglo())
     {
      
     $this->lc_regs[] = array("mesa_descripcion"=>$row['mesa_descripcion'],"mesa_coordenadax"=>$row['mesa_coordenadax'], 
								"mesa_coordenaday"=>$row['mesa_coordenaday'],"std_descripcion"=>$row['std_descripcion']);        
      }

    $this->lc_regs['str']=$this->fn_numregistro();
    return json_encode($this->lc_regs); 
   }
   $this->fn_liberarecurso();
   break;
   
  }  
 } 






}

