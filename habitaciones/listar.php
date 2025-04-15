<?php
require_once('../includes/conexion.php');
require_once('../includes/funciones.php');

// Consulta para obtener habitaciones
$query = "SELECT * FROM habitaciones ORDER BY numero";
$resultado = mysqli_query($conexion, $query);

include('../includes/header.php');
?>

<div class="card">
    <h2>Listado de Habitaciones</h2>

    <a href="agregar.php" class="btn btn-agregar">Agregar Habitación</a>

    <?php if (isset($_GET['exito'])): ?>
        <div class="alert alert-success">
            <?php
            if ($_GET['exito'] == 'agregar') echo "Habitación agregada correctamente.";
            elseif ($_GET['exito'] == 'editar') echo "Habitación actualizada correctamente.";
            elseif ($_GET['exito'] == 'eliminar') echo "Habitación eliminada correctamente.";
            ?>
        </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Número</th>
                <th>Tipo</th>
                <th>Precio por Noche</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($resultado) > 0): ?>
                <?php while ($habitacion = mysqli_fetch_assoc($resultado)): ?>
                <tr>
                    <td><?php echo $habitacion['id_habitacion']; ?></td>
                    <td><?php echo $habitacion['numero']; ?></td>
                    <td><?php echo $habitacion['tipo']; ?></td>
                    <td><?php echo $habitacion['precio_noche']; ?></td>
                    <td><?php echo htmlspecialchars($habitacion['descripcion']); ?></td>
                    <td><?php echo $habitacion['estado']; ?></td>
                    <td>
                        <a href="editar.php?id=<?php echo $habitacion['id_habitacion']; ?>" class="btn btn-editar">Editar</a>
                        <a href="eliminar.php?id=<?php echo $habitacion['id_habitacion']; ?>" class="btn btn-eliminar" onclick="return confirm('¿Está seguro de eliminar esta habitación?')">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No hay habitaciones registradas.</td>
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