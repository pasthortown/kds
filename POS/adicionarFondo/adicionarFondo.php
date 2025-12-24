<?php
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>Toma de Pedido</title>

        <!-- Librerias CSS -->
         <link rel="stylesheet" href="../bootstrap/css/bootstrap.css" type="text/css"/>
        <link rel="stylesheet" href="../bootstrap/css/bootstrap-theme.min.css" type="text/css"/>
        <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css" type="text/css"/>
        
        <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css"/>
        <link rel="stylesheet" type="text/css" href="../css/alertify.core.css"/>
        <link rel="stylesheet" type="text/css" href="../css/alertify.default.css"/>      
        <link rel="stylesheet" type="text/css" href="../css/teclado_facturacion.css"/> 
        <link rel="stylesheet" type="text/css" href="../css/movimientos.css"/>
        <link rel="stylesheet" type="text/css" href="../css/adicionarfondo.css"/>
        
    </head>
    <body>       
        <div  class="centrar" id="div_ingresoFondos"> 

   <div id="IngresoFondos">
       <div class="preguntasTitulo" style="font-size: 30px;" align="center">Adicionar Fondo</div>
    <div class="anulacionesSeparador">
        <div class="anulacionesInput"><span><img src="../imagenes/billete.png" height="85px" width="55px"></span> &nbsp;<input inputmode="none"  type="text" id="usr_admin_fondo" style="width: 380px; font-size: 40px;" readonly="readonly" />				
        </div>       
    </div>
</div>

<div id="IngresoFondosteclado"> 
    <table id="tabla_credencialesAdminfondo" align="center">
        <tr>
            <td><button class='btnVirtual' onClick="fn_agregarCaracter(usr_admin_fondo,7)">7</button></td>
            <td><button class='btnVirtual' onClick="fn_agregarCaracter(usr_admin_fondo,8)">8</button></td>
            <td><button class='btnVirtual' onClick="fn_agregarCaracter(usr_admin_fondo,9)">9</button></td>
            <td><button class='btnVirtualOKpq' onClick="fn_eliminarNumero(usr_admin_fondo);">&larr;</button></td>
        </tr>
        <tr>
            <td><button class='btnVirtual' onClick="fn_agregarCaracter(usr_admin_fondo,4)">4</button></td>
            <td><button class='btnVirtual' onClick="fn_agregarCaracter(usr_admin_fondo,5)">5</button></td>
            <td><button class='btnVirtual' onClick="fn_agregarCaracter(usr_admin_fondo,6)">6</button></td>
            <td><button class='btnVirtualOKpq' onClick="fn_eliminarTodo(usr_admin_fondo);">&lArr;</button></td>
        </tr>
        <tr>
            <td><button class='btnVirtual' onClick="fn_agregarCaracter(usr_admin_fondo,1)">1</button></td>
            <td><button class='btnVirtual' onClick="fn_agregarCaracter(usr_admin_fondo,2)">2</button></td>
            <td><button class='btnVirtual' onClick="fn_agregarCaracter(usr_admin_fondo,3)">3</button></td>
            <td><button class='btnVirtualOKpq' onClick="fn_adicionarFondo();">OK</button></td>
        </tr>
        <tr>
            <td><button class='btnVirtual' onClick="fn_agregarCaracter(usr_admin_fondo,0)">0</button></td>
            <td><button class='btnVirtual' id='btn_punto' onClick="fn_agregarCaracter(usr_admin_fondo,'.')">.</button></td>
            <td colspan="4"><button style="width:195px" class='btnVirtualOKpq' onClick="fn_cancelarAdicionFondo();">Cancelar</button></td>           
        </tr>
    </table> 
</div>
            
            
        </div>
        
        <input inputmode="none"  type="hidden" id="txt_moneda" value="<?php echo $_SESSION['simboloMoneda'] ?>"/>
        
        
        <!-- Librerias JavaScript -->
        <script src="../js/jquery.min.js"></script>
        <script type="text/javascript" src="../js/jquery-ui.js"></script>
        
        <script type="text/javascript" src="../js/alertify.js"></script>
        <script type="text/javascript" src="../js/ajax_adicionar_fondo.js"></script>
           <script type="text/javascript" src="../js/teclado_credenciales.js"></script>  
           <script src="../js/ajax_api_impresion.js"></script> 
    </body>
</html>