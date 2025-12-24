<?php
////////////////////////////////////////////////////////////////
//// DESARROLLADO POR: Worman Andrade //////////////////////////
//// DESCRIPCION: Clase que permite obtener la /////////////////
//// dirección IP de la computadora del usuario ////////////////
//// FECHA CREACION: 12-12-2013 ////////////////////////////////
//// FECHA ULTIMA MODIFICACION: 16/04/2010 /////////////////////
//// USUARIO QUE MODIFICO: Juan Esteban Canelos ////////////////
//// DECRIPCION ULTIMO CAMBIO: Colección para conexión remota //
////////////////////////////////////////////////////////////////

require_once "clase_conexionRemota.php";

class direccion {
	private $lc_ip;
	private $conexionRemota;
	
	public function __construct() {
		$this->lc_ip = NULL;
		$this->conexionRemota = new conexionRemota();
	}

	public function fn_getIp() {
		$datos = $this->conexionRemota->obtenerInformacion();

		if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) && $_SERVER["HTTP_X_FORWARDED_FOR"] != "" ) {
			$this->lc_ip = $_SERVER["FORWARDED_FOR"];
		} else {
			$this->lc_ip = $_SERVER["REMOTE_ADDR"];
		}

		// Validar conexión remota por VPN (por colección)
		if ($datos["str"] > 0) {
			foreach ($datos["segmentos"] as $segmento) {
				if (strpos($this->lc_ip, $segmento) === 0) {
					$this->lc_ip = $datos["ip"];
					break;
				}
			}
		}

		return $this->lc_ip;
	}
}