<?php
namespace App\Service\Proveedor;

use App\Repository\Proveedor\OfertasRepository;

class OfertaService {
    private $ofertasRepository;
    private $encryptionKey;

    public function __construct(OfertasRepository $ofertasRepository, string $encryptionKey) {
        $this->ofertasRepository = $ofertasRepository;
        // Forzar la clave a 32 bytes
        $this->encryptionKey = hash('sha256', $encryptionKey, true);
    }
    

    // Obtiene las ofertas, las convierte a JSON y las cifra.
    public function getEncryptedOffers(): string {
        // Obtener array de objetos Ofertas
        $ofertas = $this->ofertasRepository->getAll();

        if (!$ofertas || empty($ofertas)) {
            error_log("No se encontraron ofertas.");
            throw new \Exception("No se encontraron ofertas.");
        }
        
        
        // Convertir cada objeto a array asociativo usando los getters
        $dataArray = [];
        foreach ($ofertas as $oferta) {
            $dataArray[] = [
                'IdOferta'               => $oferta->getIdOferta(),
                'IdProveedor'            => $oferta->getIdProveedor(),
                'NombreProducto'         => $oferta->getNombreProducto(),
                'Descripcion'            => $oferta->getDescripcion(),
                'Precio'                 => $oferta->getPrecio(),
                'CantidadDisponible'     => $oferta->getCantidadDisponible(),
                'DisponibilidadInmediata'=> $oferta->getDisponibilidadInmediata(),
                'Categoria'              => $oferta->getCategoria(),
                'ComentariosAdicionales' => $oferta->getComentariosAdicionales(),
                'FechaPublicacion'       => $oferta->getFechaPublicacion()
            ];
        }

        $jsonData = json_encode($dataArray, JSON_UNESCAPED_UNICODE);
            error_log("OfertasService::getEncryptedOffers() retornó " . strlen($jsonData) . " bytes de datos JSON.");
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
