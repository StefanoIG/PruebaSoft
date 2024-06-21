<?php
session_start(); // Iniciar sesión

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    // Si el usuario no está autenticado, redirigirlo al formulario de inicio de sesión
    header("Location: ../../Html/login.html");
    exit();
}

// Asumiendo que ya tienes una conexión a la base de datos establecida en $pdo
require_once '../Auth/db.php'; // Asegúrate de que este archivo contiene la conexión a tu base de datos

$userId = $_SESSION['id_usuario']; // Asumiendo que el ID del usuario se almacena así en la sesión

// Consulta para obtener los departamentos del usuario
$query = "SELECT * FROM departamento WHERE id_usuario = :id_usuario";
$stmt = $pdo->prepare($query);
$stmt->execute(['id_usuario' => $userId]);
$departamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Función para obtener la ruta de la imagen correcta basada en la información almacenada
function obtenerRutaImagen($nombreImagen)
{
    switch ($nombreImagen) {
        case 'imagen1.jpg':
            return "../../images/1.webp";
        case 'imagen2.jpg':
            return "../../images/2.jpeg";
        case 'imagen3.jpg':
            return "../../images/3.webp";
        default:
            return "../../images/1.webp";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Departamentos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        h2 {
            text-align: center;
            padding: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .en-proceso {
            background-color: #ffffe0;
        }
        .finalizado {
            background-color: #d3d3d3;
        }
        .aceptado {
            background-color: #e0ffff;
        }
        .proceso-compra {
            background-color: #ffe4e1;
        }
        .baja {
            background-color: rgba(211, 211, 211, 0.5); /* Gris transparente */
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
            margin-right: 5px; /* Añadir espacio entre botones */
        }
        button:hover {
            background-color: #0056b3;
        }
        .estado-icono {
            font-size: 20px;
            margin-left: 10px;
        }
        .no-departamentos {
            text-align: center;
            margin-top: 20px;
            font-size: 1.2em;
            color: #dc3545;
        }
        img {
            max-width: 100px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Departamentos Publicados:</h2>
        <?php if (!empty($departamentos)): ?>
            <table>
                <tr>
                    <th>Foto</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
                <?php foreach ($departamentos as $departamento): 
                    $rutaImagen = obtenerRutaImagen($departamento['imagen_seccion_1']); ?>
                    <tr class="<?php echo htmlspecialchars($departamento['estado']); ?>">
                        <td><img src="<?php echo htmlspecialchars($rutaImagen); ?>" alt="Imagen del departamento"></td>
                        <td><?php echo htmlspecialchars($departamento['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($departamento['descripcion']); ?></td>
                        <td>$<?php echo htmlspecialchars($departamento['precio']); ?></td>
                        <td>
                            <?php switch ($departamento['estado']) {
                                case 'en proceso': ?>
                                    <button onclick="editarDepartamento(<?php echo htmlspecialchars($departamento['id_departamento']); ?>)">Editar</button>
                                    <button onclick="bajarDepartamento(<?php echo htmlspecialchars($departamento['id_departamento']); ?>)">Dar de baja</button>
                                    <?php break;
                                case 'finalizado':
                                    // No se necesita botón ni ícono adicional para finalizado
                                    break;
                                case 'aceptado': ?>
                                    <span class="estado-icono">&#9203;</span> <!-- Icono de espera (reloj de arena) -->
                                    <?php break;
                                case 'proceso_compra': ?>
                                    <button onclick="confirmarCompra(<?php echo htmlspecialchars($departamento['id_departamento']); ?>)">Confirmar Compra</button>
                                    <?php break;
                                case 'baja': ?>
                                    <button onclick="rehabilitarDepartamento(<?php echo htmlspecialchars($departamento['id_departamento']); ?>)">Rehabilitar</button>
                                    <button onclick="borrarDepartamento(<?php echo htmlspecialchars($departamento['id_departamento']); ?>)">Borrar</button>
                                    <?php break;
                            } ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p class="no-departamentos">No hay departamentos para mostrar.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function editarDepartamento(id) {
            window.location.href = 'editar_departamento.php?id=' + id;
        }
        // Función para dar de baja departamento
        function bajarDepartamento(id) {
            if (confirm('¿Estás seguro de que deseas dar de baja este departamento?')) {
                window.location.href = 'procesar_baja.php?id=' + id;
            }
        }
        // Función para rehabilitar departamento
        function rehabilitarDepartamento(id) {
            window.location.href = 'procesar_rehabilitar.php?id=' + id;
        }
        // Función para borrar departamento
        function borrarDepartamento(id) {
            if (confirm('¿Estás seguro de que deseas borrar este departamento?')) {
                window.location.href = 'procesar_borrar.php?id=' + id;
            }
        }
        // Función para confirmar compra de departamento
        function confirmarCompra(id) {
            window.location.href = 'confirmar.php?id=' + id;
        }
    </script>
</body>
</html>
