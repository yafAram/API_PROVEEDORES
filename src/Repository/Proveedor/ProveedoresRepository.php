<?php
namespace App\Repository\Proveedor;

use App\Model\Proveedor\Proveedores;
use PDO;

class ProveedoresRepository {
    private $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    // Retorna un array de objetos Ofertas
    public function getAll(): array {
        $stmt = $this->pdo->prepare("SELECT * FROM Proveedor_Login");
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $proveedoresArray = [];
        if ($data) {
            foreach ($data as $row) {
                $proveedor = new Proveedores(
                    $row['IdProveedor'],
                    $row['Usuario'],
                    $row['RazonSocial'],
                    $row['Contacto'],
                    $row['Telefono'],
                    $row['Email'],
                    $row['Etatus']
                );
                $proveedoresArray[] = $proveedor;
            }
        }
        return $proveedoresArray;
    }
}
