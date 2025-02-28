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
        $stmt = $this->pdo->prepare("SELECT * FROM proovedor_ofertas");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $ofertasArray = [];
        if ($data) {
            foreach ($data as $row) {
                $oferta = new Ofertas(
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
                $ofertasArray[] = $oferta;
            }
        }
        return $ofertasArray;
    }
}
