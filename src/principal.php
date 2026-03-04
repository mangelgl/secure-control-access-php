<?php
require_once __DIR__ . '/utils.php';

use TiposCampos;

$error = getFlashMessage();

if (!isset($_SESSION["email"])) {
    addFlashMessage(TiposCampos::GENERIC, $env["UNAUTHORIZED_ACCESS_ERROR"]);
    header("Location: index.php");
    exit();
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

        <?php if (isset($error[TiposCampos::GENERIC->value])): ?>
            <p class="error"><?= $error[TiposCampos::GENERIC->value]["message"] ?></p>
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