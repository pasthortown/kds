<?php
///////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Andres Guerron  /////////////////////////
////////DESCRIPCION: clase que servira para presentar con/ ////////
////////estilos esfectos de proceso y bloqueo de pantalla /////////
///////FECHA CREACION: 27-12-2011 /////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 20/08/2012 //////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Uso del UI propio de JQuery //////
///////////////////////////////////////////////////////////////////

/******************************************************************
/	Para agregar esta clase se debe incluir en el Archivo Principal /
/									                                                /
/	jquery.js y                                                     /
/	calendario.js                                                   /
/	calendario.css                                                  /
 ******************************************************************/

?>
<script type="text/javascript">
    //Funcion que muestra animacion de cargando cuando se ejecuta un ajax automaticamente
    function loading() {
        $("#loading").ajaxStart(function() {
            $("#loading").dialog({
                maxHeight: 280,
                width: 300,
                title: 'Procesando...',
                resizable: false,
                position: "center",
                draggable: false,
                closeOnEscape: false,
                open: function(event, ui) {
                    $(".ui-dialog-titlebar-close").hide();
                },
                modal: true
            });
        }).ajaxStop(function() {
            $("#loading").dialog("destroy");
        });
    }

    function empiezaCronometro() {
        tiempoT = $("#tiempoEspera").val();
        var contador = tiempoT / 1000;
        cronometro = setInterval(function() {
                contador = contador - 1;
                $("#loading").dialog({
                    title: 'Esperando respuesta del Banco. Tiempo Restante: ' + contador,
                });
                if (contador == 2) {
                    detenerCronometro();
                }
            },
            1000);
    }
    //Funcion que muestra animacion de cargando con la opcion de mostrar y ocultarlo en cualquier lugar deseado
    function cargando(lc_estado, llamada = 0) {
        if (lc_estado == 0) {
            document.getElementById('countdown').style = 'display:none';
            let tag = document.getElementById('progress');
            if (tag) {
                tag.remove();
            }
            $("#loading").dialog({
                maxHeight: 270,
                width: 600,
                title: 'Esperando respuesta del Banco...',
                resizable: false,
                position: "center",
                draggable: false,
                closeOnEscape: false,
                open: function(event, ui) {
                    $(".ui-dialog-titlebar-close").hide();
                },
                modal: true
            });
            tiempoT = $("#tiempoEspera").val();
            var contador = tiempoT / 1000;
            const container = document.querySelector(".container");
            const courses = [{
                course: "HTML",
                percent: contador,
                color: "#77add4"
            }, ];
            courses.forEach((course) => {
                container.innerHTML += `<div class="progess-group" id="progress"><div class="circular-progress"><span class="course-value"style="font-size:70px; color:black">KFC</span></div></div>`;
                document.querySelector(".circular-progress").style.background = `conic-gradient(#e1e3e5 370deg, #fff 10deg)`;
            });

            const progressGroups = document.querySelectorAll(".progess-group");
            progressGroups.forEach((progress, index) => {
                let progressStartValue = 1;
                progessTimer = setInterval(() => { 
                    progress.querySelector(".circular-progress").style.background = `conic-gradient(${courses[index].color} ${5.12 * progressStartValue}deg, #e1e3e5 10deg)`;
                    progress.querySelector(".course-value").innerHTML = progressStartValue + '<span style="font-size:30px"> de ' + contador + '</span><p style="font-size:25px;color:#6c6f71">Segundos</p>';               
                    if(progressStartValue===contador+1)
                    {
                        fn_detenerProcesoPagoTarjeta(localStorage.getItem('dispositivo'), '5.' + llamada);
                    }
                    progressStartValue++;                   
                }, 1000);
            });
        }
        if (lc_estado == 1) {  
            clearInterval(TEMPORIZADORUNIRED);
            clearInterval(progessTimer);
            $("#loading").dialog("destroy");
        }
        if (lc_estado == 2) {
            //empiezaCronometro();
            tiempoT2 = $("#tiempoEspera").val();
            var contador2 = tiempoT2 / 1000;
            $("#loading").dialog({
                maxHeight: 270,
                width: 600,
                title: 'Existen transacciones pendientes, por favor espere',
                resizable: false,
                position: "center",
                draggable: false,
                closeOnEscape: false,
                open: function(event, ui) {
                    $(".ui-dialog-titlebar-close").hide();
                },
                modal: true
            });

            $("#countdown").countdown360({
                radius: 60.5,
                seconds: contador2,
                strokeWidth: 15,
                fillStyle: '#0276FD',
                strokeStyle: '#003F87',
                fontSize: 50,
                fontColor: '#FFFFFF',
                label: ["segs", "segs"],
                autostart: false,
                onComplete: function() {
                    console.log('completed');
                }
            }).start();
        }
    }

    function detenerCronometro() {
        // cargando(1);
        clearInterval(cronometro);
    }
</script>
<!-- Funcion que permite cargar el procesando en toda la pagina de acuerdo al nivel -->
<?php function fn_espera($nivel)
{
?>
    <style>
        * {
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            gap: 10px;
        }

        .progess-group {
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
        }

        .circular-progress {
            height: 240px;
            width: 240px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            transition: 0.2s;
        }

        .circular-progress::before {
            content: "";
            position: absolute;
            width: 210px;
            height: 210px;
            border-radius: 50%;
            background-color: #ffffff;
            image-rendering: crisp-edges;
        }

        .course-value {
            position: relative;
            color: #eb4d4b;
            font-size: 35px;
            font-weight: 500;
        }

        .text {
            margin-top: 18px;
            font-size: 25px;
            font-weight: 500;
            letter-spacing: 1px;
            color: white;
        }

        .load {
            display: flex;
            justify-content: center;
        }
    </style>

    <div id="loading" class="load" style="display:none;background:#FFF;text-align:center;overflow:hidden;">
        <div id="countdown"></div>
        <div class="container"></div>
    </div>

<?php
}
?>