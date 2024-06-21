<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../Html/login.html");
    exit();
}

require_once '../Auth/db.php';

// 3. Recoger el ID del departamento
$idDepartamento = $_GET['id']; // Asumiendo que el ID se envía por GET

// 4. Preparar la consulta SQL
$query = "UPDATE departamento SET estado = 'baja' WHERE id_departamento = :id_departamento";

$stmt = $pdo->prepare($query);

// 5. Ejecutar la consulta
if ($stmt->execute(['id_departamento' => $idDepartamento])) {
    // 6. Redirigir o mostrar mensaje de éxito
    echo "Departamento marcado como baja con éxito.";
} else {
    echo "Error al marcar el departamento como baja.";
}