<?php
session_start();
///////////////////////////////////////////////////////////////////////////////
///////FECHA CREACION: 23-04-2015 /////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de menú /////////////////////////
///////FECHA ULTIMA MODIFICACION: 22/05/2015///////////////////////////////////
///////USUARIO QUE MODIFICO: JOSE FERNANDEZ////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: LECTURA DE CADENA POR VARIABLE DE SESION//////
///////FECHA ULTIMA MODIFICACION: 04/01/2016 //////////////////////////////////
///////USUARIO QUE MODIFICO: Christian Pinto //////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Creacion de lista dinamica de Preguntas //////
/////// Sugeridas para el orden y buscador en Lista ///////////////////////////
///////FECHA ULTIMA MODIFICACION: 25/07/2016 
///////USUARIO QUE MODIFICO: Daniel Llerena
///////DECRIPCION ULTIMO CAMBIO: Convertir caracteres especiales en entidades 
/////// HTML (htmlspecialchars)
///////////////////////////////////////////////////////////////////////////////

include_once "../../system/conexion/clase_sql.php"; 
include_once "../../clases/clase_admplus.php";

if (
    empty($_SESSION['rstId'])
    OR empty($_SESSION['usuarioId'])
    OR empty($_SESSION['cadenaId'])
) {
    die(json_encode((object)[
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$lc_config  = new configuracionplus();

$cadena  = $_SESSION['cadenaId'];
$usuario = $_SESSION['usuarioId'];

function specialChars($a) 
{
  return htmlspecialchars($a, ENT_QUOTES,'UTF-8');
}

if(htmlspecialchars(isset($_GET["guardaVariosCanalesImpresion"])))
{	
    $lc_condiciones2[1] = htmlspecialchars($_GET["pluID"]);
    $lc_condiciones2[0] = 0;
    $lc_condiciones2[2] = htmlspecialchars('E');
    $lc_condiciones2[3] = $usuario;
    $lc_config->fn_guardaVariosCanalesImpresion($lc_condiciones2);
    
    $lc_condiciones = array_map('specialChars', $_GET["impIds"]); 
    for($i=0; $i<count($lc_condiciones); $i++)
    { 	 
        $lc_condiciones1[0]= $lc_condiciones[$i];
        $lc_condiciones1[1]= htmlspecialchars($_GET["pluID"]);
        $lc_condiciones1[2]= htmlspecialchars('I');
        $lc_condiciones1[3] = htmlspecialchars($usuario);
        print $lc_config->fn_guardaVariosCanalesImpresion($lc_condiciones1);
    }			
}

if(htmlspecialchars(isset($_GET["guardaImpuestos"])))
{	
    $lc_condiciones2[1] = htmlspecialchars($_GET["pluID"]);
    $lc_condiciones2[0] = 0;
    $lc_condiciones2[2] = htmlspecialchars('E');
    $lc_condiciones2[3] = htmlspecialchars($_GET["usuario"]);
    $lc_config->fn_guardaImpuestos($lc_condiciones2);
    
    $lc_condiciones = array_map('specialChars', $_GET["impIds"]);
    for($i=0;$i<count($lc_condiciones);$i++)
    { 	 
        $lc_condiciones1[0]= $lc_condiciones[$i];
        $lc_condiciones1[1]= htmlspecialchars($_GET["pluID"]);
        $lc_condiciones1[2]= htmlspecialchars('I');
        $lc_condiciones1[3]= htmlspecialchars($_GET["usuario"]);
        print $lc_config->fn_guardaImpuestos($lc_condiciones1);
    }			
}

if(htmlspecialchars(isset($_GET["cargarPlus"])))
{
    $lc_condiciones[0] = htmlspecialchars($_GET["Accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["inicio"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["fin"]);
    $lc_condiciones[3] = $cadena;    
    $lc_condiciones[4] = htmlspecialchars($_GET["filtro"]);
    print $lc_config->fn_cargarPlus(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["cargarPlusXClasificacion"])))
{
    $lc_condiciones[0] = htmlspecialchars($_GET["Accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["inicio"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["fin"]);
    $lc_condiciones[3] = $cadena;	
    $lc_condiciones[4] = htmlspecialchars($_GET["filtro"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["opcion"]);
    print $lc_config->fn_cargarPlusXClasificacion(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["cargarConfiguracionPlus"])))
{
    $lc_condiciones[0] = htmlspecialchars('D');
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = htmlspecialchars($_GET["plu_id"]);
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = htmlspecialchars($_GET["usuario"]);
    print $lc_config->fn_cargarConfiguracionPlus(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["cargarCanalImpresion"])))
{
    $lc_condiciones[0] = htmlspecialchars('P');
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = htmlspecialchars($_GET["plu_id"]);
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = htmlspecialchars($_GET["usuario"]);
    print $lc_config->fn_cargarCanalImpresion(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["cargarImpuestosProducto"])))
{
    $lc_condiciones[0] = htmlspecialchars('M');
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = htmlspecialchars($_GET["plu_id"]);
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = htmlspecialchars($_GET["usuario"]);
    print $lc_config->fn_cargarImpuestosProducto(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["cargarImpuestos"])))
{	
    $lc_condiciones[0] = htmlspecialchars($_GET["Accion"]);
    $lc_condiciones[1] = $cadena;
    $lc_condiciones[2] = htmlspecialchars($_GET["magp_id"]);
    print $lc_config->fn_cargarImpuestos(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["cargarPreguntasPlus"])))
{
    $lc_condiciones[0] = 1;
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = htmlspecialchars($_GET["plu_id"]);
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = htmlspecialchars($_GET["usuario"]);
    $lc_condiciones[6] = 0;
    print $lc_config->fn_cargarPreguntasPlus(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["cargarPreguntasNoAgregadasPlus"])))
{
    $lc_condiciones[0] = 0;
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = htmlspecialchars($_GET["cdn_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["plu_id"]);
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = htmlspecialchars($_GET["usuario"]);
    $lc_condiciones[6] = 0;
    print $lc_config->fn_cargarPreguntasNoAgregadasPlus(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["agregarPreguntasPlus"])))
{
    $lc_condiciones[0] = 0;
    $lc_condiciones[1] = 1;
    $lc_condiciones[2] = htmlspecialchars($_GET["cdn_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["plu_id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["psug_id"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["usuario"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["orden_preguntas"]);
    print $lc_config->fn_agregarPreguntasPlus(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["quitarPreguntasPlus"])))
{
    $lc_condiciones[0] = 1;
    $lc_condiciones[1] = 2;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = htmlspecialchars($_GET["plu_id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["psug_id"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["usuario"]);
    $lc_condiciones[6] = 0;
    print $lc_config->fn_agregarPreguntasPlus(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["actualizaOrdenPregunta"])))
{
    $lc_condiciones[0] = 0;
    $lc_condiciones[1] = 4;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = htmlspecialchars($_GET["plu_id"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["usuario"]);

    //$psug = array();
    $psug = explode(",", htmlspecialchars($_GET['arreglo'])); //funcion explode para separar los arreglos por coma		
    for ($i = 0; $i < count($psug); $i++) 
    {
        $lc_condiciones[4] = $psug[$i]; // obtenemos id
        $lc_condiciones[6] = $i; // obtenemos posicion de id
        print $lc_config->fn_agregarPreguntasPlus(array_map('specialChars', $lc_condiciones));
    } 
}

if(htmlspecialchars(isset($_GET["configuracionPlu"])))
{
    $lc_condiciones[0] = htmlspecialchars($_GET["Resultado"]);
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = htmlspecialchars($_GET["plu_id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["parametro"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["usuario"]);
    print $lc_config->fn_configuracionPlu(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["cargarClasificacion"])))
{
	$lc_condiciones[0] = htmlspecialchars($_GET["Accion"]);
	$lc_condiciones[1] = $cadena;
	$lc_condiciones[2] = htmlspecialchars($_GET["magp_id"]);
	print $lc_config->fn_cargarClasificacion(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["cargarCategoria_Plus"])))
{
    $lc_condiciones[0] = htmlspecialchars($_GET["Accion"]);
    $lc_condiciones[1] = $cadena;
    $lc_condiciones[2] = htmlspecialchars($_GET["magp_id"]);
    print $lc_config->fn_cargarCategoria_Plus(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["cargarUbicacion"])))
{
    $lc_condiciones[0] = htmlspecialchars($_GET["Accion"]);
    $lc_condiciones[1] = $cadena;
    $lc_condiciones[2] = htmlspecialchars($_GET["magp_id"]);
    print $lc_config->fn_cargarUbicacion(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["cargarImpresora"])))
{
    $lc_condiciones[0] = htmlspecialchars($_GET["Accion"]);
    $lc_condiciones[1] = $cadena;
    $lc_condiciones[2] = htmlspecialchars($_GET["magp_id"]);
    print $lc_config->fn_cargarImpresora(array_map('specialChars', $lc_condiciones));
} 

if(htmlspecialchars(isset($_GET["buscadescripcionplu"])))
{
    $lc_condiciones[0] = htmlspecialchars($_GET["Accion"]);
    $lc_condiciones[1] = $cadena;
    $lc_condiciones[2] = htmlspecialchars($_GET["magp_id"]);
    print $lc_config->fn_buscadescripcionplu(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["cargarBotonPlu"])))
{
    $lc_condiciones[0] = htmlspecialchars($_GET["Accion"]);
    $lc_condiciones[1] = $cadena;
    $lc_condiciones[2] = htmlspecialchars($_GET["magp_id"]);
    print $lc_config->fn_cargarBotonPlu(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["cargarRecetas"])))
{
    $lc_condiciones[0] = htmlspecialchars($_GET["opcion"]);
    $lc_condiciones[1] = $cadena;
    $lc_condiciones[2] = htmlspecialchars($_GET["categoria"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["Num_Plu"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["ubicacion"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["rest"]);
    print $lc_config->fn_cargarRecetas(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["cargarPlatos"])))
{
    $lc_condiciones[0] = htmlspecialchars($_GET["opcion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["plu_id"]);
    $lc_condiciones[2] = $cadena;	
    $lc_condiciones[3] = htmlspecialchars($_GET["cdat_id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["usuario"]);
    print $lc_config->fn_cargarPlatos(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["guardarPlatos"])))
{
    $lc_condiciones[0] = htmlspecialchars($_GET["opcion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["plu_id"]);
    $lc_condiciones[2] = $cadena;	
    $lc_condiciones[3] = htmlspecialchars($_GET["cdat_id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["usuario"]);
    print $lc_config->fn_guardarPlatos(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["cargarSeguridades"])))
{
    $lc_condiciones[0] = htmlspecialchars($_GET["opcion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["plu_id"]);	
    $lc_condiciones[2] = $cadena;
    $lc_condiciones[3] = htmlspecialchars($_GET["usuario"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["prf_id"]);
    print $lc_config->fn_cargarSeguridades(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["guardarSeguridades"])))
{
    $lc_condiciones[0] = htmlspecialchars($_GET["opcion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["plu_id"]);	
    $lc_condiciones[2] = $cadena;
    $lc_condiciones[3] = htmlspecialchars($_GET["usuario"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["prf_id"]);
    print $lc_config->fn_guardarSeguridades(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["cargarUsuarioRecetas"])))
{
    $lc_condiciones[0] = $cadena;
    $lc_condiciones[1] = $usuario;	
    print $lc_config->fn_cargarUsuarioRecetas(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["sincronizarproductos"])))
{
    $lc_condiciones[0] = htmlspecialchars($_GET["cadena"]);	
    print $lc_config->fn_sincronizarproductos(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["cargarTipoproducto"])))
{
    $lc_condiciones[0] = htmlspecialchars($_GET["opcion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["plu_id"]);
    $lc_condiciones[2] = $cadena;
    $lc_condiciones[3] = htmlspecialchars($_GET["usuario"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["tipoproducto"]);
    print $lc_config->fn_cargarTipoproducto(array_map('specialChars', $lc_condiciones));
}

if(htmlspecialchars(isset($_GET["guardarTipoproducto"])))
{
    $lc_condiciones[0] = htmlspecialchars($_GET["opcion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["plu_id"]);
    $lc_condiciones[2] = $cadena;
    $lc_condiciones[3] = htmlspecialchars($_GET["usuario"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["tipoproducto"]);
    print $lc_config->fn_guardarTipoproducto(array_map('specialChars', $lc_condiciones));
}

