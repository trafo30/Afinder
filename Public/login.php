<?php
session_start();

$conexion = new mysqli("localhost", "root", "", "autofinder");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$usuario = $_POST['usuario'];
$contrasena = $_POST['contrasena'];

$sql = "SELECT * FROM usuarios WHERE usuario = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    // Verificar contraseña hasheada
    if (password_verify($contrasena, $user['contrasena'])) {

        // GUARDAR TAMBIÉN EL ID DEL USUARIO
        $_SESSION['id_usuario'] = (int)$user['id_usuario'];

        // Guardar otros datos en sesión
        $_SESSION['usuario']  = $user['usuario'];
        $_SESSION['nombre']   = $user['nombre'];
        $_SESSION['apellido'] = $user['apellido'];
        $_SESSION['correo']   = $user['correo'];
        $_SESSION['celular']  = $user['celular'];

        header("Location: index.php"); // Redirigir a página principal
        exit();
    } else {
        echo "Contraseña incorrecta.";
    }
} else {
    echo "Usuario no encontrado.";
}

$stmt->close();
$conexion->close();
