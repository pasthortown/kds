<?php

class OrdenPedidoPosterior extends sql {

    function getOrdenPedidoPosterior($ip) {
      $lc_sql = "EXEC pedido.get_orden_pedido_by_ip_estacion '$ip'";
      try {
        $this->fn_ejecutarquery($lc_sql);
        while ($row = $this->fn_leerarreglo()) {
          $this->lc_regs[] = array(
            "id" => $row['id'],
            "producto" => $row['producto'],
            "id_padre" => $row['id_padre'],
            "precio" => $row['precio'],
          );
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
      } catch (Exception $e) {
        return $e;
      }
      return json_encode($this->lc_regs);
    }
}