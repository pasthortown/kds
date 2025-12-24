<?php
$path = "../Funciones_Resp.txt";
header("Content-Type: application/octet-stream");    //

header("Content-Length: " . filesize($path));

header('Content-Disposition: attachment; filename='.$path);

readfile($path);