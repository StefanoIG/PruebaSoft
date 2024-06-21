<?php
// Incluir el archivo de conexión a la base de datos
require_once 'db.php';

// Verificar si se recibió el correo electrónico y el código de seguridad por POST
if (isset($_POST['email']) && isset($_POST['codigoSeguridad'])) {
    $email = $_POST['email'];
    $codigoSeguridad = $_POST['codigoSeguridad'];

    try {
        // Consultar en la tabla 'user' si el código de seguridad coincide para el correo electrónico dado
        $stmt = $pdo->prepare("SELECT codigo_seguridad FROM usuarios WHERE email = :email AND codigo_seguridad = :codigoSeguridad");
        $stmt->execute(['email' => $email, 'codigoSeguridad' => $codigoSeguridad]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si el código de seguridad coincide
        if ($userData) {
            // Si el código de seguridad coincide, enviar una respuesta al cliente
            echo json_encode(['success' => true, 'mensaje' => 'El código de seguridad es correcto.']);
        } else {
            // Si el código de seguridad no coincide, enviar una respuesta al cliente
            echo json_encode(['success' => false, 'mensaje' => 'El código de seguridad es incorrecto.']);
        }
    } catch (PDOException $e) {
        // Manejar errores de la base de datos
        echo json_encode(['success' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
    }
} else {
    // Si no se recibió el correo electrónico o el código de seguridad, enviar una respuesta de error al cliente
    echo json_encode(['success' => false, 'mensaje' => 'Error: No se recibió el correo electrónico o el código de seguridad.']);
}
?>