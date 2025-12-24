<?php
require_once '../../vendor/autoload.php';

use Maxpoint\Mantenimiento\adminReplicacion\Clases\ConexionDinamica;

session_start();
$appRoot = dirname(dirname(__DIR__));
$adminRoot = join(DIRECTORY_SEPARATOR, [$appRoot, "mantenimiento"]);
$moduleRoot = join(DIRECTORY_SEPARATOR, [$adminRoot, "promociones"]);

$conexionDinamica = new ConexionDinamica();
