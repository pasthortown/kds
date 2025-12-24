<style type="text/css">
   * {
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
    }
#mdlPrincipalAgregadores{
    display:none;
}
    .modal-Agregadores {
        position: fixed;
        top: 0;
        left: 0;
        width: 150%;
        height: 766px;
        background: rgba(0, 0, 0, 0.2);
        z-index: 999999;
        margin: 0;
        padding: 0;
        opacity: 0.0;
    }
    
    .modal {
        position: fixed;
        top: 40%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 600px;
        overflow: hidden;
        z-index: 1050;
        outline: 0;
    }
    
    ::before, ::after {
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
    }
    
    .modal-dialog-agregador {
	position: absolute;
        top: auto;
        left: -10px;
        width: 40px;
    }    
    
    .modal-content {
	position: relative;
	background-color: #fff;
	background-clip: padding-box;
	border: 1px solid rgba(0,0,0,.2);
	border-radius: 6px;
	outline: 0;	
    }    
    
    .modal-header-Agregadores {
        padding: 9px 15px;
        border-bottom: 1px solid #eee;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }
    
    .titulo-modal-Agregadores {
        font-size: 21px; 
        color: white; 
        font-family: Arial; 
        text-align: center;
    }
    
    .modal-body {
	position: relative;
	padding: 15px;
    }
    
    #contenedorAgregadores .jb-shortscroll-wrapper {
        left: 750px;  
    } 
    
    .modal-footer {
        padding: 15px;
        text-align: right;
        border-top: 1px solid #e5e5e5
    }
    
    .btn-bootstrap {
        padding: .375rem .75rem;
        border-radius: .25rem;
        color: #fff;
	background-color: #c9302c;
	border-color: #ac2925;
        height: 55px;
        display: block;
	width: 100%;
        font-size: 18px;
        opacity: 0.9;
    }

    .btn-bootstrap:hover, .btn-bootstrap:focus {
        background-color: #ac2925;
    }

    .btn-bootstrap:active {
        background-color: #ac2925;
    }
    
    .bordeAgregador {
        border-style: groove;
        border-color: silver;
        border-width: 1px;
        float: left;
        width: 50%;
    }

    #contenedorAgregadores .jb-shortscroll-wrapper {
        left: 480px;  
    }
    
    .botonAgregadores {
        font-family: Arial;
        font-size: 34px;
        font-weight: bold;
        color: #ffffff;
        background-color: #77b55a;
        height: 80px; 
        width: 150px;
        vertical-align: middle;
        border: 1px solid #4b8f29;
        -moz-box-shadow: 0px 0px 0px 0px #3e7327;
        -webkit-box-shadow: 0px 0px 0px 0px #3e7327;
        box-shadow: 0px 0px 0px 0px #3e7327;
        background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #77b55a), color-stop(1, #72b352));
        background:-moz-linear-gradient(top, #77b55a 5%, #72b352 100%);
        background:-webkit-linear-gradient(top, #77b55a 5%, #72b352 100%);
        background:-o-linear-gradient(top, #77b55a 5%, #72b352 100%);
        background:-ms-linear-gradient(top, #77b55a 5%, #72b352 100%);
        background:linear-gradient(to bottom, #77b55a 5%, #72b352 100%);
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#77b55a', endColorstr='#72b352',GradientType=0);
        -moz-border-radius: 6px;
        -webkit-border-radius: 6px;
        border-radius: 6px;
        display: inline-block;
        cursor: pointer;
        margin-bottom:0px;
        margin-top: 0px;
        margin-left: 0px;
        margin-right: 0px;
    }  
    
    .nombreAgregador {   
        margin: 5px;       
        font-size: 12pt;
    }
</style>

<div id="mdlPrincipalAgregadores" class="modal-Agregadores">
    <div id="mdlAgregdores" class="modal" style="height: 450px;">
        <div class="modal-dialog-agregador" style="width:620px;">
            <div class="modal-content">
                <div class="modal-header-Agregadores" style="background-color: #0a98bb;" id="cabeceraAgreadores">
                    <h4 class="modal-title titulo-modal-Agregadores">
                        Seleccione Agregador...      
                    </h4>
                </div>
                <div class="modal-body">
                    <div id="contenedorAgregadores">                                
                        <div class="contenedorGeneral" style="width:620px; height: 270px; overflow: auto; overflow-x: hidden; overflow-y: hidden;"> 
                            <div class="row contenedor" id="contenedor" style="width:510px; height: 275px; left: 50px;">
                                <div id="agregadores" style="padding-top: 15px;"></div> 
                            </div>                                                                        
                        </div>  
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-bootstrap" data-dismiss="modal" onclick="cerrarModalAgegadores();">Cancelar</button>                        
                </div>
            </div>      
        </div>        
    </div>    
</div>

<!-- Modal Credenciales de Administrador -->
<div id="credenciales_administrador" title="Ingrese las Credenciales del Administrador" style="display:none;" align="center">
    <div class="anulacionesSeparador">
        <div class="anulacionesInput">
            <input inputmode="none"  type="password" id="clave_administrador" style="height: 35px; width: 454px; font-size: 16px;"/>
        </div>
    </div>
    <table id="tabla_credenciales_administrador" align="center">
        <tr>
            <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(clave_administrador, 7)">7</button></td>
            <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(clave_administrador, 8)">8</button></td>
            <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(clave_administrador, 9)">9</button></td>
            <td><button style="font-size:45px;" class='btnVirtualOKpq' onclick="fn_eliminarNumero(clave_administrador);">&larr;</button></td>
        </tr>
        <tr>
            <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(clave_administrador, 4)">4</button></td>
            <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(clave_administrador, 5)">5</button></td>
            <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(clave_administrador, 6)">6</button></td>
            <td><button style="font-size:45px;" class='btnVirtualOKpq' onclick="fn_eliminarTodo(clave_administrador);">&lArr;</button></td>
        </tr>
        <tr>
            <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(clave_administrador, 1)">1</button></td>
            <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(clave_administrador, 2)">2</button></td>
            <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(clave_administrador, 3)">3</button></td>
            <td id="boton_validacion"></td>
        </tr>
        <tr>
            <td><button style="font-size:45px;" class='btnVirtual' onclick="fn_agregarCaracterNum(clave_administrador, 0)">0</button></td>
            <td colspan="4"><button style="font-size:45px;" class='btnVirtualCancelar' onclick="cerrarModalCredencialesAdministrador();">Cancelar</button></td>           
        </tr>
    </table>
</div>