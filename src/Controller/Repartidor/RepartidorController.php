<?php
namespace App\Controller\Repartidor;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Service\Repartidor\RepartidorService;

class RepartidorController {
    private $repartidorService;

    public function __construct(RepartidorService $repartidorService) {
        $this->repartidorService = $repartidorService;
    }

    /**
     * Inserta un repartidor en la base de datos.
     *
     * @param Request  $request  Objeto de petición HTTP
     * @param Response $response Objeto de respuesta HTTP
     * @param array    $args     Argumentos de la ruta (si los hubiera)
     * @return Response Respuesta en formato JSON
     */
    public function insertVehiculo(Request $request, Response $response, array $args): Response {
        try {
            // Intenta obtener el cuerpo parseado
            $data = $request->getParsedBody();
            
            // Si es null, decodifica manualmente el JSON
            if (is_null($data)) {
                $body = (string)$request->getBody();
                $data = json_decode($body, true);
            }
            
            if (!is_array($data)) {
                throw new \Exception("No se recibieron datos JSON válidos.");
            }
            
            // Llamar al service con el array de datos
            $result = $this->repartidorService->insertVehiculo($data);
            
            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $error = [
                'success' => false,
                'message' => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }
    }