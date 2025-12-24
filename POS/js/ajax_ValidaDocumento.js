/* Autor: Juan Esteban Canelos
 * Fecha: 21/08/2018
 */

function fn_validarDocumento(numero) {
    var longitud = numero.length;
    var valido = false;

    if (/^\d+$/.test(numero)) {
        if (longitud === 10 || longitud === 13) {
            var digitos = numero.split("").map(Number);
            var codProvincia = digitos[0] * 10 + digitos[1];

            //1-24: provincias
            //30: ecuatorianos registrados en el exterior
            if ((codProvincia > 0 && codProvincia <= 24) || (codProvincia === 30)) {
                var RUC = "";
                var digitoVerificador;

                //El único mecanismo de validación formalmente aceptado para personas naturales es el dígito verificador
                //Por ende, para toda persona natural se aplica algoritmo verificador de módulo 10
                //De igual manera a los RUCs cuyo tercer dígito está entre 0 y 5
                if (longitud === 10 || digitos[2] >= 0 && digitos[2] <= 5) {
                    if (longitud === 13) {
                        for (var i = 0; i < 3; i++) {
                            RUC = String(digitos.pop()) + RUC;
                        }
                    }
                    if (RUC === "" || RUC === "001") {
                        digitoVerificador = digitos.pop();
                        valido = (modulo10(digitos) === digitoVerificador);
                    }

                //Para RUCs cuyo tercer dígito es 6 se aplica algoritmo verificador de módulo 11
                //El dígito verificador es el noveno
                } else if (digitos[2] === 6) {
                    for (var i = 0; i < 4; i++) {
                        RUC = String(digitos.pop()) + RUC;
                    }
                    if (RUC === "0001") {
                        digitoVerificador = digitos.pop();
                        valido = (modulo11(digitos) === digitoVerificador);
                    }

                //Para RUCs cuyo tercer dígito es 9 se aplica algoritmo verificador de módulo 11
                } else if (digitos[2] === 9) {
                    for (var i = 0; i < 3; i++) {
                        RUC = String(digitos.pop()) + RUC;
                    }
                    if (RUC === "001") {
                        digitoVerificador = digitos.pop();
                        valido = (modulo11(digitos) === digitoVerificador);
                    }
                }
            }
        }
    }

    return valido;
}

//Algoritmo "Módulo 10"
function modulo10(digitos) {
    return digitos.reduce(function (total, valorActual, i) {
        return total - ((valorActual * (2 - i % 2)) % 9) - (9 * (valorActual === 9));
    }, 1000) % 10;
}

//Algoritmo "Módulo 11"
function modulo11(digitos) {
    var total = 0;
    var longitud = digitos.length;
    var coeficientes = [4, 3, 2, 7, 6, 5, 4, 3, 2];
    if (longitud === 8) {
        coeficientes.shift();
    }
    for (var i = 0; i < longitud; i++) {
        total += (digitos[i] * coeficientes[i]);
    }

    return 11 - (total % 11) - (11 * (total % 11 === 0));
}