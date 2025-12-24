
var CODIGO_CONCEPTO="";
var GRABAR ="";
var DESCRIPCION_CONCEPTO="";
var SIGNO_CONCEPTO="";
var ESTADO_CONCEPTO="";
var ACTIVO_CONCEPTO=-1;

$(document).ready(function () {
    $("#tbl_conceptosDepositos").hide();    
    //$("#modificado").hide();
    //$("#botonesActivosInactivos").hide();

    
    //fn_btn('cancelar', 1);
    //fn_btn('agregar', 1);
    
    //$("#sel_seleccione").bootstrapSwitch('state', false);
    //$("#sel_seleccioneM").bootstrapSwitch('state', false);
    
    fn_cargaDetalleConceptosDepositos('Activo');
});

function fn_cargaDetalleConceptosDepositos(opcion)
{
    send = {"cargaDetalleConceptos": 1};
    send.opcionEstado = opcion;
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminMotivoIngresosEgresosDeposito/config_adminMotivoIngresosEgresosDeposito.php", data: send,
    success: function (datos) 
        {    
            $("#mdl_rdn_pdd_crgnd").hide();//oculto la modal del cargando
            $("#tbl_conceptosDepositos").show();
            html = "<thead><tr class='active'>";           
            html += "<th align='center' style='width:180px; text-align:center;'>Descripci&oacute;n</th>";
            html += "<th align='center' style='width:170px; text-align:center;'>Signo</th>";
            html += "<th align='center' style='width:170px; text-align:center;'>Activo</th>";
            html += "</tr></thead>";
            $("#tbl_detalleDepositos").empty();
            if (datos.str > 0) 
            {
                for (var i = 0; i < datos.str; i++) 
                {
                    html += "<tr id='" + i + "' style='cursor:pointer;'";                    
                    html += "onclick=fn_seleccionclick(" + i + "); ondblclick='fn_seleccion(\""+ datos[i]['idConcepto'] + "\","+ i + ",\""+ datos[i]['descripcionConcepto'] + "\",\"" + datos[i]['signo'] + "\"," + datos[i]['estado'] + ")';>";
                    html += "<td align='center'  style='width:160px;'>" + datos[i]['descripcionConcepto'] + "&nbsp;</td>";
                    html += "<td align='center'  style='width:180px;'>" + datos[i]['signo'] + "&nbsp;</td>";                           
                    if (datos[i]['estado'] === 1) 
                    {
                        html += "<td align='center'  style='width:169px;'><input disabled='disabled' type='checkbox' checked='checked' id='optiondetalle'></td></tr>";
                    }
                    if (datos[i]['estado'] === 0) 
                    {
                        html += "<td align='center'  style='width:169px;'><input type='checkbox' disabled='disabled'id='optiondetalle'></td></tr>";
                    }
                    $("#tbl_detalleDepositos").html(html);
                }
                $('#tbl_detalleDepositos').dataTable({'destroy': true});
            }
        }
    });
}

// funcion que resalra la fila seleccionada
function fn_seleccionclick(fila) 
{
    $("#tbl_detalleDepositos tr").removeClass("success");
    $("#" + fila + "").addClass("success");
}

//ondblclick=fn_seleccion('" + datos[i]['idConcepto'] + "'," + i + ",'" + datos[i]['descripcionConcepto'] + "'," + datos[i]['signo'] + "," + datos[i]['estado'] + ");>";

function fn_seleccion(idConcepto, fila, desConcepto, signo,estado) 
{
    $("#tbl_detalleDepositos tr").removeClass("success");
    $("#" + fila + "").addClass("success");    
    CODIGO_CONCEPTO=idConcepto;   
    DESCRIPCION_CONCEPTO=desConcepto;
    SIGNO_CONCEPTO=signo;
    ESTADO_CONCEPTO=estado;
    GRABAR="U";    
    $("#titulomodalNuevo").text(desConcepto);
    $("#txt_desConcepto").val(desConcepto);
    $("#selSigno").val(signo);
    if(ESTADO_CONCEPTO===1)
    {
        $("#option").prop( "checked", true );
    }
    else
    {
        $("#option").prop( "checked", false );
    }
    $("#ModalNuevo").modal('show');
    //fn_accionar('Modificar');    
}

function fn_guardar() 
{
    send = {"guardaConceptosDepositos": 1};
    send.desCon = $("#txt_desConcepto").val();    
    if (GRABAR === "U") 
    {
        send.opcionActualiza = "U";
        send.idCon=CODIGO_CONCEPTO;
    }
    else
    {
        send.opcionActualiza = "I";
        send.idCon="";
    }    
    send.selCon = $("#selSigno").val();
    if($("#option").is(":checked"))
    {
        send.estadoD="1";
    }
    else
    {
        send.estadoD="0";
    }
    
    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminMotivoIngresosEgresosDeposito/config_adminMotivoIngresosEgresosDeposito.php", data: send,
    success: function () 
        {
            alertify.success("Datos guardados correctamente");
            CODIGO_CONCEPTO="";   
            DESCRIPCION_CONCEPTO="";
            SIGNO_CONCEPTO="";
            ESTADO_CONCEPTO="";
            GRABAR="";
            $("#txt_desConcepto").val("");
            //$("#selSigno").val("");
            //$("#titulomodalNuevo").text("");
            fn_cargaDetalleConceptosDepositos('Todos');            
        }
    });
    
 }
 
 function fn_cerrarModal()
 {
    CODIGO_CONCEPTO="";   
    DESCRIPCION_CONCEPTO="";
    SIGNO_CONCEPTO="";
    ESTADO_CONCEPTO="";
    GRABAR="";  
    $("#txt_desConcepto").val("");    
    $("#titulomodalNuevo").text("");
 }
 
 function agregarNuevoMotivoIngresosEgresosDeposito()
 {
     GRABAR="I";    
     $("#txt_desConcepto").val("");     
     $("#titulomodalNuevo").text("INGRESO NUEVO..");
     $("#option").prop( "checked", true );
     $("#ModalNuevo").modal('show');     
 }
 
