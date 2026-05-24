<?php
require_once __DIR__ . "/../db/conexion.php";
requireAdmin();

// Primero buscamos el libro que se va a editar.
// El id llega por la URL: editBook.php?id=...
$bookId = (int) ($_GET["id"] ?? 0);
$stmt = $conn->prepare("SELECT * FROM libros WHERE id = ?");
$stmt->bind_param("i", $bookId);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

if (!$book) {
    // Si el id no existe, volvemos al dashboard para evitar mostrar un formulario vacio.
    header("Location: /bookroulette/admin/dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recogemos los cambios del formulario.
    // Se usan los mismos nombres que en addBook.php para mantener el CRUD consistente.
    $titulo = trim($_POST["titulo"] ?? "");
    $autor = trim($_POST["autor"] ?? "");
    $genero = trim($_POST["genero"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $tono = (int) ($_POST["tono"] ?? 0);
    $profundidad = (int) ($_POST["profundidad"] ?? 0);
    $energia = (int) ($_POST["energia"] ?? 0);
    $isbn = trim($_POST["isbn_libro"] ?? "");
    $enlaceCompra = trim($_POST["enlace_compra"] ?? "");

    // Los campos opcionales se guardan como null si el admin los deja vacios.
    $isbn = $isbn === "" ? null : $isbn;
    $descripcion = $descripcion === "" ? null : $descripcion;
    $enlaceCompra = $enlaceCompra === "" ? null : $enlaceCompra;

    // Evitamos actualizar registros incompletos.
    if ($titulo === "" || $autor === "" || $genero === "") {
        $error = "Titulo, autor y genero son obligatorios.";
    } else {
        // Actualizamos el registro con los nuevos datos.
        // El WHERE id = ? asegura que solo se cambia el libro seleccionado.
        $updateStmt = $conn->prepare("UPDATE libros SET titulo = ?, autor = ?, genero = ?, descripcion = ?, tono = ?, profundidad = ?, energia = ?, isbn_libro = ?, enlace_compra = ? WHERE id = ?");
        $updateStmt->bind_param("ssssiiissi", $titulo, $autor, $genero, $descripcion, $tono, $profundidad, $energia, $isbn, $enlaceCompra, $bookId);
        $updateStmt->execute();

        // Tras guardar, volvemos a la lista del catalogo.
        header("Location: /bookroulette/admin/dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar libro | BookRoulette</title>
    <link rel="stylesheet" href="/bookroulette/css/style.css">
</head>
<body class="admin-page">
    <main class="admin-shell">
        <section class="form-card">
            <a class="back-link" href="/bookroulette/admin/dashboard.php">Volver al dashboard</a>
            <h1>Editar libro</h1>

            <?php if ($error !== ""): ?>
                <!-- Muestra errores de validacion del formulario. -->
                <div class="flash flash-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" class="stack-form admin-form">
                <!-- Los value cargan la informacion actual del libro para poder modificarla. -->
                <label>Titulo<input type="text" name="titulo" required value="<?= htmlspecialchars($book["titulo"]) ?>"></label>
                <label>Autor<input type="text" name="autor" required value="<?= htmlspecialchars($book["autor"]) ?>"></label>
                <label>Genero<input type="text" name="genero" required value="<?= htmlspecialchars($book["genero"]) ?>"></label>
                <label>Descripcion<textarea name="descripcion" rows="4" placeholder="Resumen breve del libro"><?= htmlspecialchars($book["descripcion"] ?? "") ?></textarea></label>
                <label>ISBN<input type="text" name="isbn_libro" maxlength="13" value="<?= htmlspecialchars($book["isbn_libro"] ?? "") ?>"></label>
                <label>Enlace de compra<input type="url" name="enlace_compra" placeholder="https://..." value="<?= htmlspecialchars($book["enlace_compra"] ?? "") ?>"></label>
                <label>Tono (1-10)<input type="number" name="tono" min="1" max="10" value="<?= (int) $book["tono"] ?>" required></label>
                <label>Profundidad (1-10)<input type="number" name="profundidad" min="1" max="10" value="<?= (int) $book["profundidad"] ?>" required></label>
                <label>Energia (1-10)<input type="number" name="energia" min="1" max="10" value="<?= (int) $book["energia"] ?>" required></label>
                <button type="submit" class="primary-btn">Actualizar libro</button>
            </form>
        </section>
    </main>
</body>
</html>
