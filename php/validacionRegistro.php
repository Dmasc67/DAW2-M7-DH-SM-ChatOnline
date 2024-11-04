<?php
session_start();

if (isset($_POST['index'])) {
    header('Location: ../index.php');
    exit();
}

if (!isset($_POST['registro'])) {
    header('Location: ../registro.php');
    exit();
}

require_once("conexion.php");
$user = $_POST['user'];
$nombreCompleto = $_POST['nombre'];
$password = $_POST['pwd'];
$_SESSION['user'] = $user;
$_SESSION['nombre'] = $nombreCompleto;

$user = mysqli_real_escape_string($conn, $user);
$nombreCompleto = mysqli_real_escape_string($conn, $nombreCompleto);
$password = mysqli_real_escape_string($conn, $password);

if (empty($user)) {
    header("Location: ../registro.php?userVacio");
    mysqli_close($conn);
    exit();
} elseif (!preg_match('/^[a-zA-Z0-9]+$/', $user)) {
    header("Location: ../registro.php?userError");
    mysqli_close($conn);
    exit();
} elseif (empty($nombreCompleto)) {
    header("Location: ../registro.php?nombreVacio");
    mysqli_close($conn);
    exit();
} elseif (!preg_match('/^[\p{L}\s]+$/u', $nombreCompleto)) {
    header("Location: ../registro.php?nombreError");
    mysqli_close($conn);
    exit();
} elseif (empty($password)) {
    header("Location: ../registro.php?pwdVacio");
    mysqli_close($conn);
    exit();
}

$sql_existe = "SELECT * FROM usuarios WHERE user = '$user'";
$result_existe = mysqli_query($conn, $sql_existe);

if (mysqli_num_rows($result_existe) > 0) {
    mysqli_close($conn);
    header("Location: ../registro.php?existe");
    exit();
} else {
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $sql_insert = "INSERT INTO usuarios (user, nombre, pwd) VALUES ('$user', '$nombreCompleto', '$hashedPassword')";

    if (mysqli_query($conn, $sql_insert)) {
        header("Location: ../perfil.php");
        mysqli_close($conn);
        exit();
    } else {
        mysqli_close($conn);
        header("Location: ../registro.php?error=insertFail");
        exit();
    }
}
?>