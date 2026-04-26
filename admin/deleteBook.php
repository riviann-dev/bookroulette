<?php
require_once __DIR__ . "/../db/conexion.php";
requireAdmin();

// Borrado simple por id. Se llama desde el enlace del dashboard.
$bookId = (int) ($_GET["id"] ?? 0);

if ($bookId > 0) {
    // Eliminamos el libro y sus favoritos asociados por clave foranea.
    $stmt = $conn->prepare("DELETE FROM libros WHERE id = ?");
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
}

header("Location: /bookroulette/admin/dashboard.php");
exit;
?>
