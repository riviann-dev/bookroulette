<?php
require_once __DIR__ . "/../db/conexion.php";

// Limpiamos la sesion para cerrar la cuenta actual.
$_SESSION = [];
session_destroy();

// Volvemos a la portada despues de salir.
header("Location: /bookroulette/index.php");
exit;
?>
