<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Modelo contededor de los datos a enviar
 * */
class TransactionRequestModel {

    //put your code here
    public $Amount;
    public $AmountWithTax;
    public $AmountWithOutTax;
    public $PurchaseLanguage;
    public $Tax;
    public $TimeZone;
    public $Latitud;
    public $Longitud;
    public $Token;
    public $ClientTransactionId;
    public $ClientUserId;
}
