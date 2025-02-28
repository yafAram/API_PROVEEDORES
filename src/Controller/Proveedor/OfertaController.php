<?php
namespace App\Controller\Proveedor;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Service\Proveedor\OfertaService;
use Firebase\JWT\JWT;

class OfertaController {
    private $offerService;
    private $jwtSecret;
    
    public function __construct(OfertaService $offerService, string $jwtSecret) {
        $this->offerService = $offerService;
        $this->jwtSecret = $jwtSecret;
    }
    
    // Endpoint para obtener las ofertas cifradas dentro de un JWT
    public function getOffers(Request $request, Response $response): Response {
        try {
            $encryptedData = $this->offerService->getEncryptedOffers();
            $payload = [
                'data' => $encryptedData,
                'iat'  => time(),
                'exp'  => time() + 3600 // Token válido por 1 hora
            ];
            $jwt = JWT::encode($payload, $this->jwtSecret, 'HS256');
            $result = ['token' => $jwt];
            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $error = ['error' => 'Error al obtener ofertas: ' . $e->getMessage()];
            $response->getBody()->write(json_encode($error));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
