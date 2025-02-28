<?php
namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response;

class AuthMiddleware {
    private $jwtSecret;
    
    public function __construct(string $jwtSecret) {
        $this->jwtSecret = $jwtSecret;
    }
    
    public function __invoke(Request $request, Handler $handler): Response {
        $authHeader = $request->getHeaderLine('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Token no proporcionado']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
        $token = $matches[1];
        try {
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            $request = $request->withAttribute('user', $decoded);
        } catch (\Exception $e) {
            $response = new Response();
            $response->getBody()->write(json_encode(['error' => 'Token inválido o expirado']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
        return $handler->handle($request);
    }
}
