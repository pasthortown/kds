<?php

namespace Maxpoint\Mantenimiento\adminReplicacion\Clases;

use Doctrine\DBAL\Connection;
use Maxpoint\Mantenimiento\adminReplicacion\Servicios\InsercionMasiva;

class ReplicacionLogDistribuidorController extends Controller
{
    public function llenarLogTramas($estadoTramas)
    {


        $insercionMasiva = new InsercionMasiva($this->conexion, "logtramas");
        $insercionMasiva->setColumns(array("IDLOTE", "IDTRAMA", "IDRESTAURANTE", "ESTADO", "ERRORNUMBER", "ERRORMESSAGE", "LASTUSER", "LASTUPDATE"));
        $secciones = array_chunk($estadoTramas, 100);
        foreach ($secciones as $seccion) {
            $insercionMasiva->setValues($seccion);
            $res = $insercionMasiva->execute();
        }
        return "ok";
    }


    public function EliminarDuplicados()
    {
        $query = "EXEC [dbo].[IAE_LOGTRAMAS] 'EliminarDuplicados' ,0,0";
        $res = $this->cargarDatos($query);
        return $res;
    }
}