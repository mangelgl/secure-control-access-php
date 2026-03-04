<?php

require_once __DIR__ . '/utils.php';

use TiposCampos;

if (!isset($_SESSION["email"])) {
    addFlashMessage(TiposCampos::GENERIC, $env["UNAUTHORIZED_ACCESS_ERROR"]);
    header("Location: index.php");
    exit();
} elseif (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    addFlashMessage(TiposCampos::GENERIC, $env["NOT_ADMIN_ERROR"]);
    header("Location: principal.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Admin dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="dashboard-card">

        <h1>Dashboard de administrador</h1>
        <p>Eres capaz de acceder a esta área restringida porque tienes el rol de administrador.</p>
        <ul>
            <li>Información sensible 1</li>
            <li>Información sensible 2</li>
            <li>Información sensible 3</li>
        </ul>
        <button><a href="principal.php">Volver</a></button>

    </div>
</body>

</html>