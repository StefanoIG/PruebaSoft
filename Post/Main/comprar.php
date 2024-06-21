<?php
session_start(); // Iniciar sesión

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    // Si el usuario no está autenticado, redirigirlo al formulario de inicio de sesión
    header("Location: ../../Html/login.html");
    exit();
}

require_once '../Auth/db.php'; // Conexión a la base de datos

// Obtener el ID del departamento desde la URL
$idDepartamento = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Falta el ID del departamento.');

// Consulta para obtener la información del departamento
$query = "SELECT * FROM departamento WHERE id_departamento = :id_departamento";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id_departamento', $idDepartamento, PDO::PARAM_INT);
$stmt->execute();

$departamento = $stmt->fetch(PDO::FETCH_ASSOC);

function obtenerRutaImagen($nombreImagen) {
    switch ($nombreImagen) {
        case 'imagen1.jpg':
            return "../../images/1.webp";
        case 'imagen2.jpg':
            return "../../images/2.jpeg";
        case 'imagen3.jpg':
            return "../../images/3.webp";
        case 'imagen4.jpg':
            return "../../images/4.jpg";
        case 'imagen5.jpg':
            return "../../images/5.jpg";
        case 'imagen6.jpg':
            return "../../images/6.jpg";
        case 'imagen7.jpg':
            return "../../images/7.jpg";
        case 'imagen8.jpg':
            return "../../images/8.jpg";
        case 'imagen9.jpg':
            return "../../images/9.jpeg";
        default:
            return "../../images/1.webp";
    }
}

// Obtener métodos de pago del usuario
$idUsuario = $_SESSION['id_usuario'];
$query = "SELECT id_modo, metodo FROM modos_pago WHERE id_usuario = :id_usuario";
$stmt = $pdo->prepare($query);
$stmt->execute(['id_usuario' => $idUsuario]);
$metodosPago = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Información del Departamento</title>
    <style>
        .imagenes-departamento img {
            width: 300px;
            height: 400px;
            margin-right: 10px;
        }
    </style>
    <!-- Incluir SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php if ($departamento): ?>
    <?php
    // Obtener las rutas de las imágenes
    $rutaImagenSeccion1 = obtenerRutaImagen($departamento['imagen_seccion_1']);
    $rutaImagenSeccion2 = obtenerRutaImagen($departamento['imagen_seccion_2']);
    $rutaImagenSeccion3 = obtenerRutaImagen($departamento['imagen_seccion_3']);
    ?>
    <h2>Información del Departamento</h2>
    <p>Nombre: <?= htmlspecialchars($departamento['nombre']) ?></p>
    <p>Descripción: <?= htmlspecialchars($departamento['descripcion']) ?></p>
    <p>Precio: $<?= htmlspecialchars($departamento['precio']) ?></p>
    <p>Garaje: <?= htmlspecialchars($departamento['garaje']) ?></p>
    <p>Baños: <?= htmlspecialchars($departamento['bano']) ?></p>
    <p>Cocina: <?= htmlspecialchars($departamento['cocina']) ?></p>
    <p>Habitaciones: <?= htmlspecialchars($departamento['habitaciones']) ?></p>

    <div class="imagenes-departamento">
        <h3>Imágenes del Departamento</h3>
        <img src="<?= htmlspecialchars($rutaImagenSeccion1) ?>" alt="Imagen Sección 1">
        <img src="<?= htmlspecialchars($rutaImagenSeccion2) ?>" alt="Imagen Sección 2">
        <img src="<?= htmlspecialchars($rutaImagenSeccion3) ?>" alt="Imagen Sección 3">
    </div>

    <button onclick="mostrarMetodosPago()">
        Realizar Compra
    </button>

    <script>
        function mostrarMetodosPago() {
            // Obtener métodos de pago
            const metodosPago = <?= json_encode($metodosPago) ?>;
            
            if (metodosPago.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No tiene métodos de pago',
                    text: 'Por favor registre un método de pago antes de continuar.',
                });
                return;
            }

            let opciones = '';
            metodosPago.forEach(metodo => {
                opciones += `<option value="${metodo.id_modo}">${metodo.metodo}</option>`;
            });

            Swal.fire({
                title: 'Seleccione un método de pago',
                html: `<select id="metodoPago" class="swal2-input">${opciones}</select>`,
                focusConfirm: false,
                preConfirm: () => {
                    const idModo = Swal.getPopup().querySelector('#metodoPago').value;
                    if (!idModo) {
                        Swal.showValidationMessage(`Por favor seleccione un método de pago`);
                    }
                    return { idModo: idModo };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const idModo = result.value.idModo;
                    window.location.href = `procesar_compra.php?id_departamento=<?= $idDepartamento ?>&id_modo=${idModo}`;
                }
            });
        }
    </script>
<?php else: ?>
    <p>El departamento solicitado no existe.</p>
<?php endif; ?>
</body>
</html>
