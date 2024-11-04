<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['inicio'])) {
    header('Location: ./cerrarSesion.php');
    exit();
}

require_once("php/conexion.php");

$mi_id = $_SESSION['iduserFinal'];
if (!is_numeric($mi_id)) {
    header('Location: php/cerrarSesion.php');
    exit();
}
$mi_id = mysqli_real_escape_string($conn, $mi_id);

$sql_solicitudes = "SELECT solicitudes.idsolicitud, usuarios.user AS nombre_usuario
                    FROM solicitudes
                    JOIN usuarios ON solicitudes.iduser_1 = usuarios.iduser
                    WHERE solicitudes.iduser_2 = '$mi_id' AND solicitudes.solicitud_estado = 'pendiente'";
$result_solicitudes = mysqli_query($conn, $sql_solicitudes);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitudes - ChatPro</title>
    <link rel="icon" href="img/Ico Imagotipo.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container py-5">
        <h4>Solicitudes de Amistad</h4>
        <?php if (mysqli_num_rows($result_solicitudes) > 0) : ?>
            <ul class="list-group mt-3">
                <?php while ($solicitud = mysqli_fetch_assoc($result_solicitudes)) : ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo htmlspecialchars($solicitud['nombre_usuario']); ?>
                        <div>
                            <a href="php/procesarSolicitud.php?accion=aceptar&solicitud_id=<?php echo $solicitud['idsolicitud']; ?>" class="btn btn-success btn-sm">Aceptar</a>
                            <a href="php/procesarSolicitud.php?accion=rechazar&solicitud_id=<?php echo $solicitud['idsolicitud']; ?>" class="btn btn-danger btn-sm">Rechazar</a>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else : ?>
            <p class="text-muted">No tienes solicitudes de amistad.</p>
        <?php endif; ?>

        <form action="perfil.php" method="POST">
            <input type="submit" name="volver" value="Volver" class="btn btn-primary mt-3">
        </form>
    </div>
</body>
</html>