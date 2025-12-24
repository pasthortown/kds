<?php

class AsyncServicePrint extends Thread
{
    protected $httpHost;
    protected $endPoint;
    protected $headers;
    protected $tcaCodeNotPrint;
    protected $tcaCodeOkPrint;
    private $idCadena;
    private $idRestaurante;
    private $idPeriodo;
    private $session;
    private $estado;

    /**
     * @param $idCadena
     * @param $idRestaurante
     * @param $idPeriodo
     * @param $session
     * @param $estado
     */
    public function __construct($idCadena, $idRestaurante, $idPeriodo, $session, $estado = 'PRINCIPAL')
    {
        $this->idCadena = $idCadena;
        $this->idRestaurante = $idRestaurante;
        $this->idPeriodo = $idPeriodo;
        $this->session = $session;
        $this->estado = $estado;
        $this->httpHost = $_SERVER['HTTP_HOST'];
        $this->endPoint = "$this->httpHost/pos/impresion/cliente_ws_servicioImpresion.php";
        $this->headers = [
            'Content-Type: application/json',
        ];
        $this->tcaCodeNotPrint = 105;
        $this->tcaCodeOkPrint = 200;
    }

    /**
     *
     */
    public function run()
    {
        $this->taskPrintOrdersDelivery();
    }

    /**
     * @return array
     */
    private function taskPrintOrdersDelivery()
    {
        $orders = [];
        try {
            $lc_sql = "EXEC dbo.App_cargar_pedidos_app $this->idCadena, $this->idRestaurante, '$this->idPeriodo', '$this->estado', ''";
            $this->fn_ejecutarquery($lc_sql);
            while ($row = $this->fn_leerarreglo()) {
                $transactionId = $row['codigo_factura'] ? $row['codigo_factura'] : $row['IDCabeceraOrdenPedido'];
                $orders[] = [
                    'codigo_app' => $row['codigo_app'],
                    'transaction' => $transactionId,
                    'codigo_factura' => $row['codigo_factura'],
                    'codigo_orden' => $row['IDCabeceraOrdenPedido'],
                ];
            }
            foreach ($orders as $order) {
                if ($order['transaction'] != null) {
                    $this->requestClientWsServicePrint($order);
                }
            }
            return $orders;
        } catch (Exception $exception) {
            return $orders;
        }
    }

    /**
     * @param array $order
     * @return void
     */
    private function requestClientWsServicePrint(array $order)
    {
        if ($this->validatePrintOrder($order)) {
            $httpHost = $_SERVER['HTTP_HOST'];
            $endPoint = "$httpHost/pos/impresion/cliente_ws_servicioImpresion.php";
            $headers = [
                'Content-Type: application/json',
            ];
            try {
                $body = [
                    'metodo' => 'apiServicioImpresion',
                    'tipo' => 'delivery',
                    'transaccion' => $order['transaction'],
                    'idCabeceraOrdenPedido' => null,
                    'datosAdicionales' => null,
                    'session' => $this->session,
                    'codigo_app' => $order['codigo_app'],
                    'codigo_factura' => $order['codigo_factura'],
                    'codigo_orden' => $order['codigo_orden']
                ];
                $cURLConnection = curl_init($this->endPoint);
                curl_setopt($cURLConnection, CURLOPT_POST, 1);
                curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, json_encode($body));
                curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
                $apiResponse = curl_exec($cURLConnection);
                curl_close($cURLConnection);
                return;
            } catch (Exception $exception) {
                return;
            }
        }
    }

    /**
     * @param array $order
     * @return bool
     */
    private function validatePrintOrder(array $order)
    {
        $idRestaurante = $_SESSION['rstId'];
        $codeApp = $order['codigo_app'];
        $codeInvoince = $order['codigo_factura'];
        $lc_sql = "EXEC [dbo].[VerificarCanalMovimientoExistente] $idRestaurante, '$codeApp', '$codeInvoince'";
        $this->fn_ejecutarquery($lc_sql);
        $row = $this->fn_leerarreglo();
        return !!$row['exist'];
    }
}