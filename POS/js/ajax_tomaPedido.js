// -------------------------------------------------
//  FECHA ULTIMA MODIFICACION	: 10:33 2/3/2017
//  USUARIO QUE MODIFICO	:  Mychael Castro
//  DECRIPCION ULTIMO CAMBIO	: Controlar drag and drop  con el zoom + y - del navegador.
//  -------------------------------------------------

var lc_idCategoria = 0;
$(document).ready(function () {
  $("#mdl_rdn_pdd_crgnd").show();
    $("#menuCategoria").hide();
    $("#menuProducto").hide();
    $("#menucadena").prop("disabled", "disabled");
    fn_cargarMenu();
    $("#listadoCategorias").busquedaLista();
    $("#listadoProductos").busquedaLista();
    $("#menucadena").change(function () {
        var opcion = this.value;
        if (opcion != "0") {
            var idCadena = $("#codigoCadena").val();
            fn_cargarCaracteristica(idCadena, opcion);
        } else {
            fn_limpiarDatos();
        }
    });
  fn_listaCategoria($("#codigoCadena").val(), "");
  $("#mdl_rdn_pdd_crgnd").hide();
});

function fn_limpiarDatos() {
    $("#listadoProductos ul").empty();
    $("#menuProducto").empty();
    $("#menuProducto").hide();
    $("#menuCategoria").empty();
    $("#menuCategoria").hide();
}

function fn_cargarMenu() {
  Accion = "M";
  send = { cargarMenu: 1 };
    send.accion = Accion;
    send.cdn_id = $("#codigoCadena").val();
    send.menu_id = 0;
    send.mag_id = 0;
    $.getJSON("../adminordenpedido/config_tomaPedido.php", send, function (datos) {
        if (datos.str > 0) {
            $("#menucadena").prop("disabled", false);
            $("#menucadena").html("");
      $("#menucadena").html(
        "<option selected value='0'>- Seleccionar Menu -</option>"
      );
            for (i = 0; i < datos.str; i++) {
        $("#menucadena").append(
          "<option value=" +
            datos[i]["menu_id"] +
            " idclasificacion='" +
            datos[i]["IDClasificacion"] +
            "'>" +
            datos[i]["menu_Nombre"] +
            "</option>"
        );
            }
            $("#menucadena").val(0);
        } else {
            alertify.error("No existen men&uacute;s para esta cadena");
            $("#menucadena").empty();
            $("#menucadena").prop("disabled", "disabled");
            fn_limpiarDatos();
        }
    });
}

function fn_ubicacionCategoria(id_cat, total) {
  Accion = "UC";
  if (id_cat.indexOf("btn_c") >= 0) {
    var id = id_cat.replace(new RegExp("btn_c", "g"), "");
    } else {
        var id = id_cat;
    }
    var elemento = $("#btn_c" + id + "");
    var posicion = elemento.position();
    var posicionTotal = posicion.left + "," + posicion.top;
  send = { actualizarCategoria: 1 };
    send.accion = Accion;
    send.id = id;
    send.posicionTotal = total;//posicionTotal;
    send.magp_id = 0;
    send.cat_id = 0;
  send.bandera = "";
    send.usr_id = $("#sess_usr_id").val();
    send.acMenuId = $("#menuId").val();
    $.getJSON("../adminordenpedido/config_tomaPedido.php", send, function (datos) {
        if (datos.str > 0) {
        } else {
            alertify.error("No se pudo actualizar la ubicaci&oacute;n de la categor&iacute;a");
        }
    });
}

/*funcion para hacer que el elemento encaje en el div !importante*/
function handleCardDrop(event, ui) {
  if ($(this).hasClass("taken")) {
    ui.draggable.draggable("option", "revert", true);
    $(this).addClass("taken");
    } else {
    ui.draggable.position({ of: $(this), my: "left top", at: "left top" });
    ui.draggable.draggable("option", "revert", false);
    }
}

function handleDropRemove(event, ui) {
  $(this).removeClass("taken");
}


function getZoom() {
  var ovflwTmp = $("html").css("overflow");
  $("html").css("overflow", "scroll");

    var viewportwidth;
    // the more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight 
  if (typeof window.innerWidth != "undefined") {
        viewportwidth = window.innerWidth;
  } else if (
    typeof document.documentElement != "undefined" &&
    typeof document.documentElement.clientWidth != "undefined" &&
            document.documentElement.clientWidth != 0) {
        // IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document) 
        viewportwidth = document.documentElement.clientWidth;
    } else {
        // older versions of IE 
    viewportwidth = document.getElementsByTagName("body")[0].clientWidth;
    }

    var windW = $(window).width();
    var scrollBarW = viewportwidth - windW;

    if (!scrollBarW)
        return 1;

  $("html").css("overflow", ovflwTmp);

  return 15 / scrollBarW;
}



function fn_validaPosicionCategoria(idCatt, total, opValida) {


    scroly = $("#menuCategoria").scrollTop();
    var posicionTotal = $("#posStart").val();
    var separar = posicionTotal.split(",");
    leftt = separar[0];
    arrriba = separar[1];
    arribaa = parseFloat(arriba) + parseFloat(scroly);

  if (opValida == "M") {
        magpidvalida = 0;
    if (idCatt.indexOf("btn_c") >= 0) {
      var id = idCatt.replace(new RegExp("btn_c", "g"), "");
        } else {
            var id = id_cat;
        }
  } else if (opValida == "P") {
        magpidvalida = $("#codigoAgrupacionProducto").val();
    if (idCatt.indexOf("btn_p") >= 0) {
      var id = idCatt.replace(new RegExp("btn_p", "g"), "");
        } else {
            var id = id_cat;
        }



    }

  send = { validaPosicionCategoria: 1 };
    send.opcionValida = opValida;
    send.cadenaId = $("#codigoCadena").val();
    send.menuID = $("#menuId").val();
    send.magOrden = total;
    send.magIDvalida = /*id;//*/$("#codigoCategoria").val();
    send.codigoProducto = magpidvalida;
    $.getJSON("../adminordenpedido/config_tomaPedido.php", send, function (datos) {
    if (datos.valida == "ocupado") {
      if (opValida == "M") {
        $("#btn_c" + id).css({ left: leftt + "px", top: arrriba + "px" });
      } else if (opValida == "P") {
        $("#btn_p" + id).css({ left: leftt + "px", top: arrriba + "px" });
            }
        } else {
      if (opValida == "M") {
                fn_ubicacionCategoria(idCatt, total);
      } else if (opValida == "P") {
                fn_ubicacionProducto(idCatt, total);
            }
        }
    });
}
// AL mover las categorias de parte posterior.
function fn_cargarCaracteristica(cdn_id, menu_id) {
  t = "t";
    fn_listaProductos(cdn_id, menu_id, t);
  $("#mdl_rdn_pdd_crgnd").show();
  Accion = "A";
    $("#menuId").val(menu_id);
    fn_limpiarDatos();
    $("#menuCategoria").show();
  send = { cargarCategoria: 1 };
    send.accion = Accion;
    send.cdn_id = cdn_id;
    send.menu_id = menu_id;
    send.mag_id = 0;
    $.getJSON("../adminordenpedido/config_tomaPedido.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
        var posicionTotal = "" + datos[i]["mag_orden"] + "";
                var separar = posicionTotal.split(",");
                left = separar[0];
                arriba = separar[1];
        html =
          "<div class='cat botonDrop' id='btn_c" +
          datos[i]["mag_id"] +
          "' onclick='fn_activarProducto(\"" +
          datos[i]["mag_id"] +
          "\")' style='background-color:" +
          datos[i]["mag_color"] +
          "; color:" +
          datos[i]["mag_colortexto"] +
          "; position:absolute; left:" +
          left +
          "px; top:" +
          arriba +
          "px; width:120px; height:60px; text-align:center; text-align:center;' >" +
          datos[i]["mag_descripcion"] +
          "<input class='delete' type='button' value='X'	onclick='fn_confirmaEliminarCategoria(\"" +
          datos[i]["mag_id"] +
          "\")' /></div>";
                $("#menuCategoria").append(html);
        $("#btn_c" + datos[i]["mag_id"] + "").draggable({
                    containment: "#menuCategoria",
                    scroll: true,
                    start: function (event, ui) {
                        scrolPstart = $("#menuCategoria").scrollTop();
                        left = $(this).position().left;
                        topp = $(this).position().top;
                        topp = topp + scrolPstart;
                        //   total = left + "," + topp;
                        total = Math.round(left) + "," + Math.round(topp);
                        $("#posStart").val(total);
                    },
                    drag: function (event, ui) {},
                    stop: function (event, ui) {

                        scrol = $("#menuCategoria").scrollTop();
                        posi = $(this).position();
                        left = Math.round($(this).position().left) ;
                        topp = Math.round($(this).position().top) ;
                        topp = topp + scrol;
                        total = left + "," + topp;
                        

                        var id_cat = $(event.target).attr("id");
            op = "M";
                        fn_validaPosicionCategoria(id_cat, total, op);
                    }
                });
            }
      $("#mdl_rdn_pdd_crgnd").hide();
        } else {
      $("#mdl_rdn_pdd_crgnd").hide();
            alertify.error("No hay botones configurados para esta cadena");
        }
        for (i = 0; i < 100; i++) {
      $(
        "<div class='hcat'  id='cat" +
          i +
          "' style='border-style: dashed; border-width: 1px;'></div>"
      )
        .appendTo("#menuCategoria")
        .droppable({
          accept: ".cat",
          hoverClass: "hovered",
                drop: function (event, ui) {
            ui.draggable.position({
              of: $(this),
              my: "left top",
              at: "left top"
            });
            ui.draggable.draggable("option", "revert", false);
                }
            });
        }
        fn_hoverCat();
    });
}

function  handleDropEvent(event, ui) {
  if ($(this).hasClass("occupied")) {
    ui.draggable.draggable("option", "revert", true);
        return false;
    }
  ui.draggable.position({ of: $(this), my: "left top", at: "left top" });
  s;
}

function validateDropzones() {
    $(".hcat").each(function () {
        console.log($(this).position().left + " " + $(this).position().top);
    });
}

function fn_confirmaEliminarCategoria(id) {
    alertify.confirm("Est&aacute; seguro(a) que quiere eliminar " + $("#btn_c" + id).text() + "?", function (e) {
        if (e) {
            fn_eliminarCategoria(id);
        } else {
            cadena = $("#codigoCadena").val();
            menu = $("#menuId").val();
        }
    });
}
function fn_confirmaEliminarProducto(idPro, mag_id) {
    alertify.confirm("Est&aacute; seguro(a) que quiere eliminar " + $("#btn_p" + idPro).text() + "?", function (e) {
        if (e) {
            fn_eliminarProducto(idPro, mag_id);
        } else {

        }
    });
}

function fn_eliminarCategoria(id_cat) {
  Accion = "EC";
  send = { eliminarCategoria: 1 };
    send.accion = Accion;
    send.mag_id = id_cat;
  send.mag_orden = "";
    send.magp_id = 0;
    send.cat_id = 0;
  send.bandera = "";
    send.usr_id = $("#sess_usr_id").val();
    send.eMenId = $("#menuId").val();
    $.getJSON("../adminordenpedido/config_tomaPedido.php", send, function (datos) {
        if (datos.str > 0) {
            $("#btn_c" + id_cat).remove();
            alertify.set({labels: {
                    ok: "Si",
                    cancel: "No"
                }});
            alertify.confirm("Desea mantener los productos de esta secci&oacute;n?", function (e) {
                if (e) {
            fn_actualizaMenuAgrupacion(id_cat, "si");
                } else {
            fn_actualizaMenuAgrupacion(id_cat, "no");
                }
            });
        } else {
            alertify.error("Hubo un error al momento de eliminar la categor&iacute;a");
        }
    });
}

function fn_actualizaMenuAgrupacion(id, bandera) {
  Accion = "UG";
  send = { actualizaMenuAgrupacion: 1 };
    send.accion = Accion;
    send.mag_id = 0;
  send.mag_orden = "";
    send.magp_id = 0;
    send.mag_idd = id;
    send.banderamantiene = bandera;
    send.usr_id = $("#sess_usr_id").val();
    send.actMaMenuId = $("#menuId").val();
    $.getJSON("../adminordenpedido/config_tomaPedido.php", send, function (datos) {
        fn_activarProducto(id);
        alertify.success("Operaci&oacute;n realizada con &eacute;xito..");
    });
}
// mover y soltar un plus
function fn_activarProducto(id) {
  $("#desc_cat").text(
    "Categoría: " + document.getElementById("btn_c" + id).textContent
  );

  $("#mdl_rdn_pdd_crgnd").show();
  Accion = "B";
    $("#menuProducto").empty();
    $("#menuProducto").show();
    $("#codigoCategoria").val(id);
  send = { cargarProducto: 1 };
    send.accion = Accion;
    send.cdn_id = $("#codigoCadena").val();
    send.menu_id = $("#menuId").val();
    send.mag_id = id;
    $.getJSON("../adminordenpedido/config_tomaPedido.php", send, function (datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
        var posicionTotal = "" + datos[i]["magp_orden"] + "";
                var separar = posicionTotal.split(",");
                left = separar[0];
                arriba = separar[1];
        html =
          "<div codigo='" +
          datos[i]["magp_id"] +
          "' class='men botonDrop' id='btn_p" +
          datos[i]["magp_id"] +
          "')' style='background-color:" +
          datos[i]["magp_color"] +
          "; position:absolute; color:" +
          datos[i]["magp_colortexto"] +
          "; left:" +
          left +
          "px; top:" +
          arriba +
          "px; width:120px; height:60px; text-align:center;'>" +
          datos[i]["magp_desc_impresion"] +
          "<input class='delete' type='button' value='X'	onclick='fn_confirmaEliminarProducto(\"" +
          datos[i]["magp_id"] +
          '","' +
          id +
          "\")' /></div>";
                $("#menuProducto").append(html);
        $("#btn_p" + datos[i]["magp_id"] + "").draggable({
                    containment: "#menuProducto",
                    scroll: true,
                    start: function (event, ui) {
                        scrolPstart = $("#menuProducto").scrollTop();
                        left = Math.round($(this).position().left);
                        topp = Math.round($(this).position().top);
                        topp = topp + scrolPstart;
                        total = left + "," + topp;

                        $("#posStart").val(total);
            $("#codigoAgrupacionProducto").val($(this).attr("codigo"));
                    },
                    stop: function (event, ui) {

                        scrolP = $("#menuProducto").scrollTop();
                        posi = $(this).position();
                        left = Math.round($(this).position().left);
                        topp = Math.round($(this).position().top);

                        topp = topp + scrolP;
                        total = left + "," + topp;
                        var id_prod = $(event.target).attr("id");
            opc = "P";
                        fn_validaPosicionCategoria(id_prod, total, opc);
                    }
                });
            }
      $("#mdl_rdn_pdd_crgnd").hide();
        } else {
      $("#mdl_rdn_pdd_crgnd").hide();
            alertify.error("No hay botones creados en esta categor&iacute;a");
        }
        for (i = 0; i < 200; i++) {
      $(
        "<div id='men" +
          i +
          "' class='hpro' style='border-style: dashed; border-width: 1px;'></div>"
      )
        .appendTo("#menuProducto")
        .droppable({
          accept: ".men",
          hoverClass: "hovered",
                drop: function (event, ui) {
            ui.draggable.position({
              of: $(this),
              my: "left top",
              at: "left top"
            });
            ui.draggable.draggable("option", "revert", false);
                }
            });
        }
        fn_hoverPro();
    });
}

function fn_listaCategoria(cdn_id, menu_id) {
  Accion = "C";
    $("#listadoCategorias ul").empty();
  send = { listaCategoria: 1 };
    send.accion = Accion;
    send.cdn_id = cdn_id;
    send.menu_id = menu_id;
    send.mag_id = 0;
    $.getJSON("../adminordenpedido/config_tomaPedido.php", send, function (datos) {
        if (datos.str > 0) {
            //lista de categorias
            for (i = 0; i < datos.str; i++) {
        html =
          "<li class='cat' id='cat_" +
          datos[i]["mag_id"] +
          "'><a style='background: " +
          datos[i]["mag_color"] +
          "; color: " +
          datos[i]["mag_colortexto"] +
          "'>" +
          datos[i]["mag_descripcion"] +
          "</a></li>";
                $("#listadoCategorias ul").append(html);
            }
            $("#listadoCategorias li").draggable({
        cursor: "help",
                appendTo: "#menuCategoria",
                containment: "#menuCategoria",
                scroll: true,
                helper: function () {
                    return $(this).clone().css("pointer-events", "none").show();
                },
                stop: function (event, ui) {
                    id = $("#hoverIDcategoria").val();
                    scrolProdu = $("#menuCategoria").scrollTop();
                    left = $("#" + id).position().left;
                    topp = $("#" + id).position().top;
                    topp = topp + scrolProdu;

                    total = Math.round(left) + "," + Math.round(topp);
                    $(this).animate({
                        top: "0px",
                        left: "0px"
                    });
                    var id_cat = $(event.target).attr("id");
          var id = id_cat.replace(new RegExp("cat_", "g"), "");
                    if ($("#btn_c" + id + "").length == 0) {
            $(this).addClass("cat");
                        var separador = $(event.target).text();
                        var nombre = separador;
                        $(this).find(".placeholder").remove();
            Accion = "AC";
            send = { autollenarCategoria: 1 };
                        send.accion = Accion;
                        send.id = id;
                        send.mag_orden = total;
                        send.magp_id = 0;
                        send.cat_id = 0;
            send.bandera = "";
                        send.usr_id = /*16;*/$("#sess_usr_id").val();
                        send.idDlMenu = $("#menuId").val();
                        $.getJSON("../adminordenpedido/config_tomaPedido.php", send, function (datos) {
                            if (datos.str > 0) {
                  var posicionTotal = "" + datos[0]["mag_orden"] + "";
                                var separar = posicionTotal.split(",");
                  left = Math.round(separar[0]);
                  arriba = Math.round(separar[1]);
                  html =
                    "<div id='btn_c" +
                    id +
                    "' class='cat botonDrop' style='background:" +
                    datos[0]["mag_color"] +
                    "; color:" +
                    datos[0]["mag_colortexto"] +
                    "; position:absolute; left:" +
                    left +
                    "px; top:" +
                    arriba +
                    "px; width:120px; height:60px; text-align:center;' onclick='fn_activarProducto(\"" +
                    datos[0]["mag_id"] +
                    "\")'><input class='delete' type='button' value='X'	onclick='fn_confirmaEliminarCategoria(\"" +
                    datos[0]["mag_id"] +
                    "\")' />" +
                    nombre +
                    "</div>";
                                $("#menuCategoria").append(html);
                                $("#btn_c" + id + "").draggable({
                                    containment: "#menuCategoria",
                                    scroll: true,
                                    stop: function (event, ui) {
                                        scrol = $("#menuCategoria").scrollTop();
                                        scrol = parseInt(scrol);
                                        posi = $(this).position();
                                        left = Math.round($(this).position().left);
                      left = Math.round(left);
                                        topp = Math.round($(this).position().top);
                      topp = Math.round(topp);
                                        topp = topp + scrol;
                      total = Math.round(left) + "," + Math.round(topp);

                                        var id_cat = $(event.target).attr("id");
                                        fn_ubicacionCategoria(id_cat, total);
                                    }
                                });
                                fn_dropCategoria();


                                fn_cargarCaracteristica(cdn_id, $("#menucadena").val());

                            } else {
                  alertify.error("No existe la categor&iacute;a seleccionada");
                            }
                        });
                    } else {
            alertify.error("Ya existe la categor&iacute;a seleccionada");
                    }
                }
            });
        }
    });
}

function fn_dropCategoria() {
    $("#tabs-1 li").selectable();
    $("#menuCategoria").droppable({
        activeClass: "ui-state-default",
        hoverClass: "ui-state-hover",
        accept: ":not(.ui-sortable-helper)"
    }).sortable({
        items: "li:not(.placeholder)",
        sort: function () {}
    });
}

function fn_listaProductos(cdn_id, menu_id, opcion) {
    var html;
  var idClasificacion = $("#menucadena :selected").attr("idclasificacion");
  Accion = "P";
    $("#listadoProductos ul").empty();
  send = { listaProductos: 1 };
    send.accion = Accion;
    send.cdn_id = cdn_id;
    send.menu_id = menu_id;
    send.mag_id = 0;
    send.canal = opcion;
    send.idcla = idClasificacion;
    $.getJSON("../adminordenpedido/config_tomaPedido.php", send, function (datos) {
        if (datos.str > 0) {
            for (var i = 0; i < datos.str; i++) {
        html =
          "<li class='men' id='prod_" +
          datos[i]["magp_id"] +
          "'><a style='background: " +
          datos[i]["magp_color"] +
          "; color: " +
          datos[i]["magp_colortexto"] +
          "'>" +
          datos[i]["magp_desc_impresion"] +
          "</a></li>";
                $("#listadoProductos ul").append(html);


                $("#listadoProductos li").draggable({
                    appendTo: "#menuProducto",
                    containment: "#menuProducto",
                    scroll: true,
                    helper: function () {
                        return $(this).clone().css("pointer-events", "none").show();
                    },
                    start: function (event, ui) {
                        scrolPstart = $("#listadoProductos").scrollTop();
                        left = $(this).position().left;
                        topp = $(this).position().top;
                        topp = topp + scrolPstart;
                        total = left + "," + topp;
                        $("#startLiProducto").val(total);
                    },
                    stop: function (event, ui) {


                        id = $("#hoverIDproducto").val();
                        scrolProdu = $("#menuProducto").scrollTop();
                        left = $("#" + id).position().left;
                        topp = $("#" + id).position().top;
                        topp = topp + scrolProdu;

                        total = Math.round(left) + "," + Math.round(topp);


                        $(this).animate({
                            top: "0px",
                            left: "0px"});
                        var id_prod = $(event.target).attr("id");
            var id = id_prod.replace(new RegExp("prod_", "g"), "");
                        if ($("#btn_p" + id + "").length == 0) {
                            var separador = $(event.target).text();
                            var nombre = separador;


                            // fn_validaPosicionCategoria( "btn_c"+ $("#codigoCategoria").val()  , total, 'P');

              $(this)
                .find(".placeholder")
                .remove();
                            IdMagProducto = $("#codigoCategoria").val();
              Accion = "AP";
              send = { autollenarProducto: 1 };
                            send.accion = Accion;
                            send.mag_id = $("#codigoCategoria").val();
                            send.mag_orden = total;
                            send.id = id;
                            send.cat_id = 0;
              send.bandera = "";
                            send.usr_id = $("#sess_usr_id").val();
                            send.autoMeId = $("#menuId").val();
                            $.getJSON("../adminordenpedido/config_tomaPedido.php", send, function (datos) {
                                if (datos.str > 0) {
                    var posicionTotal = "" + datos[0]["magp_orden"] + "";
                                    var separar = posicionTotal.split(",");
                                    left = separar[0];
                                    arriba = separar[1];
                    html =
                      "<div id='btn_p" +
                      id +
                      "' class='men botonDrop ui-draggable' style='background:" +
                      datos[0]["magp_color"] +
                      "; color:" +
                      datos[0]["magp_colortexto"] +
                      "; position:absolute; left:" +
                      left +
                      "px; top:" +
                      arriba +
                      "px; width:120px; height:60px; text-align:center; vertical-align:middle;'><input class='delete' type='button' value='X'	onclick='fn_confirmaEliminarProducto(\"" +
                      id +
                      '","' +
                      IdMagProducto +
                      "\")' />" +
                      nombre +
                      "</div>";

                                    $("#menuProducto").append(html);
                                    $("#btn_p" + id + "").draggable({
                                        containment: "#menuProducto",
                                        scroll: true,
                                        stop: function (event, ui) {
                                            scrolP = $("#menuProducto").scrollTop();
                                            scrolP = parseInt(scrolP);
                                            posi = $(this).position();
                                            left = $(this).position().left;
                        left = Math.round(left);
                                            topp = $(this).position().top;
                                            topp = Math.round( topp);
                                            topp = topp + scrolP;
                                            total = left + "," + topp;
                                            alert(total);
                                            
                                            var id_prod = $(event.target).attr("id");
                                            fn_ubicacionProducto(id_prod, total);
                                        }
                                    });
                                    fn_dropProducto();

                                    fn_activarProducto($("#codigoCategoria").val());
                                } else {
                    alertify.error("No existe el producto seleccionado");
                                }
                            });
                        } else {
              alertify.error("Ya existe el producto seleccionado");
                        }
                    }
                });
            }
        } else {
      html =
        "<p>No existen productos de la clasificaci&oacute;n seleccionada.</p>";
            $("#listadoProductos").html(html);
        }
    });
}

function fn_dropProducto() {
    $("#tabs-2 li").selectable();
    $("#menuProducto").droppable({
        activeClass: "ui-state-default",
        hoverClass: "ui-state-hover",
        accept: ":not(.ui-sortable-helper)"
    }).sortable({
        items: "li:not(.placeholder)",
        sort: function () {}
    });
}





function fn_ubicacionProducto(id_prod, total) {
  Accion = "UP";
  if (id_prod.indexOf("btn_p") >= 0) {
    var id = id_prod.replace(new RegExp("btn_p", "g"), "");
    } else {
        var id = id_prod;
    }
    var elemento = $("#btn_p" + id + "");
    var posicion = elemento.position();
    var posicionTotal = posicion.left + "," + posicion.top;
    var separar = total.split(",");

  send = { actualizarProducto: 1 };
    send.accion = Accion;
    send.mag_id = $("#codigoCategoria").val();
    send.posicionTotal = total;
    send.id = id;
    send.cat_id = 0;
  send.bandera = "";
    send.usr_id = $("#sess_usr_id").val();
    send.aMenId = $("#menuId").val();
  send.matriz =
    "[" + parseInt(separar[0]) / 120 + "," + parseInt(separar[1]) / 60 + "]";

    $.getJSON("../adminordenpedido/config_tomaPedido.php", send, function (datos) {
        if (datos.str > 0) {



            //alertify.alert("Se actualiz&oacute; la ubicaci&oacute;n del producto");
        } else {
            alertify.error("No se pudo actualizar la ubicaci&oacute;n del producto");
        }
    });
}

function fn_eliminarProducto(id_prod, mag_id) {
  Accion = "EP";
  send = { eliminarProducto: 1 };
    send.accion = Accion;
    send.mag_id = mag_id;
  send.mag_orden = "";
    send.magp_id = id_prod;
    send.cat_id = 0;
  send.bandera = "";
    send.usr_id = $("#sess_usr_id").val();
    send.eMenIdP = $("#menuId").val();
    $.getJSON("../adminordenpedido/config_tomaPedido.php", send, function (datos) {
        if (datos.str > 0) {
      alertify.success("Se elimin&oacute; correctamente el producto");
            $("#btn_p" + id_prod).remove();
        } else {
            alertify.error("Hubo un error al momento de eliminar el producto");
        }
    });
}

jQuery.fn.busquedaLista = function () {
    input = $('<input type="text" style="margin-top: 4px;" class="form-control"/>');
    $(input).attr("placeholder", "Buscar");
    $(this).prepend(input);
    var list = $(this);
    $(input).change(function () {
        var filter = $(this).val();
        if (filter) {
            $("li", $(list)).hide();
            $("li:Contains(" + filter + ")", $(list)).show();
        } else {
            $("li", $(list)).show();
        }
        return false;
    }).keyup(function () {
        $(this).change();
    });
    jQuery.expr[":"].Contains = jQuery.expr.createPseudo(function (arg) {
        return function (elem) {
      return (
        jQuery(elem)
          .text()
          .toUpperCase()
          .indexOf(arg.toUpperCase()) >= 0
      );
        };
    });

};

function fn_hoverPro() {
  $(".hpro")
    .on("mouseenter", function() {
      $("#hoverIDproducto").val($(this).attr("id"));
      $(this).attr("estado", "ocupado");
    })
    .mouseleave(function() {
      $(this).attr("estado", "libre");
    });
}

function fn_hoverCat() {
  $(".hcat")
    .on("mouseenter", function() {
      $("#hoverIDcategoria").val($(this).attr("id"));
      $(this).attr("estado", "ocupado");
    })
    .mouseleave(function() {
      $(this).attr("estado", "libre");
    });
}

function fn_seleccionaProducto(idProdu) {
  console.log("idProdu");
}