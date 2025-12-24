<?php

include_once "../system/conexion/clase_sql.php";
include_once "./clase_luces.php";

$lc_luces = new Luces();

if(isset($_GET["getLucesConfig"]))
{
    print $lc_luces->getLucesConfig();
}