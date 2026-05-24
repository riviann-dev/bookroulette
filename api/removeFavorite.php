<?php
require_once __DIR__ . "/../db/conexion.php";

// Elimina un favorito del usuario actual.
header("Content-Type: application/json; charset=utf-8");

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(["error" => "Debes iniciar sesion."], JSON_UNESCAPED_UNICODE);
    exit;
}

// Recibimos el id del favorito a eliminar.
$payload = json_decode(file_get_contents("php://input"), true);
$favoriteId = (int) ($payload["id_favorito"] ?? 0);

if ($favoriteId <= 0) {
    http_response_code(422);
    echo json_encode(["error" => "Favorito no valido."], JSON_UNESCAPED_UNICODE);
    exit;
}

$user = currentUser();

// Solo se puede borrar un favorito si pertenece al usuario logueado.
// Esta condicion evita que un usuario borre favoritos de otra cuenta.
$stmt = $conn->prepare("DELETE FROM favoritos WHERE id_favorito = ? AND id_usuario = ?");
$stmt->bind_param("ii", $favoriteId, $user["id_usuario"]);
$stmt->execute();

// Aunque no hubiera fila para borrar, respondemos ok porque el estado final es correcto:
// ese favorito ya no aparece para el usuario.
echo json_encode(["ok" => true], JSON_UNESCAPED_UNICODE);
?>
