<?php
return [
    'db_proveedores' => [ // Base de datos principal (Proveedores)
        'host'     => 'localhost',
        'dbname'   => 'Proveedores',
        'user'     => 'root',
        'pass'     => '12345678',
        'port'     => '3307',
        'charset'  => 'utf8mb4'
    ],
    'db_repartidores' => [ // Nueva base de datos para Repartidores
        'host'     => 'localhost',
        'dbname'   => 'Repartidores', // Nombre de la segunda base de datos
        'user'     => 'root',
        'pass'     => '12345678',
        'port'     => '3307',
        'charset'  => 'utf8mb4'
    ],
    'jwt' => [
        'secret' => 'centeotl_equipo_umizumi_66273942324312'
    ],
    'encryption' => [
        'key' => '1234567890abcdef1234567890abcdef'
    ]
];