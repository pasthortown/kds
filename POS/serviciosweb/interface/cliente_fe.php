<?php 
    require_once '../../soap/lib/nusoap.php';
    ini_set('default_socket_timeout', 1);

    /*
    $wsdl = "http://kfc.corlasosa.com:80/seed-conector-remoto/ServicioConsultaGeneraltienda";
    $client = new nusoap_client($wsdl, 'wsdl');
       
    $param = array(
            'TIENDA' => 'V030',
            'FECHA_EMISION' => '09/06/2016',
            'SUSCRIPTOR' => 5);
    $Confirmacion = $client->call('reporteComprobantes', $param);

    //respuesta Web Services
    print_r($Confirmacion);
     */ 
    $wsdl = "http://kfc.corlasosa.com:80/seed-conector-remoto/ServicioConsultaGeneraltienda";

    // first soap header. 
    $var = new SoapVar($header, XSD_ANYXML); 
    $soapHeader = new SoapHeader(NAME_SPACE, "username", $var); 
    
// second soap header. 
    $var2 = new SoapVar($header2, XSD_ANYXML); 
    $soapHeader2 = new SoapHeader(DIFF_NAME_SPACE, "password", $var2); 
    
    
    
    $client = new SoapClient($wsdl, array(
                                            'TIENDA' => 'V030',
                                            'FECHA_EMISION' => '09/06/2016',
                                            'SUSCRIPTOR' => 5)); 

    $headers = array(); 
    $headers[] = $soapHeader; 
    $headers[] = $soapHeader2; 

    // Here my code was just terminating. 
    $client->__setSoapHeaders($headers); 
?>
