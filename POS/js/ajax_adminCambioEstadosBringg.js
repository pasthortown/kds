var tablaCambioEstadosBringg;
var tablaListaMotivos;
var tablaListaMotorolo;
var motorizadoUltimaMilla;
var globalProvider;
$(document).ready(function () {

     cargarPeriodoAbierto();
    cargaTablaCambioEstadosBringg();
});

var cargando = function( estado ) {
    if ( estado ) {
        $('#mdl_pcn_rdn_pdd_crgnd').css('display', 'block');
    } else {
        $('#mdl_rdn_pdd_crgnd').css('display', 'none');
    }
};

function cargarPeriodoAbierto() {
    cargando( true );
    var send = new Object;
    send['metodo'] = 'cargaPeriodoAbierto';

    $.ajax({
            async: true,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "../adminCambioEstadosBringg/config_cambioEstadosBringg.php",
            data: send,
            success: function ( response ) {

            if ( response.idPeriodo ) {
            $("#IDPeriodo").val(response.idPeriodo);
            $("#prd_fecha").html( response.fecha + ' '+ response.hora );
            $("#prd_usuario").html( response.usuario );

            } else {
                alertify.alert("No existe un periodo abierto.");
            }
    },
    error: function () {
        cargando( false );
        alert('Error al cargar periodo abierto...');
    }
});

};

function cargaTablaCambioEstadosBringg(){
    var send = new Object;
    send['metodo'] = 'cargaTablaCambioEstadosBringg';
    var html = '<thead ><tr><th align="center">Factura</th><th align="center">Codigo App</th><th>Cliente</th><th>Estado</th><th>Motorizado</th><th>Medio</th><th></th><th>Motivo</th></tr></thead>';

    $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminCambioEstadosBringg/config_cambioEstadosBringg.php",
        data: send,
        success: function (datos) {
            cargando( false );

            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {

                    html = html + '<tr><td align="center">' + datos[i]['codFactura'] + '</td> <td align="center">' + datos[i]['codigoApp'] + '</td>  <td  align="center">' + datos[i]['cliente'] + '</td><td  align="center">' + datos[i]['estado'] + '</td>';

    

                    if (datos[i]['estadoMotorizado'] == 'PENDIENTE') {
                        html = html + '<td  align="center"> <button type="button" onclick="cargaMotorolos(1,\'' + datos[i]['codigoApp'] + '\')" class="btn btn-primary" >Asignar</button> </td>';
                            $("#estado").val(datos[i]['estado']);
                            $("#codApp").val(datos[i]['codigoApp']);
                    } else{
                        html = html + ' <td  align="center">' + datos[i]['nombreMotorizado'] + '</td>';

                    }
                    html = html + ' <td  align="center">' + datos[i]['medio'] + '</td>';

                    if(datos[i]['estado'] == 'POR ASIGNAR'){
                        html = html + '<td  align="center"><i onclick="accionCambioEstados(this)" name = "' + datos[i]['codigoApp'] + ',' + datos[i]['estado'] +',' + datos[i]['nombreMotorizado'] +',' + datos[i]['motivo'] +',' + datos[i]['idMotorolo'] +',' + datos[i]['medio'] +'"  class="iconCambioEstado"><img src="../../imagenes/domicilio/PorAsignar.png" height="" /></i></td>';
                    }
                    if(datos[i]['estado'] == 'ASIGNADO'){
                        html = html + '<td  align="center"><i onclick="accionCambioEstados(this)" name = "' + datos[i]['codigoApp'] + ',' + datos[i]['estado'] +',' + datos[i]['nombreMotorizado'] +',' + datos[i]['motivo'] +',' + datos[i]['idMotorolo'] +',' + datos[i]['medio'] +'" class="iconCambioEstado"><img src="../../imagenes/domicilio/Asignado.png" /></i></td>';
                    }
                    if(datos[i]['estado'] == 'EN CAMINO'){
                        html = html + '<td  align="center"><i onclick="accionCambioEstados(this)" name = "' + datos[i]['codigoApp'] + ',' + datos[i]['estado'] +',' + datos[i]['nombreMotorizado'] +',' + datos[i]['motivo'] +',' + datos[i]['idMotorolo'] +',' + datos[i]['medio'] +'" class="iconCambioEstado"><img src="../../imagenes/domicilio/EnCamino.png" /></i></td>';
                    }
                     if(datos[i]['estado'] == 'RECIBIDO'){
                        html = html + '<td  align="center"><i onclick="accionCambioEstados(this)" name = "' + datos[i]['codigoApp'] + ',' + datos[i]['estado'] +',' + datos[i]['nombreMotorizado'] +',' + datos[i]['motivo'] +',' + datos[i]['idMotorolo'] +',' + datos[i]['medio'] +'" class="iconCambioEstado"><img src="../../imagenes/domicilio/Recibido.png" /></i></td>';
                    }
                    if(datos[i]['estado'] == 'EN SITIO'){
                        html = html + '<td  width="30px" align="center"><i onclick="accionCambioEstados(this)" name = "' + datos[i]['codigoApp'] + ',' + datos[i]['estado'] +',' + datos[i]['nombreMotorizado'] +',' + datos[i]['motivo'] +',' + datos[i]['idMotorolo'] +',' + datos[i]['medio'] +'" class="iconCambioEstado"><img src="../../imagenes/domicilio/EnSitio.png" /></i></td>';
                    }

                    html = html + ' <td  align="center">' + datos[i]['motivo'] + '</td>';

                    html = html + '</tr>';
                }

                $('#tablaCambioEstados').html(html);
                tablaCambioEstadosBringg = $('#tablaCambioEstados').dataTable({
                                            'destroy': true,
                                             'bInfo': false,
                                            "columnDefs": [

                                                {
                                                    "targets": [ 7 ],
                                                    "visible": false,
                                                    "searchable": false
                                                }
                                            ]
                                            });


                $(".customSwitch").on('change',function (e, dt, type, indexes) {
                    var $this = $(this);
                    var codigoApp=$this.attr('name');
                    var row = $this.closest('tr');

                    if ($this.is(':checked')) {

                        cambioTipoAsignacion(codigoApp,'AUTOMATICA')
                    }else{

                        cambioTipoAsignacion(codigoApp,'MANUAL')
                    }

                });
            }else{
                $('#tablaCambioEstados').html(html);
                tablaCambioEstadosBringg = $('#tablaCambioEstados').dataTable({
                    'destroy': true,
                    'bInfo': false,
                    "columnDefs": [

                        {
                            "targets": [ 7 ],
                            "visible": false,
                            "searchable": false
                        }
                    ]
                });
            }
        }, error: function (xhr, ajaxOptions, thrownError) {
            alert(thrownError + " " + ajaxOptions + " " + thrownError);
        }
    });

}

function getIdMotolo(){
    var data = $(accion).attr('name');
    var array = data.split(',');
    var idMotorolo = array[4];
    return idMotorolo;
}

function accionUltimaMilla(accion){
    //console.log($(accion).attr('name'));
    var data = $(accion).attr('name');
    var array = data.split(',');
    var codigoApp = array[0];
    var estado = array[1];
    var motivo = array[3];
    var idMotorolo = array[4];
    var medio = array[5];

    $("#estado").val(estado);
    $("#codApp").val(codigoApp);
    $("#idMotorolo").val(idMotorolo);

    if(motivo == 0){
        cargaTablaMotivosCambioEstadosBringg('ultima milla', medio);
    }else{
        if (idMotorolo=="null") {
            cargaMotorolos(0,codigoApp);
        }
        else if(estado == 'POR ASIGNAR'){
            fn_asignarMotorizado();
        }
        cambioEstados(estado);
    }
}
function accionCambioEstados(accion){
    //console.log($(accion).attr('name'));

    var data = $(accion).attr('name');
    var array = data.split(',');
    var codigoApp = array[0];
    var estado = array[1];
    var motivo = array[3];
    var idMotorolo = array[4];
    var medio = array[5];

    $("#estado").val(estado);
    $("#codApp").val(codigoApp);
    $("#idMotorolo").val(idMotorolo);
    $("#medioMotorolo").val(medio);


    if(motivo == 0 ){
        cargaTablaMotivosCambioEstadosBringg();
    }else{
        if(estado == 'POR ASIGNAR')
            cargaMotorolos(0,codigoApp,medio);

        cambioEstados(estado);
    }
}



function cargaTablaMotivosCambioEstadosBringg() {

    var send = new Object;
    send['metodo'] = 'cargaTablaMotivosCambioEstadosBringg';
    var estado = $("#estado").val();

    var html = '<thead><tr align="center" ><th align="center" style="width: 40px;">Código</th><th align="center">IDMotivo</th><th align="left">Motivo</th></thead>';

    switch(estado) {
        case "ASIGNADO":
            $("#btnGurdaMotivo").html('Si, En Camino');

            break;
        case 'EN CAMINO':

            $("#btnGurdaMotivo").html('Si, Entregado');

            break;
        case 'EN SITIO':
            $("#btnGurdaMotivo").html('Si, Entregado');
            break;

    }

    $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminCambioEstadosBringg/config_cambioEstadosBringg.php",
        data: send,
        success: function (datos) {
           // console.log(datos);
            if(datos.str >0){
                for (i = 0; i < datos.str; i++) {
                    html = html + '<tr  id=' + datos[i]['num'] + ' onclick="fn_seleccionMotivos('+datos[i]['num']+',\''+ datos[i]['idMotivo'] +'\',this)"><td align="center">' + datos[i]['num'] + '</td><td align="center">' + datos[i]['idMotivo'] + '</td><td>' + datos[i]['descripcion'] + '</td>' ;
                }
                    html = html + '</tr>';
                $('#tablaMotivos').html(html);
                tablaListaMotivos = $('#tablaMotivos').dataTable({
                    'destroy': true,
                    'lengthChange': false,
                    'pageLength': 5,
                    'bInfo': false,
                    'select': true,
                    "columnDefs": [
                        {
                            "targets": [ 1 ],
                            "visible": false,
                            "searchable": false
                        }]
                });
                $('#mdl_Motivos').modal('show');

            }

        }, error: function (xhr, ajaxOptions, thrownError) {
            alert(thrownError + " " + ajaxOptions + " " + thrownError);
        }
    });
}
function cambioEstados(estado){

    var codigoApp = $("#codApp").val();
    var estado = $("#estado").val();
    var idMotivo = $("#idMotivo").val();
    var idPeriodo = $("#IDPeriodo").val();
    var idMotorolo = $("#idMotorolo").val();
    var accion ;

    switch(estado) {
        case "ASIGNADO":
            accion = 'Cambio Estado a En Camino';
            alertify.confirm(
                "¿Estas Seguro de colocar a EN CAMINO?",
                function (e) {
                    if (e) {

                        cambioEstadoEnCamino(codigoApp,estado,idPeriodo,idMotorolo);
                        guardaAuditoriaCambioEstado(codigoApp,estado,idMotivo,accion);

                    }
                }
            );

            break;
        case 'EN CAMINO':
            accion = 'Cambio Estado a Entregado';
            alertify.confirm("¿Estás seguro de colocar estas transacciones como ENTREGADADAS?",
                function (e) {
                    if (e) {
                        cambioEstadoEntregado(codigoApp,estado,idPeriodo,idMotorolo);
                        guardaAuditoriaCambioEstado(codigoApp,estado,idMotivo,accion);
                    }
                }
            );
            break;
        case 'En Sitio':

            break;

    }

}
function fn_seleccionMotivos(id,idMotivo,id){
    $("#idMotivo").val(idMotivo);
    $(id).addClass("colorRow");
}
async function fn_guardaMotivoCambiEstado(){

  var send = new Object;
  var codigoApp = $("#codApp").val();
  var estado = $("#estado").val();
  var idMotivo = $("#idMotivo").val();
  var idPeriodo = $("#IDPeriodo").val();
  var idMotorolo = $("#idMotorolo").val();
  var medio = $('#medioMotorolo').val();
  var accion ;

    var medio = $("#medio").val();
    if (medio) {
        // $("#idMotorolo").val(null);
        let riderId = await getRiderAgregador(medio);
        console.log("🚀 ~ riderId:", riderId)
        if (riderId) {
            $('#mdl_Motivos').modal('hide');
            $("#idMotorolo").val(riderId);
            alertify.confirm("¿Estás seguro de colocar estas transacciones como ENTREGADAS?", function (e) {
                if (e) {
                    // estado = 'ENTREGADO';
                    accion = 'Cambio Estado a Entregado';
                    cambioEstadoOrdenEntregada(codigoApp,riderId);
                    $("#idMotorolo").val(null);
                    // alertify.success('La orden ' + codigoApp + ' se encuentra ENTREGADA.');
                }
            });
            return false;
        }
    }

      switch(estado) {
        case "POR ASIGNAR":
            accion = "Cambio Estados Motivos";
            if(motorizadoUltimaMilla){
            $("#idMotorolo").val(motorizadoUltimaMilla);
                fn_asignarMotorizado();
            }else{
                cargaMotorolos(0,codigoApp, medio);
            }
            motorizadoUltimaMilla="";
            guardaAuditoriaCambioEstado(codigoApp,estado,idMotivo,accion);
            break;
        case "ASIGNADO":
            accion = 'Cambio Estado a En Camino';
            cambioEstadoEnCamino(codigoApp,estado,idPeriodo,idMotorolo);
            guardaAuditoriaCambioEstado(codigoApp,estado,idMotivo,accion);
            break;
        case 'EN CAMINO':
            accion = 'Cambio Estado a Entregado';
            cambioEstadoEntregado(codigoApp,estado,idPeriodo,idMotorolo);
            guardaAuditoriaCambioEstado(codigoApp,estado,idMotivo,accion);
            break;
        case 'EN SITIO':

            break;

    }

}

function cargaMotorolos(banderaMotorolo,codigoApp,medio=null){
    var send = new Object;
    var contador = 0;
    send['metodo'] = 'cargaMotorolos';
    send['idPeriodo'] = $("#IDPeriodo").val();
    send['medio'] = medio;

    $("#banderaMotorolo").val(banderaMotorolo);

    var html = '<thead><tr align="center"><th align="center">Código</th><th align="center">Motorizado</th><th>Codigo</th><th>#Pedidos</th></thead>';


    $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminCambioEstadosBringg/config_cambioEstadosBringg.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                for (i = 0; i < datos.str; i++) {
                    if (datos[i]["estado"] === "Asignado" &&
                        datos[i]["total"] < datos[i]["maximo_ordenes"]) {
                        html = html + '<tr class="cursor:pointer;" id=' + datos[i]['num'] + ' onclick="fn_seleccionMotorolo(\'' + datos[i]['idMotorizado'] + '\',this,\'' + codigoApp + '\')" >';
                        html = html + '<td align="center">' + datos[i]['idMotorizado'] + '</td>';
                        html = html + '<td  align="left">' + datos[i]['motorizado'] + '</td>';
                        html = html + '<td >' + datos[i]['documento'] + '</td>';
                        html = html + '<td align="center" >' + datos[i]['total'] + '/ ' + datos[i]['maximo_ordenes'] + '</td>';
                        contador++;
                    }
                    }


                $('#tablaMotorolo').html(html);


                tablaListaMotorolo = $('#tablaMotorolo').dataTable({
                    'destroy': true,
                    'lengthChange': false,
                    "language": {
                        "emptyTable": "No existen motorizados disponibles."
                    },
                    'pageLength': 5,
                    'bInfo': false,
                    // 'bFilter': false,
                    'select': true,
                    "columnDefs": [
                        {
                            "targets": [ 0,2 ],
                            "visible": false,
                            "searchable": false
                        }]
                });
                $('#mdl_Motorolos').modal('show');


            } else {
                $('#mdl_Motorolos').modal('show');
                $('#tablaMotorolo').html(html);
                $('#tablaMotorolo').dataTable({
                    'destroy': true,
                    'lengthChange': false,
                    "language": {
                        "emptyTable": "No existen motorizados disponibles."
                    },
                    'bInfo':  false,
                    'pagingType': 'numbers',
                    'select': true,
                    "columnDefs": [
                        {
                            "targets": [ 0,2 ],
                            "visible": false,
                            "searchable": false
                        }]
                });

            }




        }, error: function (xhr, ajaxOptions, thrownError) {
            alert(thrownError + " " + ajaxOptions + " " + thrownError);
        }
    });
}
function fn_seleccionMotorolo(idMotorolo,m,codigoApp){
    $("#idMotorolo").val(idMotorolo);
    $("#tablaMotorolo").removeClass("colorRow");
    $(m).addClass("colorRow");
    $("#codApp").val(codigoApp);
}
function fn_asignarMotorizado(){
    var send = new Object;
    var estado = $("#estado").val();
    var idMotivo = $("#idMotivo").val();
    var accion = 'Asignar Motorolo';
    var banderaMotorolo = $("#banderaMotorolo").val();
    var codigoApp = $("#codApp").val();
    send['metodo'] = 'fn_asignarMotorizado';
    send['idMotorolo'] = $("#idMotorolo").val();
    send['codigoApp'] = codigoApp;
    send['banderaMotorolo'] = banderaMotorolo;
    send['estado'] = estado;

    $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminCambioEstadosBringg/config_cambioEstadosBringg.php",
        data: send,
        success: function (datos) {
           // console.log(datos);
            $('#mdl_Motorolos').modal('hide');
            cargaTablaCambioEstadosBringg();
            if(datos[0]["estado"] == 1){
                alertify.success(datos[0]["mensaje"]);
            }else{
               alertify.error(datos[0]["mensaje"]);
            }
            guardaAuditoriaCambioEstado($("#codApp").val(),estado,idMotivo,accion)

        }, error: function (xhr, ajaxOptions, thrownError) {
            alert(thrownError + " " + ajaxOptions + " " + thrownError);
        }
    });
}
function cambioEstadoEnCamino(codigoApp,estado,idPeriodo,idMotorolo){

    var send = new Object;
    send['metodo'] = 'cambioEstadoEnCamino';
    send['idPeriodo'] = idPeriodo;
    send['idMotorolo'] = idMotorolo;
    send['codigoApp'] = codigoApp;

    $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminCambioEstadosBringg/config_cambioEstadosBringg.php",
        data: send,
        success: function (datos) {
            $('#mdl_Motivos').modal('hide');
            cargaTablaCambioEstadosBringg();
            if (datos[0]["estado"] === 1) {
                 alertify.success(datos[0]["mensaje"]);
            } else {
                alertify.error(datos[0]["mensaje"]);
            }

        }, error: function (xhr, ajaxOptions, thrownError) {
            alert(thrownError + " " + ajaxOptions + " " + thrownError);
        }
    });
}
function cambioEstadoEntregado(codigoApp,estado,idPeriodo,idMotorolo){

    var send = new Object;
    send['metodo'] = 'cambioEstadoEntregado';
    send['idPeriodo'] = idPeriodo;
    send['idMotorolo'] = idMotorolo;
    send['codigoApp'] = codigoApp;

    $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminCambioEstadosBringg/config_cambioEstadosBringg.php",
        data: send,
        success: function (datos) {
            $('#mdl_Motivos').modal('hide');
            cargaTablaCambioEstadosBringg();
            if (datos[0]["estado"] === 1) {
                alertify.success(datos[0]["mensaje"]);
            } else {
                alertify.error(datos[0]["mensaje"]);
            }

        }, error: function (xhr, ajaxOptions, thrownError) {
            alert(thrownError + " " + ajaxOptions + " " + thrownError);
        }
    });
}
function cambioEstadoOrdenEntregada(codigoApp,idMotorolo){

    var send = new Object;
    send['metodo'] = 'cambioEstadoOrdenEntregada';
    send['idMotorolo'] = idMotorolo;
    send['codigoApp'] = codigoApp;

    $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminCambioEstadosBringg/config_cambioEstadosBringg.php",
        data: send,
        success: function (datos) {
            $('#mdl_Motivos').modal('hide');
            cargaTablaCambioEstadosBringg();
            if (datos) {
                alertify.success('La orden ' + codigoApp + ' se encuentra ENTREGADA.');
            } else {
                alertify.error('No se pudo actualizar la orden');
            }
            $("#medio").val(null);
            $("#idMotorolo").val(null);

        }, error: function (xhr, ajaxOptions, thrownError) {
            alert(thrownError + " " + ajaxOptions + " " + thrownError);
        }
    });
}
function guardaAuditoriaCambioEstado(codigoApp,estado,idMotivo,accion){

    var send = new Object;
    send['metodo'] = 'guardaAuditoriaCambioEstado';
    send['codigoApp'] = codigoApp;
    send['estado'] = estado;
    send['idMotivo'] = idMotivo;
    send['accion'] = accion;
    $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminCambioEstadosBringg/config_cambioEstadosBringg.php",
        data: send,
        success: function (datos) {
            $('#mdl_Motivos').modal('hide');
            cargaTablaCambioEstadosBringg();
            if (datos[0]["estado"] === 1) {
                alertify.success("Motivo ingresado correctamente");
            } else {
                alertify.error("Ocurrio un error al ingresar el Motivo");
            }

        }, error: function (xhr, ajaxOptions, thrownError) {
            alert(thrownError + " " + ajaxOptions + " " + thrownError);
        }
    });

}

function cambioTipoAsignacion(codigoApp,tipoAsignacion){
    var send = new Object;
    send['metodo'] = 'cambioTipoAsignacion';
    send['codigoApp'] = codigoApp;
    send['tipoAsignacion'] = tipoAsignacion;

    $.ajax({
        async: true,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../adminCambioEstadosBringg/config_cambioEstadosBringg.php",
        data: send,
        success: function (datos) {
            cargaTablaCambioEstadosBringg();
            if(datos == 1) {
                alertify.success("Su pedio se cambio a MANUAL");
            }else{
                    alertify.error("Hubo un error al cambiar el Tipo de Asignación");
                }


        }, error: function (xhr, ajaxOptions, thrownError) {
            alert(thrownError + " " + ajaxOptions + " " + thrownError);
        }
    });
}