<?php
// Iniciar sesión (si no está iniciada)
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    // Si el usuario no está autenticado, redirigirlo al formulario de inicio de sesión
    header("Location: ../../HTML/login.html");
    exit();
}

// Verificar si es la primera vez que el usuario inicia sesión
if (!isset($_SESSION['primera_vez_login'])) {
    // Si es la primera vez, mostrar el código de seguridad y marcar la bandera de sesión
    $_SESSION['primera_vez_login'] = true;

    // Obtener el código de seguridad del usuario desde la base de datos
    require_once '../Auth/db.php';
    $idUsuario = $_SESSION['id_usuario'];
    $stmt = $pdo->prepare("SELECT codigo_seguridad FROM usuarios WHERE id_usuario = :id_usuario");
    $stmt->execute(['id_usuario' => $idUsuario]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $codigo_seguridad = $user['codigo_seguridad'];

    // Mostrar el código de seguridad
    echo "¡Bienvenido! Tu código de seguridad es: $codigo_seguridad";
}

// Obtener departamentos en proceso desde la base de datos
require_once '../Auth/db.php';
$stmt = $pdo->prepare("SELECT * FROM departamento WHERE estado = 'en proceso'");
$stmt->execute();
$departamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener notificaciones del usuario
$idUsuario = $_SESSION['id_usuario'];
$query = "SELECT * FROM notificacion WHERE id_usuario = :id_usuario ORDER BY fecha_notificacion DESC";
$stmt = $pdo->prepare($query);
$stmt->execute(['id_usuario' => $idUsuario]);
$notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Inicio</title>
    <link rel="stylesheet" href="../../Css/main.css">

</head>

<body>
    
    <header>
    <h2>Bienvenido a la Página de Inicio</h2>

    <button>
        <a href="vender.php">Vender Ahora</a>
    </button>
    <button>
        <a href="dashboard.php">Todos mis anuncios</a>
    </button>
    <button>
        <a href="perfil.php">Mi Perfil</a>
    </button>
    <button onclick="mostrarNotificaciones()">Ver Notificaciones</button>
      <!-- Botón de Cerrar Sesión -->
      <form action="../../HTML/login.html" method="POST">
        <input class="btn" type="submit" value="Cerrar Sesión">
    </form>
    </header>
    

  

    <!-- Formulario de búsqueda y filtros -->
    <form id="filtrosForm">
        <input type="text" id="buscar" placeholder="Buscar departamento..." onkeyup="filtrarDepartamentos()">
        <select id="filtroPrecio" onchange="filtrarDepartamentos()">
            <option value="">Todos los Precios</option>
            <option value="100000">Hasta $100,000</option>
            <option value="200000">Hasta $200,000</option>
            <option value="300000">Hasta $300,000</option>
            <option value="400000">Hasta $400,000</option>
            <option value="500000">Hasta $500,000</option>
        </select>
        <select id="filtroHabitaciones" onchange="filtrarDepartamentos()">
            <option value="">Todas las Habitaciones</option>
            <option value="1">1 Habitación</option>
            <option value="2">2 Habitaciones</option>
            <option value="3">3 Habitaciones</option>
            <option value="4">4 Habitaciones</option>
            <option value="5">5 o más Habitaciones</option>
        </select>
        <select id="filtroBanos" onchange="filtrarDepartamentos()">
            <option value="">Todos los Baños</option>
            <option value="1">1 Baño</option>
            <option value="2">2 Baños</option>
            <option value="3">3 Baños</option>
            <option value="4">4 o más Baños</option>
        </select>
        <select id="filtroGaraje" onchange="filtrarDepartamentos()">
            <option value="">¿Tiene Garaje?</option>
            <option value="1">Sí</option>
            <option value="0">No</option>
        </select>
    </form>

    <section>
        <h3>Lista de Departamentos:</h3>
        <div id="listaDepartamentos">
    <?php if (!empty($departamentos)) : ?>
        <?php foreach ($departamentos as $departamento) : ?>
            <div class="departamento-container">
                <div class="departamento" data-precio="<?php echo $departamento['precio']; ?>" data-habitaciones="<?php echo $departamento['habitaciones']; ?>" data-banos="<?php echo $departamento['banos']; ?>" data-garaje="<?php echo $departamento['garaje']; ?>">
                    <h4><?php echo htmlspecialchars($departamento['nombre']); ?></h4>
                    <?php $rutaImagen = obtenerRutaImagen($departamento['imagen_seccion_1']); ?>
                    <img src="<?php echo htmlspecialchars($rutaImagen); ?>" alt="Imagen del departamento" class="departamento-imagen">
                    <p>$<?php echo htmlspecialchars($departamento['precio']); ?></p>
                    <p><?php echo htmlspecialchars($departamento['habitaciones']); ?> Habitaciones</p>
                    <p><?php echo htmlspecialchars($departamento['banos']); ?> Baños</p>
                    <p><?php echo htmlspecialchars($departamento['garaje']) ? 'Tiene Garaje' : 'No Tiene Garaje'; ?></p>
                    <button><a href="comprar.php?id=<?php echo $departamento['id_departamento']; ?>">Comprar</a></button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p>No hay departamentos en proceso actualmente.</p>
    <?php endif; ?>
</div>

    </section>






    <script>
        function mostrarNotificaciones() {
            const notificaciones = <?= json_encode($notificaciones) ?>;
            if (notificaciones.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Sin notificaciones',
                    text: 'No tienes nuevas notificaciones.',
                });
                return;
            }

            let notificacionesHtml = '<ul>';
            notificaciones.forEach(notificacion => {
                notificacionesHtml += `<li>${notificacion.mensaje} - ${new Date(notificacion.fecha_notificacion).toLocaleString()}</li>`;
            });
            notificacionesHtml += '</ul>';

            Swal.fire({
                title: 'Tus Notificaciones',
                html: notificacionesHtml,
                width: 600,
                padding: '3em',
            });
        }



        function filtrarDepartamentos() {
    const buscar = document.getElementById('buscar').value.toLowerCase();
    const filtroPrecio = document.getElementById('filtroPrecio').value;
    const filtroHabitaciones = document.getElementById('filtroHabitaciones').value;
    const filtroBanos = document.getElementById('filtroBanos').value;
    const filtroGaraje = document.getElementById('filtroGaraje').value;

    const listaDepartamentos = document.getElementById('listaDepartamentos');
    const departamentos = listaDepartamentos.querySelectorAll('.departamento-container');

    const departamentosVisibles = [];

    departamentos.forEach((departamento) => {
        const datosDepartamento = departamento.querySelector('.departamento');
        const nombre = datosDepartamento.querySelector('h4').innerText.toLowerCase();
        const precio = datosDepartamento.getAttribute('data-precio');
        const habitaciones = datosDepartamento.getAttribute('data-habitaciones');
        const banos = datosDepartamento.getAttribute('data-banos');
        const garaje = datosDepartamento.getAttribute('data-garaje');

        let mostrar = true;

        if (buscar && !nombre.includes(buscar)) {
            mostrar = false;
        }

        if (filtroPrecio && parseInt(precio) > parseInt(filtroPrecio)) {
            mostrar = false;
        }

        if (filtroHabitaciones && parseInt(habitaciones) !== parseInt(filtroHabitaciones)) {
            mostrar = false;
        }

        if (filtroBanos && parseInt(banos) !== parseInt(filtroBanos)) {
            mostrar = false;
        }

        if (filtroGaraje && parseInt(garaje) !== parseInt(filtroGaraje)) {
            mostrar = false;
        }

        if (mostrar) {
            departamentosVisibles.push(departamento);
        }

        departamento.style.display = mostrar ? 'block' : 'none';
    });

    // Limpiar el contenedor y agregar los departamentos visibles de nuevo en orden
    while (listaDepartamentos.firstChild) {
        listaDepartamentos.removeChild(listaDepartamentos.firstChild);
    }

    departamentosVisibles.forEach((departamento) => {
        listaDepartamentos.appendChild(departamento);
    });
}

    </script>
</body>

</html>