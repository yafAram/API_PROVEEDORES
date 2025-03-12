<?php
namespace App\Model\Repartidor;

class Repartidor{

    private $IdRepartidor;
    private $Transporte;
    private $Placas;
    private $KilosTortillaAsignados;
    private $HoraInicio;
    private $HoraFin;



    public function __construct($IdRepartidor, $Transporte, $Placas,
    $KilosTortillaAsignados, $HoraInicio, $HoraFin){

        $this->IdRepartidor = $IdRepartidor;
        $this->Transporte = $Transporte;
        $this->Placas = $Placas;
        $this->KilosTortillaAsignados = $KilosTortillaAsignados;
        $this->HoraInicio = $HoraInicio;
        $this->HoraFin = $HoraFin;
    }


    //GET

    public function getIdRepartidor(){
        return $this->IdRepartidor;
    }

    public function getTransporte(){
        return $this->Transporte;
    }


    public function getPlacas(){
        return $this->Placas;
    }


    public function getKilosTortillaAsignados(){
        return $this->KilosTortillaAsignados;
    }


    public function getHoraInicio(){
        return $this->HoraInicio;
    }


    public function getHoraFin(){
        return $this->HoraFin;
    }

}