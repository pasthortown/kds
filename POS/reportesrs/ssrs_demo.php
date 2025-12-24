<?php
require_once 'SSRSReport.php';
define("UID", 'MACHINE_NAME\PHPDemoUser');
define("PWD", "PHPDemoUser_PWD");
define("SERVICE_URL", "http://localhost/ReportServer_SQLEXPRESS/");
define("REPORT", "/Sales");



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
	}

    $renderAsHTML = new RenderAsHTML();
	$renderAsHTML->ReplacementRoot = getPageURL();
    $result_html = $ssrs_report->Render2($renderAsHTML,
                                PageCountModeEnum::$Estimate,
                                $Extension,
                                $MimeType,
                                $Encoding,
                                $Warnings,
                                $StreamIds);

    echo '<div style="overflow:auto; width:1000px; height:700px">';
    echo $result_html;
    echo '</div>';
}
catch (SSRSReportException $serviceException)
{
    print("<pre>");
    print_r($serviceException);
    print("</pre>");
}

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
?> 
