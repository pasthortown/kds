<?php

class server
{
    public function __construct(){}
    
    public function consultaBines()
    {        
        $this->con=(is_null($this->con))?self::coneccion():$this->con;
    }
    
    static function coneccion()
    {
        $connectionInfo = array("Database" => "DBSWITCH_KFC", "UID" => "maxbines", "PWD" => "max*2016");
        $con=sqlsrv_connect("SRV-SWITCHT", $connectionInfo);
        return $con;
    }
    
    public function obtieneBines()
    {
        $query=sqlsrv_query($this->con, "select * from dbo.vw_Bines order by bin_inicio ", array(), array("Scrollable" => "buffered"));    
        $result=sqlsrv_fetch_array($query);
        print_r( $result);
    }        
}

$params=array("uri"=>"localhost:880/pos/mantenimiento/adminbines/binserver.php");
$server= new SoapServer(NULL, $params);
$server->setClass("binserver");
$server->handle();


