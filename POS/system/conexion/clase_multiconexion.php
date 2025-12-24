<?php 
@session_start();
//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Hugo Mera/////////////////////////
////////DESCRIPCION: Clase que permite la conexión con ///////
////////  la base de datos   enviando como parametro  ////////
////////
////////
////////
////////
////////
///////TABLAS INVOLUCRADAS: No hay tablas solo exite  ////////
///////////////////  la base de datos en SQLServer2008 R2/////
///////FECHA CREACION: 25-11-2013/////////////////////////////
///////FECHA ULTIMA MODIFICACION:   //////////////////////////
///////USUARIO QUE MODIFICO: /////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: /////////////////////////////
//////////////////////////////////////////////////////////////

//Clase para realizar la conexión
class ConexionMultiple{
                private $lc_host;
                private $lc_base;
	        private $lc_conec;
//Constructor de la clase	        
        public function __construct($ambiente){
            $configuraciones = parse_ini_file('replica.ini',true);
            $this->lc_host = $configuraciones[$ambiente]["db.config.host"]."\\".$configuraciones[$ambiente]["db.config.instancia"];
            $this->connectionInfo = array( "Database"=>$configuraciones[$ambiente]["db.config.dbname"], "UID"=>$configuraciones[$ambiente]["db.config.username"], "PWD"=>$configuraciones[$ambiente]["db.config.password"]);
            $this->lc_conec = NULL;  
            
        }
	
//Función que permite conectarse a la base de datos
	public function fn_conectarse()
	{ 
  
		  if (is_null($this->lc_conec))
		  {
		   if (!($this->lc_conec = sqlsrv_connect($this->lc_host, $this->connectionInfo)
				  or die ("ERROR!! al intentar conectarse con la base de datos")))
				  $this->fn_errorconec();	
			 elseif (!(sqlsrv_query($this->lc_conec, $this->lc_base)))
				 $this->fn_errorconec();
		  }
		  return $this->lc_conec;
	}

//Generar un error en caso de que no se pueda realizar la conexión
	private function fn_errorconec()
	{
	  return sqlsrv_errors();
	}
//Función que permite desconectarse a la base de datos
	public function fn_cerrarconec()
	{
	  	if(sqlsrv_close($this->lc_conec))
		 return true;
		else
		   return false;
	}
  }//FIN DE LA CLASE CONEXION
?>
