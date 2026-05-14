<?php
require_once 'conexion.php';

// if (isset($_SESSION['usuario_id'])) {
//     header("Location: ../index.php");
//     exit();
// }

$errores = [];
$nombre = '';
$apellidos = '';
$email = '';
$telefono = '';

if (isset($_POST['registrar'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $telefono = trim($_POST['telefono'] ?? '');

    if ($nombre === '') {
        $errores[] = "El nombre de usuario no puede estar vacio.";
    } elseif (strlen($nombre) < 2 || strlen($nombre) > 40) {
        $errores[] = "El nombre debe tener entre 2 y 40 caracteres.";
    }

    if ($apellidos === '') {
        $errores[] = "Introduce tus apellidos.";
    } elseif (strlen($apellidos) < 2 || strlen($apellidos) > 50) {
        $errores[] = "Los apellidos deben tener entre 2 y 50 caracteres.";
    }

    if ($email === '') {
        $errores[] = "Introduce tu correo electronico.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electronico no es valido.";
    }

    if ($telefono === '') {
        $errores[] = "Introduce tu telefono.";
    } elseif (strlen($telefono) < 9) {
        $errores[] = "El telefono debe tener al menos 9 caracteres.";
    }

    if (strlen($pass) < 6) {
        $errores[] = "La contrasena debe tener al menos 6 caracteres.";
    }

    if (empty($errores)) {
        $stmtCheck = $con->prepare("SELECT id FROM usuarios WHERE nombre = ? OR email = ?");

        if ($stmtCheck) {
            $stmtCheck->bind_param("ss", $nombre, $email);
            $stmtCheck->execute();
            $resultadoCheck = $stmtCheck->get_result();

            if ($resultadoCheck && $resultadoCheck->num_rows > 0) {
                $errores[] = "Ya existe un usuario con ese nombre o correo.";
            }

            $stmtCheck->close();
        } else {
            $errores[] = "No se pudo validar si el usuario ya existe.";
        }
    }

    if (empty($errores)) {
        $passHashed = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $con->prepare("INSERT INTO usuarios (telefono, nombre, apellidos, email, password) VALUES (?, ?, ?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param("sssss", $telefono, $nombre, $apellidos, $email, $passHashed);

            if ($stmt->execute()) {
                $stmt->close();
                header("Location: login.php?registro=ok");
                exit();
            }

            $errores[] = "Error al guardar el registro.";
            $stmt->close();
        } else {
            $errores[] = "Error en el sistema de registro.";
        }
    }
}

$nombresExistentes = [];
$result = $con->query("SELECT nombre FROM usuarios");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $nombresExistentes[] = $row['nombre'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Dream Colors</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="registro-body">
    <div class="form-wrapper">
        <div class="form-container">
            <h2>Crea tu cuenta</h2>
            <p>Registrate para acceder a tu espacio en Dream Colors.</p>

            <?php if (!empty($errores)): ?>
                <div class="form-alert form-alert-error">
                    <strong>Revisa esto:</strong>
                    <ul>
                        <?php foreach ($errores as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="registro.php" method="POST">
                <div class="form-group">
                    <label for="nombre">Usuario</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" placeholder="Tu usuario..." required>
                </div>

                <div class="form-group">
                    <label for="apellidos">Apellidos</label>
                    <input type="text" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($apellidos); ?>" placeholder="Tus apellidos..." required>
                </div>

                <div class="form-group">
                    <label for="email">Correo electronico</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="tucorreo@ejemplo.com" required>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Crea una contrasena segura" required>
                </div>

                <div class="form-group">
                    <label for="telefono">Telefono</label>
                    <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>" placeholder="Tu telefono..." required>
                </div>

                <button type="submit" name="registrar" class="btn-submit">Crear cuenta</button>
            </form>

            <div class="form-footer" style="margin-top: 25px; text-align: center; border-top: 1px solid #eee; padding-top: 15px;">
                <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesion</a></p>
                <p style="margin-top: 8px;"><a href="../index.php">← Volver a la pagina principal</a></p>
            </div>
        </div>
    </div>

    <script src="../js/registro.js"></script>
</body>
</html>
