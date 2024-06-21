<!-- no permitas entrar aqui sin iniciar sesion -->
<?php
// Iniciar sesión (si no está iniciada)
session_start();
// Verificar si el usuario está autenticado
if (!isset($_SESSION['id_usuario'])) {
    // Si el usuario no está autenticado, redirigirlo al formulario de inicio de sesión
    header("Location: ../../Html/login.html"); // Cambiar "login.php" por la página de inicio de sesión
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vender Departamento</title>
    <!-- Incluir Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <!-- Incluir Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <style>
        /* Estilo para el mapa */
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
</head>

<body>
    <h2>Vender Departamento</h2>
    <form action="../Actions/procesar_vender.php" method="post" enctype="multipart/form-data">
        <label for="nombre">Nombre del Departamento:</label>
        <input type="text" id="nombre" name="nombre" required><br>

        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion" name="descripcion" required></textarea><br>

        <label for="precio">Precio:</label>
        <input type="number" id="precio" name="precio" step="0.01" required><br>

        <!-- Nuevos campos -->
        <label for="garaje">Garaje:</label>
        <input type="number" id="garaje" name="garaje" required><br>

        <label for="banos">Baño:</label>
        <input type="number" id="banos" name="banos" required><br>

        <label for="cocina">Cocina:</label>
        <input type="number" id="cocina" name="cocina" required><br>

        <label for="habitaciones">Habitaciones:</label>
        <input type="number" id="habitaciones" name="habitaciones" required><br>

        

        <div id="map">
            <div id="floating-input" style="position: absolute; top: 10px; left: 50px; z-index: 1000;">
                <input type="text" id="direccion" placeholder="Ingrese una dirección" style="width: 200px;">
                <button type="button" onclick="buscarDireccion()">Buscar</button>
            </div>
        </div>

        <!-- Sección de imágenes 1 -->
        <h3>Sección 1</h3>
        <div>
            <label>
                <input type="radio" name="imagen_seccion_1" value="imagen1.jpg" required>
                <img src="../../images/1.webp" alt="Imagen 1" style="width: 100px;">
            </label>
            <label>
                <input type="radio" name="imagen_seccion_1" value="imagen2.jpg">
                <img src="../../images/2.jpeg" alt="Imagen 2" style="width: 100px;">
            </label>
            <label>
                <input type="radio" name="imagen_seccion_1" value="imagen3.jpg">
                <img src="../../images/3.webp" alt="Imagen 3" style="width: 100px;">
            </label>
        </div>

        <!-- Sección de imágenes 2 -->
        <h3>Sección 2</h3>
        <div>
            <label>
                <input type="radio" name="imagen_seccion_2" value="imagen3.jpg" required>
                <img src="../../images/4.jpg" alt="Imagen 4" style="width: 100px;">
            </label>
            <label>
                <input type="radio" name="imagen_seccion_2" value="imagen4.jpg">
                <img src="../../images/5.jpg" alt="Imagen 5" style="width: 100px;">
            </label>
            <label>
                <input type="radio" name="imagen_seccion_2" value="imagen4.jpg">
                <img src="../../images/6.jpg" alt="Imagen 6" style="width: 100px;">
            </label>
            <!-- Añadir más imágenes según sea necesario -->
        </div>

        <!-- Sección de imágenes 3 -->
        <h3>Sección 3</h3>
        <div>
            <label>
                <input type="radio" name="imagen_seccion_3" value="imagen5.jpg" required>
                <img src="../../images/7.jpg" alt="Imagen 7" style="width: 100px;">
            </label>
            <label>
                <input type="radio" name="imagen_seccion_3" value="imagen6.jpg">
                <img src="../../images/8.jpg" alt="Imagen 8" style="width: 100px;">
            </label>
            <label>
                <input type="radio" name="imagen_seccion_3" value="imagen6.jpg">
                <img src="../../images/9.jpeg" alt="Imagen 9" style="width: 100px;">
            </label>
            <!-- Añadir más imágenes según sea necesario -->
        </div>


        <input type="submit" value="Publicar Departamento">
        <!-- Campos ocultos para latitud y longitud -->
        <input type="hidden" id="latitud" name="latitud">
        <input type="hidden" id="longitud" name="longitud">
    </form>

    <script>
        var map, marker;

        function updateLocation(lat, lng) {
            if (marker) {
                marker.setLatLng(new L.LatLng(lat, lng));
            } else {
                marker = L.marker([lat, lng], {
                        draggable: true
                    }).addTo(map)
                    .on('dragend', function(e) {
                        document.getElementById('latitud').value = marker.getLatLng().lat;
                        document.getElementById('longitud').value = marker.getLatLng().lng;
                    });
            }
            document.getElementById('latitud').value = lat;
            document.getElementById('longitud').value = lng;
            map.setView(new L.LatLng(lat, lng), 13);
        }

        document.addEventListener("DOMContentLoaded", function() {
            map = L.map('map').setView([40.416775, -3.703790], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    updateLocation(position.coords.latitude, position.coords.longitude);
                }, function() {
                    alert("Error al obtener la ubicación");
                });
            } else {
                alert("Geolocalización no soportada por este navegador.");
            }

            map.on('click', function(e) {
                updateLocation(e.latlng.lat, e.latlng.lng);
            });
        });

        function buscarDireccion() {
            var direccion = document.getElementById('direccion').value;
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${direccion}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        updateLocation(data[0].lat, data[0].lon);
                    } else {
                        alert("Dirección no encontrada");
                    }
                })
                .catch(error => {
                    alert("Error al buscar la dirección");
                    console.error(error);
                });
        }
    </script>
</body>

</html>