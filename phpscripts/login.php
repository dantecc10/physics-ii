<?php
session_start();
session_destroy();
session_start();
include "connection.php";

if ($_POST['email'] == "demo_user@system.com") {
    $connection = new mysqli("localhost", "comercial_demo", $data[1], "comercial_demo");
}

$username = mysqli_real_escape_string($connection, $_POST['email']);
$password = mysqli_real_escape_string($connection, $_POST['password']); //Recepción de variables que pasan por filtro anti explits SQL

$sql = "SELECT * FROM `administrators` WHERE (`email_administrator` = '$username') AND (`password_administrator` = '$password') AND (`status_administrator` = 1)";
$result = $connection->query($sql);

// Verificar si se encontró un usuario válido
if ($result->num_rows > 0) {
    // Acceso concedido, redireccionar a la página de inicio del sitio web
    if ($info = $result->fetch_object()) { //Asignación y configuración de variables de sesión en arreglo de PHP
        $_SESSION['loged_in'] = true;
        $_SESSION['id'] = $info->id_administrator;
        $_SESSION['name'] = $info->name_administrator;
        $_SESSION['lastNames'] = $info->last_names_administrator;
        //$_SESSION['user'] = $info->username_administrator;
        $_SESSION['img'] = $info->icon_img_administrator;
        $_SESSION['email'] = $info->email_administrator;
    }
    $connection->close();

    if (isset($_POST['redirect'])) {
        header("Location: ../" . $_POST['redirect']);
    } else {
        header("Location: ../index.php");
    }
} else {
    // Acceso denegado, mostrar un mensaje de error y redireccionar a la página de inicio de sesión
    //echo "Nombre de usuario o contraseña incorrectos"; # Mensaje de debug
    $connection->close();
    header("Location: ../login.php?error=wrong-credentials");
}
$connection->close();

// Cerrar la conexión a la base de datos
?>