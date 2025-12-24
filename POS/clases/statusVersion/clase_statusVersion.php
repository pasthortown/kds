<?php

require "model_statusVersion.php";

class StatusVersion extends sql{

    private $modelStatusVersion;

	//constructor de la clase
    function __construct(){
        parent ::__construct();
        $this->modelStatusVersion = new ModelStatusVersion();
    }

    /**
     * Funcion que valida si la version de la estacion tiene actualmente una actualizacion
     * Pendiente(borrado de la cache)
     */
    public function getStatusLimpiaCachePendiente(
        $ipEstacion,
        $idEstacion,
        $isUrlAdmin,
        $estacionAdmin
    ){
        
        return $this->getDataStatusVersionPorSessionNoEncontrada($ipEstacion, $idEstacion, $isUrlAdmin, $estacionAdmin);
        
    }

    /**
     * Funcion que permite registrar la limpieza de la cache de una determinada estacion
     */
    public function limpiaCacheEstacion(
        $borradoAplicado,
        $idEstacion = null
    ){
        $this->modelStatusVersion = $this->fixObject($_SESSION['statusVersion']);
        if ($borradoAplicado) {
            $lc_sql = "UPDATE EstacionStatusVersion SET
            fecha_aplico = GETDATE()
            ,last_update = GETDATE()
            ,estado = 1
            WHERE id_estacion_status_version = '".$this->modelStatusVersion->getIdEstacionStatusVersion()."'";
        }else{
            $lc_sql = "UPDATE EstacionStatusVersion SET
            fecha_aplico = NULL
            ,last_update = GETDATE()
            ,estado = 0
            WHERE id_estacion_status_version = '".$this->modelStatusVersion->getIdEstacionStatusVersion()."'";
        }
        
        try {
            return $this->fn_ejecutarquery( $lc_sql );
        } catch (Exception $e) {
            print $e;
        }
    }

    /**
     * Funcion que consulta la version en BD
     */

     function getDataStatusVersionPorSessionNoEncontrada($ipEstacion, $idEstacion, $isUrlAdmin, $estacionAdmin){
        $lc_sql ="EXEC [seguridad].[GetEstacionStatusVersion]  '$ipEstacion', '$idEstacion', $isUrlAdmin ";
       
        try {
            $this->fn_ejecutarquery( $lc_sql );
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( 
                    "id" => $row['id_status_version'],
                    "fecha_status_version" => $row['fecha_status_version'],
                    "aplica_limpiado_cache" => (int) $row['aplica_limpiado_cache'],
                    "id_estacion" => $row['id_estacion'],
                    "mensaje" => $row['mensaje'],
                    "id_estacion_status_version" => $row['id_estacion_status_version'],
                );
                

                $this->modelStatusVersion->setIdEstacion($row['id_estacion']);
                $this->modelStatusVersion->setIdStatusVersion($row['id_status_version']);
                $this->modelStatusVersion->setFechaStatusVersion($row['fecha_status_version']);
                $this->modelStatusVersion->setAplicaLimpiadoCache($row['aplica_limpiado_cache']);
                $this->modelStatusVersion->setIdEstacionStatusVersion($row['id_estacion_status_version']);
                $this->modelStatusVersion->setIpEstacion($row['ip_estacion']);
                $this->modelStatusVersion->setIdCadena($row['id_cadena']);

                $_SESSION['statusVersion'] = $this->modelStatusVersion;

            }
            
            $this->lc_regs['registros'] = $this->fn_numregistro();
                
        } catch (Exception $e) {
            return $e;
        }

        return json_encode($this->lc_regs);
    }


    function fixObject(&$object) {       
        return unserialize(serialize($_SESSION['statusVersion']));    
    }

    function deleteDataCache(){

        try {
            $this->modelStatusVersion = $this->fixObject($_SESSION['statusVersion']);

            $endpointServicio   = $this->consultaVariableVColleccionCadena('ESTACION ESTATUS VERSION', 'ENDPOINT SERVICIO', $this->modelStatusVersion->getIdCadena());
            $puertoServicio     = $this->consultaVariableIColleccionCadena('ESTACION ESTATUS VERSION', 'PUERTO SERVICIO', $this->modelStatusVersion->getIdCadena());

            // Datos del servidor .NET Core
            $clientHost = $this->modelStatusVersion->getIpEstacion(); // Dirección IP del cliente
            $clientPort = $puertoServicio; // Puerto del servidor .NET Core

            // Crear los datos de la solicitud
            $data = json_encode(array(
                'ipEstacion' => $this->modelStatusVersion->getIpEstacion()
            ));

            $options = array(
                'http' => array(
                    'header'  => "Content-Type: application/json\r\n",
                    'method'  => 'POST',
                    'content' => $data,
                    'timeout' => 10, // Añadir un tiempo de espera para evitar que se quede cargando indefinidamente
                ),
            );

            //Actualizando el estado de limpieza de la cache
            $this->limpiaCacheEstacion(true);

            $context = stream_context_create($options);
            $url = "http://$clientHost:$clientPort".$endpointServicio;

            echo "Enviando solicitud a: $url\n";
            $response = file_get_contents($url, false, $context);

            if ($response === FALSE) {
                $this->limpiaCacheEstacion(false);
                echo "Error ejecutando el archivo de script en el cliente.\n";
                var_dump($response); // Mostrar los encabezados de respuesta HTTP para depuración
                exit(1);
            }

            // Imprimir el comando para depuración
            echo "Comando ejecutado: POST $url\n";

            // Verificar si hubo algún error en la ejecución
            $responseHeader = isset($http_response_header[0]) ? $http_response_header[0] : 'Sin respuesta';
            if (strpos($responseHeader, '200 OK') !== false) {
                echo "Script ejecutado correctamente.\n";
            } else {
                echo "Error al ejecutar el script. Respuesta del servidor: " . $responseHeader . "\n";
                $this->limpiaCacheEstacion(false);
            }

        } catch (\Throwable $th) {
            $this->limpiaCacheEstacion(false);
        }

    }

    public function consultaVariableVColleccionCadena($nombreColeccion, $nombreDato, $idCadena)
    {
        $lc_sql = "select [config].[fn_ColeccionCadena_VariableV] ('$idCadena','$nombreColeccion','$nombreDato') as dato";
        $dato='';
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $dato = trim($row['dato']);
            }
        }
        return $dato;
    }

    public function consultaVariableIColleccionCadena($nombreColeccion, $nombreDato, $idCadena)
    {
        $lc_sql = "select [config].[fn_ColeccionCadena_VariableI] ('$idCadena','$nombreColeccion','$nombreDato') as dato";
        $dato='';
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $dato = trim($row['dato']);
            }
        }
        return $dato;
    }

    /**
     * Funcion que valida el estado del servicio(API)
     */
    public function verificaStatusServices($ipEstacion, $idEstacion){
        if(!isset($_SESSION['statusVersion'])){
            $this->getDataStatusVersionPorSessionNoEncontrada($ipEstacion, $idEstacion, 0, 0);
        }

        $this->modelStatusVersion = $this->fixObject($_SESSION['statusVersion']);

        $endpointServicio   = $this->consultaVariableVColleccionCadena('ESTACION ESTATUS VERSION', 'ENDPOINT SERVICIO', $this->modelStatusVersion->getIdCadena());
        $puertoServicio     = $this->consultaVariableIColleccionCadena('ESTACION ESTATUS VERSION', 'PUERTO SERVICIO', $this->modelStatusVersion->getIdCadena());

        $clientHost = $this->modelStatusVersion->getIpEstacion(); // Dirección IP del cliente
        $clientPort = $puertoServicio; // Puerto del servidor .NET Core

        $url = "http://$clientHost:$clientPort".$endpointServicio.'/status';


        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $result = curl_exec($ch);
        // Saber el estado de la ejecucion del Ws  con 400(valida problema en parametro el curl valida problema de url)
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $response = array(
            'success' => false,
        );

        if($http_status === 200){
            $respuesta = json_decode($result, true);
            if( (isset($respuesta['success']) && $respuesta['success']) &&
                (isset($respuesta['statusTask']['isActive']) && $respuesta['statusTask']['isActive']) &&
                (isset($respuesta['statusTask']['exists']) && $respuesta['statusTask']['exists']))
            {
                $response['success'] = true;
            }
        }

        return json_encode($response);

    }

}