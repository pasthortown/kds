<?php

class adminReimpresionRides extends sql 
{    
    function __construct() 
    {
        parent ::__construct();
    }
    
    function fn_consultarRides($accion,$rst,$dias,$opcion)
    {
        $lc_sql = "exec [config].[USP_Carga_ListaRides] '$accion','$rst','$dias','$opcion'";
        if ($this->fn_ejecutarquery($lc_sql)) 
        {
            while ($row = $this->fn_leerarreglo()) 
            {
                $this->lc_regs[] = array("factura" => $row['factura'],
                                        "numeroFactura" => utf8_encode(trim($row['numeroFactura'])),
                                        "total" => utf8_encode(trim($row['total'])),
                                        "cliente" => utf8_encode(trim($row['cliente'])),
                                        "fecha" => $row['fecha'],
                                        "estacion" => utf8_encode(trim($row['estacion'])),
                                        "bandera" => $row['bandera']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }
    function fn_cargaTipoComprobantes($accion)
    {
        $lc_sql = "[config].[USP_Carga_ListaRides] '$accion','','',''";
        if ($this->fn_ejecutarquery($lc_sql)) 
        {
            while ($row = $this->fn_leerarreglo()) 
            {
                $this->lc_regs[] = array("nombre" => $row['nombre'],
                                        "codigo" => utf8_encode(trim($row['codigo'])));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }
    
    function fn_visualizarComprobante($codigo,$bandera)
    {
       $lc_sql = "exec  [config].[USP_visualizacion_ride]  '$codigo','$bandera'";
        if ($this->fn_ejecutarquery($lc_sql)) 
        {
            while ($row = $this->fn_leerarreglo()) 
            {
                $this->lc_regs[] = array("html" => utf8_encode($row['html']),
                                        "html3" => utf8_encode($row['html3']),
                                        "html2" => utf8_encode($row['html2']),
                                        "htmlf" => utf8_encode($row['htmlf']));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs); 
    }
    
    
}