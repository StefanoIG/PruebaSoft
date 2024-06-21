<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../Html/login.html");
    exit();
}

require_once '../Auth/db.php'; // Ajusta la ruta según sea necesario

$idUsuario = $_SESSION['id_usuario'];

// Consultar la información del usuario
$query = "SELECT * FROM usuarios WHERE id_usuario = :id_usuario";
$stmt = $pdo->prepare($query);
$stmt->execute(['id_usuario' => $idUsuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "Error al obtener la información del usuario.";
    exit();
}

// Consultar las tarjetas del usuario
$query_tarjetas = "SELECT * FROM tarjeta WHERE id_modo IN (SELECT id_modo FROM modos_pago WHERE id_usuario = :id_usuario)";
$stmt_tarjetas = $pdo->prepare($query_tarjetas);
$stmt_tarjetas->execute(['id_usuario' => $idUsuario]);
$tarjetas = $stmt_tarjetas->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<head>
    <meta charset="UTF-8">
    <title>Perfil del Usuario</title>
    <!-- Estilos y scripts necesarios -->
    <style>
        .widget-pago {
            width: 300px;
            height: 300px;
            border: 2px solid #000;
            padding: 20px;
            box-sizing: border-box;
            margin: 20px auto;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .widget-pago h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .tarjeta {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
            display: flex;
            align-items: center;
        }

        .tarjeta img {
            width: 40px;
            height: 25px;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <h1>Perfil del Usuario</h1>
    <p>Nombre: <input type="text" id="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" disabled></p>
    <p>Email: <input type="text" id="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" disabled></p>
    <button id="editarPerfil">Editar</button>
    <button id="guardarPerfil" style="display: none;">Guardar</button>

    <h2>Actualizar Contraseña</h2>
    <p>Contraseña actual: <input type="password" id="contrasena_actual"></p>
    <p>Nueva contraseña: <input type="password" id="nueva_contrasena"></p>
    <button id="actualizarContrasena">Actualizar Contraseña</button>

    <!-- Widget de métodos de pago (simulado por ahora) -->
    <div class="widget-pago">
        <h2>Métodos de Pago</h2>
        <div class="container-tarjeta">
            <p>Tarjeta de Crédito/Débito</p>
            <!-- Tarjetas actuales -->
            <div class="container-tarjeta_registradas">
            <?php foreach ($tarjetas as $tarjeta): ?>
                    <div class="tarjeta">
                        <img src="tarjeta.png" alt="Tarjeta">
                        <span>Tarjeta XXXX - <?php echo htmlspecialchars($tarjeta['ultimos_cuatro_digitos']); ?></span>
                    </div>
                <?php endforeach; ?>

            </div>
            <button onclick="solicitarDatosTarjeta()">Agregar Tarjeta</button>        
        </div>
       
    </div>

    <!-- Añadir más métodos de pago según estén disponibles -->
</body>

<script>
document.getElementById('editarPerfil').addEventListener('click', function() {
    document.getElementById('nombre').disabled = false;
    document.getElementById('email').disabled = false;
    document.getElementById('editarPerfil').style.display = 'none';
    document.getElementById('guardarPerfil').style.display = 'inline';
});

document.getElementById('guardarPerfil').addEventListener('click', function() {
    const nombre = document.getElementById('nombre').value;
    const email = document.getElementById('email').value;

    fetch('procesar_perfil.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({nombre: nombre, email: email})
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            Swal.fire('Error', data.error, 'error');
        } else {
            Swal.fire('Éxito', data.success, 'success');
            document.getElementById('nombre').disabled = true;
            document.getElementById('email').disabled = true;
            document.getElementById('editarPerfil').style.display = 'inline';
            document.getElementById('guardarPerfil').style.display = 'none';
        }
    })
    .catch((error) => {
        console.error('Error:', error);
        Swal.fire('Error', 'Hubo un problema al actualizar el perfil.', 'error');
    });
});

document.getElementById('actualizarContrasena').addEventListener('click', function() {
    const contrasena_actual = document.getElementById('contrasena_actual').value;
    const nueva_contrasena = document.getElementById('nueva_contrasena').value;

    fetch('actualizar_contrasena.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({contrasena_actual: contrasena_actual, nueva_contrasena: nueva_contrasena})
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            Swal.fire('Error', data.error, 'error');
        } else {
            Swal.fire('Éxito', data.success, 'success');
        }
    })
    .catch((error) => {
        console.error('Error:', error);
        Swal.fire('Error', 'Hubo un problema al actualizar la contraseña.', 'error');
    });
});

function solicitarDatosTarjeta() {
    Swal.fire({
        title: 'Agregar nueva tarjeta',
        html:
            '<input id="swal-input1" class="swal2-input" placeholder="Número de Tarjeta">' +
            '<input id="swal-input2" class="swal2-input" placeholder="Nombre del Titular">' +
            '<input id="swal-input3" class="swal2-input" placeholder="Fecha de Vencimiento (MM/AA)">' +
            '<input id="swal-input4" class="swal2-input" placeholder="CVC">',
        focusConfirm: false,
        preConfirm: () => {
            return [
                document.getElementById('swal-input1').value,
                document.getElementById('swal-input2').value,
                document.getElementById('swal-input3').value,
                document.getElementById('swal-input4').value
            ]
        }
    }).then((result) => {
        if (result.value) {
            let tarjeta = result.value;
            console.log(tarjeta); // Muestra los datos en la consola

            // Enviar tarjeta a procesar_tarjeta.php
            fetch('procesar_tarjeta.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(tarjeta)
            })
            .then(response => response.json())
            .then(data => {
                console.log(data); // Mostrar la respuesta del servidor en la consola
                if (data.error) {
                    Swal.fire('Error', data.error, 'error');
                } else {
                    Swal.fire('Éxito', data.success, 'success');
                }
            })
            .catch((error) => {
                console.error('Error:', error); // Mostrar errores, si los hay
                Swal.fire('Error', 'Hubo un problema al procesar la tarjeta.', 'error');
            });
        }
    });
}
</script>

</html>
