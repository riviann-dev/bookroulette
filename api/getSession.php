<?php
require_once __DIR__ . "/../db/conexion.php";

// Devuelve si hay sesion iniciada y los datos basicos del usuario.
header("Content-Type: application/json; charset=utf-8");

$user = currentUser();

echo json_encode([
    "loggedIn" => $user !== null,
    "user" => $user,
], JSON_UNESCAPED_UNICODE);
?>
