<?php
session_start();
if (!isset($_SESSION['inicio']) || !isset($_GET['amigo_id'])) {
    header('Location: php/cerrarSesion.php');
    exit();
}

require_once("conexion.php");

$mi_id = $_SESSION['iduserFinal'];
$amigo_id = $_GET['amigo_id'];

// Obtener el nombre del amigo
$sql_amigo = "SELECT user FROM usuarios WHERE iduser = '$amigo_id'";
$result_amigo = mysqli_query($conn, $sql_amigo);
$amigo = mysqli_fetch_assoc($result_amigo);

// Obtener el historial de mensajes
$sql_mensajes = "SELECT * FROM mensajes 
                 WHERE (idenviar = '$mi_id' AND idrecibir = '$amigo_id') 
                    OR (idenviar = '$amigo_id' AND idrecibir = '$mi_id') 
                 ORDER BY fecha DESC";
$result_mensajes = mysqli_query($conn, $sql_mensajes);

// Enviar un nuevo mensaje
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mensaje'])) {
    $mensaje = mysqli_real_escape_string($conn, $_POST['mensaje']);
    if (!empty($mensaje) && strlen($mensaje) <= 250) {
        $sql_insert = "INSERT INTO mensajes (idenviar, idrecibir, contenido, fecha) VALUES ('$mi_id', '$amigo_id', '$mensaje', NOW())";
        mysqli_query($conn, $sql_insert);
        header("Location: chat.php?amigo_id=$amigo_id");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatPro - <?php echo $amigo['user']; ?></title>
    <link rel="icon" href="../img/Ico Imagotipo.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container py-5">
        <form action="../perfil.php" class="d-inline">
            <input type="submit" name="volverPerfil" class="btn btn-primary" value="Volver">
        </form>
        <h4 style="padding-top:1%">Chat con <?php echo htmlspecialchars($amigo['user']); ?></h4>
        
        <!-- Mostrar mensajes -->
        <div class="mt-4 p-3 border rounded" style="height: 300px; overflow-y: auto;">
            <?php if (mysqli_num_rows($result_mensajes) > 0) : ?>
                <?php while ($mensaje = mysqli_fetch_assoc($result_mensajes)) : ?>
                    <div class="mb-2">
                        <strong><?php echo $mensaje['idenviar'] == $mi_id ? 'Tú' : htmlspecialchars($amigo['user']); ?>:</strong>
                        <p class="mb-0"><?php echo htmlspecialchars($mensaje['contenido']); ?></p>
                        <small class="text-muted"><?php echo $mensaje['fecha']; ?></small>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <p class="text-muted">No hay mensajes.</p>
            <?php endif; ?>
        </div>

        <!-- Formulario para enviar un mensaje nuevo -->
        <form action="" method="POST" class="mt-3">
            <div class="input-group">
                <input type="text" name="mensaje" maxlength="250" class="form-control" placeholder="Escribe tu mensaje...">
                <input type="submit" name="enviarMensaje" class="btn btn-primary" value="Enviar">
            </div>
        </form>
    </div>
</body>
</html>