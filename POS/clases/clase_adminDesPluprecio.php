<?php


class DescargaPluPrecio extends sql{
    //private $lc_regs;
    //constructor de la clase
    function __construct(){
        parent ::__construct();
    }
    //funcion que permite armar la sentencia sql de consulta

    function fn_consultar($lc_sqlQuery,$lc_datos){
        switch($lc_sqlQuery){
            case "cargarPluSG":
                $lc_sql = "exec config.USP_CargarPlusInformacionGerente 1," . $lc_datos[1] . "," . $lc_datos[2] . "";
                if( $this->fn_ejecutarquery($lc_sql)){
                    while($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("plu_codigo"=>$row['plu_codigo'],
                            "plu_num"=>utf8_encode(trim($row['plu_num'])),
                            "plu_descripcion"=>utf8_encode(trim($row['plu_descripcion'])),
                            "plu_canal"=>$row['plu_canal'],
                            "Pvp"=>utf8_encode(trim($row['Pvp'])),
                            "Valor_Neto"=>utf8_encode(trim($row['Valor_Neto'])),
                            "Valor_Iva"=>utf8_encode(trim($row['Valor_Iva'])),
                            "Categoria"=>utf8_encode(trim($row['Categoria'])),);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;


            case "cargarGerentePlus":
                $lc_sql = "exec config.USP_CargarPlusInformacionGerente 2," . $lc_datos[1] . "," . $lc_datos[2] . "
                 ";
                if( $this->fn_ejecutarquery($lc_sql)){
                    while($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("plu_codigo"=>$row['plu_codigo'],
                            "plu_num"=>utf8_encode(trim($row['plu_num'])),
                            "plu_descripcion"=>utf8_encode(trim($row['plu_descripcion'])),
                            "plu_canal"=>$row['plu_canal']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;

            case "cargarPluMaxp":
                $lc_sql=" DECLARE @Estado INT = 0,@Mensaje VARCHAR(250) = '';
                    EXECUTE [config].[descargar_productos] " . $lc_datos[2] . "," . $lc_datos[1] . ", @Estado OUTPUT, @Mensaje OUTPUT;
                    SELECT @Estado AS Estado, @Mensaje AS Respuesta;
               ";
                if( $this->fn_ejecutarquery($lc_sql)){
                    while($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("Estado"=>$row['Estado'],
                            "Respuesta"=>utf8_encode(trim($row['Respuesta'])));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;

            case "cargarPluPrecioMaxp":
                $lc_sql=" DECLARE @Estado INT = 0,@Mensaje VARCHAR(250) = '';
                    EXECUTE [config].[descargar_precios] " . $lc_datos[2] . "," . $lc_datos[1] . ", @Estado OUTPUT, @Mensaje OUTPUT;
                    SELECT @Estado AS Estado, @Mensaje AS Respuesta;
               ";
                if( $this->fn_ejecutarquery($lc_sql)){
                    while($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("Estado"=>$row['Estado'],
                            "Respuesta"=>utf8_encode(trim($row['Respuesta'])));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;

            case "CargarTablaMaxPlus":
                $lc_sql=" SELECT p.plu_id as plu_codigo,p.plu_num_plu as plu_num,p.plu_descripcion as plu_descripcion,c.cla_Nombre as plu_canal
                        FROM dbo.Plus p INNER JOIN dbo.Clasificacion c ON c.IDClasificacion = p.IDClasificacion
                        WHERE 
                            p.cdn_id = 	".$lc_datos[2]."
                            AND
                            p.plu_num_plu = ".$lc_datos[1]."
                             ";
                if( $this->fn_ejecutarquery($lc_sql)){
                    while($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("plu_codigo"=>$row['plu_codigo'],
                            "plu_num"=>utf8_encode(trim($row['plu_num'])),
                            "plu_descripcion"=>utf8_encode(trim($row['plu_descripcion'])),
                            "plu_canal"=>$row['plu_canal']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;
            case "CargarTablaMaxPrecios":
                $lc_sql=" SELECT producto.plu_id as plu_codigo,producto.plu_num_plu as plu_num,
                        producto.plu_descripcion as plu_descripcion,precios.pr_pvp as Pvp,
                        precios.pr_valor_neto as Valor_Neto,precios.pr_valor_iva as Valor_Iva,categorias.cat_descripcion as Categoria
                        FROM dbo.Precio_Plu AS precios
                        INNER JOIN dbo.Categoria AS categorias ON categorias.IDCategoria = precios.IDCategoria
                        INNER JOIN DBO.Plus AS producto ON producto.plu_id = precios.plu_id
                        WHERE categorias.cdn_id = ".$lc_datos[2]."	
                                AND
                                producto.plu_num_plu = ".$lc_datos[1]."
                             ";
                if( $this->fn_ejecutarquery($lc_sql)){
                    while($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("plu_codigo"=>$row['plu_codigo'],
                            "plu_num"=>utf8_encode(trim($row['plu_num'])),
                            "plu_descripcion"=>utf8_encode(trim($row['plu_descripcion'])),
                            "Pvp"=>utf8_encode(trim($row['Pvp'])),
                            "Valor_Neto"=>utf8_encode(trim($row['Valor_Neto'])),
                            "Valor_Iva"=>utf8_encode(trim($row['Valor_Iva'])),
                            "Categoria"=>utf8_encode(trim($row['Categoria'])),);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;

        }
    }
}

