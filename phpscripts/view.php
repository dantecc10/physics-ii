<!DOCTYPE html>
<html lang="es-MX">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de sensores</title>
</head>

<body>
    <div id="table-div">
        <?php
        include "functions.php";
        display_table();
        ?>
    </div>
    <script>
        function fetchData() {
            // Crear un objeto XMLHttpRequest
            var xhr = new XMLHttpRequest();

            // Configurar la solicitud
            xhr.open('GET', 'tabler.php', true);

            // Configurar el manejo de la respuesta
            xhr.onload = function() {
                // Verificar si la solicitud se completó correctamente
                if (xhr.status >= 200 && xhr.status < 300) {
                    // Parsear la respuesta JSON
                    var responseData = document.getElementById("table-div").innerHTML = xhr.responseText;
                    // Llamar al callback con los datos obtenidos
                } else {
                    // Manejar errores
                    console.error('Error al hacer la solicitud:', xhr.statusText);
                }
            };
            xhr.onerror = function() {
                console.error('Error de conexión.');
            };
            xhr.send();
        }

        setInterval(function() {
            fetchData();
        }, 2000); // 5000 milisegundos (5 segundos)
    </script>
</body>

</html>