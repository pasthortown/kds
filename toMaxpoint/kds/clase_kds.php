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
          "impresion_a_tiempo_real" => (int)$row['IMPRESION_A_TIEMPO_REAL'],
          "canales_excluidos" => $row['CANALES_EXCLUIDOS']
        ));
      }

      return json_encode(null);

    } catch (Exception $e) {
      return json_encode(array("error" => $e->getMessage()));
    }
  }

  function fn_obtener_rst_categoria($rst_id) {
    $lc_sql = "SELECT rst_categoria FROM Restaurante WHERE rst_id = $rst_id";
    try {
      $this->fn_ejecutarquery($lc_sql);

      if ($row = $this->fn_leerarreglo()) {
        return json_encode(array(
          "rst_categoria" => $row['rst_categoria']
        ));
      }

      return json_encode(array("rst_categoria" => null));

    } catch (Exception $e) {
      return json_encode(array("error" => $e->getMessage()));
    }
  }
}