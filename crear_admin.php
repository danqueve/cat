<?php
require 'db.php'; // Usa la conexión existente

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Limpiamos los datos de entrada
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    
    // ENCRIPTACIÓN CRÍTICA:
    // Aquí es donde convertimos el texto plano (ej: "hola") al hash MD5 que espera el login.php
    $password_md5 = md5($password);
    
    // Verificar si el usuario ya existe para decidir si UPDATE o INSERT
    $check = $conn->query("SELECT id FROM usuarios WHERE username = '$username'");
    
    if ($check && $check->num_rows > 0) {
        // Actualizar usuario existente (Reseteo de contraseña)
        $sql = "UPDATE usuarios SET password = '$password_md5', rol = 'admin' WHERE username = '$username'";
        if ($conn->query($sql)) {
            $mensaje = "✅ Usuario '$username' actualizado correctamente. Ahora puedes loguearte.";
        } else {
            $mensaje = "❌ Error al actualizar: " . $conn->error;
        }
    } else {
        // Crear nuevo usuario
        $sql = "INSERT INTO usuarios (username, password, rol) VALUES ('$username', '$password_md5', 'admin')";
        if ($conn->query($sql)) {
            $mensaje = "✅ Usuario '$username' creado exitosamente.";
        } else {
            $mensaje = "❌ Error al crear: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generar Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-white min-h-screen flex items-center justify-center">
    <div class="bg-slate-800 p-8 rounded-xl shadow-lg border border-slate-700 w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-400">Generador de Admin</h2>
        <p class="text-slate-400 text-sm mb-6 text-center">Utiliza esto para crear un usuario con la encriptación correcta o resetear una contraseña.</p>
        
        <?php if($mensaje): ?>
            <div class="bg-slate-900 border border-blue-500 p-4 rounded mb-6 text-center text-sm font-semibold">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block mb-1 text-slate-400 text-sm">Nombre de Usuario</label>
                <input type="text" name="username" placeholder="Ej: admin" class="w-full p-3 rounded-lg bg-slate-900 border border-slate-600 text-white focus:border-blue-500 outline-none" required>
            </div>
            <div>
                <label class="block mb-1 text-slate-400 text-sm">Nueva Contraseña</label>
                <input type="text" name="password" placeholder="Ej: miclavesegura" class="w-full p-3 rounded-lg bg-slate-900 border border-slate-600 text-white focus:border-blue-500 outline-none" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 py-3 rounded-lg font-bold transition shadow-lg">Guardar Usuario</button>
        </form>
        
        <div class="mt-8 pt-4 border-t border-slate-700 text-center">
            <a href="login.php" class="text-blue-400 hover:text-white transition font-medium"> &larr; Ir al Login</a>
        </div>
        
        <div class="mt-4 p-3 bg-red-500/10 border border-red-500/20 rounded text-center">
            <p class="text-xs text-red-400">⚠️ Por seguridad, elimina este archivo (crear_admin.php) del servidor una vez hayas terminado.</p>
        </div>
    </div>
</body>
</html>