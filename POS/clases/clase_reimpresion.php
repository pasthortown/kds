<?php

class menuPedido extends sql {

    function _construct() {
        parent ::_construct();
    }

    function fn_consultar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {
            case 'tipoDocumentos':
                $lc_sql = "SELECT * FROM [config].[fn_ColeccionReImpresion_TipoDocumentos] ('$lc_datos[0]')";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "tipo"  =>  $row['tipo'],
                            "valor" =>  $row['valor']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
            break;

            case 'listarTransacciones':
                $lc_sql = "EXECUTE [impresion].[reimpresion_consulta] '$lc_datos[0]','$lc_datos[1]','$lc_datos[2]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            'transaccion'           => $row['transaccion'],
                            'factura'               => $row['factura'],
                            'mesa'                  => $row['mesa'],
                            'odp_id'                => $row['odp_id'],
                            'subtotal'              => $row['subtotal'],
                            'total'                 => $row['total'],
                            'estacion'              => $row['estacion'],
                            'estacion_nombre'       => $row['estacion_nombre'],
                            'usuario'               => $row['usuario'],
                            'descripcion_estado'    => $row['descripcion_estado'],
                            'observacion'           => $row['observacion'],
                            'fecha_creacion'        => $row['fecha_creacion']
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
            break;

            case 'cargarImpresora':
                $lc_sql = "SELECT * FROM [impresion].[reimpresion_consultar_impresora] ('$lc_datos[0]','$lc_datos[1]')";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "id"            =>  str_replace('"','',$row['descripcion']),
                            "descripcion"   =>  str_replace('"','',$row['descripcion'])
                        );
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
            break;

            case 'visorCabeceraFactura':
                $lc_sql = "EXEC imp_voucherCabeceraFactura '" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "emp_razon_social" => utf8_encode(trim($row['emp_razon_social'])),
                            "emp_direccion" => utf8_encode(trim($row['emp_direccion'])),
                            "emp_ruc" => utf8_encode(trim($row['emp_ruc'])),
                            "rst_direccion" => utf8_encode(trim($row['rst_direccion'])),
                            "usr_usuario" => trim($row['usr_usuario']),
                            "cfac_fechacreacion" => utf8_encode(trim($row['cfac_fechacreacion'])),
                            "cli_nombres" => utf8_encode(trim($row['cli_nombres'])),
                            "cli_apellidos" => utf8_encode(trim($row['cli_apellidos'])),
                            "cli_documento" => trim($row['cli_documento']),
                            "cli_telefono" => trim($row['cli_telefono']),
                            "cli_direccion" => trim($row['cli_direccion']),
                            "documento" => trim($row['documento']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
            break;

            case 'visorDetalleFactura':
                $lc_sql = "EXEC imp_voucherDetalleFactura '" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("dtfac_cantidad" => $row['dtfac_cantidad'],
                            "plu_descripcion" => utf8_encode(trim($row['plu_descripcion'])),
                            "dtfac_precio_unitario" => number_format(($row['dtfac_precio_unitario']), 2, ".", ""),
                            "dtfac_total" => number_format(($row['dtfac_total']), 2, ".", ""));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
            break;

            case 'totalDetalleFactura':
                $lc_sql = " SELECT cf.cfac_subtotal, cf.cfac_iva, cf.cfac_total, c.cdn_tipoimpuesto, cf.cfac_base_cero, cf.cfac_base_iva
							FROM Cabecera_Factura cf inner join Restaurante r on cf.rst_id=r.rst_id
							INNER JOIN Cadena c ON c.cdn_id=r.cdn_id
							WHERE cf.cfac_id = '" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "cfac_subtotal" => $row['cfac_subtotal'],
                            "cfac_iva" => $row['cfac_iva'],
                            "cfac_total" => $row['cfac_total'],
                            "cdn_tipoimpuesto" => $row['cdn_tipoimpuesto'],
                            "cfac_base_cero" => $row['cfac_base_cero'],
                            "cfac_base_iva" => $row['cfac_base_iva']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
            break;

            case 'formasPagoDetalleFactura':
                $lc_sql = "EXEC imp_formasPago '" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("fmp_descripcion" => utf8_encode(trim($row['fmp_descripcion'])),
                            "cfac_total" => number_format(($row['cfac_total']), 2, ".", ""));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
            break;

            case 'visorCabeceraOrdenPedido':
                $lc_sql = "SELECT 
                            JSON_QUERY(Canal_MovimientoVarchar1, '$.registros[0].registrosDetalle') AS productos
                        FROM Canal_Movimiento
                        WHERE IDCanalMovimiento = '" . $lc_datos[0] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs = array("productos" => json_decode($row['productos']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
            break;
        }
    }
}
