<?php
namespace App\Service\OAuth;

use App\Database;
use Firebase\JWT\JWT;
use Exception;
use PDOException;

class AuthService {
    private $config;
    
    public function __construct(array $config) {
        // Asegurar estructura correcta
        $this->config = [
            'db_proveedores' => $config['db_proveedores'],
            'db_repartidores' => $config['db_repartidores'],
            'jwt' => $config['jwt']
        ];
    }

    public function authenticate(string $tipo, string $usuario, string $password): array {
        // Determinar configuración de BD y tabla
        switch ($tipo) {
            case 'compras':
                $dbConfigKey = 'db_proveedores';
                $table = 'Compras_Login';
                break;
            case 'proveedor':
                $dbConfigKey = 'db_proveedores';
                $table = 'Proveedor_Login';
                break;
            case 'repartidor':
                $dbConfigKey = 'db_repartidores';
                $table = 'Repartidor_Login';
                break;
            default:
                throw new Exception('Tipo de usuario inválido');
        }

        // Conexión a la base de datos
        $db = new Database($this->config[$dbConfigKey]);
        $pdo = $db->getConnection();
        error_log('Conexión a BD establecida');
        // Buscar usuario
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE Usuario = ?");
        $stmt->execute([$usuario]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        error_log('Usuario encontrado: ' . json_encode($user));

        if (!$user || $password !== $user['Password']) {

            error_log('Credenciales inválidas');
            error_log('Usuario: ' . ($user ? 'Encontrado' : 'No encontrado'));
            error_log('Password: ' . (password_verify($password, $user['Password']) ? 'Válido' : 'Inválido'));
            error_log('Password hash: ' . $user['Password']);
            error_log('Password: ' . $password);
            throw new Exception('Credenciales inválidas');
        }
        error_log('JWT: ' . $this->config['jwt']['secret']);

       // En AuthService.php
try {
    $payload = [
        'iat' => time(),
        'exp' => time() + 3600,
        'sub' => $user['IdRepartidor'], // Usar directamente el campo correcto
        'tipo' => $tipo,
        'nombre' => $user['Nombre'],
        // 'email' => $user['Email'] // <-- Eliminar o reemplazar
    ];
    error_log('Payload JWT: ' . json_encode($payload));
    $token = JWT::encode($payload, $this->config['jwt']['secret'], 'HS256');
    return [
        'token' => $token,
        'tipo' => $tipo,
        'expira' => time() + 3600
    ];
} catch (Exception $e) {
    error_log('Error generando JWT: ' . $e->getMessage());
    throw new Exception('Error al generar el token');
}
    }
}