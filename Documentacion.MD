# Documentación Completa de la API

Esta documentación describe en detalle la arquitectura, el flujo, la funcionalidad y las medidas de seguridad implementadas en la API. Se detalla la estructura de archivos, la función de cada componente y se explican ejemplos cotidianos para facilitar la comprensión del proceso.

---

## Índice

1. [Introducción](#introducción)
2. [Estructura del Proyecto](#estructura-del-proyecto)
   - [Configuración y Conexiones](#configuración-y-conexiones)
   - [Punto de Entrada (public/index.php)](#punto-de-entrada-publicindexphp)
   - [Modelos](#modelos)
   - [Controladores](#controladores)
   - [Servicios](#servicios)
   - [Repositorios](#repositorios)
   - [Middleware](#middleware)
3. [Flujo de Ejecución](#flujo-de-ejecución)
4. [Seguridad y Encriptación](#seguridad-y-encriptación)
5. [Ejemplos Cotidianos](#ejemplos-cotidianos)
6. [Manejo de Errores y Logging](#manejo-de-errores-y-logging)
7. [Conclusiones](#conclusiones)

---

## Introducción

La API está desarrollada utilizando el framework **Slim** en PHP, permitiendo la gestión de datos de proveedores y repartidores. Utiliza autenticación basada en JWT, encriptación AES-256-CBC para la información sensible y cuenta con un sistema modular que facilita la escalabilidad y el mantenimiento. Además, incorpora medidas de logging y manejo de errores para asegurar una operación robusta.

---

## Estructura del Proyecto

### Configuración y Conexiones

- **Archivo:** `config/Conexion.php`  
  **Función:**  
  - Define las conexiones a dos bases de datos:  
    - `db_proveedores`: Configuración para la base de datos de proveedores.  
    - `db_repartidores`: Configuración para la base de datos de repartidores.  
  - Especifica parámetros críticos de seguridad:  
    - **JWT:** Clave secreta para firmar y verificar tokens.  
    - **Encryption:** Clave para encriptar datos sensibles.
  
  **Importancia:**  
  Centraliza la configuración, permitiendo realizar ajustes de conexión y seguridad en un solo lugar.

### Punto de Entrada (public/index.php)

- **Archivo:** `public/index.php`  
  **Función:**  
  - Es el punto de entrada de la API.  
  - Carga dependencias mediante Composer (`vendor/autoload.php`).
  - Configura y añade los siguientes middleware:
    - **CORS:** Permite solicitudes de múltiples orígenes.
    - **Body Parsing:** Para recibir y parsear JSON.
    - **Logging:** Registra cada solicitud y respuesta usando Monolog.
    - **Manejo de Errores:** Captura y responde a excepciones no controladas.
  - Define las rutas principales y agrupa aquellas que requieren autenticación mediante el `AuthMiddleware`.
  
  **Rutas Clave:**
  - **`/` (GET):** Ruta de bienvenida con la lista de endpoints disponibles.
  - **`/auth/login` (POST):** Autenticación y generación de JWT.
  - **`/ofertas` (GET):** Obtención de ofertas encriptadas.
  - **`/proveedores` (GET):** Consulta de proveedores encriptados.
  - **`/repartidor` (POST):** Inserción de repartidores en la base de datos.
  
  **Proceso:**  
  Cada solicitud es registrada, verificada (en caso de rutas protegidas) y procesada por el controlador correspondiente.

### Modelos

Los modelos representan las entidades de la aplicación y encapsulan sus propiedades y métodos de acceso (getters):

- **Proveedores**
  - **Archivo:** `src/Model/Proveedor/Proveedores.php`
  - **Propiedades:** `IdProveedor`, `Usuario`, `RazonSocial`, `Contacto`, `Telefono`, `Email`, `Estatus`
  - **Función:** Representa la entidad de un proveedor.

- **Ofertas**
  - **Archivo:** `src/Model/Proveedor/Ofertas.php`
  - **Propiedades:** `IdOferta`, `IdProveedor`, `NombreProducto`, `Descripcion`, `Precio`, `CantidadDisponible`, `DisponibilidadInmediata`, `Categoria`, `ComentariosAdicionales`, `FechaPublicacion`
  - **Función:** Representa una oferta de producto publicada por un proveedor.

- **Repartidor**
  - **Archivo:** `src/Model/Repartidor/Repartidores.php`
  - **Propiedades:** `IdRepartidor`, `Transporte`, `Placas`, `KilosTortillaAsignados`, `HoraInicio`, `HoraFin`
  - **Función:** Representa la entidad de un repartidor y su asignación de turno.

### Controladores

Los controladores reciben las solicitudes HTTP, interactúan con los servicios y devuelven respuestas en formato JSON:

- **Proveedores/OfertasController**
  - **Archivo:** `src/Controller/Proveedor/OfertasController.php`
  - **Función:**  
    - Obtiene las ofertas a través del servicio correspondiente.
    - Cifra la información y la encapsula en un token JWT.

- **Proveedores/ProveedoresController**
  - **Archivo:** `src/Controller/Proveedor/ProveedoresController.php`
  - **Función:**  
    - Consulta la información de proveedores.
    - Encripta los datos y los devuelve dentro de un JWT.

- **Repartidores/RepartidorController**
  - **Archivo:** `src/Controller/Repartidor/RepartidorController.php`
  - **Función:**  
    - Inserta datos de un repartidor en la base de datos.
    - Realiza validaciones y devuelve el resultado de la operación.

- **OAuth/OAuthController**
  - **Archivo:** `src/Controller/OAuth/OAuthController.php`
  - **Función:**  
    - Gestiona el proceso de autenticación de usuarios.
    - Valida credenciales y genera el token JWT de acceso.

### Servicios

Los servicios contienen la lógica de negocio, como la encriptación de datos y la autenticación:

- **OfertasService**
  - **Archivo:** `src/Service/Proveedores/OfertasService.php`
  - **Función:**  
    - Obtiene todas las ofertas mediante el repositorio.
    - Convierte la información a JSON y la cifra usando AES-256-CBC.
    - Devuelve el payload encriptado concatenado con el IV.

- **ProveedoresService**
  - **Archivo:** `src/Service/Proveedores/ProveedoresService.php`
  - **Función:**  
    - Recupera la información de proveedores y la encripta de forma similar a `OfertasService`.

- **RepartidorService**
  - **Archivo:** `src/Service/Repartidor/RepartidorService.php`
  - **Función:**  
    - Valida y procesa los datos recibidos para insertar un repartidor.
    - Llama al repositorio de repartidores para guardar la información en la base de datos.

- **AuthService**
  - **Archivo:** `src/Service/OAuth/AuthService.php`
  - **Función:**  
    - Realiza la autenticación de usuarios según el tipo (compras, proveedor o repartidor).
    - Consulta la base de datos correspondiente para verificar credenciales.
    - Genera un token JWT que contiene datos básicos del usuario y la fecha de expiración.

### Repositorios

Los repositorios gestionan la interacción directa con la base de datos utilizando PDO:

- **OfertasRepository**
  - **Archivo:** `src/Repository/Proveedor/OfertasRepository.php`
  - **Función:**  
    - Ejecuta la consulta SQL para obtener todas las ofertas de la tabla `Proveedor_Ofertas`.
    - Convierte cada fila de la consulta en una instancia del modelo `Ofertas`.

- **ProveedoresRepository**
  - **Archivo:** `src/Repository/Proveedor/ProveedoresRepository.php`
  - **Función:**  
    - Ejecuta la consulta SQL para recuperar los datos de proveedores de la tabla `Proveedor_Login`.
    - Mapea los resultados a instancias del modelo `Proveedores`.

- **RepartidorRepository**
  - **Archivo:** `src/Repository/Repartidor/RepartidorRepository.php`
  - **Función:**  
    - Inserta los datos de un repartidor en la tabla `Repartidor_Turno`.
    - Devuelve el ID del registro insertado.

### Middleware

Los middleware implementan funcionalidades transversales, tales como CORS, autenticación y logging:

- **AuthMiddleware**
  - **Archivo:** `src/Middleware/AuthMiddleware.php`
  - **Función:**  
    - Verifica que las solicitudes a rutas protegidas incluyan un token JWT válido.
    - Decodifica el token y, en caso de error, devuelve una respuesta de error (401/403).

- **CorsMiddleware**
  - **Archivo:** `src/Middleware/CorsMiddleware.php`
  - **Función:**  
    - Añade las cabeceras necesarias para permitir solicitudes CORS.
    - Permite que la API sea consumida desde diferentes orígenes.

---

## Flujo de Ejecución

El flujo de ejecución de la API se puede entender en los siguientes pasos:

1. **Solicitud Inicial:**
   - Un cliente (por ejemplo, una aplicación web o móvil) envía una solicitud HTTP a la API.
   - La solicitud llega al punto de entrada `public/index.php`, donde se aplican middleware para CORS, logging y manejo del body.

2. **Autenticación:**
   - Para acceder a rutas protegidas, el cliente debe autenticarse enviando credenciales al endpoint `/auth/login`.
   - El `AuthController` valida las credenciales usando `AuthService`. Si la autenticación es exitosa, se genera y devuelve un token JWT.
   - Este token actúa como pase para realizar solicitudes posteriores.

3. **Acceso a Endpoints Protegidos:**
   - El cliente utiliza el token JWT en la cabecera `Authorization` para acceder a endpoints como `/ofertas`, `/proveedores` o `/repartidor`.
   - El `AuthMiddleware` valida el token. Si es válido, la solicitud se redirige al controlador correspondiente.

4. **Procesamiento de la Solicitud:**
   - El controlador invoca el servicio respectivo, que a su vez consulta el repositorio para interactuar con la base de datos.
   - Para consultas (ofertas, proveedores), el servicio obtiene los datos, los convierte a JSON y los cifra.
   - Para inserciones (repartidor), se validan los campos y se inserta el registro en la base de datos.

5. **Respuesta:**
   - El controlador devuelve una respuesta JSON. En el caso de datos sensibles, la información se encapsula en un JWT o se cifra.
   - En caso de errores, se retorna un mensaje descriptivo y se registra el error en el log.

---

## Seguridad y Encriptación

- **JWT (JSON Web Token):**
  - Se utiliza para autenticar a los usuarios. Cada token incluye:
    - Fecha de emisión (`iat`).
    - Fecha de expiración (`exp`).
    - Datos del usuario (como `sub` y `tipo`).
  - Se firma con una clave secreta definida en `config/Conexion.php`.

- **Encriptación:**
  - Los datos sensibles (por ejemplo, ofertas y proveedores) se convierten a JSON y se cifran con **AES-256-CBC**.
  - Se utiliza una clave de encriptación derivada mediante SHA-256 para asegurar 32 bytes.
  - El proceso incluye la generación de un vector de inicialización (IV) aleatorio y la concatenación del dato cifrado (en Base64) con el IV.

- **Middleware de Autenticación:**
  - El `AuthMiddleware` se asegura de que solo los clientes autenticados puedan acceder a ciertos endpoints.
  - Se valida y decodifica el token JWT, y se adjunta la información del usuario a la solicitud para un procesamiento seguro.

---

## Ejemplos Cotidianos

Para facilitar la comprensión, se pueden usar analogías de la vida diaria:

- **Autenticación y Token JWT:**
  - Imagina que entras a un edificio seguro. Primero, muestras tu identificación en la recepción (envías tus credenciales al endpoint `/auth/login`).  
  - Si la identificación es correcta, te entregan una tarjeta de acceso (token JWT) que te permite ingresar a diferentes áreas del edificio (acceder a otros endpoints).

- **Acceso a Servicios Protegidos:**
  - Con la tarjeta de acceso, puedes entrar a zonas restringidas. Si intentas acceder sin la tarjeta o con una caducada, el personal de seguridad (AuthMiddleware) te impide el ingreso.

- **Encriptación de Datos:**
  - Piensa en enviar un sobre sellado con información sensible. Sólo la persona con la llave adecuada puede abrirlo y leer su contenido. De la misma forma, los datos cifrados solo pueden ser descifrados por el receptor autorizado.

- **Logging y Manejo de Errores:**
  - Cada entrada y salida del edificio se registra en un libro de visitas (logs). Si ocurre algún incidente, se puede revisar el registro para identificar qué sucedió.

---

## Manejo de Errores y Logging

- **Logging con Monolog:**
  - Todas las solicitudes y respuestas se registran en `logs/app.log`.  
  - Se capturan detalles de cada solicitud (método, URI, cabeceras) y se logean errores críticos o excepciones.

- **Middleware de Errores:**
  - Se utiliza un middleware global para capturar excepciones no controladas y devolver respuestas JSON con mensajes de error claros.
  - Esto asegura que, en caso de fallos, el sistema no se detenga y se proporcione retroalimentación útil al desarrollador o al cliente.

---

## Conclusiones

La API está diseñada con un enfoque modular y seguro, facilitando la gestión de múltiples entidades (proveedores, ofertas y repartidores) mediante:

- **Configuración Centralizada:** Permite una rápida actualización de parámetros de conexión y seguridad.
- **Arquitectura Basada en Componentes:** Modelos, controladores, servicios y repositorios separados para mantener una alta cohesión y bajo acoplamiento.
- **Seguridad Robusta:** Uso de JWT, encriptación AES-256-CBC y middleware de autenticación para proteger la información.
- **Manejo de Errores y Logging:** Registro detallado de todas las operaciones para facilitar la resolución de problemas.
- **Analogías Cotidianas:** Ejemplos que facilitan la comprensión del flujo y la seguridad implementada.

Esta documentación brinda una visión completa del funcionamiento interno de la API, facilitando su mantenimiento, escalabilidad y el entendimiento de su flujo tanto para desarrolladores como para operadores.

---
