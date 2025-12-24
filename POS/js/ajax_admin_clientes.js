var $botonCrearPoliticaPantallaClientes = $("#crearPoliticaPantallaClientes");
var $listadoCamposTablaCliente = $("#listadoCamposTablaCliente");
var $camposActivosCliente = $("#listadoCamposActivos");
var $camposDisponiblesCliente = $(".campoTablaCliente");
var $camposActivosFormulario = $("#camposActivos");
var $camposInactivosFormulario = $("#listadoCamposInactivos");
var $camposInactivos = $(".campoInactivoFormulario");

var crearPoliticaPantallaClientes = function ($this) {
    $botonCrearPoliticaPantallaClientes.prop('disabled', true);
    var $peticionAjax = $.ajax({
        url: "../adminclientes/config_admin_clientes.php",
        method: "POST",
        data: {
            crearPoliticaPantallaClientes: 1
        },
        success: function (data) {
            var sitio = obtenerRuta();

            var ruta = "/" + sitio + "/adminclientes/adminClientes.php";
            /*
            $("#incluirPagina").load(ruta);
            $("#incluirPagina").empty();
             */
            location.reload();
        },
        complete: function () {
            $botonCrearPoliticaPantallaClientes.prop('disabled', false);
        },
        error: function (err) {
            alertify.error("No se pudo crear la política: " + err.statusText);
        }
    });
};

function obtenerRuta() {
    var url = window.location.href;
    var partes = url.split('/');

    // Verificar si hay un segmento adicional en la ruta en Windows
    if (partes[3] !== '') {
        // Estamos en Windows
        return partes.slice(3, partes.indexOf("mantenimiento") + 1).join('/');
    } else {
        // Estamos en Linux
        return "mantenimiento";
    }
}

$(document).ready(function () {
    $("#listadoCamposTablaCliente ul, li").disableSelection();
    $botonCrearPoliticaPantallaClientes.on("click", crearPoliticaPantallaClientes);

    $camposActivosCliente.sortable({
        revert: true,
        placeholder: "ui-state-highlight",
        forcePlaceholderSize: true
    });

    $camposDisponiblesCliente.draggable({
        revert: "invalid",
        cursor: "pointer"
    });

    $camposInactivos.draggable({
        revert: "invalid",
        cursor: "pointer"
    });

    $camposActivosFormulario.droppable({
        accept: ".campoTablaCliente, .campoInactivoFormulario",
        drop: function (event, ui) {
            $item = ui.draggable;
            agregarCampoActivo($item);
        }
    });
    $camposActivosCliente.on("click",".btnOrdenarSubir",function(){
        var $el=$(this).closest(".campoActivoFormularioClientes");
         $el.fadeOut(500, function(){
            $el.insertBefore($el.prev());
            $el.fadeIn(500);
        });
    });
    $camposActivosCliente.on("click",".btnOrdenarBajar",function(){
        var $el=$(this).closest(".campoActivoFormularioClientes");
        $el.fadeOut(500, function(){
            $el.insertAfter($el.next());
            $el.fadeIn(500);
        });
    });

    $camposActivosCliente.on("click",".btnDesactivarCampo",function(){
        var $el=$(this).closest(".campoActivoFormularioClientes");
        var dataElemento=$el.data();
        $el.fadeOut(500, function(){
            var $nuevoElementoInactivo=creardivCampoInactivo(dataElemento);
            $camposInactivosFormulario.append($nuevoElementoInactivo);
            $nuevoElementoInactivo.show(500);
        });
    });

    $("#btnguardarCamposFormulario").on("click",function(){
        var $campos=$camposActivosCliente.find(".campoActivoFormularioClientes");
        var $camposInactivos=$camposInactivosFormulario.find(".campoInactivoFormulario");
        var arrayValoresActivos=[];
        var arrayValoresInactivos=[];
        $campos.each( function( index, element ){
            var valoresElemento=valoresCampoActivo(element);
            if(false===valoresElemento) {
                arrayValoresActivos=[];
                return false;
            }
            valoresElemento.orden=(index+1);
            arrayValoresActivos.push(valoresElemento);
        });

        if(empty(arrayValoresActivos)) {
            return false;
        }

        $camposInactivos.each( function( index, element ){
            var nombreCampo=$(element).html().trim();
            var valoresElemento={
                nombreCampo:nombreCampo
            };
            arrayValoresInactivos.push(valoresElemento);
        });


        $.ajax({
            type: "POST",
            url: "../adminclientes/config_admin_clientes.php",
            data: {
                guardarValoresCamposFormulario:1,
                valoresActivos:arrayValoresActivos,
                valoresInactivos:arrayValoresInactivos
            },
            success: function (datos) {
                if(0===datos.estado) alertify.error(datos.mensaje);
            },
            complete: function () {
                $botonCrearPoliticaPantallaClientes.prop('disabled', false);
            },
            error: function (err) {
                alertify.error("No se pudo Guardar las políticas de campos: "+err);
            }
        });


    });

    function valoresCampoActivo(licampo){
        var $liCampo=$(licampo);
        var nombreCampo=$liCampo.find(".label").html().trim();
        var alias=$liCampo.find(".inputAlias").val().trim();
        if(empty(alias)){
            alertify.alert("Ningún Alias puede estar vacío");
            return false;
        }
        var obligatorio=(true===$liCampo.find(".chkObligatorio").is(':checked'))?'1':'0';
        var resultado = {
            "campo":nombreCampo,
            "alias": alias,
            "orden":1,
            "obligatorio":obligatorio,
            "activo":'1'
        };
        return resultado;
    }
    function agregarCampoActivo($item) {
        $item.fadeOut();
        var htmlItem = creardivCampoActivo($item);
        $camposActivosCliente.append(htmlItem);

    }

    function creardivCampoActivo($item) {
        var nombreCampo=$item.html().trim();
        var obligatorio = empty($item.data().obligatorio)?"":$item.data().obligatorio;
        var checkedObligatorio=(obligatorio==1?"checked":"");
        var alias = empty($item.data().alias)?"":$item.data().alias;
        var htmlDiv = "<li class='campoActivoFormularioClientes' data-campo='"+nombreCampo+"' data-alias='"+alias+"' data-obligatorio='"+obligatorio+"'>"+
            "<div class='panel panel-primary'>"+
            "<div class='panel-body'>" +
            "   <div class='row'>" +
            "       <div class='col-md-2'><h5><span class='label label-primary'>" + $item.html() + "</span></h5></div>" +
            "       <div class='col-md-7'>" +
            "       <form class='form-inline'>" +
            "           <div class='form-group'>" +
            "               <label for='alias'>Alias:</label>" +
            "               <input type='email' class='form-control inputAlias' name='alias' placeholder='Nombre para mostrar' value='"+alias+"'>" +
            "           </div>" +
            "           <div class='checkbox'>" +
            "               <label>" +
            "                   <input type='checkbox' class='chkObligatorio' "+checkedObligatorio+"> Obligatorio" +
            "               </label>" +
            "           </div>" +
            "       </form>" +
            "       </div>" +
            "       <div class='col-md-s3'>"+
            "           <div class='btn-group' role='group' aria-label='...'>"+
            "               <button type='button' class='btn btn-default btnOrdenarSubir'><span class='glyphicon glyphicon-chevron-up' aria-hidden='true'></span></button>"+
            "               <button type='button' class='btn btn-default btnOrdenarBajar'><span class='glyphicon glyphicon-chevron-down' aria-hidden='true'></span></button>"+
            "               <button type='button' class='btn btn-danger btnDesactivarCampo'><span class='glyphicon glyphicon-remove-circle' aria-hidden='true'></span></button>"+
            "       </div>"+
            "       </div>"+
            "   </div>" +
            "</div>" +
            "</li>";
        return htmlDiv;
    }

    function creardivCampoInactivo(dataCampo) {
       var htmlRetorno="<li class='list-group-item list-group-item-warning campoInactivoFormulario' style='z-index: 10;display:none' data-campo='"+dataCampo.campo+"' data-alias='"+dataCampo.alias+"' data-obligatorio='"+dataCampo.obligatorio+"'>"+dataCampo.campo+"</li>";
       return $(htmlRetorno).draggable();
    }
    function empty( val ) {
        if (val === undefined)
            return true;
        if (typeof (val) == 'function' || typeof (val) == 'number' || typeof (val) == 'boolean' || Object.prototype.toString.call(val) === '[object Date]')
            return false;
        if (val == null || val.length === 0)        // null or 0 length array
            return true;
        if (typeof (val) == "object") {
            // empty object
            var r = true;
            for (var f in val)
                r = false;
            return r;
        }
        return false;
    }
});
