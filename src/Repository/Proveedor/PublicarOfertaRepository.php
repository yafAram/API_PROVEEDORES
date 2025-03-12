<?php
namespace App\Repository\Repartidor;

use App\Model\Repartidor\Repartidor;
use PDO;

class RepartidorRepository
{
    private $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function setVehiculo($repartidor) {
        $stmt = $this->pdo->prepare("
            INSERT INTO Repartidor_Turno 
            (IdRepartidor, Transporte, Placas, KilosTortillaAsignados, HoraInicio, HoraFin) 
            VALUES 
            (:IdRepartidor, :Transporte, :Placas, :KilosTortillaAsignados, :HoraInicio, :HoraFin)
        ");
        
        $stmt->execute([
            'IdRepartidor' => $repartidor->getIdRepartidor(),
            'Transporte' => $repartidor->getTransporte(),
            'Placas' => $repartidor->getPlacas(),
            'KilosTortillaAsignados' => $repartidor->getKilosTortillaAsignados(),
            'HoraInicio' => $repartidor->getHoraInicio(),
            'HoraFin' => $repartidor->getHoraFin()
        ]);
        
        // Devuelve el ID del registro insertado
        return $this->pdo->lastInsertId();
    }
        
    }

?>