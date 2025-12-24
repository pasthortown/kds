<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once './BaseStatusResponseModel.php';

class StatusResponseModel extends BaseStatusResponseModel {

    /// <summary>
    /// Mensaje del estado de la transaccion
    /// </summary>
    public $Message;
    /// <summary>
    /// Datos de respuesta de la transaccion
    /// </summary>
    public $Data;

}
