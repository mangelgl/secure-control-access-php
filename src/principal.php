<?php
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: index.php?error=login");
    exit();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Principal - Control de acceso en PHP - mgarlop</title>
        <style>
            body {
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
            }
        </style>
    </head>
    <body>
        <h1>Bienvenido al área restringida</h1>
        <p>Has iniciado sesión como <?= $_SESSION["email"] ?></p>
        <a href="logout.php">Cerrar sesión</a>
    </body>
</html>
