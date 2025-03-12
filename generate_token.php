<?php
require __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;

$config = require __DIR__ . '/config/Conexion.php';

$secret = $config['jwt']['secret'];

echo "secret: " . $secret;

// Define el payload (puedes ajustar los valores según necesites)
// 'sub' representa el ID del usuario, 'iat' es el momento de emisión y 'exp' la expiración (en este caso, 1 hora)
$payload = [
    'sub' => 1,
    'iat' => time(),
    'exp' => time() + 3600
];

$jwt = JWT::encode($payload, $secret, 'HS256');
echo "Token JWT de prueba:\n" . $jwt . "\n";

