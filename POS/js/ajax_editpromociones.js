////////////////////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Eduardo Valencia         /////////////////////////////////////////////
///////DESCRIPCION: Creación de nueva Promoción /////////////////
///////TABLAS INVOLUCRADAS: Promociones,  Beneficios_Promociones, //////////////////////////
///////Plus_Requeridos_Promociones, Formas_Pago_Promociones, PromocionesColeccionDeDatos ///
///////FECHA CREACION: 07-08-2018              /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 01-09-2018   /////////////////////////////////////////////
///////USUARIO QUE MODIFICO: Eduardo Valencia  /////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Corrección de funcionamiento para añadir Restaurantes /////
////////////////////////////////////////////////////////////////////////////////////////////

var plus_agregados_descuento = [];
var plus_agregados_productos_requeridos = [];
var plus_agregados_restaurantes_requeridos = [];
var beneficiosProductosCupon = '';
var RequeridosProductosCupon = '';
var requeridosRestaurantesCupon = '';
var canalesPromocion = '';
var verificaAddCiudad = 0;
var verificaAddRegion = 0;
var categoriasPromocion = '';

var $botonAplicarPromocion = $("#btnagregarPromocion");

/**
 * Función que se lanza siempre al abrir la página.
 * Permite tomar las acciones y valores necesarios para una creación nueva de una promoción
 * @param NO RECIBE PARÁMETROS
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/

$(document).ready(function() {

    var cdn_id = $("#cadenaId").val();
    var Id_Promociones = $("#Id_Promociones").val();
    //var Concatenador_beneficios = $("#Concatenador_beneficios").val();
    var Concatenador_beneficios = "AND";
    fn_cargarClasificacion();
    fn_cargarLocalesNoAsignadosDescuento(Id_Promociones, cdn_id);
    fn_cargarPlusDescuento(Id_Promociones);
    fn_cargarCiudades(0, cdn_id);
    fn_cargarRegion(0, cdn_id);
    fn_cargarRestaurantesPromocion(Id_Promociones, cdn_id);
    fn_cargarPlusBeneficiosPromocion(Id_Promociones);
    fn_cargarCanal(Id_Promociones);


    $("#select-plus-cadena").chosen({ width: "100%", search_contains: true });
    $("#select-plus-descuento").chosen({ width: "100%", search_contains: true });


    document.getElementById("cantidadProductoRequerido").value = 1;
    $("#IdsProductosRequeridosPromocion").chosen({ width: "100%", search_contains: true });
    document.getElementById("cantidadProductoBeneficio").value = 1;
    $(".btn-agregar-plu").on("click", function() {
        if (document.getElementById('anadirProductos').checked || document.getElementById('anadirDescuentos').checked) {
            //Concatenador_beneficios = $("#Concatenador_beneficios").val();
            Concatenador_beneficios = "AND";
            var cantidadPLUBeneficio = $("#cantidadProductoBeneficio").val();
            if (document.getElementById('anadirProductos').checked) {
                var valorPLU = $("#select-plus-cadena").val();
                var $elementoPLU = $($("#select-plus-cadena option[value='" + valorPLU + "']")[0]);
                if (valorPLU == 0) {
                    fn_alerta("<b>Alerta!</b> Por favor escoge un producto.", "danger");
                    return false;
                }
                if (cantidadPLUBeneficio == '' || cantidadPLUBeneficio == 0) {
                    fn_alerta("<b>Alerta!</b> Por favor ingrese la cantidad del Producto.", "danger");
                    return false;
                }

                if (Concatenador_beneficios == 0 || Concatenador_beneficios == '') {
                    fn_alerta("<b>Alerta!</b> Por favor escoja un vinculador de Productos.", "danger");
                    return false;
                }

                if (plus_agregados_descuento.hasOwnProperty(valorPLU)) {
                    if (plus_agregados_descuento[valorPLU].agregado == 1) {
                        fn_alerta("<b>Alerta!</b> El PLU ya se encuentra asignado.", "danger");
                        return false;
                    }
                }

                var esAutoconsumo = esFacturacionAutoconsumo();
                var valoresNuevoElemento = {
                    agregado: 1,
                    plu_id: $elementoPLU.data("plu_id"),
                    plu_descripcion: $elementoPLU.data("plu_descripcion"),
                    plu_num_plu: $elementoPLU.data("plu_num_plu"),
                    IDDescuento: '',
                    cantidad_plu: cantidadPLUBeneficio,
                    concatenador_plu: Concatenador_beneficios,
                    Tipo_aplica: "PRODUCTOS",
                    productoAutoconsumo: esAutoconsumo
                };
                //var nuevoElemento = crearElementoListaPlusDescuento(valoresNuevoElemento);
                var nuevoElemento = crearElementoListaBeneficios(valoresNuevoElemento);
                plus_agregados_descuento[valoresNuevoElemento.plu_id] = valoresNuevoElemento;
                $("#listado-plus-agregardos-descuento").append(nuevoElemento);
                return true;

            } else {
                var valorPLU1 = $("#select-plus-descuento").val();
                var $elementoPLU1 = $($("#select-plus-descuento option[value='" + valorPLU1 + "']")[0]);
                if (valorPLU1 == 0) {
                    fn_alerta("<b>Alerta!</b> Por favor escoge un descuento.", "danger");
                    return false;
                }

                if (Concatenador_beneficios == 0 || Concatenador_beneficios == '') {
                    fn_alerta("<b>Alerta!</b> Por favor escoja un vinculador de Productos.", "danger");
                    return false;
                }

                if (plus_agregados_descuento.hasOwnProperty(valorPLU1)) {
                    if (plus_agregados_descuento[valorPLU1].agregado == 1) {
                        fn_alerta("<b>Alerta!</b> El Descuento ya se encuentra asignado.", "danger");
                        return false;
                    }
                }

                var valoresNuevoElemento = {
                    agregado: 1,
                    plu_id: 0000,
                    plu_descripcion: $elementoPLU1.data("plu_descripcion"),
                    plu_num_plu: $elementoPLU1.data("plu_num_plu"),
                    IDDescuento: valorPLU1,
                    cantidad_plu: 0,
                    concatenador_plu: Concatenador_beneficios,
                    Tipo_aplica: 'FACTURA',
                    productoAutoconsumo: false
                };

                //valoresNuevoElemento.tipo_aplica = $.fn_TipoAplicaDescuento(valoresNuevoElemento.IDDescuento);

                // var nuevoElemento = crearElementoListaPlusDescuento(valoresNuevoElemento);
                var nuevoElemento = crearElementoListaBeneficios(valoresNuevoElemento);
                plus_agregados_descuento[valoresNuevoElemento.IDDescuento] = valoresNuevoElemento;
                $("#listado-plus-agregardos-descuento").append(nuevoElemento);
                return true;
            }
        } else {
            alertify.alert('Seleccione un tipo de beneficio.');
            document.getElementById('Codigo_externo').focus();
        }
    });

    $.fn_TipoAplicaDescuento = function(IDDescuento) {
        var value = 'FACTURA'
        send = { "TipoAplicaDescuento": "1" };
        send.IDDescuento = IDDescuento;
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            "Accept": "application/json",
            contentType: "application/x-www-form-urlencoded; charset=utf-8; application/json",
            url: "../adminDescuentos/config_descuentos.php",
            data: send,
            success: function(datos) {
                if (datos.str > 0) {
                    value = datos[0].apld_descripcion;
                }
            }
        });
        return value;
    }

    $("#btnQuitarTodosRestaurantes").on("click", function() {
        $("#contenedor_lst_rest_activos").hide({
            complete: function() {
                $.when($("#lst_rst_dscto button").trigger("click")).done(function() {
                    $("#contenedor_lst_rest_activos").show();
                });
            }
        });
    });

    $('#horarioDesde').datetimepicker({
        stepping: 10,
        format: "HH:mm"
    });

    $('#horarioHasta').datetimepicker({
        useCurrent: false,
        stepping: 10,
        format: "HH:mm"
    });
    /*
        $("#horarioDesde").on("dp.change", function (e) {
            $('#horarioDesde').data("DateTimePicker").minDate(e.date);
        });

        $("#horarioHasta").on("dp.change", function (e) {
            $('#horarioHasta').data("DateTimePicker").maxDate(e.date);
        });
    */

    $('#Activo_Hasta').datetimepicker();
    $('#Activo_desde').datetimepicker({
        useCurrent: true //Important! See issue #1075
    });

    $("#Activo_desde").on("dp.change", function(e) {
        $('#Activo_desde').data("DateTimePicker").minDate(e.date);
    });

    $("#Activo_Hasta").on("dp.change", function(e) {
        $('#Activo_Hasta').data("DateTimePicker").minDate(e.date);
    });


    $("#back").on("click", function() {
        $("#incluirPagina").load('../promociones/adminPromociones.php');
    });

    $("#buscar_Restaurantes").on('keyup', function(e) {
        filtrarLocales();
    });

    $("#categoriasRestricciones").on("click", ".checkCategoria", function() {
        var $this = $(this);
        var idCategoria = $this.attr("data-idcategoria");
        var divCategoria = $this.attr("data-divcategoria");
        var estadoCheckBox = $this.is(":checked");
        var x = document.getElementById(divCategoria);
        if (estadoCheckBox) {
            x.style.display = "block";
            categoriasPromocion = categoriasPromocion.concat(idCategoria, ',');
        } else {
            categoriasPromocion = categoriasPromocion.replace(idCategoria + ',', '');
            x.style.display = "none";
        }
    });

    $(".switch-bts").bootstrapSwitch({});

    $("#listado-plus-agregardos-descuento").on("click", ".btn-quitar-beneficio", function(evt) {
        var $liPadre = $(evt.target).closest("li");
        //var datosElemento=$liPadre.data();
        $liPadre.css({ "text-decoration": "line-through" }).data("agregado", 0);
        console.log("247 document ready", $liPadre.data());
    });
});

/**
 * Función fn_cargarClasificacion()
 * Convoca al procedimiento almacenado [promociones].[Clasificacion] para mostar las clasificaciones existentes en la base de datos
 * NO RETORNA VALORES
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function fn_cargarClasificacion() {
    var send;
    send = { "cargarClasificacion": 1 };
    send.resultado = 0;
    $.getJSON("../adminDescuentos/config_descuentos.php", send, function(datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                var item = crearItemClasificacion(datos[i]);
                $("#idsCanal").append(item);
            }
        }
    });
}

function crearItemClasificacion(datosClasificacion) {
    var $itemActual = $("<option id='" + datosClasificacion['IDClasificacion'] + "' value='" + datosClasificacion['IDClasificacion'] + "'>" + datosClasificacion['cla_Nombre'] + "</option>");
    return $itemActual;
}

/**
 * Función fn_cargarPlusDescuento.
 * Ejecuta el procedimiento alamcenado [Descuentos].[Descuentos] para retornar los productos disponibles por cadena
 * Verificar si el entero de cadena es un valor correcto
 * @resultado  {int=0}
 * @cdn_id       {int=cdn_id}
 * @cla_id     {int=0}
 * @return  {boolean}
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/

function fn_cargarPlusDescuento(Id_Promociones) {
    plus_agregados_descuento = [];
    var html = '';
    send = { "cargarRequeridosPromociones": 1 };
    send.Id_Promociones = Id_Promociones;
    $.getJSON("../adminDescuentos/config_descuentos.php", send, function(datos) {
        $.each(datos, function(key, element) {
            if ("object" === $.type(element)) {
                plus_agregados_productos_requeridos[element.plu_id] = element;
                var nuevoElemento = crearElementoListaPlusRequerido(element);
                $("#listado-plus-agregardos-requeridos").append(nuevoElemento);
            }
        });
    });

    return true;
}

/**
 * Función fn_cargarCiudades(dsct_id, cdn_id)
 * Convoca al procedimiento almacenado [promociones].[Ciudades] para mostar las ciudades en las que existen restaurantes habiltiados
 * Verificar que la variable cadena sea obtenida exitosamente de la sesión
 * @dsct_id           {int=0}
 * @cdn_id               {int=@cdn_id}
 * NO RETORNA VALORES
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function fn_cargarCiudades(dsct_id, cdn_id) {
    var send;
    send = { "cargarCiudades": 1 };
    send.resultado = 0;
    send.dsct_id = dsct_id;
    send.cdn_id = cdn_id;
    send.parametro = '';
    send.pagina = 0;
    send.registros = 0;
    $.getJSON("../adminDescuentos/config_descuentos.php", send, function(datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                var item = crearItemListaCiudadaes(datos[i]);
                $("#lst_rstCiu_id").append(item);
            }
        }
    });
}

/**
 * Función fn_cargarCanal(Id_Promociones)
 * Convoca al procedimiento almacenado [promociones].[CargarCanal]  para mostar los canales que de una determianda promoción
 * Verificar que la variable cadena sea obtenida exitosamente de la sesión
 * @Id_Promociones   {nvarchar=@Id_Promociones}
 * NO RETORNA VALORES
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function fn_cargarCanal(Id_Promociones) {
    var send;
    send = { "cargarCanal": 1 };
    send.Id_Promociones = Id_Promociones;
    $.getJSON("../adminDescuentos/config_descuentos.php", send, function(datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                var item = verItemcanal(datos[i]);
            }
        }
    });
}

/**
 * Función verItemcanal(datosCanal)
 * Selecciona los canales que se añadieron a una promoción para su edición
 * @datosCanal   {datoscanal}
 * NO RETORNA VALORES
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function verItemcanal(datosCanal) {
    canalesPromocion = canalesPromocion.concat(datosCanal['variableV'], ',');

    if (canalesPromocion.indexOf(document.getElementById('0D049503-85CF-E511-80C6-000D3A3261F3').value) > -1) {
        document.getElementById('0D049503-85CF-E511-80C6-000D3A3261F3').selected = true;
    }

    if (canalesPromocion.indexOf(document.getElementById('0E049503-85CF-E511-80C6-000D3A3261F3').value) > -1) {
        document.getElementById('0E049503-85CF-E511-80C6-000D3A3261F3').selected = true;
    }
}

/**
 * Función fn_eliminarCiudades(dsct_id, cdn_id)
 * Convoca al procedimiento almacenado [promociones].[Ciudades] para mostar las ciudades en las que existen restaurantes habiltiados
 * Verificar que la variable cadena sea obtenida exitosamente de la sesión
 * @dsct_id           {int=0}
 * @cdn_id               {int=@cdn_id}
 * NO RETORNA VALORES
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function fn_eliminarCiudades(dsct_id, cdn_id) {
    var send;
    send = { "cargarCiudades": 1 };
    send.resultado = 0;
    send.dsct_id = dsct_id;
    send.cdn_id = cdn_id;
    send.parametro = '';
    send.pagina = 0;
    send.registros = 0;
    $.getJSON("../adminDescuentos/config_descuentos.php", send, function(datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                var item = eliminarCiudad(datos[i]);
            }
        }
    });
}

/**
 * Función fn_cargarRegion(dsct_id, cdn_id)
 * Convoca al procedimiento almacenado [promociones].[Regiones] para mostar las regiones en las que existen restaurantes habiltiados
 * Verificar que la variable cadena sea obtenida exitosamente de la sesión
 * @dsct_id           {int=0}
 * @cdn_id               {int=@cdn_id}
 * NO RETORNA VALORES
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function fn_cargarRegion(dsct_id, cdn_id) {
    var send;
    send = { "cargarRegiones": 1 };
    send.resultado = 0;
    send.dsct_id = dsct_id;
    send.cdn_id = cdn_id;
    send.parametro = '';
    send.pagina = 0;
    send.registros = 0;
    $.getJSON("../adminDescuentos/config_descuentos.php", send, function(datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {

                var item = crearItemListaRegiones(datos[i]);
                $("#lst_rstRegion_id").append(item);
            }
        }
    });

}

/**
 * Función fn_eliminarRegion(dsct_id, cdn_id)
 * Convoca al procedimiento almacenado [promociones].[Regiones] para mostar las regiones en las que existen restaurantes habiltiados
 * Verificar que la variable cadena sea obtenida exitosamente de la sesión
 * @dsct_id           {int=0}
 * @cdn_id               {int=@cdn_id}
 * NO RETORNA VALORES
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function fn_eliminarRegion(dsct_id, cdn_id) {
    var send;
    send = { "cargarRegiones": 1 };
    send.resultado = 0;
    send.dsct_id = dsct_id;
    send.cdn_id = cdn_id;
    send.parametro = '';
    send.pagina = 0;
    send.registros = 0;
    $.getJSON("../adminDescuentos/config_descuentos.php", send, function(datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                var item = eliminarRegion(datos[i]);
            }
        }
    });

}

/**
 * Función eliminarCiudad(datosCiudad)
 * Al recibir los datos de ciudad elimina el listado de Ciudades
 * @datosCiudad           {datos Ciudad}
 * @return {boolean}
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function eliminarCiudad(datosCiudad) {
    var idRP = datosCiudad['ciu_id'];
    if (document.getElementById('Ciudad_' + idRP) != null) {
        document.getElementById('Ciudad_' + idRP).remove();
    }
    return true;
}

function agregarRestauranteCiudad(datosRestaurante) {
    var cdn_id = $("#cadenaId").val();
    fn_cargarRestaurantesCiudades(0, cdn_id, datosRestaurante['ciu_id']);
}

function fn_cargarRestaurantesPromocion(Id_Promociones, cdn_id) {
    var send;
    send = { "cargarRestaurantesPromociones": 1 };
    send.Id_Promociones = Id_Promociones;
    send.cdn_id = cdn_id;
    $.getJSON("../adminDescuentos/config_descuentos.php", send, function(datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                var item = agregarRestaurantePromociones(datos[i]);
            }
        }
    });
}

function fn_cargarPlusBeneficiosPromocion(Id_Promociones) {
    plus_agregados_descuento = [];
    var html = '';
    send = { "cargarPlusBeneficiosPromocion": 1 };
    send.Id_Promociones = Id_Promociones;
    $.getJSON("../adminDescuentos/config_descuentos.php", send, function(datos) {

        $.each(datos, function(key, element) {
            if ("object" === $.type(element)) {
                var nuevoElemento = crearElementoListaBeneficios(element);
                $("#listado-plus-agregardos-descuento").append(nuevoElemento);
                //console.log(511,element);
            }
        });
        /*
            $.each(datos, function (key, element) {
                if ("object" === $.type(element)) {
                    plus_agregados_descuento[element.plu_id] = element;
                    var nuevoElemento = agregarElementoListaPlusDescuento(element);
                    $("#listado-plus-agregardos-descuento").append(nuevoElemento);
                }
            });
        */
    });

    return true;
}


/**
 * Función fn_cargarRestaurantesCiudades(dsct_id, cdn_id, ciu_id)
 * Ejecuta el procedimiento alamcenado [promociones].[RestaurantesCiudades] para retornar los restaurants clasificados por Ciudades
 * Verificar si el entero de cadena y de ciudad son valores correctos.
 * Con cada iteración se convoca al métido agregarRestauranteCiudad1 que hace la inserción
 * @dsct_id    {int=0}
 * @cdn_id       {int=cdn_id}
 * @ciu_id     {int=ciu_id}
 * @return     {boolean}
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function fn_cargarRestaurantesCiudades(dsct_id, cdn_id, ciu_id) {
    var send;
    send = { "cargarRestaurantesCiudades": 1 };
    send.resultado = 0;
    send.dsct_id = dsct_id;
    send.cdn_id = cdn_id;
    send.parametro = ciu_id;
    send.pagina = 0;
    send.registros = 0;
    $.getJSON("../adminDescuentos/config_descuentos.php", send, function(datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                var item = agregarRestauranteCiudad1(datos[i]);
            }
        }
    });
}

function crearItemListaCiudadaes(datosRestaurante) {
    var $itemActual = $("<li id='Ciudad_" + datosRestaurante['ciu_id'] + "' class='list-group-item' />");
    $itemActual.data("ciu_id", datosRestaurante['ciu_id']);
    $itemActual.data("ciu_nombre", datosRestaurante['ciu_nombre']);

    var idB = datosRestaurante['ciu_id'];

    var $botonAgregar = $("<button class='btn btn-xs btn-primary' id='" + idB + "'> Agregar</button>");
    var $botonEliminar = $("<button class='btn btn-xs btn-danger' id='" + idB + "'> Eliminar</button>");

    $botonAgregar.on("click", function() {
        if (verificaAddCiudad == "" || verificaAddCiudad != datosRestaurante['ciu_id']) {
            verificaAddCiudad = datosRestaurante['ciu_id'];
            $itemActual.append($botonEliminar);
            $botonAgregar.hide();
            $botonEliminar.show();
            agregarRestauranteCiudad(datosRestaurante);

        } else {
            fn_alerta("<b>Alerta!</b> Ciudad ya fue seleccionada.", "danger");
            return false;
        }
    });

    $botonEliminar.on("click", function() {
        verificaAddCiudad = "";
        $botonAgregar.show();
        $botonEliminar.hide();
        var cdn_id = $("#cadenaId").val();
        fn_EliminarRestaurantesCiudades(0, cdn_id, datosRestaurante['ciu_id']);


    });

    $itemActual.append($botonAgregar);
    $itemActual.append($botonEliminar);
    $botonEliminar.hide();

    $itemActual.append(" " + datosRestaurante['ciu_nombre'] + " ");
    return $itemActual;
}

function crearItemListaRegiones(datosRestaurante) {
    var $itemActual = $("<li id='Region_" + datosRestaurante['rgn_id'] + "' class='list-group-item' />");

    $itemActual.data("rgn_id", datosRestaurante['rgn_id']);
    $itemActual.data("rgn_descripcion", datosRestaurante['rgn_descripcion']);

    var idB = datosRestaurante['rgn_id'];

    var $botonAgregar = $("<button class='btn btn-xs btn-primary' id='" + idB + "'> Agregar</button>");
    var $botonEliminar = $("<button class='btn btn-xs btn-danger' id='" + idB + "'> Eliminar</button>");

    $botonAgregar.on("click", function() {
        if (verificaAddRegion == "" || verificaAddRegion != datosRestaurante['rgn_id']) {
            verificaAddCiudad = datosRestaurante['rgn_id'];
            $itemActual.append($botonEliminar);
            $botonAgregar.hide();
            $botonEliminar.show();
            agregarRestauranteRegion(datosRestaurante);

        } else {
            fn_alerta("<b>Alerta!</b> Ciudad ya fue seleccionada.", "danger");
            return false;
        }
    });


    $botonEliminar.on("click", function() {
        verificaAddCiudad = "";
        $botonAgregar.show();
        $botonEliminar.hide();

        var cdn_id = $("#cadenaId").val();
        fn_eliminarRestaurantesRegiones(0, cdn_id, datosRestaurante['rgn_descripcion']);


    });

    $itemActual.append($botonAgregar);
    $itemActual.append($botonEliminar);
    $botonEliminar.hide();

    $itemActual.append(" " + datosRestaurante['rgn_descripcion'] + " ");
    return $itemActual;


}


function agregarRestauranteRegion(datosRestaurante) {

    var cdn_id = $("#cadenaId").val();
    fn_cargarRestaurantesRegiones(0, cdn_id, datosRestaurante['rgn_descripcion']);

}

/**
 * Función fn_cargarRestaurantesRegiones(dsct_id, cdn_id, rgn_descripcion)
 * Ejecuta el procedimiento alamcenado [promociones].[RestaurantesRegiones] para retornar los restaurants clasificados por Regiones
 * Verificar si el entero de cadena y de region son valores correctos.
 * Con cada iteración se convoca al método agregarRestauranteCiudad1 que hace la inserción
 * @dsct_id            {int=0}
 * @cdn_id                {int=cdn_id}
 * @rgn_descripcion     {varchar=rgn_descripcion}
 * @return                {boolean}
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function fn_cargarRestaurantesRegiones(dsct_id, cdn_id, rgn_descripcion) {
    var send;
    send = { "cargarRestaurantesRegiones": 1 };
    send.resultado = 0;
    send.dsct_id = dsct_id;
    send.cdn_id = cdn_id;
    send.parametro = rgn_descripcion;
    send.pagina = 0;
    send.registros = 0;
    $.getJSON("../adminDescuentos/config_descuentos.php", send, function(datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                var item = agregarRestauranteCiudad1(datos[i]);
            }
        }
    });
}

function agregarRestaurantePromociones(datosRestaurante) {

    var $restauranteDescuento = $("<div id='Restaurante_" + datosRestaurante['rst_id'] + "' class='restaurante_descto' style='padding:3px;border:solid 1px #dedede; margin-bottom:3px' />");
    $restauranteDescuento.data("rst-id", datosRestaurante['rst_id']);

    requeridosRestaurantesCupon = requeridosRestaurantesCupon.concat(datosRestaurante['rst_id'], ',');
    $restauranteDescuento.data("rst_descripcion", datosRestaurante['rst_descripcion']);
    var htmlRestauranteDescuento = $("<div>" + datosRestaurante['rst_descripcion'] + "</div>");

    var botonEliminarRestaurante = $("<button type='button' style='float:right' class='btn btn-danger btn-xs' ><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>");
    botonEliminarRestaurante.on("click", function() {
        $(this).parents(".restaurante_descto").hide(500, function() {
            requeridosRestaurantesCupon = requeridosRestaurantesCupon.replace(datosRestaurante['rst_id'] + ',', '');
            var item = crearItemListaRestaurantes(datosRestaurante);
            $("#lst_rst_id").append(item);
            $(this).remove();
        });
    });
    htmlRestauranteDescuento.append(botonEliminarRestaurante);
    $restauranteDescuento.append(htmlRestauranteDescuento);
    $("#lst_rst_dscto").append($restauranteDescuento);
    return true;
}


function agregarRestauranteCiudad1(datosRestaurante) {

    var $restauranteDescuento = $("<div id='Restaurante_" + datosRestaurante['rst_id'] + "' class='restaurante_descto' style='padding:3px;border:solid 1px #dedede; margin-bottom:3px' />");
    $restauranteDescuento.data("rst-id", datosRestaurante['rst_id']);

    requeridosRestaurantesCupon = requeridosRestaurantesCupon.concat(datosRestaurante['rst_id'], ',');
    $restauranteDescuento.data("rst_descripcion", datosRestaurante['rst_descripcion']);
    var htmlRestauranteDescuento = $("<div>" + datosRestaurante['rst_descripcion'] + "</div>");

    $restauranteDescuento.append(htmlRestauranteDescuento);
    $("#lst_rst_dscto").append($restauranteDescuento);
    return true;
}


function crearElementoListaPlusDescuento(elemento) {
    beneficiosProductosCupon = beneficiosProductosCupon.concat(elemento.plu_id, ',', elemento.cantidad_plu, ',', elemento.Tipo_aplica, ',', elemento.IDDescuento, ',', elemento.productoAutoconsumo, ';');

    //if(elemento.plu_id ==0000){ elemento.plu_id = elemento.IDDescuento;}

    var $elementoLista = $("<li id='Beneficio_" + elemento.plu_id + "' class='listado-plus-agregardos-descuento list-group-item col-sm-10'></li>");
    $elementoLista.data("agregado", elemento.agregado);
    var textoDescripcion = elemento.plu_num_plu + " | " + elemento.plu_descripcion + " / <font color='#2e6da4'>Cantidad requerida : <b>" + elemento.cantidad_plu + "</b></font>";
    if (true === elemento.productoAutoconsumo) textoDescripcion = textoDescripcion + " (AUTOCONSUMO)";
    $elementoLista.data("plu_descripcion", textoDescripcion);
    $elementoLista.data("plu_id", elemento.plu_id);
    $elementoLista.data("plu_num_plu", elemento.plu_num_plu);
    $elementoLista.data("cantidad_plu", elemento.cantidad_plu);
    $elementoLista.data("productoautoconsumo", elemento.productoAutoconsumo);
    $elementoLista.html(textoDescripcion);

    var botonEliminarPlu = $("<button type='button' style='float:right' class='btn btn-danger btn-xs' ><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>");
    botonEliminarPlu.on("click", function() {
        beneficiosProductosCupon = beneficiosProductosCupon.replace(elemento.plu_id + ',' + elemento.cantidad_plu + ',' + elemento.Tipo_aplica + ',' + elemento.IDDescuento, ',', elemento.productoAutoconsumo + ';', '');
        //	alert(beneficiosProductosCupon);
        $(this).closest("li").css({ "text-decoration": "line-through" }).data("agregado", 0);
        $(this).remove();
        delete plus_agregados_descuento[elemento.plu_id];
    });

    $elementoLista.append(botonEliminarPlu);
    return $elementoLista;

}


function agregarElementoListaPlusDescuento(elemento) {
    //beneficiosProductosCupon = beneficiosProductosCupon.concat(elemento.plu_id, ',', elemento.cantidad_plu, ',', elemento.Tipo_aplica, ',', elemento.IDDescuento,',', elemento.productoAutoconsumo,';');
    //if(elemento.plu_id ==0000){ elemento.plu_id = elemento.IDDescuento;}
    var $elementoLista = $("<li id='Beneficio_" + elemento.plu_id + "' class='listado-plus-agregardos-descuento list-group-item col-sm-10'></li>");
    $elementoLista.data("agregado", elemento.agregado);
    var textoDescripcion = elemento.plu_num_plu + " | " + elemento.plu_descripcion + " / <font color='#2e6da4'>Cantidad a entregar : <b>" + elemento.cantidad_plu + "</b></font>";
    if (true == elemento.productoAutoconsumo) textoDescripcion = textoDescripcion + " (AUTOCONSUMO)";
    $elementoLista.data("id_beneficio", textoDescripcion);
    $elementoLista.data("plu_descripcion", textoDescripcion);
    $elementoLista.data("plu_id", elemento.plu_id);
    $elementoLista.data("plu_num_plu", elemento.plu_num_plu);
    $elementoLista.data("cantidad_plu", elemento.cantidad_plu);
    $elementoLista.data("productoautoconsumo", elemento.productoAutoconsumo);
    $elementoLista.html(textoDescripcion);

    var botonEliminarPlu = $("<button type='button' style='float:right' class='btn btn-danger btn-xs' ><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>");

    botonEliminarPlu.on("click", function() {


        $(this).closest("li").css({ "text-decoration": "line-through" }).data("agregado", 0);
        $(this).remove();
        delete plus_agregados_descuento[elemento.plu_id];
    });

    $elementoLista.append(botonEliminarPlu);
    plus_agregados_descuento[elemento.plu_id] = elemento;
    plus_agregados_descuento[elemento.plu_id].agregado = 1;

    return $elementoLista;
}

/**
 * Función filtrarLocales().
 * Visualmente permite filtrar la lista de Restaurantes de una promoción
 * NO RECIBE PARÁMETROS
 * NO RETORNA RESUTLADOS
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function filtrarLocales() {
    // Declare variables
    var input, filter, ul, li, a, i;
    input = document.getElementById('buscar_Restaurantes');
    filter = input.value.toUpperCase();
    ul = document.getElementById("lst_rst_id");
    li = ul.getElementsByTagName('li');

    // Loop through all list items, and hide those who don't match the search query
    for (i = 0; i < li.length; i++) {
        //a = li[i].getElementsByTagName("a")[0];
        string = (li[i].innerText || li[i].textContent);
        if (li[i].innerHTML.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}

/**
 * Función filtrarCiudad().
 * Visualmente permite filtrar la lista de Ciudades de donde se desprenderán Restaurantes
 * NO RECIBE PARÁMETROS
 * NO RETORNA RESUTLADOS
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function filtrarCiudad() {
    // Declare variables
    var input, filter, ul, li, a, i;
    input = document.getElementById('buscar_Ciudad');
    filter = input.value.toUpperCase();
    ul = document.getElementById("lst_rstCiu_id");
    li = ul.getElementsByTagName('li');

    // Loop through all list items, and hide those who don't match the search query
    for (i = 0; i < li.length; i++) {
        //a = li[i].getElementsByTagName("a")[0];
        string = (li[i].innerText || li[i].textContent);
        if (li[i].innerHTML.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}

/**
 * Función filtrarRegion().
 * Visualmente permite filtrar la lista de Regiones de donde se desprenderán Restaurantes
 * NO RECIBE PARÁMETROS
 * NO RETORNA RESUTLADOS
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function filtrarRegion() {
    // Declare variables
    var input, filter, ul, li, a, i;
    input = document.getElementById('buscar_Region');
    filter = input.value.toUpperCase();
    ul = document.getElementById("lst_rstRegion_id");
    li = ul.getElementsByTagName('li');

    // Loop through all list items, and hide those who don't match the search query
    for (i = 0; i < li.length; i++) {
        //a = li[i].getElementsByTagName("a")[0];
        string = (li[i].innerText || li[i].textContent);
        if (li[i].innerHTML.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}

/**
 * Función fn_EliminarRestaurantesCiudades(dsct_id, cdn_id, ciu_id)
 * Ejecuta el procedimiento alamcenado [promociones].[RestaurantesRegiones] para retornar los restaurants clasificados por Regiones
 * Verificar si el entero de cadena y de region son valores correctos.
 * Con cada iteración se convoca al método agregarRestauranteCiudad1 que hace la inserción
 * @dsct_id            {int=0}
 * @cdn_id                {int=cdn_id}
 * @rgn_descripcion     {varchar=rgn_descripcion}
 * @return                {boolean}
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function fn_EliminarRestaurantesCiudades(dsct_id, cdn_id, ciu_id) {
    var send;
    send = { "cargarRestaurantesCiudades": 1 };
    send.resultado = 0;
    send.dsct_id = dsct_id;
    send.cdn_id = cdn_id;
    send.parametro = ciu_id;
    send.pagina = 0;
    send.registros = 0;
    $.getJSON("../adminDescuentos/config_descuentos.php", send, function(datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                var item = eliminarRestauranteCiudad(datos[i]);
                // $("#lst_rstCiu_id").append(item);
            }
        }
    });
}

/**
 * Función fn_cargarRestaurantesRegiones(dsct_id, cdn_id, rgn_descripcion)
 * Ejecuta el procedimiento alamcenado [promociones].[RestaurantesRegiones] para retornar los restaurants clasificados por Regiones
 * Verificar si el entero de cadena y de region son valores correctos.
 * Con cada iteración se convoca al método agregarRestauranteCiudad1 que hace la inserción
 * @dsct_id            {int=0}
 * @cdn_id                {int=cdn_id}
 * @rgn_descripcion     {varchar=rgn_descripcion}
 * @return                {boolean}
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function fn_eliminarRestaurantesRegiones(dsct_id, cdn_id, rgn_descripcion) {
    var send;
    send = { "cargarRestaurantesRegiones": 1 };
    send.resultado = 0;
    send.dsct_id = dsct_id;
    send.cdn_id = cdn_id;
    send.parametro = rgn_descripcion;
    send.pagina = 0;
    send.registros = 0;
    $.getJSON("../adminDescuentos/config_descuentos.php", send, function(datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                var item = eliminarRestauranteRegion(datos[i]);
                // $("#lst_rstCiu_id").append(item);
            }
        }
    });
}


/**
 * Función eliminarRestauranteCiudad(datosRestaurante)
 * Al recibir los datos de Ciudadrestaurante elimina el listado de Restaurantes Activos y NoActivos
 * @datosRestaurante           {datosRestaurante}
 * @return {boolean}
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function eliminarRestauranteCiudad(datosRestaurante) {

    var idRP = datosRestaurante['rst_id'];
    if (document.getElementById('Restaurante_' + idRP) != null) {
        document.getElementById('Restaurante_' + idRP).remove();
    }

    if (document.getElementById('RestauranteNoActivo_' + idRP) != null) {
        document.getElementById('RestauranteNoActivo_' + idRP).remove();
    }
    return true;
}


/**
 * Función eliminarRegion(datosRegion)
 * Al recibir los datos de Region elimina el listado de Regiones
 * @datosRegion           {datosRegion}
 * @return {boolean}
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function eliminarRegion(datosRegion) {
    var idRP = datosRegion['rgn_id'];
    if (document.getElementById('Region_' + idRP) != null) {
        document.getElementById('Region_' + idRP).remove();
    }
    return true;
}


/**
 * Función eliminarRestauranteRegion(datosRestaurante)
 * Al recibir los datos de Restaurante elimina el listado de Restaurantes
 * @datosRestaurante    {datosRestaurante}
 * @return {boolean}
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function eliminarRestauranteRegion(datosRestaurante) {

    var idRP = datosRestaurante['rst_id'];
    if (document.getElementById('Restaurante_' + idRP) != null) {
        document.getElementById('Restaurante_' + idRP).remove();
    }
    return true;
}


function isEmpty(str) {
    return (!str || 0 === str.length);
}

function verificarDatos1() {
    if (document.getElementById('Nombre').value == '') {
        alertify.alert('Debe ingresar el nombre de la Promoción.');
        document.getElementById('Nombre').focus();
    } else {
        if (document.getElementById('Nombre_imprimible').value == '') {
            alertify.alert('Debe ingresar el Nombre Imprimible de la Promoción.');
            document.getElementById('Nombre_imprimible').focus();
        } else {
            if (document.getElementById('Codigo_externo').value == '') {
                alertify.alert('Debe ingresar el Código Externo de la Promoción.');
                document.getElementById('Codigo_externo').focus();
            } else {
                if (document.getElementById('Codigo_amigable').value == '') {
                    alertify.alert('Debe ingresar el Código Amigable de la Promoción.');
                    document.getElementById('Codigo_amigable').focus();
                } else {
                    if (document.getElementById('Activo_desde').value == '') {
                        alertify.alert('Debe ingresar la fecha de inicio de la Promoción.');
                        document.getElementById('Activo_desde').focus();
                    } else {
                        if (document.getElementById('Activo_Hasta').value == '') {
                            alertify.alert('Debe ingresar la fecha de fin de la Promoción.');
                            document.getElementById('Activo_Hasta').focus();
                        } else {
                            //guardarPromo();
                            alert(RequeridosProductosCupon);
                            alert(requeridosRestaurantesCupon);
                            alert(beneficiosProductosCupon);
                        }
                    }
                }
            }
        }
    }

}

/**
 * Función verificarDatos().
 * Revisa que se cumplan los criterios para la creación de una promoción y enlaza a la función guardarPromo()
 * NO RECIBE PARÁMETROS
 * NO RETORNA RESULTADOS
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function verificarDatos() {

    if (document.getElementById('Nombre').value == '') {
        alertify.alert('Debe ingresar el nombre de la Promoción.');
        document.getElementById('Nombre').focus();
        return false;
    }
    if (document.getElementById('Activo_desde').value == '') {
        alertify.alert('Debe ingresar la fecha de inicio de la Promoción.');
        document.getElementById('Activo_desde').focus();
        return false;
    }
    if (document.getElementById('Activo_Hasta').value == '') {
        alertify.alert('Debe ingresar la fecha de fin de la Promoción.');
        document.getElementById('Activo_Hasta').focus();
        return false;
    }

    //if (beneficiosProductosCupon == '') {
    if (contarBenerficiosAgregados() === 0) {
        alertify.alert('Debe ingresar al menos un (1) beneficio.');
        return false;
    }
    guardarPromo();
}


/**
 * Función guardarPromo().
 * Recoge todos los valores exigidos para la creación de una promoción y ejecuta el Procedimiento almacenado de inserción promociones.IAE_insertarNuevaPromocion
 * Considerar que en vista de las variaciones generadas a lo lago del desarrollo existen variables con valores NULL dabido a que su funcionalidad aún no ha sido implementada
 * NO RECIBE PARÁMETROS
 * @return   {boolean}
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function guardarPromo() {

    var Id_Promociones = $('#Id_Promociones').val();
    var cdn_id = $('#cadenaId').val();
    var Codigo_externo = null;
    var Nombre = $('#Nombre').val();
    var Nombre_imprimible = $('#Nombre_imprimible').val();
    var Codigo_amigable = null;
    var Limite_canjes_total = $('#Limite_canjes_total').val();
    if (Limite_canjes_total == '') {
        Limite_canjes_total = 0;
    }
    var Limite_canjes_cliente = $('#Limite_canjes_cliente').val();
    if (Limite_canjes_cliente == '') {
        Limite_canjes_cliente = 0;
    }
    var Total_canjeados = 0;
    var Caduca_con_tiempo = 0;
    var Unidad_Tiempo_validez = null;
    var Tiempo_validez = 0;
    var Activo_desde = $('#Activo_desde').val();
    var Activo_Hasta = $('#Activo_Hasta').val();
    var Requiere_forma_Pago = 0;
    var Puntos_Acumulables = 0;
    if (Puntos_Acumulables == '') {
        Puntos_Acumulables = 0;
    }
    var Saldo_Acumulable = 0;
    if (Saldo_Acumulable == '') {
        Saldo_Acumulable = 0;
    }
    var Bruto_minimo_factura = $('#Bruto_minimo_factura').val();
    if (Bruto_minimo_factura == '') {
        Bruto_minimo_factura = 0;
    }
    var Bruto_maximo_factura = $('#Bruto_maximo_factura').val();
    if (Bruto_maximo_factura == '') {
        Bruto_maximo_factura = 0;
    }
    var Cantidad_minima_productos_factura = $('#Cantidad_minima_productos_factura').val();
    if (Cantidad_minima_productos_factura == '') {
        Cantidad_minima_productos_factura = 0;
    }
    var Permite_otras_promociones = permitePromoSobrePromo();
    var Maximo_canje_multiple = $('#Maximo_canje_multiple').val();
    if (Maximo_canje_multiple == '') {
        Maximo_canje_multiple = 0;
    }

    if (document.getElementById('Requiere_dias').checked) {
        var Requiere_dias = 1;
        var diasSeleccionados = [];

        if (document.getElementById('1').checked) diasSeleccionados.push(1);
        if (document.getElementById('2').checked) diasSeleccionados.push(2);
        if (document.getElementById('3').checked) diasSeleccionados.push(3);
        if (document.getElementById('4').checked) diasSeleccionados.push(4);
        if (document.getElementById('5').checked) diasSeleccionados.push(5);
        if (document.getElementById('6').checked) diasSeleccionados.push(6);
        if (document.getElementById('7').checked) diasSeleccionados.push(7);
        Dias_canjeable = diasSeleccionados.join();
    } else {
        var Requiere_dias = 0;
        var Dias_canjeable = null;
    }

    if (document.getElementById('Requiere_horario').checked) {
        var Requiere_horario = 1;
        var horarioDesde = $('#horarioDesde').val();
        var horarioHasta = $('#horarioHasta').val();
        var Horario_canjeable = horarioDesde.concat(',', horarioHasta);
    } else {
        var Requiere_horario = 0;
        var Horario_canjeable = null;
    }


    if (document.getElementById('Requiere_rango_edad').checked) {
        var Requiere_rango_edad = 1;
        var edadDesde = $('#edadDesde').val();
        var edadHasta = $('#edadHasta').val();
        var Rango_edad = edadDesde.concat(',', edadHasta);
    } else {
        var Requiere_rango_edad = 0;
        var Rango_edad = null;
    }


    if (document.getElementById('Requiere_canal').checked) {
        var Requiere_canal = 1;
        var Canales_Requeridos_Promocion = $('#idsCanal').val().toString();
    } else {
        var Requiere_canal = 0;
        var Canales_Requeridos_Promocion = null;
    }

    var Requiere_genero = 0;
    var genero = null;
    var Tiene_codigo_unico = 0;
    var Activo = 1;
    var Motivo_inactivacion = null;
    var Permite_descuento_sobre_descuento = permiteDescuentoSobreDescuento();

    //////////////////////////////////////////////////////////////////////////////////////////////////
    ///////Esta linea debe recibir los IDs de las categorias que se ha seleccionado              //////
    ///////Por ahora esta igualado a vacio. Sin embargo cuando se implemente su funcionamiento  //////
    ///////se debe igual a la variable que contenga los IDs de categorias y si es necesario     //////
    ///////eliminar la última coma (,) del string con categoriasPromocion.slice(0,-1);			//////
    /////////////////////////////////////////////////////////////////////////////////////////////////
    var categoriasSeleccionadasPromocion = '';

    if (document.getElementById('Requiere_restaurante').checked) {
        var Requiere_restaurante = 1;
        var Restaurantes_Requeridos_Promocion = requeridosRestaurantesCupon.slice(0, -1);
    } else {
        var Requiere_restaurante = 0;
        var Restaurantes_Requeridos_Promocion = null;
    }

    if (document.getElementById('Requiere_productos').checked) {
        var Requiere_productos = 1;
        var Productos_Requeridos_Promocion = RequeridosProductosCupon;
    } else {
        var Requiere_productos = 0;
        var Productos_Requeridos_Promocion = null;
    }

    //var Concatenador_beneficios = $('#Concatenador_beneficios').val();
    var Concatenador_beneficios = "AND";
    if (Concatenador_beneficios == '') {
        Concatenador_beneficios = '';
    }
    //var Concatenador_plus_promocion = $('#Concatenador_plus_promocion').val();
    var Concatenador_plus_promocion = "AND";
    if (Concatenador_plus_promocion == '') {
        Concatenador_plus_promocion = '';
    }
    var LastUser = $('#usuarioId').val();
    var Beneficios_Promocion = beneficiosProductosCupon;
    var Forma_Pago_Promocion = null;
    var IDColeccionPromociones = 'F034A719-9457-E711-80C8-123456440947';
    //BENEFICIOS DE LA PROMOCION
    var beneficios = retornaStringBeneficiosCupon();
    $.ajax({
        url: "../promociones/config_promociones.php",
        type: "POST",
        data: {
            guardarPromocion: 1,
            Id_Promociones: Id_Promociones,
            cdn_id: cdn_id,
            Codigo_externo: Codigo_externo,
            Nombre: Nombre,
            Nombre_imprimible: Nombre_imprimible,
            Codigo_amigable: Codigo_amigable,
            Limite_canjes_total: Limite_canjes_total,
            Limite_canjes_cliente: Limite_canjes_cliente,
            Total_canjeados: Total_canjeados,
            Caduca_con_tiempo: Caduca_con_tiempo,
            Unidad_Tiempo_validez: Unidad_Tiempo_validez,
            Tiempo_validez: Tiempo_validez,
            Activo_desde: Activo_desde,
            Activo_Hasta: Activo_Hasta,
            Requiere_productos: Requiere_productos,
            Requiere_forma_Pago: Requiere_forma_Pago,
            Puntos_Acumulables: Puntos_Acumulables,
            Saldo_Acumulable: Saldo_Acumulable,
            Bruto_minimo_factura: Bruto_minimo_factura,
            Bruto_maximo_factura: Bruto_maximo_factura,
            Cantidad_minima_productos_factura: Cantidad_minima_productos_factura,
            Permite_otras_promociones: Permite_otras_promociones,
            Maximo_canje_multiple: Maximo_canje_multiple,
            Requiere_dias: Requiere_dias,
            Dias_canjeable: Dias_canjeable,
            Requiere_horario: Requiere_horario,
            Horario_canjeable: Horario_canjeable,
            Requiere_rango_edad: Requiere_rango_edad,
            Rango_edad: Rango_edad,
            Requiere_genero: Requiere_genero,
            genero: genero,
            Tiene_codigo_unico: Tiene_codigo_unico,
            Activo: Activo,
            Motivo_inactivacion: Motivo_inactivacion,
            Permite_descuento_sobre_descuento: Permite_descuento_sobre_descuento,
            Concatenador_beneficios: Concatenador_beneficios,
            Concatenador_plus_promocion: Concatenador_plus_promocion,
            Requiere_canal: Requiere_canal,
            Requiere_restaurante: Requiere_restaurante,
            LastUser: LastUser,
            Beneficios_Promocion: beneficios,
            Productos_Requeridos_Promocion: Productos_Requeridos_Promocion,
            Forma_Pago_Promocion: Forma_Pago_Promocion,
            Restaurantes_Requeridos_Promocion: Restaurantes_Requeridos_Promocion,
            Canales_Requeridos_Promocion: Canales_Requeridos_Promocion,
            IDColeccionPromociones: IDColeccionPromociones,
            categoriasSeleccionadasPromocion: categoriasSeleccionadasPromocion
        },
        dataType: "json",
        success: function(datos) {
            fn_alerta("<b>Correcto!</b> Se ha guardado la promoción.", "success");
        },
        complete: function() {
            $("#incluirPagina").load('../promociones/adminPromociones.php');
        },
        error: function(xhr, ajaxOptions, thrownError) {
            fn_alerta("<b>Incorrecto!</b> Ocurrió un error, inténtalo nuevamente.", "danger");
        }
    });


}

/**
 * Función fn_cargarLocalesNoAsignadosDescuento(cdn_id)
 * Convoca al procedimiento almacenado [promociones].[RestaurantesCiudadesTotal] para mostar Restaurantes
 * Verificar que la variablse cadena sea obtenida exitosamente de la sesión
 * @cdn_id {int=@cdn_id}
 * NO RETORNA VALORES
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function fn_cargando(estado) {
    if (estado) {
        $("#cargando").css("display", "block");
        $("#cargandoimg").css("display", "block");
    } else {
        $("#cargando").css("display", "none");
        $("#cargandoimg").css("display", "none");
    }
}

/**
 * Función fn_cargarLocalesNoAsignadosDescuento(cdn_id)
 * Convoca al procedimiento almacenado [promociones].[RestaurantesCiudadesTotal] para mostar Restaurantes
 * Verificar que la variablse cadena sea obtenida exitosamente de la sesión
 * @cdn_id {int=@cdn_id}
 * NO RETORNA VALORES
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function fn_cargarLocalesNoAsignadosDescuento(Id_Promociones, cdn_id) {

    var send;
    send = { "cargarRestaurantesNoAsignadosPromociones": 1 };
    send.Id_Promociones = Id_Promociones;
    send.cdn_id = cdn_id;
    $.getJSON("../adminDescuentos/config_descuentos.php", send, function(datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                var item = crearItemListaRestaurantes(datos[i]);
                $("#lst_rst_id").append(item);
            }
        }
    });
}

/**
 * Función fn_cargarLocalesNoAsignadosTotales(Id_Promociones, cdn_id)
 * Convoca al procedimiento almacenado [promociones].[RestaurantesCiudadesTotal] para mostar Restaurantes de un promoción
 * Verificar que la variablse cadena y variables promociones sea obtenida exitosamente de la sesión
 * @cdn_id           {int=@cdn_id}
 * @Id_Promociones {nvarchar=@Id_Promociones}
 * NO RETORNA VALORES
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function fn_cargarLocalesNoAsignadosTotales(Id_Promociones, cdn_id) {
    var send;
    send = { "cargarRestaurantesCiudadesTotal": 1 };
    send.cdn_id = cdn_id;
    $.getJSON("../adminDescuentos/config_descuentos.php", send, function(datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                var item = crearItemListaRestaurantes(datos[i]);
                $("#lst_rst_id").append(item);
            }
        }
    });
}


function crearItemListaRestaurantes(datosRestaurante) {
    var $itemActual = $("<li id='RestauranteNoActivo_" + datosRestaurante['rst_id'] + "' class='list-group-item' />");
    $itemActual.data("rst_id", datosRestaurante['rst_id']);
    $itemActual.data("rst_descripcion", datosRestaurante['rst_descripcion']);


    var $botonAgregar = $("<button class='btn btn-xs btn-success'>Agregar</button>");
    $botonAgregar.on("click", function() {
        $itemActual.empty().remove();
        agregarRestaurante(datosRestaurante);
    });
    $itemActual.append($botonAgregar);
    $itemActual.append(" " + datosRestaurante['rst_descripcion']);
    return $itemActual;
}

function agregarRestaurante(datosRestaurante) {
    var $restauranteDescuento = $("<div id='Restaurante_" + datosRestaurante['rst_id'] + "' class='restaurante_descto' style='padding:3px;border:solid 1px #dedede; margin-bottom:3px' />");
    $restauranteDescuento.data("rst-id", datosRestaurante['rst_id']);


    requeridosRestaurantesCupon = requeridosRestaurantesCupon.concat(datosRestaurante['rst_id'], ',');

    $restauranteDescuento.data("rst-descripcion", datosRestaurante['rst_descripcion']);

    var htmlRestauranteDescuento = $("<div>" + datosRestaurante['rst_descripcion'] + "</div>");
    var botonEliminarRestaurante = $("<button type='button' style='float:right' class='btn btn-danger btn-xs' ><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>");
    botonEliminarRestaurante.on("click", function() {
        $(this).parents(".restaurante_descto").hide(500, function() {
            requeridosRestaurantesCupon = requeridosRestaurantesCupon.replace(datosRestaurante['rst_id'] + ',', '');
            var item = crearItemListaRestaurantes(datosRestaurante);
            $("#lst_rst_id").append(item);
            $(this).remove();
        });
    });
    htmlRestauranteDescuento.append(botonEliminarRestaurante);
    $restauranteDescuento.append(htmlRestauranteDescuento);
    $("#lst_rst_dscto").append($restauranteDescuento);
    return true;
}


function btn_agregar_producto_requerido() {

    //var Concatenador_plus_promocion = $("#Concatenador_plus_promocion").val();
    var Concatenador_plus_promocion = "AND"
    var valorPLU = $("#IdsProductosRequeridosPromocion").val();
    var cantidadPLURequerido = $("#cantidadProductoRequerido").val();
    var $elementoPLU = $($("#IdsProductosRequeridosPromocion option[value='" + valorPLU + "']")[0]);

    if (valorPLU == 0) {
        fn_alerta("<b>Alerta!</b> Por favor escoga un producto e ingrese la cantidad.", "danger");
        return false;
    }
    if (cantidadPLURequerido == '' || cantidadPLURequerido == 0) {
        fn_alerta("<b>Alerta!</b> Por favor ingrese la cantidad del Producto.", "danger");
        return false;
    }
    if (Concatenador_plus_promocion == 0 || Concatenador_plus_promocion == '') {
        fn_alerta("<b>Alerta!</b> Por favor escoja un vinculador de Productos.", "danger");
        return false;
    }

    if (plus_agregados_productos_requeridos.hasOwnProperty(valorPLU)) {
        if (plus_agregados_productos_requeridos[valorPLU].agregado == 1) {
            fn_alerta("<b>Alerta!</b> El PLU ya se encuentra asignado.", "danger");
            return false;
        }
    }

    var valoresNuevoElemento = {
        agregado: 1,
        plu_id: $elementoPLU.data("plu_id"),
        plu_descripcion: $elementoPLU.data("plu_descripcion"),
        plu_num_plu: $elementoPLU.data("plu_num_plu"),
        cantidad_plu: cantidadPLURequerido,
        concatenador_plu: Concatenador_plus_promocion
    };

    var nuevoElemento = crearElementoListaPlusRequerido(valoresNuevoElemento);
    plus_agregados_productos_requeridos[valoresNuevoElemento.plu_id] = valoresNuevoElemento;
    $("#listado-plus-agregardos-requeridos").append(nuevoElemento);
    return true;
}


function crearElementoListaPlusRequerido(elemento) {

    RequeridosProductosCupon = RequeridosProductosCupon.concat(elemento.plu_id, ',', elemento.cantidad_plu, ';');


    var $elementoLista = $("<li class='elemento-lista-plus-descuento list-group-item col-sm-8'></li>");
    $elementoLista.data("agregado", elemento.agregado);
    var textoDescripcion = elemento.plu_num_plu + " | " + elemento.plu_descripcion + " / <font color='#2e6da4'>Cantidad requerida : <b>" + elemento.cantidad_plu + "</b></font>";
    $elementoLista.data("plu_descripcion", textoDescripcion);
    $elementoLista.data("plu_id", elemento.plu_id);
    $elementoLista.data("plu_num_plu", elemento.plu_num_plu);
    $elementoLista.data("cantidad_plu", elemento.cantidad_plu);
    $elementoLista.html(textoDescripcion);
    var botonEliminarPlu = $("<button type='button' style='float:right' class='btn btn-danger btn-xs' ><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>");
    botonEliminarPlu.on("click", function() {
        $(this).closest(".elemento-lista-plus-descuento").empty().remove();
        delete plus_agregados_productos_requeridos[elemento.plu_id];

        RequeridosProductosCupon = RequeridosProductosCupon.replace(elemento.plu_id + ',' + elemento.cantidad_plu + ';', '');


    });
    $elementoLista.append(botonEliminarPlu);

    var $elementoPLU = $($("#select-plus-cadena option[value='" + elemento.plu_id + "']")[0]);
    var valoresNuevoElemento = {
        agregado: 1,
        plu_id: $elementoPLU.data("plu_id"),
        plu_descripcion: $elementoPLU.data("plu_descripcion"),
        plu_num_plu: $elementoPLU.data("plu_num_plu"),
        cantidad_plu: elemento.cantidad_plu,
        concatenador_plu: "AND"
    };

    plus_agregados_productos_requeridos[valoresNuevoElemento.plu_id] = valoresNuevoElemento;
    return $elementoLista;
}

function fn_alerta(mensaje, tipo) {
    setTimeout(function() {
        $.bootstrapGrowl(mensaje, {
            type: tipo,
            align: 'right',
            width: '500',
            allow_dismiss: false
        });
    }, 100);
}

function crearElementoListaRestauranteRequerido(elemento) {
    var $elementoLista = $("<li class='listado-restaurantes-agregardos-requeridos list-group-item'></li>");
    $elementoLista.data("agregado", elemento.agregado);
    var textoDescripcion = elemento.plu_num_plu + " | " + elemento.plu_descripcion;
    $elementoLista.data("plu_descripcion", textoDescripcion);
    $elementoLista.data("plu_id", elemento.plu_id);
    $elementoLista.data("plu_num_plu", elemento.plu_num_plu);
    $elementoLista.html(textoDescripcion);
    var botonEliminarPlu = $("<button type='button' style='float:right' class='btn btn-danger btn-xs' ><span class='glyphicon glyphicon-remove' aria-hidden='true'></span></button>");
    botonEliminarPlu.on("click", function() {
        $(this).closest(".elemento-lista-plus-descuento").empty().remove();
        delete plus_agregados_descuento[elemento.plu_id];
    });
    $elementoLista.append(botonEliminarPlu);
    return $elementoLista;
}

function fn_EliminarRestaurantesCiudadesTotal(dsct_id, cdn_id) {
    var send;
    send = { "cargarRestaurantesCiudadesTotal": 1 };
    send.cdn_id = cdn_id;
    $.getJSON("../adminDescuentos/config_descuentos.php", send, function(datos) {
        if (datos.str > 0) {
            for (i = 0; i < datos.str; i++) {
                var item = eliminarRestauranteCiudad(datos[i]);
            }
        }
    });
}

function justNumbers(e) {
    tecla = (document.all) ? e.keyCode : e.which;

    if (tecla == 8) {
        return true;
    }

    if (tecla == 9) {
        return true;
    }

    patron = /[0-9.]/;
    tecla_final = String.fromCharCode(tecla);
    return patron.test(tecla_final);
}

/**
 * Función enableRequiereProductos()
 * Visulamente muestra u oculta los combos de productos o dsecuentos que serán aplicados a una promoción
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function enableRequiereProductos() {

    document.getElementById("cantidadProductoRequerido").value = 1;
    $("#IdsProductosRequeridosPromocion").chosen({ width: "100%", search_contains: true });
    var elementoLista = $("<li class='elemento-lista-plus-descuento list-group-item'></li>");

    var x = document.getElementById("panel-productos-requeridos");
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }


}


function verPromociones() {
    $("#incluirPagina").load('../promociones/adminPromociones.php');
}

/**
 * Función enableRequiereProductos()
 * Visulamente muestra u oculta el panel de restaurantes que han sido seleccionados para una promoción
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function enableRequiereRestaurantes() {

    $("#IdsProductosRequeridosPromocion").chosen({ width: "100%", search_contains: true });
    var elementoLista = $("<li class='elemento-lista-plus-descuento list-group-item'></li>");

    var x = document.getElementById("panel-restaurantes-requeridos");
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }
}

/**
 * Función enableRequiereProductos()
 * Visulamente todos los restaurantes disponibles para aplicarlos a una promoción
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function verTodosRestaurantes() {

    requeridosRestaurantesCupon = '';
    var cdn_id = $("#cadenaId").val();
    var Id_Promociones = $("#Id_Promociones").val();

    fn_EliminarRestaurantesCiudadesTotal(0, cdn_id);
    fn_cargarLocalesNoAsignadosTotales(Id_Promociones, cdn_id);

    document.getElementById("btnQuitarTodosRestaurantes").style.display = "block";
    document.getElementById("btnQuitarTodosRestaurantesCiudades").style.display = "none";
    document.getElementById("btnQuitarTodosRestaurantesRegiones").style.display = "none";


    var x = document.getElementById("RestaurantesTodos");
    var y = document.getElementById("RestaurantesCiudad");
    var z = document.getElementById("RestaurantesProvincia");

    if (x.style.display === "none") {
        x.style.display = "block";
        y.style.display = "none";
        z.style.display = "none";
    }
}


/**
 * Función enableRequiereProductos()
 * Visulamente todos los restaurantes disponibles para aplicarlos a una promoción calsificados por Restaurantes
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function verProvinciasRestaurantes() {

    requeridosRestaurantesCupon = '';
    var cdn_id = $("#cadenaId").val();
    fn_EliminarRestaurantesCiudadesTotal(0, cdn_id);
    fn_eliminarRegion(0, cdn_id)
    fn_cargarRegion(0, cdn_id);

    document.getElementById("btnQuitarTodosRestaurantes").style.display = "none";
    document.getElementById("btnQuitarTodosRestaurantesCiudades").style.display = "block";
    document.getElementById("btnQuitarTodosRestaurantesRegiones").style.display = "none";

    var x = document.getElementById("RestaurantesTodos");
    var y = document.getElementById("RestaurantesCiudad");
    var z = document.getElementById("RestaurantesProvincia");

    if (z.style.display === "none") {
        z.style.display = "block";
        x.style.display = "none";
        y.style.display = "none";
    }
}


/**
 * Función enableRequiereProductos()
 * Visulamente todos los restaurantes disponibles para aplicarlos a una promoción calsificados por Regiones
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function verRegionesRestaurantes() {
    requeridosRestaurantesCupon = '';
    var cdn_id = $("#cadenaId").val();
    fn_EliminarRestaurantesCiudadesTotal(0, cdn_id);
    fn_eliminarCiudades(0, cdn_id)
    fn_cargarCiudades(0, cdn_id);


    document.getElementById("btnQuitarTodosRestaurantes").style.display = "none";
    document.getElementById("btnQuitarTodosRestaurantesCiudades").style.display = "none";
    document.getElementById("btnQuitarTodosRestaurantesRegiones").style.display = "block";

    var x = document.getElementById("RestaurantesTodos");
    var y = document.getElementById("RestaurantesCiudad");
    var z = document.getElementById("RestaurantesProvincia");

    if (y.style.display === "none") {
        y.style.display = "block";
        x.style.display = "none";
        z.style.display = "none";
    }
}


/**
 * Función enableRequiereProductos()
 * Comtrola la habilitación de horarios para una promoción
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function enableRequiereHorario() {
    if (document.getElementById('Requiere_horario').checked) {
        document.getElementById("horarioDesde").disabled = false;
        document.getElementById("horarioHasta").disabled = false;
    } else {
        document.getElementById("horarioDesde").disabled = true;
        document.getElementById("horarioHasta").disabled = true;
    }
}


/**
 * Función enableRequiereProductos()
 * Comtrola la habilitación de tiempos para una promoción
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function enableRequiereTiempo() {
    if (document.getElementById('Caduca_con_tiempo').checked) {
        document.getElementById("Unidad_Tiempo_validez").disabled = false;
        document.getElementById("Tiempo_validez").disabled = false;
    } else {
        document.getElementById("Unidad_Tiempo_validez").disabled = true;
        document.getElementById("Tiempo_validez").disabled = true;
    }
}

/**
 * Función enableRequiereProductos()
 * Comtrola la habilitación de dias para una promoción
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function enableRequiereDias() {

    if (document.getElementById('Requiere_dias').checked) {
        document.getElementById("1").disabled = false;
        document.getElementById("2").disabled = false;
        document.getElementById("3").disabled = false;
        document.getElementById("4").disabled = false;
        document.getElementById("5").disabled = false;
        document.getElementById("6").disabled = false;
        document.getElementById("7").disabled = false;
    } else {
        document.getElementById("1").disabled = true;
        document.getElementById("2").disabled = true;
        document.getElementById("3").disabled = true;
        document.getElementById("4").disabled = true;
        document.getElementById("5").disabled = true;
        document.getElementById("6").disabled = true;
        document.getElementById("7").disabled = true;
    }
}

/**
 * Función enableRequiereProductos()
 * Comtrola la habilitación de formas de `pago para una promoción
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function enableRequiereFormaPago() {

    if (document.getElementById('Requiere_forma_Pago').checked) {
        document.getElementById("IdsFormaPago").disabled = false;
    } else {
        document.getElementById("IdsFormaPago").disabled = true;
    }
}

/**
 * Función enableRequiereProductos()
 * Comtrola la habilitación de edad para una promoción
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function enableRequiereRangoEdad() {

    if (document.getElementById('Requiere_rango_edad').checked) {
        document.getElementById("edadDesde").disabled = false;
        document.getElementById("edadHasta").disabled = false;
    } else {
        document.getElementById("edadDesde").disabled = true;
        document.getElementById("edadHasta").disabled = true;
    }
}

/**
 * Función enableRequiereProductos()
 * Controla la habilitación de canales para una promoción
 * @versión  3.0
 * @author Eduardo Valencia <educristo@gmail.com>
 * @copyright KFC
 **/
function enableRequiereCanal() {

    if (document.getElementById('Requiere_canal').checked) {
        document.getElementById("idsCanal").disabled = false;
    } else {
        document.getElementById("idsCanal").disabled = true;
    }
}

function sumaEdad(a) {
    var edadHasta = parseInt(a.value) + 1;
    document.getElementById("edadHasta").value = edadHasta;
}

function VerRT() {
    var x = document.getElementById("resticcion-tiempo");
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }

}

function VerRN() {
    var x = document.getElementById("restriccion-numericas");
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }

}

function VerRTV() {
    var x = document.getElementById("restriccion-tipoventa");
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }

}

function VerRU() {
    var x = document.getElementById("restriccion-usuario");
    if (x.style.display === "none") {
        x.style.display = "block";
    } else {
        x.style.display = "none";
    }

}


function verificarCombo() {
    var x = document.getElementById("verProductos");
    var y = document.getElementById("verDescuentos");
    var z = document.getElementById("cantidadProductoBeneficio");

    if (document.getElementById('anadirProductos').checked) {
        y.style.display = "none";
        x.style.display = "block";
        z.style.display = "block";
    } else {
        if (document.getElementById('anadirDescuentos').checked) {
            y.style.display = "block";
            x.style.display = "none";
            z.style.display = "none";
            document.getElementById("cantidadProductoBeneficio").value = 1;
        }
    }
}

function esFacturacionAutoconsumo() {
    var estadoSwitch = $("#tipofacturacion").bootstrapSwitch('state');

    //En el switch el encendido significa que es un nuevo PLU
    return estadoSwitch ? 0 : 1;
}

function retornaStringBeneficiosCupon() {
    var $contenedorBeneficios = $("#listado-plus-agregardos-descuento");
    var $elementosListadoBeneficios = $("#listado-plus-agregardos-descuento li");
    var beneficiosArray = [];
    var stringBeneficios = '';
    for (i = 0; i < $elementosListadoBeneficios.length; i++) {
        var dataliBeneficio = $($elementosListadoBeneficios[i]).data();
        if (dataliBeneficio.agregado == 1) {
            stringBeneficios += dataliBeneficio.plu_id + ',';
            stringBeneficios += dataliBeneficio.cantidad_plu + ',';
            stringBeneficios += ((dataliBeneficio.tipo_aplica ? dataliBeneficio.tipo_aplica : '') + ',');
            stringBeneficios += ((dataliBeneficio.id_descuento ? dataliBeneficio.id_descuento : '') + ',');
            stringBeneficios += (dataliBeneficio.productoautoconsumo ? dataliBeneficio.productoautoconsumo : '');
            stringBeneficios += ';';
        }
    }
    return stringBeneficios;
}

function crearElementoListaBeneficios(elemento) {
    //Crear texto interior del elemento
    var textoDescripcion = elemento.plu_num_plu + " | " + elemento.plu_descripcion + " / <font color='#2e6da4'>Cantidad a entregar : <b>" + elemento.cantidad_plu + "</b></font>";
    if (true == elemento.productoAutoconsumo) textoDescripcion = textoDescripcion + " (AUTOCONSUMO)";

    var $elementoLista = $("<li class='listado-plus-agregados-descuento list-group-item col-sm-10'" +
        " data-agregado = '" + elemento.agregado + "'" +
        " data-id_beneficio = '" + elemento.id_beneficio + "'" +
        " data-plu_descripcion = '" + elemento.plu_descripcion + "'" +
        " data-plu_id = '" + elemento.plu_id + "'" +
        " data-plu_num_plu = '" + elemento.plu_num_plu + "'" +
        " data-cantidad_plu = '" + elemento.cantidad_plu + "'" +
        " data-productoautoconsumo = '" + elemento.productoAutoconsumo + "'" +
        " data-id_descuento = '" + elemento.IDDescuento + "'" +
        " data-tipo_aplica = '" + elemento.Tipo_aplica + "'" +
        " >" + textoDescripcion +
        "<button type = 'button' style='float:right' class='btn btn-danger btn-xs btn-quitar-beneficio' >" +
        "<span class = 'glyphicon glyphicon-remove' aria-hidden='true'></span>" +
        "</button>" + "</li>"
    );

    return $elementoLista;
}

function contarBenerficiosAgregados() {
    var $ulListado = $("#listado-plus-agregardos-descuento li");
    var numeroBeneficiosAgregados = 0;
    $ulListado.each(function(clave, valor) {
        var dataElementoActual = $(valor).data();
        numeroBeneficiosAgregados += dataElementoActual.agregado;
    });
    return numeroBeneficiosAgregados;
}

function permiteDescuentoSobreDescuento() {
    var estadoSwitch = $("#descSobreDesc").bootstrapSwitch('state');
    return estadoSwitch ? 1 : 0;
}

function permitePromoSobrePromo() {
    var estadoSwitch = $("#promoSobrePromo").bootstrapSwitch('state');
    return estadoSwitch ? 1 : 0;
}