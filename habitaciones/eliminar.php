<?php
require_once('../includes/conexion.php');
require_once('../includes/funciones.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redireccionar("listar.php");
}

$id_habitacion = intval($_GET['id']);

// Verificar si la habitación tiene reservas asociadas
$query_reservas = "SELECT COUNT(*) as total FROM reservas WHERE id_habitacion = $id_habitacion";
$resultado_reservas = mysqli_query($conexion, $query_reservas);
$total_reservas = mysqli_fetch_assoc($resultado_reservas)['total'];

if ($total_reservas > 0) {
    // No se puede eliminar, tiene reservas asociadas
    redireccionar("listar.php?error=reservas");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $query = "DELETE FROM habitaciones WHERE id_habitacion = $id_habitacion";

    if (mysqli_query($conexion, $query)) {
        redireccionar("listar.php?exito=eliminar");
    } else {
        redireccionar("listar.php?error=eliminar");
    }
}

include('../includes/header.php');
?>

<div class="form-container">
    <h2>Eliminar Habitación</h2>

    <?php if ($total_reservas > 0): ?>
        <div class="alert alert-error">
            No se puede eliminar esta habitación porque tiene reservas asociadas.
        </div>
        <a href="listar.php" class="btn btn-volver">Volver al Listado</a>
    <?php else: ?>
        <p>¿Está seguro que desea eliminar esta habitación? Esta acción no se puede deshacer.</p>

        <form method="post" action="">
            <div class="form-group">
                <input type="submit" value="Confirmar Eliminación" class="btn btn-eliminar">
                <a href="listar.php" class="btn btn-volver">Cancelar</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php
include('../includes/footer.php');
mysqli_free_result($resultado_reservas);
cerrar_conexion($conexion);
?>