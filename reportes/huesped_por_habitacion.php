<?php
require_once('../includes/conexion.php');
require_once('../includes/funciones.php');

// Obtener habitaciones para el select
$query_habitaciones = "SELECT id_habitacion, numero FROM habitaciones ORDER BY numero";
$resultado_habitaciones = mysqli_query($conexion, $query_habitaciones);

// Procesar filtro
$id_habitacion = isset($_GET['id_habitacion']) ? intval($_GET['id_habitacion']) : 0;

if ($id_habitacion > 0) {
    // Obtener información de la habitación seleccionada
    $query_habitacion = "SELECT numero, tipo FROM habitaciones WHERE id_habitacion = $id_habitacion";
    $resultado_habitacion = mysqli_query($conexion, $query_habitacion);
    $habitacion = mysqli_fetch_assoc($resultado_habitacion);
   
    $query = "SELECT h.id_huesped, h.documento, h.nombre, h.apellido,
                     r.fecha_entrada, r.fecha_salida, r.estado, r.total
              FROM reservas r
              JOIN huespedes h ON r.id_huesped = h.id_huesped
              WHERE r.id_habitacion = $id_habitacion
              ORDER BY r.fecha_entrada DESC";
    $resultado = mysqli_query($conexion, $query);
}

include('../includes/header.php');
?>

<div class="card">
    <h2>Huéspedes por Habitación</h2>

    <form method="get" action="">
        <div class="form-group">
            <label for="id_habitacion">Seleccionar Habitación:</label>
            <select name="id_habitacion" id="id_habitacion" class="form-control" required>
                <option value="">-- Seleccione una habitación --</option>
                <?php while ($habitacion_select = mysqli_fetch_assoc($resultado_habitaciones)): ?>
                <option value="<?php echo $habitacion_select['id_habitacion']; ?>" <?php echo ($habitacion_select['id_habitacion'] == $id_habitacion) ? 'selected' : ''; ?>>
                    <?php echo 'Habitación ' . $habitacion_select['numero']; ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <input type="submit" value="Generar Reporte" class="btn btn-submit">
    </form>

    <?php if ($id_habitacion > 0 && isset($resultado)): ?>
    <div class="reporte-container">
        <h3>Habitación: <?php echo 'Número ' . $habitacion['numero'] . ' (' . $habitacion['tipo'] . ')'; ?></h3>
        <p>Total de huéspedes registrados: <?php echo mysqli_num_rows($resultado); ?></p>

        <table>
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Fecha Entrada</th>
                    <th>Fecha Salida</th>
                    <th>Estado</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($resultado) > 0): ?>
                    <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                    <tr>
                        <td><?php echo $fila['documento']; ?></td>
                        <td><?php echo $fila['nombre']; ?></td>
                        <td><?php echo $fila['apellido']; ?></td>
                        <td><?php echo $fila['fecha_entrada']; ?></td>
                        <td><?php echo $fila['fecha_salida']; ?></td>
                        <td><?php echo $fila['estado']; ?></td>
                        <td><?php echo number_format($fila['total'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No hay huéspedes registrados en esta habitación.</td>
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
if (isset($resultado_habitacion)) mysqli_free_result($resultado_habitacion);
mysqli_free_result($resultado_habitaciones);
cerrar_conexion($conexion);
?>