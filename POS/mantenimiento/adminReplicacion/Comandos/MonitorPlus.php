<?php

namespace Maxpoint\Mantenimiento\adminReplicacion\Comandos;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Maxpoint\Mantenimiento\adminReplicacion\Clases\ConexionDinamica;
use Maxpoint\Mantenimiento\adminReplicacion\Clases\ReplicacionDistribuidorController;
use Maxpoint\Mantenimiento\adminReplicacion\Clases\ReplicacionTiendaController;
use Maxpoint\Mantenimiento\adminReplicacion\Clases\ReplicacionAzureController;

/**
 *
 */
class MonitorPlus extends Command
{

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('mxp:monitoreo-maxpoint')
            // the short description shown while running "php bin/console list"
            ->setDescription('Envía correos electrónicos de los PLUS en las tiendas.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Recolecta el número de PLUS');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $conexion = new ConexionDinamica();
        $conAzure = $conexion->conexionAzure();
        $azureController = new ReplicacionAzureController($conAzure);
        $conDistrib = $conexion->conexionDistribuidor();
        $distribuidorController = new ReplicacionDistribuidorController($conDistrib);

        $resultadoCadenasAzure = $azureController->consultarCadenas();
        $contenido = $this->construyeCabecera('Información de Productos');
        $contenido2 = $this->construyeCabecera('Información de Lotes');

        foreach ($resultadoCadenasAzure["datos"] as $informacionCadena) {
            //var_dump($informacionCadena);
            $resultadoAzure = $azureController->consultarProductosAzure($informacionCadena["cadena"]);
            $resultadoAzureBotones = $azureController->consultarBotonesAzure($informacionCadena["cadena"]);
            $resultadoAzureBotonesMenu = $azureController->consultarBotonesMenuAzure($informacionCadena["cadena"]);
            $resultadoAzurePluSinPrecio = $azureController->consultarProductosSinPrecioAzure($informacionCadena["cadena"]);
            $resultadoAzurePreguntas = $azureController->consultarPreguntaAzure($informacionCadena["cadena"]);
            $resultadoAzureRespuestas = $azureController->consultarRespuestaAzure($informacionCadena["cadena"]);
            $resultadoAzurePluPreguntas = $azureController->consultarPluPreguntaAzure($informacionCadena["cadena"]);
            $conexionesLocales = $distribuidorController->cargarBasesLocales($informacionCadena["cadena"])["datos"];
            $resultadoDistribuidor = $distribuidorController->consultarProductosCadena($informacionCadena["cadena"]);
            $resultadoDistribuidorBotones = $distribuidorController->consultarBotones($informacionCadena["cadena"]);
            $resultadoDistribuidorBotonesMenu = $distribuidorController->consultarBotonesMenu($informacionCadena["cadena"]);
            $resultadoDistribuidorPluSinPrecio = $distribuidorController->consultarProductosSinPrecio($informacionCadena["cadena"]);
            $resultadoDistribuidorTiendasInactivas = $distribuidorController->consultarBasesInactivas($informacionCadena["cadena"]);
            $resultadoDistribuidorPreguntas = $distribuidorController->consultarPregunta($informacionCadena["cadena"]);
            $resultadoDistribuidorRespuestas = $distribuidorController->consultarRespuesta($informacionCadena["cadena"]);
            $resultadoDistribuidorPluPreguntas = $distribuidorController->consultarPluPregunta($informacionCadena["cadena"]);


            $contenido3 = '';
            $contenido3 = $contenido3 . '<br><br><table><tr><td><b>Cadena</b></td><td colspan="2">' . $informacionCadena["nombreCadena"] . '</td></tr>'
                . '<tr><td><b>Local</b></td><td><b>Servidor</b></td><td><b>Base</b></td></tr>';
            foreach ($resultadoDistribuidorTiendasInactivas["datos"] as $tiendasInactivas) {
                //var_dump($lotesTiendas);
                $contenido3 = $contenido3 . '<tr>'
                    . '<td><b>' . $tiendasInactivas["rst_cod_tienda"] . '</b></td>'
                    . '<td>' . $tiendasInactivas["IP"] . '</td>'
                    . '<td>' . $tiendasInactivas["Databasename"] . '</td></tr>';
            }
            $contenido3 = $contenido3 . '</table>';
            $contenido = $contenido . '<br><br><table><tr><td><b>Cadena</b></td><td colspan="7">' . $informacionCadena["nombreCadena"] . '</td></tr>'
                . '<tr><td><b>Local</b></td><td><b>Productos</b></td><td><b>Botones</b></td><td><b>Botones en Menú</b></td><td><b>Productos sin precio</b></td><td><b>Preguntas</b></td><td><b>Respuestas</b></td><td><b>Plu Pregunta</b></td></tr>';
            $contenido = $contenido . '<tr><td><b>AZURE</b></td><td>' . $resultadoAzure["datos"][0]["Total"] . '</td><td>' . $resultadoAzureBotones["datos"][0]["Total"] . '</td><td>' . $resultadoAzureBotonesMenu["datos"][0]["Total"] . '</td><td>' . $resultadoAzurePluSinPrecio["datos"][0]["Total"] . '</td><td>' . $resultadoAzurePreguntas["datos"][0]["Total"] . '</td><td>' . $resultadoAzureRespuestas["datos"][0]["Total"] . '</td><td>' . $resultadoAzurePluPreguntas["datos"][0]["Total"] . '</td></tr>';
            $contenido = $contenido . '<tr><td><b>Distribuidor</b></td><td>' . $resultadoDistribuidor["datos"][0]["Total"] . '</td><td>' . $resultadoDistribuidorBotones["datos"][0]["Total"] . '</td><td>' . $resultadoDistribuidorBotonesMenu["datos"][0]["Total"] . '</td><td>' . $resultadoDistribuidorPluSinPrecio["datos"][0]["Total"] . '</td><td>' . $resultadoDistribuidorPreguntas["datos"][0]["Total"] . '</td><td>' . $resultadoDistribuidorRespuestas["datos"][0]["Total"] . '</td><td>' . $resultadoDistribuidorPluPreguntas["datos"][0]["Total"] . '</td></tr>';
            //var_dump($conexionesLocales);
            $contenido2 = $contenido2 . $contenido3;
            $contenido2 = $contenido2 . '<br><br><table><tr><td><b>Cadena</b></td><td colspan="3">' . $informacionCadena["nombreCadena"] . '</td></tr>'
                . '<tr><td><b>Local</b></td><td><b>Lote</b></td><td><b>Estado</b></td><td><b>Fecha</b></td></tr>';


            foreach ($conexionesLocales as $parametrosConexion) {
                $conexionLocal = $conexion->crearConexionParametros($parametrosConexion);
                $local = new ReplicacionTiendaController($conexionLocal);
                $resultado = $local->consultarProductosTienda();
                $resultadoBotones = $local->consultarBotonesTienda();
                $resultadoBotonesMenu = $local->consultarBotonesMenuTienda();
                $resultadoPlusSinPrecio = $local->consultarProductosSinPrecioTienda();
                $resultadoPreguntas = $local->consultarPreguntaTienda();
                $resultadoRespuestas = $local->consultarRespuestaTienda();
                $resultadoPluPreguntas = $local->consultarPluPreguntaTienda();
                //$output->writeln(print_r($resultadoLotes, true));
                //$output->writeln(print_r($parametrosConexion, true));
                $contenido = $contenido . '<tr>'
                    . '<td><b>' . $parametrosConexion["rst_cod_tienda"] . '</b></td>'
                    . '<td>' . $resultado["datos"][0]["Total"] . '</td>'
                    . '<td>' . $resultadoBotones["datos"][0]["Total"] . '</td>'
                    . '<td>' . $resultadoBotonesMenu["datos"][0]["Total"] . '</td>'
                    . '<td>' . $resultadoPlusSinPrecio["datos"][0]["Total"] . '</td>'
                    . '<td>' . $resultadoPreguntas["datos"][0]["Total"] . '</td>'
                    . '<td>' . $resultadoRespuestas["datos"][0]["Total"] . '</td>'
                    . '<td>' . $resultadoPluPreguntas["datos"][0]["Total"] . '</td></tr>';

                $resultadoLotes = $local->consultarLotesTienda();
                foreach ($resultadoLotes["datos"] as $lotesTiendas) {
                    //var_dump($lotesTiendas);
                    $contenido2 = $contenido2 . '<tr>'
                        . '<td><b>' . $parametrosConexion["rst_cod_tienda"] . '</b></td>'
                        . '<td>' . $lotesTiendas["numeroLote"] . '</td>'
                        . '<td>' . $lotesTiendas["Estado"] . '</td>'
                        . '<td>' . $lotesTiendas["Fecha"] . '</td></tr>';
                }
            }
            $contenido = $contenido . '</table>';
            $contenido2 = $contenido2 . '</table>';
        }
        $contenido = $contenido . $this->construyePie();
        $contenido2 = $contenido2 . $this->construyePie();
        //echo $contenido2;
        //die();
        $this->enviarEmail('Plus en Locales', $contenido);
        $this->enviarEmail('Lotes en Locales', $contenido2);
    }

    protected function construyeCabecera($titulo)
    {
        $cabecera = '<html><head>'
            . '<style>td {border: solid black;border-width: 1px;padding-left:5px;padding-right:5px;padding-top:1px;padding-bottom:1px;font: 14px arial} </style>'
            . '</head>'
            . '<body><H1  style="font-family:Trebuchet MS; font-size:16px;color:#000000;">' . $titulo . '</H1>'
            . '<H1  style="font-family:Trebuchet MS; font-size:16px;color:#000000;">Información enviada a la Fecha:' . date("Y-m-d H:i:s") . ' </H1>';

        return $cabecera;
    }

    protected function construyePie()
    {
        $pie = '<H1 style="color: #000000; font-family:Trebuchet MS; font-weight: 200; font-size: 16px;">IMPORTANTE:Este correo es informativo, favor no responder a esta dirección de correo, ya que no se encuentra habilitada para recibir mensajes. </H1><br/></body></html>';
        return $pie;
    }

    protected function enviarEmail($asunto, $contenido)
    {
        // Create the Transport
        $transport = (new \Swift_SmtpTransport('smtp.office365.com', 587, 'tls'))
            ->setUsername('maxpoint.soporte@kfc.com.ec')
            ->setPassword('*!ofibel*!1973');

        // Create the Mailer using your created Transport
        $mailer = new \Swift_Mailer($transport);
        // Create a message
        //->setTo(['darwin.mora@kfc.com.ec', 'hugo.mera@kfc.com.ec', 'jenny.aguilar@kfc.com.ec' => 'Maxpoint Soporte'])
        $message = (new \Swift_Message($asunto))
            ->setFrom(['maxpoint.soporte@kfc.com.ec' => 'MAXPOINT'])
            ->setTo(['hugo.mera@kfc.com.ec', 'jenny.aguilar@kfc.com.ec', 'soportesoftware@grupokfc.onmicrosoft.com' => 'Maxpoint Soporte'])
            ->setBody($contenido, 'text/html');

        // Send the message
        $result = $mailer->send($message);
    }
}
