<?php

$env = parse_ini_file(__DIR__ . '/.env');
$errors = [];

if (isset($_REQUEST["error"])) {
    $errors["unauthorized"] = "Debe iniciar sesión!";
}

// Si llega una petición POST, recojo los valores
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Validaciones
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Email no válido";
    }

    if (strlen($password) < 8) {
        $errors["password"] = "Contraseña no válida";
    }

    // Si no hay errores, conecto a la base de datos
    if (empty($errors)) {
        $dsn = "mysql:dbname=" . $env["DB_DATABASE"] . ";host=" . $env["DB_HOST"] . ";port=" . $env["DB_PORT"];
        try {
            $conn = new PDO($dsn, $env["DB_USER"], $env["DB_PASSWORD"]);
        } catch (Exception $e) {
            $errors["database_conn"] = "Servicio no disponible";
            die();
        }

        $sql = "SELECT * FROM users WHERE email = :email";
        $values = [":email" => $email];

        try {
            $res = $conn->prepare($sql);
            $res->execute($values);
        } catch (Exception $e) {
            echo $e;
        }

        $user = $res->fetch(PDO::FETCH_ASSOC);
        if (!is_array($user)) {
            $errors["login"] = "Usuario o contraseña incorrectos";
        } else {
            if (password_verify($password, $user["password"])) {
                session_start();
                $_SESSION["email"] = $user["email"];
                $_SESSION["role"] = $user["role"];
                header("Location: principal.php");
            } else {
                $errors["login"] = "Usuario o contraseña incorrectos";
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Control de acceso en PHP - mgarlop</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        form {
            width: 300px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        p {
            color: red;
            margin: 5px;
        }
    </style>
</head>

<body>
    <h1>Inicia sesión</h1>
    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST">

        <!-- Correo electrónico -->
        <input type="text"
            name="email"
            placeholder="Introduzca su correo electrónico">
        <?php if (isset($errors["email"])): ?>
            <p><?= $errors["email"] ?></p>
        <?php endif; ?>

        <!-- Password -->
        <input type="password"
            name="password"
            placeholder="Introduzca su contraseña">
        <?php if (isset($errors["password"])): ?>
            <p><?= $errors["password"] ?></p>
        <?php endif; ?>

        <?php if (isset($errors["unauthorized"])): ?>
            <p><?= $errors["unauthorized"] ?></p>
        <?php endif; ?>

        <!-- Submit -->
        <input type="submit" value="Iniciar sesión">
    </form>
</body>

</html>
