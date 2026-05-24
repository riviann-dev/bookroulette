<?php
require_once __DIR__ . "/db/conexion.php";

// Obtenemos el usuario activo si hay sesion iniciada.
$user = currentUser();

// Cargamos los generos desde la base de datos para que el selector sea dinamico.
$genresResult = $conn->query("SELECT DISTINCT genero FROM libros WHERE genero IS NOT NULL AND genero <> '' ORDER BY genero ASC");
$genres = [];

while ($genreRow = $genresResult->fetch_assoc()) {
    $genres[] = $genreRow["genero"];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookRoulette</title>
    <link rel="stylesheet" href="/bookroulette/css/style.css">
    <link rel="shortcut icon" href="img/logoBookRoulette.png" type="image/x-icon">
</head>
<body>
    <header class="hero">
        <!-- Barra superior con acceso a login, logout y panel admin -->
        <nav class="topbar">
            <a class="brand" href="/bookroulette/index.php">BookRoulette</a>
            <div class="topbar-links">
                <?php if ($user): ?>
                    <span class="user-pill">Hola, <?= htmlspecialchars($user["nombre"]) ?></span>
                    <?php if (($user["rol"] ?? "") === "admin"): ?>
                        <a href="/bookroulette/admin/dashboard.php">Panel admin</a>
                    <?php endif; ?>
                    <a href="/bookroulette/auth/logout.php">Cerrar sesion</a>
                <?php else: ?>
                    <a href="/bookroulette/auth/login.php">Iniciar sesion</a>
                    <a href="/bookroulette/auth/register.php">Crear cuenta</a>
                <?php endif; ?>
            </div>
        </nav>

        <section class="hero-content">
            <!-- Texto de presentacion del proyecto -->
            <div class="hero-copy">
                <p class="eyebrow">Recomendacion literaria interactiva</p>
                <h1>Descubre tu siguiente lectura con una ruleta guiada por tu mood.</h1>
                <p class="lead">Elige un genero, dinos como te sientes y deja que el motor heuristico seleccione opciones con tono, profundidad y energia acordes a ti.</p>
            </div>

            <!-- Panel principal donde el usuario elige genero y mood -->
            <div class="control-panel">
                <label>
                    Genero literario
                    <select id="genero">
                        <option value="">Selecciona genero</option>
                        <?php foreach ($genres as $genre): ?>
                            <option value="<?= htmlspecialchars($genre) ?>"><?= htmlspecialchars(ucwords($genre)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    Estado de animo
                    <select id="mood">
                        <option value="">Selecciona mood</option>
                        <option value="feliz">Feliz</option>
                        <option value="relajado">Relajado</option>
                        <option value="reflexivo">Reflexivo</option>
                        <option value="curioso">Curioso</option>
                        <option value="aventurero">Aventurero</option>
                        <option value="romantico">Romantico</option>
                        <option value="intenso">Con ganas de algo intenso</option>
                        <option value="oscuro">Oscuro</option>
                    </select>
                </label>

                <div class="button-row">
                    <button id="generateButton" class="primary-btn" type="button">Generar opciones</button>
                    <button id="spinButton" class="secondary-btn" type="button">Girar ruleta</button>
                </div>
                <p id="statusMessage" class="status-text"></p>
            </div>
        </section>
    </header>

    <main class="page-shell">
        <section class="experience-grid">
            <!-- Zona visual de la ruleta -->
            <article class="wheel-card">
                <div class="ruleta-container">
                    <div class="puntero"></div>
                    <div id="ruleta" aria-label="Ruleta de recomendaciones"></div>
                </div>
            </article>

            <!-- Zona donde aparece la ficha del libro ganador -->
            <article class="result-card">
                <p class="section-kicker">Resultado</p>
                <div id="resultado">
                    <h2>Tu recomendacion aparecera aqui</h2>
                    <p>Genera opciones para ver los libros mejor puntuados y gira la ruleta cuando quieras.</p>
                </div>
                <?php if ($user): ?>
                    <button id="favoriteButton" class="primary-btn hidden" type="button">Guardar en favoritos</button>
                <?php else: ?>
                    <p class="helper-text">Si quieres guardar libros, <a href="/bookroulette/auth/login.php">inicia sesion</a> o <a href="/bookroulette/auth/register.php">crea una cuenta</a>.</p>
                <?php endif; ?>
            </article>
        </section>

        <section class="recommendation-panel">
            <!-- Tarjetas con las mejores opciones que entran en la ruleta -->
            <div class="panel-heading">
                <div>
                    <p class="section-kicker">Seleccion</p>
                    <h2>Opciones que entran en la ruleta</h2>
                </div>
            </div>
            <div id="optionsList" class="options-list"></div>
        </section>

        <?php if ($user): ?>
            <section class="recommendation-panel">
                <!-- Lista de libros guardados por el usuario -->
                <div class="panel-heading">
                    <div>
                        <p class="section-kicker">Tu biblioteca</p>
                        <h2>Favoritos guardados</h2>
                    </div>
                </div>
                <div id="favoritesList" class="favorites-list"></div>
            </section>
        <?php endif; ?>
    </main>

    <script>
        // Configuracion basica compartida con JavaScript.
        window.bookRouletteConfig = {
            baseUrl: "/bookroulette",
            user: <?= json_encode($user, JSON_UNESCAPED_UNICODE) ?>
        };
    </script>
    <script type="module" src="/bookroulette/js/app.js"></script>
</body>
</html>
