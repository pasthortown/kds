<?php
require_once "../system/conexion/clase_sql.php";

class clase_cuponMultimarca extends sql{
    const POSICION_SOLICITUD=7,TAM_SOLICITUD=10;
    const POSICION_INCREMENTAL=19, TAM_INCREMENTAL=6;
    const POSICION_DETALLE=17, TAM_DETALLE=2;
    const POSICION_CODCUPON=26,TAM_CODCUPON=9;
    const TAMANO_CODCUPON=35;
    
    public $incremental;
    public $detalle;
    public $codigoSolicitud;
    public $codigoSeguridad;

    public function __construct($codigoEntrada=""){
        parent ::__construct();
        $this->incremental=intval(substr($codigoEntrada, self::POSICION_INCREMENTAL, self::TAM_INCREMENTAL)); 
        
        $this->detalle=substr($codigoEntrada, self::POSICION_DETALLE, self::TAM_DETALLE);
        $this->detalle=(false===$this->detalle)?"x":$this->detalle;
        
        $patrón = '/32/';
        $this->codigoSolicitud=preg_replace($patrón,'', substr($codigoEntrada, self::POSICION_SOLICITUD, self::TAM_SOLICITUD));
        $this->codigoSolicitud=(false===$this->codigoSolicitud)?"x":$this->codigoSolicitud;
        
        $this->codigoSeguridad=substr($codigoEntrada, self::POSICION_CODCUPON, self::TAM_CODCUPON);
        $this->codigoSeguridad=(false===$this->codigoSeguridad)?"x":$this->codigoSeguridad;
    }
    public function getCodigoConvertido(){
        return $this->incremental." - ".$this->solicitud."/".$this->cod_cupon."/".$this->num_detalle;
    }

    public function fn_registrarCanjeCuponMultimarca($lc_datos){
        $lc_sql = " EXEC pedido.ORD_registrar_cupon_canjeado '" . $lc_datos[0] . "', '" . $lc_datos[1] . "', '" . $lc_datos[2] . "', " . $lc_datos[3] . ", " . $lc_datos[4] . ", " . $lc_datos[5] . ", " . $lc_datos[6] . ", " . $lc_datos[7] . ", '" . $lc_datos[8] . "', " . $lc_datos[9];
        $resultadoEjecucion=$this->fn_ejecutarquery($lc_sql);
        return true;
    }
}