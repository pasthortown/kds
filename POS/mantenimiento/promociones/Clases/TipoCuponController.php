<?php
namespace Maxpoint\Mantenimiento\promociones\Clases;
use Doctrine\DBAL\Connection;
use Maxpoint\Mantenimiento\adminReplicacion\Clases\Controller;

class TipoCuponController extends Controller
{
    public $clasificacionesCupon=[
        "USUARIO" => [
            "clase-css"=>"restriccion-usuario",
            "restricciones"=>["edad"]
        ],
        "TIPO VENTA" => [
            "clase-css"=>"restriccion-tipoventa",
            "restricciones"=>["canal"]
        ],
        "NUMERICO" => [
            "clase-css"=>"restriccion-numericas",
            "restricciones"=>[
                "limite-canjes-total",
                "limite-canjes-cliente",
                "bruto-factura",
                "minimo-productos",
                "maximo-canje-multiple"
            ]
        ],
        "TIEMPO" => [
            "clase-css"=>"resticcion-tiempo",
            "restricciones"=>[
                "restriccion-dias",
                "restriccion-horario"
            ]
        ],
        "OTRA" => [
            "clase-css"=>"restriccion-otra",
            "restricciones"=>[]
        ]

    ];

    public function tiposConfigurados(){
        $query = "EXEC [promociones].[USP_Promociones] 'tiposConfigurados', '',0,''";
        $res = $this->cargarDatos($query);
        if($res["estado"]==0){
            return [];
        }

        if(count($res["datos"])==0){
            return [];
        }

        $tiposConfigurados=[];
        foreach($res["datos"] as $fila ){
            $tiposConfigurados[$fila["Descripcion"]]=$fila;
        }
        $resultado=[];
        foreach($this->clasificacionesCupon as $nombreTipo=>$valor){
            if(array_key_exists($nombreTipo,$tiposConfigurados)){
                $nuevoArray=array_merge_recursive($valor,$tiposConfigurados[$nombreTipo]);
                $resultado[$nombreTipo]=$nuevoArray;
            }else{
                $resultado[$nombreTipo]=$valor;
            }
        }
        return $resultado;
    }

    public function tiposCupon($cupon){
        $query = "EXEC [promociones].[USP_Promociones] 'categoriasCupon', '',0,'".$cupon["Id_Promociones"]."'";
        $res = $this->cargarDatos($query);

        if($res["estado"]==0){
            return [];
        }

        if(count($res["datos"])==0){
            return [];
        }

        $retorno=[];
        foreach($res["datos"] as $fila ){
            $retorno[$fila["ID_ColeccionDeDatosCadena"]]=$fila;
        }

        return $retorno;
    }


    /*
        Retorna un array con las categorias que le corrsponden al cupon
        a partir de los valores de entrada de la petición
     */
    function retornarTiposCupon($datosRequest){
        $arrayTiposCupon=[];
        if(
            $datosRequest->Limite_canjes_total != 0
            || $datosRequest->Limite_canjes_cliente != 0
            || $datosRequest->Bruto_minimo_factura > 0
            || $datosRequest->Bruto_maximo_factura > 0
            || $datosRequest->Cantidad_minima_productos_factura != 0
            || $datosRequest->Maximo_canje_multiple != 0
        ) $arrayTiposCupon[]="NUMERICO";

        if(
            $datosRequest->Requiere_rango_edad == 1
        ) $arrayTiposCupon[]="USUARIO";

        if(
            $datosRequest->Requiere_canal == 1
        ) $arrayTiposCupon[]="TIPO VENTA";

        if(
            $datosRequest->Requiere_horario == 1
            || $datosRequest->Requiere_dias == 1
        ){
            if(
                !empty($datosRequest->Dias_canjeable)
                ||!empty($datosRequest->Horario_canjeable)
            ){
                $arrayTiposCupon[]="TIEMPO";
            }
        }
        return $arrayTiposCupon;
    }

    public function guardarTipoCupon($request,$idCadena,$idUsuario){
        $query = "EXEC [config].[IAE_Politica_Cadena] 
            @cdn_id=$idCadena,
            @accion='insertarValor',
            @idModulo=1,
            @descripcionPolitica = 'CUPONES CATEGORIAS',
            @descripcionParametro = '".utf8_encode($request->nombreParametro)."',
            @lastUser = '$idUsuario',
            @valorVarchar = '".utf8_encode($request->etiqueta)."',
            @valorInteger = '".hexdec($request->color)."',
            @valorNumeric = '0.00',
            @configuracion = 0
            ";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function retornarIdTiposCupon($parametros){
        $query = "EXEC [promociones].[USP_tiposCupon] 
                    @accion='buscarIdsParametros',
                    @NombresTipos='".$parametros["nombrestipos"]."',
                    @cdnID=".$parametros["idcadena"]
        ;

        $res = $this->cargarDatos($query);
        return $res;
    }

    public function insertarTipoCuponPromocion($parametros){
        // para cada Nombre tipo que entra insertar un valor en la tercera tabla,
        // en variableV poner el ID de el tipo de promocion
        $query = "EXEC [config].[IAE_Politica_Promociones] 
            @cdn_id=".$parametros["idcadena"].",
            @id_promociones='".$parametros["idpromociones"]."',
            @accion='insertarValor',
            @idModulo=1,
            @descripcionPolitica = 'CUPONES CATEGORIAS',
            @descripcionParametro = '".utf8_encode($parametros["descripcion"])."',
            @lastUser = '".$parametros["idusuario"]."',
            @valorVarchar = '".utf8_encode($parametros["ID_ColeccionDeDatosCadena"])."',
            @configuracion = 0
            ";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function eliminarTiposCupon($parametros){
        $dbconn = $this->conexion;
        // para cada Nombre tipo que entra insertar un valor en la tercera tabla,
        // en variableV poner el ID de el tipo de promocion

        $query = "DELETE PromocionesColeccionDeDatos
                    WHERE IDColeccionPromociones=(
                        SELECT ID_ColeccionPromociones 
                        FROM dbo.ColeccionPromociones pcdd
                        WHERE pcdd.Descripcion	='CUPONES CATEGORIAS'
                        AND cdn_id=".$parametros["idcadena"]."
                    )
                    AND ID_Promociones='".$parametros["idpromocion"]."'
        ";
        $res = $dbconn->executeUpdate($query);

        return $res;
    }

    public function retornaRestaurantesNoSeleccionadosCiudades($idcadena,$ciudaes,$idcupon=null){
        $resultado=["resultado"=>"retornaRestaurantesNoSeleccionadosCiudades"];
        return $resultado;
    }

    public function retornaRestaurantesNoSeleccionadosRegiones($idcadena,$regiones,$restaurantes,$idcupon=null){
        $sqlRegiones="";
        $sqlRestaurantes="";
        if(count($regiones)>0){
            $arrayRegiones=array_map(function($element){return "'".$element."'";}, $regiones);
            $sqlRegiones = " AND rst_localizacion in (".implode(",",$arrayRegiones).") ";

        }
        if(count($restaurantes)>0){
            $arrIdRestaurantes=array_column($restaurantes,"rstId");
            $sqlRestaurantes = " AND rst_id not in (".implode(",",$arrIdRestaurantes).") ";
        }

        $sqlConsulta="SELECT r.rst_id,r.rst_descripcion,r.rst_localizacion,r.ciu_id,c.ciu_nombre,p.prv_descripcion,c.prv_id 
                    FROM RESTAURANTE r LEFT JOIN Ciudad c
                    ON r.ciu_id=c.ciu_id 
                    JOIN PROVINCIA p 
                    ON c.prv_id=p.prv_id
                    WHERE r.cdn_id=$idcadena
                    AND r.IDStatus=(SELECT config.fn_estado('Restaurante','Activo')) ";
        $sqlConsulta.=$sqlRegiones.$sqlRestaurantes;
        //error_log( trim(str_replace(PHP_EOL, '', $sqlConsulta)).PHP_EOL ,3 ,"restaurantesRegiones.log");
        $res = $this->cargarDatos($sqlConsulta);
        return $res;
    }
}
