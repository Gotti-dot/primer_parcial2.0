<?php
require_once('../includes/conexion.php');
require_once('../includes/funciones.php');

// Consulta para obtener huéspedes
$query = "SELECT * FROM huespedes ORDER BY apellido, nombre";
$resultado = mysqli_query($conexion, $query);

include('../includes/header.php');
?>

<div class="card">
    <h2>Listado de Huéspedes</h2>

    <a href="agregar.php" class="btn btn-agregar">Agregar Huésped</a>

    <?php if (isset($_GET['exito'])): ?>
        <div class="alert alert-success">
            <?php
            if ($_GET['exito'] == 'agregar') echo "Huésped agregado correctamente.";
            elseif ($_GET['exito'] == 'editar') echo "Huésped actualizado correctamente.";
            elseif ($_GET['exito'] == 'eliminar') echo "Huésped eliminado correctamente.";
            ?>
        </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Documento</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Nacionalidad</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($resultado) > 0): ?>
                <?php while ($huesped = mysqli_fetch_assoc($resultado)): ?>
                <tr>
                    <td><?php echo $huesped['id_huesped']; ?></td>
                    <td><?php echo $huesped['documento']; ?></td>
                    <td><?php echo $huesped['nombre']; ?></td>
                    <td><?php echo $huesped['apellido']; ?></td>
                    <td><?php echo $huesped['nacionalidad']; ?></td>
                    <td><?php echo $huesped['telefono']; ?></td>
                    <td><?php echo $huesped['email']; ?></td>
                    <td>
                        <a href="editar.php?id=<?php echo $huesped['id_huesped']; ?>" class="btn btn-editar">Editar</a>
                        <a href="eliminar.php?id=<?php echo $huesped['id_huesped']; ?>" class="btn btn-eliminar" onclick="return confirm('¿Está seguro de eliminar este huésped?')">Eliminar</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No hay huéspedes registrados.</td>
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