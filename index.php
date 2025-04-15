<?php
require_once('includes/conexion.php');
require_once('includes/funciones.php');

include('includes/header.php');
?>

<div class="card">
    <h2>Bienvenido al Sistema de Gestión Hotelera</h2>

    <p>Este sistema permite administrar huéspedes, habitaciones y reservas.</p>

    <div class="stats-container">
        <div class="stat-card">
            <h3>Huéspedes Registrados</h3>
            <?php
            $query = "SELECT COUNT(*) as total FROM huespedes";
            $resultado = mysqli_query($conexion, $query);
            $total = mysqli_fetch_assoc($resultado)['total'];
            mysqli_free_result($resultado);
            ?>
            <p class="stat-number"><?php echo $total; ?></p>
            <a href="huespedes/listar.php" class="btn btn-editar">Ver Huéspedes</a>
        </div>

        <div class="stat-card">
            <h3>Habitaciones Disponibles</h3>
            <?php
            $query = "SELECT COUNT(*) as total FROM habitaciones WHERE estado = 'disponible'";
            $resultado = mysqli_query($conexion, $query);
            $total = mysqli_fetch_assoc($resultado)['total'];
            mysqli_free_result($resultado);
            ?>
            <p class="stat-number"><?php echo $total; ?></p>
            <a href="habitaciones/listar.php" class="btn btn-editar">Ver Habitaciones</a>
        </div>

        <div class="stat-card">
            <h3>Reservas Activas</h3>
            <?php
            $query = "SELECT COUNT(*) as total FROM reservas WHERE estado = 'confirmada'";
            $resultado = mysqli_query($conexion, $query);
            $total = mysqli_fetch_assoc($resultado)['total'];
            mysqli_free_result($resultado);
            ?>
            <p class="stat-number"><?php echo $total; ?></p>
            <a href="reservas/listar.php" class="btn btn-editar">Ver Reservas</a>
        </div>
    </div>

    <div class="recent-container">
        <h3>Últimas Reservas Registradas</h3>
        <?php
        $query = "SELECT r.fecha_entrada, r.fecha_salida, r.estado, r.total,
                         h.nombre as nombre_huesped, h.apellido as apellido_huesped,
                         ha.numero as numero_habitacion
                  FROM reservas r
                  JOIN huespedes h ON r.id_huesped = h.id_huesped
                  JOIN habitaciones ha ON r.id_habitacion = ha.id_habitacion
                  ORDER BY r.fecha_entrada DESC
                  LIMIT 5";
        $resultado = mysqli_query($conexion, $query);
        ?>

        <table>
            <thead>
                <tr>
                    <th>Huésped</th>
                    <th>Habitación</th>
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
                        <td><?php echo $fila['apellido_huesped'] . ', ' . $fila['nombre_huesped']; ?></td>
                        <td><?php echo $fila['numero_habitacion']; ?></td>
                        <td><?php echo $fila['fecha_entrada']; ?></td>
                        <td><?php echo $fila['fecha_salida']; ?></td>
                        <td><?php echo $fila['estado']; ?></td>
                        <td><?php echo number_format($fila['total'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No hay reservas registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include('includes/footer.php');
mysqli_free_result($resultado);
cerrar_conexion($conexion);
?>