<?php
require_once('../includes/conexion.php');
require_once('../includes/funciones.php');

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
        $query = "INSERT INTO reservas (id_huesped, id_habitacion, fecha_entrada, fecha_salida, estado, total)
                  VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conexion, $query);
        mysqli_stmt_bind_param($stmt, "iisssd", $id_huesped, $id_habitacion, $fecha_entrada, $fecha_salida, $estado, $total);

        if (mysqli_stmt_execute($stmt)) {
            redireccionar("listar.php?exito=agregar");
        } else {
            $errores[] = "Error al agregar reserva: " . mysqli_error($conexion);
        }

        mysqli_stmt_close($stmt);
    }
}

include('../includes/header.php');
?>

<div class="form-container">
    <h2>Agregar Nueva Reserva</h2>

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
            <input type="number" id="id_huesped" name="id_huesped" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="id_habitacion">ID Habitación:</label>
            <input type="number" id="id_habitacion" name="id_habitacion" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="fecha_entrada">Fecha de Entrada:</label>
            <input type="date" id="fecha_entrada" name="fecha_entrada" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="fecha_salida">Fecha de Salida:</label>
            <input type="date" id="fecha_salida" name="fecha_salida" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="estado">Estado:</label>
            <select id="estado" name="estado" class="form-control" required>
                <option value="confirmada">Confirmada</option>
                <option value="cancelada">Cancelada</option>
                <option value="finalizada">Finalizada</option>
            </select>
        </div>

        <div class="form-group">
            <label for="total">Total:</label>
            <input type="number" step="0.01" id="total" name="total" class="form-control">
        </div>

        <div class="form-group">
            <input type="submit" value="Guardar Reserva" class="btn btn-submit">
            <a href="listar.php" class="btn btn-volver">Volver al Listado</a>
        </div>
    </form>
</div>

<?php
include('../includes/footer.php');
cerrar_conexion($conexion);
?>