var $tdActivo = {};

function crearAlertError(texto) {
    var htmlAlert = "<div class='alert alert-warning alert-dismissible' role='alert'>";
    htmlAlert += "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>";
    htmlAlert += "<strong>Error!</strong>" + texto + "</div>";
    return htmlAlert;
}

function crearTextosRutas(idColeccionServidor, valorUrl) {
    $trs = $("tr.tr-url-servidor[data-nombreservidor='" + idColeccionServidor + "']");
    var retorno = [];
    $trs.each(function (element) {
        var $this = $(this);
        var nombreservidor = $this.data("nombreparametro");
        var texto = $this.find("td.valorpolitica").first().text().trim();
        retorno.push({nombreservidor: nombreservidor, texto: texto});
    });
    return retorno;
}

$(document).ready(function () {
    var $modalUrlServidor = $("#modalUrlServidor");
    var $modalRutaServicio = $("#modalRutaServicio");

    var $tituloModalUrlServidor = $("#modalUrlServidor .modal-title");
    var $tituloModalRutaServicio = $("#modalRutaServicio .modal-title");

    var $trsServidor = $(".tr-url-servidor");
    var $trsRutasServicios = $(".tr-ruta-servicio");
    var $trsGerente = $(".tr-ruta-gerente");

    var $formularioGuardarServidor = $("#formGuardarServidor");
    var $formularioGuardarRuta = $("#formGuardarRuta");

    $trsGerente.on("dblclick", function () {

    });
    $trsServidor.on("dblclick", function () {
        $this = $(this);
        $tdActivo = $this.children("td.valorpolitica").first();
        var nombreParametro = $this.data("nombreparametro");
        var idColeccionCadena = $this.data("idcoleccioncadena");
        var idColeccionDeDatosCadena = $this.data("idcolecciondedatoscadena");
        var valor = $tdActivo.text().trim();

        $("input[name='nombreServidor']").val(nombreParametro);
        $("input[name='idColeccionServidor']").val(idColeccionCadena);
        $("input[name='idParametroServidor']").val(idColeccionDeDatosCadena);
        $("input[name='inputValorServidor']").val(valor);

        $tituloModalUrlServidor.html(nombreParametro);
        $modalUrlServidor.modal("show");
    });

    $trsRutasServicios.on("dblclick", function () {
        $this = $(this);
        $tdActivo = $this.children("td.valorpolitica").first();

//.data("data-idcoleccionservidor")

        var nombreparametro = $this.data("nombreparametro");
        var idColeccionCadena = $this.data("idcoleccioncadena");
        var idColeccionDeDatosCadena = $this.data("idcolecciondedatoscadena");
        var valor = $tdActivo.text().trim();

        $("input[name='nombreRuta']").val(nombreparametro);
        $("input[name='idColeccionRuta']").val(idColeccionCadena);
        $("input[name='idParametroRuta']").val(idColeccionDeDatosCadena);
        $("input[name='inputValorRuta']").val(valor);

        var nombreservidor = $this.closest("tr.tr-rutas").data("nombreservidor");
        var textosRutas = crearTextosRutas(nombreservidor, valor);

        var htmlrutas = "";
        textosRutas.forEach(function (element) {
            htmlrutas += "<div class='row'><div class='col-md-12'><div>" + element.nombreservidor + "</div>";
            htmlrutas += "<div class='text-info ' style='font-size: 14px;word-wrap: break-word;'>http://" + element.texto + "<span class='htmlruta'>" + valor + "</span></div></div></div>";
        });
        console.log(htmlrutas);
        $("#rutasFinales").html(htmlrutas);
        $tituloModalRutaServicio.html(nombreparametro);
        $modalRutaServicio.modal("show");
    });

    $formularioGuardarRuta.on("submit", function (evt) {
        evt.preventDefault();
        evt.stopPropagation();
        var formAction = $formularioGuardarRuta.attr("action");
        var datosEnvioPeticion = $formularioGuardarRuta.serialize();
        var $peticion = $.post(formAction, datosEnvioPeticion);
        $peticion.success(function (datos) {
            if (1 == datos.estado) {
                var valor = $("input[name='inputValorRuta']").val();
                $tdActivo.html(valor);
                $modalRutaServicio.modal("hide");
                alertify.success("Guardado Correctamente");
            } else {
                $modalRutaServicio.find(".alert").remove();
                $modalRutaServicio.find(".modal-body").prepend(crearAlertError(datos.error));
            }
        });
    });

    $formularioGuardarServidor.on("submit", function (evt) {
        evt.preventDefault();
        evt.stopPropagation();
        var formAction = $formularioGuardarServidor.attr("action");
        var datosEnvioPeticion = $formularioGuardarServidor.serialize();

        var $peticion = $.post(formAction, datosEnvioPeticion);
        $peticion.success(function (datos) {
            if (1 == datos.estado) {
                var valor = $("input[name='inputValorServidor']").val();
                $tdActivo.html(valor);
                $modalUrlServidor.modal("hide");
                alertify.success("Guardado Correctamente");
            } else {
                $modalUrlServidor.find(".alert").remove();
                $modalUrlServidor.find(".modal-body").prepend(crearAlertError(datos.error));
            }
        });
    });

    $modalUrlServidor.on("shown.bs.modal", function () {
        $("input[name='inputValorServidor']").trigger("focus");
    });
    $modalUrlServidor.on("hide.bs.modal", function () {
        $("input[name='inputValorServidor']").trigger("focus");
    });

    $modalRutaServicio.on("shown.bs.modal", function () {
        $("input[name='inputValorRuta']").trigger("focus");
    });

    $("input[name='inputValorRuta']").on('change paste input', function (evt) {
        var $this = $(this);
        var valor = $this.val();
        $(".htmlruta").html(valor);
    });
});
$(document).on('focus', 'input[type=text]', function () {
    this.select();
});