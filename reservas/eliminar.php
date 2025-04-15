<?php
require_once('../includes/conexion.php');
require_once('../includes/funciones.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redireccionar("listar.php");
}

$id_reserva = intval($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $query = "DELETE FROM reservas WHERE id_reserva = $id_reserva";

    if (mysqli_query($conexion, $query)) {
        redireccionar("listar.php?exito=eliminar");
    } else {
        redireccionar("listar.php?error=eliminar");
    }
}

include('../includes/header.php');
?>

<div class="form-container">
    <h2>Eliminar Reserva</h2>

    <p>¿Está seguro que desea eliminar esta reserva? Esta acción no se puede deshacer.</p>

    <form method="post" action="">
        <div class="form-group">
            <input type="submit" value="Confirmar Eliminación" class="btn btn-eliminar">
            <a href="listar.php" class="btn btn-volver">Cancelar</a>
        </div>
    </form>
</div>

<?php
include('../includes/footer.php');
cerrar_conexion($conexion);
?>