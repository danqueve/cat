<?php
session_start();
require 'db.php';

// Verificar sesión
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$msg = "";
$msg_type = ""; // 'success' o 'error'

// --- DETECCIÓN DE ERROR CRÍTICO DE LÍMITE (SOLUCIÓN AL FALLO SILENCIOSO) ---
// Si se envió un POST pero no llegaron datos, es porque el servidor bloqueó la subida por tamaño excesivo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) && empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
    $max_post = ini_get('post_max_size');
    $msg = "❌ <b>Error de Capacidad:</b> Intentaste subir demasiados datos juntos y el servidor los rechazó.<br>El límite actual es de <b>$max_post</b>. <br>👉 <b>Solución:</b> Selecciona menos imágenes a la vez (ej: sube de a 10 o 15 fotos).";
    $msg_type = "error";
}

// --- LÓGICA: CREAR CATEGORÍA ---
if (isset($_POST['nueva_categoria'])) {
    $nombre = $conn->real_escape_string($_POST['nombre_cat']);
    $icono = "fa-solid fa-tag"; 
    $bg = "bg-slate-800";
    $text = "text-white";
    
    $sql = "INSERT INTO categorias (nombre, icono, color_bg, color_text) VALUES ('$nombre', '$icono', '$bg', '$text')";
    
    if ($conn->query($sql)) {
        $msg = "Categoría '$nombre' creada con éxito.";
        $msg_type = "success";
    } else {
        $msg = "Error al crear categoría: " . $conn->error;
        $msg_type = "error";
    }
}

// --- LÓGICA: SUBIR FLYERS (CARGA MASIVA) ---
if (isset($_POST['subir_flyer'])) {
    $titulo = $conn->real_escape_string($_POST['titulo']);
    $cat_id = (int)$_POST['categoria_id'];
    
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $success_count = 0;
    $error_count = 0;
    $error_details = "";

    // Verificar si se enviaron archivos
    if (isset($_FILES['imagen']) && is_array($_FILES['imagen']['name'])) {
        $total_files = count($_FILES['imagen']['name']);
        
        // Advertencia de límite de cantidad de archivos (PHP suele limitar a 20 por envío)
        $max_uploads = ini_get('max_file_uploads');
        if ($total_files > $max_uploads) {
            $msg = "⚠️ Advertencia: Seleccionaste $total_files archivos, pero el servidor solo permite subir $max_uploads a la vez. Algunos no se subirán.";
            $msg_type = "error";
        }

        for ($i = 0; $i < $total_files; $i++) {
            $file_name = $_FILES['imagen']['name'][$i];
            $file_tmp = $_FILES['imagen']['tmp_name'][$i];
            $file_error = $_FILES['imagen']['error'][$i];
            
            // Procesar solo si no hubo error en la subida
            if ($file_error === UPLOAD_ERR_OK && !empty($file_tmp)) {
                
                // Validar extensión
                $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $valid_extensions = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array($file_extension, $valid_extensions)) {
                    // Validar que sea imagen real
                    $check = getimagesize($file_tmp);
                    if ($check !== false) {
                        // Nombre único: timestamp + index + random
                        $new_filename = uniqid() . "_$i." . $file_extension;
                        $target_file = $target_dir . $new_filename;

                        if (move_uploaded_file($file_tmp, $target_file)) {
                            // Insertar en DB
                            $sql = "INSERT INTO flyers (categoria_id, titulo, imagen_url) VALUES ($cat_id, '$titulo', '$target_file')";
                            if ($conn->query($sql)) {
                                $success_count++;
                            } else {
                                $error_count++;
                                $error_details .= "Error DB ($file_name). ";
                            }
                        } else {
                            $error_count++;
                            $error_details .= "Error al mover ($file_name). Verifica espacio en disco. ";
                        }
                    } else {
                        $error_count++;
                        $error_details .= "Falso positivo imagen ($file_name). ";
                    }
                } else {
                    $error_count++;
                    $error_details .= "Formato inválido ($file_name). ";
                }
            } else {
                // Ignorar archivos vacíos si el usuario no seleccionó nada en uno de los slots, 
                // pero si es un error real, contarlo.
                if ($file_error !== UPLOAD_ERR_NO_FILE) {
                    $error_count++;
                    // Errores específicos de PHP
                    if ($file_error == UPLOAD_ERR_INI_SIZE || $file_error == UPLOAD_ERR_FORM_SIZE) {
                        $error_details .= "Archivo muy pesado ($file_name). ";
                    }
                }
            }
        }

        if ($success_count > 0) {
            $msg = "✅ ¡Listo! Se publicaron $success_count imágenes correctamente.";
            $msg_type = "success";
            if ($error_count > 0) {
                $msg .= " (Hubo $error_count errores: $error_details)";
            }
        } elseif ($error_count > 0) {
            $msg = "❌ No se pudo subir ninguna imagen. Detalles: $error_details";
            $msg_type = "error";
        } elseif (empty($msg)) { // Si no hay otro mensaje previo
            $msg = "⚠️ No seleccionaste ninguna imagen o hubo un error desconocido.";
            $msg_type = "error";
        }
    }
}

// Obtener categorías para el select
$cats = $conn->query("SELECT * FROM categorias ORDER BY nombre ASC");

// Obtener últimos flyers para vista previa
$last_flyers = $conn->query("SELECT flyers.*, categorias.nombre as cat_nombre FROM flyers JOIN categorias ON flyers.categoria_id = categorias.id ORDER BY flyers.id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Imperio Comercial</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <!-- Estilos -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-slate-950 text-slate-200 min-h-screen p-4 md:p-8 font-sans">

    <div class="max-w-4xl mx-auto">
        
        <!-- Header del Admin -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4 border-b border-slate-800 pb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg">IC</div>
                <div>
                    <h1 class="text-2xl font-bold text-white leading-none">Panel de Control</h1>
                    <p class="text-slate-500 text-sm mt-1">Gestión de Catálogo</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="index.php" target="_blank" class="bg-slate-800 hover:bg-slate-700 text-slate-200 px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2">
                    <i class="fa-solid fa-eye"></i> Ver Web
                </a>
                <a href="logout.php" class="bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2">
                    <i class="fa-solid fa-right-from-bracket"></i> Salir
                </a>
            </div>
        </div>

        <!-- Mensajes de Alerta -->
        <?php if($msg): ?>
            <div class="<?php echo $msg_type === 'success' ? 'bg-green-500/10 border-green-500/20 text-green-400' : 'bg-red-500/10 border-red-500/20 text-red-400'; ?> border p-4 rounded-xl mb-8 flex items-center gap-3 animate-pulse">
                <i class="fa-solid <?php echo $msg_type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?> text-xl"></i>
                <span class="font-medium"><?php echo $msg; ?></span>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- TARJETA 1: SUBIR FLYERS (Principal) -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-white">
                    <span class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center"><i class="fa-solid fa-images text-sm"></i></span>
                    Publicar Flyers (Masivo)
                </h2>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-5" id="uploadForm">
                    
                    <!-- Selección de Categoría -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-2">Categoría</label>
                        <div class="relative">
                            <select name="categoria_id" class="w-full bg-slate-950 border border-slate-800 text-white p-3 rounded-xl focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none appearance-none" required>
                                <option value="" disabled selected>Selecciona una opción...</option>
                                <?php 
                                if($cats && $cats->num_rows > 0) {
                                    $cats->data_seek(0);
                                    while($row = $cats->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['nombre']; ?></option>
                                <?php 
                                    endwhile; 
                                } else {
                                    echo "<option value='' disabled>No hay categorías creadas</option>";
                                }
                                ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Título -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-2">Título / Precio (Compartido)</label>
                        <input type="text" name="titulo" placeholder="Ej: Oferta de la Semana" class="w-full bg-slate-950 border border-slate-800 text-white p-3 rounded-xl focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none" required>
                        <p class="text-xs text-slate-500 mt-1">Este título se aplicará a todas las fotos que subas ahora.</p>
                    </div>

                    <!-- Imagen Múltiple -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-2">Imágenes del Producto</label>
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-800 border-dashed rounded-xl cursor-pointer bg-slate-950 hover:bg-slate-900 hover:border-blue-500/50 transition group">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-600 group-hover:text-blue-500 mb-2 transition"></i>
                                <p class="text-sm text-slate-500 group-hover:text-slate-400 text-center px-2">Click para seleccionar <b>varias imágenes</b></p>
                            </div>
                            <!-- ATENCIÓN: name="imagen[]" y atributo "multiple" -->
                            <input type="file" name="imagen[]" accept="image/*" class="hidden" required id="fileInput" multiple>
                        </label>
                        <p id="file-name" class="text-xs text-center text-blue-400 mt-2 h-4 truncate"></p>
                        <!-- Mensaje de advertencia JS -->
                        <div id="file-warning" class="hidden mt-2 p-2 bg-yellow-500/10 border border-yellow-500/20 text-yellow-400 text-xs rounded text-center"></div>
                    </div>

                    <button type="submit" name="subir_flyer" id="submitBtn" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-900/20 transition active:scale-[0.98]">
                        Publicar Todo
                    </button>
                </form>
            </div>

            <div class="space-y-8">
                <!-- TARJETA 2: CREAR CATEGORÍA -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl h-fit">
                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-white">
                        <span class="w-8 h-8 rounded-lg bg-purple-500/20 text-purple-400 flex items-center justify-center"><i class="fa-solid fa-plus text-sm"></i></span>
                        Nueva Categoría
                    </h2>
                    <form method="POST" class="flex flex-col sm:flex-row gap-3">
                        <input type="text" name="nombre_cat" placeholder="Nombre (Ej: Smart TV)" class="flex-1 bg-slate-950 border border-slate-800 text-white p-3 rounded-xl focus:border-purple-500 focus:ring-1 focus:ring-purple-500 outline-none" required>
                        <button type="submit" name="nueva_categoria" class="bg-purple-600 hover:bg-purple-500 text-white font-bold px-6 py-3 rounded-xl transition shadow-lg shadow-purple-900/20 whitespace-nowrap">
                            Crear
                        </button>
                    </form>
                </div>

                <!-- TARJETA 3: ÚLTIMAS SUBIDAS -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4">Últimos Flyers Subidos</h3>
                    <div class="space-y-3">
                        <?php if($last_flyers && $last_flyers->num_rows > 0): ?>
                            <?php while($flyer = $last_flyers->fetch_assoc()): ?>
                                <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-950/50 border border-slate-800 hover:border-slate-700 transition">
                                    <div class="w-10 h-10 rounded bg-slate-800 bg-cover bg-center shrink-0" style="background-image: url('<?php echo $flyer['imagen_url']; ?>');"></div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-white truncate"><?php echo $flyer['titulo']; ?></p>
                                        <p class="text-xs text-slate-500 truncate"><?php echo $flyer['cat_nombre']; ?></p>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-slate-600 text-sm text-center py-4">No hay flyers subidos aún.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-12 text-center text-slate-600 text-sm">
            &copy; <?php echo date("Y"); ?> Imperio Comercial - Panel de Administración
        </div>
    </div>

    <script>
        // UX: Script para validar tamaño y cantidad de imágenes
        const fileInput = document.getElementById('fileInput');
        const fileNameDisplay = document.getElementById('file-name');
        const fileWarning = document.getElementById('file-warning');
        const submitBtn = document.getElementById('submitBtn');

        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                
                // Mostrar cuántos archivos se eligieron
                if (this.files.length === 1) {
                    fileNameDisplay.textContent = "1 archivo seleccionado: " + this.files[0].name;
                } else {
                    fileNameDisplay.textContent = this.files.length + " archivos seleccionados para subir.";
                }
                
                // Validar si alguno es muy pesado (Advertencia visual)
                const sizeLimit = 10 * 1024 * 1024; // 10MB
                let tooBig = false;
                let bigFileName = "";

                for (let i = 0; i < this.files.length; i++) {
                    if (this.files[i].size > sizeLimit) {
                        tooBig = true;
                        bigFileName = this.files[i].name;
                        break;
                    }
                }
                
                if (tooBig) {
                    fileWarning.innerHTML = `⚠️ ¡Cuidado! "${bigFileName}" pesa más de 10MB.<br>Es posible que el servidor la rechace si subes muchas juntas.`;
                    fileWarning.classList.remove('hidden');
                } else {
                    fileWarning.textContent = "";
                    fileWarning.classList.add('hidden');
                }
            } else {
                fileNameDisplay.textContent = "";
                fileWarning.classList.add('hidden');
            }
        });
    </script>

</body>
</html>