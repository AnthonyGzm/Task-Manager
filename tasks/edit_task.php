<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: ../dashboard.php");
    exit();
}

// Fetch securely using prepared statement
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../dashboard.php");
    exit();
}

$data = $result->fetch_assoc();

if ($_POST) {
    if (empty($_POST['title'])) {
        $error = "Debe completar el título de la tarea.";
    } else {
        $title = $_POST['title'];
        $desc = $_POST['description'] ?? '';
        $status = $_POST['status'] ?? 'Pendiente';
        $priority = $_POST['priority'] ?? 'Media';

        $update_stmt = $conn->prepare("UPDATE tasks SET title=?, description=?, status=?, priority=? WHERE id=?");
        $update_stmt->bind_param("ssssi", $title, $desc, $status, $priority, $id);
        
        if ($update_stmt->execute()) {
            header("Location: ../dashboard.php");
            exit();
        } else {
            $error = "Error al actualizar la tarea.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarea - Task Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body>

<nav class="navbar navbar-custom p-3 sticky-top">
    <div class="container-fluid">
        <span class="navbar-brand">
            <i data-feather="check-square" class="text-primary"></i>
            Task Manager
        </span>
        
        <div>
            <a href="../dashboard.php" class="btn btn-secondary me-2">Volver</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="form-wrapper">
        <div class="glass-panel p-4">
            <h4 class="mb-4 d-flex align-items-center">
                <i data-feather="edit-2" class="me-2 text-warning"></i>
                Editar Tarea #<?php echo htmlspecialchars($data['id']); ?>
            </h4>

            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Título *</label>
                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($data['title']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($data['description'] ?? ''); ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Estado</label>
                        <select name="status" class="form-select">
                            <option value="Pendiente" <?php echo ($data['status'] == 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="En progreso" <?php echo ($data['status'] == 'En progreso') ? 'selected' : ''; ?>>En progreso</option>
                            <option value="Completada" <?php echo ($data['status'] == 'Completada') ? 'selected' : ''; ?>>Completada</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Prioridad</label>
                        <select name="priority" class="form-select">
                            <option value="Alta" <?php echo ($data['priority'] == 'Alta') ? 'selected' : ''; ?>>Alta</option>
                            <option value="Media" <?php echo ($data['priority'] == 'Media') ? 'selected' : ''; ?>>Media</option>
                            <option value="Baja" <?php echo ($data['priority'] == 'Baja') ? 'selected' : ''; ?>>Baja</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                        <i data-feather="refresh-ccw" class="me-2" style="width:16px;"></i>
                        Actualizar Tarea
                    </button>
                    <a href="../dashboard.php" class="btn btn-danger ms-2">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
  feather.replace()
</script>

</body>
</html>