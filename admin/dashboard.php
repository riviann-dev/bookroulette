<?php
require_once __DIR__ . "/../db/conexion.php";
requireAdmin();

// Cargamos catalogo y datos generales para el panel.
// Esta pagina solo llega aqui si requireAdmin() confirma que el usuario es admin.
$books = $conn->query("SELECT id, titulo, autor, genero, descripcion, tono, profundidad, energia, isbn_libro, enlace_compra FROM libros ORDER BY titulo ASC");

// Contadores rapidos para mostrar un resumen del estado de la aplicacion.
$usersCount = (int) $conn->query("SELECT COUNT(*) AS total FROM usuarios")->fetch_assoc()["total"];
$favoritesCount = (int) $conn->query("SELECT COUNT(*) AS total FROM favoritos")->fetch_assoc()["total"];
$booksCount = (int) $conn->query("SELECT COUNT(*) AS total FROM libros")->fetch_assoc()["total"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard admin | BookRoulette</title>
    <link rel="stylesheet" href="/bookroulette/css/style.css">
</head>
<body class="admin-page">
    <main class="admin-shell">
        <header class="admin-header">
            <div>
                <a class="back-link" href="/bookroulette/index.php">Volver a la app</a>
                <h1>Panel de administracion</h1>
                <p>Gestiona el catalogo, amplialo con nuevos generos y revisa el estado general de BookRoulette.</p>
            </div>
            <a class="primary-btn" href="/bookroulette/admin/addBook.php">Anadir libro</a>
        </header>

        <section class="stats-grid">
            <!-- Tarjetas resumen para explicar el estado de la aplicacion -->
            <article class="stat-card">
                <span>Libros</span>
                <strong><?= $booksCount ?></strong>
            </article>
            <article class="stat-card">
                <span>Usuarios</span>
                <strong><?= $usersCount ?></strong>
            </article>
            <article class="stat-card">
                <span>Favoritos</span>
                <strong><?= $favoritesCount ?></strong>
            </article>
        </section>

        <section class="table-card">
            <div class="table-head">
                <h2>Catalogo</h2>
            </div>
            <div class="table-wrap">
                <!-- Tabla principal del CRUD de libros -->
                <table>
                    <thead>
                        <tr>
                            <th>Titulo</th>
                            <th>Autor</th>
                            <th>Genero</th>
                            <th>Descripcion</th>
                            <th>ISBN</th>
                            <th>Tono</th>
                            <th>Profundidad</th>
                            <th>Energia</th>
                            <th>Compra</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($book = $books->fetch_assoc()): ?>
                            <!-- Cada fila representa un libro del catalogo -->
                            <tr>
                                <!-- htmlspecialchars evita que texto guardado en BD se interprete como HTML. -->
                                <td><?= htmlspecialchars($book["titulo"]) ?></td>
                                <td><?= htmlspecialchars($book["autor"]) ?></td>
                                <td><?= htmlspecialchars($book["genero"]) ?></td>
                                <td><?= htmlspecialchars(substr((string) ($book["descripcion"] ?? ""), 0, 90)) ?><?= !empty($book["descripcion"]) && strlen((string) $book["descripcion"]) > 90 ? "..." : "" ?></td>
                                <td><?= htmlspecialchars($book["isbn_libro"] ?? "-") ?></td>
                                <td><?= (int) $book["tono"] ?></td>
                                <td><?= (int) $book["profundidad"] ?></td>
                                <td><?= (int) $book["energia"] ?></td>
                                <td>
                                    <?php if (!empty($book["enlace_compra"])): ?>
                                        <a href="<?= htmlspecialchars($book["enlace_compra"]) ?>" target="_blank" rel="noopener noreferrer">Ver enlace</a>
                                    <?php else: ?>
                                        <span>-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions-cell">
                                    <a href="/bookroulette/admin/editBook.php?id=<?= (int) $book["id"] ?>">Editar</a>
                                    <a class="danger-link" href="/bookroulette/admin/deleteBook.php?id=<?= (int) $book["id"] ?>" onclick="return confirm('Eliminar este libro?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
