<?php

// config.php
define('DB_HOST', 'localhost'); // El host de tu base de datos, usualmente es 'localhost'
define('DB_USER', 'root'); // El nombre de usuario de la base de datos de XAMPP por defecto es 'root'
define('DB_PASS', ''); // Por defecto, XAMPP no tiene contraseña para MySQL
define('DB_NAME', 'johan'); // Asegúrate de que el nombre de tu base de datos es correcto
define('DB_PORT', '3307'); // Especifica el puerto aquí

// Intenta conectarte a la base de datos utilizando PDO.
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8";
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    // Establecer el modo de error PDO para excepción
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Conectado a la base de datos."; // Puedes eliminar o comentar esta línea después de verificar la conexión
} catch (PDOException $e) {
    // Manejar la excepción
    //die("ERROR: No se pudo conectar a la base de datos. " . $e->getMessage());
}

// No necesitas retornar la conexión si este archivo será incluido con 'require' o 'include'.
?>
