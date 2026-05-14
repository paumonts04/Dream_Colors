<?php
require_once 'conexion.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: panel.php");
    exit();
}

$error_login = "";

if (isset($_POST['ingresar'])) {
    $user = $_POST['usuario'] ?? '';
    $pass = $_POST['password'] ?? '';

    $stmt = $con->prepare("SELECT id, nombre, password FROM usuarios WHERE nombre = ? AND rol = 1");

    if ($stmt) {
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $datos = $result->fetch_assoc();

            if (password_verify($pass, $datos['password'])) {
                $_SESSION['admin_id'] = $datos['id'];
                $_SESSION['admin_user'] = $datos['nombre'];
                $stmt->close();
                header("Location: panel.php");
                exit();
            }
        }

        $stmt->close();
    }

    $error_login = "Usuario o contrasena incorrectos.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrador - Dream Colors</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="login-body">
    <div class="form-wrapper">
        <div class="form-container">
            <h2>Administrador</h2>
            <p>Acceso al panel de administración</p>

            <?php if ($error_login): ?>
                <div class="form-alert form-alert-error">
                    <?= htmlspecialchars($error_login) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <input type="text" id="usuario" name="usuario" required>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" name="ingresar" class="btn-submit">Acceder</button>
            </form>
        </div>
    </div>
</body>
</html>
