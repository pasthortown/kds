<div class="modal-headertipo" style="background-color: #0a98bb;">
    <h4 class="modal-title" style="font-size: 21px;color:white;font-family: Arial;text-align: center; ">
        CAMPAÑA SOLIDARIA
        <img src="../imagenes/admin_resources/btn_eliminar.png" onclick="fn_cerrarModalCampanaSolidaria()" class="btn_cerrar_modal_cupones"/>
    </h4>
</div>
<div style="text-align:center;">
    <div class="pantallaCalculadora" id="pantallaCalculadora">
        <table class="tbl_total_factura" align="center" border="0" width="60%">
            <tbody>
                <tr>
                    <td width="40%" style="text-align: right; font-family: Arial;"><b>Valor Unitario:</b></td>
                    <td width="50%" style="text-align: right; font-family: Arial;">
                        <div id="valorUnitario" name="valorUnitario" class="valores"></div>
                        <input inputmode="none" type="hidden" id="valorUnitarioCampañaSolidaria">
                    </td>
                </tr>
                <tr>
                    <td width="40%" style="text-align: right; font-size: 34px; font-family: Arial;">
                        <b>TOTAL:</b>
                    </td>
                    <td width="50%" style="text-align: right; font-size: 34px; font-family: Arial;">
                        <div id="valorTotal" name="valorTotal" class="valores">3.75</div>
                        <input inputmode="none" type="hidden" id="valorTotalCampañaSolidaria">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <label style="margin-top: 30px;">Cantidad: </label>
    <input inputmode="none" type="text" name="cantidadCampañaSolidaria" id="cantidadCampañaSolidaria" readonly style="width: 190px; height: 30px;"/>
    <table style="margin: 20px auto;">
        <tr>
            <td>
                <button class="btnVirtual" onclick="fn_agregarNumeroCampanaSolidaria('7')">7</button>
            </td>
            <td>
                <button class="btnVirtual" onclick="fn_agregarNumeroCampanaSolidaria('8')">8</button>
            </td>
            <td>
                <button class="btnVirtual" onclick="fn_agregarNumeroCampanaSolidaria('9')">9</button>
            </td>
            <td rowspan="2">
                <button id="ok_cantidad" class="btnVirtualOkLargo" onclick="fn_okCantidadCampanaSolidaria()">OK</button>
            </td>
        </tr>
        <tr>
            <td>
                <button class="btnVirtual" onclick="fn_agregarNumeroCampanaSolidaria('4')">4</button>
            </td>
            <td>
                <button class="btnVirtual" onclick="fn_agregarNumeroCampanaSolidaria('5')">5</button>
            </td>
            <td>
                <button class="btnVirtual" onclick="fn_agregarNumeroCampanaSolidaria('6')">6</button>
            </td>
        </tr>
        <tr>
            <td>
                <button class="btnVirtual" onclick="fn_agregarNumeroCampanaSolidaria('1')">1</button>
            </td>
            <td>
                <button class="btnVirtual" onclick="fn_agregarNumeroCampanaSolidaria('2')">2</button>
            </td>
            <td>
                <button class="btnVirtual" onclick="fn_agregarNumeroCampanaSolidaria('3')">3</button>
            </td>
            <td>
                <button class="btnVirtualBorrar" onclick="fn_eliminarCantidadCampanaSolidaria()">&larr;</button>
            </td>
        </tr>
        <tr>
            <td>
                <button id="btn_cantidad_cero" class="btnVirtual" onclick="fn_agregarNumeroCampanaSolidaria('0')">0</button>
            </td>
            <td colspan="3">
                <button class="btnVirtualCancelar"
                        id="btn_punto"
                        onclick="fn_cancelarAgregarCantidadCampanaSolidaria()"
                        style="width: 200px;">Limpiar </button>
            </td>
        </tr>
    </table>
</div>