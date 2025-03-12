<?php
return [
    'db_proveedores' => [ 
        'host'     => '191.101.0.236',  // IP del servidor (ajusta si es localhost)
        'dbname'   => 'Proveedores',    // Nombre en minúsculas (como en Docker)
        'user'     => 'root',
        'pass'     => '12345678',
        'port'     => '33061',          // Puerto de Proveedores en Docker Compose
        'charset'  => 'utf8mb4'
    ],
    'db_repartidores' => [ 
        'host'     => '191.101.0.236',  // Misma IP que Proveedores (si están en el mismo servidor)
        'dbname'   => 'Repartidores',   // Nombre en minúsculas (como en Docker)
        'user'     => 'root',
        'pass'     => '12345678',
        'port'     => '33062',          // Puerto de Repartidores en Docker Compose
        'charset'  => 'utf8mb4'
    ],
    'jwt' => [
        'secret' => 'centeotl_equipo_umizumi_66273942324312'
    ],
    'encryption' => [
        'key' => '1234567890abcdef1234567890abcdef'
    ]
];