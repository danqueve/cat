<?php
session_start();
require 'db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificamos que los datos existan antes de usarlos para evitar el Warning
    if (isset($_POST['username']) && isset($_POST['password'])) {
        
        $u = $conn->real_escape_string($_POST['username']); // Alineado con el input HTML
        $p = md5($_POST['password']); 

        // Consulta SQL corregida para usar la columna 'username' de tu base de datos
        $sql = "SELECT * FROM usuarios WHERE username = '$u' AND password = '$p'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $_SESSION['admin'] = true;
            header("Location: admin.php");
            exit();
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="flex items-center justify-center min-h-screen bg-slate-950 p-4">
    <div class="w-full max-w-sm bg-slate-900 border border-slate-800 p-8 rounded-3xl shadow-2xl">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-white">Panel Admin</h2>
            <p class="text-slate-500 text-sm">Imperio Comercial</p>
        </div>
        <?php if($error): ?><p class="text-red-400 text-center text-sm mb-4"><?php echo $error; ?></p><?php endif; ?>
        
        <form method="POST" class="space-y-4">
            <!-- INPUT NAME CORREGIDO A 'username' -->
            <input type="text" name="username" placeholder="Usuario" class="w-full p-3 rounded-xl admin-input" required>
            <input type="password" name="password" placeholder="Contraseña" class="w-full p-3 rounded-xl admin-input" required>
            <button class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-xl transition">Entrar</button>
        </form>
        
        <a href="index.php" class="block text-center text-slate-500 text-xs mt-6 hover:text-white">Volver al sitio</a>
    </div>
</body>
</html>