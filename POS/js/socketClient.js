var $orders = $("#listaPedidosKiosko");
var $socketActivo = $("#hide_socketActivo");
var instanceTippyBtnMenu = null;

class PedidosInMemory {
    constructor() {
        this.createdDay = null;
        this.pedidos = [];
    }

    /**
     * Agrega un pedido a la memoria
     * @param pedido
     */
    addPedido(pedido) {
        let hasInIndex = this.pedidos.findIndex(obj => obj.transaccion === pedido.transaccion);
        if (hasInIndex !== -1) {
            // Existe el pedido en la lista solo actualizemos su estado
            // si sus estados son diferentes asignarle el nuevo estado
            this.pedidos[hasInIndex].estado = pedido.estado;
        } else {
            // No existe el pedido solo agregamos
            this.pedidos.push(pedido);
            console.log("pedido agregado " + pedido.transaccion);
        }
        this.saveInLocalStorage();
    }

    /**
     * ¿Existe un pedido con esta transacción?
     * @param transaccion
     * @returns {boolean}
     */
    hasPedidoPickup(transaccion) {
        let hasInIndex = this.pedidos.findIndex(obj => obj.transaccion === transaccion);
        return hasInIndex !== -1;
    }

    /**
     * Remueve un pedido de la memoria local
     * @param transaccion
     */
    removePedido(transaccion) {
        for (let i = 0; i < this.pedidos.length; i++) {
            if (this.pedidos[i].transaccion === transaccion) {
                this.pedidos.splice(i, 1);
            }
        }
    }

    /**
     * Obtenemos los pedidos totales retornando un numero|string
     * @returns string
     */
    getTotalPedidosTotalesString() {
        let textCount = 0;
        let countProductos = this.pedidos;
        if (countProductos.length > 99) {
            textCount = "99+";
        } else {
            textCount = countProductos.length;
        }
        return textCount;
    }

    /**
     * Sirve para cargar los pedidos al refrescar o cargar el sitio de toma de pedido.
     * @returns {Promise<void>}
     */
    async loadFromLocalStorage() {
        let pedidosLocales = await this.getLocalstoragePedidosPickup();
        let createdDay = dayjs().format('D');
        let loadedPedidos = pedidosLocales.pedidos;
        if (pedidosLocales.createdDay) {
            let now = dayjs().format('D');
            if (pedidosLocales.createdDay !== now) {
                //Otro dia o Día diferente al guardado
                loadedPedidos = [];
                createdDay = now;
            } else {
                createdDay = pedidosLocales.createdDay;
            }
        }
        this.createdDay = createdDay;
        this.pedidos = loadedPedidos;
    }

    /**
     * Obtiene los pedidos pickup desde el local storage.
     * @returns {Promise<{pedidos: *[]}|any>}
     */
    async getLocalstoragePedidosPickup() {
        let json_raw = localStorage.getItem('pedidos_pickup');
        if (json_raw && json_raw.trim() !== '') {
            try {
                return await JSON.parse(json_raw);
            } catch (e) {
            }
        }
        return {
            pedidos: []
        };
    }

    /**
     * Actualiza un el estado de un pedido existente
     * @param pedido
     */
    updatePedido(pedido) {
        let hasInIndex = this.pedidos.findIndex(obj => obj.transaccion === pedido.transaccion);
        if (hasInIndex === -1) {
            console.log(" se intento actualizar un pedido que nunca se ingresó primero " + pedido.transaccion)
            return;
        }
        this.pedidos[hasInIndex].estado = pedido.estado;
        console.log(" transaccion actualizada " + pedido.transaccion)
        this.saveInLocalStorage();
    }

    /**
     * Obtener nuevo pedido
     * @param transaction identificador del pedido
     * @returns {undefined|*} Json del pedido
     */
    getPedido(transaction) {
        let hasInIndex = this.pedidos.findIndex(obj => obj.transaccion === transaction);
        if (hasInIndex === -1) {
            return undefined;
        }
        return this.pedidos[hasInIndex];
    }

    saveInLocalStorage() {
        let pedidosPickupWrapper = {
            createdDay: this.createdDay, pedidos: this.pedidos
        }
        localStorage.setItem('pedidos_pickup', JSON.stringify(pedidosPickupWrapper))
    }
}

class TransferenciaPedidos {


    constructor() {
        this.tienda = null; // obj json {id, descripcion}
        this.motivo = null; // obj json {id, descripcion}
        this.pedido = null; // obj pedido (lista de pedidos)
        // inicializar botones del wizard
        this.btnPrev = $("#transferir-pedido-prev-btn");
        this.btnNext = $("#transferir-pedido-next-btn");
        this.btnFinish = $("#transferir-pedido-finish-btn");
        this.btnPrev.hide();
        this.btnNext.prop("disabled", true);
        this.btnFinish.hide();
        $("#smartwizardTransferirPedido").on("stepContent", (e, anchorObject, stepIndex, stepDirection) => {
            console.log("Mostrando step")
            if (stepIndex === 0) {
                this.toggleBtns(stepIndex);
                return this.cargarLocales();
            }
            if (stepIndex === 1) {
                this.toggleBtns(stepIndex);
                return this.cargarMotivos();
            }
            if (stepIndex === 2) {
                this.toggleBtns(stepIndex);
            }
            return new Promise((resolve, reject) => resolve(''));
        });
        this.btnNext.on('click', () => {
            $('#smartwizardTransferirPedido').smartWizard("next");
        });
        this.btnPrev.on('click', () => {
            $('#smartwizardTransferirPedido').smartWizard("prev");
        });
        this.btnFinish.on('click', () => {
            // finalizar transaccion
        });
    }

    seleccionarPedido(pedido) {
        this.pedido = pedido;
        //$("#transferir-pedido-finish-btn").hide();
    }

    seleccionarLocal(tienda) {
        this.tienda = tienda;
        this.toggleBtns(0);
    }

    seleccionarMotivo(motivo) {
        this.motivo = motivo;
        this.toggleBtns(1);
    }

    toggleBtns(stepsIndex) {
        if (stepsIndex === 0) {
            this.btnPrev.hide(); // oculta boton anterior
            this.btnFinish.hide(); // oculta boton finalizar
            this.btnNext.show(); // muestra boton siguiente
            if (!this.tienda) {
                this.btnNext.prop("disabled", true);
            } else {
                this.btnNext.prop("disabled", false);
            }
        } else if (stepsIndex === 1) {
            this.btnPrev.show(); // muestra boton anterior
            this.btnNext.show(); // muestra boton siguiente
            this.btnFinish.hide(); // ocultar boton finalizar
            if (!this.motivo) {
                this.btnNext.prop("disabled", true);
            } else {
                this.btnNext.prop("disabled", false);
            }
        } else if (stepsIndex === 2) {
            this.btnPrev.show();// muestra boton anterior
            this.btnNext.hide(); // ocultar boton siguiente
            if (this.motivo && this.tienda) {
                this.btnFinish.show();
                this.btnNext.prop("disabled", false);
            }
        }
    }

    completarTransferenciaPedido() {

    }

    limpiarSeleccion() {
        this.tienda = null;
        this.motivo = null;
        this.pedido = null;
    }

    cargarLocales() {
        let send = {
            metodo: "cargarLocales"
        };
        $('#smartwizardTransferirPedido').smartWizard("loader", "show");
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "POST",
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                url: "../ordenpedido/config_transferencia.php",
                data: send,
                success: function (datos) {
                    //console.log(datos);
                    let elementos = '';
                    if (datos.registros > 0) {

                        for (let i = 0; i < datos.registros; i++) {
                            let registro = datos[i];
                            let {codigo, id, descripcion} = registro;
                            elementos += `<a class="list-group-item list-group-item-action" data-codigo="${codigo}" data-id="${id}">  ${descripcion} </a>`;
                        }

                    } else {
                        //  $("#lstLocalesTransferencia").html('<li class="datos_motorizado"><div class="datos_locales">No existen locales a los que puedas hacer transferencia.</div></li>');
                        //   $('#mdl_rdn_pdd_crgnd').hide();
                    }
                    let parent = `
                       <div id="contenedorListaTiendaTransferir"
                                         class="list-group w-100"
                                         style="height: 350px; overflow-y: auto">
                                    ${elementos}
                       </div>
                    `;
                    resolve(parent);
                    $('#smartwizardTransferirPedido').smartWizard("loader", "hide");
                    // $('#mdl_rdn_pdd_crgnd').hide();
                    // $('#modalLocalesTransferencia').modal('toggle');

                },
                error: function (data) {
                    console.log("Error: ", data);
                    reject();
                    $('#smartwizardTransferirPedido').smartWizard("loader", "hide");
                }
            });
        });
    }

    abrirTransferenciaPedidos() {
        let modalEl = $("#modalPickupTiendas");
        modalEl.modal('show');
        $('#smartwizardTransferirPedido').smartWizard({
            selected: 0,
            theme: 'dots',
            autoAdjustHeight: true,
            transitionEffect: 'fade',
            showStepURLhash: false,
            toolbarSettings: {
                toolbarPosition: 'bottom', // none, top, bottom, both
                toolbarButtonPosition: 'right', // left, right, center
                showNextButton: false, // show/hide a Next button
                showPreviousButton: false, // show/hide a Previous button
                toolbarExtraButtons: [] // Extra buttons to show on toolbar, array of jQuery input/buttons elements
            },
        });
    }

    cargarMotivos() {
        let send = {metodo: "cargarMotivos"};
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "POST",
                dataType: "json",
                contentType: "application/x-www-form-urlencoded",
                url: "../ordenpedido/config_transferencia.php",
                data: send,
                beforeSend: function (xhr) {
                    // Show the loader
                    $('#smartwizardTransferirPedido').smartWizard("loader", "show");
                },
                success: function (datos) {
                    //console.log(datos);
                    let elementos = '';
                    if (datos.registros > 0) {
                        for (let i = 0; i < datos.registros; i++) {
                            let registro = datos[i];
                            let {codigo, id, descripcion} = registro;
                            elementos += `<a class="list-group-item list-group-item-action" data-codigo="${codigo}" data-id="${id}">  ${descripcion} </a>`;
                        }
                    } else {
                        // no existen motivos
                    }
                    let parent = `
                       <div id="contenedorListaMotivosTransferir"
                                         class="list-group w-100"
                                         style="height: 350px; overflow-y: auto">
                                    ${elementos}
                       </div>
                    `;
                    resolve(parent);
                    $('#smartwizardTransferirPedido').smartWizard("loader", "hide");
                },
                error: function (data) {
                    console.log("Error: ", data);
                    $('#mdl_rdn_pdd_crgnd').hide();
                    reject('');
                    $('#smartwizardTransferirPedido').smartWizard("loader", "hide");
                }
            });
        });
    }
}

let sounds = {
    pickupInsertado: '../js/sounds/ding_bell_pickup_insertado.mp3',
    pickupPreparando: '../js/sounds/ding_bell_preparar_pickup.mp3',
}
let pedidosNotifyMemory = new PedidosInMemory()
$(document).ready(function() {
    let transferenciaPedidos = new TransferenciaPedidos();

    function clearInstanceTippyMenu() {
        if (instanceTippyBtnMenu) {
            instanceTippyBtnMenu.unmount();
            instanceTippyBtnMenu.destroy();
        }
    }

    function changePedidosTotalesMenu() {
        let modalTotal = $("#countPedidosPickupBadge");
        modalTotal.text(pedidosNotifyMemory.getTotalPedidosTotalesString());
    }

    function changePedidosPickupModal() {
        let modalTotal = $("#countPedidosPickupBadgeModal");
        modalTotal.text(pedidosNotifyMemory.getTotalPedidosTotalesString());
    }


    function createDetallePedido(detalles_productos) {
        let htmlAppend = '';
        let totalPrecio = 0;
        for (let i = 0; i < detalles_productos.length; i++) {
            let detalle = detalles_productos[i];
            let {Cantidad, Comentario, Fecha, Precio, Producto, DescripcionProducto} = detalle;
            totalPrecio += Precio;
            htmlAppend += `<li>
                                    <a data-pedido="x${Cantidad}">${Producto}</a>
                                    ${Comentario ? `
                                          <ul>
                                            <li>${Comentario}</li>
                                         </ul>
                                    ` : ``}
                                    
                            </li>`;
        }
        return htmlAppend;
    }

    async function reinitializePedidosPickup() {
        let createdDay = pedidosNotifyMemory.createdDay;
        let pedidosPickupArray = pedidosNotifyMemory.pedidos;
        if (createdDay) {
            let now = dayjs().format('D');
            if (createdDay !== now) {
                return;
            }
        }
        if (pedidosPickupArray) {
            let fechaActual = dayjs();
            for (let i = 0; i < pedidosPickupArray.length; i++) {
                let pedido = pedidosPickupArray[i];
                // Comparar si el pedido ya superó la fecha de entrega.
                let {tipo_servicio} = pedido;
                let {pickup, fecha} = tipo_servicio // fecha es la fecha de retiro
                let fechaRetiro = fechaActual.format('YYYY-MM-DD') + " " + fecha
                let fechaRetiroJs = dayjs(fechaRetiro, 'YYYY-MM-DD h:mm A');
                if (fechaActual.unix() >= fechaRetiroJs.unix()) {
                    continue;
                }
                await agregarPedidoAlTablaDom(pedido);
            }
        }
    }

    function toggleNingunPedidoPickup() {
        let containerPickupEmpty = $("#containerPickupEmpty");
        let tablaContainerPickup = $("#tablaContainerPedidosPickup");
        let btnPedidoPickupPanel = $('#btnPedidoPickupPanel');
        if (pedidosNotifyMemory.pedidos.length === 0) {
            // Mostrar "No hay ningun pedido en curso"
            if (containerPickupEmpty.hasClass("d-none")) {
                containerPickupEmpty.removeClass("d-none");
            }
            // Cambiar de background "Normal" al boton de Pedido-Pickup
            if (btnPedidoPickupPanel.hasClass("btnPedidosPickup")) {
                btnPedidoPickupPanel.removeClass("btnPedidosPickup");
            }
            btnPedidoPickupPanel.attr('disabled', true);
            // Ocultar la tabla de contenido
            if (!tablaContainerPickup.hasClass("d-none")) {
                tablaContainerPickup.addClass("d-none");
            }
        } else {
            // Ocultar "No hay ningun pedido en curso"
            if (!containerPickupEmpty.hasClass("d-none")) {
                containerPickupEmpty.addClass("d-none");
            }
            // Cambiar de background "Alertado" al boton de Pedido-Pickup
            if (!btnPedidoPickupPanel.hasClass("btnPedidosPickup")) {
                btnPedidoPickupPanel.addClass("btnPedidosPickup");
            }
            btnPedidoPickupPanel.attr('disabled', false);
            // Mostrar la tabla de contenido
            if (tablaContainerPickup.hasClass("d-none")) {
                tablaContainerPickup.removeClass("d-none");
            }
        }
    }

    function reimprimir(idCanalMovimiento) {
        let dataObj = {
            metodo: 'reimprimir-cfacid', cfacId: idCanalMovimiento
        };
        $.ajax({
            async: false,
            type: "POST",
            dataType: "json",
            contentType: "application/x-www-form-urlencoded",
            url: "../reportes/pedidos/apiPedido.php",
            data: dataObj,
            success: function (response) {
                if (response) {
                    if (response == 1) {
                        console.log("Reimpresion realizada");
                        alertify.success("Reimpresión realizada correctamente");
                    }
                }
            },
            error: function (e1, e2, e3) {
                console.log(e1);
                console.log(e2);
                console.log(e3);
            }
        });
    }

    function limpiarProductoPickupPreparandoVisto() {
        console.log("Limpiando productos pickup vistos");
        let remover = [];
        let fecha_actual = dayjs();
        let listPickupProductos = pedidosNotifyMemory.pedidos;
        console.log(listPickupProductos)
        for (let i = 0; i < listPickupProductos.length; i++) {
            let producto = listPickupProductos[i];
            if (producto.estado.toString().toLowerCase() === "entregada") {
                remover.push(producto.transaccion);
                //listPickupProductos.splice(i, 1);
                continue;
            }
            if (producto.tipo_servicio && producto.tipo_servicio.fecha) {
                let fechaPickup = producto.tipo_servicio.fecha;
                let fechaRetiro = fecha_actual.format('YYYY-MM-DD') + " " + fechaPickup
                let fecha_pickup = dayjs(fechaRetiro, 'YYYY-MM-DD h:mm A');
                console.log("Fecha actual " + fecha_actual.unix());
                console.log("Fecha retiro " + fecha_pickup.unix())
                if (fecha_actual.unix() >= fecha_pickup.unix()) {
                    // El pedido ya expiró pero aún sigue guardado
                    remover.push(producto.transaccion);
                    //listPickupProductos.splice(i, 1);
                }
            }
        }
        console.log("se limpiaron " + remover.length + " pedidos");
        for (let i = 0; i < remover.length; i++) {
            let removeTransaccion = remover[i];
            $("#row-pickup-" + removeTransaccion).remove();
            $("#spacer-" + removeTransaccion).remove();
            pedidosNotifyMemory.removePedido(removeTransaccion)
        }
        pedidosNotifyMemory.saveInLocalStorage();
        toggleNingunPedidoPickup();
        changePedidosPickupModal();
        changePedidosTotalesMenu();
    }

// Funcion que establece listener para agregar un HIDE a la alerta del boton Menu
    function setupListenerBtnMenu() {
        $("#boton_sidr").on('click', (e) => {
            clearInstanceTippyMenu();
            let alertaPickupBtnCounterPedidos = $("#alertaPickupMenu");
            if (!alertaPickupBtnCounterPedidos.hasClass("d-none")) {
                alertaPickupBtnCounterPedidos.addClass("d-none");
            }
        });
        toggleNingunPedidoPickup();
        let modalPickupPedidos = $("#modalPickupPedidos");
        modalPickupPedidos.on('hidden.bs.modal', function () {
            // Cuando se cierra el modal borrar de la lista los pedidos que ya están en estado: preparando
            limpiarProductoPickupPreparandoVisto();
        });
        modalPickupPedidos.on('shown.bs.modal', function () {
            // CUando se abre el modal de la lista de pedidos en curso
            // Oculta la instancia de Tippy para que no se siga mostrando;
            clearInstanceTippyMenu();
        });
        $('#modalPickupDetallesPedido').on('hidden.bs.modal', function () {
            // Cuando se cierra el modal borrar de la lista los pedidos que ya están en estado: preparando
            $("#modalPickupPedidos").removeClass("d-none");
        })
        $('#modalConfirmar').on('hidden.bs.modal', function () {
            let botonConfirmar = $("#modalConfirmarOk");
            botonConfirmar.off('click', () => {
            });
        });
        $('#modalPickupTiendas').on('hidden.bs.modal', function (e) {
            let currentElement = $(e.currentTarget);
            let container = currentElement.find('#contenedorListaTiendasPickup').first();
            container.empty();
        });
        let tablaContainerPedidosPickup = $("#tablaContainerPedidosPickup");
        let contenedorListaTiendas = $("#smartwizardTransferirPedido");
        tablaContainerPedidosPickup.on('click', '.selectable-notificacion', (event) => {
            let currentTarget = $(event.currentTarget);
            let transaction = currentTarget.data('transaction');
            let pedido = pedidosNotifyMemory.getPedido(transaction);
            if (pedido) {
                mostrarInformacionDetallePedidoPickup(pedido);
                $("#modalPickupDetallesPedido").modal("show");
                $("#modalPickupPedidos").addClass("d-none");
            }
        });
        tablaContainerPedidosPickup.on('click', '.btn-print-clickeable', (event) => {
            event.stopImmediatePropagation();
            let currentTarget = $(event.currentTarget);
            let disabledBtn = currentTarget.attr('disabled');
            if (typeof disabledBtn !== 'undefined' && disabledBtn !== false) {
                return;
            }
            let transaction = currentTarget.data('transaction');
            let pedido = pedidosNotifyMemory.getPedido(transaction);
            if (pedido) {
                let modalConfirmar = $("#modalConfirmar");
                let modalConfirmarBtnOk = $("#modalConfirmarOk");
                $("#modalConfirmarTitle").text("Re imprimir")
                $("#modalConfirmarBody").html("¿Estás seguro que deseas reimprimir esta orden?</br><b>Código App:</b> " + pedido.codigo_app);
                modalConfirmarBtnOk.off('click').on('click', (event) => {
                    console.log("Re-imprimiendo transaccion " + transaction);
                    reimprimir(transaction);
                    modalConfirmar.modal('hide');
                });
                modalConfirmar.modal("show");
            }
        });
        contenedorListaTiendas.on('click', '#contenedorListaTiendaTransferir a', (event) => {
            event.preventDefault()
            let $that = $(event.currentTarget);
            $that.parent().find('a').removeClass('active');
            $that.addClass('active');
            let codigo = $that.data('data-codigo');
            let codigo_id = $that.data('data-id');
            transferenciaPedidos.seleccionarLocal({
                id: codigo_id, codigo: codigo, descripcion: $that.text()
            })
        });
        contenedorListaTiendas.on('click', '#contenedorListaMotivosTransferir a', (event) => {
            event.preventDefault()
            let $that = $(event.currentTarget);
            $that.parent().find('a').removeClass('active');
            $that.addClass('active');
            let codigo_id = $that.data('data-id');
            transferenciaPedidos.seleccionarMotivo({
                id: codigo_id, descripcion: $that.text()
            })
        });
        tablaContainerPedidosPickup.on('click', '.btn-transferir-clickeable', (event) => {
            event.stopImmediatePropagation();
            let currentTarget = $(event.currentTarget);
            let disabledBtn = currentTarget.attr('disabled');
            if (typeof disabledBtn !== 'undefined' && disabledBtn !== false) {
                return;
            }
            let transaction = currentTarget.data('transaction');
            let pedido = pedidosNotifyMemory.getPedido(transaction);
            if (pedido) {
                transferenciaPedidos.abrirTransferenciaPedidos();
            }
        });
    }


    function autorizacionesDOM(codigo, mensajeRespuesta, tarjetaHabiente, pasarela, tipoTarjeta, fechaHora) {
        return `<div class="m-3 autorizacion-info">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col">
                                                        <b>Código:</b>
                                                    </div>
                                                    <div id="dp-autorizacion-codigo" class="col">
                                                        ${codigo}
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col">
                                                        <b>Mensaje respuesta:</b>
                                                    </div>
                                                    <div id="dp-autorizacion-respuesta" class="col">
                                                        ${mensajeRespuesta}
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col">
                                                        <b>Tarjeta habiente</b>
                                                    </div>
                                                    <div id="dp-autorizacion-thabiente" class="col">
                                                        ${tarjetaHabiente}
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col">
                                                        <b>Pasarela de pago</b>
                                                    </div>
                                                    <div id="dp-autorizacion-tipotarjeta" class="col">
                                                        ${pasarela}
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col">
                                                        <b>Tipo tarjeta</b>
                                                    </div>
                                                    <div id="dp-autorizacion-tipotarjeta" class="col">
                                                        ${tipoTarjeta}
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="list-group-item">
                                                <div class="row">
                                                    <div class="col">
                                                        <b>Fecha - Hora</b>
                                                    </div>
                                                    <div id="dp-autorizacion-fechahora" class="col">
                                                        ${fechaHora}
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>`;
    }

    function mostrarInformacionDetallePedidoPickup(pedido) {
        let {codigo_app, autorizaciones} = pedido;
        $("#name-transaction").text(codigo_app);
        $(".autorizacion-info").remove();
        for (let i = 0; i < autorizaciones.length; i++) {
            let autorizacion = autorizaciones[i];
            let {
                MensajeRespuestaAut, TarjetaHabiente, TipoTarjeta, PasarelaPago, created_at_str
            } = autorizacion;
            let newDOMElement = autorizacionesDOM(' ', MensajeRespuestaAut, TarjetaHabiente, PasarelaPago, TipoTarjeta, created_at_str);
            $("#autorizaciones-pago-body").append(newDOMElement);
        }

    }

    function confirmarNotificacionRecibida(estacionId, transaccionesProcesadas) {
        if (socket.connected) {
            let responseData = {
                estacionId: estacionId, transacciones: transaccionesProcesadas
            };
            socket.emit("confirmar-notificacion", responseData);
        }
    }

    async function agregarPedidoAlTablaDom(data) {
        let {
            orden,
            cliente,
            clienteDocumento,
            tipo,
            tipo_servicio,
            detalles_productos,
            transaccion,
            estado,
            especial,
            comentario_general,
            cfac_total,
            autorizaciones,
            codigo_app
        } = data;
        if (!tipo_servicio) {
            return;
        }
        if (!comentario_general || comentario_general === 'null') {
            comentario_general = 'Sin observación';
        }
        let {pickup, fecha} = tipo_servicio;
        let esParaLLevar = pickup.toString().toLowerCase().includes("llevar");
        let colorLlevar = '#FF4500FF';
        let colorServir = '#159f09';
        let textLlevar = "Llevar";
        let textServir = "Servir";
        let colorPreparando = "#fd6e30";
        let colorIngresado = "#363535";
        let isPreparando = estado.toString().toLowerCase() === "preparando";
        let disabledReImprimir = isPreparando ? `` : "disabled=true";
        console.log("Re imprimir pedido insertado " + disabledReImprimir)
        let existePedidoPickup = pedidosNotifyMemory.hasPedidoPickup(transaccion);
        pedidosNotifyMemory.addPedido(data);
        /**
         * Llama a la instancia en un boton para mostrar un tooltip forzado
         */
        clearInstanceTippyMenu();
        instanceTippyBtnMenu = tippy(document.querySelector("#boton_sidr"));
        /**
         * Crear Html a insertar en la tabla
         * @type {string} OBJ HTML A CREAR
         */
        let detallePedidoContent = createDetallePedido(detalles_productos);
        if ($(`#row-pickup-${transaccion}`).length) {
            return;
        }
        let objHtml = `
                <tr id="row-pickup-${transaccion}" scope="row" class="selectable-notificacion" data-transaction="${transaccion}">
                    <td class="middle-pedido-pickup">
                        ${codigo_app}
                    </td>
                    <td class="middle-pedido-pickup" style="text-transform: uppercase">
                        ${cliente}
                    </td>
                    <td class="middle-pedido-pickup">
                        <ol class="lista-rectangular">
                            ${detallePedidoContent}
                        </ol>
                    </td>
                    <td class="middle-pedido-pickup"> ${comentario_general}
                    </td>
                    <td class="middle-pedido-pickup">
                        $${cfac_total}
                    </td>
                    <td class="middle-pedido-pickup">
                        <div class=" d-flex flex-column justify-content-center align-items-center">
                               <span class="badge badge-dark"
                                style="background-color: ${esParaLLevar ? colorLlevar : colorServir}; color: white">
                                        ${esParaLLevar ? textLlevar : textServir}
                               </span>
                            ${fecha}
                        </div>
                    </td>
                    <td class="middle-pedido-pickup">
                        <div  class="d-flex flex-column justify-content-center align-items-center content-badge-status">
                            <span class="badge" style="background-color: ${isPreparando ? colorPreparando : colorIngresado}; color: white; padding: 10px; text-transform: uppercase;">${estado}</span>
                        </div>
                    </td>
                    <td class="middle-pedido-pickup btn-print-clickeable" data-transaction="${transaccion}" ${disabledReImprimir}>
                        <div class="btn btn-print">
                           <i class="fas fa-print icon-btn-print"></i>
                        </div>
                    </td>
                     
                </tr>
            `;
        /*<td class="middle-pedido-picclassNametn-transferir-clickeable" data-transaction="${transaccion}">
            <div class="btn btn-print">
                className <i class="fas fa-paper-planclassNamen-btn-print"></i>
            </div>
        </td>*/
        let space = `
                <tr id="spacer-${transaccion}" class="spacer">
                    <td colspan="100"></td>
                </tr>`
        let tablaTbody = $("#tabla-pedidos-pickup").find("tbody").first();
        tablaTbody.append(objHtml);
        tablaTbody.append(space);
        console.log("Agregando al DOM el elemento " + transaccion)
        if (!$("#modalPickupPedidos").hasClass("show")) {
            if (!existePedidoPickup) {
                instanceTippyBtnMenu.setProps({
                    theme: isPreparando ? 'alert' : 'pickup', hideOnClick: false, trigger: 'manual'
                })
                instanceTippyBtnMenu.setContent(isPreparando ? "Por favor prepara pedido pickup" : "Nuevo pedido pickup");
                instanceTippyBtnMenu.show();
                try {
                    let audio = new Audio(sounds.pickupInsertado);
                    await audio.play();
                } catch (errr) {

                }
                let alertaPickupBtnCounterPedidos = $("#alertaPickupMenu");
                if (alertaPickupBtnCounterPedidos.hasClass("d-none")) {
                    alertaPickupBtnCounterPedidos.removeClass("d-none");
                }
            }
        }
        await toggleNingunPedidoPickup();
        await changePedidosTotalesMenu();
        await changePedidosPickupModal();
    }

    var socket;
    var kioskoActivo = $("#hide_configuracionKioskoActivo").val();
    var pickupActivo = $("#hide_configuracionPickupActivo").val();
    let notificacionActiva = $("#hide_politica_recibe_notificacion_pickup").val();
    var urlKiosko = $("#hide_ordenesKioskoURL").val();
    //var urlKiosko = 'http://192.168.101.30:5000';
    console.log(`Configuracion de Sockets - kiosko activo: ${kioskoActivo}, pickup activo: ${pickupActivo}, url: ${urlKiosko}, notificacion pickup: ${notificacionActiva}`)
    let estacionId = $("#hide_est_id").val();
    let btnPedidosPickupList = $("#btnPedidoPickupPanel");
    if (notificacionActiva == 0) {
        btnPedidosPickupList.addClass("d-none");
    }
    if ((kioskoActivo == 1 || pickupActivo == 1) && urlKiosko != "" && notificacionActiva == 1) {
        console.log("Conectandose al servidor socket")
        let startSocket = () => {
            socket = io(urlKiosko, {query: 'estacionid=' + estacionId});
        socket.on("connect", function(data) {
            $socketActivo.val("1");
            console.log("Conectado al Socket");
        });
        socket.on("disconnect", function(data) {
            $socketActivo.val("0");
            console.log("Desconectado del Socket");
        });
        socket.on("connect_error", function() {
            $socketActivo.val("0");
            console.log("Falló la conexión al Socket");
        });
        socket.on("remove-order", function(data) {
            console.log("remove-order:");
            console.log(data);
            var $elementoLi = buscarElementoTransaccion(data.transaccion);
            if ($elementoLi !== null) {
                $elementoLi.remove();
            }
        });
        socket.on("add-order", function(data) {
            console.log("add-order:");
            console.log(data);
            // Validar que no se haya insertado previamente el elemento
            if (buscarElementoTransaccion(data.transaccion) === null) {
                    $orders.append("<li class='liKiosko animateUp fadeInUp' style='background: " + ((data.tipo === "KIOSKO") ? "#d0d0d0" : "#8ca7cf") + "' data-orden='" + data.orden + "' data-codigo_app='" + data.codigo_app + "' data-cliente='" + data.cliente + "' data-factura='" + data.transaccion + "' data-tipo='" + data.tipo + "'>" + data.tipo[0] + data.orden + "&nbsp; " + truncarPalabra(data.cliente, 14) + "</li>");
                }
            });


            socket.on("preparando-transaction", (data) => {
                setTimeout(async () => {
                    console.log("preparando-transaction: " + dayjs().format());
                    console.log(data);
                    let estacionId = $("#hide_est_id").val();
                    let {
                        orden, tipo, transaccion, estado, especial
                    } = data;
                    if (!transaccion) {
                        return;
                    }
                    let colorPreparando = "#fd6e30";
                    pedidosNotifyMemory.updatePedido(data);
                    clearInstanceTippyMenu();
                    instanceTippyBtnMenu = tippy(document.querySelector("#boton_sidr"));
                    let rowTrPickupDato = $("#row-pickup-" + transaccion);
                    let transaccionesProcesadas = [transaccion];
                    if (rowTrPickupDato) {
                        let contentBadgeStatus = rowTrPickupDato.find(".content-badge-status").first();
                        if (estado === 'Entregada') {
                            let colorPreparando = "#0dfc11";
                        }
                        let htmlChange = `<span class="badge" style="background-color: ${colorPreparando}; color: white; padding: 10px; text-transform: uppercase;">${estado}</span>`;
                        contentBadgeStatus.html(htmlChange);
                        let btnClickeable = rowTrPickupDato.find(".btn-print-clickeable").first();
                        btnClickeable.removeAttr('disabled');
                        if (!$("#modalPickupPedidos").hasClass("show")) {
                            instanceTippyBtnMenu.setProps({
                                theme: 'alert', hideOnClick: false, trigger: 'manual'
                            })
                            if (estado === 'Entregada') {
                                instanceTippyBtnMenu.setContent("Pedido pickup entregado");
                            } else {
                                instanceTippyBtnMenu.setContent("Por favor preparar pedido pickup");
                            }
                            instanceTippyBtnMenu.show();
                            try {
                                let audio = new Audio(sounds.pickupPreparando);
                                await audio.play();
                            } catch (err) {

                            }
                            let alertaPickupBtnCounterPedidos = $("#alertaPickupMenu");
                            if (alertaPickupBtnCounterPedidos.hasClass("d-none")) {
                                alertaPickupBtnCounterPedidos.removeClass("d-none");
                            }
                        }

                    }
                    confirmarNotificacionRecibida(estacionId, transaccionesProcesadas);
                }, 2000);
            });
            socket.on("resume-transaction", async (data) => {
                console.log("resume-transaction: " + dayjs().format());
                console.log(data);
                let estacionId = $("#hide_est_id").val();
                let transaccionesProcesadas = [];
                for (let i = 0; i < data.length; i++) {
                    let transaccionPickup = data[i];
                    let {
                        transaccion,
                    } = transaccionPickup;
                    transaccionesProcesadas.push(transaccion);
                    await agregarPedidoAlTablaDom(transaccionPickup);
                }
                confirmarNotificacionRecibida(estacionId, transaccionesProcesadas);
        });
            socket.on("new-notify-transaction", async (data) => {
                console.log("new-transaction: " + dayjs().format());
                console.log(data);
                await agregarPedidoAlTablaDom(data);

            });
        };
        /**
         * Inicio del servicio del socket
         * 1. Cargar las notifiaciones
         * 2. Reinicializar notificaciones cargadas
         * 3. Configurar botones
         * 4. Eventos de lista de ordenes
         * 5. Inicializar socket.
         */
        pedidosNotifyMemory.loadFromLocalStorage().then(() => {
            reinitializePedidosPickup().then(() => {
                setupListenerBtnMenu();
        agregarEventoListaOrdenes();
                startSocket();
            });
        });

    }
});

// Kiosko y Pickup
function agregarEventoListaOrdenes() {
    $orders.on("click", ".liKiosko", function(e) {
        var elemento = this;
		var datosOrden = $(this).data();
        if (!($("#listadoPedido li").length)) {
            seleccionarOrdenEfectivo(this, $(this).data());
        } else {
            alertify.confirm("La orden actual se perderá. ¿Está seguro que desea continuar?", function(e) {
                if (e) {
                    seleccionarOrdenEfectivo(elemento, datosOrden);
                }
            });
        }
    });
}

// Retorna elemento <li>
function buscarElementoTransaccion(factura) {
    var $elementoLi = null;
    $orders.find("li").each(function() {
        if ($(this).data("factura") == factura) {
            $elementoLi = $(this);
        }
    });

    return $elementoLi;
}

function truncarPalabra(palabra, longitud) {
    return (palabra.length > longitud) ? palabra.toUpperCase().substring(0, longitud) + "..." : palabra.toUpperCase();
}