<?php
// salir.php — Controlador de cierre de sesión

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vacía variables de sesión
$_SESSION = [];

// Borra la cookie de sesión (si existe)
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// Destruye la sesión
session_destroy();

// Flag opcional por si quisieras condicionar algo en la vista
$logout_ok = true;

// Carga la vista
require __DIR__ . '/vistas/salir_vista.php';
