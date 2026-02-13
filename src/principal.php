<?php
session_start();
$errors = [];

if (!isset($_SESSION["email"])) {
    header("Location: index.php?error=login");
    exit();
}

if (isset($_REQUEST["error"])) {
    $errors["not_admin"] = "No tienes permisos para acceder a esta área!";
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Principal - Control de acceso en PHP - mgarlop</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="dashboard-card">

        <h1>Bienvenido al área restringida</h1>

        <?php if (isset($errors["not_admin"])): ?>
            <p class="error"><?= $errors["not_admin"] ?></p>
        <?php endif; ?>

        <p>Has iniciado sesión como: <br><strong><?= $_SESSION["email"] ?></strong></p>

        <div style="margin-top: 30px;">
            <?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin"): ?>
                <button><a href="admin.php">Administración</a></button>
            <?php endif; ?>

            <button><a href="logout.php">Cerrar sesión</a></button>
        </div>

    </div>
</body>

</html>