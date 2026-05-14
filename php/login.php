<?php
require_once 'conexion.php';

$redirect = $_GET['redirect'] ?? $_POST['redirect'] ?? '../index.php';

if (preg_match('#^https?://#i', $redirect) || strpos($redirect, '//') === 0) {
    $redirect = '../index.php';
}

if (isset($_SESSION['usuario_id'])) {
    header("Location: " . $redirect);
    exit();
}

$error_login = "";

if (isset($_POST['ingresar'])) {
    $user = $_POST['nombre'] ?? '';
    $pass = $_POST['password'] ?? '';

    $stmt = $con->prepare("SELECT id, nombre, password FROM usuarios WHERE nombre = ?");

    if ($stmt) {
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows > 0) {
            $datos = $resultado->fetch_assoc();

            if (password_verify($pass, $datos['password'])) {
                $_SESSION['usuario_id'] = $datos['id'];
                $_SESSION['nombre'] = $datos['nombre'];

                $stmt->close();
                header("Location: " . $redirect);
                exit();
            }

            $error_login = "Usuario o contrasena incorrectos.";
        } else {
            $error_login = "Usuario o contrasena incorrectos.";
        }

        $stmt->close();
    } else {
        $error_login = "Error en el sistema de acceso.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body class="login-body">

<div class="form-wrapper">
<div class="form-container">

<h2>Acceso a Dream Colors</h2>
<p>Introduce tus credenciales para continuar.</p>

<?php if ($error_login !== ""): ?>
<div style="color: #e63946; background: #fff5f5; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #e63946; font-size: 0.9rem; text-align: center;">
<strong>Error:</strong> <?php echo htmlspecialchars($error_login); ?>
</div>
<?php endif; ?>

<form action="login.php" method="POST">
<input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
<div class="form-group">
<label for="nombre">Nombre de Usuario</label>
<input type="text" id="nombre" name="nombre" placeholder="Tu usuario..." required>
</div>

<div class="form-group">
<label for="password">Contraseña</label>
<input type="password" id="password" name="password" placeholder="Tu contrasena..." required>
</div>

<button type="submit" name="ingresar" class="btn-submit">Entrar al Sistema</button>
</form>

<div class="form-footer" style="margin-top: 25px; text-align: center; border-top: 1px solid #eee; padding-top: 15px;">
<p>¿Eres nuevo? <a href="registro.php" style="color: #1a2a6c; font-weight: bold; text-decoration: none;">Crea una cuenta</a></p>
<p style="margin-top: 8px;"><a href="../index.php" style="color: #888; font-size: 0.85rem;">← Volver a la pagina principal</a></p>
</div>
</div>
</div>

