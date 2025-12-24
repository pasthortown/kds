//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Darwin Mora ////////////////////////
////////DESCRIPCION: Js pagina index de administracion ///////
///////FECHA CREACION: 04-07-2015/////////////////////////////
//////FECHA ULTIMA MODIFICACION: /////////////////////////////
///////MODIFICADO POR: ///////////////////////////////////////
//////DESCRIPCION: ///////////////////////////////////////////
//////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 

/*-------------------------------------------------------
 Funcion que se ejecuta al cargar la pantalla
 -------------------------------------------------------*/

$(document).ready(function () {

    //Dar el foco al campo de usuario
    $('#txtUsuario').focus();

    //Cambiar de campo txtclave al precionar Enter
    $('#txtUsuario').keypress(function (e) {
        if (e.which == 13) {
            $('#txtClave').focus();
        }
    });

    //Enviar formulario al precionar Enter
    $('#txtClave').keypress(function (e) {
        if (e.which == 13) {
            valida_envia();
        }
    });

});



function valida_envia() {

    if ($("#txtUsuario").val() == '')
    {
        alertify.alert("Usted debe ingresar su Usuario de acceso");
        $("#txtUsuario").focus();

    } else if ($("#txtClave").val() == '')
    {
        alertify.alert("Usted debe ingresar su Contrase" + '\u00f1' + "a de acceso");
        $("#txtClave").focus();

    } else
    {
        document.frmAccesoAdm.submit();
    }
}
;


/*-------------------------------------------------------
 Funcion para la pantalla para seleccionar el restaurante
 -------------------------------------------------------*/
function envia_home(cdn_id) {

    $('#selrestaurante').val(cdn_id);
    send = {"nombrelocalporcadena": 1};
    send.cdn_id = cdn_id;
    $.getJSON("../inicio/config_mantenimiento.php", send, function (datos) {
        document.frmAccesoUser.submit();
    });

}
;
lc_variable = document.getElementById("txtclavenue");
(typeof (window[lc_variable]) == "undefined") ? false : true;

if (lc_variable) {
    document.getElementById("txtclavenue").focus();
}

function retornar_index() {
    location.href = "../../mantenimiento/index.php";
}


