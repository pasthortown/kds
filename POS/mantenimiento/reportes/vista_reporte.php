<?php

@session_start();

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_reportes.php';
require_once 'SSRSReport.php';

$reporte = new Reporte();

function getPageURL() {
    @$PageUrl = $_SERVER["HTTPS"] == "on" ? 'https://' : 'http://';
    $uri = $_SERVER["REQUEST_URI"];
    $index = strpos($uri, '?');
    if ($index !== false) {
        $uri = substr($uri, 0, $index);
    }
    $PageUrl .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $uri;
    return $PageUrl;
}

$variables = json_decode($reporte->obtenerUsuarioClaveInstancia($_SESSION["rstId"]));
$var0 = 0;
$var1 = 1;
$var2 = 2;
$varClave = $variables->$var0;
$varInstancia = $variables->$var1;
$varUsuario = $variables->$var2;
define("UID", $varUsuario->variable);
define("PWD", $varClave->variable);

$parameters = array();
if (isset($_POST["idReporte"])) {
    try {
        $idReporte = $_POST["idReporte"];
        //Cargar Información Reporte por Id
        $informacionReporte = $reporte->cargarInformacionReportePorId($_SESSION["cadenaId"], $_SESSION["rstId"], $idReporte);
        if ($informacionReporte["ipServidor"] !== "") {
            define("SERVICE_URL", "http://" . $informacionReporte["ipServidor"] . "/" . $varInstancia->variable . "/");
            //echo SERVICE_URL;
            //echo "<br/>";
            //echo UID;
            //echo "<br/>";
            //echo PWD;
            //echo "<br/>";
            //Cargar Parametros Reporte por Id
            $parametrosReporte = $reporte->cargarObjetoParametrosReporte($idReporte);
            define("REPORT", $informacionReporte["url"]);
            $ssrs_report = new SSRSReport(new Credentials(UID, PWD), SERVICE_URL);
            $ssrs_report->LoadReport2(REPORT, NULL);
            $executionInfo = $ssrs_report->LoadReport2(REPORT, NULL);
            foreach ($parametrosReporte as $prm) {
                if ($prm["obligatorio"] > 0) {
                    if (isset($_POST[$prm["nombre"]])) {
                        if ($prm["tipoDato"] != "B") {
                            $parameters[$prm["orden"]] = new ParameterValue();
                            $parameters[$prm["orden"]]->Name = $prm["nombre"];
                            $parameters[$prm["orden"]]->Value = $_POST[$prm["nombre"]];
                        } else {
                            if ($_POST[$prm["nombre"]] != '-1') {
                                $parameters[$prm["orden"]] = new ParameterValue();
                                $parameters[$prm["orden"]]->Name = $prm["nombre"];
                                $parameters[$prm["orden"]]->Value = $_POST[$prm["nombre"]];
                            }
                        }
                    }
                } else {
                    $parameters[$prm["orden"]] = new ParameterValue();
                    $parameters[$prm["orden"]]->Name = $prm["nombre"];
                    $parameters[$prm["orden"]]->Value = $_SESSION[$prm["columnaIntegracion"]];
                }
            }
            $ssrs_report->SetExecutionParameters2($parameters);
            if ($_POST["inView"] === "views") {
                $renderAsHTML = new RenderAsHTML();
                $renderAsHTML->ReplacementRoot = getPageURL();
                $result = $ssrs_report->Render2($renderAsHTML, PageCountModeEnum::$Estimate, $Extension, $MimeType, $Encoding, $Warnings, $StreamIds);
                //Impresión de Reporte
                print $result;
            } else if ($_POST["inView"] === "pdf") {
                $renderAsPDF = new RenderAsPDF();
                $renderAsPDF->PageWidth = "12.5in";
                $result = $ssrs_report->Render2($renderAsPDF, PageCountModeEnum::$Estimate, $Extension, $MimeType, $Encoding, $Warnings, $StreamIds);
                header("Content-Type: application/force-download");
                header("Content-Disposition: attachment; filename=\"" . $informacionReporte["label"] . ".pdf\"");
                header("Content-length: " . (string) (strlen($result)));
                header("Expires: " . gmdate("D, d M Y H:i:s", mktime(date("H") + 2, date("i"), date("s"), date("m"), date("d"), date("Y"))) . " GMT");
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
                header("Cache-Control: no-cache, must-revalidate");
                header("Pragma: no-cache");
                //Impresión de Reporte
                print $result;
            } else if ($_POST["inView"] === "excel") {
                $renderAsEXCEL = new RenderAsEXCEL();
                $result = $ssrs_report->Render2($renderAsEXCEL, PageCountModeEnum::$Estimate, $Extension, $MimeType, $Encoding, $Warnings, $StreamIds);
                $handle = fopen($_SERVER['DOCUMENT_ROOT'] . "\\pos\\mantenimiento\\reportes\\reporte.xls", 'wb');
                fwrite($handle, $result);
                fclose($handle);

                echo $html = "<html><head><title>Descargar Reporte</title></head><body><a id=\"enlace\" href=\"reporte.xls\" download></a><script type=\"text/javascript\">var obj = document.getElementById(\"enlace\");if (obj){   obj.click();   }</script></body></html>";
            } else {
                echo "No se ha especificado un formato valido.";
            }
        } else {
            echo "Lo sentimos, comuniquese con el Administrador del Sistema.";
            echo "<br/>";
            echo "<br/>";
            echo "No existe un Servidor Configurado para este local" . $_SESSION['rstId'] . " de donde consumir los reportes.";
        }
    } catch (SSRSReportException $serviceException) {
        echo "Lo sentimos, comuniquese con el Administrador del Sistema.";
        echo "<br/>";
        echo "<br/>";
        echo "Mensaje: " . $serviceException->errorDescription;
        echo "<br/>";
        echo "<br/>";
        echo print_r($serviceException);
    }
} else {
    echo "Lo sentimos, comuniquese con el Administrador del Sistema.";
}