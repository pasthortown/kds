<?php

class luces extends sql {

    function getLucesConfig() {

      $cdn_id = $_SESSION['cadenaId'];
      $rst_id = $_SESSION['rstId'];

      $lc_sql = "EXEC pedido.LUCES_config $cdn_id, $rst_id";
      try {
        $this->fn_ejecutarquery($lc_sql);
        while ($row = $this->fn_leerarreglo()) {
          $this->lc_regs[] = array(
            "numOrden" => $row['numOrden'], "url" => $row['url'], "duration" => $row['duration'], "sound" => $row['sound']
          );
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
      } catch (Exception $e) {
        return $e;
      }
      return json_encode($this->lc_regs);
    }
}
