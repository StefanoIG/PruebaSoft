<?php
session_start(); // 1. Verificar sesión

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../Html/login.html"); // Redirigir si no está autenticado
    exit();
}

require_once '../Auth/db.php'; // 2. Conexión a la base de datos

$idDepartamento = $_GET['id']; // 3. Obtener el ID del departamento

// 4. Consulta para obtener la información del departamento
$query = "SELECT * FROM departamento WHERE id_departamento = :id_departamento";
$stmt = $pdo->prepare($query);
$stmt->execute(['id_departamento' => $idDepartamento]);
$departamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$departamento) {
    echo "Departamento no encontrado.";
    exit();
}

// 5. Mostrar formulario
?>

<form action="procesar_edicion.php" method="post">
    <input type="hidden" name="id_departamento" value="<?php echo htmlspecialchars($idDepartamento); ?>">
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" value="<?php echo htmlspecialchars($departamento['nombre']); ?>"><br>
    <label for="descripcion">Descripción:</label>
    <textarea name="descripcion"><?php echo htmlspecialchars($departamento['descripcion']); ?></textarea><br>
    <label for="precio">Precio:</label>
    <input type="text" name="precio" value="<?php echo htmlspecialchars($departamento['precio']); ?>"><br>
    <label for="estado">Estado:</label>
    <select name="estado" disabled>
        <option value="en proceso" <?php echo $departamento['estado'] == 'en proceso' ? 'selected' : ''; ?>>En proceso</option>
        <option value="finalizado"  <?php echo $departamento['estado'] == 'finalizado' ? 'selected' : ''; ?>>Finalizado</option>
        <option value="aceptado" <?php echo $departamento['estado'] == 'aceptado' ? 'selected' : ''; ?>>Aceptado</option>
    </select><br>
    <input type="submit" value="Actualizar">
</form>