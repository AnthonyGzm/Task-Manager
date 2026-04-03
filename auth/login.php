<?php
session_start();
include("../config/db.php");

if ($_POST) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // This already has a prepared statement, which is good! Just keeping it secure.
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['id'];
            header("Location: ../dashboard.php");
            exit();
        } else {
            $error = "Clave incorrecta";
        }
    } else {
        $error = "Usuario no encontrado";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Task Manager</title>
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

        <form method="POST">
            <div class="mb-3 text-start">
                <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
            </div>
            
            <div class="mb-4 text-start">
                <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3">Iniciar Sesión</button>
        </form>

        <p class="text-muted small mt-4">
            ¿No tienes cuenta? <a href="register.php" class="text-primary text-decoration-none">Regístrate aquí</a>
        </p>

    </div>
</div>

<script>
  feather.replace()
</script>

</body>
</html>