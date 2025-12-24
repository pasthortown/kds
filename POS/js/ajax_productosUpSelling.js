/*
FECHA CREACION   : 05/02/2019 
DESARROLLADO POR : Daniel Llerena
DESCRIPCION      : Configuracion de productos Up Selling
*/

/* global alertify */

$(document).ready(function(){   

    cargarProductosUpSelling();
});

function cargarProductosUpSelling() {
    var send;
    var html = '<thead><tr class="active"><th class="text-center">Número Plu</th><th class="text-center">Producto Base</th><th class="text-center">Clasificación</th><th class="text-center">Master Plu</th><th class="text-center">Estado</th></tr></thead>';
    
    send = {"productosConfigurados": 1};    
    $.ajax({
        async: false
        , type: "POST"
        , dataType: "json"
        , contentType: "application/x-www-form-urlencoded"
        , url: "../productosUpSelling/config_productosUpSelling.php"
        , data: send
        , success: function (datos){
            if (datos.str > 0) {
                for (var i = 0; i < datos.str; i++){                    
                    var idProductoBase = (datos[i]["idProductoBase"]);
                    var numeroPlu = (datos[i]["numeroPlu"]);
                    var producto =  (datos[i]["producto"]);
                    var clasificacion = (datos[i]["clasificacion"]);
                    var masterPlu = (datos[i]["masterPlu"]); 
                    var estado = (datos[i]["estado"]); 
                    
                    html += '<tr id="'+i+"idProducto"+'" onclick="seleccionarFila('+i+')" ondblclick="seleccionarActualizar(\''+numeroPlu+'\', \''+clasificacion+'\', '+idProductoBase+', \''+producto+'\', '+estado+')" class="text-center"><td>'+numeroPlu+'</td><td>'+producto+'</td><td>'+clasificacion+'</td><td>'+masterPlu+'</td>';               
                   
                    if(estado == 0){
                        html += '<td><input type="checkbox" value="1" disabled/></td>';
                    }else{
                        html += '<td><input type="checkbox" value="1" checked="checked" disabled/></td>';
                    }   
                                        
                    html += '</tr>';
                }
                
                $("#tabla_detalleProductos").html(html);
                $("#tabla_detalleProductos").dataTable({"destroy": true});
                $("#tabla_detalleProductos_length").hide();
                $("#tabla_detalleProductos_paginate").addClass("col-xs-10");
                $("#tabla_detalleProductos_info").addClass("col-xs-10");
                $("#tabla_detalleProductos_length").addClass("col-xs-6");
            } else {
                html = html + '<tr><th colspan="5" class="text-center">No existen registros.</th></tr>';
                
                $("#tabla_detalleProductos").html(html);
            }
        }
    });
}

function seleccionarFila(idFila) {    
    
    $("#tabla_detalleProductos tr").removeClass("success");
    $("#"+idFila+"idProducto").addClass("success");  
    $("#tabla_detalleProductos tr").find('td:eq(1)');
}

function agregar() {
    $("#myModal").modal("show");
    
    $("#selectProductos1").empty();
    $("#selectProductos1").val(0);
    $("#selectProductos1").trigger("chosen:updated");
    
    $("#selectProductos2").empty();
    $("#selectProductos2").val(0);
    $("#selectProductos2").trigger("chosen:updated");
    
    selectProductosBase();
    //selectProductosMejora(1, 0);
    
    $("#tablaProductosMejora").html("<tr class='bg-primary'><td align='center'><h5><b>Número Plu</b></h5></td><td align='center'><h5><b>Producto Mejora</b></h5></td></tr>");
}

function selectProductosBase() {
    var send;
    var accion = 2;
    var idProductoBase = 0;
    
    send = {"productos": 1};  
    send.accion = accion;
    send.idProductoBase = idProductoBase;
    $.ajax({
        async: false
        , type: "POST"
        , dataType: "json"
        , contentType: "application/x-www-form-urlencoded"
        , url: "../productosUpSelling/config_productosUpSelling.php"
        , data: send
        , success: function (datos){    
            if (datos.str > 0) {
                
                $("#selectProductos1").append("<option selected value='0'>---- Seleccionar Producto ----</option>"); 
                
                for (var i = 0; i < datos.str; i++) {                    
                    var idProducto = (datos[i]['idProducto']);
                    var producto = (datos[i]['producto']); 

                    $("#selectProductos1").append("<option value=" + idProducto + ">" + producto + "</option>");                    
                }
                
                $('#selectProductos1').chosen();
                $("#selectProductos1_chosen").css('width', '500'); 
                
                $("#selectProductos1").change(function () {
                    var idProductoBase = $("#selectProductos1").val();
                    console.log(idProductoBase);
                    $("#idProductoBase").val(idProductoBase); 
                    
                    selectProductosMejora(1, idProductoBase);
                    detalleProductosMejora(idProductoBase);
                }); 
            }
        }
    });
}

function selectProductosMejora(opcion, idProductoBase_) {
    var send;
    var accion = 4;
    var idProductoBase;
    
    if (opcion == 1) {
        idProductoBase = 0;  
        $("#selectProductos2").empty();
        $("#selectProductos2").val(0);
        $("#selectProductos2").trigger("chosen:updated");
    } else {
        idProductoBase = idProductoBase_; 
        $("#selectProductos2Update").empty();
        $("#selectProductos2Update").val(0);
        $("#selectProductos2Update").trigger("chosen:updated");
    }        
    
    send = {"productos": 1};  
    send.accion = accion;
    send.idProductoBase = idProductoBase;
    $.ajax({
        async: false
        , type: "POST"
        , dataType: "json"
        , contentType: "application/x-www-form-urlencoded"
        , url: "../productosUpSelling/config_productosUpSelling.php"
        , data: send
        , success: function (datos){    
            if (datos.str > 0) {                
                for (var i = 0; i < datos.str; i++) {                    
                    var idProducto = (datos[i]['idProducto']);
                    var producto = (datos[i]['producto']);   

                    if (opcion == 1) {
                        if (idProducto != idProductoBase_) {
                            $("#selectProductos2").append("<option value=" + idProducto + ">" + producto + "</option>");                            
                        }  
                    } else {
                        $("#selectProductos2Update").append("<option value=" + idProducto + ">" + producto + "</option>");
                    }                    
                }
                
                if (opcion == 1) {
                    $("#selectProductos2").trigger("chosen:updated");
                    $('#selectProductos2').chosen();
                    $("#selectProductos2_chosen").css('width', '500');                     
                } else {
                    $("#selectProductos2Update").trigger("chosen:updated");
                    $('#selectProductos2Update').chosen();
                    $("#selectProductos2Update_chosen").css('width', '500');                    
                }
            }
        }
    });
}

function  agregarProducto(opcion) {  
    
    var idMejoraProducto_ = "";
    
    if (opcion === 1) {
        if ($("#selectProductos1").val() == null || $("#selectProductos1").val() == "" || $("#selectProductos1").val() == "0") {
            alertify.error("Debe seleccionar un producto base.");
            return false;
        }
    }
    
    // INSERT
    if (opcion === 1) {
        if ($("#selectProductos2").val() == null || $("#selectProductos2").val() == "") {
            alertify.error("Debe seleccionar por lo menos un producto de mejora.");
            return false;
        } else {
            $("#selectProductos2 option:selected").each(function () {
                if (idMejoraProducto_.indexOf($(this).attr('value') + ";") < 0) {
                    idMejoraProducto_ = idMejoraProducto_ + $(this).attr('value') + ";";
                }
            });  
        }
    } 
    // UPDATE
    else {
        if ($("#selectProductos2Update").val() == null || $("#selectProductos2Update").val() == "") {
            alertify.error("Debe seleccionar por lo menos un producto de mejora.");
            return false;
        } else {
            $("#selectProductos2Update option:selected").each(function () {
                if (idMejoraProducto_.indexOf($(this).attr('value') + ";") < 0) {
                    idMejoraProducto_ = idMejoraProducto_ + $(this).attr('value') + ";";
                }
            });  
        }
    }   
    
    validaColeccionUpSelling(opcion, idMejoraProducto_);
}

function validaColeccionUpSelling(opcion, idMejoraProducto_) {
    var send;
    
    send = {"validaColeccionUpSelling": 1};
    send.idProductoBase = 0;
    send.idProductoMejora = idMejoraProducto_;    
    $.ajax({
        async: false
        , type: "POST"
        , dataType: "json"
        , contentType: "application/x-www-form-urlencoded"
        , url: "../productosUpSelling/config_productosUpSelling.php"
        , data: send
        , success: function (datos){    
            if (datos.str > 0) {
                
                var existeColeccion = datos[0].existe;
                
                if (existeColeccion == 1) {
                    guardar(opcion, 0, idMejoraProducto_);                   
                } else {
                    alertify.error("La política a nivel de plus UP SELLING no se encuentra creada.");
                }
            }
        }
    });
}

function guardar(opcion, idProductoBase_, idMejoraProducto_) {
    var send;
    var idProductoBase; 
    var accion;    
    
    if (opcion == 1) {
        // INSERT
        accion = "I";
    } else if (opcion == 2) {
        // AGREGA PRODUCTOS MEJORA DEL PRODUCTO BASE 
        accion = "U";
    } else if (opcion == 3) {
        // ELIMINA LOS PRODUCTOS MEJORA DEL PRODUCTO BASE 
        accion = "D";
    } else {
        // ACTUALIZA EL ESTADO DEL PRODUCTO BASE CONFIGURADO
        accion = "S";
    }
    
    if (idProductoBase_ == 0) {
       idProductoBase = $("#idProductoBase").val();  
    } else {
        idProductoBase = idProductoBase_;
    }
    
    send = {"guardar": 1};
    send.accion = accion;
    send.idProductoBase = idProductoBase;
    send.idProductoMejora = idMejoraProducto_;    
    $.ajax({
        async: false
        , type: "POST"
        , dataType: "json"
        , contentType: "application/x-www-form-urlencoded"
        , url: "../productosUpSelling/config_productosUpSelling.php"
        , data: send
        , success: function (datos){  
           if (opcion == 1) {    
                //selectProductosMejora(1, idProductoBase); 
                detalleProductosMejora(idProductoBase);  
                cargarProductosUpSelling();
                
                alertify.success("Producto Up Selling agregado correctamente."); 
            } else if (opcion == 2 || opcion == 3) {
                
                $("#selectProductos2Update").empty();
                $("#selectProductos2Update").val(0);
                $("#selectProductos2Update").trigger("chosen:updated");
                
                selectProductosMejora(2, idProductoBase);  
                detalleProductosMejoraUpdate(idProductoBase);  
                                
                alertify.success("Producto Up Selling actualizado correctamente."); 
            } else {
                cargarProductosUpSelling();
                
                alertify.success("Estado actualizado correctamente."); 
            }
        }
    });
}

function detalleProductosMejora(idProductoBase) {
    var send;
    var html = "<tr class='bg-primary'><td align='center'><h5><b>Número Plu</b></h5></td><td align='center'><h5><b>Producto Mejora</b></h5></td></tr>";
    
    send = {"productosMejora": 1};
    send.idProductoBase = idProductoBase;
    
    $.ajax({
        async: false
        , type: "POST"
        , dataType: "json"
        , contentType: "application/x-www-form-urlencoded"
        , url: "../productosUpSelling/config_productosUpSelling.php"
        , data: send
        , success: function (datos){    
            if (datos.str > 0) {                   
                for (var i = 0; i < datos.str; i++) {     
                    var numeroPluMejora = (datos[i]["numeroPluMejora"]);
                    var productoMejora = (datos[i]["productoMejora"]);
                    
                    html += "<tr><td style='text-align: center;'>"+numeroPluMejora+"</td><td>"+productoMejora+"</td></tr>";
                }
            } else {
                html += "<tr><td colspan='2' style='text-align: center;'>No existen registros.</td></tr>";
            }

            $('#tablaProductosMejora').html(html);
        }
    });
}

function seleccionarActualizar(numeroPlu, clasificacion, idProductoBase, producto, estado) { 
    
    $("#modalUpdate").modal("show"); 
    
    $("#idProductoBase").val(idProductoBase); 
    
    $("#check_isactive").prop("checked", true); 
    $("#check_isactive").prop("disabled", false);
    
    if(estado == 0){
        $("#check_isactive").prop("checked", false);
    } else {
        $("#check_isactive").prop("checked", true);
    }
    
    $("#tituloProducto").text(producto);
    
    $("#selectProductos2Update").empty();
    $("#selectProductos2Update").val(0); 
    $("#selectProductos2Update").trigger("chosen:updated");  
    
    var productoDescripcion = numeroPlu + " - " + producto + " - " + clasificacion;

    $("#txtProductoBase").val(productoDescripcion);  
    
    selectProductosMejora(2, idProductoBase);
    detalleProductosMejoraUpdate(idProductoBase);   
}

function detalleProductosMejoraUpdate(idProductoBase) {
    var send;
    var html = "<tr class='bg-primary'><td align='center'><h5><b>Número Plu</b></h5></td><td align='center'><h5><b>Producto Mejora</b></h5></td><td align='center'><h5><b>Quitar</h5></b></td></tr>";
    
    send = {"productosMejora": 1};
    send.idProductoBase = idProductoBase;
    
    $.ajax({
        async: false
        , type: "POST"
        , dataType: "json"
        , contentType: "application/x-www-form-urlencoded"
        , url: "../productosUpSelling/config_productosUpSelling.php"
        , data: send
        , success: function (datos){    
            if (datos.str > 0) {                   
                for (var i = 0; i < datos.str; i++) {                    
                    var idProductoBase = (datos[i]["idProductoBase"]);
                    var idProductoMejorado = (datos[i]["idProductoMejorado"]);
                    var numeroPluMejora = (datos[i]["numeroPluMejora"]);
                    var productoMejora = (datos[i]["productoMejora"]);
                    
                    html += "<tr><td style='text-align: center;'>"+numeroPluMejora+"</td><td>"+productoMejora+"</td><td align='center'><button type=\"button\" class=\"btn btn-danger btn-sm aling-btn-right\" onclick=\"guardar(3, "+idProductoBase+", "+idProductoMejorado+")\"><span class=\"glyphicon glyphicon-remove\" aria-hidden=\"true\"></span></button></td></tr>";
                }
            } else {
                html += "<tr><td colspan='3' style='text-align: center;'>No existen registros.</td></tr>";
            }

            $('#tablaProductosMejoraUpdate').html(html);
        }
    });
}

function cerrarModal(opcion) {    
    if (opcion == 1) {
        $("#selectProductos1").empty();
        $("#selectProductos1").val(0);
        $("#selectProductos1").trigger("chosen:updated");
        $("#selectProductos1").chosen("destroy");

        $("#selectProductos2").empty();
        $("#selectProductos2").val(0);
        $("#selectProductos2").trigger("chosen:updated");
        $("#selectProductos2").chosen("destroy");
    } else {
        $("#selectProductos2Update").empty();
        $("#selectProductos2Update").val(0);
        $("#selectProductos2Update").trigger("chosen:updated");
        $("#selectProductos2Update").chosen("destroy");
    }
}

function estadoProductoUpSelling(estado) {
    
    var valor;
    var idProductoBase = $("#idProductoBase").val(); 
    
    if(estado) {
        valor = 1;
        $("#check_isactive").prop("checked", true);
    } else {
        valor = 0;
        $("#check_isactive").prop("checked", false);
    }
    
    guardar(4, idProductoBase, valor);
}