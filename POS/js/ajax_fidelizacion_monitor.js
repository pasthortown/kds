var _ventaTotal = {};
var _fechaDesde = "";
var _fechaHasta = "";
var _productos = [];
var _formasPago = [];
var _recargas = [];
var _horas = ["00:00","01:00","02:00","03:00","04:00","05:00","06:00","07:00","08:00","09:00","10:00","11:00","12:00","13:00","14:00","15:00","16:00","17:00","18:00","19:00","20:00","21:00","22:00","23:00"];

$(document).ready(function (){
    cargando(1);
    $("#inFechaDesde").datepicker({"format": 'dd/mm/yyyy'});
    $("#inFechaHasta").datepicker({"format": 'dd/mm/yyyy'});
    //cargarTotalesVenta();
    //cargarTopDiezProductosRedimidos();
    //cargarFormasPagoHora();
    
    $("#btnCargarDatos").click(function(){
        var desde = $("#inFechaDesde").val();
        var hasta = $("#inFechaHasta").val();
        desde = cambiarFormatoFecha(desde);
        hasta = cambiarFormatoFecha(hasta);
        _fechaDesde = desde;
        _fechaHasta = hasta;
        cargarTotalesVenta();
        cargarTotalRecargas();
        cargarTopDiezProductosRedimidos();
        cargarFormasPagoHora();
    });
    
    cargando(0);
});

var cambiarFormatoFecha = function(fecha){
    var datosFecha = fecha.split("/");
    return datosFecha[2]+datosFecha[1]+datosFecha[0];
};

var cargarTotalesVenta = function () {
    //_ventaTotal = {"totalVenta": 300, "fechaPeriodo": "23-03-3018", "porcentajeVentaGeneral": 200, "porcentajeVentaAutoconsumo": 100};
    //pintarChartTotales();
    var send = {}
    send.metodo = "cargarTotalVentas";
    send.fechaDesde = _fechaDesde;
    send.fechaHasta = _fechaHasta;
    $.ajax({ async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminFidelizacionMonitor/serviciosMonitor.php", data: send, 
        success: 
            function (datos) {
                //alert(JSON.stringify(datos));
                _ventaTotal = datos;
                pintarChartTotales();
            }
    });
};

var cargarTotalRecargas = function () {
    //_ventaTotal = {"totalVenta": 300, "fechaPeriodo": "23-03-3018", "porcentajeVentaGeneral": 200, "porcentajeVentaAutoconsumo": 100};
    //pintarChartTotales();
    var send = {}
    send.metodo = "cargarTotalRecargas";
    send.fechaDesde = _fechaDesde;
    send.fechaHasta = _fechaHasta;
    $.ajax({ async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminFidelizacionMonitor/serviciosMonitor.php", data: send, 
        success: 
            function (datos) {
                //alert(JSON.stringify(datos));
                _recargas = datos;
                pintarChartRecargas();
            }
    });
};
var cargarTopDiezProductosRedimidos = function () {
    //_ventaTotal = {"totalVenta": 300, "fechaPeriodo": "23-03-3018", "porcentajeVentaGeneral": 200, "porcentajeVentaAutoconsumo": 100};
    //pintarChartTotales();
    var send = {}
    send.metodo = "cargarTopDiezProductosRedimidos";
    send.fechaDesde = _fechaDesde;
    send.fechaHasta = _fechaHasta;
    $.ajax({ async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminFidelizacionMonitor/serviciosMonitor.php", data: send, 
        success: 
            function (datos) {
                //alert(JSON.stringify(datos));
                _productos = datos;
                cargarProductosAutoconsumo();
            }
    });
};

var cargarFormasPagoHora = function () {
    var send = {}
    send.metodo = "cargarFormasPagoHora";
    send.fechaDesde = _fechaDesde;
    send.fechaHasta = _fechaHasta;
    $.ajax({ async: false, type: "POST", dataType: "json", contentType: "application/x-www-form-urlencoded", url: "../adminFidelizacionMonitor/serviciosMonitor.php", data: send, 
        success: 
            function (datos) {
                //alert(JSON.stringify(datos));
                _formasPago = datos;
                cargarFormasPago();
            }
    });
};

var pintarChartTotales = function(){
    
    Highcharts.chart('superiorIzquierda', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: 0,
            plotShadow: false
        },
        title: {
            text: '<b>TOTAL DE VENTA</b><br/><br/><p style="font-size: 11px">' + _fechaDesde + ' al ' + _fechaHasta + '</p><br/><br/><b>$' + _ventaTotal.totalVenta + '</b>',
            align: 'center',
            y: 40
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                dataLabels: {
                    enabled: true,
                    distance: -50,
                    style: {
                        fontWeight: '',
                        color: 'white'
                    }
                },
                startAngle: -90,
                endAngle: 90,
                center: ['50%', '75%']
            }
        },
        series: [{
            type: 'pie',
            name: 'Porcentaje',
            innerSize: '50%',
            data: [
                ['Venta', _ventaTotal.porcentajeVentaGeneral],
                ['Autoconsumo', _ventaTotal.porcentajeVentaAutoconsumo],
                {
                    name: 'Proprietary or Undetectable',
                    y: 0.2,
                    dataLabels: {
                        enabled: false
                    }
                }
            ]
        }]
    });

};

var cargarProductosAutoconsumo = function(){
    Highcharts.chart('superiorDerecha', {
        chart: {
            type: 'column'
        },
        title: {
            text: '<b>TOP 10 PRODUCTOS REDIMIDOS</b>'
        },
        subtitle: {
            text: '<p style="font-size: 11px">' + _fechaDesde + ' al ' + _fechaHasta + '</p>'
        },
        xAxis: {
            type: 'category',
            labels: {
                rotation: -45,
                style: {
                    fontSize: '10px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'CANTIDADES'
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: 'Cantidad: <b>{point.y}</b>'
        },
        series: [{
            name: 'Population',
            data: _productos,
            dataLabels: {
                enabled: true,
                rotation: -90,
                color: '#FFFFFF',
                align: 'right',
                format: '{point.y}', // one decimal
                y: 10, // 10 pixels down from the top
                style: {
                    fontSize: '12px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }]
    });
};

var cargarFormasPago = function () {
    
        Highcharts.chart('inferiorIzquierda', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'FORMAS DE PAGO POR HORAS'
        },
        subtitle: {
            text: ''
        },
        xAxis: {
            categories: _horas,
            title: {
                text: null
            },
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: '',
                align: ''
            },
            labels: {
                overflow: 'justify'
            }
        },
        tooltip: {
            valueSuffix: ''
        },
        plotOptions: {
            bar: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            }
        },
        legend: {
            layout: 'horizontal',
            align: 'center',
            verticalAlign: 'top',
            x: -40,
            y: 30,
            floating: true,
            borderWidth: 1,
            backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
            shadow: true
        },
        credits: {
            enabled: false
        },
        series: _formasPago
    });
    
};

var cargando = function (estado) {
    if (estado) {
        $("#mdl_rdn_pdd_crgnd").css("display", "block");
        $("#mdl_pcn_rdn_pdd_crgnd").css("display", "block");
    } else {
        $("#mdl_rdn_pdd_crgnd").css("display", "none");
        $("#mdl_pcn_rdn_pdd_crgnd").css("display", "none");
    }
};
var pintarChartRecargas = function(){
    Highcharts.chart('inferiorDerecha', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'RECARGAS POR CAJERO'
        },
        subtitle: {
            text: '<p style="font-size: 11px">' + _fechaDesde + ' al ' + _fechaHasta + '</p>'
        },
        xAxis: {
            type: 'category',
            labels: {
                rotation: -45,
                style: {
                    fontSize: '10px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        },
        yAxis: {
            min: 0,
            title: {
                text: 'VALOR $USD'
            }
        },
        legend: {
            enabled: false
        },
        tooltip: {
            pointFormat: 'Valor $USD: <b>{point.y}</b>'
        },
        plotOptions: {
            bar: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            }
        },
        series: [{
            name: '',
            data: _recargas,
            dataLabels: {
                enabled: true,
                rotation: -90,
                color: '#FFFFFF',
                align: 'right',
                format: '{point.y}', // one decimal
                y: 10, // 10 pixels down from the top
                style: {
                    fontSize: '12px',
                    fontFamily: 'Verdana, sans-serif'
                }
            }
        }]
    });

};