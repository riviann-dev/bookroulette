<?php
require_once __DIR__ . "/../db/conexion.php";

// La API devuelve siempre JSON para que JavaScript pueda leerlo.
header("Content-Type: application/json; charset=utf-8");

// Sacamos todos los libros del catalogo con los campos que usa la app.
$sql = "SELECT id, titulo, autor, genero, descripcion, tono, profundidad, energia, isbn_libro, enlace_compra FROM libros ORDER BY titulo ASC";
$result = $conn->query($sql);

$books = [];

while ($row = $result->fetch_assoc()) {
    // Convertimos estos campos a numero para trabajar mejor en JS.
    // MySQL los devuelve como texto, pero el algoritmo de scoring necesita numeros.
    $row["tono"] = (int) $row["tono"];
    $row["profundidad"] = (int) $row["profundidad"];
    $row["energia"] = (int) $row["energia"];
    $books[] = $row;
}

// JSON_UNESCAPED_UNICODE mantiene acentos y caracteres espanoles legibles.
echo json_encode($books, JSON_UNESCAPED_UNICODE);
?>
