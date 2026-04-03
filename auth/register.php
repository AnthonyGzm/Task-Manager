<?php
include("../config/db.php");

$success = false;
if ($_POST) {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $error = "Debe completar todos los campos.";
    } else {
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $password);
        
        if ($stmt->execute()) {
            $success = true;
        } else {
            $error = "El usuario ya existe o hubo un error.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body>

<div class="auth-wrapper">
    <div class="auth-box glass-panel text-center">
        
        <i data-feather="check-square" class="app-logo-icon"></i>
        <h4 class="mb-4 text-white">Task Manager</h4>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger p-2 mb-4"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="alert alert-success p-2 mb-4">Registro exitoso. Puedes iniciar sesión.</div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3 text-start">
                <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
            </div>
            
            <div class="mb-4 text-start">
                <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3">Registrarse</button>
        </form>

        <p class="text-muted small mt-4">
            ¿Ya tienes cuenta? <a href="login.php" class="text-primary text-decoration-none">Inicia sesión</a>
        </p>

    </div>
</div>

<script>
  feather.replace()
</script>

</body>
</html>