<?php

class client
{
    public function __construct()
    {
        $params=array('location'=>'http://localhost:880/pos/mantenimiento/adminbines/binserver.php','uri'=>'urn://pos/mantenimiento/adminbines/binserver.php','trace'=>1);
        $this->instance=new SoapClient(NULL,$params);
        
    }
    
    public function obtenerBinesSwt()
    {
        return $this->instance->__soapCall('obtieneBines',array('',''));
    }
}

$client=new client();

