<?php


// Incluir conexión a la base de datos
require_once '../Auth/db.php'; // Asegúrate de que este archivo exista y tenga los detalles de conexión a tu base de datos con PDO.

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los valores del formulario
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $latitud = $_POST['latitud'];
    $longitud = $_POST['longitud'];
    $imagen_seccion_1 = $_POST['imagen_seccion_1'];
    $imagen_seccion_2 = $_POST['imagen_seccion_2'];
    $imagen_seccion_3 = $_POST['imagen_seccion_3'];
    $garaje = $_POST['garaje'];
    $banos = $_POST['banos'];
    $cocina = $_POST['cocina'];
    $habitaciones = $_POST['habitaciones'];

    // Asumiendo que el ID del usuario está almacenado en una sesión como 'id_usuario'
    session_start();
    $id_usuario = $_SESSION['id_usuario']; // Asegúrate de que el usuario esté logueado y su ID esté almacenado en $_SESSION

    try {
        // Preparar la consulta SQL para insertar el departamento
        $sql = "INSERT INTO departamento (nombre, descripcion, precio, estado, id_usuario, latitud, longitud, imagen_seccion_1, imagen_seccion_2, imagen_seccion_3, garaje, banos, cocina, habitaciones) VALUES (?, ?, ?, 'en proceso', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        // Preparar la sentencia
        $stmt = $pdo->prepare($sql);
        
        // Vincular los parámetros
        $stmt->bindParam(1, $nombre);
        $stmt->bindParam(2, $descripcion);
        $stmt->bindParam(3, $precio);
        $stmt->bindParam(4, $id_usuario);
        $stmt->bindParam(5, $latitud);
        $stmt->bindParam(6, $longitud);
        $stmt->bindParam(7, $imagen_seccion_1);
        $stmt->bindParam(8, $imagen_seccion_2);
        $stmt->bindParam(9, $imagen_seccion_3);
        $stmt->bindParam(10, $garaje);
        $stmt->bindParam(11, $banos);
        $stmt->bindParam(12, $cocina);
        $stmt->bindParam(13, $habitaciones);
        
        // Ejecutar la sentencia
        $stmt->execute();
        
        echo "Departamento publicado con éxito.";
    } catch (PDOException $e) {
        echo "Error al publicar el departamento: " . $e->getMessage();
    }

    // No es necesario cerrar la conexión explícitamente en PDO
} else {
    // Si no se ha enviado el formulario, redirigir al formulario
    header("Location: ../Main/vender.php");
    exit();
}
?>
