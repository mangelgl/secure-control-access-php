<?php

enum TiposErrores: string
{
    case ERROR = 'error';
    case WARNING = 'warn';
    case SUCCESS = 'success';
}

enum TiposCampos: string
{
    case EMAIL = 'email';
    case PASSWORD = 'password';
    case CONFIRM_PASSWORD = 'confirm_password';
    case GENERIC = 'generic';
}

// Inicia la sesión si no hay una iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Parsea las variables de entorno
$env = parse_ini_file(__DIR__ . '/.env');

/**
 * Añade un mensaje temporal en la sesión.
 */
function addFlashMessage(TiposCampos $fieldName, $message, TiposErrores $type = TiposErrores::ERROR)
{
    // Si no existe el array en la sesión, lo creamos
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }

    // Añadimos el nuevo mensaje a la lista
    $_SESSION['flash_messages'][$fieldName->value] = [
        'type'    => $type,
        'message' => $message
    ];
}

/**
 * Recupera el mensaje de la sesión y lo ELIMINA inmediatamente.
 */
function getFlashMessage()
{
    if (isset($_SESSION['flash_messages'])) {
        $messages = $_SESSION['flash_messages'];
        unset($_SESSION['flash_messages']); // Borramos todos tras leerlos
        return $messages;
    }
    return [];
}
