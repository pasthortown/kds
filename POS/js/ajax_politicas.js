function obtenerRuta() {
    var url = window.location.href;
    var partes = url.split('/');

    // Verificar si hay un segmento adicional en la ruta en Windows
    if (partes[3] !== '') {
        // Estamos en Windows
        return partes.slice(3, partes.indexOf("mantenimiento") + 1).join('/');
    } else {
        // Estamos en Linux
        return "mantenimiento";
    }
}

var sitio2 = obtenerRuta();

var sitio = window.location;
    sitio = sitio.toString();//.split('/');  
    sitio = sitio.split('/');
    
var ruta = sitio[0] + '//' + sitio[1] + sitio[2] + sitio[1] + '/' + sitio2  + '/adminPoliticas/configpoliticas.php';

var app = angular.module("app", [])
        .run(function ($http, $rootScope) {

            $rootScope.modelColeccion = "Cadena";
            $rootScope.colecciones = {};
            $rootScope.parametros = {};
            $rootScope.coleccionSelected = {};
            $rootScope.parametroSelected = {};
            $rootScope.cloneColeccion = {};
            $rootScope.cloneParametro = {};
            $rootScope.modalColeccion = {display: "none"};
            $rootScope.modalParametro = {display: "none"};
            $rootScope.cangando = 'true';
            $rootScope.accion = "";
            $rootScope.tablasIntegracion = {};
            $rootScope.tiposDatos = {};
            $rootScope.registrosIntegracion = [{"id": null, "descripcion": "-- Seleccione una Fila --"}];
            $rootScope.headers = {'Accept': 'application/json, text/javascript', 'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8; application/json'};

            $rootScope.cargarRegistrosTablaIntegracionConfigurada = function (parametro) {
                var param = {};
                param.metodo = "cargarIdTablasIntegracion";
                param.tabla = parametro;
                $http({
                    method: 'POST',
                    url: ruta,
                    data: param,
                    headers: $rootScope.headers
                }).then(function successCallback(response) {
                    $rootScope.registrosIntegracion = response.data;
                }, function errorCallback(response) {
                    alert("Mensaje Tabla Integracion: " + response.statustext);
                });
            };

            $rootScope.cargarTiposDatosParametros = function (parametro) {
                var param = {};
                param.metodo = "cargarTiposDatosParametros";
                param.tabla = parametro;
                $http({
                    method: 'POST',
                    url: ruta,
                    data: param,
                    headers: $rootScope.headers
                }).then(function successCallback(response) {
                    $rootScope.tiposDatos = response.data;
                }, function errorCallback(response) {
                    alert("Mensaje Tabla Integracion: " + response.statustext);
                });
            };
            
            $rootScope.openCargando = function(){
                $rootScope.cangando = true;
            };
            
            $rootScope.closeCargando = function(){
                $rootScope.cangando = false;
            };
            
            $rootScope.cargarTiposDatosParametros();
        });
//-- GRID COLECCIONES
app.controller("pntColeccion", function ($scope, $http, $rootScope) {

    $scope.busquedaColeccion = "";

    $scope.change = function () {
        $scope.datosColeccion("Cadena");
    };

    $scope.cargarColeccion = function (parametro) {
        if ($rootScope.modelColeccion !== parametro) {
            $rootScope.coleccionSelected = {};
            $rootScope.cloneColeccion = {};
            $rootScope.parametros = {};
            $rootScope.modelColeccion = parametro;
            $scope.datosColeccion(parametro);
        }
    };

    $scope.datosColeccion = function (parametro) {
        $rootScope.openCargando();
        $http({
            method: 'POST',
            url: ruta,
            data: {metodo: "cargarPoliticasPorCadena", coleccion: parametro},
            headers: $rootScope.headers
        }).then(function successCallback(response) {
            $rootScope.colecciones = response.data;
            $rootScope.closeCargando();
        }, function errorCallback(response) {
            $rootScope.closeCargando();
            alert("Mensaje Datos: " + response.statustext);
        });
    };

    $scope.openModalUpdateColeccion = function () {
        $rootScope.accion = "update";
        $rootScope.modalColeccion = {"display": "block"};
        if ($rootScope.coleccionSelected.descripcionIntegracion !== null) {
            $rootScope.cargarRegistrosTablaIntegracionConfigurada($rootScope.coleccionSelected.descripcionIntegracion);
        }
    };

    $scope.selectModulos = function (parametro) {
        $http({
            method: 'POST',
            url: ruta,
            data: {metodo: "cargarModulosConfiguracion", coleccion: parametro},
            headers: $rootScope.headers
        }).then(function successCallback(response) {
            $scope.modulos = response.data;
        }, function errorCallback(response) {
            alert("Mensaje Modulos: " + response.statustext);
        });
    };

    $scope.changeColeccion = function () {
        $rootScope.openCargando();
        var param = $rootScope.cloneColeccion;
        param.metodo = $rootScope.accion + "Politica";
        param.coleccion = $rootScope.modelColeccion;
        $http({
            method: 'POST',
            url: ruta,
            data: param,
            headers: $rootScope.headers
        }).then(function successCallback(response) {
            if ($rootScope.accion == "create") {
                $rootScope.colecciones = response.data;
                $rootScope.modalColeccion = {display: "none"};
            } else {
                if (response.data.confirmar == 1) {
                    angular.forEach($rootScope.colecciones, function (coleccion) {
                        if (coleccion.idColeccion == $rootScope.cloneColeccion.idColeccion) {
                            coleccion.descripcion = $rootScope.cloneColeccion.descripcion;
                            coleccion.idModulo = $rootScope.cloneColeccion.idModulo;
                            coleccion.configuracion = $rootScope.cloneColeccion.configuracion;
                            coleccion.reporte = $rootScope.cloneColeccion.reporte;
                            angular.forEach($scope.modulos, function (modulo) {
                                if(modulo.idModulo == $rootScope.cloneColeccion.idModulo)
                                    coleccion.modulo = modulo.modulo;
                            });
                            coleccion.estado1 = $rootScope.cloneColeccion.estado1;
                            coleccion.estado2 = $rootScope.cloneColeccion.estado2;
                            coleccion.fechaModificado = $rootScope.cloneColeccion.fechaModificado;
                            coleccion.horaModificado = $rootScope.cloneColeccion.horaModificado;
                            coleccion.idIntegracion = $rootScope.cloneColeccion.idIntegracion;
                            coleccion.descripcionIntegracion = $rootScope.cloneColeccion.descripcionIntegracion;
                            coleccion.activo = $rootScope.cloneColeccion.activo;
                            coleccion.cubo = $rootScope.cloneColeccion.cubo;
                            coleccion.repetirConfiguracion = $rootScope.cloneColeccion.repetirConfiguracion;
                            coleccion.observaciones = $rootScope.cloneColeccion.observaciones;
                        }
                    });
                    $rootScope.closeCargando();
                    $rootScope.modalColeccion = {display: "none"};
                } else {
                    $rootScope.closeCargando();
                    alert("Lo sentimos ha ocurrido un error en el servidor.");
                }
            }
        }, function errorCallback(response) {
            $rootScope.closeCargando();
            alert("Mensaje Cambio Coleccion: " + response.statustext);
        });
    };

    $scope.selectTablasIntegracion = function () {
        $http({
            method: 'POST',
            url: ruta,
            data: {metodo: "cargarTablasIntegracion"},
            headers: $rootScope.headers
        }).then(function successCallback(response) {
            $rootScope.tablasIntegracion = response.data;
        }, function errorCallback(response) {
            alert("Mensaje Select Tablas: " + response.statustext);
        });
    };
    
    $scope.change();
    $scope.selectModulos();
    $scope.selectTablasIntegracion();
    $rootScope.closeCargando();
    
});
//-- GRID COLECCION
app.controller("GridColeccion", function ($scope, $http, $rootScope) {

    $scope.cargarRegistrosTablaIntegracionColeccion = function () {
        $rootScope.openCargando();
        $rootScope.cloneColeccion.idIntegracion = null;
        var param = {};
        param.metodo = "cargarIdTablasIntegracion";
        param.tabla = $rootScope.cloneColeccion.descripcionIntegracion;
        $http({
            method: 'POST',
            url: ruta,
            data: param,
            headers: $rootScope.headers
        }).then(function successCallback(response) {
            $rootScope.registrosIntegracion = response.data;
            $rootScope.closeCargando();
        }, function errorCallback(response) {
            $rootScope.closeCargando();
            alert("Mensaje Grid Coleccion: " + response.statustext);
        });
    };

    $scope.cgrParametros = function (coleccion) {
        $rootScope.parametroSelected = {};
        if (coleccion.idColeccion !== $scope.cloneColeccion.idColeccion) {
            $rootScope.coleccionSelected = {
                "idColeccion": coleccion.idColeccion,
                "descripcion": coleccion.descripcion,
                "idModulo": coleccion.idModulo,
                "modulo": coleccion.modulo,
                "activo": coleccion.activo,
                "descripcionIntegracion": coleccion.descripcionIntegracion,
                "configuracion": coleccion.configuracion,
                "reporte": coleccion.reporte,
                "cubo": coleccion.cubo,
                "repetirConfiguracion": coleccion.repetirConfiguracion,
                "estado1": coleccion.estado1,
                "estado2": coleccion.estado2,
                "fechaModificacion": coleccion.fechaModificacion,
                "horaModificacion": coleccion.horaModificacion,
                "usuarioModifico": coleccion.usuarioModifico,
                "idIntegracion": coleccion.idIntegracion,
                "observaciones": coleccion.observaciones
            };
            $rootScope.cloneColeccion = {
                "idColeccion": coleccion.idColeccion,
                "descripcion": coleccion.descripcion,
                "idModulo": coleccion.idModulo,
                "modulo": coleccion.modulo,
                "activo": coleccion.activo,
                "descripcionIntegracion": coleccion.descripcionIntegracion,
                "configuracion": coleccion.configuracion,
                "reporte": coleccion.reporte,
                "cubo": coleccion.cubo,
                "repetirConfiguracion": coleccion.repetirConfiguracion,
                "estado1": coleccion.estado1,
                "estado2": coleccion.estado2,
                "fechaModificacion": coleccion.fechaModificacion,
                "horaModificacion": coleccion.horaModificacion,
                "usuarioModifico": coleccion.usuarioModifico,
                "idIntegracion": coleccion.idIntegracion,
                "observaciones": coleccion.observaciones
            };
            $scope.datosParametros();
        }
    };

    $scope.closeModalColeccion = function () {
        $rootScope.accion = "";
        $rootScope.modalColeccion = {"display": "none"};
        $rootScope.cloneColeccion = {};
    };

    $scope.openModalCreateColeccion = function () {
        $rootScope.accion = "create";
        $rootScope.modalColeccion = {"display": "block"};
        $rootScope.coleccionSelected = {};
        $rootScope.cloneColeccion = {
            "idColeccion": "",
            "descripcion": "",
            "idModulo": "1",
            "modulo": "Restaurante",
            "activo": 1,
            "descripcionIntegracion": null,
            "configuracion": 0,
            "reporte": 0,
            "cubo": 0,
            "repetirConfiguracion": 0,
            "estado1": 0,
            "estado2": 0,
            "fechaModificacion": "",
            "horaModificacion": "",
            "usuarioModifico": "",
            "idIntegracion": null,
            "observaciones": ""
        };
    };

    $scope.datosParametros = function () {
        $http({
            method: 'POST',
            url: ruta,
            data: {metodo: "cargarParametrosPolitica", coleccion: $rootScope.modelColeccion, idColeccion: $rootScope.coleccionSelected.idColeccion},
            headers: $rootScope.headers
        }).then(function successCallback(response) {
            $rootScope.parametros = response.data;
        }, function errorCallback(response) {
            alert("Mensaje Parametros: " + response.statustext);
        });
    };
});
//-- GRID PARAMETROS
app.controller("GridParametro", function ($scope, $http, $rootScope) {
    
    $scope.busquedaParametro = {decripcion: ""};

    $scope.changeParametro = function () {
        $rootScope.openCargando();
        var param = $rootScope.cloneParametro;
        param.metodo = $rootScope.accion + "Parametro";
        param.coleccion = $rootScope.modelColeccion;
        $http({
            method: 'POST',
            url: ruta,
            data: param,
            headers: $rootScope.headers
        }).then(function successCallback(response) {
            if ($rootScope.accion == "create") {
                $rootScope.parametros = response.data;
                $rootScope.modalParametro = {display: "none"};
            } else {
                if (response.data.confirmar == 1) {
                    angular.forEach($rootScope.parametros, function (parametro) {
                        if (parametro.idParametro == $rootScope.cloneParametro.idParametro) {
                            parametro.descripcion = $rootScope.cloneParametro.descripcion;
                            parametro.especificarValor = $rootScope.cloneParametro.especificarValor;
                            parametro.obligatorio = $rootScope.cloneParametro.obligatorio;
                            parametro.tipoDato = $rootScope.cloneParametro.tipoDato;
                            angular.forEach($rootScope.tiposDatos, function (tipo) {
                                if(tipo.tipoDato == $rootScope.cloneParametro.tipoDato)
                                    parametro.descripcionTipoDato = tipo.descripcionTipoDato;
                            });
                            parametro.estado1 = $rootScope.cloneParametro.estado1;
                            parametro.estado2 = $rootScope.cloneParametro.estado2;
                            parametro.fechaModificado = $rootScope.cloneParametro.fechaModificado;
                            parametro.horaModificado = $rootScope.cloneParametro.horaModificado;
                            parametro.idIntegracion = $rootScope.cloneParametro.idIntegracion;
                            parametro.descripcionIntegracion = $rootScope.cloneParametro.descripcionIntegracion;
                            parametro.activo = $rootScope.cloneParametro.activo;
                        }
                    });
                    $rootScope.modalParametro = {display: "none"};
                    $rootScope.closeCargando();
                } else {
                    alert("Lo sentimos ha ocurrido un error en el servidor.");
                }
            }
        }, function errorCallback(response) {
            $rootScope.closeCargando();
            alert("Mensaje Cambio Parametro: " + response.statustext);
        });
    };

    $scope.cgrSelectParametro = function (parametro) {
        $rootScope.parametroSelected = parametro;
        $rootScope.cloneParametro = {
            "idColeccion": parametro.idColeccion,
            "idParametro": parametro.idParametro,
            "descripcion": parametro.descripcion,
            "especificarValor": parametro.especificarValor,
            "obligatorio": parametro.obligatorio,
            "tipoDato": parametro.tipoDato,
            "descripcionTipoDato": parametro.descripcionTipoDato,
            "estado1": parametro.estado1,
            "estado2": parametro.estado2,
            "fechaModificado": parametro.fechaModificado,
            "horaModificado": parametro.horaModificado,
            "idIntegracion": parametro.idIntegracion,
            "descripcionIntegracion": parametro.descripcionIntegracion,
            "activo": parametro.activo
        };
    };

    $scope.openModalUpdateParametro = function () {
        $rootScope.modalParametro = {display: "block"};
        $rootScope.accion = "update";
        if ($rootScope.cloneParametro.descripcionIntegracion !== null) {
            $rootScope.cargarRegistrosTablaIntegracionConfigurada($rootScope.cloneParametro.descripcionIntegracion);
        }
    };

    $scope.openModalCreateParametro = function () {
        if ($rootScope.coleccionSelected.hasOwnProperty("idColeccion")) {
            $rootScope.cloneParametro = {
                "idColeccion": $rootScope.coleccionSelected.idColeccion,
                "idParametro": "",
                "descripcion": "",
                "especificarValor": 0,
                "obligatorio": 0,
                "tipoDato": "I",
                "descripcionTipoDato": "Entero",
                "estado1": 0,
                "estado2": 0,
                "fechaModificado": "",
                "horaModificado": "",
                "idIntegracion": null,
                "descripcionIntegracion": null,
                "activo": 1
            };
            $rootScope.modalParametro = {display: "block"};
            $rootScope.accion = "create";
        } else {
            alert("Seleccione una Colección");
        }
    };

    $scope.closeModalParametro = function () {
        $rootScope.cloneParametro = {};
        $rootScope.modalParametro = {display: "none"};
        $rootScope.accion = "";
    };

    $scope.cargarRegistrosTablaIntegracionColeccion = function () {
        $rootScope.openCargando();
        $rootScope.cloneParametro.idIntegracion = null;
        var param = {};
        param.metodo = "cargarIdTablasIntegracion";
        param.tabla = $rootScope.cloneParametro.descripcionIntegracion;
        $http({
            method: 'POST',
            url: ruta,
            data: param,
            headers: $rootScope.headers
        }).then(function successCallback(response) {
            $rootScope.registrosIntegracion = response.data;
            $rootScope.closeCargando();
        }, function errorCallback(response) {
            $rootScope.closeCargando();
            alert("Mensaje Carga Registro: " + response.statustext);
        });
    };

});