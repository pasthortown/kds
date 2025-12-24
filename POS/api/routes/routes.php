<?php
// routes.php
return [
    'GET' => [
        '/periodo/documentacion' =>  'PeriodoController@documentacion'
    ],
    'POST' => [
        '/periodo/aperturaPeriodo' =>  'PeriodoController@crearPeriodo'
    ],
];
?>