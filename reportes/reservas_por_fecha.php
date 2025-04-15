<?php
require_once('../includes/conexion.php');
require_once('../includes/funciones.php');

// Obtener fechas de entrada distintas para el filtro
$query_periodos = "SELECT DISTINCT fecha_entrada FROM reservas ORDER BY fecha_entrada DESC";
$resultado_periodos = mysqli_query($conexion, $query_periodos);

// Procesar filtro
$fecha_entrada = isset($_GET['fecha_entrada']) ? sanitizar($_GET['fecha_entrada']) : '';

if (!empty($fecha_entrada)) {
    $query = "SELECT r.id_reserva, r.fecha_entrada, r.fecha_salida, r.estado, r.total,
                     h.id_huesped, h.documento, h.nombre as nombre_huesped, h.apellido as apellido_huesped,
                     ha.id_habitacion, ha.numero as numero_habitacion, ha.tipo
              FROM reservas r
              JOIN huespedes h ON r.id_huesped = h.id_huesped
              JOIN habitaciones ha ON r.id_habitacion = ha.id_habitacion
              WHERE r.fecha_entrada = '$fecha_entrada'
              ORDER BY h.apellido, h.nombre, ha.numero";
    $resultado = mysqli_query($conexion, $query);
}

include('../includes/header.php');
?>

<div class="card">
    <h2>Reservas por Fecha de Entrada</h2>

    <form method="get" action="">
        <div class="form-group">
            <label for="fecha_entrada">Seleccionar Fecha de Entrada:</label>
            <select name="fecha_entrada" id="fecha_entrada" class="form-control" required>
                <option value="">-- Seleccione una fecha --</option>
                <?php while ($periodo_select = mysqli_fetch_assoc($resultado_periodos)): ?>
                <option value="<?php echo $periodo_select['fecha_entrada']; ?>" <?php echo ($periodo_select['fecha_entrada'] == $fecha_entrada) ? 'selected' : ''; ?>>
                    <?php echo $periodo_select['fecha_entrada']; ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <input type="submit" value="Generar Reporte" class="btn btn-submit">
    </form>

    <?php if (!empty($fecha_entrada) && isset($resultado)): ?>
    <div class="reporte-container">
        <h3>Fecha de Entrada: <?php echo $fecha_entrada; ?></h3>
        <p>Total de reservas: <?php echo mysqli_num_rows($resultado); ?></p>

        <table>
            <thead>
                <tr>
                    <th>Huésped</th>
                    <th>Habitación</th>
                    <th>Tipo</th>
                    <th>Fecha Salida</th>
                    <th>Estado</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($resultado) > 0): ?>
                    <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                    <tr>
                        <td><?php echo $fila['apellido_huesped'] . ', ' . $fila['nombre_huesped'] . ' (' . $fila['documento'] . ')'; ?></td>
                        <td><?php echo 'Habitación ' . $fila['numero_habitacion']; ?></td>
                        <td><?php echo $fila['tipo']; ?></td>
                        <td><?php echo $fila['fecha_salida']; ?></td>
                        <td><?php echo $fila['estado']; ?></td>
                        <td><?php echo number_format($fila['total'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No hay reservas registradas para esta fecha.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php
include('../includes/footer.php');
if (isset($resultado)) mysqli_free_result($resultado);
mysqli_free_result($resultado_periodos);
cerrar_conexion($conexion);
?>