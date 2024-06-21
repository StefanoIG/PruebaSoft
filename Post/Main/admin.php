<?php
session_start(); // Iniciar sesión

// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    // Si el usuario no está autenticado, redirigirlo al formulario de inicio de sesión
    header("Location: ../../Html/login.html");
    exit();
}

require_once '../Auth/db.php';

// Asumiendo que db.php ya está utilizando PDO para la conexión a la base de datos
// $conexion es ahora una instancia de PDO

// Asumiendo que tienes una tabla llamada 'usuarios'
$query = "SELECT COUNT(*) AS total FROM usuarios";
// Preparar la consulta
$statement = $pdo->prepare($query);
// Ejecutar la consulta
$statement->execute();

// Obtener el resultado
$usuarios = $statement->fetch(PDO::FETCH_ASSOC);

// Si no hay resultados, asegúrate de que $usuarios tenga una estructura válida para evitar errores
if (!$usuarios) {
    $usuarios = ['total' => 0];
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            height: 100vh;
            background-color: #f8f9fa;
            padding: 20px 0;
        }

        .nav-link {
            margin: 10px 0;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .nav-link:hover {
            background-color: #e9ecef;
        }

        .content {
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            margin-left: 20px;
        }

        h2 {
            margin-bottom: 20px;
        }

        .widgets {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            /* Espacio entre widgets */
        }

        .widget {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            flex: 1;
            min-width: calc(25% - 20px);
            /* Ajusta el 25% para columnas de 4 y resta el gap */
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Barra lateral -->
            <div class="col-md-3 sidebar">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-users"></i> Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-chart-line"></i> Historial General de Ventas</a>
                    </li>
                    <!-- Agregar más opciones según sea necesario -->
                     <!-- Agrega un boton que se conecte a logout.php para cerrar sesion -->
                    <li class="nav-item">
                        <a class="nav-link" href="../Auth/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                </ul>
            </div>
            <!-- Contenido principal -->
            <div class="col-md-9">
                <div class="content">
                    <h2>Bienvenido al Panel de Administración</h2>
                    <div class="widgets row">
                        <div class="col-md-3 widget">
                            <h4>Usuarios Registrados</h4>
                            <p>Total: <?php echo $usuarios['total']; ?></p>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap y jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
</body>

</html>