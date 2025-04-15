<?php
require_once('../includes/conexion.php');
require_once('../includes/funciones.php');

// Obtener huéspedes para el select
$query_huespedes = "SELECT id_huesped, documento, nombre, apellido FROM huespedes ORDER BY apellido, nombre";
$resultado_huespedes = mysqli_query($conexion, $query_huespedes);

// Procesar filtro
$id_huesped = isset($_GET['id_huesped']) ? intval($_GET['id_huesped']) : 0;

if ($id_huesped > 0) {
    // Obtener información del huésped seleccionado
    $query_huesped = "SELECT documento, nombre, apellido FROM huespedes WHERE id_huesped = $id_huesped";
    $resultado_huesped = mysqli_query($conexion, $query_huesped);
    $huesped = mysqli_fetch_assoc($resultado_huesped);

    $query = "SELECT ha.numero, ha.tipo, ha.precio_noche, r.fecha_entrada, r.fecha_salida, r.estado, r.total
              FROM reservas r
              JOIN habitaciones ha ON r.id_habitacion = ha.id_habitacion
              WHERE r.id_huesped = $id_huesped
              ORDER BY r.fecha_entrada DESC";
    $resultado = mysqli_query($conexion, $query);
}

include('../includes/header.php');
?>

<div class="card">
    <h2>Reservas por Huésped</h2>

    <form method="get" action="">
        <div class="form-group">
            <label for="id_huesped">Seleccionar Huésped:</label>
            <select name="id_huesped" id="id_huesped" class="form-control" required>
                <option value="">-- Seleccione un huésped --</option>
                <?php while ($huesped_select = mysqli_fetch_assoc($resultado_huespedes)): ?>
                <option value="<?php echo $huesped_select['id_huesped']; ?>" <?php echo ($huesped_select['id_huesped'] == $id_huesped) ? 'selected' : ''; ?>>
                    <?php echo $huesped_select['apellido'] . ', ' . $huesped_select['nombre'] . ' (' . $huesped_select['documento'] . ')'; ?>
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <input type="submit" value="Generar Reporte" class="btn btn-submit">
    </form>

    <?php if ($id_huesped > 0 && isset($resultado)): ?>
    <div class="reporte-container">
        <h3>Huésped: <?php echo $huesped['apellido'] . ', ' . $huesped['nombre'] . ' (' . $huesped['documento'] . ')'; ?></h3>
        <p>Total de reservas realizadas: <?php echo mysqli_num_rows($resultado); ?></p>

        <table>
            <thead>
                <tr>
                    <th>Habitación</th>
                    <th>Tipo</th>
                    <th>Precio/Noche</th>
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
                        <td><?php echo $fila['numero']; ?></td>
                        <td><?php echo $fila['tipo']; ?></td>
                        <td><?php echo number_format($fila['precio_noche'], 2); ?></td>
                        <td><?php echo $fila['fecha_entrada']; ?></td>
                        <td><?php echo $fila['fecha_salida']; ?></td>
                        <td><?php echo $fila['estado']; ?></td>
                        <td><?php echo number_format($fila['total'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">El huésped no tiene reservas registradas.</td>
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
if (isset($resultado_huesped)) mysqli_free_result($resultado_huesped);
mysqli_free_result($resultado_huespedes);
cerrar_conexion($conexion);
?>