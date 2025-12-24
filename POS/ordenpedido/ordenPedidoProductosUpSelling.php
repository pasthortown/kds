<style type="text/css">
    .modal-UpSelling {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #fff;
        z-index: 999999;
        margin: 0;
        padding: 0;
    }

    .modal-header-UpSelling {
        padding: 9px 15px;
        border-bottom: 1px solid #eee;
        background-color: #0480be;
        -webkit-border-top-left-radius: 5px;
        -webkit-border-top-right-radius: 5px;
        -moz-border-radius-topleft: 5px;
        -moz-border-radius-topright: 5px;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
    }

    .full-width {
        display: block;
        width: 150px;
    }

    #mdlUpSelling{
        align-content: center;
    }
</style>

<link rel="stylesheet" href="../upselling/pantalla/styles.css" media="print" onload="this.media='all'">
<noscript>
    <link rel="stylesheet" href="../upselling/pantalla/styles.css">
</noscript>

<link href="../css/upselling_icon.css" rel="stylesheet">

<div id="mdlPrincipal" 
    class="modal-UpSelling" 
    style="display: none;" 
    data-menu-id=""
    data-clasificacion-id=""
    data-categoria-id=""
    data-cadena=""
    data-restaurante=""
    data-producto-base="" 
    data-producto="" 
>
    <app-root></app-root>
</div>

<input type="hidden" name="hide_idProductoBase" id="hide_idProductoBase"/>
<input type="hidden" name="hide_magp_id" id="hide_magp_id"/>

<script src="../upselling/pantalla/polyfills.js" type="module"></script>
<script src="../upselling/pantalla/main.js" type="module"></script>

<script type="text/javascript" src="../js/ajax_ordePedidoProductosUpSelling.js"></script>
