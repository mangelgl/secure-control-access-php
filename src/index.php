<?php

require_once __DIR__ . '/utils.php';

use TiposCampos;

$error = getFlashMessage();
$errorEmail    = $error[TiposCampos::EMAIL->value]['message'] ?? null;
$errorPassword = $error[TiposCampos::PASSWORD->value]['message'] ?? null;
$errorGeneric  = $error[TiposCampos::GENERIC->value]['message'] ?? null;

// Si llega una petición POST, recojo los valores
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';
    $login = false;

    $camposFormulario = [
        "email" => $email,
        "password" => $password,
    ];

    // Si no hay errores en las validaciones, conecto a la base de datos
    if (validarCampos($camposFormulario, $env)) {
        $dsn = "mysql:dbname=" . $env["DB_DATABASE"] . ";host=" . $env["DB_HOST"] . ";port=" . $env["DB_PORT"];
        try {
            $conn = new PDO($dsn, $env["DB_USER"], $env["DB_PASSWORD"]);
        } catch (Exception $e) {
            addFlashMessage(TiposCampos::GENERIC, $env["UNEXPECTED_ERROR"]);
        }

        // Comprueba si el usuario existe en base de datos
        $sql = "SELECT email, password, role FROM users WHERE email = :email";
        $values = [":email" => $email];

        try {
            $res = $conn->prepare($sql);
            $res->execute($values);
        } catch (Exception $e) {
            addFlashMessage(TiposCampos::GENERIC, $env["UNEXPECTED_ERROR"]);
        }

        $user = $res->fetch(PDO::FETCH_ASSOC);
        // Si el usuario no existe, muestro un error genérico para no dar pistas sobre qué ha fallado
        if (!is_array($user)) {
            addFlashMessage(TiposCampos::GENERIC, $env["LOGIN_FAILED_ERROR"]);
        } else {
            // Si el usuario existe, compruebo la contraseña
            if (password_verify($password, $user["password"])) {
                $login = true;

                // Si el usuario existe y el algoritmo es antiguo, lo actualizo al nuevo algoritmo
                if (password_needs_rehash($user["password"], $env["PASSWORD_ALGO"])) {
                    $newHash = password_hash($password, $env["PASSWORD_ALGO"]);
                    $sql = "UPDATE users SET password = :password WHERE email = :email";
                    $values = [":password" => $newHash, ":email" => $email];
                    try {
                        $res = $conn->prepare($sql);
                        $res->execute($values);
                    } catch (Exception $e) {
                        addFlashMessage(TiposCampos::GENERIC, $env["UPDATE_ALGORITHM_PASSWORD_ERROR"]);
                    }
                }
            } else {
                addFlashMessage(TiposCampos::GENERIC, $env["LOGIN_FAILED_ERROR"]);
            }
        }
    }

    // Si el login es correcto, inicio sesión y redirijo a la página principal
    if ($login) {
        session_start();
        // Regenera el id de sesión para evitar ataques de "fijación de sesión"
        session_regenerate_id(true);
        $_SESSION["email"] = $user["email"];
        $_SESSION["role"] = $user["role"];
        header("Location: principal.php");
        exit();
    }
}

/* Validaciones:
    * - El email debe ser un email válido
    * - La contraseña debe tener al menos 8 caracteres
*/
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

    // Si el array de errores está vacío, la validación es correcta (true)
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
        <h1>Inicia sesión</h1>
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

            <!-- Errores de login -->
            <?php if ($errorGeneric): ?>
                <p class="error"><?= $errorGeneric ?></p>
            <?php endif; ?>

            <!-- Submit -->
            <input type="submit" value="Iniciar sesión">
            <p>¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p>
        </form>
    </div>
</body>

</html>