<?php
require_once 'db.php';

if (
    isset($_POST['email']) &&
    isset($_POST['respuesta1']) &&
    isset($_POST['respuesta2']) &&
    isset($_POST['respuesta3'])
) {
    $email = $_POST['email'];
    $respuesta1 = $_POST['respuesta1'];
    $respuesta2 = $_POST['respuesta2'];
    $respuesta3 = $_POST['respuesta3'];

    try {
        // Mostrar el correo electrónico enviado para buscar al usuario
        error_log("Email enviado para buscar al usuario: " . $email);

        // Buscar el ID del usuario basado en el correo proporcionado
        $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si se encontró el usuario
        if ($userData) {
            // Obtener el ID del usuario
            $userId = $userData['id_usuario'];

            // Mostrar la ID del usuario encontrada en la base de datos
            error_log("ID del usuario encontrado: " . $userId);

            // Buscar las respuestas de seguridad asociadas al ID del usuario
            $stmt = $pdo->prepare("SELECT * FROM security_answer WHERE id_usuario = :id_usuario");
            $stmt->execute(['userId' => $userId]);
            $securityData = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar si se encontraron las respuestas
            if ($securityData) {
               // Comparar las respuestas proporcionadas con las almacenadas en la base de datos
                if (
                    $respuesta1 === $securityData['respuesta_1'] &&
                    $respuesta2 === $securityData['respuesta_2'] &&
                    $respuesta3 === $securityData['respuesta_3']
                ) {
                    echo json_encode(['success' => true, 'mensaje' => 'Respuestas verificadas correctamente.']);
                } else {
                    echo json_encode(['success' => false, 'mensaje' => 'Las respuestas no coinciden.']);
                }

            } else {
                echo json_encode(['success' => false, 'mensaje' => 'No se encontraron respuestas de seguridad asociadas a este usuario.']);
            }
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'No se encontró ningún usuario con el email proporcionado.']);
        }
    } catch (PDOException $e) {
        // Manejar errores de la base de datos
        echo json_encode(['success' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'mensaje' => 'Error: Datos incompletos']);
}
?>