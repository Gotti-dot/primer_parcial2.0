<?php
require_once('../includes/conexion.php');
require_once('../includes/funciones.php');

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
        $query = "INSERT INTO huespedes (nombre, apellido, documento, nacionalidad, telefono, email)
                  VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conexion, $query);
        mysqli_stmt_bind_param($stmt, "ssssss", $nombre, $apellido, $documento, $nacionalidad, $telefono, $email);
        
        if (mysqli_stmt_execute($stmt)) {
            redireccionar("listar.php?exito=agregar");
        } else {
            $errores[] = "Error al agregar huésped: " . mysqli_error($conexion);
        }

        mysqli_stmt_close($stmt);
    }
}

include('../includes/header.php');
?>

<div class="form-container">
    <h2>Agregar Nuevo Huésped</h2>

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
            <input type="text" id="nombre" name="nombre" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="apellido">Apellido:</label>
            <input type="text" id="apellido" name="apellido" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="documento">Documento:</label>
            <input type="text" id="documento" name="documento" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="nacionalidad">Nacionalidad:</label>
            <input type="text" id="nacionalidad" name="nacionalidad" class="form-control">
        </div>

        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" class="form-control">
        </div>

        <div class="form-group">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <input type="submit" value="Guardar Huésped" class="btn btn-submit">
            <a href="listar.php" class="btn btn-volver">Volver al Listado</a>
        </div>
    </form>
</div>

<?php
include('../includes/footer.php');
cerrar_conexion($conexion);
?>