<?php
session_start(); // Iniciar sesión

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    // Si el usuario no está autenticado, redirigirlo al formulario de inicio de sesión
    header("Location: ../../Html/login.html");
    exit();
}

require_once '../Auth/db.php'; // Conexión a la base de datos

// Obtener el ID del departamento y el ID del modo de pago desde la solicitud GET
$idDepartamento = isset($_GET['id_departamento']) ? $_GET['id_departamento'] : die('ERROR: Falta el ID del departamento.');
$idModoPago = isset($_GET['id_modo']) ? $_GET['id_modo'] : die('ERROR: Falta el ID del modo de pago.');
$idUsuario = $_SESSION['id_usuario'];

// Obtener el monto del departamento y el ID del usuario vendedor
$query = "SELECT precio, estado, id_usuario FROM departamento WHERE id_departamento = :id_departamento";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id_departamento', $idDepartamento, PDO::PARAM_INT);
$stmt->execute();
$departamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$departamento) {
    die('ERROR: Departamento no encontrado.');
}
if($idUsuario==$departamento.$idUsuario){
    die('ERROR: No puedes comprar tu propio departamento cojudo hpta');
}


$monto = $departamento['precio'];
$idVendedor = $departamento['id_usuario'];

try {
    $pdo->beginTransaction();

    // Verificar si el método de pago pertenece al usuario autenticado
    $query = "SELECT id_modo FROM modos_pago WHERE id_modo = :id_modo AND id_usuario = :id_usuario";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'id_modo' => $idModoPago,
        'id_usuario' => $idUsuario
    ]);
    $modoPago = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$modoPago) {
        throw new Exception('El método de pago no pertenece al usuario autenticado.');
    }

    // Registrar el pago en la tabla pago
    $query = "INSERT INTO pago (id_modo, fecha_pago, estado_pago, monto) 
              VALUES (:id_modo, NOW(), 'en proceso', :monto)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'id_modo' => $idModoPago,
        'monto' => $monto
    ]);
    $idPago = $pdo->lastInsertId();

    // Registrar la compra en la tabla compra
    $query = "INSERT INTO compra (id_pago, id_departamento, fecha_compra, estado_compra) 
              VALUES (:id_pago, :id_departamento, NOW(), 'Proceso')";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'id_pago' => $idPago,
        'id_departamento' => $idDepartamento
    ]);

    // Actualizar el estado del departamento
    $query = "UPDATE departamento SET estado = 'Proceso_compra' WHERE id_departamento = :id_departamento";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id_departamento' => $idDepartamento]);

    // Agregar una notificación para el vendedor
    $query = "INSERT INTO notificacion (id_usuario, mensaje, fecha_notificacion) 
              VALUES (:id_usuario, :mensaje, NOW())";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'id_usuario' => $idVendedor,
        'mensaje' => 'Tu departamento ha sido comprado.'
    ]);

    $pdo->commit();

    echo json_encode(['success' => 'Compra realizada con éxito.']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['error' => 'Error al procesar la compra: ' . $e->getMessage()]);
}
?>