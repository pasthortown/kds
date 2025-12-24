IDAplicaDescuentoActual=0;
$(document).ready(function(){
   
  $(".btn-estados").click(function(evt){
    var estadoSeleccionado=$(evt.target).data("estado");
    cargarAplicaDescuento(estadoSeleccionado);
  });
  
  $("#modal").on("hidden.bs.modal",function(){
      resetearValoresModal();
  }); 
  
  $("#btnGuardarAplicaDescuento").on("click",guardarAplicaDescuento);
  var $inputActivos=$(".btn-estados input[value='Activo']");
  $inputActivos.closest(".btn-estados").click();
});

function cargarAplicaDescuento(seleccion){
    send={
        "cargarAplicaDescuentos":1,
        "estado":seleccion
    };
    $.post("../admindescuentos/config_descuentos.php",send,function(datos) {
        $bodyTabla=$("#listaAplicaDescuentos tbody");
        $bodyTabla.empty();
        if(datos.str>0){
            for (var i = 0, len = datos.str; i < len; i++) {
                $bodyTabla.append(crearFilaTabla(datos[i]));
            }
            $("#listaAplicaDescuentos").dataTable();
        }
    },"json");
}

function crearFilaTabla(datosFila){
    var textoEstado=datosFila["estado"]==0?"Inactivo":"Activo";
    var $filaActual=$("<tr><td>"+datosFila["apld_descripcion"]+"</td><td>"+textoEstado+"</td></tr>");
    $filaActual.on("dblclick",function(){
        IDAplicaDescuentoActual=datosFila["IDTipoDescuento"];
        asignarDatosModal(datosFila);
        $("#modal .modal-title span").html("Modificar");
        $("#modal").modal("show"); 
    });
    $filaActual.on("click",function(){
        $("#listaAplicaDescuentos tr").removeClass("success");
        $(this).addClass("success");
    });
    return $filaActual;
}

function asignarDatosModal(datosFila){
    $("#inputDescripcionAplicaDescuentoo").val(datosFila["apld_descripcion"]);
    fn_asignaValorCheckbox("inputEstadoAplicaDescuento",datosFila["estado"]); 
}
function resetearValoresModal(){
    $("#modal .modal-title span").html("Nuevo");
    IDAplicaDescuentoActual=0;
    $("#inputDescripcionAplicaDescuentoo").val("");
    fn_asignaValorCheckbox("inputEstadoAplicaDescuento",1);    
}
function fn_asignaValorCheckbox(idElemento, valor) {
    var estado = (valor == 1) ? true : false;
    $("#" + idElemento).prop("checked", estado);
}

function guardarAplicaDescuento(){
    var descripcion=$("#inputDescripcionAplicaDescuentoo").val();
    var estado=fn_retornaValorCheckbox("inputEstadoAplicaDescuento");
    var send={
       "guardarAplicaDescuentos":1,
       "IDTipoDescuento":IDAplicaDescuentoActual,
       "apld_descripcion":descripcion,
       "estado":estado,
    };
    $.post("../admindescuentos/config_descuentos.php",send,function(datos){
        if (datos.Confirmar > 0) {
            $('#modal').modal('hide');
            cargarAplicaDescuento(0);
            fn_alerta("<b>Listo!</b> Tipo de Aplicación de Descuento guardada correctamente.", "success");
            return true;
        }else{
            fn_alerta("<b>Alerta!</b> No se guardó el Tipo de Descuento.", "danger");
            return false;
        }
    },"json");
}
function fn_retornaValorCheckbox(id) {
    if (null === document.getElementById(id))
        return 0;
    if ($("#" + id).is(":checked"))
        return 1;
    return 0;
}

function fn_alerta(mensaje, tipo) {
    setTimeout(function () {
        $.bootstrapGrowl(mensaje, {
            type: tipo,
            align: 'right',
            width: '500',
            allow_dismiss: false
        });
    }, 100);
}

