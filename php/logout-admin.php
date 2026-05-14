<?php
require_once 'conexion.php';

session_destroy();
header("Location: login-admin.php");
exit();
