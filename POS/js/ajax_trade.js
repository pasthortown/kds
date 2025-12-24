var cambioEstadoTrade = function (codigo_app, estado, medio) {
  console.log("entre a cambio de estado " + medio);
  console.log(codigo_app);
  console.log(estado);
  let timeOut       = localStorage.getItem('time_out');
  let html = ""; 
  send = { metodo: "cambioEstadoTrade" };
  send.codigo_app = codigo_app;
  send.estado = estado;
  send.medio = medio;
  send.timeOut = timeOut;
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_cambio_estados.php",
    data: send,
    success: function (datos) {
      console.log("DATOS");
      console.log(datos);
      console.log("Respuesta Cambio Estado");
      console.log(datos);
    },
  });
};

var cambioEstadoTradeVarias = function (arrtransacciones, estado, medio) {
  arrtransacciones = JSON.parse(arrtransacciones);
  arrtransacciones.forEach((transaccion) => {
    let {cambio_estado, codigoApp, medio} = transaccion;
    if (cambio_estado && cambio_estado == "SI") { 
      cambioEstadoTrade(codigoApp, estado, medio);
    }
  });
};

var actualizarEventosTransaccionesAsignadas = function (idMotorizado) {
  var idRestaurante = $("#hide_rst_id").val();
  var idCadena = $("#hide_cdn_id").val();

  console.log("Ruta", location.Origin);

  var html = "";
  send = { metodo: "listaTransaccionesAsignadas" };
  send.idCadena = idCadena;
  send.idRestaurante = idRestaurante;
  send.idMotorizado = idMotorizado;
  urlConsume =
    location.origin +
    "/" +
    location.pathname.split("/")[1] +
    "/ordenpedido/config_app.php";
  console.log("url a consumir", urlConsume);
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: urlConsume,
    data: send,
    success: function (datos) {
      console.log("Datos");
      console.log(datos);

      if (datos.registros > 0) {
        var estado = datos[0]["estado"];
        var transacciones = [];
        for (var i = 0; i < datos.registros; i++) {
          transacciones.push(datos[i]["codigo_app"]);
        }
        cambioEstadoTradeVarias(transacciones, estado);
      }
    },
  });
};

var trackingTrade = function (objetoTracking, medio, codigoApp = 0) {
  console.log("Entre a Tracking Trade");

  var html = "";
  send = { metodo: "trackingTrade" };
  send.objetoTracking = objetoTracking;
  send.medio = medio;
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_cambio_estados.php",
    data: send,
    success: function (datos) {
      console.log("DATOS");
      console.log(datos);
      console.log("Respuesta TRADE");
      console.log(datos);

      if (datos.length > 0 && datos[0] === "NO_APLICA" && codigoApp !== 0) {
        cambioEstadoTrade(codigoApp, "Por Asignar", medio);
      }
      
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error(jqXHR);
      console.error(textStatus);
      console.error(errorThrown);
    },
  });
};

//funcion para cambiar estado a pedidos anulados
var cambioEstadoTradePorFactura = function (cfac_id, estado) {
  console.log("entre a cambio de por factura - anulacion");
  console.log(cfac_id);
  console.log(estado);

  var html = "";
  send = { metodo: "cambioEstadoTradePorFactura" };
  send.cfac_id = cfac_id;
  send.estado = estado;
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_cambio_estados.php",
    data: send,
    success: function (datos) {
      console.log("DATOS");
      console.log(datos);
      console.log("Respuesta Pedido Anulado");
      console.log(datos);
    },
  });
};
