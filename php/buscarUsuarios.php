<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['inicio'])) {
    header('Location: ./cerrarSesion.php');
    exit();
}

require_once("conexion.php");

$busquedaResultado = [];

// Realizar la búsqueda si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buscar']) && !empty($_POST['buscar'])) {
    // Sanitizar la cadena de búsqueda
    $busqueda = mysqli_real_escape_string($conn, $_POST['buscar']);

    $sql_busqueda = "SELECT * FROM usuarios WHERE (user LIKE '%$busqueda%' OR nombre LIKE '%$busqueda%') AND iduser != '{$_SESSION['iduserFinal']}'";
    $result_busqueda = mysqli_query($conn, $sql_busqueda);

    // Almacenar los resultados en la variable
    while ($row = mysqli_fetch_assoc($result_busqueda)) {
        $busquedaResultado[] = $row;
    }
}

// Manejar la solicitud de amistad
if (isset($_GET['solicitud']) && isset($_GET['id'])) {
    $amigo_id = $_GET['id'];
    $mi_id = $_SESSION['iduserFinal'];

    // Insertar una nueva solicitud de amistad en la tabla "amistades"
    $sql_solicitud = "INSERT INTO solicitudes (iduser_1, iduser_2, solicitud_estado) VALUES ('$mi_id', '$amigo_id', 'pendiente')";
    mysqli_query($conn, $sql_solicitud);
    header('Location: buscarUsuarios.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Usuarios - ChatPro</title>
    <link rel="icon" href="img/Ico Imagotipo.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container py-5">
        <h4>Buscar Usuarios</h4>
        <form action="" method="POST" class="d-inline">
            <div class="mb-3">
                <input type="text" id="buscar" name="buscar" class="form-control" placeholder="Buscar">
            </div>
            <input type="submit" name="submitBuscar" class="btn btn-primary" value="Buscar">
        </form>
        <form action="../perfil.php" class="d-inline">
            <input type="submit" name="volverPerfil" class="btn btn-primary" value="Volver al Perfil">
        </form>

        <!-- Resultados de Búsqueda -->
        <?php if (!empty($busquedaResultado)) : ?>
            <h3 class="mt-5">Resultados de la Búsqueda:</h3>
            <ul class="list-group mt-3">
                <?php foreach ($busquedaResultado as $usuario) : ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo htmlspecialchars($usuario['user']).' ('.htmlspecialchars($usuario['nombre']).')'; ?>
                        <a href="buscarUsuarios.php?solicitud=1&id=<?php echo $usuario['iduser']; ?>" class="btn btn-success btn-sm">Enviar Solicitud de Amistad</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submitBuscar']) && empty($busquedaResultado)) : ?>
            <p class="mt-5 text-danger">No se encontraron usuarios con ese nombre.</p>
        <?php endif; ?>
    </div>
</body>
</html>
