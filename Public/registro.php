<?php
session_start();

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "autofinder");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y sanear inputs
    $nombre     = trim($_POST['nombre']   ?? '');
    $apellido   = trim($_POST['apellido'] ?? '');
    $correo     = trim($_POST['correo']   ?? '');
    $celular    = trim($_POST['celular']  ?? '');
    $usuario    = trim($_POST['usuario']  ?? '');
    $pass_raw   = $_POST['contrasena']    ?? '';

    $errores = [];

    // -------- VALIDACIONES --------
    // Nombre / apellido básicos
    if ($nombre === '' || mb_strlen($nombre) < 2 || mb_strlen($nombre) > 100) {
        $errores[] = "Nombre inválido.";
    }
    if ($apellido === '' || mb_strlen($apellido) < 2 || mb_strlen($apellido) > 100) {
        $errores[] = "Apellido inválido.";
    }

    // Correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "Correo inválido.";
    }

    // Celular: exactamente 9 dígitos
    if (!preg_match('/^9\d{8}$/', $celular)) {
    $errores[] = "El número de celular debe tener 9 dígitos y empezar con 9.";
}
    // Usuario
    if (!preg_match('/^[A-Za-z0-9._-]{3,50}$/', $usuario)) {
        $errores[] = "Usuario inválido. Usa solo letras, números, punto, guion y guion bajo.";
    }

    // Contraseña fuerte
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^\w\s]).{8,}$/', $pass_raw)) {
        $errores[] = "La contraseña debe tener mínimo 8 caracteres, una mayúscula, una minúscula, un número y un símbolo.";
    }

    // Términos
    if (!isset($_POST['acepto_condiciones'])) {
        $errores[] = "Debes aceptar los términos y condiciones.";
    }

    if (!empty($errores)) {
        // Hay errores de validación
        $error = implode("<br>", $errores);
    } else {
        // Verificar usuario/correo único
        $chk = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE usuario=? OR correo=?");
        $chk->bind_param("ss", $usuario, $correo);
        $chk->execute();
        $chk->store_result();

        if ($chk->num_rows > 0) {
            $error = "El usuario o correo ya existe.";
        } else {
            // Hash de contraseña solo cuando ya pasó las validaciones
            $contrasena = password_hash($pass_raw, PASSWORD_DEFAULT);

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
                $error = "Error al registrar.";
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
        <img src="imgs/logo.png" alt="AutoFinder Logo">
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

       <form action="registro.php" method="POST" novalidate>
  <input type="text" name="nombre"   placeholder="Nombre"   required minlength="2" maxlength="100"
          value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" >
  <input type="text" name="apellido" placeholder="Apellido" required minlength="2" maxlength="100"
          value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>" >

<!-- CORREO -->
<div class="field">
  <input type="email"
         id="correo"
         name="correo"
         placeholder="Correo electrónico"
         required
         autocomplete="email"
         value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">
  <small class="error-msg" id="correoError"></small>
</div>

<!-- CELULAR -->
<div class="field">
  <input type="tel"
         id="celular"
         name="celular"
         placeholder="Celular"
         required
         inputmode="numeric"
         maxlength="9"
         value="<?= htmlspecialchars($_POST['celular'] ?? '') ?>">
  <small class="error-msg" id="celularError"></small>
</div>

  <input type="text" name="usuario" placeholder="Usuario" required minlength="3" maxlength="50"
         pattern="^[A-Za-z0-9._-]{3,50}$" title="Letras, números, punto, guion y guion bajo">

  <!-- Password fuerte: 8–64, mayúscula, minúscula, número y símbolo -->
  <div class="password-wrapper">
  <input 
    id="password"
    type="password"
    name="contrasena"
    placeholder="Contraseña"
    required
    pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[^\\w\\s]).{8,64}$"
    title="Mínimo 8 caracteres con mayúscula, minúscula, número y símbolo"
  >

  <!-- Icono ojo -->
  <span class="toggle-password" onclick="togglePassword()">
    <!-- Ojo cerrado (por defecto) -->
    <svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#555" viewBox="0 0 24 24">
      <path d="M12 5c-7.633 0-11 7-11 7s3.367 7 11 7 11-7 11-7-3.367-7-11-7zm0 12c-2.761 
               0-5-2.239-5-5s2.239-5 5-5 5 2.239 5 5-2.239 5-5 5zm0-8c-1.654 
               0-3 1.346-3 3s1.346 3 3 3 3-1.346 3-3-1.346-3-3-3z"/>
    </svg>
  </span>
</div>

  <div class="terms-group">
    <label><input type="checkbox" name="acepto_condiciones" required> Acepto los términos y condiciones</label>
    <label><input type="checkbox" name="suscripcion_newsletter"> Deseo recibir boletines informativos</label>
  </div>

  <button type="submit">Registrarme</button>
</form>
      </div>
    </div>
  </div>
  <script>
function togglePassword() {
  const input = document.getElementById("password");
  const icon = document.getElementById("icon-eye");

  if (input.type === "password") {
    input.type = "text";
    icon.innerHTML = `
      <path d="M12 5c-7.633 0-11 7-11 7s3.367 7 11 7 11-7 11-7-3.367-7-11-7zm0 
               12c-2.761 0-5-2.239-5-5 0-.76.176-1.477.486-2.121L14.121 
               14.757A4.948 4.948 0 0 1 12 17zm4.514-2.879L9.879 
               7.243A4.948 4.948 0 0 1 12 7c2.761 0 5 2.239 5 5 0 .76-.176 
               1.477-.486 2.121z"/>`;
  } else {
    input.type = "password";
    icon.innerHTML = `
      <path d="M12 5c-7.633 0-11 7-11 7s3.367 7 11 7 11-7 11-7-3.367-7-11-7zm0 
               12c-2.761 0-5-2.239-5-5s2.239-5 5-5 5 2.239 5 5-2.239 
               5-5 5zm0-8c-1.654 0-3 1.346-3 3s1.346 3 3 3 3-1.346 
               3-3-1.346-3-3-3z"/>`;
  }
}
</script>
document.addEventListener('DOMContentLoaded', () => {
  const correo   = document.getElementById('correo');
  const cError   = document.getElementById('correoError');
  const celular  = document.getElementById('celular');
  const celError = document.getElementById('celularError');

  // Validación correo en tiempo real
  const correoRegex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

  correo.addEventListener('input', () => {
    const v = correo.value.trim();
    if (v === '') {
      correo.setCustomValidity('');
      cError.textContent = '';
      correo.classList.remove('is-invalid');
    } else if (!correoRegex.test(v)) {
      correo.setCustomValidity('Correo no válido');
      cError.textContent = 'Correo no válido';
      correo.classList.add('is-invalid');
    } else {
      correo.setCustomValidity('');
      cError.textContent = '';
      correo.classList.remove('is-invalid');
    }
  });

  // Solo números y máximo 9 dígitos en celular
  celular.addEventListener('input', () => {
    // eliminar todo lo que no sea dígito
    celular.value = celular.value.replace(/\D/g, '').slice(0, 9);

    if (celular.value.length === 0) {
      celular.setCustomValidity('');
      celError.textContent = '';
      celular.classList.remove('is-invalid');
    } else if (!/^9\d{8}$/.test(celular.value)) {
      celular.setCustomValidity('Debe empezar con 9 y tener 9 dígitos');
      celError.textContent = 'Debe empezar con 9 y tener 9 dígitos.';
      celular.classList.add('is-invalid');
    } else {
      celular.setCustomValidity('');
      celError.textContent = '';
      celular.classList.remove('is-invalid');
    }
  });
});
</script>
</html>