function cambiarPestana() {
  var x = document.getElementById("datosMapa");
  if (x.style.display === "none") {
    x.style.display = "block";
    $("#datosMapa").show();
    $("#datosClientePayphone").hide();
    $("#btn_opciones").hide();
    $("#btn_opcionesGuardar").hide();
    $("#btn_opCancelar").hide();
    document.getElementById("btn_map").textContent="arrow_back_ios_new";
  } else {
    $("#datosMapa").hide();
    $("#datosClientePayphone").show();
    $("#btn_opcionesGuardar").show();
    $("#btn_opCancelar").show();
    document.getElementById("btn_map").textContent="room";
  }
}

function fn_quitar_coordendas(){
  $("#coords-lat").val("");
  $("#coords-long").val("");
  localStorage.setItem("ls_latitud", $("#coords-lat").val());
  localStorage.setItem("ls_longitud", $("#coords-long").val());
}


function initAutocomplete() {
  var map = new google.maps.Map(document.getElementById("map"), {
    center: {
      lat: -0.215215,
      lng: -78.507675,
    },
    zoom: 13,
    mapTypeId: "roadmap",
    controlSize: 40,
  }); // Create the search box and link it to the UI element.

  var input = document.getElementById("pac_input");
  var searchBox = new google.maps.places.SearchBox(input);
  map.controls[google.maps.ControlPosition.TOP_LEFT].push(input); // Bias the SearchBox results towards current map's viewport.

  map.addListener("bounds_changed", function () {
    searchBox.setBounds(map.getBounds());
  });
  var markers = []; // Listen for the event fired when the user selects a prediction and retrieve
  // more details for that place.

  searchBox.addListener("places_changed", function () {
    var places = searchBox.getPlaces();

    if (places.length == 0) {
      return;
    } // Clear out the old markers.

    markers.forEach(function (marker) {
      marker.setMap(null);
    });
    markers = []; // For each place, get the icon, name and location.

    var bounds = new google.maps.LatLngBounds();
    places.forEach(function (place) {
      if (!place.geometry) {
        console.log("Returned place contains no geometry");
        return;
      }

      var icon = {
        url: place.icon,
        size: new google.maps.Size(71, 71),
        origin: new google.maps.Point(0, 0),
        anchor: new google.maps.Point(17, 34),
        scaledSize: new google.maps.Size(25, 25),
      }; // Create a marker for each place.

      markers.push(
        new google.maps.Marker({
          map: map,
          icon: icon,
          title: place.name,
          position: place.geometry.location,
        })
      );
      var direccion = place.name;
      var direccion2 = place.address_components[1].long_name;
      console.log();
      console.log(place.address_components[1].long_name);
      $("#pay_txtDireccion").val(direccion.replace("&", "y"));
      $("#pay_calleSecundaria").val(quitarAcentos(direccion2));
      //$("#pay_calleSecundaria").val( place.address_components[1].long_name);
      document.getElementById("coords-lat").value =
        place.geometry.location.lat();
      document.getElementById("coords-long").value =
        place.geometry.location.lng();

      if (place.geometry.viewport) {
        // Only geocodes have viewport.
        bounds.union(place.geometry.viewport);
      } else {
        bounds.extend(place.geometry.location);
      }
    });
    map.fitBounds(bounds);
  });

  google.maps.event.addListener(map, "click", function (event) {
    addMarker(event.latLng);
  });
  // Adds a marker to the map and push to the array.
  function addMarker(location) {
    cancelarTeclados();
    deleteMarkers();
    var marker = new google.maps.Marker({
      position: location,
      map: map,
    });
    markers.push(marker);
    console.log(location);
    document.getElementById("coords-lat").value = location.lat();
    document.getElementById("coords-long").value = location.lng();
  }

  // Sets the map on all markers in the array.
  function setMapOnAll(map) {
    for (var i = 0; i < markers.length; i++) {
      markers[i].setMap(map);
    }
  }

  // Removes the markers from the map, but keeps them in the array.
  function clearMarkers() {
    setMapOnAll(null);
  }

  // Shows any markers currently in the array.
  function showMarkers() {
    setMapOnAll(map);
  }

  function quitarAcentos(cadena) {
    const acentos = {
      á: "a",
      é: "e",
      í: "i",
      ó: "o",
      ú: "u",
      Á: "A",
      É: "E",
      Í: "I",
      Ó: "O",
      Ú: "U",
    };
    return cadena
      .split("")
      .map((letra) => acentos[letra] || letra)
      .join("")
      .toString();
  }

  // Deletes all markers in the array by removing references to them.
  function deleteMarkers() {
    clearMarkers();
    markers = [];
  }
}

$(document).ready(function () {
  initAutocomplete();
  var input = document.getElementById("pay_txtDireccion");
  var searchBox = new google.maps.places.SearchBox(input);
});
