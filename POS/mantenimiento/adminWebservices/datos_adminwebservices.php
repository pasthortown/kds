<?php
include_once "../../clases/clase_adminwebservices.php";

$lc_cadena = $_SESSION['cadenaId'];
$adminwebservicesObj = new adminwebservices();
$ambientes = ["PRODUCCION", "PRUEBAS"];
$tipo = ["DOMINIO", "IP"];

$servidoresconfigurables = [
    "SOA" => [
        "rutas" => [
            "CCL VERIFICAR", "CCL GUARDAR INGRESO EGRESO",
            "CUPONES CANJEAR AUTOMATICO", "CUPONES MULTIMARCA CANJEAR",
            "CUPONES MULTIMARCA VERIFICAR", "CUPONES MULTIMARCA REVERSAR",
            "FORMASPAGO CADENA", "CUPONES CANJEAR MANUAL", "CUPONES IMPRESION HTML",
            "PRODUCTOS MODIFICAR", "PRECIOS AGREGAR", "RESTAURANTES POR CADENA",
            "DEPARTAMENTOS ACTUALIZAR", "MODIFICADORES CARGAR",
        ]
    ],
    "GERENTE" => [
        "rutas" => ["CCL CANCELACION", "INTERFACE UIO", "INTERFACE GYE",]
    ],
    "TRANS VENTA" => [
        "rutas" => [
            "CALCULO INTERFACE DESTINO", "ACTUALIZACION PRECIOS",
            "INYECCION INGRESO DESTINO", "RETORNA PRECIOS",
            "VALIDA CAJERO", "VALIDA TRANSFERENCIA",
        ]
    ],
    "GO TRADE" => [
        "rutas" => ["CANJE", "ANULACION"]
    ],
    "FIREBASE" => [
        "rutas" => ["CANJEPUNTOS", "CONSULTA",
            "DAR BAJA A TRANSACCION", "ESTADOCLIENTE",
            "PREREGISTRO", "REGISTRO TRANSACCION"
        ]
    ]
];
$servidoresconfigurados = $adminwebservicesObj->cargarServidoresConfigurados($lc_cadena);

$rutasconfiguradas = $adminwebservicesObj->cargarRutasConfiguradas($lc_cadena);
$ambienteBDD = $adminwebservicesObj->cargarTipoAmbienteConfigurado($lc_cadena);
