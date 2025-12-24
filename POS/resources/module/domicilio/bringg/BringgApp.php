<?php

/////////////////////////////////////////////////////////////
//// DESARROLLADO POR: Aldo Navarrete L.                  ///
//// DESCRIPCION: Creacion de Orden desde MaxPoint        ///
//// FECHA CREACION: 06/08/2020                           ///
//// FECHA ULTIMA MODIFICACION:                           ///
//// USUARIO QUE MODIFICO:                                ///
//// DECRIPCION ULTIMO CAMBIO:                            ///
/////////////////////////////////////////////////////////////

$ds = DIRECTORY_SEPARATOR;
$base_dir = realpath(dirname(__FILE__) . $ds . "..") . $ds;

include_once "{$base_dir}{$ds}../../../system{$ds}conexion{$ds}clase_sql.php";
include_once "{$base_dir}{$ds}clases/clase_bringg.php";

/**
 * \file BringgApp.php
 * \brief cuerpo de la petición
 * Se realiza la generacion de POOL de Bringg para asignacion de domiciliario
 */
/**
 * \class BringgApp
 * \brief cuerpo de la petición
 * Se realiza la generacion de POOL de Bringg para asignacion de domiciliario
 * @author Angel Renteria
 */
class BringgApp extends sql{


    public $bringg;
    public $codFactura;
    public $cdn_id;
    public $rst_id;
    public $cabecera_factura;
    
    function __construct($cdn_id, $rst_id ) {
        $this->bringg =  new Bringg();
        $this->cdn_id = $cdn_id;
        $this->rst_id = $rst_id;
    }

    /**
     * \fn crearOrden
     * \brief Permite generar la orden que se envía a bringg.
     * Se hicieron adecuaciones para que se incluya información coo el id pool que se envía a bring y asi puedan identificar el pool.
     * @param urlConsumo url de consumo de bringg
     * @param codigoFactura el codigo de factura que se consulta en la base de datos y del que se genera la información
     * @param codigoApp el codigo de app unico generado en el maxpoint
     */
    function crearOrden($urlConsumo, $codigoFactura, $codigoApp){

        $this->cabecera_factura = [];
       
        $lc_sql = "EXEC dbo.App_cargar_pedido_bringg $this->cdn_id, $this->rst_id, '$codigoApp'";
        try {
            
            $lc_rowdatos = [];

            $this->bringg->fn_ejecutarquery($lc_sql);
            if ($this->bringg->fn_numregistro() > 0) {
                $row = $this->bringg->fn_leerarreglo();
                $lc_rowdatos = $row;
            }


            if($this->bringg->fn_numregistro() > 0 && isset($lc_rowdatos)) {


                $codTienda = $lc_rowdatos['Cod_Tienda'];
                $codigoFacturaBringg= $lc_rowdatos['Cod_Factura'];
                //$codTienda = 'K024';

                $requiredSkills = $lc_rowdatos['required_skills'];

                if(($codTienda!=null) || ($codTienda!='')) {
                    $fpago      = $lc_rowdatos['fpago'];
                    $total      = $lc_rowdatos['total'];
                    $delivery   = $lc_rowdatos['delivery'];
                    
                    //Datos del cliente
                    /**
                     * Se incluye datos necesarios para Pool de motorizados bringg
                     * $DirRest  -  $TelRest - $latRest - $longRest - $PoolTrue   
                    */
                    $nombre     = utf8_encode($lc_rowdatos['nombre']);
                    $telefono   = $lc_rowdatos['telefono'];
                    $direccion  = utf8_encode($lc_rowdatos['direccion']);
                    $ciudad     = utf8_encode($lc_rowdatos['ciudad']);
                    $email      = utf8_encode($lc_rowdatos['email']); 
                    $lat        = $lc_rowdatos['latitud'];
                    $lng        = $lc_rowdatos['longitud'];
                    $PoolTeam   = $lc_rowdatos['TeamPool'];
                    $TagId      = $lc_rowdatos['tagId'];  
                    $TaskIdConf = $lc_rowdatos['TaskIdConf'];
                    $NameRest   = utf8_encode($lc_rowdatos['NameRest']);
                    $DirRest    = utf8_encode($lc_rowdatos['DirRest']);
                    $TelRest    = $lc_rowdatos['TelRest'];
                    $latRest    = $lc_rowdatos['LatRest'];
                    $longRest   = $lc_rowdatos['LongRest'];
                    $PoolTrue   = $lc_rowdatos['Pooling'];	

                    // way_points
                    $refencia   = utf8_encode($lc_rowdatos['inmueble']); 
                    $pais       = $lc_rowdatos['pais'];
                    $numero     = utf8_encode($lc_rowdatos['numero']);
                    $inmueble   = ''; //NO EXISTE
                    $codCliente = $lc_rowdatos['clienteBringg']; 
                    $entrega    = $lc_rowdatos['entrega'];

                    //ini - fin
                    $VenIni     = $lc_rowdatos['VenIni'];
                    $VenFin     = $lc_rowdatos['VenFin'];


                    // consulta de detalle de la orden
                    $this->bringg->datosDetalle($codigoFactura);
                    while ($lc_rowdatos = $this->bringg->fn_leerarreglo()) {
                        $productosT[] = array(
                                'price' => $lc_rowdatos['precio'],
                                'name' => utf8_encode($lc_rowdatos['Descripcion']),
                                'external_id' => $lc_rowdatos['Cod_Plu'],
                                'quantity' => $lc_rowdatos['Cantidad'],
                                'pending' => true,
                            );
                        $productosF[] = array(
                                'price' => $lc_rowdatos['precio'],
                                'name' => utf8_encode($lc_rowdatos['Descripcion']),
                                'external_id' => $lc_rowdatos['Cod_Plu'],
                                'quantity' => $lc_rowdatos['Cantidad'],
                                'pending' => false,
                            );
                    }

                    $clienteO = array(
                        'external_id' => $codCliente,
                        'name' => $nombre,
                        'phone' => $telefono,
                        'address' => utf8_encode($direccion).', '.$ciudad.', '.$pais,
                        'address_second_line' => '# '.utf8_encode($numero).' - '.utf8_encode($refencia),
                        'email' => $email,
                        'city' => $ciudad,
                        'lat' => $lat,
                        'lng' => $lng,
                    );
                
                    $clienteL = array(
                        'external_id' => $codTienda,
                        'name' => 'Local '.$codTienda,
                        'address' => 'AV. Amazonas y el Inca'.', '.$ciudad.', '.$pais, //CONSULTAR
                        'phone' => '2245777', //CONSULTAR
                        'email' => 'domikfc024@kfc.com.ec', //CONSULTAR
                        'city' => $ciudad //NO EXISTE
                    );

                    /**
                     * Se incluye array con informacion de  $tienda 
                     * @autor Angel Renteria
                     * @retunr array  $tienda
                     */ 

                    $tienda = array(
                        'external_id' => $codTienda,
                        'name' => $NameRest,
                        'phone' => $TelRest,
                        'address' => utf8_encode($DirRest).', '.$ciudad.', '.$pais,
                        'email' => '',
                        'city' => $ciudad,
                        'lat' => $latRest,
                        'lng' => $longRest,

                    );
                
                    /**
                     * cuerpo de la petición
                     *  Se realiza validacion de POOL de Bringg para asignacion de domiciliario
                     * @autor Angel Renteria
                     * return array para JSON $data
                     * JSON para enviar a url de solicitud de asignacion d emotorizado
                    */
                    if($PoolTrue==1){

                        $data = array(
                            'title' => 'Pedido'.$PoolTeam,
                            'external_id' => $codigoFacturaBringg,
                            'ready_to_execute' => true,
                            'payment_method' => $fpago,
                            'total_price' => $total,
                            'delivery_price' => $delivery,
                           // 'task_configuration_id'=> $TaskIdConf,
                            "tag_id"=> $TagId,
                            'external_team_id' => $PoolTeam,
                            'customer' => $clienteO,
                           // 'inventory' => $productosT,
                            'way_points' => array(       
                                array(
                                    'position' => 1,
                                    "pickup_dropoff_option"=> 0,	
                                    'inventory' => $productosF,
                                    'customer' => $tienda,
                                    'address' => $DirRest.', '.$ciudad.', '.$pais,
                                    'country' => $pais,
                                    'city' => $ciudad,
                                    //'scheduled_at' => $entrega,
                                    'no_earlier_than' => $VenIni,
                                    'no_later_than' => $VenFin,
                                    'pending' => true,

                                ),
                                array(
                                    'position' => 2,
                                    "pickup_dropoff_option"=> 1,
                                    'inventory' => $productosF,
                                    'customer' => $clienteO,
                                    'address' => $direccion.', '.$ciudad.', '.$pais,
                                    'country' => $pais,
                                    'city' => $ciudad,
                                    //'scheduled_at' => $entrega,
                                    'no_earlier_than' => $VenIni,
                                    'no_later_than' => $VenFin,
                                    'pending' => false,

                                ),
                            ),
						  
                            //'external_team_id' => $codTienda,
                            'share_location' => TRUE,
    
                //            'required_skills'=> [$requiredSkills],
                            "task_type_id" => 4
                        );

                /**
                     * cuerpo de la petición
                     *  Se realiza validacion de POOL de Bringg para asignacion de domiciliario
                     * @autor Angel Renteria
                     * return array para JSON $data
                     * JSON para enviar a url de solicitud de asignacion d emotorizado
                    */
                    
                    }elseif($PoolTrue==0){
                        $data = array(
                            'title' => 'Pedido'.$codTienda,
                            'external_id' => $codigoFacturaBringg,
                            'ready_to_execute' => true,
                            'payment_method' => $fpago,
                            'total_price' => $total,
                            'delivery_price' => $delivery,
                            'task_configuration_id'=> $TaskIdConf,
                            'external_team_id' => $codTienda,
                            'customer' => $clienteO,
                            'inventory' => $productosT,
                            'way_points' => array(       
                                array(
                                    'position' => 1,
                                    'inventory' => $productosF,
                                    'customer' => $clienteO,
                                    'address' => $direccion.', '.$ciudad.', '.$pais,
                                    'country' => $pais,
                                    'city' => $ciudad,
                                    'scheduled_at' => $entrega,
                                ),
                            ),
                            'external_team_id' => $codTienda,
                            'share_location' => TRUE,
    
                //            'required_skills'=> [$requiredSkills],
                            "task_type_id" => 5
                        );
                    }
                   




                    $content = json_encode($data);
                    $url = $urlConsumo;


                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, ($url));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                    'Content-Type:application/json',
                                    'Content-Length: '.strlen($content), ));
                    $result = curl_exec($ch);

                   

                $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    
                    if ($status != 200) {
                        echo "Error: call to URL $url failed with status $status, response $result, curl_error ".curl_error($curl).', curl_errno '.curl_errno($curl);
                        die("Error: call to URL $url failed with status $status, response $result, curl_error ".curl_error($curl).', curl_errno '.curl_errno($curl));
                    }
                    $response = json_decode($result);
                
                    if ($response->success) {
                        $idOrden = $response->task->id;
                        $lat = $response->task->lat;
                        $lng = $response->task->lng;
                        $idCliente = $response->task->customer_id;
                        $this->bringg->updateOrden($idOrden, $idCliente, $lat, $lng, $codigoFactura, $codCliente, $telefono, $inmueble, $codigoApp, $result);
                        //$this->bringg->eliminarUltimoEstadoPorAsignar($codigoApp);
                        $this->bringg->insertaAuditoria($url, 'Crear Orden orden: '.$codigoFactura.' '.$content, $status, json_encode($response));
                        
                        $lc_sql_tracking = "EXEC dbo.respuesta_bringg_tracking  '$codigoApp'";

                        $this->bringg->fn_ejecutarquery($lc_sql_tracking);
                        if ($this->bringg->fn_numregistro() > 0) {
                            $row_tracking = $this->bringg->fn_leerarreglo();
                        }

                        $objTracking = new \stdClass();
                        $objTracking->order_id = $row_tracking['order_id'];
                        $objTracking->status = 'Por Asignar';

                        $objClientBringg =  new \stdClass();
                        $objClientBringg->id = $row_tracking['client_bringg_id'];
                        $objCoordinate = new \stdClass();
                        $objCoordinate->latitude = $row_tracking['client_bringg_latitude'];
                        $objCoordinate->longitude = $row_tracking['client_bringg_longitude'];
                        $objClientBringg->coordinate = $objCoordinate;
                        $objTracking->client_bringg = $objClientBringg;

                        $objOrderBringg = new  \stdClass();
                        $objOrderBringg->uuid = $row_tracking['order_bringg_uuid'];
                        $objOrderBringg->task_id = $row_tracking['order_bringg_task_id'];
                        $objTracking->order_bringg = $objOrderBringg;

                        $objSharedLocation = new  \stdClass();
                        $objSharedLocation->uuid = $row_tracking['shared_location_uuid'];
                        $objTracking->shared_location = $objSharedLocation;
                        
                        return json_encode($objTracking);
                    } else {
                        $this->bringg->insertaAuditoria($url, 'Completar orden: '.$codigoFactura, $status, $result);
                        return json_encode('error');
                    }
                    //$this->bringg->fn_liberarecurso();
                }

            } else {
                $this->bringg->insertaAuditoria('', 'Completar orden: '.$codigoFactura, 'ERROR', 'NO APLICA');
                return json_encode('NO APLICA');
            }

            
        } catch (Exception $e) {

            $this->bringg->insertaAuditoria('', 'Completar orden: '.$codigoFactura, 'ERROR', json_encode($e));
            return $e;
        }
    }


    function anularOrden($urlConsumo, $idBringg, $codFactura){

        if ($idBringg != null) {
            $data = array(
                'id' => $idBringg
            );

            $content = json_encode($data);

            //$this->bringg->fn_liberarecurso();
            $url = $urlConsumo;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, ($url));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Content-Length: '.strlen($content)));

            $result = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        
            if ($status != 200) {
                echo "Error: call to URL $url failed with status $status, response $result, curl_error ".curl_error($curl).', curl_errno '.curl_errno($curl);
                die("Error: call to URL $url failed with status $status, response $result, curl_error ".curl_error($curl).', curl_errno '.curl_errno($curl));
            }
            $response = json_decode($result);
            if ($response->success) {
                $this->bringg->insertaAuditoria($url, 'Cancelar orden: '.$codFactura, $status, $response->success);
                return 'OK';
            } else {
                $this->bringg->insertaAuditoria($url, 'Cancelar orden: '.$codFactura, $status, $result);
                return 'error';
            }

        }        
    }


}



