<?php
require_once __DIR__ . "/../db/conexion.php";

// Si ya hay sesion, no tiene sentido volver al login.
if (isLoggedIn()) {
    header("Location: /bookroulette/index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recogemos los datos enviados desde el formulario.
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    // Validacion basica: no dejamos avanzar si falta algun campo.
    if ($email === "" || $password === "") {
        $error = "Completa email y contrasena.";
    } else {
        // Buscamos al usuario por email.
        $stmt = $conn->prepare("SELECT id_usuario, nombre, email, password, rol FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // password_verify compara la contrasena escrita con el hash guardado.
        if (!$user || !password_verify($password, $user["password"])) {
            $error = "Credenciales incorrectas.";
        } else {
            // Guardamos solo los datos necesarios en la sesion.
            $_SESSION["user"] = [
                "id_usuario" => (int) $user["id_usuario"],
                "nombre" => $user["nombre"],
                "email" => $user["email"],
                "rol" => $user["rol"],
            ];

            // Tras iniciar sesion volvemos a la app principal.
            header("Location: /bookroulette/index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesion | BookRoulette</title>
    <link rel="stylesheet" href="/bookroulette/css/style.css">
    <link rel="shortcut icon" href="/bookroulette/img/logoBookRoulette.png" type="image/x-icon">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-card">
            <a class="back-link" href="/bookroulette/index.php">Volver a BookRoulette</a>
            <h1>Iniciar sesion</h1>
            <p class="auth-copy">Accede para guardar tus libros favoritos y, si eres admin, gestionar el catalogo.</p>

            <?php if ($error !== ""): ?>
                <!-- Mostramos errores de validacion o credenciales sin usar alert(). -->
                <div class="flash flash-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" class="stack-form">
                <label>
                    Email
                    <input type="email" name="email" required>
                </label>
                <label>
                    Contrasena
                    <input type="password" name="password" required>
                </label>
                <button type="submit" class="primary-btn">Entrar</button>
            </form>

            <p class="helper-text">Si aun no tienes cuenta, <a href="/bookroulette/auth/register.php">registrate aqui</a>.</p>
        </section>
    </main>
</body>
</html>
