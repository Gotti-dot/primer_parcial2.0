<?php
require_once('../includes/conexion.php');
require_once('../includes/funciones.php');

// Consulta para obtener reservas con información de huéspedes y habitaciones
$query = "SELECT r.id_reserva, r.fecha_entrada, r.fecha_salida, r.estado, r.total,
                 h.id_huesped, h.documento, h.nombre as nombre_huesped, h.apellido as apellido_huesped,
                 ha.id_habitacion, ha.numero as numero_habitacion, ha.tipo
          FROM reservas r
          JOIN huespedes h ON r.id_huesped = h.id_huesped
          JOIN habitaciones ha ON r.id_habitacion = ha.id_habitacion
          ORDER BY r.fecha_entrada DESC";
$resultado = mysqli_query($conexion, $query);

include('../includes/header.php');
?>

<div class="card">
    <h2>Listado de Reservas</h2>

    <a href="agregar.php" class="btn btn-agregar">Agregar Reserva</a>

    <?php if (isset($_GET['exito'])): ?>
        <div class="alert alert-success">
            <?php
            if ($_GET['exito'] == 'agregar') echo "Reserva agregada correctamente.";
            elseif ($_GET['exito'] == 'editar') echo "Reserva actualizada correctamente.";
            elseif ($_GET['exito'] == 'eliminar') echo "Reserva eliminada correctamente.";
            ?>
        </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Huésped</th>
                <th>Habitación</th>
                <th>Fecha Entrada</th>
                <th>Fecha Salida</th>
                <th>Estado</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($resultado) > 0): ?>
                <?php while ($reserva = mysqli_fetch_assoc($resultado)): ?>
                <tr>
                    <td><?php echo $reserva['id_reserva']; ?></td>
                    <td>
                        <?php echo $reserva['apellido_huesped'] . ', ' . $reserva['nombre_huesped']; ?>
                        <br><small><?php echo $reserva['documento']; ?></small>
                    </td>
                    <td>
                        <?php echo 'Habitación ' . $reserva['numero_habitacion']; ?>
                        <br><small><?php echo $reserva['tipo']; ?></small>
                    </td>
                    <td><?php echo $reserva['fecha_entrada']; ?></td>
                    <td><?php echo $reserva['fecha_salida']; ?></td>
                    <td><?php echo $reserva['estado']; ?></td>
                    <td><?php echo number_format($reserva['total'], 2); ?></td>
                    <td>
                        <a href="editar.php?id=<?php echo $reserva['id_reserva']; ?>" class="btn btn-editar">Editar</a>
                        <a href="cambiar_estado.php?id=<?php echo $reserva['id_reserva']; ?>" class="btn btn-submit">cambiar</a>
                        <a href="eliminar.php?id=<?php echo $reserva['id_reserva']; ?>" class="btn btn-eliminar" onclick="return confirm('¿Está seguro de eliminar esta reserva?')">Eliminar</a>
                        
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No hay reservas registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
include('../includes/footer.php');
mysqli_free_result($resultado);
cerrar_conexion($conexion);
?>