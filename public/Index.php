<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use App\Database;
use App\Repository\Proveedor\OfertasRepository;
use App\Service\Proveedor\OfertaService;
use App\Controller\Proveedor\OfertaController;
use App\Middleware\AuthMiddleware;

// Cargar configuración
$config = require __DIR__ . '/../config/Conexion.php'; // O config.php según tu nombre

$app = AppFactory::create();

// Conexión a la base de datos
try {
    $db = new Database($config);
    $pdo = $db->getConnection();
} catch (Exception $e) {
    die(json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]));
}

// Instanciar repositorio, servicio y controlador para ofertas
$ofertasRepository = new OfertasRepository($pdo);
$offerService = new OfertaService($ofertasRepository, $config['encryption']['key']);
$offerController = new OfertaController($offerService, $config['jwt']['secret']);

// Definir rutas
// Ruta protegida: obtiene las ofertas cifradas dentro de un JWT
$app->get('/offers', [$offerController, 'getOffers'])
    ->add(new AuthMiddleware($config['jwt']['secret']));

// Middleware global de errores
$app->addErrorMiddleware(true, true, true);

$app->run();
