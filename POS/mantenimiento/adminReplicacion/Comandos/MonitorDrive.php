<?php

namespace Maxpoint\Mantenimiento\adminReplicacion\Comandos;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Maxpoint\Mantenimiento\adminReplicacion\Clases\ConexionDinamica;
use Maxpoint\Mantenimiento\adminReplicacion\Clases\ReplicacionDistribuidorController;
use Maxpoint\Mantenimiento\adminReplicacion\Clases\ReplicacionTiendaController;
use Maxpoint\Mantenimiento\adminReplicacion\Servicios\InsercionMasiva;

/**
 */
class MonitorDrive extends Command
{

    protected function configure()
    {
        $this->
        // the name of the command (the part after "bin/console")
        setName('mxp:drive-maxpoint')
            ->
            // the short description shown while running "php bin/console list"
            setDescription('Envia correos electronicos de informacion de drive en las tiendas.')
            ->
            // the full command description shown when running the command with
            // the "--help" option
            setHelp('Verifica la venta de Drive de la tienda');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $conexion = new ConexionDinamica();
        $conDistrib = $conexion->conexionDistribuidor();
        $distribuidorController = new ReplicacionDistribuidorController($conDistrib);

        $contenido = $this->construyeCabecera('Información de Locales que no se consumió la Venta de Drive');

        // var_dump($informacionCadena);
        $conexionesLocales = $distribuidorController->cargarBasesLocalesDrive(10)["datos"];

        $contenido = $contenido . '<br><br><table><tr><td><b>Local</b></td></tr>';
        $output->writeln(date('m/d/Y h:i:s a', time()));
        foreach ($conexionesLocales as $parametrosConexion) {
            $conexionLocal = $conexion->crearConexionParametros($parametrosConexion);
            //$output->writeln(print_r($parametrosConexion, true));
            $local = new ReplicacionTiendaController($conexionLocal);
            $resultado = $local->consultarVentaDriveTienda();
            //$output->writeln(print_r($resultado, true));
            if (is_array($resultado["datos"]) && count($resultado["datos"]) > 0) {
                try {
                    $insercionMasiva = new InsercionMasiva($conDistrib, "RegaliasYUM");
                    $insercionMasiva->setColumns(array("Cod_Restaurante", "Transaccion", "VentaBruta", "VentaNeta", "FechaPeriodo"));
                    $insercionMasiva->setValues($resultado["datos"]);
                    $insercionMasiva->execute();
                } catch (Exception $e) {
                    $output->writeln($e->getMessage());
                    //echo 'Caught exception: ',  $e->getMessage(), "\n";
                }
            } else {
                $contenido = $contenido . '<tr>'
                    . '<td>' . $parametrosConexion["rst_cod_tienda"] . '</td></tr>';

            }
            //echo $contenido;
            //die();
        }
        $output->writeln(date('m/d/Y h:i:s a', time()));
        $contenido = $contenido . '</table>';

        $contenido = $contenido . $this->construyePie();
        //echo $contenido;
        //die();
        $this->enviarEmail('Locales que no sincronizaron Venta DRIVE', $contenido);
    }

    protected function construyeCabecera($titulo)
    {
        $cabecera = '<html><head>' . '<style>td {border: solid black;border-width: 1px;padding-left:5px;padding-right:5px;padding-top:1px;padding-bottom:1px;font: 14px arial} </style>' . '</head>' . '<body><H1  style="font-family:Trebuchet MS; font-size:16px;color:#000000;">' . $titulo . '</H1>' . '<H1  style="font-family:Trebuchet MS; font-size:16px;color:#000000;">Información enviada a la Fecha:' . date("Y-m-d H:i:s") . ' </H1>';

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
        $transport = (new \Swift_SmtpTransport('smtp.office365.com', 587, 'tls'))->setUsername('maxpoint.soporte@kfc.com.ec')->setPassword('*!ofibel*!1973');

        // Create the Mailer using your created Transport
        $mailer = new \Swift_Mailer($transport);
        // Create a message
        // ->setTo(['darwin.mora@kfc.com.ec', 'hugo.mera@kfc.com.ec', 'jenny.aguilar@kfc.com.ec' => 'Maxpoint Soporte'])
        $message = (new \Swift_Message($asunto))->setFrom([
            'maxpoint.soporte@kfc.com.ec' => 'MAXPOINT'
        ])
            ->setTo([
                'gustavo.pilco@kfc.com.ec',
                'amarilis.loor@kfc.com.ec',
                'viviana.socasi@kfc.com.ec',
                'diego.carrillo@kfc.com.ec',
                'freddy.soria@kfc.com.ec',
                'hugo.mera@kfc.com.ec' => 'Maxpoint Soporte'
            ])
            ->setBody($contenido, 'text/html');

        // Send the message
        $result = $mailer->send($message);
    }

}
