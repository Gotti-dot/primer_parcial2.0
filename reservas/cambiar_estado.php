<?php
require_once('../includes/conexion.php');
require_once('../includes/funciones.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: listar.php");
    exit();
}

$id_reserva = intval($_GET['id']);

// Obtener datos de la reserva
$query_reserva = "SELECT r.*, h.nombre AS nombre_huesped, h.apellido AS apellido_huesped,
                         ha.numero AS numero_habitacion, ha.tipo AS tipo_habitacion
                  FROM reservas r
                  JOIN huespedes h ON r.id_huesped = h.id_huesped
                  JOIN habitaciones ha ON r.id_habitacion = ha.id_habitacion
                  WHERE r.id_reserva = ?";
$stmt = mysqli_prepare($conexion, $query_reserva);
mysqli_stmt_bind_param($stmt, "i", $id_reserva);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$reserva = mysqli_fetch_assoc($resultado);

if (!$reserva) {
    header("Location: listar.php");
    exit();
}

mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nuevo_estado = sanitizar($_POST['estado']);

    // Validar estado
    $estados_validos = ['confirmada', 'cancelada', 'finalizada'];
    if (!in_array($nuevo_estado, $estados_validos)) {
        $error = "Estado inválido.";
    } else {
        $query_update = "UPDATE reservas SET estado = ? WHERE id_reserva = ?";
        $stmt = mysqli_prepare($conexion, $query_update);
        mysqli_stmt_bind_param($stmt, "si", $nuevo_estado, $id_reserva);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: listar.php?exito=estado_actualizado");
            exit();
        } else {
            $error = "Error al actualizar el estado de la reserva: " . mysqli_error($conexion);
        }

        mysqli_stmt_close($stmt);
    }
}

$estados_disponibles = [
    'confirmada' => 'Confirmada',
    'cancelada' => 'Cancelada',
    'finalizada' => 'Finalizada'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Estado de Reserva</title>
    <link rel="stylesheet" href="../css/estilo.css">
</head>
<body>
    <header>
        <h1>Cambiar Estado de Reserva #<?= htmlspecialchars($reserva['id_reserva']) ?></h1>
    </header>

    <main>
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <div class="card reserva-info">
            <h2>Detalles de la Reserva</h2>
            <p><strong>Huésped:</strong> <?= htmlspecialchars($reserva['apellido_huesped'] . ', ' . $reserva['nombre_huesped']) ?></p>
            <p><strong>Habitación:</strong> Hab. <?= htmlspecialchars($reserva['numero_habitacion']) ?> (<?= ucfirst(htmlspecialchars($reserva['tipo_habitacion'])) ?>)</p>
            <p><strong>Fechas:</strong> <?= htmlspecialchars($reserva['fecha_entrada']) ?> al <?= htmlspecialchars($reserva['fecha_salida']) ?></p>
            <p><strong>Total:</strong> $<?= number_format($reserva['total'], 2) ?></p>
            <p><strong>Estado actual:</strong> <span class="estado-<?= htmlspecialchars($reserva['estado']) ?>"><?= ucfirst(htmlspecialchars($reserva['estado'])) ?></span></p>
        </div>
        
        <div class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label for="estado">Nuevo Estado:</label>
                    <select name="estado" id="estado" class="form-control" required>
                        <?php foreach ($estados_disponibles as $valor => $texto): ?>
                            <option value="<?= htmlspecialchars($valor) ?>" <?= $reserva['estado'] == $valor ? 'selected' : '' ?>>
                                <?= htmlspecialchars($texto) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-agregar">Actualizar Estado</button>
                    <a href="listar.php" class="btn btn-volver">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Sistema de Reservas</p>
    </footer>
</body>
</html>