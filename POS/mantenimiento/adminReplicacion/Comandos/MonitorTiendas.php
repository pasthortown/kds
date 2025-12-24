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
class MonitorTiendas extends Command
{

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('mxp:tiendas-maxpoint')
            // the short description shown while running "php bin/console list"
            ->setDescription('Envía correos electrónicos de infromación en las tiendas.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Verifica el cierre de la tienda');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $conexion = new ConexionDinamica();
        $conAzure = $conexion->conexionAzure();
        $azureController = new ReplicacionAzureController($conAzure);
        $conDistrib = $conexion->conexionDistribuidor();
        $distribuidorController = new ReplicacionDistribuidorController($conDistrib);

        $resultadoCadenasAzure = $azureController->consultarCadenas();
        $contenido = $this->construyeCabecera('Información de Periodos');

        foreach ($resultadoCadenasAzure["datos"] as $informacionCadena) {
            //var_dump($informacionCadena);
            $resultadoAzure = $azureController->consultarProductosAzure($informacionCadena["cadena"]);
            $conexionesLocales = $distribuidorController->cargarBasesLocales($informacionCadena["cadena"])["datos"];
            $resultadoDistribuidor = $distribuidorController->consultarProductosCadena($informacionCadena["cadena"]);

            $contenido = $contenido . '<br><br><table><tr><td><b>Cadena</b></td><td colspan="2">' . $informacionCadena["nombreCadena"] . '</td></tr>'
                . '<tr><td><b>Local</b></td><td><b>Periodo Ayer</b></td></tr>';
            //$contenido = $contenido . '<tr><td><b>AZURE</b></td><td>' . $resultadoAzure["datos"][0]["Total"] . '</td><td>' . $resultadoAzureBotones["datos"][0]["Total"] . '</td><td>' . $resultadoAzureBotonesMenu["datos"][0]["Total"] . '</td><td>' . $resultadoAzurePluSinPrecio["datos"][0]["Total"] . '</td></tr>';
            //$contenido = $contenido . '<tr><td><b>Distribuidor</b></td><td>' . $resultadoDistribuidor["datos"][0]["Total"] . '</td><td>' . $resultadoDistribuidorBotones["datos"][0]["Total"] . '</td><td>' . $resultadoDistribuidorBotonesMenu["datos"][0]["Total"] . '</td><td>' . $resultadoDistribuidorPluSinPrecio["datos"][0]["Total"] . '</td></tr>';
            //var_dump($conexionesLocales);


            foreach ($conexionesLocales as $parametrosConexion) {
                $conexionLocal = $conexion->crearConexionParametros($parametrosConexion);
                $local = new ReplicacionTiendaController($conexionLocal);
                $resultado = $local->consultarPeriodoTienda();
                //$output->writeln(print_r($resultado, true));
                //die();
                //$output->writeln(print_r($parametrosConexion, true));
                if ($resultado["estado"] == 0) {
                    $contenido = $contenido . '<tr>'
                        . '<td><b>' . $parametrosConexion["rst_cod_tienda"] . '</b></td>'
                        . '<td>Error al consultar</td></tr>';
                } else {
                    if (empty($resultado["datos"])) {
                        $contenido = $contenido . '<tr>'
                            . '<td><b>' . $parametrosConexion["rst_cod_tienda"] . '</b></td>'
                            . '<td> NO TIENE PERIODO</td></tr>';
                    } else if ($resultado["datos"][0]["Estado"] === "ABIERTO") {
                        $contenido = $contenido . '<tr>'
                            . '<td><b>' . $parametrosConexion["rst_cod_tienda"] . '</b></td>'
                            . '<td>' . $resultado["datos"][0]["Estado"] . '</td></tr>';
                    }
                }
            }
            $contenido = $contenido . '</table>';
        }
        $contenido = $contenido . $this->construyePie();
        //echo $contenido;
        //die();
        $this->enviarEmail('Periodos en Locales', $contenido);
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
            ->setTo(['hugo.mera@kfc.com.ec', 'luis.vasquez@kfc.com.ec', 'darwin.mora@kfc.com.ec', 'edgar.lopez@kfc.com.ec', 'cristhian.taco@kfc.com.ec', 'soporte@kfc.com.ec' => 'Maxpoint Soporte'])
            ->setBody($contenido, 'text/html');

        // Send the message
        $result = $mailer->send($message);
    }

}
