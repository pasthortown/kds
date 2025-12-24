<?php

class kdsRegional extends sql {

  function __construct() {
      parent ::__construct();
  }

  function fn_get_politicas_kds_regional($rst_id, $est_id) {
    $lc_sql = "EXEC sp_GetPoliticasKDSRegional $rst_id, '$est_id';";
    try {
      $this->fn_ejecutarquery($lc_sql);

      if ($row = $this->fn_leerarreglo()) {
        return json_encode(array(
          "url" => $row['URL'],
          "email" => $row['EMAIL'],
          "password" => $row['PASSWORD'],
          "activo" => (int)$row['ACTIVO'],
          "impresion_a_tiempo_real" => (int)$row['IMPRESION_A_TIEMPO_REAL']
        ));
      }

      return json_encode(null);

    } catch (Exception $e) {
      return json_encode(array("error" => $e->getMessage()));
    }
  }
}