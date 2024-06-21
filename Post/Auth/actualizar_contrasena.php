<?php
require_once 'db.php';
if (isset($_POST['email']) && isset($_POST['nuevaContrasena'])) {
    $email = $_POST['email'];
    $nuevaContrasena = password_hash($_POST['nuevaContrasena'], PASSWORD_DEFAULT); // Hashear la nueva contraseña
    try {
        // Actualizar la contraseña en la base de datos
        $stmt = $pdo->prepare("UPDATE usuarios SET contrasena = :contrasena WHERE email = :email");
        $stmt->execute(['contrasena' => $nuevaContrasena, 'email' => $email]);
        
        echo json_encode(['success' => true, 'mensaje' => 'Contraseña actualizada con éxito.']);
    } catch (PDOException $e) {
        // Manejar errores de la base de datos
        echo json_encode(['success' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'mensaje' => 'Error: Datos incompletos']);
}
?>