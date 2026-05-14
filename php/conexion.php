<?php
// Comprobamos si se ha iniciado sesion
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define ('servidor', 'localhost');
define ('usuario', 'root');
define ('password', '');
define ('baseDeDatos', 'dream_colors');

$con = new mysqli (servidor, usuario, password, baseDeDatos);

if ($con->connect_error) {
    die("Error de conexión: " . $con->connect_error); // Para si falla la conexion
}

$con->set_charset("utf8mb4"); //Para que no haya problemas con tildes y las ñ
?>