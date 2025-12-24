<?php

class CallREST {

    /* Funcion generica para llamar end point */
    function call($request, $timeOut = 3) {

        $curl = curl_init();
        curl_setopt_array($curl, $request->headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        //curl_setopt($curl, CURLOPT_NOBODY , true);
        //curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); 
        curl_setopt($curl, CURLOPT_TIMEOUT,$timeOut);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,$timeOut);

        
       
        //Objeto response donde se recibira la respuesta del endpoint
        $response = new stdClass();
        $response->data = '';
        try {
            
            //Respuesta
            $curl_response = curl_exec($curl);
            if(!$curl_response || strlen(trim($curl_response)) == 0){
                $response->httpStatus = 408;
                $response->exception = true;
                $response->exceptionMessage = 'Se encontró una respuesta vacia en el request : ' . $request->url;
            }else{
                $response->data = $curl_response;
                //Obtener CodigoError
                $response->numberError = curl_errno($curl);
                // print "Number Error: " . $response->numberError;

                //Obtener error
                $response->error = curl_error($curl);
                // print "Error: " . $response->error . "<br/>";

                //OBtener estado http
                $response->httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                // print "Status: " . $response->httpStatus . "<br/>";

                if($response->numberError != CURLE_OK) {
                    $response->httpStatus = 408;
                    $response->exception = true;
                    $response->exceptionMessage = $response->numberError . ": " . curl_strerror($response->numberError);
                }
            }
        } catch (Exception $e) {
            $response->httpStatus = 500;
            $response->exception = true;
            $response->exceptionMessage = "Error interno del servidor: " . $e->getMessage();
        }

        curl_close($curl);

        return $response;

    }

}