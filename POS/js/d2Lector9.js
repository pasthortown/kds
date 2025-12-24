
var Respuesta = -1;
var Mensaje = "-1:ERROR, BROWSER NO IMPLEMENTADO";
var ConFoto = 0;
var Operacion = 2;
var BioOperacion = 2;

function padleft(val, ch, num) {
    var re = new RegExp(".{" + num + "}$");
    var pad = "";
    if (!ch) ch = " ";
    do {
        pad += ch;
    } while (pad.length < num);
    return re.exec(pad + val)[0];
}


function TipoNav() {
    var tipoNav = 0;
    var agent = navigator.userAgent;
    if (agent.indexOf("MSIE") > -1)
        tipoNav = 1;
    else
        if (agent.indexOf("Firefox") > -1)
            tipoNav = 2;
        else
            if (agent.indexOf("Chrome") > -1)
                tipoNav = 3;
    return tipoNav;
}

function CapturaHuellaCr(identificacion, posicion, dispositivo) {
    var tipo = TipoNav();
    posicion = padleft(posicion, '0', 2);
    dispositivo = padleft(dispositivo, '0', 2);
    Respuesta = -2;
    Mensaje = "-2:ERROR DE INVOCACION";
    if (tipo == 1 || tipo == 0) {
        CapturaIE(identificacion, posicion, dispositivo)
    }
    else
        if (tipo == 2 || tipo == 3) {
            CapturaFX(identificacion, posicion, dispositivo, tipo, ConFoto);
        }
    return true;
}

function CapturaIE(identificacion, posicion, dispositivo) {
    try {
        var x = new ActiveXObject("D2webFTRMF.d2ftrMF.1");
        x.Identificacion = identificacion;
        Respuesta = "-1";
        Mensaje = "NO HAY DATOS!";
        if (dispositivo == 2)
            x.capturaHuella(posicion, 1, Operacion, ConFoto); //0 sin fotografia
        else if (dispositivo == 4)
            x.capturaHuella(posicion, 2, 1, ConFoto); //0 sin fotografia
        else if (dispositivo == 7)
            x.capturaRostro(1, 1);
        else {
            Respuesta = -4;
            Mensaje = "-4:DISPOSITIVO NO IMPLEMENTADO";
            return Respuesta;
        }
        Respuesta = x.respuesta;
        if (Respuesta == 0) {
            if (x.huellaWSQ1 != 'NO EXISTEN DATOS') {
                if (dispositivo == 4)
                    Mensaje = Respuesta + ':' + x.huellaWSQ1 + "@WSQ@" + x.huellaWSQ2 + "@WSQ@" + x.huellaWSQ3 + "@WSQ@" + x.huellaWSQ4 + "@WSQ@" + x.huellaWSQ5 + "@WSQ@" + x.huellaWSQ6 + "@WSQ@" + x.huellaWSQ7 + "@WSQ@" + x.huellaWSQ8 + "@WSQ@" + x.huellaWSQ9 + "@WSQ@" + x.huellaWSQ10
                else
                    Mensaje = Respuesta + ':' + x.huellaWSQ1;
            }
            else {
                Respuesta = -9;
                Mensaje = x.huellaWSQ1;
            }
        }
    } catch (err) {
        Mensaje = err.description;
        Respuesta = -8;
    }
    return Respuesta;
}

function CapturaFX(ident, posicion, dispositivo, tipo, tomaFoto) 
{
    var milliseconds = new Date().getTime();
    ident = milliseconds + "." + ident + posicion + dispositivo + tipo + tomaFoto + BioOperacion;   
    try {
        new Ajax.Request("http://127.0.0.1:6111/" + ident, {
            method: "GET",
            asynchronous: false,
            onComplete: function (response) {
                result = response.responseText;
                try {
                    Respuesta = parseInt(result.substr(0, 2));
                    Mensaje = Respuesta + result.substring(2, result.length);
                    if (isNaN(Respuesta)) {
                        Respuesta = -5;
                        Mensaje = "-5:ERROR DE INVOCACION";
                    }
                } catch (err) {
                    Respuesta = -7;
                    Mensaje = err.Description;

                }
            }
        });
    } catch (err) {
        Respuesta = -6;
        Mensaje = err.Description;
    }
}