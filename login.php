<?php
session_start();
require 'db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $u = $conn->real_escape_string($_POST['username']);
        $p = md5($_POST['password']);

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
    <title>Admin — Imperio Comercial</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="hero-gradient min-h-screen flex items-center justify-center p-4">

    <!-- Decorative blobs -->
    <div class="fixed top-0 left-0 w-72 h-72 rounded-full opacity-20 animate-float pointer-events-none"
         style="background: radial-gradient(circle, #F97316, transparent); transform: translate(-30%, -30%)"></div>
    <div class="fixed bottom-0 right-0 w-96 h-96 rounded-full opacity-15 animate-float pointer-events-none"
         style="background: radial-gradient(circle, #FBBF24, transparent); transform: translate(30%, 30%); animation-delay: -3s"></div>

    <div class="glass-card animate-fade-up w-full max-w-sm p-8 relative"
         style="border-color: rgba(249,115,22,0.25)">

        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl btn-warm animate-pulse-glow text-2xl font-bold mb-4"
                 style="font-family: 'Space Grotesk', sans-serif">IC</div>
            <h1 class="gradient-text text-2xl font-bold mb-1"
                style="font-family: 'Space Grotesk', sans-serif">Imperio Comercial</h1>
            <p class="text-sm" style="color: var(--text-secondary)">Panel de administración</p>
        </div>

        <?php if ($error): ?>
        <div class="mb-5 p-3 rounded-xl text-sm text-center font-medium"
             style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25); color: #f87171">
            <i class="fa-solid fa-circle-exclamation mr-2"></i><?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color: var(--text-secondary)">Usuario</label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-sm" style="color: var(--text-muted)"></i>
                    <input type="text" name="username" placeholder="usuario"
                           class="admin-input pl-9" required autocomplete="username">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1.5 uppercase tracking-wider" style="color: var(--text-secondary)">Contraseña</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-sm" style="color: var(--text-muted)"></i>
                    <input type="password" name="password" placeholder="••••••••"
                           class="admin-input pl-9" required autocomplete="current-password">
                </div>
            </div>
            <button type="submit" class="btn-warm w-full py-3 mt-2 font-bold text-base"
                    style="font-family: 'Space Grotesk', sans-serif">
                <i class="fa-solid fa-right-to-bracket"></i>
                Ingresar
            </button>
        </form>

        <a href="index.php" class="block text-center text-xs mt-6 hover:opacity-80 transition"
           style="color: var(--text-muted)">
            <i class="fa-solid fa-arrow-left mr-1"></i> Volver al catálogo
        </a>
    </div>

</body>
</html>
