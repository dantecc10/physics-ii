<?php
var_dump($_POST);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include "functions.php";
    // Recoger los datos enviados en el cuerpo de la solicitud POST
    $temperature = isset($_POST['temperature']) ? $_POST['temperature'] : 'No data';
    $humidity = isset($_POST['humidity']) ? $_POST['humidity'] : 'No data';

    $temperature = floatval($temperature);
    $humidity = floatval($humidity);

    // Actualizar los valores en la base de datos
    echo (update_vals([$temperature, $humidity], 1)) ? "success" : "error";

    // Imprimir los datos (o hacer algo con ellos)
    //echo "Temperature: " . htmlspecialchars($temperature) . " °C<br>";
    //echo "Humidity: " . htmlspecialchars($humidity) . " %<br>";
} else {
    echo "No POST data received.";
}