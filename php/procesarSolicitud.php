<?php
session_start();
require_once("conexion.php");

// Verificar si los parámetros necesarios están presentes
if (isset($_GET['accion'], $_GET['solicitud_id'])) {
    $accion = $_GET['accion'];
    $solicitud_id = $_GET['solicitud_id'];

    if ($accion === 'aceptar') {
        // Cambiar el estado de la solicitud a "aceptada"
        $sql = "UPDATE solicitudes SET solicitud_estado = 'aceptada' WHERE idsolicitud = '$solicitud_id'";
    } elseif ($accion === 'rechazar') {
        // Cambiar el estado de la solicitud a "rechazada"
        $sql = "UPDATE solicitudes SET solicitud_estado = 'rechazada' WHERE idsolicitud = '$solicitud_id'";
    }

    mysqli_query($conn, $sql);
}

// Redirigir de vuelta a la página de solicitudes de amistad
header('Location: ../solicitudes.php');
exit();
?>