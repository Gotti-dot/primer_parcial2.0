<?php
require_once('../includes/conexion.php');
require_once('../includes/funciones.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redireccionar("listar.php");
}

$id_habitacion = intval($_GET['id']);

// Obtener datos de la habitación con una consulta segura
$query = "SELECT * FROM habitaciones WHERE id_habitacion = ?";
$stmt = mysqli_prepare($conexion, $query);
mysqli_stmt_bind_param($stmt, "i", $id_habitacion);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$habitacion = mysqli_fetch_assoc($resultado);

if (!$habitacion) {
    redireccionar("listar.php");
}

mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $numero = sanitizar($_POST['numero']);
    $tipo = sanitizar($_POST['tipo']);
    $precio_noche = floatval($_POST['precio_noche']);
    $descripcion = sanitizar($_POST['descripcion']);
    $estado = sanitizar($_POST['estado']);

    // Validar campos obligatorios
    $errores = array();

    if (empty($numero)) $errores[] = "El número de habitación es obligatorio";
    if (!in_array($tipo, ['individual', 'doble', 'suite'])) $errores[] = "El tipo de habitación no es válido";
    if ($precio_noche <= 0) $errores[] = "El precio por noche debe ser mayor a cero";
    if (!in_array($estado, ['disponible', 'ocupada', 'mantenimiento'])) $errores[] = "El estado de la habitación no es válido";

    if (empty($errores)) {
        $query = "UPDATE habitaciones SET
                  numero = ?, tipo = ?, precio_noche = ?, descripcion = ?, estado = ?
                  WHERE id_habitacion = ?";

        $stmt = mysqli_prepare($conexion, $query);
        mysqli_stmt_bind_param($stmt, "ssdssi", $numero, $tipo, $precio_noche, $descripcion, $estado, $id_habitacion);

        if (mysqli_stmt_execute($stmt)) {
            redireccionar("listar.php?exito=editar");
        } else {
            $errores[] = "Error al actualizar la habitación: " . mysqli_error($conexion);
        }

        mysqli_stmt_close($stmt);
    }
}

include('../includes/header.php');
?>

<div class="form-container">
    <h2>Editar Habitación</h2>

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
            <label for="numero">Número de Habitación:</label>
            <input type="text" id="numero" name="numero" class="form-control"
                   value="<?php echo htmlspecialchars($habitacion['numero']); ?>" required>
        </div>

        <div class="form-group">
            <label for="tipo">Tipo de Habitación:</label>
            <select id="tipo" name="tipo" class="form-control" required>
                <option value="individual" <?php echo ($habitacion['tipo'] == 'individual') ? 'selected' : ''; ?>>Individual</option>
                <option value="doble" <?php echo ($habitacion['tipo'] == 'doble') ? 'selected' : ''; ?>>Doble</option>
                <option value="suite" <?php echo ($habitacion['tipo'] == 'suite') ? 'selected' : ''; ?>>Suite</option>
            </select>
        </div>

        <div class="form-group">
            <label for="precio_noche">Precio por Noche:</label>
            <input type="number" step="0.01" id="precio_noche" name="precio_noche" class="form-control"
                   value="<?php echo htmlspecialchars($habitacion['precio_noche']); ?>" required>
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" class="form-control"><?php echo htmlspecialchars($habitacion['descripcion']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="estado">Estado:</label>
            <select id="estado" name="estado" class="form-control" required>
                <option value="disponible" <?php echo ($habitacion['estado'] == 'disponible') ? 'selected' : ''; ?>>Disponible</option>
                <option value="ocupada" <?php echo ($habitacion['estado'] == 'ocupada') ? 'selected' : ''; ?>>Ocupada</option>
                <option value="mantenimiento" <?php echo ($habitacion['estado'] == 'mantenimiento') ? 'selected' : ''; ?>>Mantenimiento</option>
            </select>
        </div>

        <div class="form-group">
            <input type="submit" value="Actualizar Habitación" class="btn btn-submit">
            <a href="listar.php" class="btn btn-volver">Volver al Listado</a>
        </div>
    </form>
</div>

<?php
include('../includes/footer.php');
mysqli_free_result($resultado);
cerrar_conexion($conexion);
?>