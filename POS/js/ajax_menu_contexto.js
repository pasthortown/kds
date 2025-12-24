$(function () {



    /**************************************************
     * Context-Menu with Sub-Menu
     **************************************************/
    $.contextMenu({
        selector: '.context-menu-sub',
        callback: function (key, options) {
            var m = "clicked: " + key;
            //window.console && console.log(m) || alert(m); 
            switch (key) {
                case 'edit':

                    var IDMesa = $('.context-menu-active').attr('id');
                    var rst_id = $('#restaurante').val();
 
                    var send = {"VerificarMisMesa": 1};
                    send.mesa_id = IDMesa;
                    send.rst_id = rst_id;

                    $.ajax({async: false, type: "POST", dataType: 'json', contentType: "application/x-www-form-urlencoded", url: "../adminmesas/config_mesa.php", data: send,
                        success: function (datos)
                        {
                            if (datos.str > 0) {
                                if (datos[0]['mensaje'] === 'Disponible') {

                                    var text_mesa = $('.context-menu-active').children("label").html();
                                    var id_tipo_mesa = $('.context-menu-active').attr("tmes_id"); //tmes_id
                                    document.getElementById('selec_tipo').value = id_tipo_mesa;
                                    $("#selec_tipo").trigger("chosen:updated");
                                    $('#nombreMesa').val(text_mesa);
                                    $("#check_activo").prop("checked", "checked");
                                    $("#codigomesa").val($('.context-menu-active').attr("id"));
                                    $('#Modal_modificarrmesa').modal('show');

                                } else
                                {
                                    $('#' + IDMesa).children('img').attr("src", datos[0]['ruta']);
                                    alertify.alert("Actualmente ésta mesa está en uso, por favor intente en un momento.");
                                }
                            }


                        }
                    });






//                    var estado_mesa = $('.context-menu-active').children("img").attr("src");
//                    if (estado_mesa.substring(estado_mesa.lastIndexOf("/")) === "/Disponible.png" || estado_mesa.substring(estado_mesa.lastIndexOf("/")) === "/Activo.png") {
//                        var text_mesa = $('.context-menu-active').children("label").html();
//                        var id_tipo_mesa = $('.context-menu-active').attr("tmes_id"); //tmes_id
//                        document.getElementById('selec_tipo').value = id_tipo_mesa;
//                        $("#selec_tipo").trigger("chosen:updated");
//                        $('#nombreMesa').val(text_mesa);
//                        $("#check_activo").prop("checked", "checked");
//                        $("#codigomesa").val($('.context-menu-active').attr("id"));
//                        $('#Modal_modificarrmesa').modal('show');
//                    } else {
//                        alertify.alert("Actualmente ésta mesa está en uso, por favor intente en un momento.");
//                    }
                    break;
            }
        },
        items: {
            "edit": {"name": "Modificar", "icon": "edit"},
            "sep1": "---------"
//            "quit": {"name": "Salir", "icon": "quit"},
//            "sep2": "---------",//
//            "fold1": { 
//            
//                "name": "Sub group", 
//                "items": {
//                    "fold1-key1": {"name": "Foo bar"},
//                    "fold2": {
//                        "name": "Sub group 2", 
//                        "items": {
//                            "fold2-key1": {"name": "alpha"},
//                            "fold2-key2": {"name": "bravo"},
//                            "fold2-key3": {"name": "charlie"}
//                        }
//                    },
//                    "fold1-key3": {"name": "delta"}
//                }
//            }
        }
    });
});


 