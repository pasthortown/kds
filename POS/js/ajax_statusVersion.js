
/**
 * @fileoverview    Script que permite realizar las validaciones de la version del maxpoint
 * y gestiona la actualizacion con el borrado de la cahce
 * @version         1.0
 *
 * @author          Luis Coronel <lufecoro@outlook.com>
 * @copyright       GrupoKFC
 *
 * History
 * v1.0 – Se realizo la validacion para realizar el borrado de la cache de las estaciones.
 * ----
*/

let url = '';
let isUrlAdmin = false;
let estacionAdmin = '';
let STATUS_SERVICES = false; //Variable que permite validar si el api esta OK

$(document).ready(function() {

    url = window.location.origin +'/'+window.location.pathname.split('/')[1];
    isUrlAdmin = (window.location.pathname.includes('mantenimimento')) ? true : false;
    estacionAdmin = window.location.origin+window.location.pathname;
    //getActualizacionPendiente();
    mostralModalAplicado();
    verificaStatusServices();
    
});

/**
 * Funcion que carga la version actual en sesion
 */

function buildAlertActualizacion(){
    let timerInterval;
    Swal.fire({
      icon: "warning",
      title: "¡Atención, el sistema se está actualizando!",
      html: "Tiempo restante <b></b> segundos.",
      timer: 4000,
      timerProgressBar: true,
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
        const timer = Swal.getPopup().querySelector("b");
        timerInterval = setInterval(() => {
          timer.textContent = `${Math.floor(1+Swal.getTimerLeft()/1000)}`;
        }, 100);
      },
      willClose: () => {
        clearInterval(timerInterval);
        clearCache();
      }
    }).then((result) => {
      /* Read more about handling dismissals below */
      if (result.dismiss === Swal.DismissReason.timer) {
        console.log("I was closed by the timer");
      }
    });
}

/**
 * Funcion que limpia la cache del navegador al dar clic en Aceptar
 */
function aceptacionLimpiadoCache(idEstacion){
  var limpiaCacheEstacion = { "limpiaCacheEstacion": 1 };
  send = limpiaCacheEstacion;
  send.idEstacion = idEstacion;
  $.ajax({
    type: "POST",
    url: url + "/clases/statusVersion/config_statusVersion.php",
    data: send,
    success: function(response) {
      Swal.fire({
        title: "Excelente!",
        text: "Borrado de Cache satisfactoriamente!",
        icon: "success",
        allowOutsideClick: false,
      }).then((resultConfirmacion) => {
        if(resultConfirmacion.isConfirmed){
          clearCache();
        }
      });
    },
    failure: function (response) {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Lo sentimos ocurrio un error interno!",
      });
    }
  });
}

/**
 * Funcion que valida si exste una actualizacion pendiente
 */
async function getActualizacionPendienteAux(){
  return new Promise(function (resolve, reject) {
    var actualizacionPendiente = false;
    var validaLimpiaCache = { "validaLimpiaCache": 1 };

    send = validaLimpiaCache;
    send.ip = ($('#hid_ip').length) ? $("#hid_ip").val() : '';
    send.isUrlAdmin = isUrlAdmin;
    send.estacionAdmin = estacionAdmin;

    $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: url+'/clases/statusVersion/config_statusVersion.php',
        data: send,
        success: function (datos) {
          var result;
          if (datos.registros > 0) {
              if (datos[0].aplica_limpiado_cache == '1') {
                actualizacionPendiente = true;
              }
              result = {
                "estado": 200,
                "actualizacionPendiente": actualizacionPendiente,
              }
          }else{
            result = {
              "estado": 500,
              "mensaje": "Error de conexion."
            }
          }
          resolve(codificacion);
        }
        ,
        error: function (e) {
            resolve({
                "estado": 500,
                "mensaje": "Error de conexion."
            });
        }
    });
  });
}


/**
 * Funcion que valida si exste una actualizacion pendiente
 */
function getActualizacionPendiente(){
    var actualizacionPendiente = false;
    if(!STATUS_SERVICES){
      actualizacionPendiente = false
    }else{
      actualizacionPendiente = false;
      var validaLimpiaCache = { "validaLimpiaCache": 1 };

      send = validaLimpiaCache;
      send.ip = ($('#hid_ip').length) ? $("#hid_ip").val() : '';
      send.isUrlAdmin = isUrlAdmin;
      send.estacionAdmin = estacionAdmin;
      $.ajax({
        async: false,
        type: "GET",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url: url+'/clases/statusVersion/config_statusVersion.php',
        data: send,
        success: function (datos) {
          console.log(datos);
          if (datos.registros > 0) {
              if (datos[0].aplica_limpiado_cache) {
                actualizacionPendiente = true;
              }
          }
        }
      });
    }
    
    return actualizacionPendiente;
}

/**
 * Funcion que borra la cache del navegador mediante jQuery
 */
function clearCache() {
 
  var limpiarDataCache = { "limpiarDataCache": 1 };
  send = limpiarDataCache;
  $.getJSON(url+"/clases/statusVersion/config_statusVersion.php", send, function(datos) {
   
  });
}

function mostralModalAplicado() {
  aplica = ($('#aplicoBorradoCache') !== null) ? $('#aplicoBorradoCache').val() : '0';
  if(aplica){
    Swal.fire({
      customClass: {
        confirmButton: "btn btn-primary btn-continuar-by-actualizacion",
      },
      buttonsStyling: false,
      title: "Excelente!",
      html: "<h3>Se actualizó el sistema correctamente.</h3>",
      icon: "success",
      confirmButtonText: "<h5>Continuar<h5>",
      allowOutsideClick: false,
    });
  }
}

/**
 * Funcion que valida si el servicio(API), esta respondiendo
 */
function verificaStatusServices() {
 
  var verificaStatusServices = { "verificaStatusServices": 1 };
  send = verificaStatusServices;
  send.ip = ($('#hid_ip').length) ? $("#hid_ip").val() : '';
  $.getJSON(url+"/clases/statusVersion/config_statusVersion.php", send, function(datos) {
   console.log(datos);
    if(datos.success){
      STATUS_SERVICES = datos.success;
    }else{
      STATUS_SERVICES = false;
    }
  });
}

