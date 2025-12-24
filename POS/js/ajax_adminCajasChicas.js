////////////////////////////////////////////////////////////////////////////////////
//////DESARROLLADO POR: ANDRES ROMERO///////////////////////////////////////////
///////////DESCRIPCION: DESMONTAR CAJERO ///////////////////////////////////////////
////////FECHA CREACION: 13/10/2015//////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////

/* 
 * HISTORIAL DE CAMBIOS:
    10/01/2017 Francisco Sierra
        Se incorpora el proceso de transferencia de venta 
    20/04/2018 Juan Esteban Canelos
        Se incorpora animación de carga al realizar interface
*/
/* global parseFloat */

var cajasChicas = [];
var cajasChicas_tabla = [];
var cajas_chicas_detalles = [];

$(document).ready(function () {
    $('#fecha_inicio').daterangepicker({singleDatePicker: true, format: 'YYYY-MM-DD', drops: 'down'}, function (start, end, label) {
        //console.log(start.toISOString(), end.toISOString(), label);
    });
    $('#fecha_fin').daterangepicker({singleDatePicker: true, format: 'YYYY-MM-DD', drops: 'down'}, function (start, end, label) {
        //console.log(start.toISOString(), end.toISOString(), label);
    });
});

function cargar_URL(tipoTransaccion) {
    let urlToReturn;
    let send={
        ruta_servidor: 1,
        tipoTransaccion: tipoTransaccion
    }
    $.ajax({
        async: false,
        type: "GET",
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded",
        url: "./../adminCajasChicas/config_adminCajasChicas.php",
        data: send,
        success: function( datos ) {
            urlToReturn = datos[0].urlwebservice;
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('Error: ', JSON.stringify(jqXHR));
            alertify.error("No se pudo completar la consulta.");
            html_tabla ='<tr><td colspan="2">No se pudo completar la consulta.</td></tr>';
            $( "#lista_CajasChicas" ).html(html_tabla);
        }
    });        
    return urlToReturn;

}

function buscar_cajasChicas() {
    let contenedor_cajasChicas = document.getElementById("contenedor_cajasChicas");
    contenedor_cajasChicas.style.display = "none";
    //api= /api/caja-chica/consulta
    let url = cargar_URL('CONSULTAR')
    let send = {
        cargarListaCajasChicas: 1,
        fecha_inicio: $("#fecha_inicio").val(),
        fecha_fin: $("#fecha_fin").val(),
        url: url 
    }
    $.ajax({
        async: false,
        type: "POST",
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded",
        url: "./../adminCajasChicas/config_adminCajasChicas.php",
        data: send,
        success: function( datos ) {
            cajasChicas = datos;
            show_CajasChicas();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('Error: ', JSON.stringify(jqXHR));
            alertify.error("No se pudo completar la consulta.");
            html_tabla ='<tr><td colspan="2">No se pudo completar la consulta.</td></tr>';
            $( "#lista_cajasChicas" ).html(html_tabla);
            }
    });

    contenedor_cajasChicas.style.display = "block";

}

function show_CajasChicas() {
    cajasChicas_tabla = [];
    cajasChicas.forEach(element_income => {
        let existe = false;
        cajasChicas_tabla.forEach(element_table => {
            if (element_income.Cod_CierreChica == element_table.Cod_CierreChica) {
                existe = true;
            }
        });
        if (!existe) {
            cajasChicas_tabla.push({Cod_CierreChica: element_income.Cod_CierreChica, fechaDesde: element_income.Fecha_desde, fechaHasta: element_income.Fecha_hasta, ValorTotal:0});
        }
    });

    cajasChicas_tabla.forEach(cajaChica => {
        cajasChicas.forEach(element => {
            if (element.Cod_CierreChica == cajaChica.Cod_CierreChica){
                cajaChica.ValorTotal += Number(element.Total);
            }
        })
        
    })
    
    let html_tabla = '';
    console.log('tabla: ', cajasChicas_tabla)
    cajasChicas_tabla.forEach(element => {
        html_tabla += '<tr ondblclick="show_detalles_caja_chica(\''+element.Cod_CierreChica+'\')"><td>'+element.fechaDesde+'</td><td>'+element.fechaHasta+'</td><td>'+element.ValorTotal+'</td></tr>';
    });
    if (html_tabla) {
        $( "#lista_cajasChicas" ).html(html_tabla);
        console.log(1);
    } else {
        console.log(2);
        html_tabla ='<tr><td colspan="2">No se encontraron registros</td></tr>';
        $( "#lista_cajasChicas" ).html(html_tabla);
    }
}

function show_detalles_caja_chica(Cod_CierreChica) {
    cajas_chicas_detalles = [];
    cajasChicas.forEach(element => {
        if (element.Cod_CierreChica == Cod_CierreChica) {
            cajas_chicas_detalles.push(element);
        }
    });
    show_cajas_chicas(Cod_CierreChica);
}

function show_cajas_chicas(Cod_CierreChica) {
    let bodyModal = '<h4><b>Rubros:</b></h4><table class="table"><thead><tr>';
    bodyModal += '<th># Caja Chica</th><th>Rubro</th><th>Fecha</th><th>Total</th><th>Código Cierre Caja Chica</th><th>Código Cierre</th></tr></thead><tbody>';
    cajas_chicas_detalles.forEach(element => {
        if (element.Num_CajaChica != null){
            bodyModal += '<tr><td>'+element.Num_CajaChica+'</td><td>'+element.DescripcionRubro+'</td><td>'+element.Fecha+'</td><td>'+element.Total+'</td><td>'+element.Cod_CierreChica+'</td><td>'+element.Cod_Cierre+'</td></tr>';
        }
    });
    bodyModal +='</tbody></table>';
    bodyModal += '<h4><b>Compras de caja Chica:</b></h4><table class="table"><thead><tr>';
    bodyModal += '<th># Mov. Inv.</th><th>Comentario</th><th>Fecha</th><th>Total</th><th>Código Cierre Caja Chica</th><th>Código Cierre</th></tr></thead><tbody>';
    cajas_chicas_detalles.forEach(element => {
        if (element.Cod_Mov_Inv != null){
            bodyModal += '<tr><td>'+element.Cod_Mov_Inv+'</td><td>'+element.Comentario_MovInv+'</td><td>'+element.Fecha+'</td><td>'+element.Total+'</td><td>'+element.Cod_CierreChica+'</td><td>'+element.Cod_Cierre+'</td></tr>';
        }
    });
    bodyModal +='</tbody></table>';

    let buttonsModal = "<div class='col-xs-2'><button type='button' id='btn_desrelacionarCajaChica' class='btn btn-danger' onclick='btn_desrelacionarCajaChica()' ><span class='glyphicon glyphicon-usd' aria-hidden='true'></span>&nbsp;Des-relacionar Caja Chica</button></div>";
    buttonsModal += "<div class='col-md-offset-9'><button type='button' onclick='fn_cerraModal();' data-dismiss='modal' class='btn btn-default'>Cancelar</button></div>";
    build_modal('Rubros y compras de Cierre Chica: ' + Cod_CierreChica, bodyModal, buttonsModal,  '');
}

function btn_desrelacionarCajaChica (){
    let listaCajasChicas=[];
    let listaCodCierreChica = [];
    let listaMovInv=[];

    cajas_chicas_detalles.forEach(cajaChica => {
        let existe = false;
        listaCodCierreChica.forEach(item => {
            if (cajaChica.Cod_CierreChica == item) {
                existe = true;
            }
        });
        if (!existe) {
            listaCodCierreChica.push(cajaChica.Cod_CierreChica);
        }
    });
    listaCodCierreChica=listaCodCierreChica.join();

    cajas_chicas_detalles.forEach(cajaChica => {
        if (cajaChica.Num_CajaChica != null){
            listaCajasChicas.push(cajaChica.Num_CajaChica);
        }
    });
    listaCajasChicas=listaCajasChicas.join()

    cajas_chicas_detalles.forEach(cajaChica => {
        if (cajaChica.Cod_Mov_Inv != null){
            listaMovInv.push(cajaChica.Cod_Mov_Inv);
        }
    });
    listaMovInv=listaMovInv.join()
    //api = /api/caja-chica/desrelacionar
    let url = cargar_URL('DESRELACIONAR')

    let send = {
        desrelacionarCajasChicas: 1,
        url: url,
        listaCodCierreChica: listaCodCierreChica,
        listaCajasChicas: listaCajasChicas,
        listaMovInv:listaMovInv
    }

    $.ajax({
        async: false,
        type: "POST",
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded",
        url: "./../adminCajasChicas/config_adminCajasChicas.php",
        data: send,
        success: function( datos ) {
            alertify.success("Caja chica desrelacionada correctamente.");
            buscar_CajasChicas();
            fn_cerraModal();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('Error: ', JSON.stringify(jqXHR));
            alertify.error("No se pudo completar la operación");
            buscar_CajasChicas();
            fn_cerraModal();
        }
    });

}

function consultar_localizacion(){
    let localizacion;
    $.ajax({
        async: false,
        type: "POST",
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded",
        url: "./../adminCajasChicas/config_adminCajasChicas.php",
        data: {consultar_localizacion:1},
        success: function( datos ) {
            localizacion = datos[0].localizacion;
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('Error: ', JSON.stringify(jqXHR));
            alertify.error("No se pudo completar la operación");
        }
    });
    return localizacion;
}

function build_modal(tituloModal, cuerpoModal, botonesModal, notaModal) {
    $( "#tituloModal" ).html(tituloModal);
    $( "#cuerpoModal" ).html(cuerpoModal);
    $( "#botonesModal" ).html(botonesModal);
    $( "#notaModal" ).html(notaModal);
    $("#ModalCajasChicas").modal('show');
}

function fn_cerraModal() {
    $("#ModalCajasChicas").modal('hide');
}

function ejemplo() {
    alert(1);
}

