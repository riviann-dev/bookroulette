<?php
// Iniciamos sesion una sola vez para poder guardar el usuario logueado.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$user = "root";
$pass = "";
$db = "bookroulette";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexion: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Esta funcion crea las tablas que faltan en local.
// Para la presentacion puedes decir que es una ayuda para desarrollo.
ensureSchema($conn);

function ensureSchema(mysqli $conn): void
{
    $conn->query("
        ALTER TABLE libros
        ADD COLUMN IF NOT EXISTS descripcion TEXT NULL AFTER genero
    ");

    $conn->query("
        ALTER TABLE libros
        ADD COLUMN IF NOT EXISTS enlace_compra VARCHAR(255) NULL AFTER isbn_libro
    ");

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
    // Si hay sesion iniciada devolvemos los datos del usuario.
    return $_SESSION["user"] ?? null;
}

function isLoggedIn(): bool
{
    return currentUser() !== null;
}

function isAdmin(): bool
{
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
    // Protege paginas que solo puede usar un administrador.
    if (!isAdmin()) {
        header("Location: " . $redirect);
        exit;
    }
}
?>
