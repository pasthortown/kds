//Version del modulo
var version = "1.0.0",
    promocion = {
        //iniciar
        component: "",
        componentInit: "",

        iniciar: function (comp, compInit) {
            this.component = comp;
            this.componentInit = compInit;
            document.getElementById(compInit).addEventListener('click', function () {
                $("#inputParametro").val("");
                var modal = document.getElementById(promocion.component);
                modal.className += " mostrarElemento";
                $("#mdlIntegracion").show();
                fn_alfaNumerico("#inputParametro");
                $('#btn_cancelar_teclado').attr('onclick', 'promocion.ocultar()');
                $('#btn_ok_teclado').attr('onclick', 'promocion.agregar()');
            }, false);
        },

        agregar: function () {
            $("#txt_lectorBarras").val($("#inputParametro").val());
            $("#txt_lectorBarras").trigger('change');
            $("#keyboard").hide();
            $("#mdlIntegracion").hide();
            fn_focusLector();
        },

        ocultar: function () {
            $("#keyboard").hide();
            $("#keyboard").hide();
            $("#mdlIntegracion").hide();
            fn_focusLector();
        }
    };