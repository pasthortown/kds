function telegram(json,accion) {
    var send; 
    send = {};//
    send.metodo='telegram';
    send.json=json;
    send.token=datos_telegram(1,accion);
    send.chatid=datos_telegram(2,accion);
    var status=0;
    console.log(accion);
    if(accion==1){
    var url1="../../serviciosweb/interface/config_cliente_interface_telegram.php";}
    else{
    var url1="../serviciosweb/interface/config_cliente_interface_telegram.php";}
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url:url1,
        data: send,
        success: function (datos) {
            status=datos;
        }
    });   
    
    return status;
}
function datos_telegram(dato,accion) {
    var send;    
    send = {"datos_telegram": 1};//
    send.dato=dato;
    var status=null;
    if(accion==1){
        var url1="../adminDesmontarCajero/config_adminDesmontarCajero.php";}
        else{
        var url1="../mantenimiento/adminDesmontarCajero/config_adminDesmontarCajero.php";}
        
    $.ajax({
        async: false,
        type: "POST",
        dataType: "json",
        contentType: "application/x-www-form-urlencoded",
        url:url1,
        data: send,
        success: function (datos) {
         status=datos[0]['datos'];
        }
    });   
    return status;
}

