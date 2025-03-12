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
use App\Repository\Repartidor\RepartidorRepository;
use App\Service\Repartidor\RepartidorService;
use App\Controller\Repartidor\RepartidorController;
use App\Controller\OAuth\AuthController;
use App\Service\OAuth\AuthService;
use App\Middleware\AuthMiddleware;
use App\Middleware\CorsMiddleware;
use Slim\Psr7\Response;

// Cargar configuración
$config = require __DIR__ . '/../config/Conexion.php';

$app = AppFactory::create();


// 1. Middleware CORS (PRIMERO)
$app->add(new CorsMiddleware());

// 2. Body Parser (para recibir JSON)
$app->addBodyParsingMiddleware();

// 3. Logger


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
    $dbProveedores = new Database($config['db_proveedores']);
    $pdoProveedores = $dbProveedores->getConnection();

    $dbRepartidores = new Database($config['db_repartidores']);
    $pdoRepartidores = $dbRepartidores->getConnection();
} catch (Exception $e) {
    $logger->error('Database Connection Error', ['message' => $e->getMessage()]);
    die(json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]));
}

// Configurar autenticación primero
$authService = new AuthService($config);
$authController = new AuthController($authService, $config['jwt']['secret']);

// Manejar OPTIONS para login primero
$app->options('/auth/login', function ($request, $response, $args) {
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, X-Client-Type')
        ->withHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
});

$app->post('/auth/login', [$authController, 'login']);

// Rutas protegidas
$app->group('', function ($group) use ($config, $pdoProveedores, $pdoRepartidores) {
    // Ofertas
    $ofertasRepository = new OfertasRepository($pdoProveedores);
    $offerService = new OfertaService($ofertasRepository, $config['encryption']['key']);
    $offerController = new OfertaController($offerService, $config['jwt']['secret']);
    $group->get('/ofertas', [$offerController, 'getOffers']);

    // Proveedores
    $proveedoresRepository = new ProveedoresRepository($pdoProveedores);
    $proveedoresService = new ProveedoresService($proveedoresRepository, $config['encryption']['key']);
    $proveedoresController = new ProveedoresController($proveedoresService, $config['jwt']['secret']);
    $group->get('/proveedores', [$proveedoresController, 'getProveedores']);

    // Repartidores
    $repartidorRepository = new RepartidorRepository($pdoRepartidores);
    $repartidorService = new RepartidorService($repartidorRepository, $config['encryption']['key']);
    $repartidorController = new RepartidorController($repartidorService, $config['jwt']['secret']);
    $group->post('/repartidor', [$repartidorController, 'insertVehiculo']);
})->add(new AuthMiddleware($config['jwt']['secret']));

// Catch-all para otras rutas OPTIONS (debe ir al final)
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});


$app->run();
