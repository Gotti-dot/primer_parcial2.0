<?php
require_once('../includes/conexion.php');
require_once('../includes/funciones.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redireccionar("listar.php");
}

$id_reserva = intval($_GET['id']);

// Obtener datos de la reserva con una consulta segura
$query = "SELECT * FROM reservas WHERE id_reserva = ?";
$stmt = mysqli_prepare($conexion, $query);
mysqli_stmt_bind_param($stmt, "i", $id_reserva);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$reserva = mysqli_fetch_assoc($resultado);

if (!$reserva) {
    redireccionar("listar.php");
}

mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_huesped = intval($_POST['id_huesped']);
    $id_habitacion = intval($_POST['id_habitacion']);
    $fecha_entrada = sanitizar($_POST['fecha_entrada']);
    $fecha_salida = sanitizar($_POST['fecha_salida']);
    $estado = sanitizar($_POST['estado']);
    $total = floatval($_POST['total']);

    // Validar campos obligatorios
    $errores = array();

    if ($id_huesped <= 0) $errores[] = "El ID del huésped es obligatorio";
    if ($id_habitacion <= 0) $errores[] = "El ID de la habitación es obligatorio";
    if (empty($fecha_entrada)) $errores[] = "La fecha de entrada es obligatoria";
    if (empty($fecha_salida)) $errores[] = "La fecha de salida es obligatoria";
    if (strtotime($fecha_entrada) >= strtotime($fecha_salida)) $errores[] = "La fecha de salida debe ser posterior a la fecha de entrada";
    if (!in_array($estado, ['confirmada', 'cancelada', 'finalizada'])) $errores[] = "El estado de la reserva no es válido";
    if ($total < 0) $errores[] = "El total de la reserva no puede ser negativo";

    if (empty($errores)) {
        $query = "UPDATE reservas SET id_huesped = ?, id_habitacion = ?, fecha_entrada = ?, fecha_salida = ?, estado = ?, total = ?
                  WHERE id_reserva = ?";

        $stmt = mysqli_prepare($conexion, $query);
        mysqli_stmt_bind_param($stmt, "iisssdi", $id_huesped, $id_habitacion, $fecha_entrada, $fecha_salida, $estado, $total, $id_reserva);

        if (mysqli_stmt_execute($stmt)) {
            redireccionar("listar.php?exito=editar");
        } else {
            $errores[] = "Error al actualizar la reserva: " . mysqli_error($conexion);
        }

        mysqli_stmt_close($stmt);
    }
}

include('../includes/header.php');
?>

<div class="form-container">
    <h2>Editar Reserva</h2>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-group">
            <label for="id_huesped">ID Huésped:</label>
            <input type="number" id="id_huesped" name="id_huesped" class="form-control"
                   value="<?php echo htmlspecialchars($reserva['id_huesped']); ?>" required>
        </div>

        <div class="form-group">
            <label for="id_habitacion">ID Habitación:</label>
            <input type="number" id="id_habitacion" name="id_habitacion" class="form-control"
                   value="<?php echo htmlspecialchars($reserva['id_habitacion']); ?>" required>
        </div>

        <div class="form-group">
            <label for="fecha_entrada">Fecha de Entrada:</label>
            <input type="date" id="fecha_entrada" name="fecha_entrada" class="form-control"
                   value="<?php echo htmlspecialchars($reserva['fecha_entrada']); ?>" required>
        </div>

        <div class="form-group">
            <label for="fecha_salida">Fecha de Salida:</label>
            <input type="date" id="fecha_salida" name="fecha_salida" class="form-control"
                   value="<?php echo htmlspecialchars($reserva['fecha_salida']); ?>" required>
        </div>

        <div class="form-group">
            <label for="estado">Estado:</label>
            <select id="estado" name="estado" class="form-control" required>
                <option value="confirmada" <?php echo ($reserva['estado'] == 'confirmada') ? 'selected' : ''; ?>>Confirmada</option>
                <option value="cancelada" <?php echo ($reserva['estado'] == 'cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                <option value="finalizada" <?php echo ($reserva['estado'] == 'finalizada') ? 'selected' : ''; ?>>Finalizada</option>
            </select>
        </div>

        <div class="form-group">
            <label for="total">Total:</label>
            <input type="number" step="0.01" id="total" name="total" class="form-control"
                   value="<?php echo htmlspecialchars($reserva['total']); ?>">
        </div>

        <div class="form-group">
            <input type="submit" value="Actualizar Reserva" class="btn btn-submit">
            <a href="listar.php" class="btn btn-volver">Volver al Listado</a>
        </div>
    </form>
</div>

<?php
include('../includes/footer.php');
mysqli_free_result($resultado);
cerrar_conexion($conexion);
?>