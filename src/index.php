<?php

$env = parse_ini_file(__DIR__ . '/.env');
$errors = [];
$login = false;

// Si el usuario ha intentado acceder a una página sin iniciar sesión, muestro un error
if (isset($_REQUEST["error"])) {
    $errors["unauthorized"] = "¡Debe iniciar sesión!";
}

// Si llega una petición POST, recojo los valores
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';

    $camposFormulario = [
        "email" => $email,
        "password" => $password,
    ];

    // Si no hay errores en las validaciones, conecto a la base de datos
    if (validarCampos($camposFormulario, $errors)) {
        $dsn = "mysql:dbname=" . $env["DB_DATABASE"] . ";host=" . $env["DB_HOST"] . ";port=" . $env["DB_PORT"];
        try {
            $conn = new PDO($dsn, $env["DB_USER"], $env["DB_PASSWORD"]);
        } catch (Exception $e) {
            $errors["database_conn"] = "Servicio no disponible";
            die();
        }

        // Comprueba si el usuario existe en base de datos
        $sql = "SELECT * FROM users WHERE email = :email";
        $values = [":email" => $email];

        try {
            $res = $conn->prepare($sql);
            $res->execute($values);
        } catch (Exception $e) {
            $errors["user_query_check"] = "No se ha podido comprobar el usuario";
        }

        $user = $res->fetch(PDO::FETCH_ASSOC);
        // Si el usuario no existe, muestro un error genérico para no dar pistas sobre qué ha fallado
        if (!is_array($user)) {
            $errors["login"] = "Usuario o contraseña incorrectos";
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
                        $errors["database_update"] = "No se ha podido actualizar la contraseña";
                    }
                }
            } else {
                $errors["login"] = "Usuario o contraseña incorrectos";
            }
        }
    }

    // Si el login es correcto, inicio sesión y redirijo a la página principal
    if ($login) {
        session_start();
        $_SESSION["email"] = $user["email"];
        $_SESSION["role"] = $user["role"];
        header("Location: principal.php");
    }
}

/* Validaciones:
    * - El email debe ser un email válido
    * - La contraseña debe tener al menos 8 caracteres
*/
function validarCampos($campos, &$errors)
{
    // Email
    if (empty($campos["email"]) || !filter_var($campos["email"], FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Email no válido";
    }

    // Password
    if (empty($campos["password"]) || strlen($campos["password"]) < 8) {
        $errors["password"] = "Contraseña no válida (mínimo 8 caracteres)";
    }

    // Si el array de errores está vacío, la validación es correcta (true)
    return empty($errors);
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
            <?php if (isset($errors["email"])): ?>
                <p class="error"><?= $errors["email"] ?></p>
            <?php endif; ?>

            <!-- Password -->
            <input type="password"
                name="password"
                placeholder="Introduzca su contraseña">
            <?php if (isset($errors["password"])): ?>
                <p class="error"><?= $errors["password"] ?></p>
            <?php endif; ?>

            <!-- Errores de login -->
            <?php if (isset($errors["database_conn"])): ?>
                <p class="error"><?= $errors["database_conn"] ?></p>
            <?php endif; ?>
            <?php if (isset($errors["user_query_check"])): ?>
                <p class="error"><?= $errors["user_query_check"] ?></p>
            <?php endif; ?>
            <?php if (isset($errors["login"])): ?>
                <p class="error"><?= $errors["login"] ?></p>
            <?php endif; ?>
            <?php if (isset($errors["database_update"])): ?>
                <p class="error"><?= $errors["database_update"] ?></p>
            <?php endif; ?>
            <?php if (isset($errors["unauthorized"])): ?>
                <p class="error"><?= $errors["unauthorized"] ?></p>
            <?php endif; ?>

            <!-- Submit -->
            <input type="submit" value="Iniciar sesión">
        </form>
    </div>
</body>

</html>