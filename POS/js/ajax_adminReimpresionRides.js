
$(document).ready(function () 
{
    fn_consultarNombreComprobantes();
   fn_consultarRides("todo");
}
);

function fn_consultarNombreComprobantes(opcion)
{
    //$("#opciones_estado").empty();
    send = {"cargaLabelComprobantes": 1};
    html="";
    $.getJSON("../adminreimpresionrides/config_adminReimpresionRides.php", send, function (datos) 
    {
        if (datos.str > 0) 
        {
            for (i = 0; i < datos.str; i++) 
            {                    
                html = html+'<label id="opciones_'+i+'" class="btn btn-default btn-sm"><input type="radio" value="Activos" autocomplete="off" name="ptns_std_rst"/>'+datos[i]['nombre']+'</label>';
            }
            $('#opciones_estado').append(html);                                   
        } else 
        {
            html = html + '<tr><th colspan="8">No existen registros.</th></tr>';
            $('#opciones_estado').html(html);
            //fn_cargando(0);
        }
    });  
}

function fn_consultarRides()
{     
    var html = '<thead><tr class="active"><th>C&oacute;digo</th><th>N&uacute;mero</th><th class="text-center">Valor</th><th class="text-center">Cliente</th><th>Fecha</th><th>Estacion</th></tr></thead>';
    send = {"cargarRides": 1};
    $.getJSON("../adminreimpresionrides/config_adminReimpresionRides.php", send, function (datos) 
    {        
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) 
            {                
                html = html + '<tr id="factura_' + datos[i]['factura'] + '" onclick="fn_seleccionarDocumento(\'' + datos[i]['factura'] + '\')" ondblclick="fn_visualizarDocumento(\'' + datos[i]['factura'] + '\',\''+datos[i]['bandera']+'\')"><td>' + datos[i]['factura'] + '</td><td>' + datos[i]['numeroFactura'] + '</td><td class="text-center">' + datos[i]['total'] + '</td><td class="text-center">' + datos[i]['cliente'] + '</td><td>'   + datos[i]['fecha'] + '</td><td>' + datos[i]['estacion'] + '</td>'+ '</tr>';                              
            }
            $('#listaRestaurantes').html(html);
            $('#listaRestaurantes').dataTable({'destroy': true,'order': [[ 5, "desc" ]]});

            $("#listaRestaurantes_length").hide();
            $("#listaRestaurantes_paginate").addClass('col-xs-10');
            $("#listaRestaurantes_info").addClass('col-xs-10');
            $("#listaRestaurantes_length").addClass('col-xs-6');
            //fn_cargando(0);
        } else {
            html = html + '<tr><th colspan="8">No existen registros.</th></tr>';
            $('#listaRestaurantes').html(html);
            //fn_cargando(0);
        }
    });
}

function fn_seleccionarDocumento(id)
{
    $("#listaRestaurantes tr").removeClass("success");
    $("#factura_" + id).addClass("success");
}

function fn_visualizarDocumento(codigo,bandera)
{
   
   
    send = {"visualizarComprobante": 1};
    send.factura = codigo;
    send.tipo    = bandera;
    $.ajax({async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminreimpresionrides/config_adminReimpresionRides.php", data: send, success: function (datos)
        {
            if (datos.str > 0)
            {
                $("#div_reporteFinDeDia").empty();
                for (i = 0; i < datos.str; i++)
                {
                    $("#div_reporteFinDeDia").append(datos[i]['html']);
                    $("#div_reporteFinDeDia").append(datos[i]['html3']);
                    $("#div_reporteFinDeDia").append(datos[i]['html2']);
                    $("#div_reporteFinDeDia").append(datos[i]['htmlf']);
                    $("#ModalReporteFinDeDia").modal('show');
                }
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR);
            alert(textStatus);
            alert(errorThrown);
        }
    });
}

function fn_imprimirRide (nombre)
{
    var divToPrint=document.getElementById(nombre);
    var newWin=window.open('','Print-Window');
    newWin.document.open();
    newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
    newWin.document.close();
    setTimeout(function(){newWin.close();},10);   
}
