<?php
session_start(); // Iniciar sesión

header('Content-Type: application/json'); // Asegurar que todas las respuestas sean JSON

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../Html/login.html");
    exit();
}

require_once '../Auth/db.php'; // Asegúrate de que este archivo contiene la conexión a tu base de datos

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener datos del formulario
    $input = json_decode(file_get_contents('php://input'), true);
    $numero_tarjeta = $input[0];
    $nombre_titular = $input[1];
    $fecha_exp = $input[2];
    $cvc = $input[3];
    $id_usuario = $_SESSION['id_usuario'];

    // Validar datos del formulario (simple validación de ejemplo)
    if (empty($numero_tarjeta) || empty($nombre_titular) || empty($fecha_exp) || empty($cvc)) {
        echo json_encode(['error' => 'Todos los campos son obligatorios.']);
        exit();
    }

    // Hashear los datos sensibles
    $hash_numero_tarjeta = password_hash($numero_tarjeta, PASSWORD_DEFAULT);
    $hash_fecha_exp = password_hash($fecha_exp, PASSWORD_DEFAULT);
    $hash_cvc = password_hash($cvc, PASSWORD_DEFAULT);
    $ultimos_cuatro_digitos = substr($numero_tarjeta, -4);


    try {
        // Iniciar la transacción
        $pdo->beginTransaction();

        // Insertar en la tabla modos_pago
        $query_modo = "INSERT INTO modos_pago (id_usuario) VALUES (:id_usuario)";
        $stmt_modo = $pdo->prepare($query_modo);
        $stmt_modo->execute(['id_usuario' => $id_usuario]);

        // Obtener el ID del modo de pago recién insertado
        $id_modo = $pdo->lastInsertId();

        // Insertar en la tabla tarjeta
        $query_tarjeta = "INSERT INTO tarjeta (hash_numero_tarjeta, fecha_exp, hash_cvc, id_modo, ultimos_cuatro_digitos) VALUES (:hash_numero_tarjeta, :fecha_exp, :hash_cvc, :id_modo, :ultimos_cuatro_digitos)";
        $stmt_tarjeta = $pdo->prepare($query_tarjeta);
        $stmt_tarjeta->execute([
            'hash_numero_tarjeta' => $hash_numero_tarjeta,
            'fecha_exp' => $hash_fecha_exp,
            'hash_cvc' => $hash_cvc,
            'id_modo' => $id_modo,
            'ultimos_cuatro_digitos' => $ultimos_cuatro_digitos

        ]);

        // Confirmar la transacción
        $pdo->commit();

        echo json_encode(['success' => 'Tarjeta registrada con éxito.']);
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $pdo->rollBack();
        echo json_encode(['error' => 'Error al registrar la tarjeta: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Método de solicitud no válido.']);
}
?>
