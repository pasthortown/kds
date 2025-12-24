IDTipoDescuentoActual=0;
$(document).ready(function(){
   
  $(".btn-estados").click(function(evt){
    var estadoSeleccionado=$(evt.target).data("estado");
    cargarTiposDescuento(estadoSeleccionado);
  });
  
  $("#modal").on("hidden.bs.modal",function(){
      resetearValoresModal();
  }); 
  
  $("#btnGuardarTipoDEscuento").on("click",guardarTipoDescuento);
  var $inputActivos=$(".btn-estados input[value='Activo']");
  //$inputActivos.closest(".btn-estados").click();
  $inputActivos.closest(".btn-estados").click();
  // cargarTiposDescuento($btnDescuentosActivos.data("estado"));
});

function cargarTiposDescuento(seleccion){
    send={
        "cargarTiposDescuentos":1,
        "estado":seleccion
    };
    $.post("../admindescuentos/config_descuentos.php",send,function(datos) {
        $bodyTabla=$("#listaTiposDescuentos tbody");
        $bodyTabla.empty();
        if(datos.str>0){
            for (var i = 0, len = datos.str; i < len; i++) {
                $bodyTabla.append(crearFilaTabla(datos[i]));
            }
            $("#listaTiposDescuentos").dataTable();
        }
    },"json");
}

function crearFilaTabla(datosFila){
    var textoEstado=datosFila["estado"]==0?"Inactivo":"Activo";
    var $filaActual=$("<tr><td>"+datosFila["tpd_descripcion"]+"</td><td>"+textoEstado+"</td></tr>");
    $filaActual.on("dblclick",function(){
          $("#modal .modal-title span").html("Modificar");
        IDTipoDescuentoActual=datosFila["IDTipoDescuento"];
        asignarDatosModal(datosFila);
        $("#modal").modal("show"); 
    });
    $filaActual.on("click",function(){
        $("#listaTiposDescuentos tr").removeClass("success");
        $(this).addClass("success");
    });
    return $filaActual;
}

function asignarDatosModal(datosFila){
    $("#inputDescripcionTipoDescuento").val(datosFila["tpd_descripcion"]);
    fn_asignaValorCheckbox("inputEstadoTipoDescuento",datosFila["estado"]); 
}
function resetearValoresModal(){
     $("#modal .modal-title span").html("Nuevo");
    IDTipoDescuentoActual=0;
    $("#inputDescripcionTipoDescuento").val("");
    fn_asignaValorCheckbox("inputEstadoTipoDescuento",1);    
}
function fn_asignaValorCheckbox(idElemento, valor) {
    var estado = (valor == 1) ? true : false;
    $("#" + idElemento).prop("checked", estado);
}

function guardarTipoDescuento(){
    /*
     *     $lc_condiciones["IDTipoDescuento"]=$_POST["IDTipoDescuento"];
    $lc_condiciones["tpd_descripcion"]=$_POST["tpd_descripcion"];
    $lc_condiciones["estado"]=$_POST["estado"];
     */
    var descripcion=$("#inputDescripcionTipoDescuento").val();
    var estado=fn_retornaValorCheckbox("inputEstadoTipoDescuento");
    var send={
       "guardarTipoDescuentos":1,
       "IDTipoDescuento":IDTipoDescuentoActual,
       "tpd_descripcion":descripcion,
       "estado":estado,
    };
    $.post("../admindescuentos/config_descuentos.php",send,function(datos){
        if (datos.Confirmar > 0) {
            $('#modal').modal('hide');
            cargarTiposDescuento(0);
            fn_alerta("<b>Listo!</b> Tipo de Descuento guardado correctamente.", "success");
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

