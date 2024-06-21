<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../Html/login.html");
    exit();
}

require_once '../Auth/db.php'; // Ajusta la ruta según sea necesario

$data = json_decode(file_get_contents('php://input'), true);
$contrasena_actual = $data['contrasena_actual'];
$nueva_contrasena = $data['nueva_contrasena'];
$idUsuario = $_SESSION['id_usuario'];

// Obtener la contraseña actual del usuario
$query = "SELECT contrasena FROM usuarios WHERE id_usuario = :id_usuario";
$stmt = $pdo->prepare($query);
$stmt->execute(['id_usuario' => $idUsuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario || !password_verify($contrasena_actual, $usuario['contrasena'])) {
    echo json_encode(['error' => 'La contraseña actual no es correcta.']);
    exit();
}

// Validar la nueva contraseña (puedes añadir más validaciones aquí si es necesario)
if (strlen($nueva_contrasena) < 8) {
    echo json_encode(['error' => 'La nueva contraseña debe tener al menos 8 caracteres.']);
    exit();
}

// Actualizar la contraseña en la base de datos
$nueva_contrasena_hashed = password_hash($nueva_contrasena, PASSWORD_DEFAULT);
$query = "UPDATE usuarios SET contrasena = :contrasena WHERE id_usuario = :id_usuario";
$stmt = $pdo->prepare($query);

if ($stmt->execute(['contrasena' => $nueva_contrasena_hashed, 'id_usuario' => $idUsuario])) {
    echo json_encode(['success' => 'Contraseña actualizada correctamente.']);
} else {
    echo json_encode(['error' => 'Error al actualizar la contraseña.']);
}
?>
