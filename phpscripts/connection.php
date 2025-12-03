<?php
include_once "secrets.php";
$credentials = generatePasskey('sql');
$connection = new mysqli("localhost", $credentials[0], $credentials[1], $credentials[2]);
if ($connection->connect_error) {
    die('Error : (' . $connection->connect_error . ')');
}