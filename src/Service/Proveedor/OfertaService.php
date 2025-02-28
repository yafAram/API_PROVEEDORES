<?php
namespace App\Service\Proveedor;

use App\Repository\Proveedor\OfertasRepository;

class OfertaService {
    private $ofertasRepository;
    private $encryptionKey;

    public function __construct(OfertasRepository $ofertasRepository, string $encryptionKey) {
        $this->ofertasRepository = $ofertasRepository;
        $this->encryptionKey = $encryptionKey;
    }

    // Obtiene las ofertas, las convierte a JSON y las cifra.
    public function getEncryptedOffers(): string {
        // Obtener array de objetos Ofertas
        $ofertas = $this->ofertasRepository->getAll();

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

        // Cifrado con AES-256-CBC
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($ivLength);
        $encryptedData = openssl_encrypt($jsonData, 'aes-256-cbc', $this->encryptionKey, 0, $iv);
        // Concatenar el dato cifrado con el IV para poder descifrar después
        $encryptedPayload = base64_encode($encryptedData) . "::" . base64_encode($iv);
        return $encryptedPayload;
    }
}
