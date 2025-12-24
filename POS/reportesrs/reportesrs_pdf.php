<?php

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR:  ////////////////////////////////////////////
///////DESCRIPCION: /////////////////////////
///////TABLAS INVOLUCRADAS:  /////////////////////////////////////////////
///////FECHA CREACION: /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

echo '--1<br>';
echo $_GET["reporte"];
echo '<br>';
echo $_GET["cdn_id"];
echo '<br>';
echo '--2';
//include_once("file:///C|/inetpub/wwwroot/system/conexion/clase_sql.php"); 
//include_once ("file:///C|/inetpub/wwwroot/clases/clase_menu.php");
 require_once 'SSRSReport.php';
 
 function getPageURL()
{
	$PageUrl = $_SERVER["HTTPS"] == "on"? 'https://' : 'http://';
	$uri = $_SERVER["REQUEST_URI"];
	$index = strpos($uri, '?');
	if($index !== false)
	{
		$uri = substr($uri, 0, $index);
	}
	$PageUrl .= $_SERVER["SERVER_NAME"] .
				":" .
				$_SERVER["SERVER_PORT"] .
				$uri;
	return $PageUrl;
}


if(isset($_GET["reporte"])){
echo 'acaaaaa!!';
$lc_cdn_id = $_GET["cdn_id"];
	
	define("UID", 'KFC/darwin.mora');
	define("PWD", "webmaster*01");
	define("SERVICE_URL", "http://192.168.100.245/ReportServer");
	define("REPORT", "/reporte");
	
	
	try
	{
		$ssrs_report = new SSRSReport(new Credentials(UID, PWD), SERVICE_URL);
		
		if (isset($_REQUEST['rs:ShowHideToggle']))
		{
		   $ssrs_report->ToggleItem($_REQUEST['rs:ShowHideToggle']);
	
		}
		else
		{
	
			$ssrs_report->LoadReport2(REPORT, NULL);
			$executionInfo = $ssrs_report->LoadReport2(REPORT, NULL);
			$parameters = array();
			$parameters[0] = new ParameterValue();
			$parameters[0]->Name = "par_cadena";
			$parameters[0]->Value = $lc_cdn_id;
			$ssrs_report->SetExecutionParameters2($parameters);
		}
			

		$renderAsPDF = new RenderAsPDF();
		$renderAsPDF->PageWidth = "12.5in";
		$result = $ssrs_report->Render2($renderAsPDF,
								PageCountModeEnum::$Estimate,
								$Extension,
								$MimeType,
								$Encoding,
								$Warnings,
								$StreamIds);
	
	 
	   header("Content-Type: application/force-download");
	   header("Content-Disposition: attachment; filename=\"".FILENAME."\"");
	   header("Content-length: ".(string)(strlen($result)));
	   header("Expires: ".gmdate("D, d M Y H:i:s", mktime(date("H")+2,
														  date("i"), date("s"),
														  date("m"), date("d"),
														  date("Y")))." GMT");
	   header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	   header("Cache-Control: no-cache, must-revalidate");
	   header("Pragma: no-cache");

		print $result;

	}
	catch (SSRSReportException $serviceException)
	{
		print_r($serviceException);
	}

}
?>