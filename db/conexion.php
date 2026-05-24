<?php
// Iniciamos sesion una sola vez para poder guardar el usuario logueado.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$user = "root";
$pass = "";
$db = "bookroulette";

// Creamos la conexion principal con MySQL.
// Esta variable $conn se reutiliza en todos los archivos que incluyen conexion.php.
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    // Si la base de datos no esta disponible, detenemos la pagina con un mensaje claro.
    die("Error de conexion: " . $conn->connect_error);
}

// utf8mb4 permite guardar correctamente acentos, enes y otros caracteres.
$conn->set_charset("utf8mb4");

// Esta funcion crea las tablas que faltan en local.
// Para la presentacion puedes decir que es una ayuda para desarrollo.
ensureSchema($conn);

function ensureSchema(mysqli $conn): void
{
    // Estas columnas se aseguran por si la base de datos viene de una version anterior.
    $conn->query("
        ALTER TABLE libros
        ADD COLUMN IF NOT EXISTS descripcion TEXT NULL AFTER genero
    ");

    $conn->query("
        ALTER TABLE libros
        ADD COLUMN IF NOT EXISTS enlace_compra VARCHAR(255) NULL AFTER isbn_libro
    ");

    // Tabla de usuarios registrados. El rol permite separar usuarios normales y admins.
    $conn->query("
        CREATE TABLE IF NOT EXISTS usuarios (
            id_usuario INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            rol ENUM('usuario', 'admin') NOT NULL DEFAULT 'usuario',
            fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Tabla intermedia entre usuarios y libros.
    // Guarda que libros ha marcado como favoritos cada usuario.
    $conn->query("
        CREATE TABLE IF NOT EXISTS favoritos (
            id_favorito INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario INT NOT NULL,
            id_libro INT NOT NULL,
            fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_favorito (id_usuario, id_libro),
            CONSTRAINT fk_favoritos_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
            CONSTRAINT fk_favoritos_libro FOREIGN KEY (id_libro) REFERENCES libros(id) ON DELETE CASCADE
        )
    ");
}

function currentUser(): ?array
{
    // Si hay sesion iniciada, devolvemos los datos guardados al hacer login.
    return $_SESSION["user"] ?? null;
}

function isLoggedIn(): bool
{
    // Devuelve true cuando existe un usuario activo en la sesion.
    return currentUser() !== null;
}

function isAdmin(): bool
{
    // Un usuario es admin si esta logueado y su rol es "admin".
    $user = currentUser();
    return $user !== null && ($user["rol"] ?? "") === "admin";
}

function requireLogin(string $redirect = "/bookroulette/auth/login.php"): void
{
    // Protege paginas que solo puede usar un usuario autenticado.
    if (!isLoggedIn()) {
        header("Location: " . $redirect);
        exit;
    }
}

function requireAdmin(string $redirect = "/bookroulette/auth/login.php"): void
{
    // Protege paginas que solo puede usar un administrador, como el CRUD de libros.
    if (!isAdmin()) {
        header("Location: " . $redirect);
        exit;
    }
}
?>
