<?php
require_once __DIR__ . "/../db/conexion.php";

// Este endpoint devuelve los favoritos del usuario logueado.
header("Content-Type: application/json; charset=utf-8");

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(["error" => "Debes iniciar sesion."], JSON_UNESCAPED_UNICODE);
    exit;
}

// Usamos un JOIN para traer datos del libro y del favorito a la vez.
$stmt = $conn->prepare("
    SELECT
        favoritos.id_favorito,
        favoritos.fecha,
        libros.id,
        libros.titulo,
        libros.autor,
        libros.genero,
        libros.tono,
        libros.profundidad,
        libros.energia,
        libros.isbn_libro
    FROM favoritos
    INNER JOIN libros ON libros.id = favoritos.id_libro
    WHERE favoritos.id_usuario = ?
    ORDER BY favoritos.fecha DESC
");

$user = currentUser();
// Filtramos por el id del usuario para que nadie vea favoritos de otra cuenta.
$stmt->bind_param("i", $user["id_usuario"]);
$stmt->execute();
$result = $stmt->get_result();

$favorites = [];
while ($row = $result->fetch_assoc()) {
    // Guardamos cada fila en un array para devolverlo como JSON.
    $favorites[] = $row;
}

// Devolvemos una lista JSON que JavaScript pintara en la seccion de favoritos.
echo json_encode($favorites, JSON_UNESCAPED_UNICODE);
?>
