<?php

require_once 'SSRSReport.php';

function getPageURL() {
    @$PageUrl = $_SERVER["HTTPS"] == "on" ? 'https://' : 'http://';
    $uri = $_SERVER["REQUEST_URI"];
    $index = strpos($uri, '?');
    if ($index !== false) {
        $uri = substr($uri, 0, $index);
    }
    $PageUrl .= $_SERVER["SERVER_NAME"] .
            ":" .
            $_SERVER["SERVER_PORT"] .
            $uri;
    return $PageUrl;
}

define("UID", 'KFC/darwin.mora');
define("PWD", "webmaster*01");
define("SERVICE_URL", "http://192.168.100.245/ReportServer");
define("REPORT", "/reporte");


try {
    $ssrs_report = new SSRSReport(new Credentials(UID, PWD), SERVICE_URL);

    if (isset($_REQUEST['rs:ShowHideToggle'])) {
        $ssrs_report->ToggleItem($_REQUEST['rs:ShowHideToggle']);
    } else {

        $ssrs_report->LoadReport2(REPORT, NULL);
        $executionInfo = $ssrs_report->LoadReport2(REPORT, NULL);
        $parameters = array();
        $parameters[0] = new ParameterValue();
        $parameters[0]->Name = "par_cadena";
        $parameters[0]->Value = 12;
        $parameters[1] = new ParameterValue();
        $parameters[1]->Name = "par_fechai";
        $parameters[1]->Value = "2016/01/01";
        $parameters[2] = new ParameterValue();
        $parameters[2]->Name = "par_fechaf";
        $parameters[2]->Value = "2016/07/01";
        $ssrs_report->SetExecutionParameters2($parameters);
    }

    $renderAsHTML = new RenderAsHTML();
    $renderAsHTML->ReplacementRoot = getPageURL();
    $result_html = $ssrs_report->Render2($renderAsHTML, PageCountModeEnum::$Estimate, $Extension, $MimeType, $Encoding, $Warnings, $StreamIds);

    print $result_html;
} catch (SSRSReportException $serviceException) {
    print_r($serviceException);
}

/*
try {
    $ssrs_report = new SSRSReport(new Credentials(UID, PWD), SERVICE_URL);
    $ssrs_report->LoadReport2(REPORT, NULL);
    $executionInfo = $ssrs_report->LoadReport2(REPORT, NULL);
    $parameters = array();
    $parameters[0] = new ParameterValue();
    $parameters[0]->Name = "par_cadena";
    $parameters[0]->Value = 12;
    $parameters[1] = new ParameterValue();
    $parameters[1]->Name = "par_fechai";
    $parameters[1]->Value = "2016/01/01";
    $parameters[2] = new ParameterValue();
    $parameters[2]->Name = "par_fechaf";
    $parameters[2]->Value = "2016/07/01";
    $ssrs_report->SetExecutionParameters2($parameters);

    // Require the Report to be rendered in HTML format
    $renderAsHTML = new RenderAsHTML();

// Set the links in the reports to point to the php app

    $renderAsHTML->ReplacementRoot = getPageURL();

// Set the root path on the server for any image included in the report
    $renderAsHTML->StreamRoot = './images/';

// Execute the Report
    $result_html = $ssrs_report->Render2($renderAsHTML, PageCountModeEnum::$Actual, $Extension, $MimeType, $Encoding, $Warnings, $StreamIds);

// Save all images on the server (under /images/ dir)
    foreach ($StreamIds as $StreamId) {
        $renderAsHTML->StreamRoot = null;
        $result_png = $ssrs_report->RenderStream($renderAsHTML, $StreamId, $Encoding, $MimeType);

        if (!$handle = fopen("./images/" . $StreamId, 'wb')) {
            echo "Cannot open file for writing output";
            exit;
        }

        if (fwrite($handle, $result_png) === FALSE) {
            echo "Cannot write to file";
            exit;
        }
        fclose($handle);
    }
// include the Report within a Div on the page
    echo '<html><body><br/><br/>';
    echo '<div align="center">';
    echo '<div style="overflow:auto; width:700px; height:600px">';
    echo $result_html;
    echo '</div>';
    echo '</div>';
    echo '</body></html>';
} catch (SSRSReportException $serviceException) {
    print_r($serviceException);
}
*/