<?php

require_once __DIR__ . '/utils.php';

use TiposCampos;

$error = getFlashMessage();
$errorEmail    = $error[TiposCampos::EMAIL->value]['message'] ?? null;
$errorPassword = $error[TiposCampos::PASSWORD->value]['message'] ?? null;
$errorConfirmPassword = $error[TiposCampos::CONFIRM_PASSWORD->value]['message'] ?? null;
$errorGeneric  = $error[TiposCampos::GENERIC->value]['message'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $registro = false;
    // Recoger datos del formulario
    $campos = [
        "email" => trim($_POST["email"] ?? ""),
        "password" => $_POST["password"] ?? "",
        "confirm_password" => $_POST["confirm_password"] ?? "",
    ];

    // Validar campos
    $errors = [];
    if (validarCampos($campos, $env)) {
        $dsn = "mysql:dbname=" . $env["DB_DATABASE"] . ";host=" . $env["DB_HOST"] . ";port=" . $env["DB_PORT"];
        try {
            $conn = new PDO($dsn, $env["DB_USER"], $env["DB_PASSWORD"]);
        } catch (Exception $e) {
            addFlashMessage(TiposCampos::GENERIC, $env["UNEXPECTED_ERROR"]);
        }

        // Comprueba si el usuario existe en base de datos
        $sql = "SELECT email FROM users WHERE email = :email";
        $values = [":email" => $campos["email"]];

        try {
            $res = $conn->prepare($sql);
            $res->execute($values);
        } catch (Exception $e) {
            addFlashMessage(TiposCampos::GENERIC, $env["UNEXPECTED_ERROR"]);
        }

        $user = $res->fetch(PDO::FETCH_ASSOC);
        // Si el usuario ya existe, muestro un error genérico para no dar pistas sobre qué ha fallado
        if (is_array($user)) {
            addFlashMessage(TiposCampos::GENERIC, $env["EMAIL_ALREADY_EXIST_ERROR"]);
        } else {
            // Si el usuario no existe, lo creo
            $passwordHash = password_hash($campos["password"], $env["PASSWORD_ALGO"]);
            $sql = "INSERT INTO users (email, password, role) VALUES (:email, :password, 'user')";
            $values = [":email" => $campos["email"], ":password" => $passwordHash];
            try {
                $res = $conn->prepare($sql);
                $res->execute($values);
                $registro = true;
            } catch (Exception $e) {
                addFlashMessage(TiposCampos::GENERIC, $env["DATABASE_INSERT_ERROR"]);
            }
        }
    }

    if ($registro) {
        // Redirigir a login.php después de un registro exitoso
        header("Location: index.php");
        exit();
    }
}

function validarCampos($campos, &$env)
{
    $error = false;
    // Email
    if (empty($campos["email"]) || !filter_var($campos["email"], FILTER_VALIDATE_EMAIL)) {
        addFlashMessage(TiposCampos::EMAIL, $env["EMAIL_INVALID_ERROR"]);
        $error = true;
    }

    // Password
    if (empty($campos["password"]) || strlen($campos["password"]) < 8) {
        addFlashMessage(TiposCampos::PASSWORD, $env["PASSWORD_INVALID_ERROR"]);
        $error = true;
    }

    if (empty($campos["confirm_password"]) || $campos["password"] !== $campos["confirm_password"]) {
        addFlashMessage(TiposCampos::CONFIRM_PASSWORD, $env["CONFIRM_PASSWORD_ERROR"]);
        $error = true;
    }

    return !$error;
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Control de acceso en PHP - mgarlop</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="form-container">
        <h1>Regístrate</h1>
        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">

            <!-- Correo electrónico -->
            <input type="text"
                name="email"
                placeholder="Introduzca su correo electrónico">
            <?php if ($errorEmail): ?>
                <p class="error"><?= $errorEmail ?></p>
            <?php endif; ?>

            <!-- Password -->
            <input type="password"
                name="password"
                placeholder="Introduzca su contraseña">
            <?php if ($errorPassword): ?>
                <p class="error"><?= $errorPassword ?></p>
            <?php endif; ?>

            <!-- Confirm Password -->
            <input type="password"
                name="confirm_password"
                placeholder="Confirme su contraseña">
            <?php if ($errorConfirmPassword): ?>
                <p class="error"><?= $errorConfirmPassword ?></p>
            <?php endif; ?>

            <!-- Errores de registro -->
            <?php if ($errorGeneric): ?>
                <p class="error"><?= $errorGeneric ?></p>
            <?php endif; ?>

            <!-- Submit -->
            <input type="submit" value="Registrarse">
        </form>
    </div>
</body>

</html>