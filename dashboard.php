<?php
session_start();
include("config/db.php");

if (!isset($_SESSION['user'])) {
    header("Location: auth/login.php");
    exit();
}

// Fetch stats
$total = $conn->query("SELECT COUNT(*) as t FROM tasks")->fetch_assoc()['t'];
$pend = $conn->query("SELECT COUNT(*) as t FROM tasks WHERE status='Pendiente'")->fetch_assoc()['t'];
$prog = $conn->query("SELECT COUNT(*) as t FROM tasks WHERE status='En progreso'")->fetch_assoc()['t'];
$comp = $conn->query("SELECT COUNT(*) as t FROM tasks WHERE status='Completada'")->fetch_assoc()['t'];

// Fetch latest tasks
$result = $conn->query("SELECT * FROM tasks ORDER BY created_at DESC, id DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Load Feather Icons (lightweight alternative to FontAwesome) -->
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
            <a href="tasks/add_task.php" class="btn btn-primary me-2">
                <i data-feather="plus" class="me-1"></i> Nueva Tarea
            </a>
            <button type="button" class="btn btn-danger btn-icon" data-bs-toggle="modal" data-bs-target="#logoutModal" title="Cerrar Sesión">
                <i data-feather="log-out"></i>
            </button>
        </div>
    </div>
</nav>

<div class="container mt-5">

    <!-- Bento Grid for Stats -->
    <div class="bento-grid">
        <div class="glass-panel stat-card stat-total">
            <div class="stat-value"><?php echo $total; ?></div>
            <div class="stat-label">Total</div>
        </div>
        <div class="glass-panel stat-card stat-pendiente">
            <div class="stat-value"><?php echo $pend; ?></div>
            <div class="stat-label">Pendientes</div>
        </div>
        <div class="glass-panel stat-card stat-progreso">
            <div class="stat-value"><?php echo $prog; ?></div>
            <div class="stat-label">En progreso</div>
        </div>
        <div class="glass-panel stat-card stat-completada">
            <div class="stat-value"><?php echo $comp; ?></div>
            <div class="stat-label">Completadas</div>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="glass-panel-md table-container mt-4 mb-5">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="20%">Título</th>
                        <th width="25%">Descripción</th>
                        <th width="12%">Estado</th>
                        <th width="12%">Prioridad</th>
                        <th width="15%">Creada</th>
                        <th width="11%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $i = 1; while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="text-muted"><?php echo $i++; ?></td>
                            <td class="task-title">
                                <?php echo htmlspecialchars($row['title']); ?>
                            </td>
                            <td class="task-desc">
                                <?php echo htmlspecialchars($row['description']); ?>
                            </td>
                            <td>
                                <?php 
                                    $statusClass = strtolower(str_replace(' ', '', $row['status'] ?? 'pendiente'));
                                ?>
                                <span class="badge-custom badge-status-<?php echo $statusClass; ?>">
                                    <?php echo $row['status'] ?: 'Pendiente'; ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                    $priorityClass = strtolower($row['priority'] ?? 'baja');
                                ?>
                                <span class="badge-custom badge-priority-<?php echo $priorityClass; ?>">
                                    <?php echo $row['priority'] ?: 'Baja'; ?>
                                </span>
                            </td>
                            <td class="text-muted small">
                                <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                            </td>
                            <td>
                                <a href="tasks/edit_task.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-icon btn-sm me-1" title="Editar">
                                    <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-icon btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" data-taskid="<?php echo $row['id']; ?>" title="Eliminar">
                                    <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                No hay tareas por mostrar. ¡Empieza creando una!
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-white" style="background: rgba(17, 24, 39, 0.95); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px;">
      <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
        <h5 class="modal-title d-flex align-items-center" id="deleteModalLabel">
            <i data-feather="alert-triangle" class="text-danger me-2"></i> Confirmar Eliminación
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="color: #9CA3AF;">
        ¿Estás completamente seguro de que deseas eliminar permanentemente esta tarea? Esta acción no se puede deshacer.
      </div>
      <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.04);">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Sí, eliminar</a>
      </div>
    </div>
  </div>
</div>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-white" style="background: rgba(17, 24, 39, 0.95); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.08); border-radius: 16px;">
      <div class="modal-header" style="border-bottom: 1px solid rgba(255,255,255,0.04);">
        <h5 class="modal-title d-flex align-items-center" id="logoutModalLabel">
            <i data-feather="log-out" class="text-danger me-2"></i> Cerrar Sesión
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="color: #9CA3AF;">
        ¿Estás seguro que deseas cerrar sesión y salir del sistema?
      </div>
      <div class="modal-footer" style="border-top: 1px solid rgba(255,255,255,0.04);">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <a href="auth/logout.php" id="confirmLogoutBtn" class="btn btn-danger">Sí, cerrar sesión</a>
      </div>
    </div>
  </div>
</div>

<script>
  feather.replace()
  
  // Script to pass ID to modal
  const deleteModal = document.getElementById('deleteModal');
  if (deleteModal) {
      deleteModal.addEventListener('show.bs.modal', function (event) {
          const button = event.relatedTarget;
          const taskId = button.getAttribute('data-taskid');
          const confirmBtn = document.getElementById('confirmDeleteBtn');
          confirmBtn.href = 'tasks/delete_task.php?id=' + taskId;
      });
  }
</script>

</body>
</html>