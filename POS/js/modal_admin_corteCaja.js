// Get the modal
var modal = document.getElementById("comandasMotorizados");

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
//btn.onclick = function() {
//  modal.style.display = "block";
//}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  alertify.confirm( `Estas seguro que deseas terminar la revisión de las comandas?` , function ( e ) {
    if ( e ) {
      revisionComandas = true;
      modal.style.display = "none";
      $(".jb-shortscroll-wrapper").css( "display", "block" );
    }
  });
}

// When the user clicks anywhere outside of the modal, close it
//window.onclick = function(event) {
//  if (event.target == modal) {
//    modal.style.display = "none";
//    $(".jb-shortscroll-wrapper").css( "display", "block" );
//  }
//}