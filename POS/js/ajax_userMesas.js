/* global alertify */ 

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Clase para validacion de combos//////////
//////////////////////////////////////////////////////////////
///////FECHA CREACION: 20-Enero-2014//////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
var num_accion = 0;
var cod_piso = 0;
var cod_area = 0;
var sw_numPersonas = 0;
var categoria = -1;
var mesa = 0;
var vistaInfo = false;
var perfilAcceso = false;
var unionMesa = false; // bandera para controlar si la union de mesa esta activa para que el usuario seleccione las mesa.
var vistaInfoTran = false;
var modalPersonas = "";
var perfil_Usuario = "";
var filaSeleccionada = '';
var HabilitarRPM = false;
var TiempoRPM = false;
var tiempoEspera;
var listaPermisos = new Array();
var tipoUser;
var img = "";
var img_E = "";
var listaMedios = [];
var agregadoresArray=[];
var reintentos_maximo_duna=0;
var runReintentosOnUpdate=false;

var tiempoDespacho=[];
var tiempoMotorizado=[];
var tiempoTotalPedido=[];

var comparaTiempo=[];

var tiempoEsperaEstadoPendiente=[];
var tiempoEsperaEstadoRecibido=[];
var tiempoEsperaEstadoPorAsignar=[];
var tiempoEsperaEstadoAsignado=[];
var tiempoEsperaEstadoEnCamino=[];

//Jean Meza Cevallos Mantener lista
var ultimoElementoSeleccionado="";
var mesa_descripcion="";


$(document).ready(function () {
  agregadores();
  fn_cargarConfiguracionRestaurante();
  $("#modalTransferirMesas").hide();
  $("#aumentarContador").hide();
  $("#mdl_rdn_pdd_crgnd").hide();
  $("#modal_cargando_pedido").hide();
  fn_LoadAccess($("#txtUsuarioLogin").val(), $("#txtPantalla").val());
  fn_Loaduser($("#txtUsuarioLogin").val());
  fn_ResfrescarPanelMesas(); // funcion que actualiza el panel de mesa, esto se puede desactivar por colecccion.
  fn_PedidoRapidoEnEspera();
  modalPersonas = fn_modalPersonas();
  fn_popupCantidad();
  obtenerActualizarCodigo(1);

  $("#cntLocalesTransferencia").shortscroll();
  $("#cntLocalesTransferenciaPickup").shortscroll();
  $("#cntMotivosTransferencia").shortscroll();
  $("#cntMotivosTransferenciaPickup").shortscroll();

  cargarPiso();
  perfilAcceso = existe("Acceso a mesas ocupadas", listaPermisos);

  localStorage.removeItem('cuenta');
  localStorage.removeItem('tipo');

  document.oncontextmenu = function () {
    return false;
  };
  $("#mccs").shortscroll({
    scrollSpeed: 200,
  });

  if (
    typeof GetURLParameter("IDPisos") !== "undefined" &&
    typeof GetURLParameter("IDAreaPiso") !== "undefined"
  ) {
    var IDPisos = GetURLParameter("IDPisos");
    var IDAreaPiso = GetURLParameter("IDAreaPiso");
    CargarArea(IDPisos);
    var imagen = $("#" + GetURLParameter("IDAreaPiso")).val();
    cargarDatos(IDPisos, imagen);
    fn_cargaMesas($("#txtRest").val(), IDPisos, IDAreaPiso);
    cod_area = IDAreaPiso;
    cod_piso = IDPisos;

    setInterval(cargarPedidosRecibidos,30000);
  }

  $("#cboEstadoPedido").change(function () {
    ultimoElementoSeleccionado="";
    seleccionCambioEstado();
  });

  $("#parBusqueda").val("");
  $("#parBusqueda").keypad({
      onClose: function(value, inst) { 
        busqueda();
      }
    }
  );

  $("#cboEstadoPedido").hide();
  
  $("#listaPedido").shortscroll();
  $("#detalle_pedido").shortscroll();

    if (cod_area && IDPisos) {

      fn_cargarMotivosAnulacion();
      fn_cargarProveedorTracking();
      fn_cargarSemaforoConfig();
      // La configuración ahora es por restaurante y medio
      //fn_cambioEstadosAutomatico();
      fn_cargarURLCrearPedidoBringg();
      fn_cargarURLAnularPedidoBringg();

    }
    // La configuración ahora es por restaurante y medio
    //fn_cambioEstadosAutomatico();

    $("#btn_pedidos_ocultar_app").hide();

    /*lECTURA VITALITY
    $("#txtCodigoVitality").on("change", function() {
        var codigoV = $("#txtCodigoVitality").val();
        $("#txtCodigoVitality").val(codigoV);
        cargarTokenSeguridadVitality(codigoV);
    });
    */

   localStorage.removeItem("id_menu");
   localStorage.removeItem("id_cla");
   localStorage.removeItem("es_menu_agregador");
   localStorage.removeItem("id_menu_facturacion");
   localStorage.removeItem('id_agregador');
   localStorage.removeItem("id_cla_facturacion");
   localStorage.removeItem("es_menu_agregador_facturacion");
   localStorage.removeItem("timeoutQPM");
   $('#anulacionesContenedor').hide();
   $('#anulacionesMotivo').hide();

      // Tienes Pedidos en espera
  const queryString = window.location.search;
  const urlParams = new URLSearchParams(queryString);
  // Acceder a los valores de los parámetros GET
  const PedidosPendiente = urlParams.get('PedidosPendiente');
  const pedidoRapidoPendiente = urlParams.get('pedidoRapidoPendiente');

  if (PedidosPendiente == 1) {

    habilitarContenedorPedidos();
    $("#modal_cargando_pedido").hide();
    $("#modalTransferirMesas").hide();
    $("#aumentarContador").hide();
    $("#mdl_rdn_pdd_crgnd").hide();

  }

  if (pedidoRapidoPendiente == 1){
      pedidoRapido(0)
  }

});

function fn_cambioEstadosAutomatico() {
  send = { metodo: "cambioEstadosAutomatico" };
  send.cdn_id = $("#txtRest").val();
  //send.cdn_id = $("#txtCadena").val();
  console.log('CAMBIO ESTADOS');
  $.ajax({
    async: true,
    type: "POST",
    dataType: 'json',
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function( datos ) {

      console.log('CAMBIO ESTADOS');
      if(datos && datos[0]){    
        $("#cambio_estados_automatico").val(datos[0].automatico);
      }

      //console.log('CAMBIO ESTADO AUTOMATICO');
      //console.log($("#cambio_estados_automatico").val());

    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR);
      console.log(textStatus);
      console.log(errorThrown);
    }
  });
}

function fn_cargarURLCrearPedidoBringg(){
  send = { metodo: "cargarURLCrearPedidoBringg" };
  send.rst_id = $("#txtRest").val();
  $.ajax({
    async: true,
    type: "POST",
    dataType: 'json',
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function( datos ) {
      if(datos && datos[0]){    
        $("#url_bringg_crear").val(datos[0].url);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR);
      console.log(textStatus);
      console.log(errorThrown);
    }
  });
}

function fn_cargarURLAnularPedidoBringg(){
  send = { metodo: "cargarURLAnularPedidoBringg" };
  send.rst_id = $("#txtRest").val();
  $.ajax({
    async: true,
    type: "POST",
    dataType: 'json',
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function( datos ) {
      if(datos && datos[0]){    
        $("#url_bringg_anular").val(datos[0].url);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR);
      console.log(textStatus);
      console.log(errorThrown);
    }
  });
}

function fn_cargarProveedorTracking(){
    send = { metodo: "cargarPoliticaProveedorTracking" };
    send.cdn_id =   $("#txtCadena").val();
    $.ajax({
        async: true,
        type: "POST",
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded",
        url: "../ordenpedido/config_app.php",
        data: send,
        success: function( datos ) {
            if(datos && datos[0]){    
                $("#proveedor_tracking").val(datos[0].metodo);
            }
        }
    });
}

function fn_cargarSemaforoConfig() {
  send = { metodo: "cargarSemaforoConfig" };
  send.cdn_id = $("#txtCadena").val();
  $.ajax({
    async: true,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function (datos) {
      console.log(datos);
      if (datos) {
        $("#semaforoConfig").val(JSON.stringify(datos));
      }
    },
  });
  if ($("#ValidacionSesionApi").val().length>0) {
    alertify.alert('Version 1 de api en curso. Error API V2: '+ $("#ValidacionSesionApi").val());
  }
}

function fn_cargarConfiguracionRestaurante() {
  var send;
  send = { cargarConfiguracionRestaurante: 1 };
  $.getJSON("../anulacion/config_anularOrden.php", send, function (datos) {
    if (datos.str > 0) {
      $("#hide_tipo_servicio").val(datos[0]["tpsrv_descripcion"]);
      $("#aplica_nc_sinconsumidor").val(datos[0]["aplica_nc_sinconsumidor"]);
      $("#config_servicio_domicilio").val(datos[0]["servicioDomicilio"]);
      $("#config_servicio_pickup").val(datos[0]["servicioPickup"]);

      if (datos[0]["servicioDomicilio"] == 1) {
        cargarEstadosPedidos();
        fn_cargarProveedorTracking();
        fn_cargarMotivosAnulacion();
        fn_cargarSemaforoConfig();
        // La configuración ahora es por restaurante y medio
        //fn_cambioEstadosAutomatico();
        fn_cargarURLCrearPedidoBringg();
        fn_cargarURLAnularPedidoBringg();
        $("#btn_pedidos_entregados").show();
        $("#btn_pedidos_app").show();
      } else {
        $("#btn_pedidos_entregados").hide();
        $("#btn_pedidos_app").hide();
      }


      if (datos[0]["servicioPickup"] == 1) {
        $("#btn_pedidos_pickup_app").show();
      } else {
        $("#btn_pedidos_pickup_app").hide();
      }

    } else {
      $("#btn_pedidos_entregados").hide();
      $("#btn_pedidos_app").hide();
      $("#btn_pedidos_pickup_app").hide();
    }

  });
}

function fn_cargarMotivosAnulacion() {
  var send;
  var html = "<option value='0'>- Seleccionar Opci&oacute;n -</option>";
  send = { motivoAnulacion: 1 };
  $.getJSON("../anulacion/config_anularOrden.php", send, function (datos) {
    if (datos.str > 0) {
      for (var i = 0; i < datos.str; i++) {
        html +=
          "<option value='" +
          datos[i]["mtv_id"] +
          "'>" +
          datos[i]["mtv_descripcion"] +
          "</option>";
      }
      $("#motivosAnulacion").html(html);
    }
  });
}

function cargarEstadosPedidos() {
  var html = "<option value='PRINCIPAL'>EN PROCESO</option>";
  html += "<option value='ANULADO'>ANULADO</option>";
  html += "<option value='ENTREGADO'>ENTREGADO</option>";
  html += "<option value='EN CAMINO'>EN CAMINO</option>";
  html += "<option value='PENDIENTE'>PENDIENTES</option>";
  html += "<option value='RECIBIDO'>RECIBIDOS</option>";
  html += "<option value='POR ASIGNAR'>POR ASIGNAR</option>";
  html += "<option value='ASIGNADO'>ASIGNADOS</option>";

  $("#cboEstadoPedido").html(html);
}

function seleccionCambioEstado() {
  cargarDeliveryPedidosPendientes();
  runReintentosOnUpdate=true;
  $("#listaPedido").show();
  $("#div_busqueda").show();
  $("#detalle_pedido").hide();
  $("#btn_ver").hide();
  $("#btn_cancelar").hide();

  var estado_seleccionado = $("#cboEstadoPedido").val();
  var estado_seleccionado_label = jQuery("#cboEstadoPedido option:selected").text();

  $("#tituloPedidos").html("PEDIDOS " + estado_seleccionado_label);

  if ( estado_seleccionado != "ENTREGADO" && estado_seleccionado != "ANULADO" ) {
    var html = "";
    $("#listado_pedido_app").html(html);
    send = { metodo: "cargarPedidosApp" };
    send.estado = "RECIBIDO";
    (send.estadoBusqueda = estado_seleccionado), (send.parametroBusqueda = "");
    $("#mdl_rdn_pdd_crgnd").show();

    $.ajax({
      async: false,
      type: "POST",
      dataType: "json",
      contentType: "application/x-www-form-urlencoded",
      url: "../ordenpedido/config_app.php",
      data: send,
      success: function (datos) {
        $("#mdl_rdn_pdd_crgnd").hide();
        $("#sltd_codigo_app").html('');
        $("#sltd_estado_app").html('');
        if (datos.registros > 0) {
          cargarBurbujasBotonesApp(datos.cantidad[0], datos.todos[0]);
          cargarLista(datos.pedidos[0]);

        } else {
          $("#listado_pedido_app").append(
            '<li class="datos_app"><div>No existen pedidos.</div></li>'
          );
        }

        //obtenerCantidadEstadosPedidosApp();
        obtenerCantidadEstadosPedidosFiltrados();
      },
    });

    verificarBotones();
    $("#mdl_rdn_pdd_crgnd").hide();

  } else {
    cargarPedidosEntregados( estado_seleccionado, "" );
  }

}

async function cargarDeliveryPedidosPendientes() {
  var html = "";
  $("#listado_pendientes").html(html);
  send = { metodo: "cargarDeliveryPedidosPendientes" };
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function (datos) {
      if (datos.registros > 0) {
        cargarListaPendientes(datos);
      } else {
        $("#listado_pendientes").append(
          '<li class="datos_app"><div>No existen pedidos.</div></li>'
        );
      }
    },
  });
}

async function cargarListaPendientes(pedidos) {
  console.log("cargando pedidos");
  let cantidadTotalPedidos = pedidos.registros;
  let pedidosFiltrados = [];
  let contadorRegistros = 0;
  for (
    let indexPedidos = 0;
    indexPedidos < cantidadTotalPedidos;
    indexPedidos++
  ) {
    let pedido = pedidos[indexPedidos];
    pedidosFiltrados.push(pedido);
    contadorRegistros++;
  }
  pedidosFiltrados.registros = contadorRegistros;
  pedidos = pedidosFiltrados;
  let datos = pedidos;

  let cantidadPedidos = datos.registros;
  $("#listado_pendientes").empty();
  if (cantidadPedidos == 0) {
    $("#listado_pendientes").append(
      '<li class="datos_app"><div>No existen pedidos.</div></li>'
    );
  }
  for (var i = 0; i < cantidadPedidos; i++) {
    html =
      '<li class="datos_app" ' +
      'id="' +
      datos[i]["codigo_app"] +
      '" fecha="' +
      datos[i]["fecha"] +
      '" medio="' +
      datos[i]["medio"] +
      '" codigo_factura="' +
      datos[i]["codigo_factura"] +
      '" codigo_externo="' +
      datos[i]["codigo_externo"] +
      '" duna_reintentos="' +
      datos[i]["duna_reintentos"] +
      '" crea_duna="' +
      datos[i]["crea_duna"] +
      '" asigna_duna="' +
      datos[i]["asigna_duna"] +
      '" envio_inmediato="' +
      datos[i]["envio_inmediato"] +
      '" opciones_proveedor="' +
      datos[i]["opciones_proveedor"] +
      '" retira_efectivo="' +
      datos[i]["retira_efectivo"] +
      '"></li>';
    $("#listado_pendientes").append(html);
  }
  setTimeout(() => {
    //para que no cuelgue la carga de pedidos peticion se hace dentro de timeout
    runAfterListPendiente();
  }, 0);
}

function teclado(elEvento) {

  evento = elEvento || window.event;
  k = evento.keyCode; //nÃºmero de cÃ³digo de la tecla.
  //teclas nÃºmericas del teclado alfamunÃ©rico
  if (k > 47 && k < 58) {
    p = k - 48; //buscar nÃºmero a mostrar.
    p = String(p); //convertir a cadena para poder aÃ±Ã¡dir en pantalla.
    //fn_agregarNumero(p); //enviar para mostrar en pantalla
  }
  //Teclas del teclado nÃºmerico. Seguimos el mismo procedimiento que en el anterior.
  if (k > 95 && k < 106) {
    p = k - 96;
    p = String(p);
    //fn_agregarNumero(p);
  }
  if (k == 110 || k == 190) {
    fn_agregarNumero(".");
  } //teclas de coma decimal
  if (k == 8) {
    fn_eliminarNumero();
  } //Retroceso en escritura : tecla retroceso.
  if (k > 57 && k < 210) {
    document.getElementById("parBusqueda").value = "";
  }
}

function busqueda() {
  var parametroBusqueda = $("#parBusqueda").val();

  if ( !parametroBusqueda ) {
    parametroBusqueda = "";
  }

  var estado_seleccionado = $("#cboEstadoPedido").val();
  var estado_seleccionado_label = jQuery(
    "#cboEstadoPedido option:selected"
  ).text();

  $("#tituloPedidos").html("PEDIDOS " + estado_seleccionado_label);

  if ( estado_seleccionado != "ENTREGADO" && estado_seleccionado != "ANULADO" ) {

    var html = "";
    $("#listado_pedido_app").html(html);
    send = { metodo: "cargarPedidosApp" };
    send.estado = "RECIBIDO";
    (send.estadoBusqueda = estado_seleccionado),
      (send.parametroBusqueda = parametroBusqueda);
    $.ajax({
      async: false,
      type: "POST",
      dataType: "json",
      contentType: "application/x-www-form-urlencoded",
      url: "../ordenpedido/config_app.php",
      data: send,
      success: function (datos) {
        if (datos.registros > 0) {
          cargarBurbujasBotonesApp(datos.cantidad[0], datos.todos[0]);
          cargarLista(datos.pedidos[0]);
        } else {
          $("#listado_pedido_app").append(
            '<li class="datos_app"><div>No existen pedidos.</div></li>'
          );
        }
        //obtenerCantidadEstadosPedidosApp();
        obtenerCantidadEstadosPedidosFiltrados();
      },
    });

  } else {
    cargarPedidosEntregados( estado_seleccionado, parametroBusqueda );
  }
  
}

function CambioElEstadoDeMesa(idMesa_prm, estado_prm) {
  var result = "Iguales";
  $("#mesas div").each(function () {
    var idMesa = $(this).attr("id");
    var estado = $(this).children("a").children("img").attr("src");
    estado = estado.substring(
      estado.lastIndexOf("/") + 1,
      estado.lastIndexOf(".")
    );

    if (idMesa.trim() === idMesa_prm.trim()) {
      if (estado.trim() !== estado_prm.trim()) {
        result = "No iguales";
      } else {
        result = "Iguales";
      }
    }
  });
  return result;
}

function refresh() {
  if (cod_area !== "" && cod_piso !== "") {
    //   fn_actualizaEstadosMesa( $("#txtRest").val(), cod_piso, cod_area);
    var estado = 0;
    send = { CargarMesa: 1 };
    send.rest = $("#txtRest").val();
    send.piso = cod_piso;
    send.area = cod_area;
    send.est_id = $("#est_id").val();
    send.user_pos = $("#txtUsuarioLogin").val();
    send.idPeriodo = $("#idPeriodo").val();
    $.ajax({
      async: false,
      type: "GET",
      dataType: "json",
      contentType: "application/x-www-form-urlencoded",
      url: "config_UserMesas.php",
      data: send,
      success: function (datos) {
        for (i = 0; i < datos.str; i++) {
          var IdMesaSQL = datos[i]["mesa_id"];
          var StatusMesaSQl = datos[i]["std_descripcion"];
          var respuesta = CambioElEstadoDeMesa(IdMesaSQL, StatusMesaSQl);
          if (respuesta === "No iguales" || datos[i]["mi_mesa"]) {
            var IdDiv = $("#" + IdMesaSQL);

            IdDiv.removeClass(IdDiv.attr("class"));
            IdDiv.addClass("mesa");
            IdDiv.addClass(datos[i]["mi_mesa"]);

            $("#" + IdMesaSQL)
              .children("a")
              .children("img")
              .attr(
                "src",
                datos[i]["tmes_ruta_imagen"] +
                  datos[i]["std_descripcion"] +
                  ".png"
              );
            $("#" + IdMesaSQL)
              .children("a")
              .attr(
                "onMouseOver",
                "MM_swapImage('mesa" +
                  IdMesaSQL +
                  "','','" +
                  datos[i]["tmes_ruta_imagen"] +
                  datos[i]["std_descripcion"] +
                  "_E.png" +
                  "',1)"
              );
            $("#" + IdMesaSQL)
              .children("a")
              .attr(
                "onMouseOut",
                "MM_swapImage('mesa" +
                  IdMesaSQL +
                  "','','" +
                  datos[i]["tmes_ruta_imagen"] +
                  datos[i]["std_descripcion"] +
                  ".png" +
                  "',1)"
              );
            //
          }
          // $('#mesas').append("<div  onclick=\"fn_irOrdenPedido('" + datos[i]['mesa_id'] + "',1,'" + datos[i]['std_descripcion'] + "')\"  estacion='" + datos[i]['Estacion_asociada'] + "' class='mesa " + datos[i]['mi_mesa'] + "' align='center' id='" + datos[i]['mesa_id'] + "' style='width:" + dimension[0] + "px;height:" + dimension[1] + "px;left:" + datos[i]['mesa_coordenadax'] + "px; top:" + datos[i]['mesa_coordenaday'] + "px; position:absolute;'><a onMouseOver=\"MM_swapImage('mesa" + datos[i]['mesa_id'] + "','','" + datos[i]['tmes_ruta_imagen'] + datos[i]['std_descripcion'] + "_E.png',1)\" onMouseOut='MM_swapImgRestore()' href='#' ><img  style=\"width:100%; height:100%\" name='mesa" + datos[i]['mesa_id'] + "' src='" + datos[i]['tmes_ruta_imagen'] + datos[i]['std_descripcion'] + ".png' border='0' ><br/><label style='position:absolute; font-size: 9px; color: white;top:50%;left:50%;transform: translate(-50%, -50%);'>" + datos[i]['mesa_descripcion'] + "</label></a></div>");
        }
        if (datos.str > 0) {
          $("#btn_pedidos_app span").attr("badge", datos[0]["total_pedidos"]);
          $("#btn_pedidos_error span").attr("badge", datos[0]["errores"]);
        } else {
          $("#btn_pedidos_app span").attr("badge", "");
          $("#btn_pedidos_error span").attr("badge", "");
        }
      },
    });
    // Si está activo bringg refresca los filtros de pedidos
    // obtenerCantidadEstadosPedidosApp();

  }
  parar();
}

var HabilitarRPM = false;

function SetHabilitadoRPM(dato) {
  HabilitarRPM = dato;
}

function GetHabilitadoRPM() {
  return HabilitarRPM;
}

var TiempoRPM = false;

function SetTiempoRPM(dato) {
  TiempoRPM = dato;
}

function GetTiempoRPM() {
  return TiempoRPM;
}

function fn_ResfrescarPanelMesas() {
  send = { ResfrescarPanelMesas: 1 };
  send.rst_id = $("#txtRest").val();
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "config_UserMesas.php",
    data: send,
    success: function (datos) {
      if (datos.str > 0) {
        SetHabilitadoRPM(datos[0]["habilitado"]);
        SetTiempoRPM(datos[0]["tiempo"]);
      } else {
        SetHabilitadoRPM(false);
        SetTiempoRPM(0);
      }
    },
  });
}

var tiempoEspera;

function ini() {
  if (GetHabilitadoRPM() === "Si") {
    tiempoEspera = setTimeout(refresh, GetTiempoRPM());
  }
  if ($("#ValidacionRucCodigoMensaje").val().length>0) {
    alertify.alert($("#ValidacionRucCodigoMensaje").val());
  }
}

function parar() {
  if (GetHabilitadoRPM() === "Si") {
    clearTimeout(tiempoEspera);
    tiempoEspera = setTimeout(refresh, GetTiempoRPM());
  }
}

function fn_PedidoRapidoEnEspera() {
  send = { pedidoRapidoEnEspera: 1 };
  send.rst_id = $("#txtRest").val();
  send.est_id = $("#est_id").val();

  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "config_UserMesas.php",
    data: send,
    success: function (datos) {
      if (datos.str > 0) {
        $("#PedidoRapido").attr("style", datos[0]["style"]);
        $("#PedidoRapido").attr("title", datos[0]["title"]);
        if (datos[0]["mensaje"] === "Si") {
          $("#PedidoRapido").focus();
          alertify.success(datos[0]["mens_alerta"]);
        }
      }
    },
  });
}

var listaPermisos = new Array();

function fn_LoadAccess(usuario_id, pantalla) {
  send = { cargarPermisos: 1 };
  send.usuario_id = usuario_id;
  send.pantalla = pantalla;
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../seguridades/config_usuario.php",
    data: send,
    success: function (datos) {
      if (datos.str > 0) {
        for (var i = 0; i < datos.str; i++) {
          if (
            document.getElementsByName(datos[i]["descripcion"])[0] !== undefined
          ) {
            var IDBoton = document.getElementsByName(datos[i]["descripcion"])[0]
              .id;
            $("#" + IDBoton).removeAttr("disabled");
            $("#" + IDBoton).removeClass("boton_Accion_Disable1");
            $("#" + IDBoton).addClass("boton_Accion1");
          }

          listaPermisos[i] = datos[i]["descripcion"];
        }
      } else {
        alertify.error("No cuenta con ningún permiso sobre ésta pantalla.");
      }
    },
  });
}

var tipoUser;

function setTipoUser(tipo) {
  tipoUser = tipo;
}

function GetTipoUser() {
  return tipoUser;
}

function fn_Loaduser(usuario_id) {
  $("#infoUsuario").html("");
  send = { cargarUsuario: 1 };
  send.usuario_id = usuario_id;
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../seguridades/config_usuario.php",
    data: send,
    success: function (datos) {
      if (datos.str > 0) {
        setTipoUser(datos[0]["perfil"]);
        $("#perfil_usuario").html("<strong>" + datos[0]["perfil"] + ": </strong>");
        $("#nombre_usuario").html(datos[0]["usuario"]);
        perfil_Usuario = datos[0]["perfil"];
      }
    },
  });
}

//Cargar combo de la cadena
function cargarPiso() {
  var codRestaurante = $("#txtRest").val();
  send = { cargarPiso: 1 };
  send.codigo = codRestaurante;
  send.est_id = $("#est_id").val();
  send.cnd_id = $("#txtCadena").val();
  $.ajax({
    async: false,
    type: "GET",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "config_UserMesas.php",
    data: send,
    success: function (datos) {
      if (datos.str > 0) {
        sw_numPersonas = datos[0]["rst_num_personas"];
        $("#piso").html("");
        for (i = 0; i < datos.str; i++) {
          $("#piso").append(
            '<button   class="' +
              datos[i]["classCss"] +
              '" onclick="CargarArea(\'' +
              datos[i]["pis_id"] +
              "')\">" +
              datos[i]["pis_numero"] +
              "</button>"
          );
        }
        if ($("#cargoPisoArea").val() === "No") {
          CargarArea(datos[0]["pisoDefecto"]);
        }
      }
    },
  });
}

function CargarArea(codigopiso) {
  $("#mesas").attr("style", "");
  $("#mesas").html("");
  $("#area").html("");
  setPiso(codigopiso);
  var send = { CargarArea: 1 };
  send.codigo = codigopiso;
  send.est_id = $("#est_id").val();
  send.cnd_id = $("#txtCadena").val();
  $.ajax({
    async: false,
    type: "GET",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "config_UserMesas.php",
    data: send,
    success: function (datos) {
      if (datos.str > 0) {
        $("#area").html("");
        for (i = 0; i < datos.str; i++) {
          //$("#area").append("<input type='button' class='area_button' onclick=\"cargarDatos()\"  value='"+datos[i]['arp_descripcion'].substr(0,1)+"\' ></input>");
          $("#area").append(
            "<input type='button' style=\"width: 100%\" class='area_button " +
              datos[i]["classCss"] +
              "' onclick=\"cargarDatos('" +
              datos[i]["arp_id"] +
              "','" +
              datos[i]["arp_imagen"] +
              "')\"  value='" +
              (datos[i]["arp_descripcion"] === ""
                ? "Nombre no disponible"
                : datos[i]["arp_descripcion"]) +
              "' ></input>"
          );
          $("#area").append(
            "<input type='hidden' id=" +
              datos[i]["arp_id"] +
              " value=" +
              datos[i]["arp_imagen"] +
              "></input>"
          );
        }

        if ($("#cargoPisoArea").val() === "No") {
          for (i = 0; i < datos.str; i++) {
            if (datos[i]["classCss"] === "boton_Accion_EE1") {
              cargarDatos(datos[i]["AreaPisoDefecto"], datos[i]["arp_imagen"]);
              break;
            }
          }
        }
      }
    },
  });
}

function cargarDatos(area, imagen) {
  $("#listado_pedido_app li").removeClass("focus");
  $("#pnl_pickup").hide();
  $("#mesas").show();
  $("#detalle_plu").hide();
  $("#cnt_pedidos").hide();

  $("#btn_pedidos_error").show();
  $("#btn_imprimir_error").hide();
  $("#cnt_pedidos_error").hide();

  if ($("#config_servicio_domicilio").val() == 1) {
    $("#btn_pedidos_app").show();
    $("#btn_pedidos_entregados").show();
  } else {
    $("#btn_pedidos_app").hide();
    $("#btn_pedidos_entregados").hide();
  }

  $("#btn_ver").hide();
  $("#btn_en_camino").hide();
  $("#btn_asignar").hide();
  $("#btn_confirmar").hide();
  $("#btn_entregado").hide();
  $("#btn_cancelar").hide();
  $("#btn_facturar").hide();
  $("#btn_transferir").hide();
  $("#btn_anular").hide();
  $("#btn_reenviar_bringg,#btn_reenviar_moto_duna").hide();
  $("#cuado").show();

  mostrarOcultarBotonesPolitica();
  pararTimerSemaforo();
  mostrarOcultarBotonesPedidosApp(0);

  setArea(area);
  //$(".mesa").empty();
  $("#contenedorRetomarOrden").show();
  var codRestaurante = $("#txtRest").val();

  var codCadena = $("#txtCadena").val();
  codPiso = $("#piso").val();
  codArea = $("#area").val();
  //     cod_piso=codPiso;
  //    cod_area=area;

  var cadena = codCadena + "_" + codRestaurante + "_" + codPiso + "_" + codArea;
  var objeto = document.getElementById("mesas");
  objeto.style.backgroundImage = "url(data:image/png;base64," + imagen + ")";
  objeto.style.backgroundRepeat = "no-repeat";
  objeto.style.width = "100%";
  objeto.style.height = "100%";
  //    objeto.style.width = (screen.width - (screen.width * 0.3)) + 'px';
  //    objeto.style.height = (screen.height - (screen.height * 0.1)) + 'px';

  $("#mesas").css("background-repeat", "no-repeat");
  $("#mesas").css("background-size", "100% 100%");
  $("#mesas").css("height", "625px");

  fn_cargaMesas(codRestaurante, getPiso(), getArea());
}
/////////////////////////////CARGAR LA IMAGEN////////////////////////////////////////////

/////////////////////////////CARGA LAS MESAS DE ACUERDO A LA BASE DE DATOS//////////////
function fn_cargaMesas(codRestaurante, codPiso, codArea) {

  //****actualiza ordenes de pedido "odp_total=0"
  var cod_estacion = $("#est_id").val();
  var cod_usuario = $("#txtUsuarioLogin").val();
  var cod_periodo = $("#idPeriodo").val();
  fn_actualizarOrdenesPedido(cod_estacion,cod_usuario,cod_periodo);
  //****

  var send;
  var estado = 0;

  send = { CargarMesa: 1 };
  send.rest = codRestaurante;
  send.piso = codPiso;
  send.area = codArea;
  send.est_id = $("#est_id").val();
  send.user_pos = $("#txtUsuarioLogin").val();
  send.idPeriodo = $("#idPeriodo").val();

  $.ajax({
    async: false,
    type: "GET",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "config_UserMesas.php",
    data: send,
    success: function (datos) {
      if (datos.str > 0) {
        $("#mesas").html("");
        var nombreCliente = "";
        var texto = "";

        if ($("#switch").val() == 1) {
          for (var i = 0; i < datos.str; i++) {
            var dimension = datos[i]["mesa_dimension"].split("|");
            nombreCliente = datos[i]["nombreCliente"];
            texto =
              "<label style='position: absolute; font-weight: bold;!important font-size: 12px; color: white; top: 55%; left: 50%; transform: translate(-50%, -50%);'>" +
              nombreCliente +
              "</label>";

            if (nombreCliente != "") {
              $("#mesas").append(
                "<button  onclick=\"fn_irOrdenPedido('" +
                  datos[i]["mesa_id"] +
                  "',1,'" +
                  datos[i]["std_descripcion"] +
                  "')\"  estacion='" +
                  datos[i]["Estacion_asociada"] +
                  "' class='mesa " +
                  datos[i]["mi_mesa"] +
                  "' align='center' id='" +
                  datos[i]["mesa_id"] +
                  "' style='width:" +
                  dimension[0] +
                  "px;height:" +
                  dimension[1] +
                  "px;left:" +
                  datos[i]["mesa_coordenadax"] +
                  "%; top:" +
                  datos[i]["mesa_coordenaday"] +
                  "%; position:absolute;background-color: Transparent; background-repeat:no-repeat;cursor:pointer;overflow: hidden;'><a onMouseOver=\"MM_swapImage('mesa" +
                  datos[i]["mesa_id"] +
                  "','','" +
                  datos[i]["tmes_ruta_imagen"] +
                  datos[i]["std_descripcion"] +
                  "_E.png',1)\" onMouseOut='MM_swapImgRestore()' href='#' ><img  style=\"width:100%; height:100%\" name='mesa" +
                  datos[i]["mesa_id"] +
                  "' src='" +
                  datos[i]["tmes_ruta_imagen"] +
                  datos[i]["std_descripcion"] +
                  ".png' border='0' ><br/><label style='position:absolute; font-size: 19px; color: white;top:30%;left:50%;transform: translate(-50%, -50%);'>" +
                  datos[i]["mesa_descripcion"] +
                  "</label>" +
                  texto +
                  "</a></button>"
              );
            } else {
              $("#mesas").append(
                "<button  onclick=\"fn_irOrdenPedido('" +
                  datos[i]["mesa_id"] +
                  "',1,'" +
                  datos[i]["std_descripcion"] +
                  "')\"  estacion='" +
                  datos[i]["Estacion_asociada"] +
                  "' class='mesa " +
                  datos[i]["mi_mesa"] +
                  "' align='center' id='" +
                  datos[i]["mesa_id"] +
                  "' style='width:" +
                  dimension[0] +
                  "px;height:" +
                  dimension[1] +
                  "px;left:" +
                  datos[i]["mesa_coordenadax"] +
                  "%; top:" +
                  datos[i]["mesa_coordenaday"] +
                  "%; position:absolute;background-color: Transparent; background-repeat:no-repeat;cursor:pointer;overflow: hidden;'><a onMouseOver=\"MM_swapImage('mesa" +
                  datos[i]["mesa_id"] +
                  "','','" +
                  datos[i]["tmes_ruta_imagen"] +
                  datos[i]["std_descripcion"] +
                  "_E.png',1)\" onMouseOut='MM_swapImgRestore()' href='#' ><img  style=\"width:100%; height:100%\" name='mesa" +
                  datos[i]["mesa_id"] +
                  "' src='" +
                  datos[i]["tmes_ruta_imagen"] +
                  datos[i]["std_descripcion"] +
                  ".png' border='0' ><br/><label style='position:absolute; font-size: 19px; color: white;top:50%;left:50%;transform: translate(-50%, -50%);'>" +
                  datos[i]["mesa_descripcion"] +
                  "</label></a></button>"
              );
            }
          }
        } else {
          for (var i = 0; i < datos.str; i++) {
            var dimension1 = datos[i]["mesa_dimension"].split("|");
            nombreCliente = datos[i]["nombreCliente"];
            texto =
              "<label style='position: absolute; font-weight: bold;!important font-size: 12px; color: white; top: 55%; left: 50%; transform: translate(-50%, -50%);'>" +
              nombreCliente +
              "</label>";

            if (nombreCliente != "") {
              $("#mesas").append(
                "<button  onclick=\"fn_irOrdenPedido('" +
                  datos[i]["mesa_id"] +
                  "',1,'" +
                  datos[i]["std_descripcion"] +
                  "')\" estacion='" +
                  datos[i]["Estacion_asociada"] +
                  "'  class='mesa " +
                  datos[i]["mi_mesa"] +
                  "' align='center' id='" +
                  datos[i]["mesa_id"] +
                  "' style='width:" +
                  dimension1[0] +
                  "px;height:" +
                  dimension1[1] +
                  "px;left:" +
                  datos[i]["mesa_coordenadax"] +
                  "%; top:" +
                  datos[i]["mesa_coordenaday"] +
                  "%; position:absolute;background-color: Transparent; background-repeat:no-repeat;cursor:pointer;overflow: hidden;'><a onMouseOver=\"MM_swapImage('mesa" +
                  datos[i]["mesa_id"] +
                  "','','" +
                  datos[i]["tmes_ruta_imagen"] +
                  datos[i]["std_descripcion"] +
                  "_E.png',1)\" onMouseOut='MM_swapImgRestore()' href='#'><img  style=\"width:100%; height:100%\" name='mesa" +
                  datos[i]["mesa_id"] +
                  "' src='" +
                  datos[i]["tmes_ruta_imagen"] +
                  datos[i]["std_descripcion"] +
                  ".png' border='0' ><br/><label style='position:absolute; font-size: 19px; color: white;top:30%;left:50%;transform: translate(-50%, -50%);'>" +
                  datos[i]["mesa_descripcion"] +
                  "</label>" +
                  texto +
                  "</a></button>"
              );
            } else {
              $("#mesas").append(
                "<button  onclick=\"fn_irOrdenPedido('" +
                  datos[i]["mesa_id"] +
                  "',1,'" +
                  datos[i]["std_descripcion"] +
                  "')\" estacion='" +
                  datos[i]["Estacion_asociada"] +
                  "'  class='mesa " +
                  datos[i]["mi_mesa"] +
                  "' align='center' id='" +
                  datos[i]["mesa_id"] +
                  "' style='width:" +
                  dimension1[0] +
                  "px;height:" +
                  dimension1[1] +
                  "px;left:" +
                  datos[i]["mesa_coordenadax"] +
                  "%; top:" +
                  datos[i]["mesa_coordenaday"] +
                  "%; position:absolute;background-color: Transparent; background-repeat:no-repeat;cursor:pointer;overflow: hidden;'><a onMouseOver=\"MM_swapImage('mesa" +
                  datos[i]["mesa_id"] +
                  "','','" +
                  datos[i]["tmes_ruta_imagen"] +
                  datos[i]["std_descripcion"] +
                  "_E.png',1)\" onMouseOut='MM_swapImgRestore()' href='#'><img  style=\"width:100%; height:100%\" name='mesa" +
                  datos[i]["mesa_id"] +
                  "' src='" +
                  datos[i]["tmes_ruta_imagen"] +
                  datos[i]["std_descripcion"] +
                  ".png' border='0' ><br/><label style='position:absolute; font-size: 19px; color: white;top:50%;left:50%;transform: translate(-50%, -50%);'>" +
                  datos[i]["mesa_descripcion"] +
                  "</label></a></button>"
              );
            }
          }
        }
      }
    },
  });
}

function fn_activarEirOrdenPedido(mesa_id, estado, std_id) {
  vistaInfo = false;
  fn_irOrdenPedido(mesa_id, estado, std_id);
}

function fn_transferirMesas(mesa_id, estado, std_id) {
  std_id = fn_estadoMesa(mesa_id);
  if (
    std_id !== "Disponible" &&
    (perfil_Usuario !== "Mesero" || perfil_Usuario !== "MESERO")
  ) {
    $("#tituloModalTransferir").html(
      "<h4>Cambiar productos de la Mesa <strong> " +mesa_descripcion+
        $("div#" + mesa_id + " > a > label").text() +
        "</strong><br> a la Mesa:</h4>"
    );
    $("#modalInfoMesas").modal("toggle");
    $("#modalTransferirMesas").show();

    $("#modalTransferirMesas").dialog({
      modal: true,
      position: {
        my: "left",
        at: "left",
      },
      width: 500,
      heigth: 500,
      resize: false,
      opacity: 0,
      show: "none",
      hide: "none",
      duration: 500,
      open: function (event, ui) {
        $(".ui-dialog-titlebar").hide();
        $("#input_transferirMesa").val("");
        fn_alfaNumericov2("#input_transferirMesa", mesa_id, std_id);
      },
    });
  } else {
    alertify.alert("La mesa no tiene productos para transferir");
  }
}

function fn_okTransferirMesas(mesa_id, std_id) {
  var value = $("#input_transferirMesa").val();
  std_id = fn_estadoMesa(mesa_id);
  if (
    std_id !== "Disponible" &&
    (perfil_Usuario !== "Mesero" || perfil_Usuario !== "MESERO")
  ) {
    if (value.length > 0) {
      send = { transferirMesas: 1 };
      send.mesa_id_origen = mesa_id;
      send.nombre_mesa_destino = value;
      send.IDPeriodo = $("#idPeriodo").val();
      $.getJSON("config_ordenPedido.php", send, function (datos) {
        if (datos.str > 0) {
          //alert(datos[0]['mensaje']);
          alertify.alert(datos[0]["mensaje"]);
          fn_modalTransferirMesas();
          refresh();
        } else {
          alert("Error al transferir la mesa, comuníquense con soporte");
        }
      });
      //alertify.success('La transferencia a la mesa ' + value + ' se realizó exitosamente')
    } else {
      alertify.error("No ha digitado ninguna mesa ");
    }
  } else {
    alertify.alert("La mesa no tiene productos para transferir");
  }
}

function fn_modalTransferirMesas() {
  $("#keyboard").hide();
  $("#keyboard").empty();
  $("#modalTransferirMesas").hide();
  $("#modalTransferirMesas").dialog("close");
}

var img = "";
var img_E = "";

function existe(elemento, arreglo) {
  for (var i = 0; i < arreglo.length; i++) {
    if (arreglo[i] === elemento) {
      return true;
    }
  }
  return false;
}

function fn_irOrdenPedido(mesa_id, estado, std_id) {
  var aplicaActualizacion = getActualizacionPendiente();
  if (aplicaActualizacion){
      buildAlertActualizacion();
      return;
  }
  var acceso = perfilAcceso;
  //var fac_pendiente = fn_hayfacturaPendiente(mesa_id);
  var fac_pendiente = 0;
  var datosMensaje = "";
  var cat_id = "";
  var odp_id = "";
  var codigo_app = "";
  var rst_id = $("#txtRest").val();
  // obtener el estado de mesa atual.
  var send = { VerificarMisMesa: 1 };
  send.mesa_id = mesa_id;
  send.periodo_id = $("#idPeriodo").val();
  send.estacion_id = $("#est_id").val();
  send.user_login = $("#txtUsuarioLogin").val();
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "config_UserMesas.php",
    data: send,
    success: function (datosIMG) {
      fac_pendiente = datosIMG[0]["facturaPendiente"];
      datosMensaje = datosIMG[0]["mensaje"];
      cat_id = datosIMG[0]["cat_id"];
      odp_id = datosIMG[0]["odp_id"];
      codigo_app = datosIMG[0]["codigo_app"];
      std_id = encodeURI(datosIMG[0]["estado_mesa"]);
      if (datosIMG.str > 0) {
        img = datosIMG[0]["ruta"];
        img_E = datosIMG[0]["ruta_E"];
        if (datosMensaje === "Si") {
          if (
            (fac_pendiente === false || datosMensaje === "Si") &&
            (acceso || datosMensaje === "Si")
          ) {
            if (!fn_retomaFacturaPendiente(mesa_id, codigo_app)) {
              setMesa(mesa_id);
              mesa = mesa_id;
              if (vistaInfo) {
                $("#mdl_rdn_pdd_crgnd").show();
                informacion_Mesa(mesa_id);
                $("#mdl_rdn_pdd_crgnd").hide();
              } else {
                //                                if (estado) {
                switch (std_id) {
                  case encodeURI("En Uso"):
                  case encodeURI("Cuenta"):
                    window.location.href =
                      "tomaPedido.php?numMesa=" +
                      mesa_id +
                      "&estadoMesa=" +
                      std_id +
                      "&cat_id=" +
                      cat_id +
                      "&rst_id=" +
                      rst_id +
                      "&codigo_app=" +
                      codigo_app;
                    break;
                  case encodeURI("Disponible"):
                  case encodeURI("Activo"):
                    if (modalPersonas === "Si") {
                      $("#aumentarContador").show();
                      $("#aumentarContador").dialog("open");
                    } else {
                      window.location.href =
                        "tomaPedido.php?numMesa=" +
                        mesa_id +
                        "&cat_id=" +
                        cat_id +
                        "&rst_id=" +
                        rst_id +
                        "&numPers=" +
                        1 +
                        "&codigo_app=" +
                        codigo_app;
                    }
                    break;
                  case encodeURI("Division de Cuentas"):
                  case encodeURI("Cuenta Dividida"):
                    window.location.href = ".." + fn_url(mesa_id, odp_id);
                    break;
                  case 31:
                    fn_retomarCuentaAbierta(mesa);
                    break;
                  default:
                    window.location.href =
                      "tomaPedido.php?numMesa=" +
                      mesa_id +
                      "&codigo_app=" +
                      codigo_app;
                }

                //                                    if (std_id === 'En Uso' || std_id === 'Cuenta') {
                //                                        window.location.href = "tomaPedido.php?numMesa=" + mesa_id + "&estadoMesa=" + std_id + "&cat_id=" + cat_id + "&rst_id=" + rst_id;
                //                                    } else if (std_id === 'Disponible' || std_id === 'Activo') {
                //                                        if (modalPersonas === 'Si') {
                //                                            $("#aumentarContador").show();
                //                                            $("#aumentarContador").dialog("open");
                //                                        } else {
                //                                            window.location.href = "tomaPedido.php?numMesa=" + mesa_id + "&cat_id=" + cat_id + "&rst_id=" + rst_id + "&numPers=" + (1);
                //                                        }
                //                                    } else if (std_id === 'Division de Cuentas' || std_id === 'Cuenta Dividida') {
                //                                        window.location.href = ".." + fn_url(mesa_id, odp_id);
                //                                    } else if (std_id === 31) {
                //                                        fn_retomarCuentaAbierta(mesa);
                //                                    }
                //                                } else {
                //                                    alertify.alert('No puede acceder a ésta mesa porque está siendo atendida por otro mesero.');
                //                                }
              }
            } else {
              document.forms["cobro"].submit();
            }
          } else {
            alertify.alert(
              "La mesa está siendo atendida por el usuario: " +
                datosIMG[0]["usr_usuario"]
            );
          }
        } else {
          $("#" + mesa_id)
            .children("a")
            .children("img")
            .attr("src", datosIMG[0]["ruta"]);
          $("#" + mesa_id)
            .children("a")
            .attr(
              "onMouseOver",
              "MM_swapImage('mesa" +
                mesa_id +
                "','','" +
                datosIMG[0]["ruta_E"] +
                "',1)"
            );
          $("#" + mesa_id)
            .children("a")
            .attr(
              "onMouseOut",
              "MM_swapImage('mesa" +
                mesa_id +
                "','','" +
                datosIMG[0]["ruta"] +
                "',1)"
            );
          var mensaje =
            GetTipoUser() === "MESERO"
              ? "La mesa está siendo atendida por el usuario: " +
                datosIMG[0]["usr_usuario"]
              : "La mesa está siendo atendida por el usuario: " +
                datosIMG[0]["usr_usuario"];
          alertify.alert(mensaje);
        }
      }
    },
  });
}

function fn_retomaFacturaPendiente(mesa_id, codigo_app) {
  var retomaFac;
  send = { retomaFacturaPendiente: 1 };
  send.mesa_id = mesa_id;
  send.opcion = 1;
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "config_UserMesas.php",
    data: send,
    success: function (data) {
      if (data.str > 0) {
        $("#formsEnvio").html(
          '<form action="../facturacion/factura.php" name="cobro" method="post" style="display:none;"><input type="text" name="odp_id" value="' +
            data[0]["IDCabeceraOrdenPedido"] +
            '" /><input type="text" id="dop_cuenta" name="dop_cuenta" value="' +
            data[0]["dop_cuenta"] +
            '" /><input type="text" name="mesa_id" value="' +
            mesa_id +
            '" /> <input type="text" name="codigo_app" value="' +
            codigo_app +
            '" /></form>'
        );
        retomaFac = true;
      } else {
        retomaFac = false;
      }
    },
  });
  return retomaFac;
}

function fn_hayfacturaPendiente(mesa_id) {
  var retomaFac;
  send = { retomaFacturaPendiente: 1 };
  send.mesa_id = mesa_id;
  send.opcion = 2;
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "config_UserMesas.php",
    data: send,
    success: function (data) {
      if (data.str > 0) {
        //    $("#formsEnvio").html('<form action="../facturacion/factura.php" name="cobro" method="post" style="display:none;"><input type="text" name="odp_id" value="' + data[0]['IDCabeceraOrdenPedido'] + '" /><input type="text" id="dop_cuenta" name="dop_cuenta" value="' + data[0]['dop_cuenta'] + '" /><input type="text" name="mesa_id" value="' + mesa_id + '" /></form>');

        retomaFac = true;
      } else {
        retomaFac = false;
      }
    },
  });
  return retomaFac;
}

function fn_estadoMesa(mesa_id) {
  var retomaFac;
  var rutaMesa = "";
  var rutaMesa_E = "";
  send = { estadoMesa: 1 };
  send.IDMesa = mesa_id;
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "config_UserMesas.php",
    data: send,
    success: function (data) {
      if (data.str > 0) {
        retomaFac = data[0]["estado_mesa"];
        rutaMesa = data[0]["ruta"];
        rutaMesa_E = data[0]["ruta_E"];
      } else {
        retomaFac = data[0]["estado_mesa"];
        rutaMesa = data[0]["ruta"];
        rutaMesa_E = data[0]["ruta_E"];
      }
      mesa_descripcion=data[0]["mesa_descripcion"];
    },
  });
  $("#" + mesa_id)
    .children("a")
    .children("img")
    .attr("src", rutaMesa);
  $("#" + mesa_id)
    .children("a")
    .attr(
      "onMouseOver",
      "MM_swapImage('mesa" + mesa_id + "','','" + rutaMesa_E + "',1)"
    );
  $("#" + mesa_id)
    .children("a")
    .attr(
      "onMouseOut",
      "MM_swapImage('mesa" + mesa_id + "','','" + rutaMesa + "',1)"
    );
  return retomaFac;
}

function fn_salir() {
  window.location.href = "../index.php";
}

function fn_url(mesa_id, odp_id) {
  var url;
  send = { obtiene_url: 1 };
  send.mesa_id = mesa_id;
  send.odp_id = odp_id;
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "config_UserMesas.php",
    data: send,
    success: function (datos) {
      if (datos.str > 0) {
        url = datos[0]["url_cuenta"];
      } else {
        alertify.error("La estación no tiene configurada una mesa.");
      }
    },
  });
  return url;
}

/*----------------------------------------------------------------------------------------------------
 Funci�n retoma una cuenta abierta
 -----------------------------------------------------------------------------------------------------*/
function fn_retomarCuentaAbierta(num_mesa) {
  var odp_id = 0;
  var cfac_id = "";
  var dop_cuenta = 0;
  var mesa_iden = num_mesa;
  send = { retomarCuentaAbierta: 1 };
  send.mesa_id = mesa_iden;
  send.rest_id = $("#txtRest").val();
  $.getJSON("config_UserMesas.php", send, function (datos) {
    if (datos.str > 0) {
      odp_id = datos[0]["odp_id"];
      cfac_id = datos[0]["cfac_id"];
      /*			alertify.alert("odp_id: "+odp_id+", factura: "+cfac_id);
             return false;*/
      $("#contenedorRetomarOrden").html(
        '<form action="../facturacion/factura.php" name="cobro" method="post" style="display:none;"><input type="text" name="odp_id" value="' +
          odp_id +
          '" /><input type="text" name="dop_cuenta" value="' +
          dop_cuenta +
          '" /><input type="text" name="mesa_id" value="' +
          mesa_iden +
          '" /></form>'
      );
      document.forms["cobro"].submit();
    }
  });
}

function fn_guardarNumeroPersonas() {}

function setCategoria(cat) {
  categoria = cat;
}

function GetCategoria() {
  return categoria;
}

function setMesa(prmmesa) {
  mesa = prmmesa;
}

function GetMesa() {
  return mesa;
}

function setArea(area) {
  cod_area = area;
}

function getArea() {
  return cod_area;
}

function setPiso(piso) {
  cod_piso = piso;
}

function getPiso() {
  return cod_piso;
}

function fn_alertaPermisosPerfil(mns) {
  alertify.alert(mns);
}

function fn_irReservas() {
  window.location.href = "reservas/reservas.php";
}

function fn_irCorteCaja() {
  window.location.href = "../corteCaja/corteCaja.php";
}

function fn_irFuncionesGerente() {
  var aplicaActualizacion = getActualizacionPendiente();
  if (aplicaActualizacion){
      buildAlertActualizacion();
      return;
  }else{
    window.location.href = "../funciones/funciones_gerente.php";
  }
}

function fn_salirSistema() {
  window.location.href = "../index.php";
}

function fn_agregarNumero(valor) {
  lc_cantidad = document.getElementById("cantidad").value;
  if (valor === "0") {
    if (lc_cantidad === "" || lc_cantidad === "0") {
      $("#cantidad").val("");
      alertify.alert("Por favor, ingrese un número válido de personas");
      return; // Detener la ejecución de la función
    }
  }
  if (lc_cantidad === "0" && valor === ".") {
    document.getElementById("cantidad").value = "0.";
    coma = 1;
  } else {
    if (valor === "." && coma === 0) {
      lc_cantidad = lc_cantidad + valor;
      document.getElementById("cantidad").value = lc_cantidad;
      coma = 1;

    } else if (valor === "." && coma === 1) {
    } else {
      $("#cantidad").val("");
      lc_cantidad = lc_cantidad + valor;
      document.getElementById("cantidad").value = lc_cantidad;
    }
  }
}

/*----------------------------------------------------------------------------------------------------
 Funci�n para eliminar un n�mero
 -----------------------------------------------------------------------------------------------------*/
function fn_eliminarCantidad() {
  var lc_cantidad = document
    .getElementById("cantidad")
    .value.substring(0, document.getElementById("cantidad").value.length - 1);
  if (lc_cantidad == "") {
    lc_cantidad = "";
    coma = 0;
  }
  if (lc_cantidad == ".") {
    coma = 0;
  }
  document.getElementById("cantidad").value = lc_cantidad;
}

function pedidoRapido(valid) {
  var aplicaActualizacion = getActualizacionPendiente();
  if (aplicaActualizacion){
      buildAlertActualizacion();
      return;
  }
  console.log('valid en fast food')
  console.log(valid)
  //GUARDANDO NUMERO DE INTENTOS DE VALIDACION DE EMAIL EN LOCAL STORAGE
  localStorage.setItem('intValida',valid);
  localStorage.setItem('intValidaOP', valid);
  localStorage.setItem('intValidaFC', valid);
  localStorage.setItem('intValidaCC', valid);
	var send = { pedidoRapido: 1 };
	send.est_id = $('#est_id').val();
	send.cdn_id = $('#txtCadena').val();
	$.ajax({
		async: false,
		type: 'POST',
		dataType: 'json',
		contentType: 'application/x-www-form-urlencoded',
		url: '../ordenpedido/config_UserMesas.php',
		data: send,
		success: function (datos) {
			if (datos.str > 0) {
				var mesa = datos[0]['IDMesa'];
				send = { fidelizacionActiva: 1 };
				send.idRestaurante = $('#txtRest').val();
				$.ajax({
					async: false,
					type: 'POST',
					dataType: 'json',
					contentType: 'application/x-www-form-urlencoded',
					url: '../ordenpedido/config_UserMesas.php',
					data: send,
					success: function (datos) {
						var pantalla = 'tomaPedido.php';
						if (datos !== null && datos.fidelizacionActiva == 1) {
							pantalla = 'tomaPedido.php';
						}
						window.location.href = pantalla + '?numMesa=' + mesa;
					}
				});
			} else {
				alertify.error('La estación no tiene configurada una mesa.');
			}
		}
	});
}

function fn_unionMesa() {
  //        if ($('#buttonUnion').attr("class") === "boton_Accion info_unionMesa") {
  //
  //        $('#buttonUnion').removeClass();
  //        $('#buttonUnion').addClass("boton_Accion info_unionMesa_E");
  //        alertify.success('Seleccione las mesas');
  //        unionMesa = true;
  //    } else {
  //        $('#buttonUnion').removeClass();
  //        $('#buttonUnion').addClass("boton_Accion info_unionMesa");
  //        unionMesa = false;
  //    }
  //
  //        $('#buttonInfo').removeClass();
  //        $('#buttonInfo').addClass("boton_Accion info_mesa_btn");
  //        vistaInfo = false;
}

/*----------------------------------------------------------------------------------------------------
 Funciones de popup para cambiar la Cantidad de los productos
 -----------------------------------------------------------------------------------------------------*/
function fn_popupCantidad() {
  $("#cantidad").val("");
  $("#aumentarContador").dialog({
    modal: true,
    autoOpen: false,
    resizable: false,
    show: {
      effect: "blind",
      duration: 500,
    },
    hide: {
      effect: "explode",
      duration: 500,
    },
    width: "auto",
    buttons: {
      Continuar: function () {
        send = { configuracionOrdenPedido: 1 };
        send.mesa_id = GetMesa();
        send.num_Pers = $("#cantidad").val();
        send.vistaInfo = 0;
        $.getJSON("config_ordenPedido.php", send, function (datos) {
          var rst_id = $("#txtRest").val();
          setCategoria(datos[0]["cat_id"]);
        });
        var can = $("#cantidad").val();
        if (can.length > 0 && can <= 50) {
          var mesa = GetMesa();
          var categoria = GetCategoria();
          var rst_id = $("#txtRest").val();
          window.location.href =
            "tomaPedido.php?numMesa=" +
            mesa +
            "&cat_id=" +
            categoria +
            "&rst_id=" +
            rst_id +
            "&numPers=" +
            can;
        } else if (can > 50) {
          $("#cantidad").val("");
          alertify.alert("Por favor, ingrese un número válido de personas");

        }
      },
      Cancelar: function () {
        $("#cantidad").val("");
        $(this).dialog("close");
        $("#keyboard").hide();
      },
    },
    open: function (event, ui) {
      $(".ui-dialog-titlebar").hide();
    },
  });
}

function informacion_Mesa(idMesa) {
  $("#nombre_mesa").html(
    $("#" + idMesa)
      .children("a")
      .children("label")
      .html()
  );
  var estado = $("#" + idMesa).attr("onclick");
  estado = estado.substring(estado.lastIndexOf(",") + 2);
  estado = estado.substring(0, estado.indexOf("'"));
  $("#contenedor_info_mesa").html("");
  estado = fn_estadoMesa(idMesa);
  var bandera = true;
  if (estado === "Disponible") bandera = false;
  if (estado === "Activo") bandera = false;
  if (bandera) {
    var orden_p = "";
    var send = { configuracionOrdenPedido: 1 };
    send.mesa_id = idMesa;
    send.num_Pers = 1;
    send.vistaInfo = 1;
    send.estado = 1;
    $.getJSON("config_ordenPedido.php", send, function (datos) {
      var send1 = { informacionMesa: 1 };
      orden_p = datos[0]["odp_id"];
      send1.odp_id = datos[0]["odp_id"];
      send1.est_ip = "";
      send1.IDMesa = idMesa;
      $.getJSON("config_ordenPedido.php", send1, function (datos) {
        $("#num_splits").html(datos[0]["cantidad_splits"]);
        $("#num_cliente").html(datos[0]["numero_personas"]);
        $("#capacidad_mesa").html(datos[0]["tipo_mesa"]);
        $("#mi_mesa").html(datos[0]["mi_mesa"]);
        $("#estado").html(datos[0]["estado"]);
        var send2 = { informacionMesaAll: 1 };
        send2.odp_id = orden_p;
        $.getJSON("config_ordenPedido.php", send2, function (datos) {
          var html = "";
          for (i = 0; i < datos.str; i++) {
            html =
              html +
              '<center style="margin-top: 5%">\n' +
              "                                                <section>\n" +
              "                                                    <article>\n" +
              "                                                        <div><strong>Transacci&oacute;n N°: </strong> " +
              datos[i]["numero_transaccion"] +
              "</div>\n" +
              "                                                        <div><strong>Informaci&oacute;n transacci&oacute;n: </strong> <span>" +
              datos[i]["transaccion_info"] +
              "</span></div>\n" +
              "                                                        <div><strong>Última Orden: </strong> <span>" +
              datos[i]["last_order"] +
              "</span></div>\n" +
              "                                                        <br />\n" +
              "                                                          <div><strong>Observaciones </strong></div>\n" +
              '                                                           <div><span style="margin: 0; padding: 0"> ' +
              datos[i]["observacion"] +
              " </span></div>\n" +
              "                                                          <div><strong>Total final </strong></div>\n" +
              '                                                           <div><h2 style="margin: 0; padding: 0">$ ' +
              datos[i]["final_total"] +
              " </h2></div>\n" +
              "                                                           <hr />\n" +
              "                                                    </article>\n" +
              "                                                </section>\n" +
              "                                            </center>";
          }
          $("#contenedor_info_mesa").html(html);
        });
      });
    });
  } else {
    var send1 = { informacionMesa: 1 };
    send1.odp_id = "";
    send1.est_ip = "";
    send1.IDMesa = idMesa;
    $.getJSON("config_ordenPedido.php", send1, function (datos) {
      $("#num_splits").html(datos[0]["cantidad_splits"]);
      $("#num_cliente").html(datos[0]["numero_personas"]);
      $("#capacidad_mesa").html(datos[0]["tipo_mesa"]);
      $("#mi_mesa").html(datos[0]["mi_mesa"]);
      $("#estado").html(datos[0]["estado"]);
    });
  }

  var evento = $("#" + idMesa).attr("onclick");
  evento = evento.substring(evento.indexOf("("), evento.length);
  var btn = document.getElementById("btn_modal");
  btn.click();
  var html =
    '<button type="button" data-dismiss="modal" class="btn btn-danger btn-lg"> <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Cancelar</button>\n' +
    '<button id="transferirMesas" type="button" onclick="fn_transferirMesas' +
    evento +
    '" class="btn btn-info btn-lg"> <span class="glyphicon glyphicon-retweet" aria-hidden="true"></span> Cambiar<br>Mesa</button>\n' +
    '<button type="button" onclick="fn_activarEirOrdenPedido' +
    evento +
    '"   id="btn_ir_orden_pedido"  class="btn btn-primary btn-lg"> <span class="glyphicon glyphicon-ok" aria-hidden="true"> </span> Retomar</button>\n';
  $("#footerButton").html(html);
  html2canvas($("#" + idMesa), {
    onrendered(canvas) {
      var link = document.getElementById("download");
      var image = canvas.toDataURL();
      document.getElementById("img_fondo").src = canvas.toDataURL();
    },
  });
}

function fn_opcion_informacion() {
  if (
    $("#informacionPorMesa").attr("class") === "info_mesa_E_btn boton_Accion1"
  ) {
    $("#cnt_pedidos").hide();
    $("#btn_pedidos_app").show();
    $("#btn_pedidos_entregados").show();

    $("#btn_imprimir_error").hide();
    $("#btn_pedidos_error").show();
    $("#cnt_pedidos_error").hide();

    $("#btn_facturar").hide();
    $("#btn_ver").hide();
    $("#btn_cancelar").hide();
    $("#cuado").show();
    $("#informacionPorMesa").removeClass();
    $("#informacionPorMesa").addClass("boton_Accion1 info_mesa_btn");
    alertify.success("Seleccione una mesa");
    vistaInfo = true;
    // otro boton cambiar estado
    $("#informacionTotalMesas").removeClass();
    $("#informacionTotalMesas").addClass("view_btn boton_Accion1");
    vistaInfoTran = false;
    mostrarOcultarBotonesPolitica();

  } else {
    $("#informacionPorMesa").removeClass();
    $("#informacionPorMesa").addClass("info_mesa_E_btn boton_Accion1");
    vistaInfo = false;
  }
  fn_opcion_transaccion();
  //       $('#buttonUnion').removeClass();
  //        $('#buttonUnion').addClass("boton_Accion info_unionMesa");
  //        unionMesa = false;
}

function obtenerUsuarios() {
  send = { obtenerUsuarios: 1 };
  send.opcion = 2;
  send.IDUsersPos = $("#txtUsuarioLogin").val();
  $.ajax({
    type: "GET",
    data: send,
    url: "config_UserMesas.php",
    dataType: "json",
    success: renderListaUsuarios,
  });
}

function renderListaUsuarios(data) {
  $(".listarUsuario option").remove();
  if (data.str < 1) {
    alert("NINGÚN RESULTADO");
  } else {
    $(".listarUsuario").append(
      '<option value="0">Seleccionar Usuario...</option>'
    );
    for (i = 0; i < data.str; i++) {
      $(".listarUsuario").append(
        '<option style="height:20px" id=' +
          data[i]["IDEstacion"] +
          "  value=" +
          data[i]["IDUsersPos"] +
          ">" +
          data[i]["est_nombre"] +
          "</option>"
      );
    }
    $(".listarUsuario").focus();
  }
}

function fn_botonlistarUsuario() {
  if (
    $("#transferir_cuenta").attr("class") === "transferirCuenta boton_Accion1"
  ) {
    $("#transferir_cuenta").removeClass();
    $("#transferir_cuenta").addClass("boton_Accion1 info_mesa_btn");
    vistaInfoTran = true;
    $("#informacionPorMesa").removeClass();
    $("#informacionPorMesa").addClass("info_mesa_E_btn boton_Accion1");
    vistaInfo = false;
  } else {
    $("#transferir_cuenta").removeClass();
    $("#transferir_cuenta").addClass("transferirCuenta boton_Accion1");
    vistaInfoTran = false;
  }
  fn_transferir_cuentas();
}

function fn_listausuarios() {
  $("#listado_cuentas").empty();
  var html = "";
  var estado = 0;
  send = { TransferenciaCuentas: 1 };
  send.opcion = 1;
  send.IDEstacion = $("#est_id").val();
  send.IDUsersPos = $("#txtUsuarioLogin").val(); //$('#txtUsr_Id').val();
  send.IDPeriodo = $("#idPeriodo").val();
  send.cdn_id = $("#txtCadena").val();
  $.ajax({
    async: false,
    type: "GET",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "config_UserMesas.php",
    data: send,
    success: function (datos) {
      $("#mesas").hide();
      $("#transferencia_cuenta").show();
      if (datos.str > 0) {
        $("#listado_cuentas").empty();
        for (i = 0; i < datos.str; i++) {
          html +=
            '<tr style="height:60px;"> <td>' +
            datos[i]["mesa_descripcion"] +
            "</td><td>" +
            datos[i]["usr_descripcion"] +
            "</td><td>" +
            "Transferir cuenta a" +
            "</td><td>" +
            "<select id=" +
            datos[i]["IDCabeceraOrdenPedido"] +
            ' class="form-control listarUsuario" style="height:40px;width:200px"></select>' +
            "</td></tr>";
        }
        $("#listado_cuentas").append(html);
      }
    },
  });
}

function fn_listausuariosMesero() {
  $("#listado_cuentas").empty();
  var html = "";
  var estado = 0;
  send = { TransferenciaCuentas: 1 };
  send.opcion = 1;
  send.IDEstacion = $("#est_id").val();
  send.IDUsersPos = $("#txtUsuarioLogin").val(); //$('#txtUsr_Id').val();
  send.IDPeriodo = $("#idPeriodo").val();
  send.cdn_id = $("#txtCadena").val();
  $.ajax({
    async: false,
    type: "GET",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "config_UserMesas.php",
    data: send,
    success: function (datos) {
      $("#mesas").hide();
      $("#transferencia_cuenta").show();
      if (datos.str > 0) {
        $("#listado_cuentas").empty();
        for (i = 0; i < datos.str; i++) {
          html +=
            '<tr style="height:60px;"> <td>' +
            datos[i]["mesa_descripcion"] +
            "</td><td>" +
            datos[i]["usr_descripcion"] +
            "</td><td>" +
            "Transferir cuenta a" +
            "</td><td>" +
            '<input type="password" id=' +
            datos[i]["IDCabeceraOrdenPedido"] +
            ' class="form-control listarPass" style="height:40px;width:200px"></input>' +
            "</td></tr>";
        }
        $("#listado_cuentas").append(html);
      }
    },
  });
}

function fn_valida_cajero() {
  fn_listausuarios();
  obtenerUsuarios();
  $(".listarUsuario").change(function () {
    //alert("Seleccionaste IDUsersPos: " + $(this).val() + " ID select :" + $(this).closest('select').attr('id') + " Option ID:" + $(this).children(":selected").attr("id"));
    var IDUsersPos = $(this).val();
    var IDEstacion = $(this).children(":selected").attr("id");
    var IDCabeceraOrdenPedido = $(this).closest("select").attr("id");
    fn_actualiza_cuentas(1, IDUsersPos, IDEstacion, IDCabeceraOrdenPedido);
  });
}

function fn_transferir_cuentas() {
  if (vistaInfoTran) {
    switch (perfil_Usuario.toUpperCase()) {
      case "CAJERO":
        fn_validarUsuarioAdministrador1();
        //                if(estado_divide){
        //                fn_listausuarios();
        //                obtenerUsuarios();
        //                $('.listarUsuario').change(function () {
        //                    //alert("Seleccionaste IDUsersPos: " + $(this).val() + " ID select :" + $(this).closest('select').attr('id') + " Option ID:" + $(this).children(":selected").attr("id"));
        //                    var IDUsersPos = $(this).val();
        //                    var IDEstacion = $(this).children(":selected").attr("id");
        //                    var IDCabeceraOrdenPedido = $(this).closest('select').attr('id');
        //                    fn_actualiza_cuentas(1, IDUsersPos, IDEstacion, IDCabeceraOrdenPedido);
        //
        //                });
        //                }
        break;
      case "ADMINISTRADOR LOCAL":
      case "ADMINISTRADOR":
        fn_validarUsuarioAdministrador1();
        break;
      case "MESERO":
        fn_listausuariosMesero();
        $(".listarPass").click(function () {
          var IDUsersPos = $("#" + $(this).attr("id")).val();
          var IDCabeceraOrdenPedido = $(this).attr("id");
          alert(
            "IDU" +
              IDUsersPos +
              " IDCabeceraOrdenPedido:" +
              IDCabeceraOrdenPedido
          );
          fn_actualiza_cuentas(2, IDUsersPos, "", IDCabeceraOrdenPedido);
        });
        break;
    }
  } else {
    $("#mesas").show();
    $("#transferencia_cuenta").hide();
  }
}

function fn_actualiza_cuentas(
  Opcion,
  IDUsersPos,
  IDEstacion,
  IDCabeceraOrdenPedido
) {
  send = { ActualizaTransferenciaCuentas: 1 };
  send.opcion = Opcion;
  send.IDUsersPos = IDUsersPos; //$('#txtUsr_Id').val();
  send.IDEstacion = IDEstacion;
  send.IDCabeceraOrdenPedido = IDCabeceraOrdenPedido;
  $.ajax({
    async: false,
    type: "GET",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "config_UserMesas.php",
    data: send,
    success: function (datos) {
      if (datos.str > 0) {
        alertify.log(datos[0]["mensaje"]);
      } else {
        alertify.error("No se actualizaron los registros");
      }
      //fn_transferir_cuentas();
      fn_valida_cajero();
    },
  });
}

function fn_transaction_view() {
  if ($("#informacionTotalMesas").attr("class") === "view_btn boton_Accion1") {
    $("#cnt_pedidos").hide();
    $("#btn_pedidos_app").show();
    $("#btn_pedidos_entregados").show();

    $("#btn_imprimir_error").hide();
    $("#btn_pedidos_error").show();
    $("#cnt_pedidos_error").hide();

    $("#btn_facturar").hide();
    $("#btn_ver").hide();
    $("#btn_cancelar").hide();
    $("#cuado").show();
    $("#informacionTotalMesas").removeClass();
    $("#informacionTotalMesas").addClass("boton_Accion1 info_mesa_btn");
    vistaInfoTran = true;
    $("#informacionPorMesa").removeClass();
    $("#informacionPorMesa").addClass("info_mesa_E_btn boton_Accion1");
    vistaInfo = false;
    mostrarOcultarBotonesPolitica();

  } else {
    $("#informacionTotalMesas").removeClass();
    $("#informacionTotalMesas").addClass("view_btn boton_Accion1");
    vistaInfoTran = false;
  }
  fn_opcion_transaccion();
}

//visualizar trasnsacciones
function fn_opcion_transaccion() {
  if (vistaInfoTran) {
    $("#mesas").hide();
    $("#transferencia_cuenta").hide();
    if (cod_area === 0 || cod_piso === 0) {
      alert("Sin datos");
    } else {
      $("#detalle_plu").show();
      var html = "";
      send = {
        consultaMesaFS: 1,
        cod_piso: cod_piso,
        cod_area: cod_area,
      };
      $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../corteCaja/config_corteCaja.php",
        data: send,
        success: function (datos) {
          if (datos.str > 0) {
            $("#listado_mesas").empty();
            for (i = 0; i < datos.str; i++) {
              espacio = "&nbsp;";
              guion2 = "-";
              total = " Total:";
              servicio = $("#hide_tipo_servicio").val();
              if (servicio == 1) {
                if (datos[i]["total"] == 0) {
                  html =
                    "<td>" +
                    datos[i]["nombre_estacion"] +
                    "</td><td>" +
                    datos[i]["total"].toFixed(2) +
                    "</td>";
                } else {
                  descripcion = datos[i]["std_descripcion"];
                  html =
                    '<tr style="height:60px;" onclick=fn_modalMesas("' +
                    datos[i]["odp_id"] +
                    '","' +
                    datos[i]["mesa_descripcion"] +
                    '","' +
                    datos[i]["est_id"] +
                    '","' +
                    datos[i]["IDMesa"] +
                    '","' +
                    datos[i]["std_descripcion"].toString() +
                    '")><td>' +
                    datos[i]["mesa_descripcion"] +
                    "</td><td>" +
                    datos[i]["nombre_estacion"] +
                    "</td><td>" +
                    datos[i]["total"].toFixed(2) +
                    "</td><td>" +
                    datos[i]["odp_fecha_creacion"] +
                    "</td></tr>";
                }
              } else if (servicio == 2) {
                if (datos[i]["total"] == 0) {
                  html =
                    '<tr style="height:60px;" onclick=fn_modalMesas("' +
                    datos[i]["odp_id"] +
                    '","' +
                    datos[i]["mesa_descripcion"] +
                    '","' +
                    datos[i]["est_id"] +
                    '","' +
                    datos[i]["IDMesa"] +
                    '","' +
                    encodeURIComponent(datos[i]["std_descripcion"]) +
                    '")><td>' +
                    datos[i]["mesa_descripcion"] +
                    "</td><td>" +
                    datos[i]["nombre_estacion"] +
                    "</td><td>" +
                    datos[i]["total"].toFixed(2) +
                    "</td><td>" +
                    datos[i]["odp_fecha_creacion"] +
                    "</td></tr>";
                } else {
                  html =
                    '<tr style="height:60px;" onclick=fn_modalMesas("' +
                    datos[i]["odp_id"] +
                    '","' +
                    datos[i]["mesa_descripcion"] +
                    '","' +
                    datos[i]["est_id"] +
                    '","' +
                    datos[i]["IDMesa"] +
                    '","' +
                    encodeURIComponent(datos[i]["std_descripcion"]) +
                    '")><td>' +
                    datos[i]["mesa_descripcion"] +
                    "</td><td>" +
                    datos[i]["nombre_estacion"] +
                    "</td><td>" +
                    datos[i]["total"].toFixed(2) +
                    "</td><td>" +
                    datos[i]["odp_fecha_creacion"] +
                    "</td></tr>";
                }
              }

              $("#listado_mesas").append(html);
            }
          }
          $("#detalle_plu").shortscroll();
        },
      });
    }
  } else {
    $("#mesas").show();
    $("#detalle_plu").hide();
  }
}
//carga mesas y retoma valores
function fn_modalMesas(
  codigo_orden,
  codigo_mesa,
  estacionId,
  mesa_id,
  std_descripcion
) {
  $("#hid_codigoorden").val(codigo_orden);
  if ($("#est_id").val() == estacionId) {
    $("#btn_tomar_mesa").show(); //attr('visible', true);
  } else {
    $("#btn_tomar_mesa").hide(); //attr('visible', false);
  }
  var html =
    "<tr class='active'><th style='text-align:center'>Cantidad</th><th style='text-align:center'>Descripci&oacute;n</th><th style='text-align:center;'>Precio</th></tr>";
  send = { consultadetalleMesa: 1 };
  send.codigoOrden = codigo_orden;
  $.ajax({
    async: false,
    type: "GET",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../corteCaja/config_corteCaja.php",
    data: send,
    success: function (datos) {
      if (datos.str > 0) {
        // $("#detalleMesa").empty();
        for (i = 0; i < datos.str; i++) {
          if (datos[i]["dop_cantidad"] == 0 || datos[i]["subtotal"] == 0) {
          } else {
            html += "<tr><td>" + datos[i]["dop_cantidad"] + "</td>";
            html += "<td>" + datos[i]["plu_descripcion"] + "</td>";
            html += "<td>" + datos[i]["subtotal"] + "</td></tr>";
          }
          $("#detalleMesa").html(html);
        }

        send = { consultatotalesMesa: 1 };
        send.codigoOrden = codigo_orden;
        $.ajax({
          async: false,
          type: "GET",
          dataType: "json",
          contentType: "application/x-www-form-urlencoded",
          url: "../corteCaja/config_corteCaja.php",
          data: send,
          success: function (datos) {
            if (datos.str > 0) {
              $("#txt_txt_totalNeto").val("$" + datos.precioNeto.toFixed(2));
              $("#txt_iva").val("$" + datos.IVA.toFixed(2));
              $("#txt_totalDetalle").val("$" + datos.total.toFixed(2));
              $("#txt_codigoMesa").val(codigo_orden);
              $("#txt_mesa_descripcion").val(codigo_mesa);
              $("#txt_observacion").val(datos.odp_observacion);
            }
          },
        });
      }
    },
  });
  $("#content").dialog({
    title: "Cuentas Abiertas",
    width: 950,
    autoOpen: false,
    resizable: false,
    show: {},
    hide: {},
    modal: true,
    position: "center top",
    closeOnEscape: false,
  });
  $("#content").dialog("open");
  $("#btn_okDetalle").click(function () {
    $("#content").dialog("close");
  });

  /*
	} else {
        alertify.error("<b>Atenci&oacute;n..!!</b>  La mesa a la que intenta acceder no fue atendida en esta estaci&oacute;n.");
    }
	*/

  $("#btn_tomar_mesa").attr(
    "onclick",
    'fn_irOrdenPedido("' +
      mesa_id +
      '",1,"' +
      decodeURIComponent(std_descripcion) +
      '")'
  );
}

function fn_mesas_recargar(codMesa) {
  send = { ActualizaMesa: 1 };
  send.codMesa = codMesa;
  $.ajax({
    async: false,
    type: "GET",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../corteCaja/config_corteCaja.php",
    data: send,
    success: function (datos) {
      if (datos.str > 0) {
      } else {
        setTimeout("fn_mesas_recargar()", 1000);
      }
    },
  });
}

function GetURLParameter(sParam) {
  var sPageURL = window.location.search.substring(1);
  var sURLVariables = sPageURL.split("&");
  for (var i = 0; i < sURLVariables.length; i++) {
    var sParameterName = sURLVariables[i].split("=");
    if (sParameterName[0] == sParam) {
      return sParameterName[1];
    }
  }
}

function fn_modalPersonas() {
  var resultado = "";
  send = { verificaSeleccionNumeroMesa: 1 };
  send.rst_id = $("#txtRest").val();
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "config_UserMesas.php",
    data: send,
    success: function (datos) {
      if (datos.str > 0) {
        resultado = datos[0]["resultado"];
      } else {
        resultado = "No";
      }
    },
  });
  return resultado;
}

function fn_cerrarDialogoAnulacion() {
  $("#usr_clave").val("");
  $("#motivoObservacion").val("");
  $("#anulacionesMotivo .anulacionesSubmit").empty();
  //$("#numPad").hide();
  $("#keyboard").hide();
  $("#anularOrden").prop("disabled", false);
  $("#anulacionesContenedor").dialog("close");
  $("#transferir_cuenta").removeClass();
  $("#transferir_cuenta").addClass("transferirCuenta boton_Accion1");
  vistaInfoTran = false;
}

function fn_validarUsuarioAdministrador1() {
  $("#anulacionesContenedor").show();
  $("#anulacionesContenedor").dialog({
    modal: true,
    width: 500,
    heigth: 500,
    resize: false,
    opacity: 0,
    show: "none",
    hide: "none",
    duration: 500,
    open: function (event, ui) {
      $(".ui-dialog-titlebar").hide();
      fn_numerico("#usr_clave");
      $("#usr_clave").val("");
      $("#usr_clave").attr(
        "onchange",
        "fn_validarCredencialesAdministrador1()"
      );
    },
  });
}

function fn_validarCredencialesAdministrador1() {
  var usr_clave = $("#usr_clave").val();
  if (usr_clave.indexOf("%") >= 0) {
    var old_usr_clave = usr_clave.split("?;")[0];
    var new_usr_clave = old_usr_clave.replace(new RegExp("%", "g"), "");
    var usr_tarjeta = new_usr_clave;
    usr_clave = "noclave";
  } else {
    var usr_tarjeta = 0;
  }
  if (usr_clave != "") {
    send = { validarUsuario: 1 };
    send.usr_clave = usr_clave;
    send.usr_tarjeta = usr_tarjeta;
    $.getJSON("config_ordenPedido.php", send, function (datos) {
      if (datos.str > 0) {
        $("#anulacionesContenedor").dialog("close");
        $("#usr_clave").val("");
        //estado_divide = true;
        fn_valida_cajero();
      } else {
        fn_numerico("#usr_clave");
        alertify.confirm(
          "La clave ingresada no tiene permisos de Administrador.",
          function (e) {
            if (e) {
              alertify.set({ buttonFocus: "none" });
              $("#usr_clave").focus();
            }
          }
        );
        $("#usr_clave").val("");
      }
    });
  } else {
    fn_nubtnVirtualOKpqmerico("#usr_clave");
    alertify.confirm("Ingrese clave de Administrador", function (e) {
      if (e) {
        alertify.set({ buttonFocus: "none" });
        $("#usr_clave").focus();
      }
    });
    $("#usr_clave").val("");
  }
}

function fn_alfaNumericov2(e, mesa_id, std_id) {
  if (!$(e.target).closest("#keyboard").length) {
    toggleDiv("keyboard");
  }

  $("#keyboard").empty();
  var posicion = $(e).position();
  var leftPos = 155;
  var topPos = 300;
  leftPos: posicion.left;
  topPos: posicion.top;
  var num0 =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ",0)'>0</button>";
  var num1 =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ",1)'>1</button>";
  var num2 =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ",2)'>2</button>";
  var num3 =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ",3)'>3</button>";
  var num4 =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ",4)'>4</button>";
  var num5 =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ",5)'>5</button>";
  var num6 =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ",6)'>6</button>";
  var num7 =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ",7)'>7</button>";
  var num8 =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ",8)'>8</button>";
  var num9 =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ",9)'>9</button>";
  var cadQ =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"Q")\'>Q</button>';
  var cadW =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"W")\'>W</button>';
  var cadE =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"E")\'>E</button>';
  var cadR =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"R")\'>R</button>';
  var cadT =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"T")\'>T</button>';
  var cadY =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"Y")\'>Y</button>';
  var cadU =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"U")\'>U</button>';
  var cadI =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"I")\'>I</button>';
  var cadO =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"O")\'>O</button>';
  var cadP =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"P")\'>P</button>';
  var cadA =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"A")\'>A</button>';
  var cadS =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"S")\'>S</button>';
  var cadD =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"D")\'>D</button>';
  var cadF =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"F")\'>F</button>';
  var cadG =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"G")\'>G</button>';
  var cadH =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"H")\'>H</button>';
  var cadJ =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"J")\'>J</button>';
  var cadK =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"K")\'>K</button>';
  var cadL =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"L")\'>L</button>';
  var cadZ =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"Z")\'>Z</button>';
  var cadX =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"X")\'>X</button>';
  var cadC =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"C")\'>C</button>';
  var cadV =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"V")\'>V</button>';
  var cadB =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"B")\'>B</button>';
  var cadN =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"N")\'>N</button>';
  var cadM =
    "<button class='btnVirtualUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"M")\'>M</button>';
  var arroba =
    "<button class='btnVirtualBorrarUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"@")\'>@</button>';
  var guion =
    "<button class='btnVirtualBorrarUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"-")\'>-</button>';
  var barraBaja =
    "<button class='btnVirtualBorrarUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"_")\'>_</button>';
  var numeral =
    "<button class='btnVirtualBorrarUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',"#")\'>#</button>';
  var espacio =
    "<button class='btnEspaciadoraUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    '," ")\'>Espacio</button>';
  var coma =
    "<button class='btnVirtualBorrarUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',",")\'>,</button>';
  var punto =
    "<button class='btnVirtualBorrarUM' onclick='fn_agregarCaracter(" +
    $(e).attr("id") +
    ',".")\'>.</button>';
  var borrarCaracter =
    "<button class='btnVirtualBorrarUM' onclick='fn_eliminarNumero(" +
    $(e).attr("id") +
    ")'>&larr;</button>";
  var borrarTodo =
    "<button class='btnVirtualBorrarUM' onclick='fn_eliminarTodo(" +
    $(e).attr("id") +
    ")'>&lArr;</button>";
  var btnOk =
    "<button id='btn_ok_teclado' class='btnVirtualOKUM' id='btn_ok_pad' onclick='fn_okTransferirMesas(\"" +
    mesa_id +
    '","' +
    std_id +
    "\")' >OK</button>";
  var btnCancelar =
    "<button id='btn_cancelar_teclado' class='btnVirtualCancelarUM' onclick='fn_modalTransferirMesas()'>Cancelar</button>";
  $("#keyboard").css({
    display: "block",
    position: "absolute",
    left: "1px",
  });
  $("#keyboard").append(
    num1 +
      num2 +
      num3 +
      num4 +
      num5 +
      num6 +
      num7 +
      num8 +
      num9 +
      num0 +
      borrarCaracter +
      "<br/>" +
      cadQ +
      cadW +
      cadE +
      cadR +
      cadT +
      cadY +
      cadU +
      cadI +
      cadO +
      cadP +
      borrarTodo +
      "<br/>" +
      arroba +
      cadA +
      cadS +
      cadD +
      cadF +
      cadG +
      cadH +
      cadJ +
      cadK +
      cadL +
      guion +
      "<br/>" +
      numeral +
      barraBaja +
      cadZ +
      cadX +
      cadC +
      cadV +
      cadB +
      cadN +
      cadM +
      coma +
      punto +
      "<br/>" +
      btnCancelar +
      espacio +
      btnOk
  );
}

/*
 * Integracion App y Web
 */
var timerSemaforo;
var habilitarContenedorPedidos = function () {
  runReintentosOnUpdate=true;

  if ( $("#config_servicio_pickup").val() == 1) {
    $("#btn_pedidos_pickup_app").show();
  } else {
    $("#btn_pedidos_pickup_app").hide();
  }

  $("#btn_notificar").hide();
  $("#pnl_pickup").hide();

  $("#sltd_codigo_app").html('');
  $("#sltd_estado_app").html('');

  $("#cuado").hide();
  $("#cntMesas").show();
  $("#cnt_pedidos").show();
  $("#btn_pedidos_app").hide();
  $("#btn_pedidos_entregados").show();

  $("#btn_imprimir_error").hide();
  $("#btn_pedidos_error").show();
  $("#cnt_pedidos_error").hide();

  $("#btn_facturar").hide();
  $("#btn_ver").hide();
  $("#btn_cancelar").hide();
  $("#btn_anular").hide();

  $("#listaPedido").show();
  $("#div_busqueda").show();
  $("#detalle_pedido").hide();
  $("#listado_pedido_app").html("");

  mostrarOcultarBotonesPedidosApp(1);

  cargarPedidosRecibidos();
  $("#cboEstadoPedido").val("PRINCIPAL");
  mostrarOcultarBotonesPolitica();
  cargarListaMedios();

  // Iniciar Timer
  if ( !timerSemaforo ) {
    timerSemaforo = setInterval(update, 60000);
  }

};

var cargarPedidosRecibidos = function () {
  var aplicaActualizacion = getActualizacionPendiente();
  if (aplicaActualizacion){
      buildAlertActualizacion();
      return;
  }else{
    cargarDeliveryPedidosPendientes();
    var html = "";
    if($("#listado_pedido_app").find("li.focus").length>0){
      ultimoElementoSeleccionado = $("#listado_pedido_app").find("li.focus").attr("id");
    }else{
      ultimoElementoSeleccionado=""; 
    }
    $("#listado_pedido_app").html(html);
    send = { metodo: "cargarPedidosApp" };
    send.estado = "RECIBIDO";
    (send.estadoBusqueda = "PRINCIPAL"), (send.parametroBusqueda = "");
    $.ajax({
      async: false,
      type: "POST",
      dataType: "json",
      contentType: "application/x-www-form-urlencoded",
      url: "../ordenpedido/config_app.php",
      data: send,
      success: function (datos) {
        if (datos.registros > 0) {
          cargarBurbujasBotonesApp(datos.cantidad[0], datos.todos[0]);
          cargarLista(datos.pedidos[0]);
        } else {
          $("#listado_pedido_app").append(
            '<li class="datos_app"><div>No existen pedidos.</div></li>'
          );
        }
        //obtenerCantidadEstadosPedidosApp();
        obtenerCantidadEstadosPedidosFiltrados();
      },
    });
  }
  
};
// comienza los motorizados
var cargarMotorizadosActivos = function () {
  var codigo = $("#listado_pedido_app").find("li.focus").attr("codigo_fac");
  var medio  =  $("#listado_pedido_app").find("li.focus").attr("medio");
  var html = "";
  var contador = 0;
  //$("#listado_pedido_app").html( html );
  $("#asignarMotorizadosLst").html(html);
  $("#asignarHeader").html(html);
  $("#asignarFooter").html(html);
  send = { metodo: "cargarMotorizados" };
  send.idPeriodo = $("#idPeriodo").val();
  send.idCadena = $("#txtCadena").val();
  send.idRestaurante = $("#txtRest").val();
  send.medio = medio;
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function (datos) {

      if (datos.registros > 0) {
        var header = "<h3>Asignar Pedido #"+codigo+"</h3>";
        $("#asignarHeader").html(header);
        for (var i = 0; i < datos.registros; i++) {
          if (
            datos[i]["estado"] === "Asignado" &&
            datos[i]["total"] < datos[i]["maximo_ordenes"]
          ) {
            html =
              '<li class="datos_motorizado" idMotorizado="' +
              datos[i]["idMotorizado"] +
              '" motorizado="' +
              datos[i]["motorizado"] +
              '"  onclick="fn_asignarMotorizado(this)"><div class="asigna_motorizado"><b>' +
              datos[i]["motorizado"] +
              '</b><div class="asigna_estado"> ' +
              datos[i]["estado"] +
              '</div></div><div class="datos_empresa_motorolo">'+datos[i]["tipo"]+'</div> <div class="asigna_total"> ' +
              datos[i]["total"] +
              "/" +
              datos[i]["maximo_ordenes"] +
              "</div></li>";
            $("#asignarMotorizadosLst").append(html);
            contador++;
          }
        }
        var str =
        "<button class=\"btn_cerrar\" onClick=\"$('#modalAsignarMotorizado').modal('toggle');\">Cerrar</button>";
        $("#asignarFooter").append(str);
        if (contador === 0) {
          $("#asignarMotorizadosLst").html('<li class="datos_motorizado"><div>No existen motorizados disponibles.</div></li>');
        }
      } else {
        $("#asignarMotorizadosLst").html('<li class="datos_motorizado"><div>No existen motorizados asignados.</div></li>');
      }
    },
  });
};

var confirmarOrden = function () {
  $('#modal_cargando_pedido').show();
  $("#texto_cargando_pedido").text("Generando confirmación...");

  var medio = $("#listado_pedido_app").find("li.focus").attr("medio");
  let html = "";
  let codigo = $("#listado_pedido_app").find("li.focus").attr("id");
  var medio = $("#" + codigo).attr("medio");
  let proveedorTracking = $("#proveedor_tracking").val();
  let cambio_estado = $("#" + codigo).attr("cambio_estado");


  alertify.confirm("¿Desea Confirmar este pedido?", function (e) {
    
    if (e) {
      send = { metodo: "confirmarOrden" };
      send.idUserPos = $("#txtUsuarioLogin").val();
      send.codigo_app = $("#listado_pedido_app").find("li.focus").attr("id");
      send.idRestaurante = $("#txtRest").val();
      $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../ordenpedido/config_app.php",
        data: send,
        success: function (datos) {
          if (datos.estado > 0) {
            alertify.success(datos.mensaje);
            cargarPedidosRecibidos();
            if (cambio_estado == "SI") {
              //if (proveedorTracking && proveedorTracking === "TRADE") {
                //KFC: RECIBIDO = TRADE: Pedido Recibido
                cambioEstadoTrade(codigo, "Pedido Recibido", medio);
                console.log(medio);
              //}
            }
            $("#btn_transferir").hide();
            $("#btn_facturar").show();
            $("#btn_ver").show();
            $("#btn_cancelar").hide();
            $("#listaPedido").show();
            $("#div_busqueda").show();
            $("#detalle_pedido").hide();
            $('#btn_confirmar').hide();
            mostrarOcultarBotonesPolitica();
            setTimeout(function(){  $('#'+this.filaSeleccionada).addClass("focus"); }, 600);
          } else {
            setTimeout(() => {
              if(datos.mensaje)
              alertify.error("Error: " + datos.mensaje);
              else
              alertify.error("EL tiempo de envio de cambio de estado a exedido." );

            }, 200);
          }
          $('#modal_cargando_pedido').hide();
        },error: function(e){
          $('#modal_cargando_pedido').hide();
        }
      });
    }else {
      $('#modal_cargando_pedido').hide();
    }
  });
};

var cargarListaTransaccionesAsignadas = function (idMotorizado, motorizado) {
  var html = "";
  var medio = $("#listado_pedido_app").find("li.focus").attr("medio");
  //$("#listado_pedido_app").html( html );
  $("#asignarMotorizadosLst").html(html);
  $("#asignarHeader").html(html);
  $("#asignarFooter").html(html);
  var proveedorTracking = $("#proveedor_tracking").val();
  console.log("lista transaccion asignadas");

  send = { metodo: "listaTransaccionesAsignadas" };
  send.idCadena = $("#txtCadena").val();
  send.idRestaurante = $("#txtRest").val();
  send.idMotorizado = idMotorizado;
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function (datos) {
      console.log("respuesta en camino");
      console.log(datos.registros);

      if (datos.registros > 0) {
        var estado = datos[0]["estado"];
        var transacciones = [];
        for (var i = 0; i < datos.registros; i++) {

          var cambio_estado_item = $("#" + datos[i]["codigo_app"]).attr("cambio_estado");

          let transaccion={
            codigoApp: datos[i]["codigo_app"], 
            cambio_estado: cambio_estado_item, 
            medio: datos[i]["medio"]
          }

          transacciones.push(transaccion);
          html =
            '<li class="datos_motorizado" ><div class="lst_codigo"><b>' +
            datos[i]["codigo"] +
            '</b></div><div class="lst_cliente">' +
            datos[i]["cliente"] +
            '</div><div class="lst_forma_pago"> ' +
            datos[i]["forma_pago"] +
            '</div><div class="lst_total">$' +
            datos[i]["total"] +
            "</div></li>";
          $("#asignarMotorizadosLst").append(html);
        }
        var header = "<h3>" + estado + " - " + motorizado + "</h3>";
        $("#asignarHeader").append(header);
        $("#modalAsignarMotorizado").modal("toggle");

        //agrego los botones
        var textoBoton = "";
        var str =
          "<button class=\"btn_cerrar\"  onClick=\"$('#modalAsignarMotorizado').modal('toggle');\">Cerrar</button>";
        if (estado === "ASIGNADO") {
          textoBoton = "En Camino";
        }
        if (estado === "EN CAMINO") {
          textoBoton = "Confirmar Entrega";
        }
        console.log(transacciones);
        let arrtransacciones = JSON.stringify(transacciones).split('"').join("&quot;")
        console.log(typeof(arrtransacciones));
        console.log(arrtransacciones);
        var idPeriodo = $("#idPeriodo").val();
        str +=
          '<button class="btn_cambioEstado" onClick="fn_cambiarEstado(\'' +
          idPeriodo +
          "','" +
          idMotorizado +
          "','" +
          estado +
          "','" +
          medio +
          "','" +
         motorizado +
          "','" +
          `${arrtransacciones}` +
          "');\">" +
          textoBoton +
          "</button>";

        $("#asignarFooter").append(str);
      } else {
        $("#asignarMotorizadosLst").append(
          '<li class="datos_app"><div>No hay lista.</div></li>'
        );
      }

/*      if (estado == "ASIGNADO") {
        if (proveedorTracking &&proveedorTracking === "TRADE") {
          //console.log("EnCamino: ", estado);
          //KFC: ENCAMINO = TRADE: En Camino
          console.log(transacciones);
          cambioEstadoTradeVarias(transacciones, "En Camino", medio);
        }
      } else {
        if (proveedorTracking && proveedorTracking === "TRADE") {
          //console.log("Entregado: ", estado);
          //KFC: ENTREGADO = TRADE: Entregado
          console.log(transacciones);
          cambioEstadoTradeVarias(transacciones, "Entregado", medio);
        }
      }*/

    },
  });
};
//

var seleccionarPedido = function (id) {
  
  $("#listado_pedido_app li").removeClass("focus");
  $(id).addClass("focus");
  this.filaSeleccionada = $("#listado_pedido_app").find("li.focus").attr("id");
  // Configuración Cambio Estados Automatico por Medio
  var automatico = $("#" + filaSeleccionada).attr("automatico");
  // console.log("AUTOMATICO: ", automatico);
  $("#cambio_estados_automatico").val( automatico );
  verificarBotones();
};

var accion = function () {

  $('#div_busqueda').hide();
  var codigo = $("#listado_pedido_app").find("li.focus").attr("id");
  if (!codigo) {
    alertify.error("Seleccione una transaccion");
    return;
  }
  $("#listaPedido").hide();
  $("#div_busqueda").hide();
  $("#detalle_pedido").show();
  $("#btn_ver").hide();
  $("#btn_cancelar").show();
  var cliente = $("#" + codigo).attr("cliente");
  var documento = $("#" + codigo).attr("documento");
  var telefono = $("#" + codigo).attr("telefono");
  var observacion = $("#" + codigo).attr("observacion");
  var direccion = $("#" + codigo).attr("direccion");
  var fecha = $("#" + codigo).attr("fecha");
  var medio = $("#" + codigo).attr("medio");
  var adicional = $("#" + codigo).attr("adicional");
  var formapago = $("#" + codigo).attr("formapago");
  var codigo_fac = $("#" + codigo).attr("codigo_fac");
  var estado = $("#" + codigo).attr("estado");
  var fecha_estado = $("#" + codigo).attr("fecha_estado");
  var user_estado = $("#" + codigo).attr("user_estado");
  var mediopago = $("#" + codigo).attr("mediopago") == "null" ? "" :$("#" + codigo).attr("mediopago");
  
  var notificar_medio = $("#" + codigo).attr("notificar_medio");
  var notificar_listo = $("#" + codigo).attr("notificar_listo");


  var motivo_anulacion = $("#" + codigo).attr("motivo_anulacion")== "null" ? "" :$("#" + codigo).attr("motivo_anulacion");

  if($("#" + codigo).attr("motivo_anulacion")){
    //console.log('si entra');
    $("#motivo_anulacion_contenedor").show();
  }else{
    $("#motivo_anulacion_contenedor").hide();
  }

  var observacion_factura = $("#" + codigo).attr("observacion_factura");
  var motorizado = $("#" + codigo).attr("motorizado");
  var motorizado_telefono = $("#" + codigo).attr("motorizado_telefono") == "null" ? "" : $("#" + codigo).attr("motorizado_telefono");
  $("#codigo_app").html(codigo_fac);
  $("#cliente").html(cliente);
  $("#documento").html(documento);
  $("#observacion td").html(observacion);
  $("#direccion td").html(direccion);
  $("#telefono td").html(telefono);
  $("#formapago").html(formapago);
  $("#fecha").html(fecha);
  $("#medio").html(medio);
  $("#adicional td").html(adicional);
  $("#estado").html(estado);
  $("#fecha_estado").html(fecha_estado);
  $("#user_estado").html(user_estado);
  $("#mediopago").html(mediopago);
  $("#motivo_anulacion").html(motivo_anulacion);
  $("#observacion_factura").html(observacion_factura);
  $("#motorizado_nombres").html(motorizado);
  $("#motorizado_telefono").html(motorizado_telefono);

  $("#notificar_medio").html(notificar_medio);
  $("#notificar_listo").html(notificar_listo);

  pintar_datos_transferencia($("#listado_pedido_app").find("li.focus"));

  cargarDetallePedido(codigo, medio);
  mostrarOcultarBotonesPolitica();

};

function plugThemGet() {
    var send;
    var resultadoObject = new datosPlugThemPost();

    if (Object.values(resultadoObject).length > 0) {

        var BrandId = resultadoObject["BrandId"];
        var idCajero = resultadoObject["idCajero"];
        var EmpId = resultadoObject["EmpId"];
        var EmpName = resultadoObject["EmpName"];
        var SiteId = resultadoObject["SiteId"];
        var SiteName = resultadoObject["SiteName"];
        var ShiftManagerId = resultadoObject["ShiftManagerId"];
        var ShiftManagerName = resultadoObject["ShiftManagerName"];
        var Categories = "";
        var CustomerDoc = $("#txtClienteCedula").val();
        var CustomerName = $("#txtClienteNombre").val();
        var CustomerEmail = $("#txtClienteEmail").val();
        var CustomerMobile = $("#txtClienteTelefono").val();
        var EffortValue = "1";
        var EffortReason = "";
        var EffortComment = "";
        var InvRange = "";
        var transaccion = $("#txtNumFactura").val();

        send = {};
        send.metodo = "plugThemGet";
        send.BrandId = BrandId;
        send.EmpId = EmpId;
        send.EmpName = EmpName;
        send.SiteId = SiteId;
        send.SiteName = SiteName;
        send.ShiftManagerId = ShiftManagerId;
        send.ShiftManagerName = ShiftManagerName;
        send.Categories = Categories;
        send.CustomerDoc = CustomerDoc;
        send.CustomerName = CustomerName;
        send.CustomerEmail = CustomerEmail;
        send.CustomerMobile = CustomerMobile;
        send.EffortValue = EffortValue;
        send.EffortReason = EffortReason;
        send.EffortComment = EffortComment;
        send.InvRange = InvRange;
        send.transaccion = transaccion;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "../facturacion/clienteWS_plugThemCliente.php",
            data: send,
            timeout:7000,
            success: function (datos) {


                // fidelizacion: si es si el plan de datos  no imprimir aun (el campo fdznDocumento contendra una cedula cuando se trate de fdzn activa)

            }
        });
    } else {


        // fidelizacion: si es si el plan de datos  no imprimir aun (el campo fdznDocumento contendra una cedula cuando se trate de fdzn activa)

    }
}
function plugThemPost(token_type, acces_token, habilitarQR,idFactura,cedula,nombre,telefono,email) {
    var send;
    var resultadoObject = new datosPlugThemPost(idFactura);

    if (Object.values(resultadoObject).length > 0) {

        var BrandId = resultadoObject["BrandId"];
        var idCajero = resultadoObject["idCajero"];
        var EmpId = resultadoObject["EmpId"];
        var EmpName = resultadoObject["EmpName"];
        var SiteId = resultadoObject["SiteId"];
        var SiteName = resultadoObject["SiteName"];
        var ShiftManagerId = resultadoObject["ShiftManagerId"];
        var ShiftManagerName = resultadoObject["ShiftManagerName"];
        var Categories = "";
///PLUGTHEM
        var CustomerDoc =  cedula;
        var CustomerName = nombre;
        var CustomerEmail = email;
        var CustomerMobile = telefono;

        var EffortValue = "1";
        var EffortReason = "";
        var EffortComment = "";
        var InvRange = "";
        var qr_enable = habilitarQR;
        var transaccion = idFactura;

        var token_type = token_type;
        var acces_token = acces_token;

        send = {};
        send.metodo = "plugThemPost";
        send.BrandId = BrandId;
        send.idCajero = idCajero;
        send.EmpId = EmpId;
        send.EmpName = EmpName;
        send.SiteId = SiteId;
        send.SiteName = SiteName;
        send.ShiftManagerId = ShiftManagerId;
        send.ShiftManagerName = ShiftManagerName;
        send.Categories = Categories;
        send.CustomerDoc = CustomerDoc;
        send.CustomerName = CustomerName;
        send.CustomerEmail = CustomerEmail;
        send.CustomerMobile = CustomerMobile;
        send.EffortValue = EffortValue;
        send.EffortReason = EffortReason;
        send.EffortComment = EffortComment;
        send.InvRange = InvRange;
        send.qr_enable = qr_enable;
        send.transaccion = transaccion;
        send.token_type = token_type;
        send.acces_token = acces_token;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "../facturacion/clienteWS_plugThemCliente.php",
            data: send,
            success: function (datos) {
              fn_promociones_movistar(idFactura);
              if(datos.respuesta==1000){
                  alertify.success("Encuesta Plugthem Enviada!")
              }
              else
              {
                setTimeout(() => {
                  if(datos.mensaje)
                  alertify.error("Error: " + datos.mensaje);
                  else
                  alertify.error("EL tiempo de envio de Encuesta Plugthem a exedido." );
                }, 200);
              }



            }
        });
    } else {


        // fidelizacion: si es si el plan de datos  no imprimir aun (el campo fdznDocumento contendra una cedula cuando se trate de fdzn activa)

    }
}
function datosPlugThemPost(idFactura) {
    var send;
    var datosPlugThemPost = { "datosPlugThemPost": 1 };
    var transaccion = idFactura;
    var objectResultado = new Array();

    send = datosPlugThemPost;
    send.transaccion = transaccion;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../facturacion/config_plugThem.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                objectResultado["BrandId"] = datos[0]["BrandId"];
                objectResultado["idCajero"] = datos[0]["idCajero"];
                objectResultado["EmpId"] = datos[0]["EmpId"];
                objectResultado["EmpName"] = datos[0]["EmpName"];
                objectResultado["SiteId"] = datos[0]["SiteId"];
                objectResultado["SiteName"] = datos[0]["SiteName"];
                objectResultado["ShiftManagerId"] = datos[0]["ShiftManagerId"];
                objectResultado["ShiftManagerName"] = datos[0]["ShiftManagerName"];
            }
        }
    });

    return objectResultado;
}
function aplicaPlugThem() {
    var send;
    var aplicaPlugThem = { "aplicaPlugThem": 1 };
    var resultadoObject = new Array();

    send = aplicaPlugThem;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../facturacion/config_plugThem.php",
        data: send,
        success: function (datos) {
            if (datos.str > 0) {
                resultadoObject["aplicaCadena"] = datos[0]["aplicaCadena"];
                resultadoObject["aplicaRestaurante"] = datos[0]["aplicaRestaurante"];
            }
        }
    });

    return resultadoObject;
}
function validaLoginPlugThem(idFactura,cedula,nombre,telefono,email) {
    var resultadoObject = new aplicaPlugThem();
    if (Object.values(resultadoObject).length > 0) {
        var aplicaCadena = resultadoObject["aplicaCadena"];
        var aplicaRestaurante = resultadoObject["aplicaRestaurante"];
        //alert('aplicaCadena: ' + aplicaCadena + ', aplicaRestaurante: ' + aplicaRestaurante);
        if (aplicaCadena === 1 && aplicaRestaurante === 1) {
            tokenLogin(idFactura,cedula,nombre,telefono,email);
        } else {


            // fidelizacion: si es si el plan de datos  no imprimir aun (el campo fdznDocumento contendra una cedula cuando se trate de fdzn activa)

        }
    } else {


        // fidelizacion: si es si el plan de datos  no imprimir aun (el campo fdznDocumento contendra una cedula cuando se trate de fdzn activa)

    }
}
function tokenLogin(idFactura,cedula,nombre,telefono,email) {
    var send;
    var tokenLogin = { "tokenLogin": 1 };
    send = tokenLogin;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../facturacion/config_plugThem.php",
        data: send,
        success: function (datos) {
            if (datos[0]["token_type"] !== "0" && datos[0]["access_token"] !== "0") {
                valorConfiguracionPlugThem(datos[0]["token_type"], datos[0]["access_token"],idFactura,cedula,nombre,telefono,email);
            } else {


                // fidelizacion: si es si el plan de datos  no imprimir aun (el campo fdznDocumento contendra una cedula cuando se trate de fdzn activa)

            }
        }
    });
}
function valorConfiguracionPlugThem(token_type, access_token,idFactura,cedula,nombre,telefono,email) {
    var send;
    var valorConfiguracionPlugThem = { "valorConfiguracionPlugThem": 1 };
    send = valorConfiguracionPlugThem;
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../facturacion/config_plugThem.php",
        data: send,
        success: function (datos) {
            var valorFactura = $("#pagoGranTotal").val();
            var valorPlugThem = datos[0]["valor"];
            var aplica = datos[0]["aplica"];
            var contadorFacturas = datos[0]["contador"];

            // Valor total de factura
            if (aplica === 1) {
                if (parseFloat(valorFactura) > parseFloat(valorPlugThem)) {
                  if(cedula!='9999999999999')
                  {
                      plugThemPost(token_type, access_token, "",idFactura,cedula,nombre,telefono,email);
                  } else {
                    fn_promociones_movistar(idFactura);
                  }

                } else {
                    //plugThemGet();
                    if(cedula!='9999999999999')
                    {
                        plugThemPost(token_type, access_token, "1",idFactura,cedula,nombre,telefono,email);
                    } else {
                       fn_promociones_movistar(idFactura);
                    }
                }
            }
            // Número de facturas
            else if (aplica === 2) {
                if (parseInt(contadorFacturas) !== 0 && parseInt(valorPlugThem) !== 0) {
                    if (parseInt(contadorFacturas) === parseInt(valorPlugThem)) {
                        if(cedula!='9999999999999')
                        {
                          plugThemPost(token_type, access_token, "",idFactura,cedula,nombre,telefono,email);
                        } else {
                          fn_promociones_movistar(idFactura);
                        }
                    } else {
                        //plugThemGet();
                            if(cedula!='9999999999999')
                            {
                              plugThemPost(token_type, access_token, "1",idFactura,cedula,nombre,telefono,email);
                            } else {
                              fn_promociones_movistar(idFactura);
                            }
                    }
                } else {



                }
            } else {

                // fidelizacion: si es si el plan de datos  no imprimir aun (el campo fdznDocumento contendra una cedula cuando se trate de fdzn activa)

            }
        }
    });
}

async function changeStatusAgregador(codApp,medio){
  let pickeingAgregador = await checkPickeingAgregador(medio);
  let rider = await getRiderAgregador(medio);

  if (!rider.riderId) {
    return false;
  }

  if (pickeingAgregador == 'SI') {
    pickingAgregadorFlow(codApp,rider.riderId);
    return true;
  }
  changeOrderStatus(codApp,'ENTREGADO',rider.riderId);
}

function getRiderAgregador(medio){
  return new Promise((resolve, reject) => {
    send= {
      metodo:'getRiderAgregador',
      medio: medio
    }
    $.ajax({
      type: "POST",
      url: "../mantenimiento/adminCambioEstadosBringg/config_cambioEstadosBringg.php",
      data: send,
      dataType: "json",
      success: function (resp) {
        if (resp) {
          resolve(resp);
        } else {
          resolve(false);
        }
      },
    });
  });
}

async function pickingAgregadorFlow(codApp,riderId){
  var pickingFlaw = await getPickingFlaw(codApp);
  if(pickingFlaw == "1") {
    changeOrderStatus(codApp,'ENTREGADO',riderId);
  }
}

function getPickingFlaw(codApp){
  send = {metodo:'getPickingFlaw', codApp: codApp}
  return $.ajax({
    type: "POST",
    url: "../ordenpedido/config_app.php",
    data: send,
    dataType: "json"
  });
}

function checkPickeingAgregador(medio){
  send= {metodo:'getRestaurantConfig', collection: 'LISTA MEDIO '+ medio.toUpperCase(), parameter:'MOTORIZADOS QR',config:'variableV'}
  return $.ajax({
    type: "POST",
    url: "../ordenpedido/config_app.php",
    data: send,
    dataType: "json"
  });
}

function changeOrderStatus(codApp,status=null,riderID=null,monitor=true){
  send= { codApp: codApp,
    futureStatus:status,
    monitor:monitor,
    riderID:riderID}
  $.ajax({
    type: "POST",
    url: "../ordenpedido/changeStatus.php",
    data: send,
    dataType: "json",
    success: function(data){
      var changedOrders = JSON.stringify(data.changedStatus);
      alertify.success("order has been change to status : "+ changedOrders);
      cargarPedidosRecibidos();
    },
    error: function(jqXHR, exception) {
      alertify.error("error "+jqXHR.responseText);
      //cargarPedidosRecibidos();
    }
  });

}

var facturar = function () {
  $('#modal_cargando_pedido').show();
  $("#texto_cargando_pedido").text("Generando factura...");

  let codigo = $("#listado_pedido_app").find("li.focus").attr("id");
  let medio = $("#listado_pedido_app").find("li.focus").attr("medio");
  let proveedorTracking = $("#proveedor_tracking").val();
  let cambio_estados_automatico = $("#cambio_estados_automatico").val();
  let cambio_estado = $("#" + codigo).attr("cambio_estado");
  let medioProcesado = medio.trim().toUpperCase();
  localStorage.setItem('lasexecution', new Date().getTime());
  //asignamos el ultimo id seleccionado al boton
  $("#btn_reenviar_moto_duna").attr("idlist",codigo);

  console.log("Codigo:"+codigo);
  console.log('CAMBIO ESTADO AUTOMATICO !!!');
  console.log(cambio_estados_automatico);
  console.log('MEDIO !!! ');
  console.log(medio);
 
    /*if (num_accion == 1) {
    alertify.error("Espere un momento por favor...");
    return;
  }*/



  if ( !codigo ) {
    alertify.error("Seleccione una transaccion...");
    return;
  }
  alertify.confirm("¿Desea facturar este pedido? ", function (e) {
    if (e) {
      num_accion = 1;
      send = { metodo: "facturarPedidoApp" };
      send.codigo = codigo;
      $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../ordenpedido/config_app.php",
        data: send,
        success: function (datos) {
          num_accion = 0;
          if (datos.codigo == 200) {
            setTimeout(() => {
              alertify.success(datos.mensaje);
            }, 200);
            var idfactura =datos.idFactura;

            if (idfactura) {
              // Llamar a uberDirect y manejar el resultado dentro de la función success
              uberDirectCashStatus(codigo, medio, function(callback) {
                console.log("Uber Direct Cash Pass:", callback.status);
                if (callback.msj != "MEDIO INCORRECTO" && callback.msj != "FORMA DE PAGO INCORRECTA" && callback.msj != "UBER DIRECT EFECTIVO: OFF" && callback.msj != "ORDEN NO FACTURADA") {
                  if (callback.status) {
                    console.log("La Orden " + codigo + " será procesada en Uber Direct Cash");
                    medio = 'UBER';
                    nombreProveedorDelivery = 'UBER';
                    setTimeout(function() {
                      alertify.success(callback.msj + ". Orden " + codigo);
                    }, 3000);
                  } else {
                    console.log("La Orden " + codigo + " no será procesada en Uber Direct Cash");
                    setTimeout(function() {
                      alertify.error(callback.msj + ". Orden " + codigo);
                    }, 3000);
                  }
                }
              });
            }
            facturarTransaccion(datos.idFactura, datos.cedula, datos.nombre, datos.telefono, datos.email);
            ////////////////////JQ/////////////////////
            let apiImpresion = getConfiguracionesApiImpresion();            
            if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1){      
              fn_cargando(1);
              let result = new apiServicioImpresion('factura_orden',datos.idFactura);
              let imprime = result["imprime"];
              let mensaje = result["mensaje"];
              fn_APIkds('apiServicioKdsDomicilio',codigo);
              if (!imprime) {
                  alertify.success('Imprimiendo Factura...');
                }
              }else{
                fn_APIkds('apiServicioKdsDomicilio',codigo);
              }
            ///////////////////////////////////////
          } else if (datos.codigo == 99) {
            ////////////////////JQ/////////////////////
            let apiImpresion = getConfiguracionesApiImpresion();
            if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1){      
              fn_cargando(1);
              let result = new apiServicioImpresion('factura_orden',datos.idFactura);
              let imprime = result["imprime"];
              let mensaje = result["mensaje"];
              fn_APIkds('apiServicioKdsDomicilio',codigo);
              if (!imprime) {
                  alertify.success('Imprimiendo Factura...');
                }
              }
            ///////////////////////////////////////
            $("#mdl_diferenciaPrecios").modal("toggle");
            $("#mdl_diferenciaPreciosLabel").html('<h4 class="modal-title" id="mdl_diferenciaPreciosLabel"><b>ATENCIÓN!!</b></h4>');
            $("#mdl_diferenciaPreciosBody").html(datos.mensaje);
            $("#btn_diferenciaPrecios").html('<button type="button" class="btn btn-success" data-dismiss="modal" onclick="facturarTransaccion(\''+datos.idFactura+'\',\''+datos.cedula+'\',\''+datos.nombre+'\',\''+datos.telefono+'\',\''+datos.email+'\')">Continuar...</button>');            
          } else {
         
            $("#mdl_diferenciaPrecios").modal("toggle");
            $("#mdl_diferenciaPreciosLabel").html('<h4 class="modal-title" id="mdl_diferenciaPreciosLabel"><b>ERROR!!</b></h4>');
            $("#mdl_diferenciaPreciosBody").html(datos.mensaje);
            $("#btn_diferenciaPrecios").html('<button type="button" class="btn btn-danger" data-dismiss="modal">OK</button>');
            cargarPedidosRecibidos();
          }

          $('#modal_cargando_pedido').hide();
        },error: function(e){
          console.log(e)
          $('#modal_cargando_pedido').hide();
        }
      });
    }else{ 
      $('#modal_cargando_pedido').hide();
    }
  });
};
async function verificarEstadoDragonTail(codigo, medio){
  let dragonTailActive = await getDragonTailStatus();
  if (validarJSON(dragonTailActive)) {
    dragonTailActive = JSON.parse(dragonTailActive);
  }
  if (dragonTailActive?.registros > 0) {
    const { active } = dragonTailActive[0];
    if (active) {
      console.log('Servicio DragonTail Activo');
      createDragonTailOrder(codigo, medio);
      return active;
    }
  }
}

function enviarTransaccionQPM() {
  let timeoutLocalStorage = parseInt(localStorage.getItem("timeoutQPM") !== undefined
  && localStorage.getItem("timeoutQPM") !== null ? localStorage.getItem("timeoutQPM") : 0);
  let send={
      transaccion : 'transaccionVendida',
      parametros:{            
          idTransaccion : $("#listado_pedido_app").find("li.focus").attr("id"),/* Validacion por codigo APP */
          rst_id:$("#txtRest").val(),
          cdn_id:$("#txtCadena").val(),
          accion : '1',
          timeout: timeoutLocalStorage
      }
  };
  $.ajax({
      async: false,
      type: "POST",
      dataType: "json",
      contentType: "application/x-www-form-urlencoded",
      url: "../QPM/solicitudMaxPoint.php",
      data: send,
      success: function (datos) {
      console.log(datos);
      }
      , error: function (e) {
          console.log(e);
      }
  });
  
}
/**
 * Funcion envia las transacciones a qpm en el caso que el restaurante sea modo despacho y parcial.
 * @function enviarTransaccionQPMDespachoYParcial
 * @param {String} idFactura  cfac_id Id de la factura generada, en modo despacho y parcial.
 * @return {void}
 */
function enviarTransaccionQPMDespachoYParcial(idFactura) {
  let timeoutLocalStorage = parseInt(localStorage.getItem("timeoutQPM") !== undefined
  && localStorage.getItem("timeoutQPM") !== null ? localStorage.getItem("timeoutQPM") : 0);
  console.log("Factura: " +idFactura);
  let send={
      transaccion : 'transaccionVendida',
      parametros:{            
          idTransaccion : idFactura,/* Validacion por codigo APP */
          rst_id:$("#txtRest").val(),
          cdn_id:$("#txtCadena").val(),
          accion : '1',
          timeout: timeoutLocalStorage
      }
  };
  $.ajax({
      async: false,
      type: "POST",
      dataType: "json",
      contentType: "application/x-www-form-urlencoded",
      url: "../QPM/solicitudMaxPoint.php",
      data: send,
      success: function (datos) {
      console.log(datos);
      }
      , error: function (e) {
          console.log(e);
      }
  });
}


async function obtenerNombreProveedorDeliveryPorMedio(nombreMedio) {
  let parametros={
    metodo: "obtenerNombreProveedorDeliveryPorMedio",
    nombreMedio: nombreMedio
  }
  let datosNombreProveedorDeliveryPorMedio = await ajaxRequest('config_app.php',parametros,'POST');
  return datosNombreProveedorDeliveryPorMedio;
}

async function enviarPedidoDeliveryPorMedio(medio,factura,codigo) {
  fn_cargando(1);
  let datosNombreProveedorDelivery = await obtenerNombreProveedorDeliveryPorMedio(medio);
  
  if (validarJSON(datosNombreProveedorDelivery)) {
    datosNombreProveedorDelivery = JSON.parse(datosNombreProveedorDelivery);
  }

      if (datosNombreProveedorDelivery?.registros > 0) {
    const nombre_proveedor = datosNombreProveedorDelivery[0].nombre_proveedor;
    let nombreProveedorDelivery = nombre_proveedor?.toUpperCase().trim();

    let uberOff = false;
    if (nombreProveedorDelivery.includes('UBER')) {
      uberDirectCashStatus(codigo, medio, function(callback) {
        console.log("Uber Direct Cash Pass:", callback.status);
        if (callback.msj == "MEDIO INCORRECTO" || callback.msj == "FORMA DE PAGO INCORRECTA" || callback.msj == "UBER DIRECT EFECTIVO: OFF" || callback.msj == "ORDEN NO FACTURADA") {
          if (!nombreProveedorDelivery.includes(',')) {
            nombreProveedorDelivery = medio;
          } else {
            uberOff = true;
          }
        }
      });
    } 

    var orden=$("#" + codigo);
    var retira_efectivo = $(orden).attr("retira_efectivo");
    if(retira_efectivo!="SI"){
      nombreProveedorDelivery=retira_efectivo;
    }

    if (nombreProveedorDelivery && nombreProveedorDelivery.includes(',')) {
      let proveedor_envio = nombreProveedorDelivery.replace(" ", "").split(',');
      var html = "";

      $("#enviarMotorizadosLst").html(html);
      $("#enviarHeader").html(html);
      $("#enviarFooter").html(html);
      var header = "<h3>Enviar Pedido #"+codigo+" a: </h3>";
      $("#enviarHeader").html(header);
      for (var i = 0; i < proveedor_envio.length; i++) {
        if (uberOff && proveedor_envio[i] != 'UBER' || medio == 'UBER') {
          html = "<div style='margin-top:5px !important; display: flex; justify-content: center; width: 100%;'><button class='btn btn-primary' id='medio_"+proveedor_envio[i]+"' data-codigo='"+factura+"' data-medio='"+proveedor_envio[i]+"' onclick='enviarConsultaSelectedAgregador(\""+proveedor_envio[i]+"\",\""+factura+"\",\""+medio+"\",\""+codigo+"\")'>"+proveedor_envio[i]+"</button></div>";
          $("#enviarMotorizadosLst").append(html);
        }
      }
      var str = "<button class=\"btn_cerrar\" onClick=\"$('#modalEnviarMotorizado').modal('toggle');\">Cerrar</button>";
      $("#enviarFooter").append(str);
      setTimeout(() => {
        fn_cargando(0);
        $('#modalEnviarMotorizado').modal('show');
      }, 300);
    }else{
      verificarEstadoDragonTail(codigo, medio);
      mostrarOcultarBotonesPolitica();
	   switch (nombreProveedorDelivery) {
        case "BRINGG":
            fn_crearOrdenBringg(factura,medio, codigo);
          break;
        case "DUNA":
          let parametrosDuna = {
            idFactura:factura, 
            idApp:codigo,
            medio
          };
          crearOrdenDuna(parametrosDuna);
          break;
        case "NINGUNO":
          cambioEstadoAutomaticoSinProveedor({idFactura:factura,idApp:codigo,medio});			  
        break;
        default:
            enviarConsultaOtros(nombreProveedorDelivery,factura);
            //fn_crearOrdenBringg(factura,medio, codigo); //ASI ESTABA ANTES
          break;
      }
    }
  }
}

async function cambioEstadoAutomaticoSinProveedor({idFactura,idApp,medio}) {
  let cambioEstadosAutomatico = $("#cambio_estados_automatico").val();

  if(cambioEstadosAutomatico == 1 || cambioEstadosAutomatico === 'SI' ) {

    cambioEstadoTrade( idApp, "Por Asignar", medio );

    let parametrosCambioEstadoSinProveedor = {
      metodo: "cambioEstadoAutomaticoSinProveedor",
      idFactura,
      idApp,
      medio
    };


    if (idFactura && idApp && medio) {
      let respuestaCambioEstadoSinProveedor = await ajaxRequest("../ordenpedido/config_app.php",parametrosCambioEstadoSinProveedor,"POST");
      //se envía la orden a los motorizados.
      crearOrdenDunaMotorizado();
      //se vuelve a cargar la lista de pedidis
      cargarPedidosRecibidos();
      setTimeout(function () {
        $("#" + this.filaSeleccionada).addClass("focus");
        let orden = $("#listado_pedido_app").find("li.focus");
        let id_asociado = $(orden).attr("id");
        let codigo_externo = $(orden).attr("codigo_externo");
        let asigna_duna = $(orden).attr("asigna_duna");
        let crea_duna = $(orden).attr("crea_duna");
        let proveedor = $(orden).attr("motorizado_confirm_prov");
        
        if (id_asociado) {
          if (codigo_externo || crea_duna==0) {
            $("#btn_reenviar_bringg").hide();
            if (asigna_duna==1 || proveedor!="DUNA") {
              $("#btn_reenviar_moto_duna").hide();
            } else {
              $("#btn_reenviar_moto_duna").show();
            }
          }
        }
      }, 1000);
    }
  }
}

const ajaxRequest = (url, paramsRequired, type = 'GET') => {
  return $.ajax({
      type,
      url,
      data: paramsRequired,
      success: function (response) {
          return response;
      }
  });
}

function validarJSON(strJSON) {
  if (/^[\],:{}\s]*$/.test(strJSON.replace(/\\["\\\/bfnrtu]/g, '@').
      replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
      replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
      return true;
  }
  return false;
}

function fn_promociones_movistar(num_factura) {
  // send = { metodo: "cargarDetallePedidoApp" };  <--- ejemplo
  let data_to_send = { metodo: "promocionesMovistar"};
  data_to_send.idFactura = num_factura;
  console.log('Inicio funcion prom movistar');
  $.ajax({
      async: false,
      type: "POST", // VERIFICAR ESTO; EL RESTO DE FUNCIONES CREADAS AQUI SON POST
      dataType: "json",
      url: "config_app.php",
      data: data_to_send,
      success: function (datos) {
          if (datos.Respuesta != 'NO APLICA') {
              fn_consume_ws_movistar(datos.Respuesta, num_factura);
          }
      }
  });
}

function fn_consume_ws_movistar(url_data, num_factura) {
  
  data_to_send = {metodo: "consumir_ws"},
  data_to_send.url_data = url_data
  data_to_send.codFactura = num_factura
  data_to_send.codRestaurante = $("#txtCadena").val();
  data_to_send.codCadena = $("#txtRest").val();
  $.ajax({
      async: true,
      type: "POST",
      dataType: "json",
      url: "config_app.php",
      data: data_to_send,
      success: function (datos) {
        if (datos[0].status == 'OK') {
          console.log(datos[0]);
          let QR_Data = datos[0].Num_Cupon;

          fn_auditoria_cupones_movistar(datos[0], 'SUCCESS', data_to_send, url_data);
          fn_SETQR_promociones_movistar(num_factura, QR_Data);
        } else {
          console.log(datos[0]);
          fn_auditoria_cupones_movistar(datos[0], 'ERROR', data_to_send, url_data);
          // alertify.error("Error al consumir WS Movistar: " + datos.message);
        }
      },
      error: function (error){
        fn_auditoria_cupones_movistar('Error al consumir WS Movistar', 'ERROR', data_to_send, url_data);
      }
  }); 
}

function fn_auditoria_cupones_movistar(respuesta, estado, params, url_ws) {
  let data_to_send = { 
      metodo: 'auditoria_cupones_movistar',
      atran_descripcion: JSON.stringify(respuesta),
      atran_accion: estado,
      atran_varchar1: JSON.stringify(params),
      atran_varchar2: url_ws,
      rst_id: $("#txtRest").val(),
      IDUsersPos: $("#txtUsr_Id").val(),
      atran_modulo: 'CUPONES MOVISTAR'
  };
  $.ajax({
      async: false,
      type: "POST",
      dataType: "json",
      url: "config_app.php",
      data: data_to_send,
      success: function (datos) {
          // IGNORED
          
      }
  });
}

function fn_SETQR_promociones_movistar(num_factura,QR_Data) {
  let data_to_send = { metodo: "SetQRPromocionesMovistar"};
  data_to_send.cfac = num_factura;
  data_to_send.QRData = QR_Data;
  $.ajax({
      async: false,
      type: "POST", // VERIFICAR ESTO; EL RESTO DE FUNCIONES CREADAS AQUI SON POST
      dataType: "json",
      url: "config_app.php",
      data: data_to_send,
      success: function (datos) {
          //IGNORED
      }
  });
}

var irAtras = function () {
  $("#listaPedido").show();
  $("#div_busqueda").show();
  $("#detalle_pedido").hide();
  $("#btn_ver").show();
  $("#btn_cancelar").hide();
  mostrarOcultarBotonesPolitica();
};

var cargarDetallePedido = function (codigo, medio) {
  var html = "";
  $("#listado_pedido_app table.detalle_pedido").html(html);
  send = { metodo: "cargarDetallePedidoApp" };
  send.codigo = codigo;
  send.medio = medio;
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function (datos) {
      if (datos.registros > 0) {
        html =
          '<tr><th class="centro" width="20%">CANTIDAD</th><th colspan="2">DESCRIPCION</th><th class="txt_derecha">PVP APP</th><th class="txt_derecha">PVP MXP</th></tr>';
        $("#detalle_pedido table.detalle_pedido").html(html);
        for (var i = 0; i < datos.registros; i++) {
          if (datos[i]["tipo"] == 1) {

            var fidelizacion = '';
            if ( datos[i]['fidelizacion'] === 'SI' )
              fidelizacion = '[CANJE]'
            
            html =
              '<tr><td class="centro">' +
              datos[i]["cantidad"] +
              '</td><td colspan="2">' +
              datos[i]["descripcion"] + ' <b>' + fidelizacion + '</b>' +
              '</td><td class="txt_derecha">' +
              datos[i]["total_app"] +
              '</td><td class="txt_derecha">' +
              datos[i]["total_mxp"] +
              "</td></tr>";
          } else {
            html = '<tr><td></td><td colspan="3" class="modificador"><b>' + datos[i]["descripcion"] + "</b></td><td></td></tr>";
          }
          $("#detalle_pedido table.detalle_pedido").append(html);
        }
        //Descuentos
        if(datos[0]['montoTotalDescuentos'] > 0){
          $("#detalle_pedido table.detalle_pedido").append(
            `<tr><td></td><td colspan="2" class="modificador"></td><th class="txt_derecha" style="font-size: 20px;padding-top: 5px;">Descuento:</th><td class="txt_derecha" style="font-size: 20px;padding-top: 5px;"><b>
            ${datos[0]['montoTotalDescuentos']}</b></td></tr>`
          );
        }
        $("#detalle_pedido table.detalle_pedido").append(
          '<tr><td></td><td colspan="2" class="modificador"></td><th class="txt_derecha fs_20">TOTAL:</th><td class="txt_derecha fs_20"><b>$ ' +
            datos[0]["total"] +
            "</b></td></tr>"
        );
      }
    },
  });
};

var asignarMotorizado = function (idMotorizado, codigo_app) {
  var html = "";
  $("#listado_pedido_app table.detalle_pedido").html(html);
  var proveedorTracking = $("#proveedor_tracking").val();
  var cambio_estado = $("#" + codigo_app).attr("cambio_estado");
  var medio = $("#listado_pedido_app").find("li.focus").attr("medio");
  console.log("asignadno motorizado al medio: "+ medio);

  send = { metodo: "asignarMotorizado" };
  send.codigo_app = codigo_app;
  send.idMotorizado = idMotorizado;
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function (datos) {
      if ( datos[0]["estado"] === 1 ) {
        $("#modalAsignarMotorizado").modal("toggle");
        alertify.success(datos[0]["mensaje"]);
        cargarPedidosRecibidos();
        //if (proveedorTracking && proveedorTracking === "TRADE" && cambio_estado == 'SI') {
        if (cambio_estado == 'SI'){
          //KFC: ASIGNADO = TRADE: Asignado
          cambioEstadoTrade(codigo_app, "Asignado", medio);
        }
        //}
      } else {
        setTimeout(() => {
          if(datos[0]["mensaje"])
          alertify.error("Error: " +datos[0]["mensaje"]);
          else
          alertify.error("EL tiempo de envio de cambio de estado a exedido." );
        }, 200);
        //$("#modalAsignarMotorizado").modal("toggle");
      }
    },
  });
};

var confirmarDesasignarMotorizado = function () {
  var codigo_app = $("#listado_pedido_app").find("li.focus").attr("id");
  var motorizado = $("#listado_pedido_app").find("li.focus").attr("motorizado");
  var idMotorizado = $("#listado_pedido_app").find("li.focus").attr("idmotorizado");
  alertify.confirm("Esta seguro de desasignar el pedido: " + codigo_app + " al motorizado " + motorizado,
    function (e) {
      if (e) {
       desasignarMotorizado(idMotorizado, codigo_app);
      }
    }
  );
};

var desasignarMotorizado = function (idMotorizado, codigo_app) {
  
  var proveedorTracking = $("#proveedor_tracking").val();
  var cambio_estado = $("#" + codigo_app).attr("cambio_estado");

  send = { metodo: "reversarAsignacionMotorolo" };
  send.codigo_app = codigo_app;
  send.idMotorizado = idMotorizado;
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function ( datos ) {
      if (datos[0]["estado"]) {
        $("#listado_pedido_app").find("li.focus").attr('estado', 'POR ASIGNAR');
        $("#listado_pedido_app").find("li.focus").attr('motorizado', '');
        $("#listado_pedido_app").find("li.focus").attr('idmotorizado', '');
        $("#listado_pedido_app").find("li.focus").find(".lista_motorizado").html("");
        $("#listado_pedido_app").find("li.focus").find(".lista_estado").html('<i class="material-icons">two_wheeler</i>');
        alertify.success(datos[0]["mensaje"]);
        // cargarPedidosRecibidos();
        verificarBotones();
        // obtenerCantidadEstadosPedidosApp();
      } else {
        setTimeout(() => {
          alertify.error("Error: " + datos[0]["mensaje"]);
        }, 200);      }

      /*
      if (datos[0]["estado"] === 1 && cambio_estado == 'SI') {
          if (proveedorTracking && proveedorTracking === "TRADE") {
            //KFC: ASIGNADO = TRADE: Asignado
            cambioEstadoTrade(codigo_app, "Por Asignar");
          } 
        alertify.success(datos[0]["mensaje"]);
        
      

      setTimeout(function(){  $('#'+this.filaSeleccionada).addClass("focus"); }, 0);
      $('#btn_asignar').show();
      $('#btn_desasignar').hide();
      $('#btn_en_camino').hide();
      */
    }
  });

};

var cambiarEstadoEnCamino = function (idPeriodo, idMotorizado) {
  var html = "";

  send = { metodo: "cambioTransaccionesEnCamino" };
  send.idPeriodo = idPeriodo;
  send.idMotorizado = idMotorizado;
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function (datos) {
      //console.log(JSON.stringify(datos));
      if (datos[0]["estado"] === 1) {
        cargarPedidosRecibidos();
        $("#modalAsignarMotorizado").modal("toggle");
        alertify.success(datos[0]["mensaje"]);
      } else {
        setTimeout(() => {
          alertify.error("Error: " + datos[0]["mensaje"]);
        }, 200);        //$("#modalAsignarMotorizado").modal("toggle");
      }
    },
  });
};

var cambiarEstadoEntregado = function (idPeriodo, idMotorizado) {
  var html = "";

  send = { metodo: "cambioTransaccionesEntregado" };
  send.idPeriodo = idPeriodo;
  send.idMotorizado = idMotorizado;
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function (datos) {
      //console.log(JSON.stringify(datos));
      if (datos[0]["estado"] === 1) {
        $("#sltd_codigo_app").html('');
        $("#sltd_estado_app").html('');
        cargarPedidosRecibidos();
        alertify.success(datos[0]["mensaje"]);
        $("#modalAsignarMotorizado").modal("toggle");
      } else {
        setTimeout(() => {
          alertify.error("Error: " + datos[0]["mensaje"]);
        }, 200);        //$("#modalAsignarMotorizado").modal("toggle");
      }
    },
  });
};

/**
 * Permite enviar pedido al proveedor cuando se da clic en boton enviar a proveedor
 * @param {*} loading muestra ventana de carga
 */
var reenviarDelivery = async function(loading=0){
  $('#'+this.filaSeleccionada).addClass("focus"); 
  var orden = $("#listado_pedido_app").find("li.focus");
  var idApp = $(orden).attr("id");
  var idFactura = $(orden).attr("codigo_factura");
  var retira_efectivo = $(orden).attr("retira_efectivo");
  let medio = $(orden).attr("medio");
  fn_cargando(loading);

  let nombreProveedorDelivery ='';
  let datosNombreProveedorDelivery = await obtenerNombreProveedorDeliveryPorMedio(medio);
  
  if (validarJSON(datosNombreProveedorDelivery)) {
    datosNombreProveedorDelivery = JSON.parse(datosNombreProveedorDelivery);
  }

  if (datosNombreProveedorDelivery?.registros > 0) {
    const { nombre_proveedor } = datosNombreProveedorDelivery[0];
    nombreProveedorDelivery = nombre_proveedor?.toUpperCase().trim();
  }

  if(retira_efectivo!="SI"){
    nombreProveedorDelivery=retira_efectivo;
  }

  if (idFactura) {
    uberDirectCashStatus(idApp, medio, function(callback) {
      console.log("Uber Direct Cash Pass:", callback.status);
      if (callback.msj != "MEDIO INCORRECTO" && callback.msj != "FORMA DE PAGO INCORRECTA" && callback.msj != "UBER DIRECT EFECTIVO: OFF") {
        if (callback.status) {
          console.log("La Orden " + idApp + " será procesada en Uber Direct Cash");
          medio = 'UBER';
          nombreProveedorDelivery = 'UBER';
          setTimeout(function() {
            alertify.success(callback.msj + ". Orden " + idApp);
          }, 3000);
        } else {
          console.log("La Orden " + idApp + " no será procesada en Uber Direct Cash");
          nombreProveedorDelivery = medio;
          if (callback.msj != "ORDEN NO FACTURADA") {
            setTimeout(function() {
              alertify.error(callback.msj + ". Orden " + idApp);
            }, 3000);
          }
        }
      }
    });

    await verificarEstadoDragonTail(idApp, medio);
  }

  let metodosDelivery={
    "BRINGG" : () => fn_crearOrdenBringg(idFactura,medio, idApp),
    "DUNA" : () => crearOrdenDuna({idFactura, idApp,medio}),
    "OTROS" : () => enviarConsultaOtros(nombreProveedorDelivery,idFactura)
  }

  metodosDelivery[nombreProveedorDelivery.trim()]
      ? metodosDelivery[nombreProveedorDelivery.trim()]()
      : metodosDelivery['OTROS']();

}

var fn_crearOrdenBringg = function($idFactura,medio, $idApp){

  var cambio_estados_automatico = $("#cambio_estados_automatico").val();

  //console.log('Cambio de Estados Automaticos');
  //console.log(cambio_estados_automatico);

  if( cambio_estados_automatico && cambio_estados_automatico == 1 || cambio_estados_automatico === 'SI' ) {

    var $url_bringg =  $("#url_bringg_crear").val();

    send = { metodo: "cambioEstadoBringg" };
    send.idFactura  = $idFactura;
    send.idApp      = $idApp;
    send.url        = $url_bringg;

    $.ajax({
        async: false,
        type: "POST",
        dataType: 'json',
        contentType: "application/x-www-form-urlencoded",
        url: "../ordenpedido/config_app.php",
        data: send,
        success: function( datos ) {
          fn_cargando(0);
            //RESPUESTA DESDE BRINGG

            //console.log('DATOS TRACKING');
            //console.log(datos);

            if(datos && datos != 'error'){                           
              //NOTIFICACION A TRADE DE TRACKING
              trackingTrade(datos, medio, $idApp)
              //trackingTrade(datos, medio)
            }

            cargarPedidosRecibidos();
            setTimeout(function(){  
              $('#'+this.filaSeleccionada).addClass("focus"); 
              var orden = $("#listado_pedido_app").find("li.focus");
              var id_asociado = $(orden).attr("id");
              var codigo_externo = $(orden).attr("codigo_externo");
              var asigna_duna = $(orden).attr("asigna_duna");
              var crea_duna = $(orden).attr("crea_duna");
              var proveedor = $(orden).attr("motorizado_confirm_prov");
              console.log('Codigo Externo Bringg');
              //console.log(codigo_externo);
  

              if(id_asociado){
                if(codigo_externo || crea_duna==0){
                  $('#btn_reenviar_bringg').hide();
                  if(asigna_duna==1 || proveedor!="DUNA"){
                    $('#btn_reenviar_moto_duna').hide();
                  }else{
                    $('#btn_reenviar_moto_duna').show();
                  }
                }else{
                  $('#btn_reenviar_bringg').show();
                }  
              }else{
                $('#btn_reenviar_bringg').hide();
              }
          }, 1000);      
        },
        error: function (jqXHR, textStatus, errorThrown) {
          fn_cargando(0);
          cargarPedidosRecibidos();
          setTimeout(function(){  $('#'+this.filaSeleccionada).addClass("focus"); }, 1000);   
          $('#btn_reenviar_bringg').show();
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        },
    
    });

  }

}

/**
 * @fn crearOrdenDunaMotorizado
 * @author Jean Meza
 * Permite crear la orden del motorizado
 */
async function crearOrdenDunaMotorizado(ordenId){
  var nameelement=ordenId
  var focused = $("#listado_pedido_app").find("li.focus").attr("id");
  if(!nameelement)
    nameelement=focused?focused:$("#btn_reenviar_moto_duna").attr("idlist");
  var listelement=$("#" + nameelement);
  var DUNA_confirm = $(listelement).attr("motorizado_confirm");
  var ProveedorMot = $(listelement).attr("motorizado_confirm_Prov");
  var dunaURL = $(listelement).attr("urlwsduna");
  var comURL = $(listelement).attr("dunaws");
  var medio = $(listelement).attr("medio");
  var codigo = $(listelement).attr("id");
  var idFactura = $(listelement).attr("codigo_factura");
  $("#btn_reenviar_moto_duna").hide();

  //validamos que la opcion de confirmar motorizado este activa, esto se obtiene de las politicas dentro del html la lista del pedido
  console.log("Motorizado Proveedor:"+ProveedorMot);
  console.log("Motorizado:"+DUNA_confirm);
  if (DUNA_confirm == 1 && ProveedorMot == 'DUNA') {
    console.log("medio: " + medio + " idfactura: " + idFactura + " codigo " + codigo);
    if(medio != 'Web-e')
      codigo=idFactura+"_"+codigo+"_"+medio;
    var url=dunaURL+comURL.replace('codigo',encodeURI(codigo));

    url = url.includes('http') 
                  ? url
                  : 'http://' + url;
      
    let parametrosCrearOrden = {
      metodo: "crearOrdenDunaMotorizado",
      url,
      codigo
    };

    let respuestaMotorizado = await ajaxRequest("../ordenpedido/config_app.php",parametrosCrearOrden,"POST");
    if (respuestaMotorizado.includes("Error")) {
      if (reintentos_maximo_duna==0){
        alertify.error("Error en la carga de datos DEUNAMOTO, vuelva a intentar");
        $("#btn_reenviar_moto_duna").show();
      }else{
        console.log("No se pudo cargar DEUNAMOTO, se volverá a cargar");
        if($(listelement).attr("asigna_duna")!=0 && ($(listelement).attr("asigna_duna")*-1)>=reintentos_maximo_duna){
          $("#btn_reenviar_moto_duna").show();
        }else{
          $(listelement).attr("asigna_duna","1");
          $("#btn_reenviar_moto_duna").hide();
        }
      }
    } else {
      $(listelement).attr("asigna_duna","1");
      $("#btn_reenviar_moto_duna").hide();
      alertify.success("Carga de datos DEUNAMOTO correcta");
    }
  }
}

async function crearOrdenDuna({idFactura, idApp,medio}, recargar=true){
  fn_cargando(0);
  let cambioEstadosAutomatico = $("#cambio_estados_automatico").val();

  if(cambioEstadosAutomatico == 1 || cambioEstadosAutomatico === 'SI' ) {

    let parametrosObtenerUrl = { 
      metodo: "obtenerUrlCrearPedidoDuna",
      rst_id: $("#txtRest").val()
    };
    
    cambioEstadoTrade( idApp, "Por Asignar", medio );

    let obtenerUrlIngresoDuna = await ajaxRequest("../ordenpedido/config_app.php", parametrosObtenerUrl, "POST");

    if (validarJSON(obtenerUrlIngresoDuna)) {

      let datosUrlIngresoDuna = JSON.parse(obtenerUrlIngresoDuna);

      let url = datosUrlIngresoDuna[0]?.url.includes('https://') 
                  ? datosUrlIngresoDuna[0]?.url.replace('http://','') 
                  : datosUrlIngresoDuna[0]?.url;

      let parametrosCrearOrdenDuna = {
        metodo: "crearOrdenDuna",
        idFactura,
        idApp,
        url
      };

      if (parametrosCrearOrdenDuna.url) {
        let respuestaCambioEstadoDuna = await ajaxRequest("../ordenpedido/config_app.php",parametrosCrearOrdenDuna,"POST");
        $("#btn_reenviar_bringg").text(`Enviar DUNA`);
        if (respuestaCambioEstadoDuna.includes("Error")) {
          if (reintentos_maximo_duna==0){
            alertify.error("Error en la carga de datos DUNA, vuelva a intentar");
            $("#btn_reenviar_bringg").show();
          }else{
            console.log("No se pudo cargar DUNA, se volverá a cargar");
            if($("#" + idApp).attr("crea_duna")!=0 && ($("#" + idApp).attr("crea_duna")*-1)>=reintentos_maximo_duna)
              $("#btn_reenviar_bringg").show();
            else{
              $("#" + idApp).attr("asigna_duna","1");
              $("#btn_reenviar_bringg").hide();
            }
          }
        } else {
          //se envía la orden a los motorizados.
          crearOrdenDunaMotorizado(idApp);
          //se vuelve a cargar la lista de pedidos
          if (recargar){
            cargarPedidosRecibidos();
          }
          setTimeout(function () {
            $("#" + this.filaSeleccionada).addClass("focus");
            var orden = $("#listado_pedido_app").find("li.focus");
            var id_asociado = $(orden).attr("id");
            var codigo_externo = $(orden).attr("codigo_externo");
            var asigna_duna = $(orden).attr("asigna_duna");
            var crea_duna = $(orden).attr("crea_duna");
            var proveedor = $(orden).attr("motorizado_confirm_prov");

            if (id_asociado) {
              if (codigo_externo || crea_duna==0) {
                $("#btn_reenviar_bringg").hide();
                if ((proveedor=="DUNA" && asigna_duna<1 && reintentos_maximo_duna==0)  || (asigna_duna!=0 && (asigna_duna*-1)>reintentos_maximo_duna)) {
                  $("#btn_reenviar_moto_duna").show();
                } else {
                  $("#btn_reenviar_moto_duna").hide();
                }
              } else {
                $("#btn_reenviar_bringg").show();
              }
            } else {
              $(orden).attr("crea_duna","1");
              $("#btn_reenviar_bringg").hide();
            }
          }, 1000);
          alertify.success("Carga de orden DUNA correcta");
        }
      }
    }
  }
}

var fn_accionMotorizado = function () {
  $("#mdl_rdn_pdd_crgnd").show();
  var orden = $("#listado_pedido_app").find("li.focus");
  console.log("works!");

  //console.log(orden);
  var idMotorizado = $(orden).attr("idMotorizado");
  var motorizado = $(orden).attr("motorizado");
  //var motorizado = $(orden).attr('motorizado');
  var codigo = $(orden).attr("id");
  var estado = $(orden).attr("estado");

  var medioPedido = $(orden).attr("medio");

  if (estado === "PENDIENTE") {

      send = { metodo: "verificarMotorizadoAgregador" };
      send.medio = medioPedido;
      send.periodo  =  $("#idPeriodo").val();

    $.ajax({
      async: false,
      type: "POST",
      dataType: "json",
      contentType: "application/x-www-form-urlencoded",
      url: "../ordenpedido/config_app.php",
      data: send,
      success: function (datos) {

        if(datos && datos[0] && datos[0]['respuesta'] == 1){
          confirmarOrden();
        }else{
          alertify.error("No se encuentra un motorizado ASIGNADO para el medio "+medioPedido);
        }

      },error: function (jqXHR, textStatus, errorThrown) {
        ('No se pudo realizar la validacion de motorizado agregador vigente')
        console.error(jqXHR);
        console.error(textStatus);
        console.error(errorThrown);
      } 
    });

  }
  if (estado === "POR ASIGNAR") {
    // console.log('Voy a asignar');
    cargarMotorizadosActivos();
    $("#modalAsignarMotorizado").modal("toggle");
  }
  if (estado === "ASIGNADO" || estado === "EN CAMINO") {
    cargarListaTransaccionesAsignadas(idMotorizado, motorizado);
  }

  $("#mdl_rdn_pdd_crgnd").hide();
};


var fn_asignarMotorizado = function ( m ) {
  $('#modal_cargando_pedido').show();

  $(m).addClass('seleccionado');
  var idMotorizado = $(m).attr("idMotorizado");
  var motorizado = $(m).attr("motorizado");
  var codigo = $("#listado_pedido_app").find("li.focus").attr("id");
  var medio = $("#listado_pedido_app").find("li.focus").attr("medio");
  var code = $("#listado_pedido_app").find("li.focus").attr("codigo_fac");

  $(".asignarMotorizadosLst li").removeClass("focus");
  $( motorizado ).addClass("focus");

  alertify.confirm("¿Desea Asignar el pedido del medio: "+medio+": " + code + " al motorizado: " + motorizado + "?",
    function (e) {
      if (e) {
        asignarMotorizado( idMotorizado, codigo );
        setTimeout(function(){  $('#'+this.filaSeleccionada).addClass("focus"); }, 600);
        $('#btn_asignar').hide();
        $('#btn_en_camino').show();
        $('#btn_desasignar').show();
        //$("#modalAsignarMotorizado").modal("toggle");
        $('#modal_cargando_pedido').hide();
        enviarMonitor(codigo);
      } else {
        $(m).removeClass('seleccionado');
        $('#modal_cargando_pedido').hide();
      }
    }
  );
};

var fn_cambiarEstado = function ( idPeriodo, idMotorizado, estado, medio, motorizado, arrtransacciones ) {
  $(".asignarMotorizadosLst li").removeClass("focus");
  $("#modal_cargando_pedido").show();

  $(motorizado).addClass("focus");
  if ( estado === "ASIGNADO" ) {
    alertify.confirm(
      "¿Estas Seguro de colocar a " + motorizado + " EN CAMINO?",
      function (e) {
        if (e) {

          $("#btn_anular").hide();
          $("#btn_facturar").hide();
          $("#btn_en_camino").hide();
          $("#btn_entregado").hide();
          $("#btn_asignar").hide();
          $("#btn_desasignar").hide();

          cambiarEstadoEnCamino(idPeriodo, idMotorizado);
          //console.log(transacciones,medio);
          cambioEstadoTradeVarias(arrtransacciones, "En Camino", medio);
          $("#modal_cargando_pedido").hide();
        }else{
          $("#modal_cargando_pedido").hide();
        }
      }
    );
  } else if ( estado === "EN CAMINO" ) {
    $("#modal_cargando_pedido").show();
    alertify.confirm("¿Estás seguro de colocar estas transacciones como ENTREGADAS?",
      function (e) {
        console.log(arrtransacciones,medio);
        if (e) {
          cambiarEstadoEntregado(idPeriodo, idMotorizado);
          console.log(arrtransacciones,medio);
          cambioEstadoTradeVarias(arrtransacciones, "Entregado", medio);
          //cambiarEstadoEntregado(idPeriodo,idMotorizado);
          //$("#modalAsignarMotorizado").modal("toggle");
          $("#modal_cargando_pedido").hide();
        }else{
          $("#modal_cargando_pedido").hide();
        }
      }
    );
  }
};

var verificarBotones = function () {
  var transaccion = $("#listado_pedido_app").find("li.focus");

  var idMotorizado = $(transaccion).attr("idMotorizado");
  var motorizado = $(transaccion).attr("motorizado");
  //var motorizado = $(orden).attr('motorizado');
  var codigo = $(transaccion).attr("id");
  var codigo_app = $(transaccion).attr("codigo_fac");
  var estado = $(transaccion).attr("estado");
  var medio = $(transaccion).attr("medio");
  var automatico = $(transaccion).attr("automatico");
  let codigoExterno=$(transaccion).attr("codigo_externo");
  let asigna_duna=$(transaccion).attr("asigna_duna");
  var crea_duna = $(transaccion).attr("crea_duna");
  var uber_cash = $(transaccion).attr("uber_cash");

  var notificar_medio = $(transaccion).attr("notificar_medio");
  var notificar_listo = $(transaccion).attr("notificar_listo");

  if (notificar_medio == 1 && notificar_listo == 0 && estado == 'ENTREGADO')   {
    $("#btn_notificar").show();
  }else { 
    $("#btn_notificar").hide();
  }

  if (codigoExterno || crea_duna==0) {
    $("#btn_reenviar_bringg").hide();
    if (asigna_duna==1) {
      $("#btn_reenviar_moto_duna").hide();
    }
  }

  $("#sltd_codigo_app").html('<b>#'+codigo_app+'</b>');
  $("#sltd_estado_app").html('<b>'+estado+'</b>');

  if (estado == "INGRESADO") {
    $("#btn_anular").hide();
    $("#btn_facturar").hide();
    $("#btn_en_camino").hide();
    $("#btn_entregado").hide();
    $("#btn_asignar").hide();
    $("#btn_desasignar").hide();
    $("#btn_ver").show();
    $("#btn_transferir").hide();
  } else if (estado == "PENDIENTE") {
    $("#btn_facturar").hide();
    $("#btn_en_camino").hide();
    $("#btn_entregado").hide();
    $("#btn_asignar").hide();
    $("#btn_confirmar").show();
    $("#btn_anular").hide();
    $("#btn_desasignar").hide();
    $("#btn_ver").show();
    if (existeAgregador(medio)) {
      $("#btn_transferir").hide();
    } else {
      $("#btn_transferir").show();
    }
  } else if (estado == "RECIBIDO") {
    $("#btn_facturar").show();
    $("#btn_en_camino").hide();
    $("#btn_entregado").hide();
    $("#btn_asignar").hide();
    $("#btn_confirmar").hide();
    $("#btn_anular").show();
    $("#btn_anular").html("Anular");
    $("#btn_desasignar").hide();
    $("#btn_ver").show();
    $("#btn_transferir").hide();
  } else if (estado == "POR ASIGNAR") {
    $("#btn_facturar").hide();
    $("#btn_en_camino").hide();
    $("#btn_entregado").hide();
    if (uber_cash =="true") {
      $("#btn_asignar").hide();
    } else {
      $("#btn_asignar").show();
    }
    $("#btn_confirmar").hide();
    $("#btn_anular").hide();
    $("#btn_anular").html("Nota de Crédito");
    $("#btn_desasignar").hide();
    $("#btn_ver").show();
    $("#btn_transferir").hide();
    
  } else if (estado == "ASIGNADO") {
    $("#btn_facturar").hide();
    // $("#btn_cambiar_estado").show();
    $("#btn_en_camino").show();
    $("#btn_entregado").hide();
    $("#btn_asignar").hide();
    $("#btn_confirmar").hide();
    $("#btn_anular").hide();
    $("#btn_anular").html("Nota de Crédito");
    $("#btn_desasignar").show();
    $("#btn_ver").show();
    $("#btn_transferir").hide();
  } else if (estado == "EN CAMINO") {
    $("#btn_facturar").hide();
    // $("#btn_cambiar_estado").show();
    $("#btn_en_camino").hide();
    $("#btn_entregado").show();
    $("#btn_asignar").hide();
    $("#btn_confirmar").hide();
    $("#btn_anular").hide();
    $("#btn_anular").html("Nota de Crédito");
    $("#btn_desasignar").hide();
    $("#btn_ver").show();
    $("#btn_transferir").hide();
  } else if (estado == "ENTREGADO") {
    $("#btn_facturar").hide();
    // $("#btn_cambiar_estado").show();
    $("#btn_en_camino").hide();
    $("#btn_entregado").hide();
    $("#btn_asignar").hide();
    $("#btn_confirmar").hide();
    $("#btn_anular").hide();
    $("#btn_anular").html("Nota de Crédito");
    $("#btn_desasignar").hide();
    $("#btn_ver").show();
    $("#btn_transferir").hide();
  } else if (estado == "ANULADO") {
    $("#btn_facturar").hide();
    // $("#btn_cambiar_estado").show();
    $("#btn_en_camino").hide();
    $("#btn_entregado").hide();
    $("#btn_asignar").hide();
    $("#btn_confirmar").hide();
    $("#btn_anular").hide();
    $("#btn_desasignar").hide();
    $("#btn_ver").show();
    $("#btn_transferir").hide();
  } else {
    $("#btn_facturar").hide();
    $("#btn_en_camino").hide();
    $("#btn_entregado").hide();
    $("#btn_asignar").hide();
    $("#btn_confirmar").hide();
    $("#btn_anular").hide();
    $("#btn_desasignar").hide();
    $("#btn_ver").hide();
    $("#btn_transferir").hide();
    $("#btn_cancelar").hide();
    $("#btn_reenviar_bringg,#btn_reenviar_moto_duna").hide();
    $("#sltd_codigo_app").html('');
    $("#sltd_estado_app").html('');
  }

  let cambio=0;
  if (document.getElementById("Activo"+codigo))
    cambio=document.getElementById("Activo"+codigo).value;

    var reintentos=(crea_duna*-1);
    //cambio de estado automatico a manual, despues de un tiempo, excepto para enviar motorizado
    //si cambio es 1 && envio es correcto se ingresa para ocultar boton || o si se supera reintentos se ingresa para mostrar boton || o si reintentos esta desactivado se ingresa para mostrar boton
    if (cambio == 0 || (cambio == 0 && (crea_duna==1 || reintentos>=reintentos_maximo_duna || reintentos_maximo_duna==0))) {
      mostrarOcultarBotonesPolitica();
    }
};

/*
var cambioEstadoTradeVarias = function(transacciones, estado) {

    console.log("Entregado: ", estado);
    
    function capitalize(string) {
        return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
    }

    transacciones.forEach(e=>{
        cambioEstadoTrade(e,capitalize(estado));
    })

}*/

var cargar_impresionesError = function () {

  $("#btn_pedidos_ocultar_app").hide();

  if ( $("#config_servicio_pickup").val() == 1) {
    $("#btn_pedidos_pickup_app").show();
  } else {
    $("#btn_pedidos_pickup_app").hide();
  }

  $("#pnl_pickup").hide();
  
  $("#cuado").hide();
  $("#cntMesas").show();
  $("#cnt_pedidos").hide();
  $("#cnt_pedidos_error").show();
  
  if ($("#config_servicio_domicilio").val() == 1) {
    $("#btn_pedidos_app").show();
    $("#btn_pedidos_entregados").show();
  } else {
    $("#btn_pedidos_app").hide();
    $("#btn_pedidos_entregados").hide();
  }

  $("#btn_facturar").hide();
  $("#btn_ver").hide();
  $("#btn_cancelar").hide();
  $("#listaPedido").hide();
  $("#div_busqueda").hide();
  $("#detalle_pedido").hide();
  $("#btn_imprimir_error").show();
  $("#btn_pedidos_error").hide();
  mostrarOcultarBotonesPolitica();

  // Scroll
  $("#lst_imp_error").shortscroll();

  var html = "";
  $("#lst_error").html(html);
  send = { metodo: "cargarImpresionesError" };
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function (datos) {
      if (datos.registros > 0) {
        for (var i = 0; i < datos.registros; i++) {
          html =
            '<li class="datos_error" id="' +
            datos[i]["idCanalMovimiento"] +
            '" onclick="seleccionarTransaccionError(this)"><div class="codigo_transaccion"><b>' +
            datos[i]["idFactura"] +
            '</b></div><div class="total_transaccion">$' +
            datos[i]["total"] +
            '</div><div class="error_transaccion">' +
            datos[i]["tipo_error"] +
            '</div><div class="tipo_transaccion">' +
            datos[i]["tipo"] +
            "</div></li>";
          $("#lst_error").append(html);
        }
      } else {
        $("#lst_error").append(
          '<li class="datos_error"><div>No existen pedidos.</div></li>'
        );
      }
    },
  });
};

var seleccionarTransaccionError = function (id) {
  $("#lst_error li").removeClass("focus");
  $(id).addClass("focus");
};

var imprimir_transaccionError = function () {
  var idCanalMovimiento = $("#lst_error").find("li.focus").attr("id");

  var html = "";
  $("#lst_error").html(html);
  send = { metodo: "impresionTransaccionError" };
  send.idCanalMovimiento = idCanalMovimiento;
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function (datos) {
      if (datos.estado > 0) {
        cargar_impresionesError();
        alertify.alert(datos.mensaje);
      } else {
        setTimeout(() => {
          alertify.error("Error: " + datos.mensaje);
        }, 200);
      }
    },
  });
};

/**
 * ANULACION DE PEDIDOS
 */
var anular = function () {
  var codigo = $("#listado_pedido_app").find("li.focus").attr("id");
  var medio = $("#listado_pedido_app").find("li.focus").attr("medio");
  var estadoPedido = $("#listado_pedido_app").find("li.focus").attr("estado");

  if ( !codigo ) {
    alertify.error("Seleccione una transaccion...");
    return;
  }
  
  alertify.confirm("¿Estas seguro de anular este pedidos?",
    function (e) {
      if (e) {

        if ( estadoPedido == "RECIBIDO" ) {
          send = { metodo: "cambioEstadoPedido" };
          send.codigo_app = codigo;
          send.estado = "ANULADO";
          fn_notificacion_tracking(send, codigo);
        } else if (
          estadoPedido == "POR ASIGNAR" ||
          estadoPedido == "ASIGNADO" ||
          estadoPedido == "EN CAMINO" ||
          estadoPedido == "ENTREGADO"
        ) {
          fn_validarAnulacion(codigo);
        }

        $("#btn_anular").hide();
        $("#sltd_codigo_app").html("");
        $("#sltd_estado_app").html("");

      }
    });

};

fn_notificacion_tracking = function (dataNotificacion, codigo) {
  //Logica de notificacion de acuerdo al proceedor TRADE U Otro
  var proveedorTracking = $("#proveedor_tracking").val();
  var cambio_estados_automatico = $("#cambio_estados_automatico").val();
  var cambio_estado = $("#" + codigo).attr("cambio_estado");
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: dataNotificacion,
    success: function (datos) {

      if ( datos.estado === 1 ) {

        alertify.success(datos.mensaje);
        cargarPedidosRecibidos();

        if ( cambio_estado == 'SI' ) {
          //NOTIFICACION A TRADE
          if ( proveedorTracking && proveedorTracking === "TRADE" ) {
            //KFC: ANULADA = TRADE: Anulada
            cambioEstadoTrade(codigo, "Anulada");
          }
        }

        //NOTIFICACION A BRINGG
        if( cambio_estados_automatico && cambio_estados_automatico == 1 || cambio_estados_automatico === 'SI' ){
          fn_anular_bringg(codigo);
        }

        $("#btn_facturar").show();
        $("#btn_ver").show();
        $("#btn_cancelar").hide();
        $("#listaPedido").show();
        $("#div_busqueda").show();
        $("#detalle_pedido").hide();
        $("#btn_anular").hide();
        mostrarOcultarBotonesPolitica();

      } else {
        setTimeout(() => {
          alertify.error("Error  : " + datos.mensaje);

        }, 200);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert(jqXHR);
      alert(textStatus);
      alert(errorThrown);
    },
  });
};

fn_anular_bringg = function($codigo_app){
  send = { metodo: "obtenerCodigoExterno" };
  send.codigo_app  = $codigo_app;

  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function (datos) {
      var $codigo_externo = datos[0]['codigo_externo'];
      var $cfac_id        = datos[0]['cfac_id'];

      if($codigo_externo){
    
        var $url_bringg =  $("#url_bringg_anular").val();    
        send = { metodo: "anulacionOrdenBringg" };
        send.idBringg     = $codigo_externo;
        send.url          = $url_bringg;
        send.cfac_id      = $cfac_id;

        $.ajax({
            async: false,
            type: "POST",
            dataType: 'json',
            contentType: "application/x-www-form-urlencoded",
            url: "../ordenpedido/config_app.php",
            data: send,
            success: function( datos ) {
                //RESPUESTA DESDE BRINGG
                console.log('Respuesta Bringg ANULACION');
                console.log(datos);
            }
        });
    

      }



    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error(jqXHR);
      console.error(textStatus);
      console.error(errorThrown);
    },
  });

}


function fn_validarAnulacion($codigo_app) {
  send = { metodo: "facturaPorPedido" };
  send.codigo_app = $codigo_app;

  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function (datos) {
      if (datos[0]["estado"] === 1) {
        fn_validaCajeroActivoParaAnulacion(
          datos[0]["factura"],
          datos[0]["documento_con_datos"],
          $codigo_app
        );
      } else {
        setTimeout(() => {
          alertify.error("Error: " + datos[0]["mensaje"]);

        }, 200);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert(jqXHR);
      alert(textStatus);
      alert(errorThrown);
    },
  });
}

function fn_validaCajeroActivoParaAnulacion(
  factura,
  documento_con_datos,
  $codigo_app
) {
  var send = { validaCajeroActivoParaAnulacion: 1 };
  send.facturaId = factura;
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../anulacion/config_anularOrden.php",
    data: send,
    success: function (datos) {
      if (datos.str > 0) {
        if (datos.estado == "Activo") {
          fn_dialogCredenciales(1, documento_con_datos, factura, $codigo_app);
        } else {
          alertify.alert(datos.mensaje);
          return false;
        }
      } else {
        alertify.error("Error CajeroActivoParaAnulacion");
        return false;
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert(jqXHR);
      alert(textStatus);
      alert(errorThrown);
    },
  });
}

function fn_dialogCredenciales(
  opcionBoton,
  documento_con_datos,
  cfac_id,
  $codigo_app
) {
  $("#anularOrden").prop("disabled", true);
  $("#anulacionesContenedor").show();
  $("#anulacionesContenedor").dialog({
    modal: true,
    position: "center",
    closeOnEscape: false,
    width: 500,
    height: 440,
    draggable: false,
    open: function () {
      $(".ui-dialog-titlebar").hide();
      $("#usr_clave").attr(
        "onchange",
        "fn_validarUsuario(" +
          opcionBoton +
          "," +
          documento_con_datos +
          ",'" +
          cfac_id +
          "','" +
          $codigo_app +
          "');"
      );
      fn_numerico("#usr_clave");
    },
  });
}

function fn_validarUsuario(opcion, documento_con_datos, cfac_id, $codigo_app) {
  $("#numPad").hide();
  var usr_clave = $("#usr_clave").val();
  var cfac_id = cfac_id;
  var usr_tarjeta;

  if (usr_clave.indexOf("%") >= 0) {
    var old_usr_clave = usr_clave.split("?;")[0];
    var new_usr_clave = old_usr_clave.replace(new RegExp("%", "g"), "");
    usr_tarjeta = new_usr_clave;
    usr_clave = "noclave";
  } else {
    usr_tarjeta = 0;
  }

  if (usr_clave != "") {
    var send;
    var rst_id = $("#txtRest").val();
    send = { validarUsuario: 1 };
    send.rst_id = rst_id;
    send.usr_clave = usr_clave;
    send.usr_tarjeta = usr_tarjeta;
    $.getJSON("../anulacion/config_anularOrden.php", send, function (datos) {
      if (datos.str > 0) {
        $("#anulacionesContenedor").dialog("close");
        lc_userAdmin = datos.usr_id;
        $("#usr_clave").val("");
        if (opcion == 1) {
          $("#hide_opcion_nota_credito").val(0);
          fn_motivoAnulacion(
            cfac_id,
            1,
            lc_userAdmin,
            documento_con_datos,
            $codigo_app
          );
        } else if (opcion == 2) {
          $("#hide_opcion_nota_credito").val(1);
          alertify.alert("Para anular esta factura de CONSUMIDOR FINAL dirijirse a la sección de Transacciones");
          $("#numPad").hide();
          $("#anulacionesContenedor").hide();
          $("#anulacionesMotivo").hide();
          $("#anulacionesMotivo").dialog("close");
        }
      } else {
        fn_numerico("#usr_clave");
        var cabeceramsj = "Atenci&oacute;n!!";
        var mensaje = "Clave incorrecta vuelva a intentarlo.";
        fn_mensajeAlerta(cabeceramsj, mensaje, 1, "#usr_clave");
      }
    });
  } else {
    fn_numerico("#usr_clave");
    var cabeceramsj = "Atenci&oacute;n!!";
    var mensaje = "Ingrese su clave de administrador(a)!!!!!!.";
    fn_mensajeAlerta(cabeceramsj, mensaje, 1, "#usr_clave");
  }
}

/* Funcion que permite visualizar una alerta */
function fn_mensajeAlerta(cabecera, mensaje, evento, objeto) {
  var msj = "<h3>" + cabecera + "</h3> <br> <h3>" + mensaje + "</h3> <br><br>";

  alertify.alert(msj, function (e) {
    if (e) {
      if (evento == 1) {
        $(objeto).val("");
        $(objeto).focus();
      }
    }
  });
}

function fn_motivoAnulacion(
  cfac_id,
  fpf_id,
  id_admin,
  documento_con_datos,
  $codigo_app
) {
  if (!$("li.focus").length) {
    alertify.alert("No ha seleccionado una factura para eliminar");
  } else {
    fn_alfaNumerico("#motivoObservacion");
    $("#anulacionesMotivo").show();
    $("#anulacionesMotivo").dialog({
      modal: true,
      width: 510,
      height: 440,
      position: {
        my: "top",
        at: "top+80",
      },
      resize: false,
      opacity: 0,
      show: "none",
      hide: "none",
      open: function (event, ui) {
        setTimeout(() => {
          $("#keyboard").show();
          $("#motivosAnulacion").val(0);
          $("#motivoObservacion").val("");
          $("#btn_ok_teclado").attr(
            "onclick",
            'fn_verificarConfiguracionTipoEnvioEstacion("' +
              cfac_id +
              '", "' +
              id_admin +
              '", "' +
              documento_con_datos +
              '","' +
              $codigo_app +
              '")'
          );
          $("#btn_cancelar_teclado").attr(
            "onclick",
            "fn_cerrarDialogoMotivo()"
          );
          $(".ui-dialog-titlebar").hide();
        }, 400);
      },
    });
  }
}

function fn_verificarConfiguracionTipoEnvioEstacion(
  cfac_id,
  lc_userAdmin,
  documento_con_datos,
  $codigo_app
) {
  var pais_aplica_nc = $("#aplica_nc_sinconsumidor").val();
  var documento_con_datos = documento_con_datos;
  var opcion = $("#motivosAnulacion").val();
  var motivo = $("#motivoObservacion").val();
  motivo = trim(motivo);

  if (opcion != "0") {
    if (motivo.length > 0) {
      if (pais_aplica_nc == 1) {
        if (documento_con_datos == 1) {
          fn_formasPago(cfac_id, $codigo_app);
        } else {
          console.log("Entre 2");
          alertify.alert(
            "Para anular esta factura de CONSUMIDOR FINAL dirijirse a la sección de Transacciones"
          );
          $("#keyboard").hide();
          $("#numPad").hide();
          $("#anulacionesMotivo").hide();
          $("#anulacionesMotivo").dialog("close");
        }
      } else {
        fn_formasPago(cfac_id, $codigo_app);
      }

      $("#btn_anulacancela").show();
    } else {
      var cabeceramsj = "Atenci&oacute;n!!";
      var mensaje = "El Comentario de anulaci&oacute;n es obligatorio.";
      fn_mensajeAlerta(cabeceramsj, mensaje, 1, "#motivoObservacion");
    }
  } else {
    var cabeceramsj = "Atenci&oacute;n!!";
    var mensaje = "Seleccione un motivo de anulaci&oacute;n.";
    fn_mensajeAlerta(cabeceramsj, mensaje, 1, "#motivosAnulacion");
  }
}

function fn_formasPago(cfac_id, codigo_app) {
  $("#anulacionesMotivo").hide();
  $("#anulacionesMotivo").dialog("close");
  $("#keyboard").hide();

  var estado_seleccionado = $("#cboEstadoPedido").val();

  if (!$("li.focus").length) {
    var cabeceramsj = "Atenci&oacute;n!!";
    var mensaje = "No ha seleccionado una factura para anular.";
    fn_mensajeAlerta(cabeceramsj, mensaje, 0, "0");
  } else {
    var cfac_id = cfac_id;

    $("#anularOrden").prop("disabled", true);

    $("#mdl_rdn_pdd_crgnd").show();
    var send;
    send = { metodo: "generarNotaCreditoApp" };
    send.idFactura = cfac_id;
    send.idMotivoAnulacion = $("#motivosAnulacion").val();
    send.idUsuario = lc_userAdmin;
    send.observacion = $("#motivoObservacion").val();
    $.ajax({
      async: false,
      type: "POST",
      url: "../anulacion/cambioEstado_TransaccionApp.php",
      data: send,
      dataType: "json",
      success: function ( datos ) {
        //console.log("Respuesta Anulación:", datos);        
        if ( datos.code === 200 ) {
          $("#mdl_rdn_pdd_crgnd").hide();
          alertify.success( "Nota de crédito generada correctamente." );
          $("#btn_anular").hide();

          if ( estado_seleccionado != "ENTREGADO" && estado_seleccionado != "ANULADO" ) {
            cargarPedidosRecibidos();
          } else {
            cargarPedidosEntregados( estado_seleccionado, "" );
          }

          verificarBotones();

        } else {
          $("#mdl_rdn_pdd_crgnd").hide();
          $("#anularOrdeen").prop("disabled", false);
          alertify.alert("Error " + datos.message + ", no se pudo generar la nota de crédito.");
        }
      },
      error: function () {
        $("#anularOrden").prop("disabled", false);
        $("#mdl_rdn_pdd_crgnd").hide();
        alertify.error("Error al generar nota de crédito...");
      },
    });
  }
}

function fn_cerrarDialogoMotivo() {
  $("#anulacionesMotivo").dialog("close");
  $("#usr_clave").val("");
  $("#motivoObservacion").val("");
  $("#anulacionesMotivo .anulacionesSubmit").empty();
  $("#numPad").hide();
  $("#keyboard").hide();
  $("#anularOrden").prop("disabled", false);
}

function trim(cadena) {
  var retorno = cadena.replace(/^\s+/g, "");
  retorno = retorno.replace(/\s+$/g, "");
  return retorno;
}

async function filtrarPedidosEstacion(pedidos) {
  let responseMedio = await obtenerMediosEstacion("1");
  let strMedios = responseMedio[0].medios;
  

  if (strMedios !== "") {
    let mediosVisualizar = strMedios.split(",");
    let pedidosFiltrados = [];
    let contadorRegistros = 0;

    mediosVisualizar.forEach((medioVizualizar) => {
      let cantidadTotalPedidos = pedidos.length;

      for (
        let indexPedidos = 0;
        indexPedidos < cantidadTotalPedidos;
        indexPedidos++
      ) {
        let pedido = pedidos[indexPedidos];
        if (
          pedido?.medio.toUpperCase().trim() ==
          medioVizualizar.toUpperCase().trim()
        ) {
          pedidosFiltrados.push(pedido);
          contadorRegistros++;
        }
      }
    });

    pedidosFiltrados.registros = contadorRegistros;
    pedidos = pedidosFiltrados;

  }
  return pedidos;
}

 async function cargarLista(pedidos) {
  $('#mdl_rdn_pdd_crgnd').show();
  let datos = await filtrarPedidosEstacion(pedidos);

  let cantidadPedidos = datos.length;
  $("#listado_pedido_app").empty();
  if (cantidadPedidos == 0) {
    $("#listado_pedido_app").append(
      '<li class="datos_app"><div>No existen pedidos.</div></li>'
    );
  }
  for (var i = 0; i < cantidadPedidos; i++) {
    // verifico que tiempo indicar
    var tiempo = 0;
    var tfecha = 0;
    var semaforo;
    // console.log(now);
    var icon ;

    if (datos[i]["tiempo"] != null) {
      tiempo = datos[i]["tiempo"];
      tfecha = tiempo;
    }

    if(tiempoEsperaEstadoPendiente[i] == null){
      if(tiempoEsperaEstadoPendiente[i]>0){
        //console.log("Pendiente:"+tiempoEsperaEstadoPendiente[i]); 
      }else{
        tiempoEsperaEstadoPendiente[i]=0;    
      }
    }
    if(tiempoEsperaEstadoRecibido[i] == null){
      if(tiempoEsperaEstadoRecibido[i]>0){
        //console.log("Recibido:"+tiempoEsperaEstadoRecibido[i]); 
      }else{
        tiempoEsperaEstadoRecibido[i]=0;
      }

    }
    if(tiempoEsperaEstadoPorAsignar[i] == null){
      if(tiempoEsperaEstadoPorAsignar[i]>0){
        //console.log("Por Asignar:"+tiempoEsperaEstadoPorAsignar[i]); 
      }else{
        tiempoEsperaEstadoPorAsignar[i] =0;
      }

    }
    if(tiempoEsperaEstadoAsignado[i] == null){
      if(tiempoEsperaEstadoAsignado[i]>0){
        //console.log("Asignado:"+tiempoEsperaEstadoPorAsignar[i]); 
      }else{
        tiempoEsperaEstadoAsignado[i]=0;
      }
    }

    if(tiempoEsperaEstadoEnCamino[i] == null){
      if(tiempoEsperaEstadoEnCamino[i]>0){
        //console.log("Asignado:"+tiempoEsperaEstadoEnCamino[i]); 
      }else{
        tiempoEsperaEstadoEnCamino[i]=0;
      }
    }


  let tiempoStorage = {
    tiempoEntrega:0,
    tiempoDespacho:0,
    tiempoTotal:0 ,
    tiempoPendiente:0 ,
    tiempoRecibido:0,
    tiempoPorAsignar:0,
    tiempoAsignado:0,
    tiempoEnCamino:0,
    codigo_app:datos[i]["codigo_app"],
  }

  if(localStorage.getItem("tiempos") === null ){
    let pedidosTiempoStorage=[];
    pedidosTiempoStorage.push(tiempoStorage);
    localStorage.setItem("tiempos",JSON.stringify(pedidosTiempoStorage));
    //console.log("mE CREO");

  }else{    
    let pedidosTiempoStorage=JSON.parse(localStorage.getItem("tiempos"));
    
    if(pedidosTiempoStorage[i]){
      
      let buscaPosicionEnStorage = pedidosTiempoStorage.findIndex((storage)=> {          
        return storage.codigo_app === datos[i]["codigo_app"]        
      });        

      let posicionStorage = buscaPosicionEnStorage;
      
      if(posicionStorage < 0){
        pedidosTiempoStorage.push(tiempoStorage);
        localStorage.setItem("tiempos",JSON.stringify(pedidosTiempoStorage));
        //console.log("me estoy creando");
      }else{
        //console.log("Ya existe ese Registro en la posición: "+posicionStorage);
      }
    }else{
      //console.log("No existe voy a crear");
      pedidosTiempoStorage.push(tiempoStorage);
      localStorage.setItem("tiempos",JSON.stringify(pedidosTiempoStorage));
    }
  }

  switch (datos[i]["estado"]) {
    case "POR ASIGNAR":
      tiempoEsperaEstadoPorAsignar[i]= datos[i]["tiempo"];
      //console.log("Estoy por asignar"+tiempoEsperaEstadoPorAsignar[i]) ;
      
      let pedidosTiempoStoragePorAsignar=JSON.parse(localStorage.getItem("tiempos"));

      let buscaPosicionEnStoragePorAsignar = pedidosTiempoStoragePorAsignar.findIndex((storage)=> {          
        return storage.codigo_app === datos[i]["codigo_app"]        
      });

      let posicionStoragePorAsignar = buscaPosicionEnStoragePorAsignar;
      
      if(posicionStoragePorAsignar >= 0){
       
        if( tiempoEsperaEstadoPorAsignar[i] == 0){
          comparaTiempo[posicionStoragePorAsignar]=tiempoEsperaEstadoPorAsignar[i];
        }else{
          if(tiempoEsperaEstadoPorAsignar[i]   != comparaTiempo[posicionStoragePorAsignar] ){
            comparaTiempo[posicionStoragePorAsignar] = tiempoEsperaEstadoPorAsignar[i] ;
            var minutosRecibidos = tiempoEsperaEstadoPorAsignar[i] - pedidosTiempoStoragePorAsignar[posicionStoragePorAsignar].tiempoPorAsignar;
            pedidosTiempoStoragePorAsignar[posicionStoragePorAsignar].tiempoPorAsignar += minutosRecibidos;
  
            var actualizaTiempoDespacho = pedidosTiempoStoragePorAsignar[posicionStoragePorAsignar].tiempoPendiente + pedidosTiempoStoragePorAsignar[posicionStoragePorAsignar].tiempoRecibido + pedidosTiempoStoragePorAsignar[posicionStoragePorAsignar].tiempoPorAsignar;
            var actualizaTiempoTotal = pedidosTiempoStoragePorAsignar[posicionStoragePorAsignar].tiempoEntrega + actualizaTiempoDespacho;
  
            pedidosTiempoStoragePorAsignar[posicionStoragePorAsignar].tiempoEntrega = 0;
            pedidosTiempoStoragePorAsignar[posicionStoragePorAsignar].tiempoDespacho = actualizaTiempoDespacho;
            pedidosTiempoStoragePorAsignar[posicionStoragePorAsignar].tiempoTotal = actualizaTiempoTotal;
            pedidosTiempoStoragePorAsignar[posicionStoragePorAsignar].codigo_app = datos[i]["codigo_app"];
  
            localStorage.setItem("tiempos",JSON.stringify(pedidosTiempoStoragePorAsignar));
          }
        }
        // console.log(comparaTiempo[posicionStoragePorAsignar]);
        //console.log( tiempoEsperaEstadoPorAsignar[i]);
       
      }

      break;
    case "ASIGNADO":
      tiempoEsperaEstadoAsignado[i]= datos[i]["tiempo"];
      //console.log("Estoy asignado"+tiempoEsperaEstadoAsignado[i]);

      let pedidosTiempoStorageAsignado=JSON.parse(localStorage.getItem("tiempos"));

      let buscaPosicionEnStorageAsignado = pedidosTiempoStorageAsignado.findIndex((storage)=> {          
        return storage.codigo_app === datos[i]["codigo_app"]        
      });

      let posicionStorageAsignado = buscaPosicionEnStorageAsignado;
      
      if(posicionStorageAsignado >= 0){
       
        if( tiempoEsperaEstadoAsignado[i] == 0){
          comparaTiempo[posicionStorageAsignado]=tiempoEsperaEstadoAsignado[i];
        }else{
          if(tiempoEsperaEstadoAsignado[i]   != comparaTiempo[posicionStorageAsignado] ){
            comparaTiempo[posicionStorageAsignado] = tiempoEsperaEstadoAsignado[i] ;
            var minutosRecibidos = tiempoEsperaEstadoAsignado[i] - pedidosTiempoStorageAsignado[posicionStorageAsignado].tiempoAsignado;
            pedidosTiempoStorageAsignado[posicionStorageAsignado].tiempoAsignado += minutosRecibidos;
  
            var actualizaTiempoDespacho = pedidosTiempoStorageAsignado[posicionStorageAsignado].tiempoPendiente + pedidosTiempoStorageAsignado[posicionStorageAsignado].tiempoRecibido + pedidosTiempoStorageAsignado[posicionStorageAsignado].tiempoPorAsignar + pedidosTiempoStorageAsignado[posicionStorageAsignado].tiempoAsignado ;
            var actualizaTiempoTotal = pedidosTiempoStorageAsignado[posicionStorageAsignado].tiempoEntrega + actualizaTiempoDespacho;
  
            pedidosTiempoStorageAsignado[posicionStorageAsignado].tiempoEntrega = 0;
            pedidosTiempoStorageAsignado[posicionStorageAsignado].tiempoDespacho = actualizaTiempoDespacho;
            pedidosTiempoStorageAsignado[posicionStorageAsignado].tiempoTotal = actualizaTiempoTotal;
            pedidosTiempoStorageAsignado[posicionStorageAsignado].codigo_app = datos[i]["codigo_app"];
  
            localStorage.setItem("tiempos",JSON.stringify(pedidosTiempoStorageAsignado));
          }
        }
        // console.log(comparaTiempo[posicionStorageAsignado]);
        // console.log( tiempoEsperaEstadoAsignado[i]);
       
      }




      break;
    case "EN CAMINO":
      tiempoEsperaEstadoEnCamino[i]= datos[i]["tiempo"];
      // console.log("Estoy En Camino"+tiempoEsperaEstadoEnCamino[i]);

      let pedidosTiempoStorageEnCamino=JSON.parse(localStorage.getItem("tiempos"));

      let buscaPosicionEnStorageEnCamino = pedidosTiempoStorageEnCamino.findIndex((storage)=> {          
        return storage.codigo_app === datos[i]["codigo_app"]        
      });

      let posicionStorageEnCamino = buscaPosicionEnStorageEnCamino;
      
      if(posicionStorageEnCamino >= 0){
       
        if( tiempoEsperaEstadoEnCamino[i] == 0){
          comparaTiempo[posicionStorageEnCamino]=tiempoEsperaEstadoEnCamino[i];
        }else{
          if(tiempoEsperaEstadoEnCamino[i]   != comparaTiempo[posicionStorageEnCamino] ){
            comparaTiempo[posicionStorageEnCamino] = tiempoEsperaEstadoEnCamino[i] ;
            
            var minutosRecibidos = tiempoEsperaEstadoEnCamino[i] - pedidosTiempoStorageEnCamino[posicionStorageEnCamino].tiempoEnCamino;
            // console.log("minutosRecibidos:"+minutosRecibidos);
            pedidosTiempoStorageEnCamino[posicionStorageEnCamino].tiempoEnCamino += minutosRecibidos;
              
            var actualizaTiempoEngrega = pedidosTiempoStorageEnCamino[posicionStorageEnCamino].tiempoEnCamino;
            var actualizaTiempoDespacho = pedidosTiempoStorageEnCamino[posicionStorageEnCamino].tiempoPendiente + pedidosTiempoStorageEnCamino[posicionStorageEnCamino].tiempoRecibido + pedidosTiempoStorageEnCamino[posicionStorageEnCamino].tiempoPorAsignar + pedidosTiempoStorageEnCamino[posicionStorageEnCamino].tiempoAsignado ;
            var actualizaTiempoTotal = actualizaTiempoEngrega + actualizaTiempoDespacho;
          
            pedidosTiempoStorageEnCamino[posicionStorageEnCamino].tiempoEntrega = actualizaTiempoEngrega;
            pedidosTiempoStorageEnCamino[posicionStorageEnCamino].tiempoDespacho = actualizaTiempoDespacho;
            pedidosTiempoStorageEnCamino[posicionStorageEnCamino].tiempoTotal = actualizaTiempoTotal;
            pedidosTiempoStorageEnCamino[posicionStorageEnCamino].codigo_app = datos[i]["codigo_app"];
  
            localStorage.setItem("tiempos",JSON.stringify(pedidosTiempoStorageEnCamino));
          }
        }
        // console.log(comparaTiempo[posicionStorageEnCamino]);
        // console.log( tiempoEsperaEstadoEnCamino[i]);       
      }
      break;
    case "INGRESADO":
      icon = '<i class="material-icons">fiber_news</i>';
      break;
    case "PENDIENTE":
      tiempoEsperaEstadoPendiente[i]= datos[i]["tiempo"];
      // console.log("Estoy pendiente"+tiempoEsperaEstadoPendiente[i]);

      let pedidosTiempoStoragePendiente=JSON.parse(localStorage.getItem("tiempos"));

      let buscaPosicionEnStoragePendiente = pedidosTiempoStoragePendiente.findIndex((storage)=> {          
        return storage.codigo_app === datos[i]["codigo_app"]        
      });        

      let posicionStoragePendiente = buscaPosicionEnStoragePendiente;
      
      if(posicionStoragePendiente >= 0){

        // var actualizaTiempoPendiente = pedidosTiempoStoragePendiente[posicionStoragePendiente].tiempoDespacho + pedidosTiempoStoragePendiente[posicionStoragePendiente].tiempoRecibido;
        // var actualizaTiempoTotal = pedidosTiempoStorageRecibido[posicionStorageRecibido].tiempoEntrega + actualizaTiempoDespacho;
  

        pedidosTiempoStoragePendiente[posicionStoragePendiente].tiempoEntrega=0;
        pedidosTiempoStoragePendiente[posicionStoragePendiente].tiempoPendiente=tiempoEsperaEstadoPendiente[i];
        pedidosTiempoStoragePendiente[posicionStoragePendiente].tiempoDespacho=tiempoEsperaEstadoPendiente[i];
        pedidosTiempoStoragePendiente[posicionStoragePendiente].tiempoTotal=pedidosTiempoStoragePendiente[posicionStoragePendiente].tiempoEntrega + pedidosTiempoStoragePendiente[posicionStoragePendiente].tiempoDespacho;
        pedidosTiempoStoragePendiente[posicionStoragePendiente].codigo_app=datos[i]["codigo_app"];

        localStorage.setItem("tiempos",JSON.stringify(pedidosTiempoStoragePendiente));
      }
      break;

    case "RECIBIDO":
      tiempoEsperaEstadoRecibido[i]= datos[i]["tiempo"];
      // console.log("Estoy Recibido"+tiempoEsperaEstadoRecibido[i]);
      
      
      let pedidosTiempoStorageRecibido=JSON.parse(localStorage.getItem("tiempos"));

      let buscaPosicionEnStorageRecibido = pedidosTiempoStorageRecibido.findIndex((storage)=> {          
        return storage.codigo_app === datos[i]["codigo_app"]        
      });

      let posicionStorageRecibido = buscaPosicionEnStorageRecibido;
      
      if(posicionStorageRecibido >= 0){
       
        if( tiempoEsperaEstadoRecibido[i] == 0){
          comparaTiempo[posicionStorageRecibido]=tiempoEsperaEstadoRecibido[i];
        }else{
          if(tiempoEsperaEstadoRecibido[i]   != comparaTiempo[posicionStorageRecibido] ){
            comparaTiempo[posicionStorageRecibido] = tiempoEsperaEstadoRecibido[i] ;
            var minutosRecibidos = tiempoEsperaEstadoRecibido[i] - pedidosTiempoStorageRecibido[posicionStorageRecibido].tiempoRecibido;
            pedidosTiempoStorageRecibido[posicionStorageRecibido].tiempoRecibido += minutosRecibidos;
  
            var actualizaTiempoDespacho = pedidosTiempoStorageRecibido[posicionStorageRecibido].tiempoPendiente + pedidosTiempoStorageRecibido[posicionStorageRecibido].tiempoRecibido;
            var actualizaTiempoTotal = pedidosTiempoStorageRecibido[posicionStorageRecibido].tiempoEntrega + actualizaTiempoDespacho;
  
            pedidosTiempoStorageRecibido[posicionStorageRecibido].tiempoEntrega = 0;
            pedidosTiempoStorageRecibido[posicionStorageRecibido].tiempoDespacho = actualizaTiempoDespacho;
            pedidosTiempoStorageRecibido[posicionStorageRecibido].tiempoTotal = actualizaTiempoTotal;
            pedidosTiempoStorageRecibido[posicionStorageRecibido].codigo_app = datos[i]["codigo_app"];
  
            localStorage.setItem("tiempos",JSON.stringify(pedidosTiempoStorageRecibido));
          }
        }
        // console.log(comparaTiempo[posicionStorageRecibido]);
        // console.log( tiempoEsperaEstadoRecibido[i]);
       
      }

      break;
    default:
      icon = '<i class="material-icons">inbox</i>';
  }

  // console.log("PENDIENTEANTESDESUMA:"+tiempoEsperaEstadoPendiente[i]);
  // console.log("RECIBIDOANTESDESUMA:"+tiempoEsperaEstadoRecibido[i]);
  // console.log("PORASIGNARANTESDESUMA:"+tiempoEsperaEstadoPorAsignar[i]);
  // console.log("ASIGNADOANTESDESUMA:"+tiempoEsperaEstadoAsignado[i]);
  // console.log("ASIGNADOANTESDESUMA:"+tiempoEsperaEstadoEnCamino[i]);


  let pedidosTiempoStorageGuardado=JSON.parse(localStorage.getItem("tiempos"));

  let buscaPosicionEnStorageGuardado = pedidosTiempoStorageGuardado.findIndex((storage)=> {          
    return storage.codigo_app === datos[i]["codigo_app"]        
  });

  let posicionStorageGuardado = buscaPosicionEnStorageGuardado;
  
  if(posicionStorageGuardado >= 0){
    tiempoDespacho = pedidosTiempoStorageGuardado[posicionStorageGuardado].tiempoDespacho;
    tiempoTotalPedido = pedidosTiempoStorageGuardado[posicionStorageGuardado].tiempoTotal;
    tiempoMotorizado = pedidosTiempoStorageGuardado[posicionStorageGuardado].tiempoEntrega;
  }



    switch (datos[i]["estado"]) {
      case "POR ASIGNAR":
        icon = '<i class="material-icons">two_wheeler</i>';
        break;
      case "ASIGNADO":
        icon = '<i class="material-icons">assignment_ind</i>';
        break;
      case "EN CAMINO":
        icon = '<i class="material-icons">commute</i>';
        break;
      case "INGRESADO":
        icon = '<i class="material-icons">fiber_news</i>';
        break;
      case "PENDIENTE":
        icon = '<i class="material-icons">pending_actions</i>';
        break;
      case "RECIBIDO":
        icon = '<i class="material-icons">compare_arrows</i>';
        break;
      default:
        icon = '<i class="material-icons">inbox</i>';
    }
    semaforo = dibujarSemaforo( tiempo );

    html =
      '<li class="datos_app" style="background-color:'+datos[i]["color_fila"]+';color:'+datos[i]["color_texto"]+'" ' +
      'id="' +
      datos[i]["codigo_app"] +
      '" tiempo="' +
      datos[i]["tiempo"] +
      '" codigo_fac="' +
      datos[i]["codigo"] +
      '" documento="' +
      datos[i]["documento"] +
      '" formapago="' +
      datos[i]["forma_pago"] +
      '" medio="' +
      datos[i]["medio"] +
      '" observacion="' +
      datos[i]["observacion"] +
      '" adicional="' +
      datos[i]["datos_envio"] +
      '" direccion="' +
      datos[i]["direccion_despacho"] +
      '" total="' +
      datos[i]["total"] +
      '" automatico="' +
      datos[i]["automatico"] +
      '" fecha="' +
      datos[i]["fecha"] +
      '" fidelizacion="' +
      datos[i]["fidelizacion"] +
      '" telefono="' +
      datos[i]["telefono"] +
      '" cliente="' +
      datos[i]["cliente"] +
      '"  idMotorizado="' +
      datos[i]["idMotorizado"] +
      '"  motorizado="' +
      datos[i]["motorizado"] +
      '" motorizado_telefono="' +
      datos[i]["motorizado_telefono"] +
      '" estado="' +
      datos[i]["estado"] +
      '" dunaws="' +
      datos[i]["dunaws"] +
      '" urlwsduna="' +
      datos[i]["urlwsduna"] +
      '" motorizado_confirm="' +
      datos[i]["motorizado_confirm"] +
      '" motorizado_confirm_Prov="' +
      datos[i]["motorizado_confirm_Prov"] +
      '" cambio_estado="' +
      datos[i]["cambio_estado"] +
      '" codigo_externo="' +
      datos[i]["codigo_externo"] +
      '" asigna_duna="' +
      datos[i]["asigna_duna"] +
      '" crea_duna="' +
      datos[i]["crea_duna"] +
      '" retira_efectivo="' +
      datos[i]["retira_efectivo"] +
      '" opciones_proveedor="' +
      datos[i]["opciones_proveedor"] +
      '" duna_reintentos="' +
      datos[i]["duna_reintentos"] +
      '" codigo_factura="' +
      datos[i]["codigo_factura"] +
      '" uber_cash="' +
      datos[i]["uber_cash"] +
      '" notificar_medio="' +
      datos[i]["notificar_medio"] +
      '" notificar_listo="' +
      datos[i]["notificar_listo"] +
      '" onclick="seleccionarPedido(this)"><div class="lista_medios">' +
      datos[i]["medio"] +
      '</div><div class="codigo_app"><b>' +
      datos[i]["codigo"] +
      '</b></div><div class="cliente_app">' +
      datos[i]["cliente"] +
      '</div><div class="lista_motorizado">' +
      datos[i]["motorizado"].substr(0, 20) +
      '</div><div class="lista_estado"> ' +
      icon +
      '</div><div class="lista_semaforo">' +
      semaforo +
      '</div><div class="lista_tiempo">' +
      formatoHorasMinutos( tiempo ) +
      '</div><div class="lista_tiempo">' +
      formatoHorasMinutos( tiempoDespacho ) +
      '</div><div class="lista_tiempo">' +
      formatoHorasMinutos( tiempoMotorizado ) +
      '</div><div class="lista_tiempo">' +
      formatoHorasMinutos( tiempoTotalPedido ) +
      "</div>"+
      "<input type='hidden' id='Activo"+datos[i]["codigo_app"]+"' name='Activo"+datos[i]["codigo_app"]+"' value='"+datos[i]["activar"]+"'>"+
      "</li>";
    $("#listado_pedido_app").append(html);
   // $("#listado_pedido_app").children().css("background-color",datos[i]["color_fila"]);
   // $("#listado_pedido_app").children().css("color",datos[i]["color_texto"]);

   
  }

  runAfterListLoad();
  $('#mdl_rdn_pdd_crgnd').hide();
}

function formatoHorasMinutos( tiempo ) {
  var horas = 0;
  var minutos = tiempo%60;
  if( tiempo%60 > 0 ) {
    minutos = tiempo%60;
      horas = Math.trunc(tiempo/60);
  } else {
    horas = Math.trunc(tiempo/60);
  }
  return completarCeros(horas) + 'h' + completarCeros(minutos);
}

function completarCeros ( numero ) {
	valor = numero;
    if ( numero < 10 ) {
    	numero = '0' + numero;
    }
	return numero;
}

//**********VITALITY



function mostrarPanel() {
  $("#rdn_pdd_brr_ccns").show();
}

var opcionSetVitality = function(opcion) {
  vitality = opcion;
  recarga = "";
  $("#cntRecargas").hide();
  $("#registroCliente").hide();
  $("#pre2").hide();
  $("#ingresoCedula").hide();
  flujo_seguimiento("ingresoVitality", "pre1", true);
  $("#tqtIngresoVT").html('Vitality<p style="font-size: 0.6em;" class="mensajePequeno">Lea código de seguridad.</p>');
  $("#txtCodigoVitality").focus();
};

function update() {
  //console.log("Timer se ejecuto " + new Date());
  var listFocus=$("#listado_pedido_app").find("li.focus");
  
  /**
   * Se valida si hay un elemento seleccionado para volverlo a seleccionar despues de cargar la lista
   */
  if(listFocus.length>0){
    ultimoElementoSeleccionado = listFocus.attr("id");
  }else{
    ultimoElementoSeleccionado=""; 
  }

  $("#listado_pedido_app")
    .find(".datos_app")
    .each(function () {
      var minute = $(this).attr("tiempo");
      minute++;

      var difference = formatoHorasMinutos( minute );
      $(this).attr("tiempo", minute);

      var semaforo = dibujarSemaforo( minute );
      $(this).children(".lista_tiempo").html( difference );
      $(this).children(".lista_semaforo").html( semaforo );
      /**
       * se cargaba varias veces toda la lista
       * @author Jean Meza Cevallos - 19/07/2022
       */
      //cargarPedidosRecibidos(); //se comenta esta linea

    });
    /**
     * se coloca fuera del for para que se actualice una sola vez
     * se adiciona la funcion validaRententoDuna
     * se valida si la ultima actualizacion ocurrio antes del minuto
     * @author Jean Meza Cevallos - 19/07/2022
     */
    hora_actual=new Date().getTime();
    lasexecution=localStorage.getItem('lasexecution');
    
    var diferencia = (hora_actual - lasexecution) / (1000); //--> sin division=milisegundos -> 1000*milisegundos=segundos -> 60*segundos=minutos -> 60*minutos=horas -> 24*horas=días
    console.log("comprobando="+diferencia);
    if(diferencia>50){
      console.log("cargando lista en update");
      runReintentosOnUpdate=true;
      cargarPedidosRecibidos();
    }
}


/**
 * Esta funcion se ejecuta despues de que se cargue la lista.
 * @author Jean Meza Cevallos - 19/07/2022
 */
function runAfterListLoad() {
  if(ultimoElementoSeleccionado!=""){
    seleccionarPedido($("#" + ultimoElementoSeleccionado))
  }
}


/**
 * Esta funcion se ejecuta despues de que se cargue la lista.
 * @author Jean Meza Cevallos - 19/07/2022
 */
async function runAfterListPendiente() {
  reintentos_maximo_duna=$("#listado_pendientes").children("li").first().attr("duna_reintentos");
  if(reintentos_maximo_duna>0 && runReintentosOnUpdate==true){
    validaReenvioDuna();
    validaReenvioDunaMoto();
  }
  localStorage.setItem('lasexecution', new Date().getTime());
  runReintentosOnUpdate=false;

  if(ultimoElementoSeleccionado!=""){
    seleccionarPedido($("#" + ultimoElementoSeleccionado))
  }
}

/**
 * Permite validar si el reenvio de duna esta activo en alguno de los elementos y volver a intentar este envio
 * @author Jean Meza Cevallos - 19/07/2022
 */
function validaReenvioDuna() {
  console.log('comienza validacion reenvio duna ' + new Date());
  $("#listado_pendientes > li").each(function() {
    //var motorizado_confirm_prov=$(this).attr("motorizado_confirm_prov");
    var motorizado_confirm_prov="DUNA";
    var crea_duna=$(this).attr("crea_duna");
    var intentos_fallidos=crea_duna*-1;
    if(crea_duna<1 && (motorizado_confirm_prov=="DUNA" || intentos_fallidos<=reintentos_maximo_duna)){ //EXISTEN ENVÝOS FALLIDOS
      console.log("cantidad de intentos fallidos: " + intentos_fallidos);
      if(intentos_fallidos<=reintentos_maximo_duna){
        var codigo=$(this).attr("id");
        console.log("se volverá a enviar codigo: " + codigo);
        var orden=$(this);
        var idApp = $(orden).attr("id");
        var idFactura = $(orden).attr("codigo_factura");
        let medio = $(orden).attr("medio");
        var automatico = $(orden).attr("automatico");
        $("#cambio_estados_automatico").val( automatico );
        
        //crearOrdenDuna({idFactura,idApp,medio},false); //asi estaba
        crearOrdenProveedores(idFactura,idApp,medio);
      }
    }
  });
  console.log("termina validacion reenvio duna");
}

/**
 * Permite validar si el reenvio de duna esta activo en alguno de los elementos y volver a intentar este envio
 * @author Jean Meza Cevallos - 19/07/2022
 */
function validaReenvioDunaMoto() {
  console.log('comienza validacion reenvio duna motorizado ' + new Date());
  $("#listado_pendientes > li").each(function() {
    //var motorizado_confirm_prov=$(this).attr("motorizado_confirm_prov");
    var motorizado_confirm_prov="DUNA";
    var asigna_duna=$(this).attr("asigna_duna");
    var crea_duna=$(this).attr("crea_duna");
    var codigo_externo=$(this).attr("codigo_externo");
    if(asigna_duna<1 && motorizado_confirm_prov=="DUNA" && crea_duna>=1 && (codigo_externo || codigo_externo != 'null' || codigo_externo != '')){ //EXISTEN ENVÝOS FALLIDOS
      var intentos_fallidos=$(this).attr("asigna_duna")*-1;
      console.log("cantidad de intentos fallidos: " + intentos_fallidos);
      if(intentos_fallidos<=reintentos_maximo_duna){
        var codigo=$(this).attr("id");
        console.log("se volverá a enviar codigo: " + codigo);
        
        crearOrdenDunaMotorizado(codigo);
      }
    }
  });
  console.log("termina validacion reenvio duna motorizado");
}

async function crearOrdenProveedores(idFactura,idApp,medio) {
    let nombreProveedorDelivery ='';
    let datosNombreProveedorDelivery = await obtenerNombreProveedorDeliveryPorMedio(medio);
    
    if (validarJSON(datosNombreProveedorDelivery)) {
      datosNombreProveedorDelivery = JSON.parse(datosNombreProveedorDelivery);
    }
  
    if (datosNombreProveedorDelivery?.registros > 0) {
      const { nombre_proveedor } = datosNombreProveedorDelivery[0];
      nombreProveedorDelivery = nombre_proveedor?.toUpperCase().trim();
    }
  
    var orden=$("#" + idApp);
    var retira_efectivo = $(orden).attr("retira_efectivo");
    if(retira_efectivo!="SI"){
		nombreProveedorDelivery=retira_efectivo;
	}

    let uberdirect = $(orden).attr("uber_cash");
    if (idFactura && uberdirect !== 'true') {
      uberDirectCashStatus(idApp, medio, function(callback) {
        console.log("Uber Direct Cash Pass:", callback.status);
        if (callback.msj != "MEDIO INCORRECTO" && callback.msj != "FORMA DE PAGO INCORRECTA" && callback.msj != "UBER DIRECT EFECTIVO: OFF") {
          if (callback.status) {
            console.log("La Orden " + idApp + " será procesada en Uber Direct Cash");
            medio = 'UBER';
            nombreProveedorDelivery = 'UBER';
            setTimeout(function() {
              alertify.success(callback.msj + ". Orden " + idApp);
            }, 3000);
          } else {
            console.log("La Orden " + idApp + " no será procesada en Uber Direct Cash");
            nombreProveedorDelivery = medio;
            if (callback.msj != "ORDEN NO FACTURADA") {
              setTimeout(function() {
                alertify.error(callback.msj + ". Orden " + idApp);
              }, 3000);
            }
          }
        }
      });
      await verificarEstadoDragonTail(idApp, medio);
    }

    if (nombreProveedorDelivery && !nombreProveedorDelivery.includes(',')) {
      let metodosDelivery={
        "BRINGG" : () => fn_crearOrdenBringg(idFactura,medio, idApp),
        "DUNA" : () => crearOrdenDuna({idFactura, idApp,medio},false),
        "OTROS" : () => enviarConsultaOtros(nombreProveedorDelivery,idFactura)
      }

    metodosDelivery[nombreProveedorDelivery.trim()]
        ? metodosDelivery[nombreProveedorDelivery.trim()]()
        : metodosDelivery['OTROS']();
  }else{
    console.log("Envio cancelado por múltiples proveedores");
  }
}
   

function pararTimerSemaforo() {
  //console.log('paro timer semaforo');
  clearInterval(timerSemaforo);
}

function dibujarSemaforo( tiempo ) {
  var semaforoconfig = JSON.parse($("#semaforoConfig").val());

  var clength = Object.values(semaforoconfig).length - 1;
  var min = 0;
  var luz = "";
  for (var i = 0; i < clength; i++) {
    if (tiempo === 0 && i === 0) {
      luz =
        '<span class="semaforo" style="background-color:' +
        semaforoconfig[i].color +
        '"></span>';
    }
    if (tiempo > min && tiempo <= semaforoconfig[i].tiempo) {
      luz =
        '<span class="semaforo" style="background-color:' +
        semaforoconfig[i].color +
        '"></span>';
    }
    if (tiempo >= semaforoconfig[i].tiempo && i + 1 === clength) {
      luz =
        '<span class="semaforo" style="background-color:' +
        semaforoconfig[i].color +
        '"></span>';
    }
    min = semaforoconfig[i].tiempo;
  }

  //console.log(luz);

  return luz;
}

var obtenerCantidadEstadosPedidosApp = function () {

  send = { metodo: "obtenerCantidadEstadosPedidosApp" };
  send.idPeriodo = $("#idPeriodo").val();
  send.idCadena = $("#txtCadena").val();
  send.idRestaurante = $("#txtRest").val();
  $.ajax({
    async: true,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function (datos) {
      if (datos.registros > 0) {
        cargarBurbujasBotonesApp(datos.cantidad[0], datos.todos[0]);
      } else {
        alertify.error("No se pudo obtener la cantidad de estados de pedidos");
      }
    },
  });
};

function obtenerPedidosEnProgreso (){
  send = { metodo: "cargarPedidosApp" };
  send.estado = "RECIBIDO";
  (send.estadoBusqueda = "PRINCIPAL"), (send.parametroBusqueda = "");
  return $.ajax({
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function (response) {
      return response;
    },
  });

}

async function obtenerCantidadEstadosPedidosFiltrados(){
}

function cargarBurbujasBotonesApp(data, todos) 
{
  $("#btn_filtro_app_pendiente").find("span").attr("badge",'');
  $("#btn_filtro_app_recibido").find("span").attr("badge", '');
  $("#btn_filtro_app_porasignar").find("span").attr("badge",'');
  $("#btn_filtro_app_asignado").find("span").attr("badge",'');
  $("#btn_filtro_app_encamino").find("span").attr("badge",'');

  data.forEach(element => {
    if ("PENDIENTE" in element) {
      $("#btn_filtro_app_pendiente").find("span").attr("badge",element["PENDIENTE"]);
    }

    if ("RECIBIDO" in element) {
      $("#btn_filtro_app_recibido").find("span").attr("badge", element["RECIBIDO"]);
    }

    if ("POR_ASIGNAR" in element) {
      $("#btn_filtro_app_porasignar").find("span").attr("badge",element["POR_ASIGNAR"]);
    }

    if ("ASIGNADO" in element) {
      $("#btn_filtro_app_asignado").find("span").attr("badge",element["ASIGNADO"]);
    }

    if ("EN_CAMINO" in element) {
      $("#btn_filtro_app_encamino").find("span").attr("badge",element["EN_CAMINO"]);
    }
  });

  todos.forEach(element => {
    if ("TODOS" in element) {
      $("#btn_filtro_app_principal").find("span").attr("badge",element["TODOS"]);
    }
  });
}
async function mostrarOcultarBotonesPolitica() {
  var cambio_estados_automatico = $("#cambio_estados_automatico").val();
  //cambio_estados_automatico = 'SI';
  //console.log('CAMBIO ESTADOS AUTOMATICO - BOTONES POLITICA');
  //console.log(cambio_estados_automatico);

  if( cambio_estados_automatico && cambio_estados_automatico == 1 || cambio_estados_automatico === 'SI' ) {
    $("#btn_en_camino").hide();
    $("#btn_entregado").hide();
    $("#btn_asignar").hide();
    $("#btn_desasignar").hide();
    $('#btn_reenviar_moto_duna').hide();
    $("#btn_reenviar_bringg").hide();

    var transaccion = $("#listado_pedido_app").find("li.focus");
    if(transaccion.length==0){
      if(ultimoElementoSeleccionado!=""){
        seleccionarPedido($("#" + ultimoElementoSeleccionado))
      }
      if(transaccion.length==0){
        transaccion = $("#listado_pedido_app").find("li").first();
      }
    }
    var codigo_app = $(transaccion).attr("id");
    var codigo_factura = $(transaccion).attr("codigo_factura");
    var codigo_externo = $(transaccion).attr("codigo_externo");
    var asigna_duna = $(transaccion).attr("asigna_duna");
    var crea_duna = $(transaccion).attr("crea_duna");
    var proveedor = $(transaccion).attr("motorizado_confirm_prov");
    var duna_reintentos = $(transaccion).attr("duna_reintentos");
    var estado = $(transaccion).attr("estado");
    var retira_efectivo = $(transaccion).attr("retira_efectivo");
    let medio = $(transaccion).attr("medio");

    let nombreProveedorDelivery ='NINGUNO';
    let datosNombreProveedorDelivery = await obtenerNombreProveedorDeliveryPorMedio(medio);
    
    if (validarJSON(datosNombreProveedorDelivery)) {
      datosNombreProveedorDelivery = JSON.parse(datosNombreProveedorDelivery);
    }
  
    if (datosNombreProveedorDelivery?.registros > 0) {
      const { nombre_proveedor } = datosNombreProveedorDelivery[0];
      nombreProveedorDelivery = nombre_proveedor?.toUpperCase().trim();
    }

    let uberOff = false;
    if (codigo_factura !== undefined && codigo_factura !== null) {
      if (nombreProveedorDelivery.includes('UBER')) {
        uberDirectCashStatus(codigo_app, medio, function(callback) {
          console.log("Uber Direct Cash Pass:", callback.status);
          if (callback.msj == "UBER DIRECT EFECTIVO: OFF") {
            if (!nombreProveedorDelivery.includes(',')) {
              nombreProveedorDelivery = medio.toUpperCase().trim();
            } else {
              uberOff = true;
            }
          }
        });
      }
    }

    if(retira_efectivo!="SI"){
      nombreProveedorDelivery=retira_efectivo;
    }

    if (nombreProveedorDelivery && nombreProveedorDelivery.includes(',')) {
      let proveedor_envio = nombreProveedorDelivery.replace(" ", "").split(',');
      var html = "";
      //var contador = 0;
      $("#btn_reenviar_bringg").text(`Enviar a`);

      $("#enviarMotorizadosLst").html(html);
      $("#enviarHeader").html(html);
      $("#enviarFooter").html(html);
      var header = "<h3>Enviar Pedido #"+codigo_app+" a: </h3>";
      $("#enviarHeader").html(header);
      for (var i = 0; i < proveedor_envio.length; i++) {
        if (uberOff && proveedor_envio[i] != 'UBER' || medio == 'UBER') {
          //html = "<button class='btn btn-primary' id='medio_"+contador+"' data-idmotorizado='"+datos[i]["idMotorizado"]+"' data-codigo='"+codigo_factura+"' data-medio='"+proveedor_envio[i]+"' onclick='enviarConsultaAgregadorJDP("+contador+")'>"+proveedor_envio[i]+"</button></br>";
          html = "<div style='margin-top:5px !important; display: flex; justify-content: center; width: 100%;'><button class='btn btn-primary' id='medio_" + proveedor_envio[i] + "' data-codigo='" + codigo_factura + "' data-medio='" + proveedor_envio[i] + "' onclick='enviarConsultaSelectedAgregador(\"" + proveedor_envio[i] + "\",\"" + codigo_factura + "\",\"" + medio + "\",\"" + codigo_app + "\")'>" + proveedor_envio[i] + "</button></div>";
          $("#enviarMotorizadosLst").append(html);
          //contador++;
        }
      }
      var str = "<button class=\"btn_cerrar\" onClick=\"$('#modalEnviarMotorizado').modal('toggle');\">Cerrar</button>";
      $("#enviarFooter").append(str);

      $("#btn_reenviar_bringg").attr("onclick", "").unbind("click");
      $("#btn_reenviar_bringg").on('click',function(){
        $('#modalEnviarMotorizado').modal('show');
      });
      
    }else {
      $("#btn_reenviar_bringg").attr("onclick", "").unbind("click");

      //valida si se muestra el loading
      switch (nombreProveedorDelivery) {
        case "BRINGG":
        case "DUNA":
          $("#btn_reenviar_bringg").attr("onclick", "reenviarDelivery()");
        default:
          $("#btn_reenviar_bringg").attr("onclick", "reenviarDelivery(1)");
      }

      $("#btn_reenviar_bringg").text(`Enviar ${nombreProveedorDelivery}`);
    }
  
    //$("#btn_reenviar_bringg").text(`Enviar ${nombreProveedorDelivery}`);

    if(((crea_duna<1 && duna_reintentos==0) || (crea_duna*-1)>=duna_reintentos || (nombreProveedorDelivery && nombreProveedorDelivery.includes(','))) && codigo_app && codigo_factura && ( !codigo_externo || codigo_externo == 'null' || codigo_externo == '' ) && $("#cnt_pedidos").is(":visible")) {
      let gestionarBtnReenviar = {
        "INGRESADO"   : () => $('#btn_reenviar_bringg').hide(),
        "PENDIENTE"   : () => $('#btn_reenviar_bringg').hide(),
        "RECIBIDO"    : () => $('#btn_reenviar_bringg').hide(),
        "POR ASIGNAR" : () => $('#btn_reenviar_bringg').show(),
        "ASIGNADO"    : () => $('#btn_reenviar_bringg').show(),
        "EN CAMINO"   : () => $('#btn_reenviar_bringg').show(),
        "ENTREGADO"   : () => $('#btn_reenviar_bringg').hide(),
        "ANULADO"     : () => $('#btn_reenviar_bringg').hide()
      };
      gestionarBtnReenviar?.[estado]?.();

      if (nombreProveedorDelivery === 'NINGUNO') {
        $("#btn_reenviar_bringg,#btn_reenviar_moto_duna").hide();
      }

    }else if (((asigna_duna<1 && duna_reintentos==0) || (asigna_duna*-1)>=duna_reintentos) && crea_duna==1 && codigo_app && codigo_factura && proveedor=="DUNA" && ( codigo_externo && codigo_externo != 'null' && codigo_externo != '') && $("#cnt_pedidos").is(":visible")){
      let gestionarBtnReenviarDuna = {
        "INGRESADO"   : () => $('#btn_reenviar_moto_duna').hide(),
        "PENDIENTE"   : () => $('#btn_reenviar_moto_duna').hide(),
        "RECIBIDO"    : () => $('#btn_reenviar_moto_duna').hide(),
        "POR ASIGNAR" : () => $('#btn_reenviar_moto_duna').show(),
        "ASIGNADO"    : () => $('#btn_reenviar_moto_duna').show(),
        "EN CAMINO"   : () => $('#btn_reenviar_moto_duna').show(),
        "ENTREGADO"   : () => $('#btn_reenviar_moto_duna').hide(),
        "ANULADO"     : () => $('#btn_reenviar_moto_duna').hide()
      };
      gestionarBtnReenviarDuna?.[estado]?.();
    }
  } else {
    $("#btn_reenviar_bringg,#btn_reenviar_moto_duna").hide();
  }
}

/**
 * Envia la petición al backend maxpoint para que gestione el envio al api pedidos ya
 * @param {*} agregador 
 */
function enviarConsultaSelectedAgregador(agregador,idFactura,medio,idApp) {
  // if (agregador === 'UBER') {
  //   if(uberDirectCashStatusMedio(codigo_app, medio) === 'false'){
  //       alertify.error("No se puede procesar Uber Direct Cash");
  //   };
  // }
  mostrarOcultarBotonesPolitica();
  $("#modalEnviarMotorizado").modal("toggle");
  fn_cargando(1); //solo aplica en pedidosya
  agregador = trim(agregador);

  let uberdirect = $(idApp).attr("uber_cash");
  if (idFactura && uberdirect !== 'true') {
    if (agregador === 'UBER') {
      uberDirectCashStatus(idApp, medio, function(callback) {
        console.log("Uber Direct Cash Pass:", callback.status);
        if (callback.msj != "MEDIO INCORRECTO" && callback.msj != "FORMA DE PAGO INCORRECTA" && callback.msj != "UBER DIRECT EFECTIVO: OFF") {
          if (callback.status) {
            console.log("La Orden " + idApp + " será procesada en Uber Direct Cash");
            agregador = 'UBER';
            setTimeout(function() {
              alertify.success(callback.msj + ". Orden " + idApp);
            }, 3000);
          } else {
            console.log("La Orden " + idApp + " no será procesada en Uber Direct Cash");
            agregador = medio;
            if (callback.msj != "ORDEN NO FACTURADA") {
              setTimeout(function() {
                alertify.error(callback.msj + ". Orden " + idApp);
              }, 3000);
            }
          }
        }
      });
    } else {
      uberDirectCashStatusMedio(idApp, medio);
      }

    verificarEstadoDragonTail(idApp, agregador);
  }

  setTimeout(() => {
    let metodosDelivery={
      "BRINGG" : () => {fn_cargando(0); fn_crearOrdenBringg(idFactura,medio, idApp) },
      "DUNA" : () => {fn_cargando(0); crearOrdenDuna(idFactura, idApp,medio);},
      "OTROS" : () => enviarConsultaOtros(agregador,idFactura)
    }

    metodosDelivery[agregador.trim()]
        ? metodosDelivery[agregador.trim()]()
        : metodosDelivery['OTROS']();
  }, 200);
}

/**
 * Envia la petición al backend maxpoint para que gestione el envio al api pedidos ya
 * @param {*} agregador 
 */
async function enviarConsultaOtros(agregador, codigo_factura) {
    var orden=$("#listado_pedido_app").find("li.focus");
	if(orden.length==0){
		orden = $("#listado_pendientes").children("li");
	}

	//en caso que no encuentre a veces sucede por que se recarga la pagina y mejor se abandona la accion
	if(orden.length==0){
		console.log("no existen opciones de provedor");
		var opciones_string="NO";
	}else{
		var opciones_string=$(orden).attr("opciones_proveedor");
	}

	if(!opciones_string)
		opciones_string="NO";

    opciones_string=decodeURIComponent(opciones_string);
    if(opciones_string=="NO"){
      enviarConsultaOtrosAjax(agregador, codigo_factura);
      return;
    }
  
    //caso contrario si muestra el modal
    let opciones = JSON.parse(opciones_string);
    var html = "";
    //var contador = 0;
    $("#btn_reenviar_bringg").text(`Enviar a`);
  
    $("#enviarMotorizadosLst").html(html);
    $("#enviarHeader").html(html);
    $("#enviarFooter").html(html);
    var header = "<h3>Opciones de " + agregador + ": </h3>";
    $("#enviarHeader").html(header);
  
    for (var clave in opciones) {
      if (opciones.hasOwnProperty(clave)) {
        var opcion = opciones[clave];
        html = "<div style='margin-top:5px !important; display: flex; justify-content: center; width: 100%;'><button class='btn btn-primary' id='medio_"+opcion+"' onclick='enviarConsultaOtrosAjax(\""+agregador+"\",\""+codigo_factura + ";" + clave +"\")'>"+opcion+"</button></div>";
        $("#enviarMotorizadosLst").append(html);
      }
    }

      var str = "<button class=\"btn_cerrar\" onClick=\"$('#modalEnviarMotorizado').modal('toggle');\">Cerrar</button>";
      $("#enviarFooter").append(str);
      setTimeout(() => {
        fn_cargando(0);
        $('#modalEnviarMotorizado').modal('show');
      }, 300);
}

/**
 * Envia la petición al backend maxpoint para que gestione el envio al api pedidos ya
 * @param {*} agregador 
 */
async function enviarConsultaOtrosAjax(agregador, codigo_factura) {
  $('#modalEnviarMotorizado').modal('hide');
  //var idBoton='#medio_'+agregador;
  send = { metodo: "enviarConsultaAgregadores" };
  send.medio = agregador;
  send.codigo_factura = codigo_factura;
  //send.idMotorizado = $('#medio_'+contador).data("idmotorizado");
  $.ajax({
    async: false,
    type: "POST",
    dataType: 'json',
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function( datos ) {
      fn_cargando(0);
      if(datos["error"]){
        const PROVEEDORES_SINWEBHOOK = ["DUNA","BRINGG"];
        reintentos=($("li[codigo_factura='"+codigo_factura+"']").attr("crea_duna")*-1);
        //si no tenemos error, mostramos algun mensaje por defecto
        if(!datos["response"] && datos["response"]==null){
          if(datos["response"]!==null && !PROVEEDORES_SINWEBHOOK.includes(agregador)
          && (reintentos_maximo_duna==0 //reintentos manuales activado
          || $("#btn_reenviar_bringg").text() == "Enviar a" //en caso que sea multiple proveedor
          || reintentos<=1 //primer intento despues de facturar
          || (!reintentos && codigo_factura) //esto se da cuando se factura
          || reintentos>reintentos_maximo_duna)
          ){
			cargarPedidosRecibidos();
            alertify.error("Error en la carga de datos " + agregador + ", vuelva a intentar");
            return;
          }else{
			cargarPedidosRecibidos();
            console.log("Error en la carga de datos " + agregador + ", vuelva a intentar");
            return;
          }
        }
        //caso contrario continuamos validando los otros errores
        response=datos["response"];
		if(!response["jsonResult"]){
          error="SIN RESPUESTA";
          code="SIN RESPUESTA";
        }else{
          if(response["jsonResult"]["error"])
            error=response["jsonResult"]["error"];
          else
            error="ERROR DESCONOCIDO"

            if(response["jsonResult"]["error"])
            code=response["jsonResult"]["code"];
          else
            code="CODIGO DESCONOCIDO"
        }
														 
        if(!PROVEEDORES_SINWEBHOOK.includes(agregador)
        && code.includes("PROVEEDOR_")  //codigo de error de proveedor
        && (reintentos_maximo_duna==0 //reintentos manuales activado
          || $("#btn_reenviar_bringg").text() == "Enviar a" //en caso que sea multiple proveedor
          || reintentos<=1 //primer intento despues de facturar
          || (!reintentos && codigo_factura) //esto se da cuando se factura
          || reintentos>reintentos_maximo_duna) //cuando se supera los reintentos y cambia a manual
        ){
          alertify.alert("Error " + agregador + ": " + error);
        }else if(reintentos_maximo_duna==0 || reintentos>=reintentos_maximo_duna){
          alertify.error("Error en la carga de datos " + agregador + ", vuelva a intentar");
        }
        console.log("Error en la carga de datos " + agregador + ", revisar error a continuación:");
        console.log(datos);
      }else{
        alertify.success("Carga de datos " + agregador + " correcta");
        $("#btn_reenviar_bringg").hide();
      }
      cargarPedidosRecibidos();
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log(jqXHR);
      console.log(textStatus);
      //console.log(errorThrown);
      cargarPedidosRecibidos();
      fn_cargando(0);
      alertify.error("Error en la carga de datos " + agregador + ", vuelva a intentar");
    }
  });
}

async function cargarListaMedios() {
  let medios       = localStorage.getItem('pedidos_medios');
  medios           = JSON.parse(medios)

  dibujarListaMedios(medios);
}

function dibujarListaMedios(datos) {
  var html ="";
  for (var i = 0; i < datos.length; i++) {
    if (datos[i]['codigo'] !== 'DEFAULT') {
      html += "<div class='medios' style='background-color:"+datos[i]['color_fila']+";color:"+datos[i]['color_texto']+"'>"+datos[i]['codigo']+"</div>";
    }
  }

  if (document.getElementById('agregators')) {
    document.getElementById('agregators').remove();
  }

  const container = document.querySelector('.medios_info');
  const class02 = container.querySelector('.jb-shortscroll-content');

  if (!class02) {
    const newClass02 = document.createElement('div');
    newClass02.id = 'agregators';
    newClass02.classList.add('jb-shortscroll-content');
    container.insertBefore(newClass02, container.firstChild);
    $('#agregators').html(html);
    $('#agregators').shortscroll();
    $('#agregators').height("25rem");
    $('#agregators').css("margin-top","1rem");
  } else {
    $('.medios_info').html(html);
    $('.medios_info').shortscroll();
    $('.medios_info').height("25rem");
    $('.medios_info').css("margin-top","1rem");
  }

}

function mostrarOcultarBotonesPedidosApp( option ) {

  if ( option === 1 ) {
    $("#filtro_app").show();
    $("#filtro_app_fin").hide();
    $("#transacciones_view").hide();
    $(".medios_info").show();
    $(".mesas_info").hide();
  } else if ( option === 0 ) {
    $("#filtro_app").hide();
    $("#filtro_app_fin").hide();
    $("#transacciones_view").show();
    $(".medios_info").hide();
    $(".mesas_info").show(); 
  } else if ( option === 2 ) {
    $("#filtro_app").hide();
    $("#filtro_app_fin").show();
    $("#transacciones_view").hide();
    $(".medios_info").show();
    $(".mesas_info").hide();
  }

}

var habilitarContenedorPedidosEntregados = function () {

  $("#btn_pedidos_ocultar_app").hide();
  
  if ( $("#config_servicio_pickup").val() == 1) {
    $("#btn_pedidos_pickup_app").show();
  } else {
    $("#btn_pedidos_pickup_app").hide();
  }

  $("#pnl_pickup").hide();
  
  $("#sltd_codigo_app").html('');
  $("#sltd_estado_app").html('');
  
  $("#cuado").hide();
  $("#cntMesas").show();
  $("#cnt_pedidos").show();
  $("#btn_pedidos_app").show();
  $("#btn_pedidos_entregados").hide();

  $("#btn_imprimir_error").hide();
  $("#btn_pedidos_error").show();
  $("#cnt_pedidos_error").hide();

  $("#btn_facturar").hide();
  $("#btn_ver").hide();
  $("#btn_transferir").hide();
  $("#btn_asignar").hide();
  $("#btn_cancelar").hide();
  $("#btn_anular").hide();

  $("#listaPedido").show();
  $("#listado_pedido_app").html("");
  $("#div_busqueda").show();
  $("#detalle_pedido").hide();
  $("#cboEstadoPedido").val("ENTREGADO");
  pararTimerSemaforo();
  cargarPedidosEntregados("ENTREGADO", "");
  cargarListaMedios();
  mostrarOcultarBotonesPedidosApp(2);
};

var cargarPedidosEntregados = function ( estado, busqueda = "" ) {

  var html = "";
  $("#listado_pedido_app").html(html);
  send = { metodo: "cargarPedidosEntregados" };
  send.estadoBusqueda = estado;
  send.parametroBusqueda = busqueda;
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function (datos) {
      if ( datos.registros > 0 ) {
        for (var i = 0; i < datos.registros; i++) {

          var icon = '';
          if (datos[i]["estado"] === "ANULADO") {
            icon = '<i class="material-icons">cancel</i>';
          } else if (datos[i]["estado"] === "ENTREGADO") {
            icon = '<i class="material-icons">done</i>';
          }

          if (localStorage.getItem("tiempos") === null || typeof localStorage.getItem("tiempos") === "undefined") {
            let tiempoStorage = {
              tiempoEntrega: 0, tiempoDespacho: 0, tiempoTotal: 0, tiempoPendiente: 0, tiempoRecibido: 0,
              tiempoPorAsignar: 0, tiempoAsignado: 0, tiempoEnCamino: 0, codigo_app: null,
            }
            let pedidosTiempoStorage = [];
            pedidosTiempoStorage.push(tiempoStorage);
            localStorage.setItem("tiempos", JSON.stringify(pedidosTiempoStorage));
            cargarLista(datos);
          }

          let pedidosTiempoStorageGuardado=JSON.parse(localStorage.getItem("tiempos"));

          let buscaPosicionEnStorageGuardado = pedidosTiempoStorageGuardado.findIndex((storage)=> {          
            return storage.codigo_app === datos[i]["codigo_app"]        
          });

          let posicionStorageGuardado = buscaPosicionEnStorageGuardado;

          if (posicionStorageGuardado >= 0) {

            tiempoDespacho = pedidosTiempoStorageGuardado[posicionStorageGuardado].tiempoDespacho;
            tiempoTotalPedido = pedidosTiempoStorageGuardado[posicionStorageGuardado].tiempoTotal;
            tiempoMotorizado = pedidosTiempoStorageGuardado[posicionStorageGuardado].tiempoEntrega;
          } else {
            tiempoDespacho = 0;
            tiempoTotalPedido = 0;
            tiempoMotorizado = 0;
          }

          html = '<li class="datos_app" style="background-color:' + datos[i]["color_fila"] + ';color:' + datos[i]["color_texto"] + '" ' +
              'id="' +
              datos[i]["codigo_app"] +
              '" codigo_fac="' +
              datos[i]["codigo"] +
              '" documento="' +
              datos[i]["documento"] +
              '" formapago="' +
              datos[i]["forma_pago"] +
              '" medio="' +
              datos[i]["medio"] +
              '" observacion="' +
              datos[i]["observacion"] +
              '" adicional="' +
              datos[i]["datos_envio"] +
              '" direccion="' +
              datos[i]["direccion_despacho"] +
              '" total="' +
              datos[i]["total"] +
              '" fecha="' +
              datos[i]["fecha"] +
              '" telefono="' +
              datos[i]["telefono"] +
              '" cliente="' +
              datos[i]["cliente"] +
              '"  idMotorizado="' +
              datos[i]["idMotorizado"] +
              '"  motorizado="' +
              datos[i]["motorizado"] +
              '" motorizado_telefono="' +
              datos[i]["motorizado_telefono"] +
              '" estado="' +
              datos[i]["estado"] +
              '" cambio_estado="' +
              datos[i]["cambio_estado"] +
              '" codigo_externo="' +
              datos[i]["codigo_externo"] +
              '" asigna_duna="' +
              datos[i]["asigna_duna"] +
              '" crea_duna="' +
              datos[i]["crea_duna"] +
              '" duna_reintentos="' +
              datos[i]["duna_reintentos"] +
              '" retira_efectivo="' +
              datos[i]["retira_efectivo"] +
              '" opciones_proveedor="' +
              datos[i]["opciones_proveedor"] +
              '" codigo_factura="' +
              datos[i]["codigo_factura"] +
              '" fecha_estado="' +
              datos[i]["fecha_estado"].date +
              '" user_estado="' +
              datos[i]["user_estado"] +
              '" mediopago="' +
              datos[i]["mediopago"] +
              '" motivo_anulacion="' +
              datos[i]["motivo_anulacion"] +
              '" observacion_factura="' +
              datos[i]["observacion_factura"] +
              '" notificar_medio="' +
              datos[i]["notificar_medio"] +
              '" notificar_listo="' +
              datos[i]["notificar_listo"] +          
              '" onclick="seleccionarEntregado(this)"><div class="lista_medios">' +
              datos[i]["medio"] +
              '</div><div class="codigo_app"><b>' +
              datos[i]["codigo"] +
              '</b></div><div class="cliente_app">' +
              datos[i]["cliente"] +
              '</div><div class="lista_motorizado">' +
              datos[i]["motorizado"] +
              '</div><div class="lista_estado">' +
              icon +
              '</div><div class="lista_semaforo">' +
              '' +
              '</div><div class="lista_tiempo">' +
              ' - ' +
              '</div><div class="lista_tiempo">' +
              formatoHorasMinutos(tiempoDespacho) +
              '</div><div class="lista_tiempo">' +
              formatoHorasMinutos(tiempoMotorizado) +
              '</div><div class="lista_tiempo">' +
              formatoHorasMinutos(tiempoTotalPedido) +
              "</div></li>";
          $("#listado_pedido_app").append(html);
        }
      } else {
        $("#listado_pedido_app").append(
            '<li class="datos_app"><div>No existen pedidos.</div></li>'
        );
      }
    },
  });
};

var seleccionarEntregado = function ( id ) {
  $("#listado_pedido_app li").removeClass("focus");
  $(id).addClass("focus");
  this.filaSeleccionada = $("#listado_pedido_app").find("li.focus").attr("id");
  verificarBotones();
};

/**
 * Funciones para Reporte de Pickup
 */
var mostrarPedidosPickup = function() {
    $("#cuado").hide();
    $("#pnl_pickup").show();
    $("#tabs-1").load('reporte_pickup.php');
    $("#tabs-2").load('reporte_pickup_central.php');
    $("#btn_pedidos_app").hide();
    $("#btn_pedidos_ocultar_app").show();
  $("#cntMesas").hide();
  $("#cnt_pedidos").hide();
  $("#btn_pedidos_entregados").hide();
  $("#btn_pedidos_pickup_app").hide();
  mostrarBotonTransferenciaPickup();
}

var ocultarPedidosPickup = function() {  
  $("#btn_pedidos_ocultar_app").hide();
  if ( $("#config_servicio_pickup").val() == 1) {
    $("#btn_pedidos_pickup_app").show();
  } else {
    $("#btn_pedidos_pickup_app").hide();
  }
  $("#pnl_pickup").hide();
  $("#mesas").show();
  $("#cntMesas").show();
  $("#detalle_plu").hide();
  $("#cnt_pedidos").hide();
  $("#btn_pedidos_error").show();
  $("#btn_imprimir_error").hide();
  $("#cnt_pedidos_error").hide();

  if ($("#config_servicio_domicilio").val() == 1) {
    $("#btn_pedidos_app").show();
    $("#btn_pedidos_entregados").show();
  } else {
    $("#btn_pedidos_app").hide();
    $("#btn_pedidos_entregados").hide();
  }

  $("#btn_ver").hide();
  $("#btn_en_camino").hide();
  $("#btn_asignar").hide();
  $("#btn_confirmar").hide();
  $("#btn_entregado").hide();
  $("#btn_cancelar").hide();
  $("#btn_facturar").hide();
  $("#btn_transferir").hide();
  $("#btn_anular").hide();
  $("#btn_reenviar_bringg,#btn_reenviar_moto_duna").hide();
  $("#cuado").show();
  location.reload();
  mostrarBotonTransferenciaPickup();
}

var transferir_pedido_pickup = function() { 
  var codigo_app_pickup = $(".seleccionRegistro").attr("codigo_app_pickup");
  if(codigo_app_pickup == undefined) {
    alertify.error("Seleccione un pedido de pickup");
    return;
  }
  var estado_pedido_pickup = $(".seleccionRegistro").attr("estado_pedido_pickup");
  var nombre_pickup = $(".seleccionRegistro").attr("nombre_pickup");
  //console.log("estado->" +estado_pedido_pickup);
  if (estado_pedido_pickup == 'Ingresado' || estado_pedido_pickup == 'Preparando') {
  var forma_pago = $(".seleccionRegistro").attr("forma_pago_pickup");
  alertify.confirm("¿Quieres transferir el pedido de <b>" + nombre_pickup +  "</b> # " + codigo_app_pickup + "?",
  function (e) {
    if (!e) {
      return;
    }
    $('#mdl_rdn_pdd_crgnd').show();
      var html = "";
      $("#lstLocalesTransferenciaPickup").html(html);

      send = { metodo: "cargarLocalesPickup" };
      $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../ordenpedido/config_transferencia.php",
        data: send,
        success: function (datos) {
          //console.log(datos);
          if (datos.length > 0) {
            
            for (var i = 0; i < datos.length; i++) {
                //console.log(datos[i]);
                html = '<li class="datos_locales" idRestaurante="' + datos[i]["store_id"] + '" codigo="' + datos[i]["store"] + 
                  '" onclick="cargarMotivosTransferenciaPickup('+datos[i]["store_id"]+', \''+datos[i]["store"]+'\', \''+datos[i]["store"]+'\', \''+codigo_app_pickup+'\', \''+forma_pago+'\')"><div class="lst_registro"><b>' + datos[i]["store"] + ' ' + datos[i]["address"] +'</b></div></li>';
                $("#lstLocalesTransferenciaPickup").append(html);
            }

          } else {
            $("#lstLocalesTransferenciaPickup").html('<li class="datos_motorizado"><div class="datos_locales">No existen locales a los que puedas hacer transferencia.</div></li>');
            $('#mdl_rdn_pdd_crgnd').hide();
          }

          $('#mdl_rdn_pdd_crgnd').hide();
          $('#modalLocalesTransferenciaPickup').modal('toggle');

        },
        error: function (data) {
          console.log("Error: ", data);
          $('#mdl_rdn_pdd_crgnd').hide();
        }
      });
  });
  } else {
    //enviar no se puede transferir
    alertify.error("No se puede transferir un pedido con estado: <b>" + estado_pedido_pickup + "</b>")
    return;
  }
  
};

let notificarBoton = 0;

var notificarPedido = function() {
  
  $('#btn_notificar').html("📤 Enviando...");

  var transaccion = $("#listado_pedido_app").find("li.focus");

  var codigo = $(transaccion).attr("id");
  var medio = $(transaccion).attr("medio");

  let send = { metodo: "notificarPedido" };
  send.codigo = codigo;
  send.medio = medio;

  if (!codigo || !medio) {
      alertify.alert( "Error al notificar pedido." );
      $('#btn_notificar').html("El pedido está listo");
      return;
  }

  if (notificarBoton == 1){
    console.log("espere un momento, enviando notificacion...")
    return;
  }

  notificarBoton = 1;

  $.ajax({
    async: true,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function (datos) {

      $('#btn_notificar').html("El pedido está listo");
      notificarBoton = 0;

      if (datos.error){
          alertify.error("Error al notificar pedido:  <b>" + codigo + "</b>.");
      }else{
          $('#btn_notificar').hide();
          cargarPedidosEntregados("ENTREGADO", "");
          alertify.success("Notificado con exito:  <b>" + codigo + "</b>.");
      }

    },
    error: function (data) {
      console.log("Error: ", data);
      notificarBoton = 0;
      $('#btn_notificar').html("El pedido está listo");

    }
  });

};

var transferir_pedido = function() {
  
  
  var transaccion = $("#listado_pedido_app").find("li.focus");
  var codigo = $(transaccion).attr("id");
  var codigo_app = $(transaccion).attr("codigo_fac");
  var medio = $(transaccion).attr("medio");
  if (existeAgregador(medio)){
    alertify.alert( "No puedes hacer transferencia de este pedido." );
    return;
  }
  alertify.confirm("¿Quieres transferir el pedido #" + codigo_app + "?",
    function (e) {
      if (!e) {
        return;
      }

      $('#mdl_rdn_pdd_crgnd').show();
      var html = "";
      $("#lstLocalesTransferencia").html(html);

      send = { metodo: "cargarLocales" };
      $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../ordenpedido/config_transferencia.php",
        data: send,
        success: function (datos) {
          //console.log(datos);
          if (datos.registros > 0) {

            for (var i = 0; i < datos.registros; i++) {
                html = '<li class="datos_locales" idRestaurante="' + datos[i]["id"] + '" codigo="' + datos[i]["codigo"] + 
                  '" onclick="cargarMotivosTransferencia('+datos[i]["id"]+', \''+datos[i]["codigo"]+'\', \''+datos[i]["descripcion"]+'\', \''+codigo+'\', \''+medio+'\')"><div class="lst_registro"><b>' + datos[i]["descripcion"] +'</b></div></li>';
                $("#lstLocalesTransferencia").append(html);
            }

          } else {
            $("#lstLocalesTransferencia").html('<li class="datos_motorizado"><div class="datos_locales">No existen locales a los que puedas hacer transferencia.</div></li>');
            $('#mdl_rdn_pdd_crgnd').hide();
          }

          $('#mdl_rdn_pdd_crgnd').hide();
          $('#modalLocalesTransferencia').modal('toggle');

        },
        error: function (data) {
          console.log("Error: ", data);
          $('#mdl_rdn_pdd_crgnd').hide();
        }
      });

  });

};

var cargarMotivosTransferencia = function( idlocal, codigolocal, local, codigo, medio ) {
  
  alertify.confirm("El local a transferir pedido: " + local,
    function (e) {
      if (!e) {
        return;
      }

      $('#mdl_rdn_pdd_crgnd').show();
      var html = "";
      $("#lstMotivosTransferencia").html(html);

      send = { metodo: "cargarMotivos" };
      $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../ordenpedido/config_transferencia.php",
        data: send,
        success: function (datos) {
          //console.log(datos);
          if (datos.registros > 0) {

            for (var i = 0; i < datos.registros; i++) {
                html = '<li class="datos_locales" idMotivo="' + datos[i]["id"] + '" motivo="' + datos[i]["descripcion"] + 
                  '" onclick="iniciarTransferencia('+idlocal+', \''+codigolocal+'\', \''+local+'\', \''+datos[i]["id"]+'\', \''+datos[i]["descripcion"]+'\', \''+codigo+'\', \''+medio+'\')"><div class="lst_registro"><b>' + datos[i]["descripcion"] +'</b></div></li>';
                $("#lstMotivosTransferencia").append(html);
            }

          } else {
            $("#lstMotivosTransferencia").html('<li class="datos_locales"><div class="datos_locales">No existen motivos configurados.</div></li>');
            $('#mdl_rdn_pdd_crgnd').hide();
          }

          $('#mdl_rdn_pdd_crgnd').hide();
          $('#modalLocalesTransferencia').modal('toggle');
          $('#modalMotivoTransferencia').modal('toggle');

        },
        error: function (data) {
          console.log("Error: ", data);
          $('#mdl_rdn_pdd_crgnd').hide();
        }
      });

    }
  );

};

var cargarMotivosTransferenciaPickup = function( idlocal, codigolocal, local, codigo, forma_pago ) {
  
  alertify.confirm("El local a transferir pedido: " + local,
    function (e) {
      if (!e) {
        return;
      }

      $('#mdl_rdn_pdd_crgnd').show();
      var html = "";
      $("#lstMotivosTransferenciaPickup").html(html);

      send = { metodo: "cargarMotivosPickup" };
      $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../ordenpedido/config_transferencia.php",
        data: send,
        success: function (datos) {
          //console.log(datos);
          if (datos.registros > 0) {

            for (var i = 0; i < datos.registros; i++) {
                html = '<li class="datos_locales" idMotivo="' + datos[i]["id"] + '" motivo="' + datos[i]["descripcion"] + 
                  '" onclick="iniciarTransferenciaPickup('+idlocal+', \''+codigolocal+'\', \''+local+'\', \''+datos[i]["id"]+'\', \''+datos[i]["descripcion"]+'\', \''+codigo+'\', \''+forma_pago+'\')"><div class="lst_registro"><b>' + datos[i]["descripcion"] +'</b></div></li>';
                $("#lstMotivosTransferenciaPickup").append(html);
            }

          } else {
            $("#lstMotivosTransferenciaPickup").html('<li class="datos_locales"><div class="datos_locales">No existen motivos configurados.</div></li>');
            $('#mdl_rdn_pdd_crgnd').hide();
          }

          $('#mdl_rdn_pdd_crgnd').hide();
          $('#modalLocalesTransferenciaPickup').modal('toggle');
          $('#modalMotivoTransferenciaPickup').modal('toggle');

        },
        error: function (data) {
          console.log("Error: ", data);
          $('#mdl_rdn_pdd_crgnd').hide();
        }
      });

    }
  );

};

var iniciarTransferenciaPickup = function( idlocal, codigolocal, local, idmotivo, motivo, codigo, forma_pago ) {

  //var cod = $("#listado_pedido_app").find("li.focus").attr("codigo_fac");

  alertify.confirm("El pedido #"+codigo+" se transferirá al local: " + local + " por motivo: " + motivo + " <b>¿Deseas continuar?</b>",
    function (e) {
      if (!e) {
        return;
      }

      $('#mdl_rdn_pdd_crgnd').show();
      $('#modalMotivoTransferenciaPickup').modal('toggle');

      send = { metodo: "transferirPedidoPickup" };
      send.idLocal = idlocal;
      send.codigoLocal = codigolocal;
      send.local = local;
      send.idMotivo = idmotivo;
      send.motivo = motivo;
      send.codigo = codigo;
      send.forma_pago = forma_pago;
      $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../ordenpedido/config_transferencia.php",
        data: send,
        success: function (datos) {
          console.log("TRANSFERENCIA");
          console.log(datos);    
          if (datos.codigo == 1) {
            $('#mdl_rdn_pdd_crgnd').hide();
            alertify.alert( datos.mensaje );            
            //mostrarPedidosPickup();
            //location.reload();
          } else {
            $('#mdl_rdn_pdd_crgnd').hide();
            alertify.alert(" " + datos.mensaje);
            //mostrarPedidosPickup();
            //location.reload();
          }
        },
        error: function (data) {
          console.log("Error: ", data);
          $('#mdl_rdn_pdd_crgnd').hide();
        }
      });

    }
  );

};

function mostrarBotonTransferenciaPickup() {
  let displayPanelPickup = $("#pnl_pickup").css("display");
  console.log("DISPLAY ->" + displayPanelPickup);
  if (displayPanelPickup != 'none')  {
    send = { metodo: "mostrarBotonTransferenciaPickup" };
        $.ajax({
          async: true,
          type: "POST",
          dataType: "json",
          contentType: "application/x-www-form-urlencoded",
          url: "../ordenpedido/config_transferencia.php",
          data: send,
          success: function (datos) {
            console.log(datos);
              if (datos[0].respuesta == 1) {
                $("#btn_transferir_pickup").show();
              } else {
                $("#btn_transferir_pickup").hide();
              }  
            }, error: function (data) {
              console.log("Error: ", data);
              $('#mdl_rdn_pdd_crgnd').hide();
            }
          });
        } 
}


var iniciarTransferencia = function( idlocal, codigolocal, local, idmotivo, motivo, codigo, medio ) {

  var cod = $("#listado_pedido_app").find("li.focus").attr("codigo_fac");

  alertify.confirm("El pedido #"+cod+" se transferirá al local: " + local + " por motivo: " + motivo + " <b>¿Deseas continuar?</b>",
    function (e) {
      if (!e) {
        return;
      }

      $('#mdl_rdn_pdd_crgnd').show();
      $('#modalMotivoTransferencia').modal('toggle');

      send = { metodo: "transferirPedido" };
      send.idLocal = idlocal;
      send.codigoLocal = codigolocal;
      send.local = local;
      send.idMotivo = idmotivo;
      send.motivo = motivo;
      send.codigo = codigo;
      send.medio = medio;
      $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../ordenpedido/config_transferencia.php",
        data: send,
        success: function (datos) {
    
          //console.log(datos);
    
          if (datos.codigo === 1) {
            cargarPedidosRecibidos();
            $("#btn_ver").hide();
            $("#btn_confirmar").hide();
            $("#btn_transferir").hide();
            $('#mdl_rdn_pdd_crgnd').hide();
            alertify.alert( datos.mensaje );
          } else {
            cargarPedidosRecibidos();
            $('#mdl_rdn_pdd_crgnd').hide();
            alertify.alert(" " + datos.mensaje);
          }
    
        },
        error: function (data) {
          cargarPedidosRecibidos();
          console.log("Error: ", data);
          $('#mdl_rdn_pdd_crgnd').hide();
        }
      });

    }
  );

};

var cargarPedidosTransferidos = function ( estado, busqueda = "" ) {

  $("#sltd_codigo_app").html('');
  $("#sltd_estado_app").html('');

  var html = "";
  $("#listado_pedido_app").html(html);
  send = { metodo: "cargarPedidosTransferidos" };
  send.estadoBusqueda = estado;
  send.parametroBusqueda = busqueda;
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function (datos) {

      if ( datos.registros > 0 ) {
        for (var i = 0; i < datos.registros; i++) {

          var icon = '';
          if ( datos[i]["tipo_transferencia"] === "ENVIADA" ) {
            icon = '<i class="material-icons">north_east</i>';
          } else if ( datos[i]["tipo_transferencia"] === "RECIBIDA" ) {
            icon = '<i class="material-icons">south_west</i>';
          }

          html = '<li class="datos_app" style="background-color:'+datos[i]["color_fila"]+';color:'+datos[i]["color_texto"]+'" ' +
          'id="' +
          datos[i]["codigo_app"] +
          '" codigo_fac="' +
          datos[i]["codigo"] +
          '" documento="' +
          datos[i]["documento"] +
          '" formapago="' +
          datos[i]["forma_pago"] +
          '" medio="' +
          datos[i]["medio"] +
          '" observacion="' +
          datos[i]["observacion"] +
          '" adicional="' +
          datos[i]["datos_envio"] +
          '" direccion="' +
          datos[i]["direccion_despacho"] +
          '" total="' +
          datos[i]["total"] +
          '" fecha="' +
          datos[i]["fecha"] +
          '" telefono="' +
          datos[i]["telefono"] +
          '" cliente="' +
          datos[i]["cliente"] +
          '"  idMotorizado="' +
          datos[i]["idMotorizado"] +
          '"  motorizado="' +
          datos[i]["motorizado"] +
          '" motorizado_telefono="' +
          datos[i]["motorizado_telefono"] +
          '" estado="' +
          datos[i]["estado"] +
          '" cambio_estado="' +
          datos[i]["cambio_estado"] +
          '" codigo_externo="' +
          datos[i]["codigo_externo"] +
          '" asigna_duna="' +
          datos[i]["asigna_duna"] +
          '" crea_duna="' +
          datos[i]["crea_duna"] +
          '" duna_reintentos="' +
          datos[i]["duna_reintentos"] +
          '" retira_efectivo="' +
          datos[i]["retira_efectivo"] +
          '" opciones_proveedor="' +
          datos[i]["opciones_proveedor"] +
          '" codigo_factura="' +
          datos[i]["codigo_factura"] +

          '" tipo_transferencia="' + datos[i]["tipo_transferencia"] +
          '" idLocal="' + datos[i]["idLocal"] +
          '" codigoLocal="' + datos[i]["codigoLocal"] +
          '" local="' + datos[i]["local"] +
          '" usuarioTransfiere="' + datos[i]["usuarioTransfiere"] +
          '" motivo="' + datos[i]["motivo"] +

          '" onclick="seleccionarPedidoTransferido(this)"><div class="lista_medios">' +
          datos[i]["medio"] +
          '</div><div class="codigo_app"><b>' +
          datos[i]["codigo"] +
          '</b></div><div class="cliente_app">' +
          datos[i]["cliente"] +
          '</div><div class="lista_motorizado">' +
          datos[i]["motorizado"] +
          '</div><div class="lista_estado">'+
          icon + '</div><div class="lista_semaforo"></div><div class="lista_tiempo"></div></li>';
          $("#listado_pedido_app").append(html);
        }
      } else {
        $("#listado_pedido_app").append(
          '<li class="datos_app"><div>No existen pedidos.</div></li>'
        );
      }
    },
  });
};

var seleccionarPedidoTransferido = function ( id ) {

  $("#listado_pedido_app li").removeClass("focus");
  $(id).addClass("focus");

  this.filaSeleccionada = $("#listado_pedido_app").find("li.focus").attr("id");

  var codigo = $(id).attr("codigo");
  var codigo_fac = $(id).attr("codigo_fac");
  var estado = $(id).attr("tipo_transferencia");

  $("#sltd_codigo_app").html('<b>#'+codigo_fac+'</b>');
  $("#sltd_estado_app").html('<b>'+estado+'</b>');

  $("#btn_en_camino").hide();
  $("#btn_entregado").hide();
  $("#btn_asignar").hide();
  $("#btn_confirmar").hide();
  $("#btn_anular").hide();
  $("#btn_desasignar").hide();
  $("#btn_ver").show();
  $("#btn_transferir").hide();
};

var cargar_transferencia_salida = function () {
  cargarPedidosTransferidos('TRANSFERIDO', '');
};

var cargar_transferencia_entrada = function () {
  cargarPedidosTransferidos('RECIBIDA', '');
};

var pintar_datos_transferencia = function ( id ) {

  var tipo_transferencia = $(id).attr("tipo_transferencia");

  if( tipo_transferencia ) {

    var local  = $(id).attr("local");
    var usuario  = $(id).attr("usuarioTransfiere");

    $("#tipo_transferencia").html( tipo_transferencia );
    $("#local_trans").html( local );
    $("#usuario_trans").html( usuario );

    $("#sprdr_trans").show();
    $("#cnt_transferencia").show();
    $("#cnt_detalle_transferencia").show();
  } else {
    $("#sprdr_trans").hide();
    $("#cnt_transferencia").hide();
    $("#cnt_detalle_transferencia").hide();
  }

};
var agregadores = function () {

  send = { metodo: "agregadores"};
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_app.php",
    data: send,
    success: function (datos) {
    agregadoresArray=datos;
    },
  });
};
var existeAgregador =function(medio){
 for(var x=0;x<agregadoresArray.length;x++){
   if (agregadoresArray[x]['descripcion']==medio)
   return 1;
 }
 return 0;
};

function obtenerMediosEstacion(accion) {
  let medios = localStorage.getItem('pedidos_medios_filtra');
  return JSON.parse(medios)
}



function facturarTransaccion (idFactura, cedula, nombre, telefono, email) {

  console.log('cedulaa transaccion',cedula);
  var codigo = $("#listado_pedido_app").find("li.focus").attr("id");
  var medio = $("#listado_pedido_app").find("li.focus").attr("medio");
  var codigo_externo = $("#listado_pedido_app").find("li.focus").attr("codigo_externo");
  var proveedorTracking = $("#proveedor_tracking").val();
  var cambio_estados_automatico = $("#cambio_estados_automatico").val();
  var cambio_estado = $("#" + codigo).attr("cambio_estado");
  var medioProcesado = medio.trim().toUpperCase();
  if (idFactura != '') {
	enviarTransaccionQPMDespachoYParcial(idFactura);
    var send;
    var datosCliente = { "datosCliente": 1 };
    var transaccion = idFactura;
    var objectResultado = new Array();
    send = datosCliente;
    send.transaccion = transaccion;
    console.log("ENTRO A ID FACTURA VACIO");
    $.ajax({
      async: false,
      type: "POST",
      dataType: "json",
      contentType: "application/x-www-form-urlencoded",
      url: "../facturacion/config_plugThem.php",
      data: send,
      success: function (datos) {
        if (datos.str > 0) {              
          validaLoginPlugThem(datos[0]["idFactura"],datos[0]["cedula"],datos[0]["nombre"],datos[0]["telefono"],datos[0]["email"]);
        }
      }
    });
  }
  //validaLoginPlugThem(idFactura, cedula, nombre, telefono, email);
  //consulta si productos movistar stan presentes 
  // CAMBIO ESTADOS TRADE
  if(cambio_estado == "SI" && (cambio_estados_automatico == 'NO' || cambio_estados_automatico == 0)) {
    //if ( proveedorTracking === "TRADE") {
      //KFC: ASIGNADO = TRADE: Asignado
      cambioEstadoTrade( codigo, "Por Asignar", medio );
    //}
  }
  //BRINGG - NOTIFICACION CREACION DE PEDIDO   
  console.log("Bringg: ", cambio_estados_automatico);
  if( cambio_estados_automatico === 'SI' && (!codigo_externo || codigo_externo == "null")) {
    send = { metodo: "facturaPorPedido" };
    send.codigo_app = codigo;
    $.ajax({
      async: false,
      type: "POST",
      dataType: "json",
      contentType: "application/x-www-form-urlencoded",
      url: "../ordenpedido/config_app.php",
      data: send,
      success: function (datos) {
        console.log(datos);
        
        if (datos[0]["estado"] == "1") {
          let factura = datos[0]["factura"];
          enviarPedidoDeliveryPorMedio( medio,factura, codigo);
        } else {
          console.log("Error bringg: ", datos[0]["mensaje"]);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        cargarPedidosRecibidos();
      },
    });
  } else {
    verificarEstadoDragonTail(codigo, medio);
  }
  //cargarPedidosRecibidos();
  //Ejecucion automatica de pasos
  if(existeAgregador(medioProcesado)) {
    $("#btn_facturar").hide();
    $("#btn_ver").show();
    $("#btn_cancelar").hide();
    $("#listaPedido").show();
    $("#div_busqueda").show();
    $("#detalle_pedido").hide();
    $("#btn_anular").hide();
    $("#btn_asignar").hide();
    $("#sltd_codigo_app").html('');
    $("#sltd_estado_app").html('');
  } else {
    $("#btn_facturar").hide();
    $("#btn_ver").show();
    $("#btn_cancelar").hide();
    $("#listaPedido").show();
    $("#div_busqueda").show();
    $("#detalle_pedido").hide();
    $("#btn_anular").show();
    $("#btn_asignar").show();
    setTimeout(function(){  $('#'+this.filaSeleccionada).addClass("focus"); }, 600);
  }
  //mostrarOcultarBotonesPolitica();
}

//función que actualiza todas las ordenes de pedido de la estación a "odp_total=0" para poder retomarlas
function fn_actualizarOrdenesPedido(cod_estacion,cod_usuario,cod_periodo){

  var send;
  var actualizaOdp = { "actualizaTodasOdp": 1 };
  send = actualizaOdp;
  send.opcion = '2';
  send.id_odp = '0';
  send.estacion = cod_estacion;
  send.usuario = cod_usuario;
  send.periodo = cod_periodo;    
  $.ajax({
    async: false,
    type: "POST",
    dataType: "json",
    contentType: "application/x-www-form-urlencoded",
    url: "../ordenpedido/config_UserMesas.php",
    data: send,
    success: function (datos) {
      if (datos.str > 0) {              
        console.log(datos[0]["mensaje"])          
      }
    }
  });
}

/**Funciones para generar codigo e imprimir Agregadores Pickup */
var obtenerActualizarCodigo = function (action) {
  if (action == 1){
    send = { metodo: "obtenerActualizarCodigo"};
    send.action = action
    $.ajax({
      async: false,
      type: "POST",
      dataType: "json",
      contentType: "application/x-www-form-urlencoded",
      url: "../ordenpedido/config_app.php",
      data: send,
      success: function (datos) { 
        console.log(datos)
        if (datos['codigo'].length <= 4){
          $('#numero_rest').val(datos['codigo']);  
        }else if(datos['aplica_politicas'] == 'SI'){
          alertify.error(datos['codigo']);
        }else{
          document.getElementById("codigo-confirmacion-agregadores").style.display = "none";
        } 
      },
      error: function(datos){
        console.log(datos)
      }
    });
  }
  if (action == 2){
      alertify.confirm("El codigo del local se va a cambiar ¿Deseas continuar?</b>",
      function (e) {
        if (!e) {
          return;
        }

      send = { metodo: "obtenerActualizarCodigo"};
      send.action = action
      $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: "../ordenpedido/config_app.php",
        data: send,
        success: function (datos) {
          $('#numero_rest').val(datos['codigo']);
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.log(jqXHR);
          console.log(textStatus);
          console.log(errorThrown);
        }
      });
    });
  }
}

var imprimirCodigo = function (action) {
  let apiImpresion = getConfiguracionesApiImpresion();
  send.codigoConfirmacionDelivery =  $('#numero_rest').val(); 
  if (apiImpresion.api_impresion_tienda_aplica == 1 && apiImpresion.api_impresion_estacion_aplica == 1){      
    fn_cargando(1);
    let result = new apiServicioImpresion('codigo_confirmacion_delivery',null, null,send);
    let imprime = result["imprime"];
    let mensaje = result["mensaje"];
    if (!imprime) {
        alertify.success('Imprimiendo Código de Confirmación...');
    }
  }else{
    send = { metodo: "imprimirCodigo", codigo: $('#numero_rest').val() };
    send.action = action

    $.ajax({
      async: false,
      type: "POST",
      dataType: "json",
      contentType: "application/x-www-form-urlencoded",
      url: "../ordenpedido/config_app.php",
      data: send,
      success: function (datos) {        
        alertify.success("Codigo impreso");
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log(jqXHR);
        console.log(textStatus);
        console.log(errorThrown);
      }
    });
  }
}

function validarInput() {
  var input = document.getElementById("cantidad");
  var valor = parseInt(input.value);

  if (valor <= 0) {
    input.value = "";
  }
}

function uberDirectCashStatus(codigo, medio, callback) {
  fn_cargando(1);
  send = {
    codigo_app: codigo,
    medio: medio,
  }

  $.ajax({
    type: "POST",
    url: "../resources/module/domicilio/uber-cash/orders.php",
    data: send,
    dataType: "json",
    async: false,
    success: function(data){
      callback(data);
      fn_cargando();
    },
    error: function(jqXHR, exception) {
      callback(false);
      alertify.error("error "+jqXHR.responseText);
    }
  });
}

function uberDirectCashStatusMedio(codigo, medio) {
  fn_cargando(1);
  send = {
    codigo_app: codigo,
    medio: medio,
  }

  $.ajax({
    type: "POST",
    url: "../resources/module/domicilio/uber-cash/getOrderMedio.php",
    data: send,
    dataType: "json",
    async: false,
    success: function(data){
      fn_cargando();
    },
    error: function(jqXHR, exception) {
      alertify.error("Anulado el proceso de Uber Direct Efectivo.");
    }
  });
}