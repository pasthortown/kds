<?php

class ModelStatusVersion{

    private $idEstacion;
    private $idStatusVersion;
    private $idEstacionStatusVersion;
    private $fechaStatusVersion;
    private $aplicaLimpiadoCache;
    private $ipEstacion;
    private $idCadena;

    public function __construct(){}

    public function getIdEstacion() {
        return $this->idEstacion;
    }

    public function setIdEstacion($idEstacion) {
        $this->idEstacion = $idEstacion;
    }

    public function getIdStatusVersion() {
        return $this->idStatusVersion;
    }

    public function setIdStatusVersion($idStatusVersion) {
        $this->idStatusVersion = $idStatusVersion;
    }

    public function getIdEstacionStatusVersion() {
        return $this->idEstacionStatusVersion;
    }

    public function setIdEstacionStatusVersion($idEstacionStatusVersion) {
        $this->idEstacionStatusVersion = $idEstacionStatusVersion;
    }

    public function getFechaStatusVersion() {
        return $this->fechaStatusVersion;
    }

    public function setFechaStatusVersion($fechaStatusVersion) {
        $this->fechaStatusVersion = $fechaStatusVersion;
    }

    public function getAplicaLimpiadoCache() {
        return $this->aplicaLimpiadoCache;
    }

    public function setAplicaLimpiadoCache($aplicaLimpiadoCache) {
        $this->aplicaLimpiadoCache = $aplicaLimpiadoCache;
    }

    public function getIdCadena() {
        return $this->idCadena;
    }

    public function setIdCadena($idCadena) {
        $this->idCadena = $idCadena;
    }

    public function getIpEstacion() {
        return $this->ipEstacion;
    }

    public function setIpEstacion($ipEstacion) {
        $this->ipEstacion = $ipEstacion;
    }

}