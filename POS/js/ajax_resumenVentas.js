$(document).ready(function () {
    $("#cntndr_pntll_rsmn_vnts").hide();
    $("#cntndr_pntll_rsmn_vnts_totales").hide();
    
    fn_cargarAccesosSistema();
    if ($("#txt_bloqueado").val() != 0)
    {
        $("#nuevaorden").attr("Disabled", true);
        $("#nuevaorden").removeClass("boton_Opcion");
        $("#nuevaorden").addClass("boton_Opcion_Bloqueado");
    }
    $('#cntndr_dtll_rsmn_vnts').shortscroll();
    fn_cargarConfiguracionResumenVentas();

    var fecha = moment().format('DD/MM/YYYY hh:mm');
    $('#tqt_fch_ctl').html(fecha);
    $('#tqt_fch_ctl_totales').html(fecha);

    //Modal Menu Desplegable
    $('#rdn_pdd_brr_ccns').css('display', 'none');

    $('#cnt_mn_dsplgbl_pcns_drch').css('display', 'none');

    $('#boton_sidr').click(function () {
        $('#rdn_pdd_brr_ccns').css('display', 'block');
        $('#cnt_mn_dsplgbl_pcns_drch').css('display', 'block');
    });

    $('#rdn_pdd_brr_ccns').click(function () {
        $('#rdn_pdd_brr_ccns').css('display', 'none');
        $('#cnt_mn_dsplgbl_pcns_drch').css('display', 'none');
    });
    
    
    fn_cargarResumenVentas();
});

function fn_cargaVentaCajero(event)
{
    event.stopPropagation();
    $("#cntndr_pntll_rsmn_vnts").show();
    $("#div_Opciones").hide();
    
}
function fn_cargaVentaTotal(event)
{   
    event.stopPropagation();
    $("#cntndr_pntll_rsmn_vnts").hide();
    $("#div_Opciones").hide();
    $("#cntndr_pntll_rsmn_vnts_totales").show();
    send = {"cargarVentaTotalesCuentas": 1};
    $.getJSON("config_resumenventas.php", send, function (datos) {
        if (datos.str > 0) {            
            Chart.defaults.global.tooltips.custom = function(tooltip) {
		// Tooltip Element
		var tooltipEl = document.getElementById('chartjs-tooltip');

		// Hide if no tooltip
		if (tooltip.opacity === 0) {
			tooltipEl.style.opacity = 0;
			return;
		}

		// Set caret Position
		tooltipEl.classList.remove('above', 'below', 'no-transform');
		if (tooltip.yAlign) {
			tooltipEl.classList.add(tooltip.yAlign);
		} else {
			tooltipEl.classList.add('no-transform');
		}

		function getBody(bodyItem) {
			return bodyItem.lines;
		}

		// Set Text
		if (tooltip.body) {
			var titleLines = tooltip.title || [];
			var bodyLines = tooltip.body.map(getBody);

			var innerHtml = '<thead>';

			titleLines.forEach(function(title) {
				innerHtml += '<tr><th>' + title + '</th></tr>';
			});
			innerHtml += '</thead><tbody>';

			bodyLines.forEach(function(body, i) {
				var colors = tooltip.labelColors[i];
				var style = 'background:' + colors.backgroundColor;
				style += '; border-color:' + colors.borderColor;
				style += '; border-width: 2px'; 
				var span = '<span class="chartjs-tooltip-key" style="' + style + '"></span>';
				innerHtml += '<tr><td>' + span + body + '</td></tr>';
			});
			innerHtml += '</tbody>';

			var tableRoot = tooltipEl.querySelector('table');
			tableRoot.innerHTML = innerHtml;
		}

		var positionY = this._chart.canvas.offsetTop;
		var positionX = this._chart.canvas.offsetLeft;

		// Display, position, and set styles for font
		tooltipEl.style.opacity = 1;
		tooltipEl.style.left = positionX + tooltip.caretX + 'px';
		tooltipEl.style.top = positionY + tooltip.caretY + 'px';
		tooltipEl.style.fontFamily = tooltip._fontFamily;
		tooltipEl.style.fontSize = 50;// tooltip.fontSize;
		tooltipEl.style.fontStyle = tooltip._fontStyle;
		tooltipEl.style.padding = tooltip.yPadding + 'px ' + tooltip.xPadding + 'px';
	};

	var config = {
		type: 'pie',
		data: {
			datasets: [{
				data: [datos[0]['total'], datos[1]['total']],
				backgroundColor: [
					window.chartColors.orange,
					window.chartColors.red					
				],
			}],
			labels: [
				datos[0]['tipo']+'    '+ datos[0]['simbolo'] + datos[0]['total'],
				datos[1]['tipo']+'    '+ datos[0]['simbolo'] + datos[1]['total']
			]
		},
		options: {
			responsive: true,
			legend: {
				display: true
			},
			tooltips: {
				enabled: false,
			}
		}
	};
        
        var ctx = document.getElementById("chart-area").getContext("2d");
	window.myPie = new Chart(ctx, config);
            
            //$('#tqt_fch_prd').html(datos[0]['fecha']);
            //$('#tqt_fch_prd_totales').html(datos[0]['fecha']);
        }
    });    
    
    
    
    
    
}

function fn_cargarConfiguracionResumenVentas() {
    send = {"cargarConfiguracionResumenVentas": 1};
    $.getJSON("config_resumenventas.php", send, function (datos) {
        if (datos.str > 0) {
            $('#tqt_fch_prd').html(datos[0]['fecha']);
            $('#tqt_fch_prd_totales').html(datos[0]['fecha']);
        }
    });
}

function fn_cargarResumenVentas() {
    var html = "";
    var tl_total = 0;
    var tl_transacciones = 0;
    var tl_ticket = 0;
    var tl_cupones = 0;
    var usr_anterior = '';
    send = {"cargarResumenVentasFacturas": 1};
    $.ajax({async: false, url: "config_resumenventas.php", data: send, dataType: "json", success:
                function (datos) {
                    if (datos.str > 0) {
                        for (i = 0; i < datos.str; i++) {
                            tl_total += parseFloat(datos[i]['totalVenta']);
                            tl_transacciones += parseFloat(datos[i]['Transacciones']);
                            tl_ticket += parseFloat(datos[i]['Ticket']);
                            tl_cupones += parseFloat(datos[i]['cuponesCanjeados']);
                            if (datos[i]['nombreUsuario'] != usr_anterior) {
                                html += '<tr><td>' + datos[i]['nombreUsuario'] + '</td><td class="text-center">' + datos[i]['fechaInicio'] + '</td><td class="text-center">' + datos[i]['cuponesCanjeados'] + '</td><td class="text-center">' + datos[i]['Transacciones'] + '</td><td class="text-center">' + parseFloat(datos[i]['totalVenta']).toFixed(2) + '</td><td class="text-center">' + parseFloat(datos[i]['Ticket']).toFixed(2) + '</td></tr>';
                            } else {
                                html += '<tr><td>' + datos[i]['nombreUsuario'] + '</td><td class="text-center">' + datos[i]['fechaInicio'] + '</td><td class="text-center">' + datos[i]['cuponesCanjeados'] + '</td><td class="text-center">' + datos[i]['Transacciones'] + '</td><td class="text-center">' + parseFloat(datos[i]['totalVenta']).toFixed(2) + '</td><td class="text-center">' + parseFloat(datos[i]['Ticket']).toFixed(2) + '</td></tr>';
                            }
                            usr_anterior = datos[i]['nombreUsuario'];
                        }
                        tl_ticket = tl_total / tl_transacciones;
                        html += '<tr class="active tbl_rsmn_vnts_ttls"><th colspan="2">Totales</th><td class="text-center">' + tl_cupones + '</td><td class="text-center">' + tl_transacciones + '</td><td class="text-center">' + tl_total.toFixed(2) + '</td><td class="text-center">' + tl_ticket.toFixed(2) + '</td></tr>';

                        $('#tbl_rsm_vnts_prd').append(html);
                    } else {
                        html += '<tr class="active tbl_rsmn_vnts_ttls"><th colspan="2">Totales</th><td class="text-center">' + tl_cupones + '</td><td class="text-center">' + tl_transacciones + '</td><td class="text-center">' + tl_total.toFixed(2) + '</td><td class="text-center">' + tl_ticket.toFixed(2) + '</td></tr>';
                        $('#tbl_rsm_vnts_prd').append(html);
                    }




                    //assumption: the column that you wish to rowspan is sorted.

                    //this is where you put in your settings
                    var indexOfColumnToRowSpan = 0;
                    var $table = $('#tbl_rsm_vnts_prd');


                    //this is the code to do spanning, should work for any table
                    var rowSpanMap = {};
                    $table.find('tr').each(function () {
                        var valueOfTheSpannableCell = $($(this).children('td')[indexOfColumnToRowSpan]).text();
                        $($(this).children('td')[indexOfColumnToRowSpan]).attr('data-original-value', valueOfTheSpannableCell);
                        rowSpanMap[valueOfTheSpannableCell] = true;
                    });

                    for (var rowGroup in rowSpanMap) {
                        var $cellsToSpan = $('td[data-original-value="' + rowGroup + '"]');
                        var numberOfRowsToSpan = $cellsToSpan.length;
                        $cellsToSpan.each(function (index) {
                            if (index == 0) {
                                $(this).attr('rowspan', numberOfRowsToSpan);
                            } else {
                                $(this).hide();
                            }
                        });
                    }



                }
    });
}

function fn_cargarAccesosSistema() {
    send = {"cargarAccesosPerfil": 1};
    send.pnt_id = 'resumenVentas.php';
    $.getJSON("config_resumenventas.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                switch (datos[i]['acc_descripcion']) {
                    case 'Salir':
                        $('#btn_salirSistema').attr("disabled", false);
                        $("#btn_salirSistema").removeClass("boton_Opcion_Bloqueado");
                        $("#btn_salirSistema").addClass('boton_Opcion');
                        break;
                    case 'Funciones Gerente':
                        $('#funcionesGerente').attr("disabled", false);
                        $("#funcionesGerente").removeClass("boton_Opcion_Bloqueado");
                        $("#funcionesGerente").addClass('boton_Opcion');
                        break;
                }
            }
        }
    });
}

function fn_obtenerMesa() {
    send = {"obtenerMesa": 1};
    $.getJSON("config_resumenventas.php", send, function (datos) {
        if (datos.str > 0) {
            if (datos.respuesta === 3) {
                $('#cntFormulario').html('<form action="../facturacion/factura.php" name="formulario" method="post" style="display:none;"><input type="text" name="odp_id" value="' + datos.IDOrdenPedido + '" /><input type="text" name="dop_cuenta" value="' + 0 + '" /><input type="text" name="mesa_id" value="' + datos.IDMesa + '" /></form>');
                document.forms['formulario'].submit();
            } else {
                window.location.href = "../ordenpedido/tomaPedido.php?numMesa=" + datos.IDMesa;
            }
        } else {
            alert("Este local no tiene mesas disponibles.");
        }
    });
}


function fn_salirSistema() {
    window.location.href = "../index.php";
}

function fn_funcionesGerente() {
    window.location.href = "../funciones/funciones_gerente.php";
}