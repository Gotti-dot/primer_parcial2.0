<?php
require_once('../includes/conexion.php');
require_once('../includes/funciones.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redireccionar("listar.php");
}

$id_huesped = intval($_GET['id']);

// Obtener datos del huésped con una consulta segura
$query = "SELECT * FROM huespedes WHERE id_huesped = ?";
$stmt = mysqli_prepare($conexion, $query);
mysqli_stmt_bind_param($stmt, "i", $id_huesped);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$huesped = mysqli_fetch_assoc($resultado);

if (!$huesped) {
    redireccionar("listar.php");
}

mysqli_stmt_close($stmt);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = sanitizar($_POST['nombre']);
    $apellido = sanitizar($_POST['apellido']);
    $documento = sanitizar($_POST['documento']);
    $nacionalidad = sanitizar($_POST['nacionalidad']);
    $telefono = sanitizar($_POST['telefono']);
    $email = sanitizar($_POST['email']);

    // Validar campos obligatorios
    $errores = array();

    if (empty($nombre)) $errores[] = "El nombre es obligatorio";
    if (empty($apellido)) $errores[] = "El apellido es obligatorio";
    if (empty($documento)) $errores[] = "El documento es obligatorio";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = "El correo electrónico no es válido";

    if (empty($errores)) {
        $query = "UPDATE huespedes SET
                  nombre = ?, apellido = ?, documento = ?, nacionalidad = ?, telefono = ?, email = ?
                  WHERE id_huesped = ?";

        $stmt = mysqli_prepare($conexion, $query);
        mysqli_stmt_bind_param($stmt, "ssssssi", $nombre, $apellido, $documento, $nacionalidad, $telefono, $email, $id_huesped);

        if (mysqli_stmt_execute($stmt)) {
            redireccionar("listar.php?exito=editar");
        } else {
            $errores[] = "Error al actualizar huésped: " . mysqli_error($conexion);
        }

        mysqli_stmt_close($stmt);
    }
}

include('../includes/header.php');
?>

<div class="form-container">
    <h2>Editar Huésped</h2>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" class="form-control"
                   value="<?php echo htmlspecialchars($huesped['nombre']); ?>" required>
        </div>

        <div class="form-group">
            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" class="form-control"
                   value="<?php echo htmlspecialchars($huesped['apellido']); ?>" required>
        </div>

        <div class="form-group">
            <label for="documento">Documento:</label>
            <input type="text" id="documento" name="documento" class="form-control"
                   value="<?php echo htmlspecialchars($huesped['documento']); ?>" required>
        </div>

        <div class="form-group">
            <label for="nacionalidad">Nacionalidad:</label>
            <input type="text" id="nacionalidad" name="nacionalidad" class="form-control"
                   value="<?php echo htmlspecialchars($huesped['nacionalidad']); ?>">
        </div>

        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" class="form-control"
                   value="<?php echo htmlspecialchars($huesped['telefono']); ?>">
        </div>

        <div class="form-group">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" class="form-control"
                   value="<?php echo htmlspecialchars($huesped['email']); ?>" required>
        </div>

        <div class="form-group">
            <input type="submit" value="Actualizar Huésped" class="btn btn-submit">
            <a href="listar.php" class="btn btn-volver">Volver al Listado</a>
        </div>
    </form>
</div>

<?php
include('../includes/footer.php');
mysqli_free_result($resultado);
cerrar_conexion($conexion);
?>