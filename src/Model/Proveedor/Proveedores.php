<?php
namespace App\Model\Proveedor;

class Proovedores{
    private $IdProveedor;
    private $Usuario;
    private $RazonSocial;
    private $Contacto;
    Private $Telefono;
    private $Email;
    private $Estatus;
 

    public function __construct($IdProveedor, $Usuario, $RazonSocial, $Contacto,
    $Telefono, $Email, $Estatus){

        $this->IdProveedor = $IdProveedor;
        $this->Usuario = $Usuario;
        $this->RazonSocial = $RazonSocial;
        $this->Contacto = $Contacto;
        $this->Telefono = $Telefono;
        $this->Email = $Email;
        $this->Estatus = $Estatus;

    }


    public function getIdProveedor(){
        return $this->IdProveedor;
    }

    public function getUsuario(){
        return $this->Usuario;
    }

    public function getRazonSocial(){
        return $this->RazonSocial;
    }

    public function getContacto(){
        return $this->Contacto;
    }

    public function getTelefono(){
        return $this->Telefono;
    }

    public function getEmail(){
        return $this->Email;
    }

    public function getEstatus(){
        return $this->Estatus;
    }



}