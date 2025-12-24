/* global alertify */

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Darwin Mora ////////////////////////////////////////////
///////DESCRIPCION: mostrar el trafico de la pagina de inicio//////////////////
///////TABLAS INVOLUCRADAS: Menu, /////////////////////////////////////////////
///////FECHA CREACION: 20-02-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////	

var usr_id = 0;
var pnt_Id = 0;
var ruta;
var lc_std = -1;

$(document).ready(function () {
    //Funcion Cargando
    fn_cargando(1);

    //proceso sincronico false
    $.ajaxSetup({async: false});

    //Incluir menu superior
    fn_cargar_menu_superior();

    //recuperar el ID del usuario
    usr_id = $("#txt_usuario").val();

    //proceso sincronico false
    $.ajaxSetup({async: true});

    //Fin Funcion Cargando
    fn_cargando(0);
    fn_cargarRestaurantes();
});

/*-------------------------------------------------------
 Funcion para capturar el trafico del ingreso por usuario
 -------------------------------------------------------*/
function fn_captura_trafico(pnt_id, usr_id) {

    console.log(pnt_id);
    fn_cargando(1);
    $.ajaxSetup({async: false});

    $.ajaxSetup({cache: false});
    
    var sitio = obtenerRuta();

    var ruta = "/" + sitio + "/" + $("#" + pnt_id).attr("name");
    console.log(ruta);

    $("#incluirPagina").load(ruta);

    $.ajaxSetup({async: true});
    send = {"capturarTrafico": 1};
    send.pnt_id = pnt_id;
    send.usr_id = usr_id;
    $.getJSON("../inicio/config_mantenimiento.php", send, function (datos) {
        if (datos.str > 0) {
            ctra_id = datos[0]["ctra_id"];
        }
    });
    $.ajaxSetup({async: true});
    fn_cargando(0);
}

function obtenerRuta() {
    var url = window.location.href;
    var partes = url.split('/');

    // Verificar si hay un segmento adicional en la ruta en Windows
    if (partes[3] !== '') {
        // Estamos en Windows
        return partes.slice(3, partes.indexOf("mantenimiento") + 1).join('/');
    } else {
        // Estamos en Linux
        return "mantenimiento";
    }
}

/*-------------------------------------------------------
 Funcion para mostrar el trafico del ingreso por usuario
 -------------------------------------------------------*/
function fn_muestra_trafico(pnt_id, usr_id) {
    var muestraTrafico = {"muestraTrafico": 1};
    var html = "";

    send = muestraTrafico;
    send.pnt_id = pnt_id;
    send.usr_id = usr_id;
    $.getJSON("../inicio/config_mantenimiento.php", send, function (datos) {
        if (datos.str > 0) {
            html = "<div class='tile-container bd-lime' style='width: 390px;'>";
            for (var i = 0; i < datos.str; i++) {
                if (i == 0) {
                    html = html + "<div data-role='tile' class='tile bg-red fg-white'><div class='tile-content iconic'><span class='icon mif-cogs mif-lg'></span><span class='tile-label'>" + datos[i]['pnt_Nombre_Mostrar'] + "</span></div></div>";
                } else if (i == 1) {
                    html = html + "<div data-role='tile' class='tile-small bg-darkGreen fg-white'><div class='tile-content iconic'><span class='icon mif-cogs mif-lg'></span><span class='tile-label'>" + datos[i]['pnt_Nombre_Mostrar'] + "</span></div></div>";
                } else if (i == 2) {
                    html = html + "<div data-role='tile' class='tile-small bg-cyan fg-white'><div class='tile-content iconic'><span class='icon mif-envelop mif-lg'></span><span class='tile-label'>" + datos[i]['pnt_Nombre_Mostrar'] + "</span></div></div>";
                } else if (i == 3) {
                    html = html + "<div data-role='tile' class='tile-small bg-green fg-white'><div class='tile-content iconic'><span class='icon mif-home mif-lg'></span><div class='tile-label'>" + datos[i]['pnt_Nombre_Mostrar'] + "</div></div></div>";
                } else if (i == 4) {
                    html = html + "<div data-role='tile' class='tile-small bg-darkGreen fg-white'><div class='tile-content iconic'><span class='icon mif-chart-dots mif-lg'></span><span class='tile-label'>" + datos[i]['pnt_Nombre_Mostrar'] + "</span></div></div>";
                }
            }
            html = html + "</div>";
            $("#tabla").append(html);
        } else {
            $("#id_atajo").hide();
        }
    });
}

/*-------------------------------------------------------
 Funcion para mostrar pantalla de espera (Cargando)
 -------------------------------------------------------*/
function fn_cargando(estado) {
    if (estado) {
        $("#cargando").css("display", "block");
        $("#cargandoimg").css("display", "block");
    } else {
        $("#cargando").css("display", "none");
        $("#cargandoimg").css("display", "none");
    }
}

/*-------------------------------------------------------
 Funcion para mostrar pantalla de espera (Cargando)
 -------------------------------------------------------*/
function fn_cargar_menu_superior() {
    var muestra_menusuperior = {"muestra_menusuperior": 1};
    var html = "";
    send = muestra_menusuperior;
    $.getJSON("../inicio/config_mantenimiento.php", send, function (datos) {
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
                html = html + "<li><a href='#' id='menu_" + datos[i]["pnt_id"] + "' name='" + datos[i]["pnt_Ruta"] + "' onClick='fn_muestra_menu(\"" + datos[i]["pnt_id"] + "\")'><i class='" + datos[i]["pnt_Imagen"] + "'></i>" + datos[i]["pnt_Nombre_Mostrar"] + "</a></li>";
            }
            $("#menu_superior").append(html);
        }
    });
}

/*----------------------------------------
 //Funcion para mostrar el menu 
 ----------------------------------------*/
function fn_muestra_menu(pnt_id) {
    var ruta = $("#menu_" + pnt_id).attr("name");

    $.ajaxSetup({async: false});
    fn_cargando(1);
    var sitio = obtenerRuta();

    $("#incluirPagina").empty();
    $("#incluirPagina").load("/" + sitio + "/" + ruta);

    $.ajaxSetup({async: true});
    fn_cargando(0);
}

function fn_cargarRestaurantes() {
    var muestra_restaurantes = {"muestra_restaurantes": 1};
    send = muestra_restaurantes;
    $.getJSON("../inicio/config_mantenimiento.php", send, function (datos) {
        if (datos.str > 0) {
            $("#cmb_restaurante").html("<option selected  value='0'>Seleccionar Tienda</option>");
            var res = fn_seleccionaRestaurante();
            for (var i = 0; i < datos.str; i++) {
                var cadena = "<option value=" + datos[i]['rst_id'] + "";
                if (datos[i]['rst_id'] === res) {
                    cadena += " selected='selected' > ";
                } else {
                    cadena += "> ";
                }
                cadena += datos[i]['rst_cod_tienda'] + " " + datos[i]['rst_descripcion'] + "</option>";
                $("#cmb_restaurante").append(cadena);
            }

            // $("#cmb_restaurante").change(function () {
            //     var lc_rest = $("#cmb_restaurante").val();
            //    fn_cambiarRestaurante(lc_rest);
            // });
        }
    });
}

function fn_cambiarRestaurante() {
    var rstId = $("#cmb_restaurante").val();
    if (verificarRestaurante(rstId)) {
        var send = {"cambia_restaurante": 1};
        send.rstId = rstId;
        $.getJSON("../inicio/config_mantenimiento.php", send, function (datos) {
            alertify.success("Cambio de Restaurante");
            location.reload();
        });
    } else {
        alertify.error('Por favor seleccione un restaurante');
    }
}

function fn_seleccionaRestaurante() {
    var selecciona_restaurante = {"selecciona_restaurante": 1};
    send = selecciona_restaurante;
    var dato = 0;
    dato = $.getJSON("../inicio/config_mantenimiento.php", send, function (datos) {
    });
    return dato;
}

function clearjQueryCache() {
    for (var x in jQuery.cache) {
        delete jQuery.cache[x];
    }
    location.reload();
}

function sitioMaxpointxidRes(ip, rstId) {
    var send;
    send = {"CargarSitioMaxpoint": 1};
    send.rstId = rstId;
    send.SitioM = 'SITIO';
    $.getJSON("../inicio/config_mantenimiento.php", send, function (datos) {
        if (datos.str > 0) {
            window.open('http://' + ip + ':880/' + datos[0].Sitio + '/mantenimiento/index.php');//}else{
        } else {
            window.open('http://' + ip + ':880/pos/mantenimiento/index.php');//}else{
        }
    });
}

function RedireccionarLocal() {
    var rstId = $("#cmb_restaurante").val();
    if (verificarRestaurante(rstId)) {
        var send;
        send = {"CargarIps": 1};
        send.ambiente = 'onpremise';
        send.rstId = rstId;
        $.getJSON("../adminReplicacion/config_replicacion.php", send, function (datos) {
            if (datos.str > 0) {
                sitioMaxpointxidRes(datos[0].ip, rstId);
            } else {
                alertify.error('Error no encuentra la ip del local');
            }
        });
    } else {
        alertify.error('Por favor seleccione un restaurante');
    }
}

function verificarRestaurante(rstId) {
    return (rstId != 0);
}
