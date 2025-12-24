<?php

/* * *******************************************************
 *          DESARROLLADO POR: Alex Merino                *
 *          DESCRIPCION: Clase negocio empresa           *
 *          FECHA CREACION: 14/04/2018                   *
 * ******************************************************* */

include("../../system/conexion/clase_sql.php");
include("../../clases/clase_adminEmpresa.php");

$lc_objeto = new empresa();
$lc_cadena = $_SESSION['cadenaId'];
$lc_usuario = $_SESSION['usuarioId'];

if (isset($_GET["infTipoAmbiente"])) {
    $lc_condiciones[0] = 4; //Accion 1 del procedimiento almacenado  
    $lc_condiciones[1] = 1;
    print $lc_objeto->fn_consultar("infTipoAmbiente", $lc_condiciones);
} else
    if (isset($_GET["infTipoEmision"])) {
        $lc_condiciones[0] = 5; //Accion 1 del procedimiento almacenado
        $lc_condiciones[1] = 1;
        print $lc_objeto->fn_consultar("infTipoEmision", $lc_condiciones);
    } else
        if (isset($_GET["lstEmpresas"])) {
            $lc_condiciones[0] = 1; //Accion 1 del procedimiento almacenado
            $lc_condiciones[1] = htmlspecialchars($_GET['pa_id']);
            print $lc_objeto->fn_consultar("lstEmpresas", $lc_condiciones);
        } else
            if (isset($_GET["infPais"])) {
                $lc_condiciones[0] = 2; //Accion 2 del procedimiento almacenado
                $lc_condiciones[1] = htmlspecialchars($lc_cadena);
                print $lc_objeto->fn_consultar("infPais", $lc_condiciones);
            } else
                if (isset($_GET["infEmpresa"])) {
                    $lc_condiciones[0] = 3; //Accion 3 del procedimiento almacenado
                    $lc_condiciones[1] = htmlspecialchars($_GET['id_Empresa']);
                    print $lc_objeto->fn_consultar("infEmpresa", $lc_condiciones);

                } else
                    if (isset($_GET["guardarDatosModificadosEmpresa"])) {
                        $lc_condiciones[0] = htmlspecialchars($_GET['idEmpresa']);
                        $lc_condiciones[1] = htmlspecialchars($_GET['ruc']);
                        $lc_condiciones[2] = htmlspecialchars($_GET['nombre']);
                        $lc_condiciones[3] = htmlspecialchars($_GET['ciudad']);
                        $lc_condiciones[4] = htmlspecialchars($_GET['direccion']);
                        $lc_condiciones[5] = htmlspecialchars($_GET['razonSocial']);
                        $lc_condiciones[6] = htmlspecialchars($_GET['telefono']);
                        $lc_condiciones[7] = htmlspecialchars($_GET['tipoContribuyente']);
                        $lc_condiciones[8] = htmlspecialchars($_GET['resolucion']);
                        $lc_condiciones[9] = htmlspecialchars($_GET['fechRes']);
                        $lc_condiciones[10] = $lc_usuario;
                        $lc_condiciones[11] = htmlspecialchars($_GET['bandera']); // Puede ser 1->Activo || 2-> Inactivo
                        $lc_condiciones[12] = htmlspecialchars($_GET['tipEmision']);
                        $lc_condiciones[13] = htmlspecialchars($_GET['tipAmbiente']);
                        $lc_condiciones[14] = htmlspecialchars($_GET['contabilidad']);
                        print $lc_objeto->fn_ingresarAutoImpresora("guardarDatosModificadosEmpresa", $lc_condiciones);
                    } else
                        if (isset($_GET["CargarColeccionEmpresa"])) {
                            $lc_condiciones[0] = 1;//accion 1 cargar colecciones de Empresa
                            $lc_condiciones[1] = htmlspecialchars($_GET['id_Empresa']);
                            print $lc_objeto->fn_consultar("cargarColeccionEmpresa", $lc_condiciones);
                        } else
                            if (isset($_GET["ListarColeccionxEmpresa"])) {
                                $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
                                $lc_condiciones[1] = htmlspecialchars($_GET['empresa']);
                                $lc_condiciones[2] = htmlspecialchars($_GET['lc_IDColeccionEmpresa_edit']);
                                $lc_condiciones[3] = htmlspecialchars($_GET['lc_IDColeccionDeDatosEmpresa_edit']);
                                print $lc_objeto->fn_consultar("ListarColeccionEmpresa", $lc_condiciones);

                            } else if (htmlspecialchars(isset($_GET["modificarEmpresaColeccion"]))) {
                                $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
                                $lc_condiciones[1] = htmlspecialchars($_GET["lc_IDColeccionEmpresa_edit"]);
                                $lc_condiciones[2] = htmlspecialchars($_GET["lc_IDColeccionDeDatosEmpresa_edit"]);
                                $lc_condiciones[3] = htmlspecialchars($_GET["varchar"]);
                                $lc_condiciones[4] = htmlspecialchars($_GET["entero"]);
                                $lc_condiciones[5] = htmlspecialchars($_GET["fecha"]);
                                $lc_condiciones[6] = htmlspecialchars($_GET["seleccion"]);
                                $lc_condiciones[7] = htmlspecialchars($_GET["numerico"]);
                                $lc_condiciones[8] = htmlspecialchars($_GET["fecha_inicio"]);
                                $lc_condiciones[9] = htmlspecialchars($_GET["fecha_fin"]);
                                $lc_condiciones[10] = htmlspecialchars($_GET["minimo"]);
                                $lc_condiciones[11] = htmlspecialchars($_GET["maximo"]);
                                $lc_condiciones[12] = htmlspecialchars($_GET["IDUsuario"]);
                                $lc_condiciones[13] = htmlspecialchars($_GET["estado"]);
                                print $lc_objeto->fn_consultar("modificarEmpresaColeccion", $lc_condiciones);
                            } else if (htmlspecialchars(isset($_GET["detalleColeccionEmpresa"]))) {
                                $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
                                $lc_condiciones[1] = htmlspecialchars($_GET['empresa']);
                                print $lc_objeto->fn_consultar("detalleColeccionEmpresa", $lc_condiciones);
                            } else if (htmlspecialchars(isset($_GET["datosColeccionEmpresa"]))) {
                                $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
                                $lc_condiciones[1] = htmlspecialchars($_GET['empresa']);
                                $lc_condiciones[2] = htmlspecialchars($_GET['IDColeccionEmpresa']);
                                print $lc_objeto->fn_consultar("datosColeccionEmpresa", $lc_condiciones);

                            } else if (htmlspecialchars(isset($_GET["insertarEmpresaColeccion"]))) {
                                $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
                                $lc_condiciones[1] = htmlspecialchars($_GET["IDColecciondeDatosEmpresa"]);
                                $lc_condiciones[2] = htmlspecialchars($_GET["IDColeccionEmpresa"]);
                                $lc_condiciones[4] = htmlspecialchars($_GET["varchar"]);
                                $lc_condiciones[5] = htmlspecialchars($_GET["entero"]);
                                $lc_condiciones[6] = htmlspecialchars($_GET["fecha"]);
                                $lc_condiciones[7] = htmlspecialchars($_GET["seleccion"]);
                                $lc_condiciones[8] = htmlspecialchars($_GET["numerico"]);
                                $lc_condiciones[9] = htmlspecialchars($_GET["fecha_inicio"]);
                                $lc_condiciones[10] = htmlspecialchars($_GET["fecha_fin"]);
                                $lc_condiciones[11] = htmlspecialchars($_GET["minimo"]);
                                $lc_condiciones[12] = htmlspecialchars($_GET["maximo"]);
                                $lc_condiciones[13] = htmlspecialchars($_GET["IDUsuario"]);
                                $lc_condiciones[14] = 0;
                                print $lc_objeto->fn_consultar("insertarEmpresaColeccion", $lc_condiciones);
                            }


?>