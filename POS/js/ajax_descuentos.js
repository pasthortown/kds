
/////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Jose Fernandez/////////////////////////////////
////////DESCRIPCION: Ajax para manejo de descuentos//////////////////////
///////FECHA CREACION: 23/02/2015////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION:28/04/2014//////////////////////////////
///////////////////////////////////////////////////////////////////////////


function validaExisteFormaPago()
{
    $("#hid_bandera_propina").val(0);
    send = {"validaExisteFormaPago": 1};
    send.factAevaluar = $("#txtNumFactura").val();
    $.getJSON("config_descuentos.php", send, function (datos)
    {
        if (datos.Existe == 1)
        {
            alertify.alert("Ya existe una forma de pago aplicada a esta factura. No puede aplicar descuento.");
            return false;
            //.prop( "disabled", false );
        }
        if (datos.Existe == 0)
        {

            fn_validacuponYaAplicado();
        }

    });
}



function fn_validacuponYaAplicado()
{
    send = {"validacuponYaAplicado": 1};
    send.factAplicado = $("#txtNumFactura").val();
    $.getJSON("config_descuentos.php", send, function (datos)
    {
        if (datos.aplicado == 1)
        {
            alertify.alert("Ya existe un cupon aplicado a esta factura");
            return false;
        } else
        {
            buscaDescuentos();
        }
    }
    );
}

function buscaDescuentos()
{
    send = {"buscaDescuentos": 1};
    $.getJSON("config_descuentos.php", send, function (datos)
    {
        if (datos.str > 0)
        {
            $("#modalDescuentos").empty();
            for (i = 0; i < datos.str; i++)
            {
                descripcion = datos[i]['dsct_descripcion'];
                html = "<tr><td><input type='button' value='" + descripcion + "' style='height:80px; width:270px' onclick=fn_valida(" + datos[i]['dsct_id'] + "," + datos[i]['dsct_maximo'] + "," + datos[i]['dsct_valor'] + "," + datos[i]['tpd_id'] + "," + datos[i]['dsct_precio_minimo'] + "," + datos[i]['dsct_cantidad_minima'] + "," + datos[i]['apld_id'] + "," + datos[i]['dsct_porfactura'] + ") /></td></tr>";
                $("#modalDescuentos").append(html);
            }
            $("#modalDescuentos").dialog
                    ({
                        modal: true,
                        position: "center",
                        closeOnEscape: false,
                        show: "blind",
                        //hide: "shake",
                        resizable: "false",
                        height: 500,
                        width: 315,
                        buttons: {
                            "Cancelar": function () {
                                $(this).dialog("close");
                            }
                        }
                    });
            $("#modalDescuentos").dialog("open");
            //$('#modalDescuentos').shortscroll();
        } else
        {
            alertify.alert("No existen descuentos configurados para este restaurante");
        }
    }
    );
}


function fn_modal_credenciales(desc, maxi, valor, tipo, mini, canti, aplica, por/*,inicio,fin*/)
{
    $("#anulacionesContenedor").show();
    $("#anulacionesContenedor").dialog(
            {
                //modal: true,
                width: 700,
                heigth: 500,
                resize: false,
                opacity: 0,
                show: "explode",
                hide: "explode",
                duration: 5000,
                position: "center",
                open: function (event, ui)
                {
                    $(".ui-dialog-titlebar").hide();
                }
            });
    $("#anulacionesContenedor").dialog("open");
    $("#usr_clave").focus();

}

function fn_validarUser()
{
    descuento = $("#id_desc").val();
    maximo = $("#maximo").val();
    valor = $("#valor").val();
    tipod = $("#tipo_des").val();
    precip_min = $("#precio_min").val();
    canti_min = $("#canti_min").val();
    aplica = $("#aplica").val();
    por = $("#porfactura").val();
    finicio = $("#fecha_ini").val();
    ffin = $("#fecha_fin").val();

    var usr_clave = $("#usr_clave").val();
    if (usr_clave != "")
    {
        //var rst_id=document.getElementById("hide_rst_id").value;	
        send = {"validarUsuario": 1};
        send.usr_clave = usr_clave;
        $.getJSON("config_descuentos.php", send, function (datos)
        {
            if (datos.admini == 1)
            {
                $("#anulacionesContenedor").dialog("close");
                $("#usr_clave").val("");
                insertaDescuento(descuento, maximo, valor, tipod, precip_min, canti_min, aplica, por, finicio, ffin);
            } else
            {
                alertify.confirm("Clave no autorizada", function (e) {
                    if (e) {
                        alertify.set({buttonFocus: "none"});
                        $("#usr_clave").focus();
                    }
                });
                $("#usr_clave").val("");
            }
        });
    } else
    {
        alertify.confirm("Ingrese la clave.", function (e) {
            if (e) {
                alertify.set({buttonFocus: "none"});
                $("#usr_clave").focus();
            }
        });
        $("#usr_clave").val("");
    }
}




function fn_valida(desc, maxi, valor, tipo, mini, canti, aplica, por/*,inicio,fin*/)
{
    aplica_producto = '';
    aplica_categoria = '';
    $("#id_desc").val(desc);
    $("#maximo").val(maxi);
    $("#valor").val(valor);
    $("#tipo_des").val(tipo);
    $("#precio_min").val(mini);
    $("#canti_min").val(canti);
    $("#aplica").val(aplica);
    $("#porfactura").val(por);
//$("#fecha_ini").val(inicio);
//$("#fecha_fin").val(fin);
    if (tipo == 1 || tipo == 2)// 1 % de descuento - 2 Monto de descuento
    {
        if (aplica == 1 || aplica == 2 || aplica == 3 || aplica == 4)// 
        {
            send = {"validaDescuentoPorProducto": 1};
            send.factA = $("#txtNumFactura").val();
            send.id_desc = desc;
            send.valorD = valor;
            send.aplicaD = aplica;
            $.getJSON("config_descuentos.php", send, function (datos)
            {
                aplica_producto = datos.aplica;
                cantidadDeDetalle = datos.cantidad_minima
                send = {"validaDescuentoPorCategoria": 1};
                send.factC = $("#txtNumFactura").val();
                send.id_descC = desc;
                send.valorC = valor;
                send.aplicaC = aplica;
                $.getJSON("config_descuentos.php", send, function (datos)
                {
                    aplica_categoria = datos.aplica;

                    if (aplica_producto == 1)
                    {
                        if (canti > 0)
                        {
                            if ((cantidadDeDetalle < canti))
                            {
                                alertify.alert("Cantidad de producto menor que el solicitado para aplicar descuento.")
                                return false;
                            }
                        }
                        perfil = $("#usr_perfil").val();
                        send = {"valida_seguridad_usuario": 1};
                        send.idDescuento = desc;
                        $.getJSON("config_descuentos.php", send, function (datos)
                        {
                            if (datos.prf_id == perfil)
                            {
                                insertaDescuento(desc, maxi, valor, tipo, mini, canti, aplica, por);
                            } else
                            {
                                fn_modal_credenciales(desc, maxi, valor, tipo, mini, canti, aplica, por);

                            }
                        });
                    }//fin aplica descuento por producto
                    if (aplica_categoria == 1)
                    {
                        precioDeFactura = datos.precio_factura;
                        if (mini > 0)
                        {
                            if ((precioDeFactura < mini))
                            {
                                alertify.alert("Valor de la factura menor que el solicitado para aplicar descuento.");
                                return false;
                            }
                        }
                        perfil = $("#usr_perfil").val();
                        send = {"valida_seguridad_usuario": 1};
                        send.idDescuento = desc;
                        $.getJSON("config_descuentos.php", send, function (datos)
                        {
                            if (datos.prf_id == perfil)
                            {
                                insertaDescuento(desc, maxi, valor, tipo, mini, canti, aplica, por);
                            } else
                            {
                                fn_modal_credenciales(desc, maxi, valor, tipo, mini, canti, aplica, por);

                            }
                        });
                    }//fin aplica descuento por categoria
                    if (aplica_producto == 0 && aplica_categoria == 0)
                    {
                        alertify.alert("No aplica descuento a esta factura.")
                        return false;
                    }
                }
                );	//fin de aplica por categoria					
            }
            );//fin de aplica por producto



        }

    }
    if (tipo == 4)
    {
        $("#id_desc").val(desc);
        fn_muestraTeclado();
    }
}

function insertaDescuento(desc, maxi, valor, tipo, mini, canti, aplica, por)
{
    valor = parseFloat(valor);
    total = $("#valorTotal").val();
    if (tipo == 1)
    {
        Descuento = (valor * total) / 100;
        descuentoTotal = total - Descuento;
    }
    if (tipo == 2)
    {
        Descuento = (total - valor);
        descuentoTotal = Descuento;
    }
    send = {"insertaDescuento": 1};
    send.id_descF = desc;
    send.valorDeDescuento = descuentoTotal;
    send.factF = $("#txtNumFactura").val();
    $.getJSON("config_descuentos.php", send, function (datos)
    {
        $("#modalDescuentos").dialog("destroy");
        $("#anulacionesContenedor").dialog("destroy");
        //$("#aumentarContador").dialog( "destroy" );		
        descuento = "<tr><td align='center' style='width:30px; background-color:#FE2E2E; color: white;'>1<td align='left' style='width:229px; background-color:#FE2E2E; color: white;'>" + datos.dsct_descripcion + "<td 				align='left' style='width:58px; background-color:#FE2E2E; color: white;'>$ " + (datos.desf_valor).toFixed(2) + "</td></td></td></tr>";

        $("#item").append(descuento);
        fn_actualizaTotalesDescuentos();

    });
}

function fn_consultaDescuentos() {
    send = {"consultaDescuentos": 1};
    send.factDescu = $("#txtNumFactura").val();
    $.getJSON("config_descuentos.php", send, function (datos) {
        if (datos.str > 0) {
            descuento = "<tr><td align='center' style='width:30px; background-color:#FE2E2E; color: white;'>1<td align='left' style='width:229px; background-color:#FE2E2E; color: white;'>" + datos.dsct_descripcion + "<td align='left' style='width:58px; background-color:#FE2E2E; color: white;'>$ " + (datos.desf_valor).toFixed(2) + "</td></td></td></tr>";

            $("#item").append(descuento);
            fn_actualizaTotalesDescuentos();
        }
    });
}

function fn_ok() {
    if ($("#hid_bandera_propina").val() == 0) {
        id = $("#id_desc").val();
        aDescontar = parseFloat($("#cantidad").val());
        total = $("#valorTotal").val();
        if ((total - aDescontar) <= 0) {
            alertify.alert("Factura con valor en cero. Descuento no permitido");
            return false;
        }
        send = {"insertaDescuentoEntradaManual": 1};
        send.id_des = id;
        send.valorDesscuento = aDescontar;
        send.factCabecera = $("#txtNumFactura").val();
        $.getJSON("config_descuentos.php", send, function (datos)
        {
            //$("#valoresTotalesFactura").empty();
            //fn_listaFacturar();		
            $("#modalDescuentos").dialog("destroy");
            $("#aumentarContador").dialog("destroy");
            descuento = "<tr><td align='center' style='width:30px; background-color:#FE2E2E; color: white;'>1<td align='left' style='width:229px; background-color:#FE2E2E; color: white;'>" + datos.dsct_descripcion + "<td align='left' style='width:58px; background-color:#FE2E2E; color: white;'>$ " + (datos.desf_valor).toFixed(2) + "</td></td></td></tr>";

            $("#listaFactura").append(descuento);
            fn_actualizaTotalesDescuentos();

            //fn_muestraDescuentoEnPantalla();
        });
    }
    if ($("#hid_bandera_propina").val() == 1) {
        alertify.confirm("Est� seguro de aplicar propina a esta transaccion.? Solo aplica para pagos con Tarjeta.", function (e) {
            if (e) {
                $("#aumentarContador").dialog("close");
            } else {
                $("#cantidad").val('');
                $("#aumentarContador").dialog("close");
            }
        });
    }
}

function fn_actualizaTotalesDescuentos() {
    $("#valoresTotalesFactura").empty();
    var totalItem = 0;
    var subTotalbaseIva12 = 0;
    var subTotalbaseIva0 = 0;
    var subTotalFinal = 0;
    var IvaFinal = 0;
    var TotalFinal = 0;
    var cdn_tipoimpuesto;
    send = {"muestraTotalesConDescuento": 1};
    send.cfac_idd = $("#txtNumFactura").val();
    $.getJSON("config_descuentos.php", send, function (datos) {
        $("#valoresTotalesFactura").empty();
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                cdn_tipoImpuesto = datos[i]['cdn_tipoimpuesto'];
                if (datos[i]['plu_impuesto'] == 1) {
                    subTotalbaseIva12 = datos[i]['cfac_base_iva'];
                    IvaFinal = parseFloat(datos[i]['cfac_iva']);//parseFloat(datos[i]['totalizado']*0.12).toFixed(2);
                } else if (datos[i]['plu_impuesto'] == 0) {
                    subTotalbaseIva0 = parseFloat(datos[i]['cfac_base_cero']).toFixed(2);
                }


                $("#cdn_tipoImpuesto").val(cdn_tipoImpuesto);

                subTotalFinal = parseFloat(datos[i]['cfac_subtotal']);
            }
            TotalFinal = parseFloat(subTotalbaseIva12) + parseFloat(IvaFinal) + parseFloat(subTotalbaseIva0);
            TotalFinal = parseFloat(TotalFinal);

            $("#btnBaseFactura").val(TotalFinal.toFixed(2));
            valoresTotales = "<br/><table id='tblValoresTotales' width='350px' align='left' cellpading='0' >";
            valoresTotales += "<tr><td width='130px' align='right'>Subtotal</td><td width='40px' align='center'>$</td>";
            valoresTotales += "<td width='100px' align='center'>" + (parseFloat(subTotalFinal)).toFixed(2) + "</td></tr>";
            if (cdn_tipoImpuesto != 'Incluido') {
                valoresTotales += "<tr><td width='130px' align='right'>Base Imponible 12%</td><td width='40px' align='center'>$</td>";
                valoresTotales += "<td width='100px' align='center'>" + parseFloat(subTotalbaseIva12).toFixed(2) + "</td></tr>";

                valoresTotales += "<tr><td width='130px' align='right'>Base Imponible 0%</td><td width='40px' align='center'>$</td>";
                valoresTotales += "<td width='100px' align='center'>" + (parseFloat(subTotalbaseIva0)).toFixed(2) + "</td></tr>";
            }
            valoresTotales += "<tr><td width='130px' align='right'>Iva 12%</td><td width='40px' align='center'>$</td>";
            valoresTotales += "<td width='100px' align='center'>" + (parseFloat(IvaFinal)).toFixed(2) + "</td></tr>";

            valoresTotales += "<tr><td width='120px' align='right'></td><td width='40px' align='center'></td>";
            valoresTotales += "<td width='100px' align='center'>&mdash;&mdash;&mdash;&mdash;</td></tr>";

            valoresTotales += "<tr><th width='130px' align='right'>Total</th><th width='20px' align='center'>$</th>";
            valoresTotales += "<th width='100px' align='center'>" + (parseFloat(TotalFinal)).toFixed(2) + "</th></tr>";
            valoresTotales += "</table><br/><table id='formasPago2' align='left' width='300px'></table><div id='formasT'></div>";

            $("#valoresTotalesFactura").append(valoresTotales);

            $("#valorSubTotal").val((parseFloat(subTotalFinal)).toFixed(2));
            $("#valorIva").val((parseFloat(IvaFinal)).toFixed(2));
            $("#valorTotal").val((parseFloat(TotalFinal)).toFixed(2));

            $("#pagoTotal").val((parseFloat(TotalFinal)).toFixed(2));
            $("#diferenciaPago").val((parseFloat(TotalFinal) * -1).toFixed(2));
        }
    });
}

function fn_muestraDescuentoEnPantalla() {
    send = {"muestraDescuentoEnPantalla": 1};
    send.factCabecera = $("#txtNumFactura").val();
    $.getJSON("config_descuentos.php", send, function (datos)
    {
        $("#modalDescuentos").dialog("destroy");
        $("#aumentarContador").dialog("destroy");
        fn_muestraDescuentoEnPantalla();
    });
}

function fn_muestraTeclado() {
    $("#aumentarContador").dialog({
        modal: true,
        autoOpen: false,
        show: {
            effect: "blind",
            duration: 500
        },
        hide: {
            effect: "explode",
            duration: 500
        },
        width: "auto",
        buttons: {
            Cancelar: function () {
                //$("#cantidad").val('');
                $(this).dialog("close");
                //$("#keyboard").hide();
            }
        },
        open: function (event, ui)
        {
            $(".ui-dialog-titlebar").hide();
        }
    });
    $("#aumentarContador").dialog("open");
}

/*----------------------------------------------------------------------------------------------------
 Funci�n para agregar un n�mero
 -----------------------------------------------------------------------------------------------------*/
function fn_agregarNumeroD(valor) {
    lc_cantidad = document.getElementById("cantidad").value;
    if (lc_cantidad == 0 && valor == ".")
    {

        //si escribimos una coma al principio del n�mero
        document.getElementById("cantidad").value = "0."; //escribimos 0.
        coma2 = 1;
    } else
    {
        //continuar escribiendo un n�mero
        if (valor == "." && coma2 == 0)
        {
            //si escribimos una coma decimal p�r primera vez
            lc_cantidad = lc_cantidad + valor;
            document.getElementById("cantidad").value = lc_cantidad;
            coma2 = 1; //cambiar el estado de la coma  
        }
        //si intentamos escribir una segunda coma decimal no realiza ninguna acci�n.
        else if (valor == "." && coma2 == 1) {
        }
        //Resto de casos: escribir un n�mero del 0 al 9: 	 
        else
        {
            $("#cantidad").val('');
            lc_cantidad = lc_cantidad + valor;
            document.getElementById("cantidad").value = lc_cantidad;
        }
    }
    fn_focusLector();
}

function fn_focusLector() {
    $("#cantidad").focus();
}

function fn_eliminarCantidad() {
    var lc_cantidad = document.getElementById("cantidad").value.substring(0, document.getElementById("cantidad").value.length - 1);
    if (lc_cantidad == "") {
        lc_cantidad = "";
        coma2 = 0;
    }
    if (lc_cantidad == ".") {
        coma2 = 0;
    }
    document.getElementById("cantidad").value = lc_cantidad;
    fn_focusLector();
}

function fn_cerrarDialogoUsuarioAdmin() {
    $('#usr_clave').val('');
    $('#anulacionesContenedor').dialog('close');
}