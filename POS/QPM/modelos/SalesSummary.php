<?php
//include("../system/conexion/clase_sql.php");

class SalesSummaryQPM extends sql{
        
    public function consultarSalesSummary($_parametros)
    {
        $this->lc_regs = Array();
        $lc_sql = "EXEC [config].[USP_Sales_Summary_QPM] '".$_parametros['accion']."', '".$_parametros['periodo']."'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "fecha_apertura" => $row['fecha_apertura']->format('Y-m-d'),
                    "venta_bruta" => $row['venta_bruta'],
                    "moneda" => utf8_encode($row['moneda'])
                );
            }
            $this->lc_regs['str'] = $this->fn_numregistro();
            return json_encode($this->lc_regs);
        }
        $this->fn_liberarecurso();
    }

    public function ingresarTransaccionParaAuditoriaSalesSummary($_parametros)
    {
            $this->lc_regs = Array();
            $lc_sql = "EXEC [seguridad].[IAE_Auditoria_Transaccion] @rst_id='".$_parametros['rst_id']."',
                                                                @atran_modulo='".$_parametros['atran_modulo']."',
                                                                @atran_descripcion='".$_parametros['atran_descripcion']."',
                                                                @atran_accion='".$_parametros['atran_accion']."',
                                                                @Auditoria_TransaccionVarchar1='".$_parametros['Auditoria_TransaccionVarchar1']."',
                                                                @Auditoria_TransaccionVarchar2='".$_parametros['Auditoria_TransaccionVarchar2']."'
                        ";
            if ($this->fn_ejecutarquery($lc_sql)) {
                while ($row = $this->fn_leerarreglo()) {
                    $this->lc_regs[] = array(
                        "IDAuditoriaTransaccion" => utf8_encode($row['IDAuditoriaTransaccion'])
                    );
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
            }
            $this->fn_liberarecurso();
        
    }
}
