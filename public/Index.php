<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use App\Database;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Repository\Proveedor\OfertasRepository;
use App\Service\Proveedor\OfertaService;
use App\Controller\Proveedor\OfertaController;
use App\Repository\Proveedor\ProveedoresRepository;
use App\Service\Proveedor\ProveedoresService;
use App\Controller\Proveedor\ProveedoresController;
use App\Middleware\AuthMiddleware;
use Slim\Psr7\Response;

// Cargar configuración
$config = require __DIR__ . '/../config/Conexion.php';

$app = AppFactory::create();

// Manejar solicitudes OPTIONS
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

// Middleware para agregar cabeceras CORS
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// Configuración de Monolog
$logger = new Logger('api');
$logFile = __DIR__ . '/../logs/app.log';
$logger->pushHandler(new StreamHandler($logFile, Logger::DEBUG));

// Middleware para loggear cada solicitud y respuesta
$app->add(function ($request, $handler) use ($logger) {
    $logger->info('Incoming Request', [
        'method'  => $request->getMethod(),
        'uri'     => (string)$request->getUri(),
        'headers' => $request->getHeaders()
    ]);
    $response = $handler->handle($request);
    $logger->info('Response', [
        'status' => $response->getStatusCode()
    ]);
    return $response;
});

// Middleware global de errores con Monolog
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler(function ($request, $exception, $displayErrorDetails) use ($logger) {
    $logger->error('Unhandled Exception', [
        'message' => $exception->getMessage(),
        'file'    => $exception->getFile(),
        'line'    => $exception->getLine(),
        'trace'   => $exception->getTraceAsString()
    ]);
    $response = new Response();
    $errorData = [
        'error'   => 'Ocurrió un error interno en la API',
        'message' => $exception->getMessage()
    ];
    $response->getBody()->write(json_encode($errorData));
    return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
});

// Conexión a la base de datos
try {
    $db = new Database($config);
    $pdo = $db->getConnection();
} catch (Exception $e) {
    $logger->error('Database Connection Error', ['message' => $e->getMessage()]);
    die(json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]));
}

// Instanciar dependencias para Ofertas
$ofertasRepository = new OfertasRepository($pdo);
$offerService = new OfertaService($ofertasRepository, $config['encryption']['key']);
$offerController = new OfertaController($offerService, $config['jwt']['secret']);

// Ruta protegida: obtiene las ofertas cifradas en un JWT
$app->get('/ofertas', [$offerController, 'getOffers'])
    ->add(new AuthMiddleware($config['jwt']['secret']));

// Instanciar dependencias para Proveedores
$proveedoresRepository = new ProveedoresRepository($pdo);
$proveedoresService = new ProveedoresService($proveedoresRepository, $config['encryption']['key']);
$proveedoresController = new ProveedoresController($proveedoresService, $config['jwt']['secret']);

// Ruta protegida: obtiene los proveedores cifrados en un JWT
$app->get('/proveedores', [$proveedoresController, 'getProviders'])
    ->add(new AuthMiddleware($config['jwt']['secret']));

// Ejecutar la aplicación
$app->run();
