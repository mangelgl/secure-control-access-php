<?php
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: index.php?error=login");
    exit();
} elseif (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: principal.php?error=not_admin");
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
    <h1>Dashboard de administrador</h1>
    <p>Eres capaz de acceder a esta área restringida porque tienes el rol de administrador.</p>
    <ul>
        <li>Información sensible 1</li>
        <li>Información sensible 2</li>
        <li>Información sensible 3</li>
    </ul>
    <a href="principal.php">Volver</a>
</body>

</html>