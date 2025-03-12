<?php
return [
    'db' => [
        'host'     => 'localhost',
        'dbname'   => 'u787421145_Proveedores',
        'user'     => 'u787421145_teamworkumizoo',
        'pass'     => 'EquipoUmizumi666#',
        'port'     => '3306',
        'charset'  => 'utf8mb4'
    ],
    'jwt' => [
        'secret' => 'django-insecure-s6h+vgf8%h642jd28_a(rw%&7k*hk0r@knosuyo65ih_#dabqz'
    ],
    'encryption' => [
        // Para AES-256 se requiere una clave de 32 bytes.
        // Tu valor actual "2314278493627193" tiene 16 caracteres, por lo que no es suficiente.
        // Puedes solucionarlo de una de estas dos maneras:
        // a) Definir una cadena de 32 caracteres, por ejemplo:
        'key' => '1234567890abcdef1234567890abcdef',
        // o b) Convertir la clave actual a 32 bytes usando hash('sha256', ...):
        // 'key' => hash('sha256', '2314278493627193', true)
    ]
];
