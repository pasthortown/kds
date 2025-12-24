
<?php

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Clase para Carga de Combos///////////////
/////////////////////En las Mesas/////////////////////////////
///////TABLAS INVOLUCRADAS: Cadena, Restaurante //////////////
////////////////////////////Pisos, AreaPisos, Mesas///////////
////////////////////////////Reservas//////////////////////////
///////FECHA CREACION: 28-Enero-2014//////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 

class reservas extends sql 
{
  //constructor de la clase
 function __construct()
   {
	//con herencia 
	parent::__construct();
   }


/////////////////////////////////////////BUSCADOR CLIENTES - PREDICTIVO////////////////////////////////////////////////////
	
	function fn_consultarCliente($lc_cliente)
		{ 	
				$lc_sql="SELECT * FROM Cliente	WHERE (cli_nombres like '%$lc_cliente[0]%') OR (cli_apellidos like '%$lc_cliente[0]%')";
					if($lc_datos = $this->fn_ejecutarquery($lc_sql)) {
						while($row = $this->fn_leerarreglo()) {	
							$nombreCliente = $row['cli_nombres'].' '.$row['cli_apellidos'];
							$this->lc_regs[] = array("cli_nombres"=>$nombreCliente,"value"=>strtoupper(utf8_encode(trim($nombreCliente))));	
						}	
					}
					return json_encode($this->lc_regs);	
			
			}


/////////////////////////////////////////GUARDAR RESERVAS////////////////////////////////////////////////////
function fn_guardarReservas($lc_descripcion, $lc_fecha, $lc_horaInicia, $lc_horaFin, $lc_clienteNombre, $lc_clienteFono,$lc_mesa)
	{	
		$lc_horaInicial = $lc_fecha." ".$lc_horaInicia;
		$lc_horaFinal   = $lc_fecha." ".$lc_horaFin;
		
		$lc_queryInsertReserva = "SET DATEFORMAT ymd  INSERT INTO Cliente_Reserva(rsv_descripcion, rsv_fecha, rsv_horainicio, rsv_horafin, rsv_nombre_cliente, rsv_fono) 
										VALUES('".$lc_descripcion."', '".$lc_fecha."', '".$lc_horaInicial."', '".$lc_horaFinal."', '".$lc_clienteNombre."', '".$lc_clienteFono."')";
		$lc_datosInsertReserva =  $this->fn_ejecutarquery($lc_queryInsertReserva);

		$lc_query = "SELECT * FROM Cliente_Reserva";
		$lc_datos =  $this->fn_ejecutarquery($lc_query);
		
		$lc_numreg = $this->fn_numregistro();
		
		$lc_queryMesa = "INSERT INTO Cliente_mesa(mesa_id, rsv_id) VALUES('".$lc_mesa."','".$lc_numreg."')";
		$lc_datosMesa =  $this->fn_ejecutarquery($lc_queryMesa);
		
		$lc_queryReserva = "UPDATE Mesa SET std_id=33 WHERE mesa_id=".$lc_mesa;
		$lc_datosReserva =  $this->fn_ejecutarquery($lc_queryReserva);		
		if($lc_datosReserva)
			{	?>
				<script type="text/javascript">
                    alert("Se almacenaron correctamente los datos");
                    document.location.href="../userMesas.php";
                </script> <?php
			}else
			{?>
				<script type="text/javascript">
                    alert("Los Datos no se almacenaron, por favor intente nuevamente");
                    window.history.back();
                </script> <?php 
			}
	}

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
  	$lc_query="SELECT m.mesa_id, m.mesa_descripcion, m.mesa_coordenadax, m.mesa_coordenaday,e.std_descripcion
						FROM Restaurante r inner join Pisos p on r.rst_id=p.rst_id
							  inner join AreaPiso ap on p.pis_id=ap.pis_id
							  inner join Mesa m on m.arp_id =ap.arp_id
							  inner join Estado e on m.std_id=e.std_id
							  WHERE r.rst_id=".$lc_datos[0]." and p.pis_id=".$lc_datos[1]." and ap.arp_id=".$lc_datos[2]." AND m.mesa_coordenadax is not NULL ORDER BY mesa_id "; 
   if($this->fn_ejecutarquery($lc_query))
   {
     while($row = $this->fn_leerarreglo())
     {
      
     $this->lc_regs[] = array(
        "mesa_id"=>$row['mesa_id'],
        "mesa_descripcion"=>$row['mesa_descripcion'],
        "mesa_coordenadax"=>(float)$row['mesa_coordenadax'],
        "mesa_coordenaday"=>(float)$row['mesa_coordenaday'],
        "std_descripcion"=>$row['std_descripcion']);
      }

    $this->lc_regs['str']=$this->fn_numregistro();
    return json_encode($this->lc_regs); 
   }
   $this->fn_liberarecurso();
   break;   
  }  
 } 
}

