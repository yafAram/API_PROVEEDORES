<?php
namespace App\Controller\OAuth;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Service\OAuth\AuthService;

class AuthController {
    private $authService;
    
    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function login(Request $request, Response $response): Response {
        error_log('[AuthController] Iniciando proceso de login');
        
        $data = $request->getParsedBody();
        error_log('[AuthController] Datos recibidos: ' . print_r($data, true));
        
        $clientType = $request->getHeaderLine('X-Client-Type');
        error_log('[AuthController] Cabecera X-Client-Type: ' . $clientType);

        // Validación mejorada de campos
        if (!isset($data['usuario']) || !isset($data['password']) || empty($clientType)) {
            error_log('[AuthController] Error de validación: Campos requeridos faltantes');
            return $this->jsonResponse($response, 'Se requieren usuario, password y X-Client-Type', 400);
        }

        try {
            error_log('[AuthController] Intentando autenticar usuario: ' . $data['usuario']);
            
            $tokenData = $this->authService->authenticate(
                $clientType,
                trim($data['usuario']), // Limpieza de espacios
                $data['password']
            );

            // Validación adicional del token
            if (!isset($tokenData['token']) || empty($tokenData['token'])) {
                throw new \Exception('Error generando token JWT');
            }

            error_log('[AuthController] Autenticación exitosa para: ' . $data['usuario']);
            
            // Respuesta manual con validación UTF-8
            $response->getBody()->write(
                json_encode($tokenData, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)
            );
            
            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus(200);

        } catch (\PDOException $e) {
            error_log('[AuthController] Error de base de datos: ' . $e->getMessage());
            return $this->jsonResponse($response, 'Error interno del servidor', 500);
            
        } catch (\JsonException $e) {
            error_log('[AuthController] Error de codificación JSON: ' . $e->getMessage());
            return $this->jsonResponse($response, 'Error generando respuesta', 500);
            
        } catch (\Exception $e) {
            error_log('[AuthController] Error general: ' . $e->getMessage());
            return $this->jsonResponse($response, $e->getMessage(), 401);
        }
    }

    private function jsonResponse(Response $response, $message, $statusCode): Response {
        $payload = ['error' => $message];
        
        try {
            $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            $response->getBody()->write($json);
            
            return $response
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withStatus($statusCode);
                
        } catch (\JsonException $e) {
            // Fallback para errores críticos de JSON
            $response->getBody()->write('{"error":"Error crítico procesando respuesta"}');
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }
}





