<?php

require_once 'SSRSReport.php';

define("UID", "KFC\darwin.mora");

define("PASWD", "webmaster*01");

define("SERVICE_URL", "http://192.168.100.245/Reportserver/");

try

{

    $ssrs_report = new SSRSReport(new Credentials(UID, PASWD), SERVICE_URL);                

}

catch (SSRSReportException $serviceException)

{

    echo $serviceException->GetErrorMessage();
	echo 'hola';
	
}    


?> 
