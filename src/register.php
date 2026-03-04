<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $env = parse_ini_file(__DIR__ . '/.env');
    $errors = [];
    $registro = false;
    // Recoger datos del formulario
    $campos = [
        "email" => trim($_POST["email"] ?? ""),
        "password" => $_POST["password"] ?? "",
        "confirm_password" => $_POST["confirm_password"] ?? "",
    ];

    // Validar campos
    $errors = [];
    if (validarCampos($campos, $errors)) {
        $dsn = "mysql:dbname=" . $env["DB_DATABASE"] . ";host=" . $env["DB_HOST"] . ";port=" . $env["DB_PORT"];
        try {
            $conn = new PDO($dsn, $env["DB_USER"], $env["DB_PASSWORD"]);
        } catch (Exception $e) {
            $errors["database_conn"] = "Servicio no disponible";
        }

        // Comprueba si el usuario existe en base de datos
        $sql = "SELECT email FROM users WHERE email = :email";
        $values = [":email" => $campos["email"]];

        try {
            $res = $conn->prepare($sql);
            $res->execute($values);
        } catch (Exception $e) {
            $errors["user_query_check"] = "No se ha podido comprobar el usuario";
        }

        $user = $res->fetch(PDO::FETCH_ASSOC);
        // Si el usuario ya existe, muestro un error genérico para no dar pistas sobre qué ha fallado
        if (is_array($user)) {
            $errors["user_already_exists"] = "El usuario ya existe";
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
                $errors["database_insert"] = "No se ha podido registrar el usuario";
            }
        }
    }

    if ($registro) {
        // Redirigir a login.php después de un registro exitoso
        header("Location: index.php");
        exit();
    }
}

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

    if (empty($campos["confirm_password"]) || $campos["password"] !== $campos["confirm_password"]) {
        $errors["confirm_password"] = "Las contraseñas no coinciden";
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
        <h1>Regístrate</h1>
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

            <!-- Confirm Password -->
            <input type="password"
                name="confirm_password"
                placeholder="Confirme su contraseña">
            <?php if (isset($errors["confirm_password"])): ?>
                <p class="error"><?= $errors["confirm_password"] ?></p>
            <?php endif; ?>

            <!-- Errores de registro -->
            <?php if (isset($errors["database_conn"])): ?>
                <p class="error"><?= $errors["database_conn"] ?></p>
            <?php endif; ?>
            <?php if (isset($errors["user_query_check"])): ?>
                <p class="error"><?= $errors["user_query_check"] ?></p>
            <?php endif; ?>
            <?php if (isset($errors["user_already_exists"])): ?>
                <p class="error"><?= $errors["user_already_exists"] ?></p>
            <?php endif; ?>
            <?php if (isset($errors["database_insert"])): ?>
                <p class="error"><?= $errors["database_insert"] ?></p>
            <?php endif; ?>

            <!-- Submit -->
            <input type="submit" value="Registrarse">
        </form>
    </div>
</body>

</html>