<?php
require_once __DIR__ . "/../db/conexion.php";

// Limpiamos la sesion para cerrar la cuenta actual.
// Primero vaciamos el array y luego destruimos la sesion en el servidor.
$_SESSION = [];
session_destroy();

// Volvemos a la portada despues de salir.
header("Location: /bookroulette/index.php");
exit;
?>
