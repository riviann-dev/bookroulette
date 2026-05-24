<?php
require_once __DIR__ . "/../db/conexion.php";

// Si el usuario ya ha iniciado sesion, lo devolvemos al inicio.
if (isLoggedIn()) {
    header("Location: /bookroulette/index.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recogemos y limpiamos los datos del formulario de registro.
    $nombre = trim($_POST["nombre"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $passwordConfirm = $_POST["password_confirm"] ?? "";

    // Validaciones en cadena. En cuanto falla una, se guarda el mensaje de error.
    if ($nombre === "" || $email === "" || $password === "" || $passwordConfirm === "") {
        $error = "Completa todos los campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Introduce un email valido.";
    } elseif (strlen($password) < 6) {
        $error = "La contrasena debe tener al menos 6 caracteres.";
    } elseif ($password !== $passwordConfirm) {
        $error = "Las contrasenas no coinciden.";
    } else {
        // Comprobamos si ya existe una cuenta con el mismo email.
        $existsStmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
        $existsStmt->bind_param("s", $email);
        $existsStmt->execute();
        $existsStmt->store_result();

        if ($existsStmt->num_rows > 0) {
            $error = "Ese email ya esta registrado.";
        } else {
            // La primera cuenta del sistema sera administradora.
            $countResult = $conn->query("SELECT COUNT(*) AS total FROM usuarios");
            $totalUsers = (int) $countResult->fetch_assoc()["total"];
            $rol = $totalUsers === 0 ? "admin" : "usuario";

            // La contrasena nunca se guarda en texto plano.
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Guardamos el nuevo usuario con consulta preparada para evitar inyeccion SQL.
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nombre, $email, $passwordHash, $rol);
            $stmt->execute();

            $success = $rol === "admin"
                ? "Cuenta creada. Al ser la primera cuenta, se ha asignado rol de administrador."
                : "Cuenta creada correctamente. Ya puedes iniciar sesion.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | BookRoulette</title>
    <link rel="stylesheet" href="/bookroulette/css/style.css">
    <link rel="shortcut icon" href="img/logoBookRoulette.png" type="image/x-icon">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-card">
            <a class="back-link" href="/bookroulette/index.php">Volver a BookRoulette</a>
            <h1>Crear cuenta</h1>
            <p class="auth-copy">Registrate para guardar recomendaciones y consultar tus favoritos.</p>

            <?php if ($error !== ""): ?>
                <!-- Mensaje de error cuando algun dato del registro no es valido. -->
                <div class="flash flash-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success !== ""): ?>
                <!-- Mensaje de exito cuando la cuenta se crea correctamente. -->
                <div class="flash flash-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="post" class="stack-form">
                <label>
                    Nombre
                    <input type="text" name="nombre" required>
                </label>
                <label>
                    Email
                    <input type="email" name="email" required>
                </label>
                <label>
                    Contrasena
                    <input type="password" name="password" required>
                </label>
                <label>
                    Repite la contrasena
                    <input type="password" name="password_confirm" required>
                </label>
                <button type="submit" class="primary-btn">Crear cuenta</button>
            </form>

            <p class="helper-text">Si ya tienes cuenta, <a href="/bookroulette/auth/login.php">inicia sesion</a>.</p>
        </section>
    </main>
</body>
</html>
