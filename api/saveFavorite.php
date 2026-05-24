<?php
require_once __DIR__ . "/../db/conexion.php";

// Guarda un libro como favorito para el usuario actual.
header("Content-Type: application/json; charset=utf-8");

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(["error" => "Debes iniciar sesion para guardar favoritos."], JSON_UNESCAPED_UNICODE);
    exit;
}

// Recibimos el id del libro desde fetch en formato JSON.
$payload = json_decode(file_get_contents("php://input"), true);
$bookId = (int) ($payload["id_libro"] ?? 0);

if ($bookId <= 0) {
    http_response_code(422);
    echo json_encode(["error" => "Libro no valido."], JSON_UNESCAPED_UNICODE);
    exit;
}

$user = currentUser();

// Insertamos la relacion usuario-libro en la tabla favoritos.
// La tabla tiene una restriccion UNIQUE para impedir favoritos duplicados.
$stmt = $conn->prepare("INSERT INTO favoritos (id_usuario, id_libro) VALUES (?, ?)");
$stmt->bind_param("ii", $user["id_usuario"], $bookId);

if ($stmt->execute()) {
    // Si la insercion sale bien, JavaScript muestra este mensaje al usuario.
    echo json_encode(["ok" => true, "message" => "Libro guardado en favoritos."], JSON_UNESCAPED_UNICODE);
    exit;
}

if ((int) $stmt->errno === 1062) {
    // Error 1062 en MySQL significa entrada duplicada.
    http_response_code(409);
    echo json_encode(["error" => "Ese libro ya esta en favoritos."], JSON_UNESCAPED_UNICODE);
    exit;
}

// Cualquier otro error se trata como problema del servidor.
http_response_code(500);
echo json_encode(["error" => "No se pudo guardar el favorito."], JSON_UNESCAPED_UNICODE);
?>
