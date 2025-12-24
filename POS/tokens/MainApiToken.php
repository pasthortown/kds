<?php

if (file_exists("../tokens/api_sir_integrations/ConsultarApiTokens.php")) {
    include_once("../tokens/api_sir_integrations/ConsultarApiTokens.php");
} elseif (file_exists("../../tokens/api_sir_integrations/ConsultarApiTokens.php")) {
    include_once("../../tokens/api_sir_integrations/ConsultarApiTokens.php");
}

function apiTokenIntegracion($idCadena, $Parametro)
{
    
    if (!isset($idCadena) || !isset($Parametro)) {
        return '';
    }
    
    $ApiOrdenPedio = new ConsultarApiTokens($idCadena);

    switch ($Parametro) {
        case 'TokenOrdenPedido':
            return $ApiOrdenPedio->obtenerToken();
            break;
        case 'TokenTypeOrdenPedido':
            return $ApiOrdenPedio->obtenerTokenType();
            break;
        case 'CrearToken':
            return $ApiOrdenPedio->crearToken();
            break;
        default:
        return '';
            break;
    }

}

?>