<?php
namespace App\Controller\Proveedor;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Service\Proveedor\ProveedoresService;
use Firebase\JWT\JWT;

class ProveedoresController {
    private $proveedoresService;
    private $jwtSecret;
    
    public function __construct(ProveedoresService $proveedoresService, string $jwtSecret) {
        $this->ProveedoresService = $proveedoresService;
        $this->jwtSecret = $jwtSecret;
    }
    
    // Endpoint para obtener las ofertas cifradas dentro de un JWT
    public function getOffers(Request $request, Response $response): Response {
        try {
            $encryptedData = $this->proveedoresService->getEncryptedOffers();
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
            $error = ['error' => 'Error al obtener proveedores: ' . $e->getMessage()];
            $response->getBody()->write(json_encode($error));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}
