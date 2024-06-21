<?php
// Incluir el archivo de conexión a la base de datos
require_once 'db.php';

// Verificar si se recibió el correo electrónico por POST
if (isset($_POST['email'])) { // Cambiado 'correo' por 'email'
    $email = $_POST['email']; // Cambiado 'correo' por 'email'

    try {
        // Consultar en la tabla 'user' si existe el correo electrónico
        $stmt = $pdo->prepare("SELECT codigo_seguridad FROM usuarios WHERE email = :email"); // Cambiado 'correo' por 'email' en el valor del array
        $stmt->execute(['email' => $email]); // Cambiado 'correo' por 'email' en la clave del array
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si se encontró el correo electrónico
        if ($userData) {
            // Si el correo electrónico existe, enviar una respuesta al cliente
            echo json_encode(['success' => true, 'mensaje' => 'Correo electrónico encontrado.', 'email' => $email]); // Cambiado 'correo' por 'email'
        } else {
            // Si el correo electrónico no existe, enviar una respuesta al cliente
            echo json_encode(['success' => false, 'mensaje' => 'El correo electrónico ingresado no existe.']);
        }
    } catch (PDOException $e) {
        // Manejar errores de la base de datos
        echo json_encode(['success' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
    }
} else {
    // Si no se recibió el correo electrónico, enviar una respuesta de error al cliente
    echo json_encode(['success' => false, 'mensaje' => 'Error: No se recibió el correo electrónico.']);
}
?>