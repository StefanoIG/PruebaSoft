<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../Html/login.html");
    exit();
}

require_once '../Auth/db.php';

// 3. Recoger los datos del formulario
$idDepartamento = $_POST['id_departamento'];
$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];
$precio = $_POST['precio'];
// El estado no se actualiza porque el campo está deshabilitado en el formulario

// 4. Validar los datos (Este paso es básico, se recomienda una validación más exhaustiva)
if (empty($nombre) || empty($descripcion) || empty($precio)) {
    echo "Todos los campos son obligatorios.";
    exit();
}

// 5. Preparar la consulta SQL
$query = "UPDATE departamento SET nombre = :nombre, descripcion = :descripcion, precio = :precio WHERE id_departamento = :id_departamento";

$stmt = $pdo->prepare($query);

// 6. Ejecutar la consulta
if ($stmt->execute(['nombre' => $nombre, 'descripcion' => $descripcion, 'precio' => $precio, 'id_departamento' => $idDepartamento])) {
    // 7. Redirigir o mostrar mensaje de éxito
    echo "Departamento actualizado con éxito."; // Asumiendo que esta página existe
} else {
    echo "Error al actualizar el departamento.";
}