<?php
namespace App\Model\Proveedor;

class Ofertas{
    private $IdOferta;
    private $IdProveedor;
    private $NombreProducto;
    private $Descripcion;
    private $Precio;
    Private $CantidadDisponible;
    private $DisponibilidadInmediata;
    private $Categoria;
    private $ComentariosAdicionales;
    private $FechaPublicacion;

    public function __construct($IdOferta, $IdProveedor, $NombreProducto, $Descripcion,
    $Precio, $CantidadDisponible, $DisponibilidadInmediata, $Categoria, $ComentariosAdicionales,
    $FechaPublicacion){

        $this->IdOferta = $IdOferta;
        $this->IdProveedor = $IdProveedor; 
        $this->NombreProducto = $NombreProducto;
        $this->Descripcion = $Descripcion;
        $this->Precio = $Precio;
        $this->CantidadDisponible = $CantidadDisponible;
        $this->DisponibilidadInmediata = $DisponibilidadInmediata;
        $this->Categoria = $Categoria;
        $this->ComentariosAdicionales = $ComentariosAdicionales;
        $this->FechaPublicacion = $FechaPublicacion;

    }


    public function getIdOferta(){
        return $this->IdOferta;
    }

    public function getIdProveedor(){
        return $this->IdProveedor;
    }

    public function getNombreProducto(){
        return $this->NombreProducto;
    }

    public function getDescripcion(){
        return $this->Descripcion;
    }

    public function getPrecio(){
        return $this->Precio;
    }

    public function getCantidadDisponible(){
        return $this->CantidadDisponible;
    }

    public function getDisponibilidadInmediata(){
        return $this->DisponibilidadInmediata;
    }

    public function getCategoria(){
        return $this->Categoria;
    }

    public function getComentariosAdicionales(){
        return $this->ComentariosAdicionales;
    }

    public function getFechaPublicacion(){
        return $this->FechaPublicacion;
    }



}