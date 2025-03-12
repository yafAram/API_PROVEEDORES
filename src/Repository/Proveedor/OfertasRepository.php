<?php
namespace App\Repository\Proveedor;

use App\Model\Proveedor\Ofertas;
use PDO;

class OfertasRepository {
    private $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    // Retorna un array de objetos Ofertas
    public function getAll(): array {
        $stmt = $this->pdo->prepare("SELECT * FROM Proveedor_Ofertas");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $ofertasArray = [];
        if($data){
            foreach ($data as $row) {
                $ofertasArray[] = new Ofertas(
                    $row['IdOferta'],
                    $row['IdProveedor'],
                    $row['NombreProducto'],
                    $row['Descripcion'],
                    $row['Precio'],
                    $row['CantidadDisponible'],
                    $row['DisponibilidadInmediata'],
                    $row['Categoria'],
                    $row['ComentariosAdicionales'],
                    $row['FechaPublicacion']
                );
            }
        }
        error_log("OfertasRepository::getAll() retornó " . count($ofertasArray) . " ofertas.");
        return $ofertasArray;

    }
}
