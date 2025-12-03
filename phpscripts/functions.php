<?php
function sql_fetch_fields($table, $fields, $id, $custom_query)
{
    include_once "connection.php";

    if ($custom_query != "" && $custom_query != null) {
        $query = $custom_query;
    } else {
        if ($id == "" or $id == null) {
            $query = "SELECT * FROM `$table`";
        } else {
            $query_field = ($fields[0]);
            $query = "SELECT * FROM `$table` WHERE `$query_field` = '$id'";
        }
    }

    $result = mysqli_query($connection, $query) or die("Error en la consulta a la base de datos");
    $data = array();

    // Comprobar si las filas son mayores que 0
    $result = $connection->query($query);
    // Verificar si se encontró un usuario válido
    if ((stripos($query, "UPDATE") === false) && (stripos($query, "INSERT") === false)) {
        if ($result->num_rows > 0) {
            $i = 0;
            // Hacer fetch a los datos
            while ($row = $result->fetch_array()) {
                // Procesar cada registro obtenido
                $n = sizeof($fields);
                for ($j = 0; $j < $n; $j++) {
                    if ($id == "" or $id == null) {
                        // Procesar cada columna de cada registro 
                        $data[$i][$j] = $row[$fields[$j]];
                    } else {
                        // Procesar cada columna de cada registro 
                        $data[$j] = $row[$fields[$j]];
                    }
                }
                $i++;
            }
            return $data;
        }
    } else {
        // No hay registros en la tabla, o la consulta hizo una actualización: devolver null
        $connection->close();
        return null;
    }
}

function update_vals($data, $sensor)
{
    $target = 1;
    if (isset($sensor)) {
        $target = $sensor;
    }
    $sql = "UPDATE `esp32_sensors` SET `temp_sensor` = $data[0], `humidity_sensor` = $data[1] WHERE `id_sensor` = $target";
    include_once "connection.php";
    $result = mysqli_query($connection, $sql) or die("Error en la consulta a la base de datos");
    $connection->close();
    return ($result) ? true : false;
}

function display_table()
{
    include_once "connection.php";

    // Hacer la consulta a la tabla esp32_sensors
    $sql = "SELECT * FROM `esp32_sensors`";
    $result = $connection->query($sql);

    if ($result->num_rows > 0) {
        // Construir la tabla HTML
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Nombre del Sensor</th><th>Temperatura (°C)</th><th>Humedad (%)</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id_sensor"] . "</td>";
            echo "<td>" . $row["name_sensor"] . "</td>";
            echo "<td class='container temp-container'>" . $row["temp_sensor"] . "</td>";
            echo "<td class='container hum-container'>" . $row["humidity_sensor"] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "0 results";
    }

    // Cerrar la conexión
    $connection->close();
}
