<?php
namespace App\Service\Proveedor;

use App\Repository\Proveedor\ProveedoresRepository;

class ProveedoresService {
    private $proveedoresRepository;
    private $encryptionKey;

    public function __construct(ProveedoresRepository $proveedoresRepository, string $encryptionKey) {
        $this->proveedoresRepository = $proveedoresRepository;
        $this->encryptionKey = $encryptionKey;
    }

    // Obtiene las ofertas, las convierte a JSON y las cifra.
    public function getEncryptedProveedores(): string {
        // Obtener array de objetos Ofertas
        $proveedores = $this->proveedoresRepository->getAll();

        // Convertir cada objeto a array asociativo usando los getters
        $dataArray = [];
        foreach ($proovedores as $proveedor) {
            $dataArray[] = [
                'IdProveedor'               => $proovedor->getIdProveedor(),
                'Usuario'                   => $proovedor->getUsuario(),
                'RazonSocial'               => $proovedor->getRazonSocial(),
                'Contacto'                  => $proovedor->getContacto(),
                'Telefono'                  => $proovedor->getTelefono(),
                'Email'                     => $proovedor->getEmail(),
                'Estatus'                   => $proovedor->getEstatus()
            ];
        }

        $jsonData = json_encode($dataArray, JSON_UNESCAPED_UNICODE);

        // Cifrado con AES-256-CBC
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($ivLength);
        $encryptedData = openssl_encrypt($jsonData, 'aes-256-cbc', $this->encryptionKey, 0, $iv);
        // Concatenar el dato cifrado con el IV para poder descifrar después
        $encryptedPayload = base64_encode($encryptedData) . "::" . base64_encode($iv);
        return $encryptedPayload;
    }
}
