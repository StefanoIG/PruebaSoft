<?php
// Incluir el archivo de conexión a la base de datos
require_once 'db.php';

// Establecer la cabecera para JSON
header('Content-Type: application/json');

// Verificar si se recibieron datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recuperar los datos del formulario
    $email = $_POST['correo'];
    $password = $_POST['password'];

    try {
        // Buscar al usuario en la base de datos por su correo electrónico
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si se encontró al usuario
        if ($user) {
            // Verificar la contraseña
            if (password_verify($password, $user['contrasena'])) {
                // Iniciar sesión (o establecer una bandera de sesión)
                session_start();
                $_SESSION['id_usuario'] = $user['id_usuario'];
                $_SESSION['email'] = $user['email'];

                // Verificar el tipo de usuario y redirigir según corresponda
                $redirectUrl = $user['tipo_usuario'] == 'general' ? '../Post/Main/inicio.php' : '../Post//Main/admin.php';
                
                // Devolver respuesta en JSON
                echo json_encode(['success' => true, 'redirect' => $redirectUrl]);
            } else {
                // Contraseña incorrecta
                echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta.']);
            }
        } else {
            // Usuario no encontrado
            echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
        }
    } catch (PDOException $e) {
        // Manejar errores de la base de datos
        echo json_encode(['success' => false, 'message' => 'Error al iniciar sesión: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error: No se recibieron datos del formulario.']);
}
?>
