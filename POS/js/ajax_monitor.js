/**
 * Desarrollado por: Joseph Purificacion
 * Descripción: Integración MXP con monitor de estados
 * Fecha: 14/06/2023
 */

function enviarMonitor(codigo,delivery=null) {
  console.log({"DELIVERY":delivery});
  // Validar si la política INTEGRAR MONITOR esta habilitada
  let validarPoliticaMonitor = { metodo: "validarPoliticaRestauranteMonitor" };
  let enviarMonitor = "";
  let estadoMonitor = [];
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: validarPoliticaMonitor,
    success: function (respValidador) {
      enviarMonitor = respValidador["habilitado"];
      console.log("HABILITAR MONITOR",enviarMonitor);
    },
  });
  // Integracion con Monitor;
  if (enviarMonitor == "SI") {
    // Recuperamos informacion de la factura
    let cargarDatosPedido = { metodo: "cargarDatosPedido" };
    let datosPedido="";
    let estado="";
    let medioPedido="";
    let cfac_id="";
    cargarDatosPedido.codigo_app = codigo;
    $.ajax({
      async: false,
      type: "POST",
      dataType: "json",
      contentType: "application/x-www-form-urlencoded",
      url: "../ordenpedido/config_app.php",
      data: cargarDatosPedido,
      success: function (respValidador) {
        datosPedido = respValidador;
        console.log({"DATA PEDIDO":datosPedido, "ESTADO":estado});
      },
    });
    // Recuperacion politica ENTREGA INMEDIATA
    let cargarEntregaInmediata =  {metodo:'getRestaurantConfig', collection: 'CONFIGURACION DOMICILIO', parameter:'ENTREGA INMEDIATA',config:'variableV'}
    let entregainmediata="";
    $.ajax({
      async: false,
      type: "POST",
      dataType: "json",
      contentType: "application/x-www-form-urlencoded",
      url: "../ordenpedido/config_app.php",
      data: cargarEntregaInmediata,
      success: function (respValidador) {
        entregainmediata=respValidador;
        console.log("ENTREGA INMEDIATA",entregainmediata);
      },
    });
    estado = datosPedido["estado"];
    medioPedido = datosPedido["medio"];
    cfac_id = datosPedido["cfac_id"];
    console.log("ESTADO FACTURA",delivery == "DT" && cfac_id !== null,cfac_id);
    console.log({"MEDIO":medioPedido.toUpperCase(),"ESTADO":estado, "DATA":datosPedido});
    if (medioPedido.toUpperCase() == "UBER") {
      switch (estado) {
        case "ASIGNADO":
          estadoMonitor = ["POR_ASIGNAR", "ASIGNADO"];
          break;
        case "ENTREGADO":
          estadoMonitor = ["EN_CAMINO", "ENTREGADO"];
          break;
      }
    } else if(medioPedido.toUpperCase() == "RAPPI" || medioPedido.toUpperCase() == "DIDI"){
      switch (estado) {
        case "POR ASIGNAR":
          estadoMonitor = ["POR_ASIGNAR", "ASIGNADO"];
          break;
        case "ENTREGADO":
          estadoMonitor = ["EN_CAMINO", "ENTREGADO"];
          break;
      }
    } /*else if((delivery=="DT" || delivery == "MU" || delivery == "CARGO" || delivery == "BRINGG" || delivery == "DUNA") && entregainmediata=="AUTOMATICA"){
      switch (estado) {
        case "POR ASIGNAR":
        estadoMonitor = ["POR_ASIGNAR", "ASIGNADO", "EN_CAMINO"];
      }
    }else if((delivery=="DT" || delivery == "MU" || delivery == "CARGO" || delivery == "BRINGG" || delivery == "DUNA") && entregainmediata!=="AUTOMATICA"){
      switch (estado) {
        case "POR ASIGNAR":
        estadoMonitor = ["ASIGNADO", "EN_CAMINO"];
      }
    } */else {
      switch (estado) {
        case "POR ASIGNAR":
          estadoMonitor = ["POR_ASIGNAR"];
          break;
        case "EN CAMINO":
          estadoMonitor = ["EN_CAMINO"];
          break;
        case "ANULADO":
          estadoMonitor = ["ANULADO"];
          break;
    default:
          estadoMonitor = [estado];
          break;
      }
    }
    let telefono = datosPedido["telefono"];
    let cliente = datosPedido["nombre_cliente"];
    let codigo_factura = datosPedido["cfac_id"];
    let motorolo = datosPedido["motorolo"];
    let tracking = datosPedido["tracking"];
    sendMonitor = { id: codigo };
    sendMonitor.timeline = estadoMonitor;
    sendMonitor.telefono = telefono;
    sendMonitor.canal = medioPedido;
    sendMonitor.motorolo = trim(motorolo) == "" ? null : motorolo;
    sendMonitor.tracking = trim(tracking) == "" ? null : tracking;
    sendMonitor.cfac_id = codigo_factura;
    sendMonitor.cliente = cliente;
    console.log({"ESTADO MONITOR ENVIAR":estadoMonitor,"SEND_MONITOR":sendMonitor});
    if (estadoMonitor !== "") {
      $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../clases/clase_monitor.php",
        data: sendMonitor,
        success: function (datos) {
          /*
          if (datos["status"]) {
            alertify.success(datos["message"]);
          } else {
            alertify.error(datos["message"]);
          }*/
        },
        error: function (jqXHR, textStatus, errorThrown) {
          /*
          ("No se pudo conectar al monitor");
          console.error(jqXHR);
          console.error(textStatus);
          console.error(errorThrown);*/
        },
      });
    }
  }
}
