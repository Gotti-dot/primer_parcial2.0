<?php
function formatearFecha($fecha) {
    return date('d/m/Y', strtotime($fecha));
}


function sanitizar($dato) {
    global $conexion;
    return mysqli_real_escape_string($conexion, trim($dato));
}


function redireccionar($url) {
    header("Location: $url");
    exit();
}

?>
