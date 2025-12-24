<?php
session_start();

///////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR JOSE FERNANDEZ////////////////////////////////////
///////////DESCRIPCION: MENU DE PANTALLA DE CONFIGURACION DE PROMOCIONES/////
////////FECHA CREACION: 02/04/2014// ///////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

include_once("../../system/conexion/clase_sql.php");
include_once("../../clases/clase_seguridades.php");

$lc_idusuario = $_SESSION['usuarioId'];
$lc_obj = new seguridades();
$lc_accion = $_GET['accion'];
$lc_nomPantalla = 'configuracion_estacion.php';
$lc_resultado_todo = $lc_obj->fn_botones($lc_idusuario, $lc_nomPantalla, "todo");
?>
<table align="center" border="0" cellpadding="0" cellspacing="0" >
    <tr>  
        <td>
            <?php
            if ($lc_accion == 'Cancelar') {
                if ($lc_resultado_todo == 1) {
                    echo '<a href="#" onclick="fn_accionar(' . "'Nuevo'" . ')" border=0><img name="Nuevo" src="../../imagenes/botones/btnAgregar.png" border=0 class="solo_bordes"></a>';
                } else {
                    $lc_resultado_evento = $lc_obj->fn_botones($lc_idusuario, $lc_nomPantalla, "nuevo");
                    if ($lc_resultado_evento == 1) {
                        echo '<a href="#" onclick="fn_accionar(' . "'Nuevo'" . ')" border=0><img name="Nuevo" src="../../imagenes/botones/btnAgregar.png" border=0 class="solo_bordes"></a>';
                    } else {
                        echo '<img name="Nuevo" src="../../imagenes/botones/btnAgregarHide.png" border=0  class="solo_bordes">';
                    }
                }
            } else {
                echo '<img name="Nuevo" src="../../imagenes/botones/btnAgregarHide.png" border=0  class="solo_bordes">';
            }
            ?>
        </td>

        <td>
            <?php
            if ($lc_accion == 'Nuevo' || $lc_accion == 'Modificar') {
                if ($lc_resultado_todo == 1) {
                    echo '<a href="#" onclick="fn_accionar(' . "'Grabar'" . ')" border=0><img name="Grabar" src="../../imagenes/botones/btnGuardar.png" border=0 class="solo_bordes"></a>';
                } else {
                    $lc_resultado_evento = $lc_obj->fn_botones($lc_idusuario, $lc_nomPantalla, "grabar");
                    if ($lc_resultado_evento == 1) {
                        echo '<a href="#" onclick="fn_accionar(' . "'Grabar'" . ')" border=0><img name="Grabar" src="../../imagenes/botones/btnGuardar.png" border=0 class="solo_bordes"></a>';
                    } else {
                        echo '<img name="Grabar" src="../../imagenes/botones/btnGuardarHide.png" border=0  class="solo_bordes">';
                    }
                }
            } else {
                echo '<img name="Grabar" src="../../imagenes/botones/btnGuardarHide.png" border=0  class="solo_bordes">';
            }
            ?>          
        </td>

        <td >
<?php
if ($lc_accion == 'Cancelar') {
    if ($lc_resultado_todo == 1) {
        echo '<a href="#" onclick="fn_accionar(' . "'Modificar'" . ')"  border=0><img name="Modificar" src="../../imagenes/botones/btnModificar.png" border=0 class="solo_bordes"></a>';
    } else {
        $lc_resultado_evento = $lc_obj->fn_botones($lc_idusuario, $lc_nomPantalla, "modificar");
        if ($lc_resultado_evento == 1) {
            echo '<a href="#" onclick="fn_accionar(' . "'Modificar'" . ')" border=0><img name="Modificar" src="../../imagenes/botones/btnModificar.png" border=0 class="solo_bordes"></a>';
        } else {
            echo '<img name="Modificar" src="../../imagenes/botones/btnModificarHide.png" border=0  class="solo_bordes">';
        }
    }
} else {
    echo '<img name="Modificar" src="../../imagenes/botones/btnModificarHide.png" border=0  class="solo_bordes">';
}
?>
        </td>  

        <td >
<?php
if ($lc_accion == 'Nuevo' || $lc_accion == 'Modificar') {
    if ($lc_resultado_todo == 1) {
        echo '<a href="#" name="Cancelar" onclick="fn_accionar(' . "'Cancelar'" . ')" border=0><img name="Cancelar" src="../../imagenes/botones/btnCancelar.png" border=0 class="solo_bordes"></a>';
    } else {
        $lc_resultado_evento = $lc_obj->fn_botones($lc_idusuario, $lc_nomPantalla, "cancelar");
        if ($lc_resultado_evento == 1) {
            echo '<a href="#" name="Cancelar" onclick="fn_accionar(' . "'Cancelar'" . ')" border=0><img name="Cancelar" src="../../imagenes/botones/btnCancelar.png" border=0 class="solo_bordes"></a>';
        } else {
            echo '<img name="Cancelar" src="../../imagenes/botones/btnCancelarHide.png"  border=0  class="solo_bordes">';
        }
    }
} else {
    echo '<img name="Cancelar" src="../../imagenes/botones/btnCancelarHide.png"  border=0  class="solo_bordes">';
}
?>                
        </td>

    </tr> 
</table>