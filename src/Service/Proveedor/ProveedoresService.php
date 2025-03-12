<?php
namespace App\Service\Proveedor;

use App\Repository\Proveedor\ProveedoresRepository;

class ProveedoresService {
    private $proveedoresRepository;
    private $encryptionKey;

    public function __construct(ProveedoresRepository $proveedoresRepository, string $encryptionKey) {
        $this->proveedoresRepository = $proveedoresRepository;
        // Forzar la clave a 32 bytes
        $this->encryptionKey = hash('sha256', $encryptionKey, true);
    }
    

    // Obtiene las ofertas, las convierte a JSON y las cifra.
    public function getEncryptedProveedores(): string {
        // Obtener array de objetos Ofertas
        $proveedores = $this->proveedoresRepository->getAll();

        if (!$proveedores || empty($proveedores)) {
            error_log("No se encontraron proveedores.");
            throw new \Exception("No se encontraron proveedores.");
        }
        
        
        // Convertir cada objeto a array asociativo usando los getters
        $dataArray = [];
        foreach ($proveedores as $proveedor) {
            $dataArray[] = [
                'IdProveedor'               => $proveedor->getIdProveedor(),
                'Usuario'                   => $proveedor->getUsuario(),
                'RazonSocial'               => $proveedor->getRazonSocial(),
                'Contacto'                  => $proveedor->getContacto(),
                'Telefono'                  => $proveedor->getTelefono(),
                'Email'                     => $proveedor->getEmail(),
                'Estatus'                   => $proveedor->getEstatus()
            ];
        }

        $jsonData = json_encode($dataArray, JSON_UNESCAPED_UNICODE);
            error_log("ProveedoresService::getEncryptedProveedores() retornó " . strlen($jsonData) . " bytes de datos JSON.");
            error_log("Datos JSON: " . $jsonData);
            error_log("Clave de cifrado: " . bin2hex($this->encryptionKey));
            error_log("key".$this->encryptionKey);
        // Cifrado con AES-256-CBC
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($ivLength);
        $encryptedData = openssl_encrypt(
            $jsonData, 
            'aes-256-cbc', 
            $this->encryptionKey, 
            OPENSSL_RAW_DATA, // <--- Cambiar de 0 a OPENSSL_RAW_DATA
            $iv
        );
    
        // Concatenar el dato cifrado (en Base64) con el IV (en Base64)
        $encryptedPayload = base64_encode($encryptedData) . "::" . base64_encode($iv);
        return $encryptedPayload;
    }
}
