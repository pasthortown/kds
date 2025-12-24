<?php

include_once("../../system/conexion/clase_sql.php");
include_once("clase_reportes.php");
require_once("SSRSReport.php");

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

define("UID", 'KFC\hugo.mera');
define("PWD", "ofikfc*557");
define("SERVICE_URL", "http://192.168.100.245/ReportServer");

$parameters = array();
if (isset($_POST["idReporte"])) {

    $idReporte = $_POST["idReporte"];
    //Cargar Información Reporte por Id
    $informacionReporte = $reporte->cargarInformacionReportePorId($idReporte);
    //Cargar Parametros Reporte por Id
    $parametrosReporte = $reporte->cargarObjetoParametrosReporte($idReporte);
    define("REPORT", $informacionReporte["url"]);
    
    try {
        $ssrs_report = new SSRSReport(new Credentials(UID, PWD), SERVICE_URL);
        if (isset($_REQUEST['rs:ShowHideToggle'])) {
            $ssrs_report->ToggleItem($_REQUEST['rs:ShowHideToggle']);
        } else {
            $ssrs_report->LoadReport2(REPORT, NULL);
            $executionInfo = $ssrs_report->LoadReport2(REPORT, NULL);
            foreach ($parametrosReporte as $prm) {
                if ($prm["obligatorio"] > 0) {
                    if (isset($_POST[$prm["nombre"]])) {
                        $parameters[$prm["orden"]] = new ParameterValue();
                        $parameters[$prm["orden"]]->Name = $prm["nombre"];
                        $parameters[$prm["orden"]]->Value = $_POST[$prm["nombre"]];
                    }
                } else {
                    switch ($prm["nombre"]) {
                        case "RprmTienda":
                            $parameters[$prm["orden"]] = new ParameterValue();
                            $parameters[$prm["orden"]]->Name = $prm["nombre"];
                            $parameters[$prm["orden"]]->Value = "294";
                            break;
                        case "RprmUsuario":
                            $parameters[$prm["orden"]] = new ParameterValue();
                            $parameters[$prm["orden"]]->Name = $prm["nombre"];
                            $parameters[$prm["orden"]]->Value = "Jorge Tinoco";
                            break;
                        case "RprmCadena":
                            $parameters[$prm["orden"]] = new ParameterValue();
                            $parameters[$prm["orden"]]->Name = $prm["nombre"];
                            $parameters[$prm["orden"]]->Value = "12";
                            break;
                        default:
                            break;
                    }
                }
            }
            $ssrs_report->SetExecutionParameters2($parameters);
        }

        $renderAsHTML = new RenderAsHTML();
        $renderAsHTML->ReplacementRoot = getPageURL();
        $result_html = $ssrs_report->Render2($renderAsHTML, PageCountModeEnum::$Estimate, $Extension, $MimeType, $Encoding, $Warnings, $StreamIds);

        echo $result_html;
    } catch (SSRSReportException $serviceException) {
        print_r($serviceException);
    }
} else {
    //Cerrar pestaña
}