<?php

require_once(dirname(dirname(__FILE__)) . "/system/conexion/clase_sql.php");

class conexionRemota extends sql {
    function __construct() {
        parent ::__construct();
	}

	function obtenerInformacion() {
        $this->lc_regs = array();
        $lc_sql = "EXEC config.coleccionConexionRemota";
		try {
            $this->fn_ejecutarquery($lc_sql);
            if ($row = $this->fn_leerarreglo()) {
                $this->lc_regs["idEstacion"] = $row["idEstacion"];
                $this->lc_regs["ip"] = $row["ip"];
                $this->lc_regs["segmentos"] = $this->separarSegmentos($row["segmentos"]);
            }
            $this->lc_regs["str"] = $this->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
	}

	function separarSegmentos($segmentos) {
		$segmentosArr = [];
		$pccount = substr_count($segmentos, ";");
		for ($i = 0; $i < $pccount; $i++) {
			$pcpos = strpos($segmentos, ";");
			array_push($segmentosArr, trim(substr($segmentos, 0, $pcpos)));
			$segmentos = substr($segmentos, $pcpos + 1);
		}
		array_push($segmentosArr, trim($segmentos));
		return $segmentosArr;
	}
}