<?php
session_start();

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "autofinder");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y sanear inputs
    $nombre     = trim($_POST['nombre']);
    $apellido   = trim($_POST['apellido']);
    $correo     = trim($_POST['correo']);
    $celular    = trim($_POST['celular']);
    $usuario    = trim($_POST['usuario']);
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

    // Validar que acepte condiciones
    if (!isset($_POST['acepto_condiciones'])) {
        $error = "Debes aceptar los términos y condiciones.";
    } else {
        // Verificar usuario/correo único
        $chk = $conexion->prepare("SELECT id FROM usuarios WHERE usuario=? OR correo=?");
        $chk->bind_param("ss", $usuario, $correo);
        $chk->execute();
        $chk->store_result();

        if ($chk->num_rows > 0) {
            $error = "El usuario o correo ya existe.";
        } else {
            // Insertar nuevo usuario
            $stmt = $conexion->prepare("
                INSERT INTO usuarios
                  (nombre, apellido, correo, usuario, contrasena, celular)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "ssssss",
                $nombre, $apellido, $correo, $usuario, $contrasena, $celular
            );

            if ($stmt->execute()) {
                // Registro exitoso: mostramos toast y redirigimos
                echo "<!DOCTYPE html>
<html lang=\"es\">
<head>
  <meta charset=\"UTF-8\">
  <title>Registro Completo</title>
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
  <link href=\"https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap\" rel=\"stylesheet\">
  <link rel=\"stylesheet\" href=\"css/styles3.css\">
</head>
<body>
  <!-- Toast -->
  <div id=\"toast\">🎉 ¡Usuario creado con éxito! 🎉</div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const toast = document.getElementById('toast');
      toast.classList.add('show');
      setTimeout(() => {
        toast.classList.remove('show');
        window.location.href = 'index.php';
      }, 3000);
    });
  </script>
</body>
</html>";
                exit;
            } else {
                $error = "Error al registrar: " . $stmt->error;
            }
            $stmt->close();
        }
        $chk->close();
    }
}

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrarte - AutoFinder</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/styles3.css">
</head>
<body>
  <div class="registro-wrapper">
    <!-- Panel izquierdo -->
    <div class="left-panel">
      <div class="logo-circle">
        <img src="imagenes/logo.png" alt="AutoFinder Logo">
      </div>
      <h1>AutoFinder</h1>
      <p>Tu mejor aliado para comparar precios.</p>
    </div>

    <!-- Panel derecho -->
    <div class="right-panel">
      <div class="form-container">
        <h2>Registrarte</h2>

        <?php if (!empty($error)): ?>
          <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="registro.php" method="POST">
          <input type="text" name="nombre"     placeholder="Nombre"            required>
          <input type="text" name="apellido"   placeholder="Apellido"          required>
          <input type="email" name="correo"    placeholder="Correo electrónico" required>
          <input type="tel" name="celular"     placeholder="Celular"           required>
          <input type="text" name="usuario"    placeholder="Usuario"           required>
          <input type="password" name="contrasena" placeholder="Contraseña"   required>

          <div class="terms-group">
            <label>
              <input type="checkbox" name="acepto_condiciones" required>
              Acepto los términos y condiciones
            </label>
            <label>
              <input type="checkbox" name="suscripcion_newsletter">
              Deseo recibir boletines informativos
            </label>
          </div>

          <button type="submit">Registrarme</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>