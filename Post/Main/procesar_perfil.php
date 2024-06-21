<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../Html/login.html");
    exit();
}

require_once '../Auth/db.php'; // Ajusta la ruta según sea necesario

$data = json_decode(file_get_contents('php://input'), true);
$nombre = $data['nombre'];
$email = $data['email'];
$idUsuario = $_SESSION['id_usuario'];

// Validar datos (añadir validaciones según tus necesidades)

$query = "UPDATE usuarios SET nombre = :nombre, email = :email WHERE id_usuario = :id_usuario";
$stmt = $pdo->prepare($query);

if ($stmt->execute(['nombre' => $nombre, 'email' => $email, 'id_usuario' => $idUsuario])) {
    echo json_encode(['success' => 'Perfil actualizado correctamente.']);
} else {
    echo json_encode(['error' => 'Error al actualizar el perfil.']);
}
?>
