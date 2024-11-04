<?php
session_start();

if (!isset($_POST['inicio'])) {
    header('Location: ../index.php');
    exit();
}

require_once("conexion.php");
$user = $_POST['userlogin'];
$password = $_POST['pwdlogin'];
$_SESSION['userlogin'] = $user;

$user = mysqli_real_escape_string($conn, $user);
$password = mysqli_real_escape_string($conn, $password);

if (empty($user)) {
    header("Location: ../index.php?userloginVacio");
    mysqli_close($conn);
    exit();
} elseif (!preg_match('/^[a-zA-Z0-9]+$/', $user)) {
    header("Location: ../index.php?userloginError");
    mysqli_close($conn);
    exit();
} elseif (empty($password)) {
    header("Location: ../index.php?pwdloginVacio");
    mysqli_close($conn);
    exit();
}

$sql_existe = "SELECT * FROM usuarios WHERE user = '$user'";
$result_existe = mysqli_query($conn, $sql_existe);

if (mysqli_num_rows($result_existe) > 0) {
    $usuario = mysqli_fetch_assoc($result_existe);
    // Verificar la contraseña
    if (password_verify($password, $usuario['pwd'])) {
        // Establecer la sesión del usuario
        $_SESSION['iduserFinal'] = $usuario['iduser'];
        $_SESSION['userFinal'] = $usuario['user'];
        $_SESSION['inicio'] = true;
        mysqli_close($conn);
        header("Location: ../perfil.php");
        exit();
    } else {
        // Contraseña incorrecta
        mysqli_close($conn);
        header("Location: ../index.php?loginError");
        exit();
    }
} else {
    // Usuario no encontrado
    mysqli_close($conn);
    header("Location: ../index.php?loginError");
    exit();
}
?>