<?php
// Incluir el archivo de conexión a la base de datos
require_once 'db.php';

// Verificar si se recibieron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar los datos del formulario
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $email = $_POST['correo'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $respuesta_1 = $_POST['respuesta_1'];
    $respuesta_2 = $_POST['respuesta_2'];
    $respuesta_3 = $_POST['respuesta_3'];

    // Validar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        echo "Las contraseñas no coinciden.";
        exit; // Detener la ejecución del script
    }

    // Hash de la contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Preparar la consulta para insertar el nuevo usuario en la tabla 'user'
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, email, contrasena, fecha_registro, ultima_actividad) 
                                VALUES (:nombre, :apellido, :correo, :password_hash, CURDATE(), NOW())");
        // Ejecutar la consulta
        $stmt->execute([
            'nombre' => $nombre,
            'apellido' => $apellido,
            'correo' => $email,
            'password_hash' => $password_hash
        ]);

        // Obtener el ID del usuario insertado
        $id_usuario = $pdo->lastInsertId();

        // Preparar la consulta para insertar las respuestas de seguridad en la tabla 'security_answers'
        $stmt = $pdo->prepare("INSERT INTO security_answers (id_usuario, respuesta_1, respuesta_2, respuesta_3) 
                                VALUES (:id_usuario, :respuesta_1, :respuesta_2, :respuesta_3)");
        // Ejecutar la consulta
        $stmt->execute([
            'id_usuario' => $id_usuario,
            'respuesta_1' => $respuesta_1,
            'respuesta_2' => $respuesta_2,
            'respuesta_3' => $respuesta_3
        ]);

        // Generar el código único para el usuario
        $codigo_seguridad = generarCodigoUnico();

        // Actualizar el código único en la tabla de usuarios
        $stmt = $pdo->prepare("UPDATE usuarios SET codigo_seguridad = :codigo_seguridad WHERE id_usuario = :id_usuario");
        $stmt->execute([
            'codigo_seguridad' => $codigo_seguridad,
            'id_usuario' => $id_usuario
        ]);

        // Redireccionar al usuario a una página de éxito o mostrar un mensaje de éxito
        echo "¡Registro exitoso!";

    } catch (PDOException $e) {
        // Manejar errores de la base de datos
        echo "Error al registrar usuario: " . $e->getMessage();
    }
} else {
    // Si no se recibieron datos por POST, redireccionar a una página de error o mostrar un mensaje de error
    echo "Error: No se recibieron datos del formulario.";
}

// Función para generar un código único
function generarCodigoUnico() {
    // Lógica para generar el código único (puedes adaptarla según tus necesidades)
    return substr(md5(uniqid(mt_rand(), true)), 0, 6); // Ejemplo de generación de un código de 6 caracteres
}
?>
