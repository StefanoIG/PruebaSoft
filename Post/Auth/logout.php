<?php
// Iniciar sesión (si no está iniciada)
session_start();

// Destruir todas las variables de sesión
$_SESSION = array();

// Borrar la cookie de sesión (si se usa)
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// Finalizar la sesión
session_destroy();

// Redirigir al usuario al formulario de inicio de sesión o a una página de inicio
header("Location: ../../Html/login.html"); // Cambiar "login.php" por la página de inicio de sesión
exit();
?>
