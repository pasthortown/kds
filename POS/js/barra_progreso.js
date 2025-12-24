$(document).ready( 
    function () {
        modalBarraProgreso(0);
    }
);

function modalBarraProgreso( onOff ) {
    if ( onOff === 1 ) {
        barraProgreso(0, 'Estamos por iniciar…');
        $('#modalBarraProgreso').modal('show');
    } else {
        $('#modalBarraProgreso').modal('hide');
    }
}

function barraProgreso( grado, titulo ) {
    if( grado >= 0 && grado <= 360 ) {
        if( grado >= 0 && grado <= 180 ) { 
            document.documentElement.style.setProperty('--rotate-loading-2', '0deg');
            document.documentElement.style.setProperty('--rotate-loading-1', grado + 'deg');
        } else {
            document.documentElement.style.setProperty('--rotate-loading-1', '180deg');
            document.documentElement.style.setProperty('--rotate-loading-2', ( grado - 180 ) + 'deg');
        }

        document.getElementById('progress-value').innerHTML = parseInt( ( grado * 100 ) / 360 ) + '%';
        document.getElementById('modalBarraProgresoTituloLargo').innerHTML = titulo;
    }
}
