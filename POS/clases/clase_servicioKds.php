<?php
class ServicioKds extends sql {
    function __construct() {
        parent::__construct();
    }

    public function cuerpoKDS($IDOrdenPedido,$tipo=0,$cuenta=-1) {
        $lc_sql = "EXEC [dbo].[cuerpoKDS] '$IDOrdenPedido',$tipo,$cuenta";
        $datos=null;
        $data=null;
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $data['LsNumeOrdenTMP']=trim(utf8_encode($row['LsNumeOrdenTMP']));
                $data['IDCanalMovimiento']=trim(utf8_encode($row['IDCanalMovimiento']));
                $data['Tipo_Servicio']=trim(utf8_encode($row['Tipo_Servicio']));
                $data['Canal_Monitor']=trim(utf8_encode($row['Canal_Monitor']));
                $data['Guarda_Orden']=trim(utf8_encode($row['Guarda_Orden']));
                $data['IDRestaurante']=trim(utf8_encode($row['IDRestaurante']));
                $data['PsNumeOrden']=trim(utf8_encode($row['IDOrdenPedido']));
                $IDOrdenPedido=trim(utf8_encode($row['IDOrdenPedido']));
                $datos[]=$data;
            }
            if(!empty($datos))
                foreach ($datos as $clave => $valor){
                    $Tipo_Servicio=trim($valor['Tipo_Servicio']);
                    $Guarda_Orden=trim($valor['Guarda_Orden']);
                    $datos[$clave]['Productos']=$this->movDetallePedido($IDOrdenPedido,$valor['Canal_Monitor'],$valor['IDRestaurante'],$Tipo_Servicio,$Guarda_Orden);
                }
            return ($datos); 
        }
    }

    public function movDetallePedido($IdOrdenPedido,$CanalMonitor,$IdRestaurante,$TipoServicio,$GuardarOrden) {
        $producto=null;
        $lc_sql="EXEC [dbo].[MOV_DetallePedido] '$IdOrdenPedido','$CanalMonitor','$IdRestaurante','$TipoServicio','$GuardarOrden';";
        if ($this->fn_ejecutarquery($lc_sql)) {
            $i=0;
            while ($row = $this->fn_leerarreglo()) {
                $producto[] = array(
                    'ProductoId' => trim(utf8_encode($row['dop_id'])).$this->agregarCerosALaIzquierda($i,3),
                    'PsNumeOrden' => trim(utf8_encode($IdOrdenPedido)),
                    'Nombre' => trim(utf8_encode($row['magp_desc_impresion'])),
                    'Cantidad' => trim(utf8_encode($row['dop_cantidad'])));
                    ++$i;                
            } 
        }
        return $producto;
    }

    public function politica_kds($IDRestaurante) {
        $lc_sql = "EXEC [dbo].[politica_kds] '$IDRestaurante'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs = array(
                    'URL'               => trim(utf8_encode($row['URL'])),
                    'INTENTOS'          => trim(utf8_encode($row['INTENTOS'])),
                    'TIMEOUT'           => trim(utf8_encode($row['TIMEOUT'])),
                    'APIKDS'            => trim(utf8_encode($row['APIKDS']))
                );        
            }
            return ($this->lc_regs); 
        }
    }

    function agregarCerosALaIzquierda($numero, $longitud = 3) {
        return str_pad($numero, $longitud, '0', STR_PAD_LEFT);
    }

    public function politica_kds_proveedor($IDRestaurante) {
        $lc_sql = "select [config].[fn_ColeccionRestaurante_ProveedorKDS]($IDRestaurante, 'ecuador') as proveedor";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs = array(
                    'habilitado'               => trim(utf8_encode($row['proveedor']))
                );        
            }
            return ($this->lc_regs); 
        }
    }

}
