/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/* global parseFloat */

$(document).ready(function () {
    $('#mdlPrincipal').hide();
});

function validaProductoUpSelling(idProducto, magp_id, pvp, productoBase, jsonProductos) {
    const modal = document.getElementById('mdlPrincipal');
    modal.setAttribute('data-menu-id',$("#hide_menu_id").val());
    modal.setAttribute('data-clasificacion-id',$("#hid_cla_id").val());
    modal.setAttribute('data-categoria-id',rst_categoria);
    modal.setAttribute('data-cadena',$("#hide_cdn_id").val());
    modal.setAttribute('data-restaurante',$("#hide_rst_id").val());
    modal.setAttribute('data-producto-base',productoBase);
    modal.setAttribute('data-producto',idProducto);

    $("#hide_idProductoBase").val(idProducto);
    $("#hide_magp_id").val(magp_id);

    $('#mdlPrincipal').show();
    $('#mdlUpSelling').show();
    const consulta = fn_consulApiUpselling();
    if(consulta){
        if (typeof window.openModalAndLoadData === 'function') {
            window.openModalAndLoadData();
        } else {
            cancelarPreguntaSegerida({});
        }
    } else {
        cancelarPreguntaSegerida({});
        
    }
}

function validadPreguntaSegerida(magp_id, idProducto, idProductoBase, validador, nombreProducto, cantidad, jsonUpselling) {

    $('#mdlPrincipal').hide();
    $('#mdlUpSelling').hide();
    $("#hide_upselling").val(jsonUpselling);

    if (validador > 0) {
        fn_ejecutaValidaciones(magp_id, idProducto, idProductoBase);
    } else {
        fn_verificarPreguntaSugeridaUpselling(magp_id, idProducto, 0, idProductoBase, false, nombreProducto, cantidad);
    }
}

function cancelarPreguntaSegerida(jsonUpselling) {

    $('#mdlPrincipal').hide();
    $('#mdlUpSelling').hide();
    $("#hide_upselling").val(jsonUpselling);

    var idProducto = $("#hide_idProductoBase").val();
    var magp_id = $("#hide_magp_id").val();

    fn_verificarPreguntaSugerida(magp_id, idProducto, 0, 0);
}

window.validadPreguntaSegerida = validadPreguntaSegerida;
window.cancelarPreguntaSegerida = cancelarPreguntaSegerida;