<?php
require_once __DIR__ . "/../db/conexion.php";
requireAdmin();

// Este formulario permite crear nuevos libros desde el panel admin.
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recogemos los datos enviados por el formulario.
    // trim elimina espacios al principio y al final en los campos de texto.
    $titulo = trim($_POST["titulo"] ?? "");
    $autor = trim($_POST["autor"] ?? "");
    $genero = trim($_POST["genero"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $tono = (int) ($_POST["tono"] ?? 0);
    $profundidad = (int) ($_POST["profundidad"] ?? 0);
    $energia = (int) ($_POST["energia"] ?? 0);
    $isbn = trim($_POST["isbn_libro"] ?? "");
    $enlaceCompra = trim($_POST["enlace_compra"] ?? "");

    // Convertimos cadenas vacias en null para guardar campos opcionales limpios.
    $isbn = $isbn === "" ? null : $isbn;
    $descripcion = $descripcion === "" ? null : $descripcion;
    $enlaceCompra = $enlaceCompra === "" ? null : $enlaceCompra;

    // Solo titulo, autor y genero son obligatorios.
    // Los valores numericos ya vienen controlados por min/max en el formulario.
    if ($titulo === "" || $autor === "" || $genero === "") {
        $error = "Titulo, autor y genero son obligatorios.";
    } else {
        // Insertamos el libro en la base de datos.
        // Usamos consulta preparada para enviar valores sin construir SQL a mano.
        $stmt = $conn->prepare("INSERT INTO libros (titulo, autor, genero, descripcion, tono, profundidad, energia, isbn_libro, enlace_compra) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiiiss", $titulo, $autor, $genero, $descripcion, $tono, $profundidad, $energia, $isbn, $enlaceCompra);
        $stmt->execute();

        // Al terminar volvemos al dashboard para ver el libro en la tabla.
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
    <title>Anadir libro | BookRoulette</title>
    <link rel="stylesheet" href="/bookroulette/css/style.css">
    <link rel="shortcut icon" href="img/logoBookRoulette.png" type="image/x-icon">
</head>
<body class="admin-page">
    <main class="admin-shell">
        <section class="form-card">
            <a class="back-link" href="/bookroulette/admin/dashboard.php">Volver al dashboard</a>
            <h1>Anadir libro</h1>

            <?php if ($error !== ""): ?>
                <!-- Mensaje visible si falta algun campo obligatorio. -->
                <div class="flash flash-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" class="stack-form admin-form">
                <!-- Formulario del CRUD: estos campos coinciden con las columnas de libros. -->
                <label>Titulo<input type="text" name="titulo" required></label>
                <label>Autor<input type="text" name="autor" required></label>
                <label>Genero<input type="text" name="genero" required placeholder="fantasia, terror, romance..."></label>
                <label>Descripcion<textarea name="descripcion" rows="4" placeholder="Resumen breve del libro"></textarea></label>
                <label>ISBN<input type="text" name="isbn_libro" maxlength="13"></label>
                <label>Enlace de compra<input type="url" name="enlace_compra" placeholder="https://..."></label>
                <label>Tono (1-10)<input type="number" name="tono" min="1" max="10" value="5" required></label>
                <label>Profundidad (1-10)<input type="number" name="profundidad" min="1" max="10" value="5" required></label>
                <label>Energia (1-10)<input type="number" name="energia" min="1" max="10" value="5" required></label>
                <button type="submit" class="primary-btn">Guardar libro</button>
            </form>
        </section>
    </main>
</body>
</html>
