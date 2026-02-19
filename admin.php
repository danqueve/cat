<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$msg = "";
$msg_type = "";

// Error crítico de límite
if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST) && empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
    $max_post = ini_get('post_max_size');
    $msg = "❌ <b>Error de Capacidad:</b> El servidor rechazó la solicitud. Límite: <b>$max_post</b>. Sube menos imágenes a la vez.";
    $msg_type = "error";
}

// Agregar columnas nuevas si no existen
$setupCols = [
    'orden'        => 'INT DEFAULT 0',
    'destacado'    => 'TINYINT(1) NOT NULL DEFAULT 0',
    'sin_stock'    => 'TINYINT(1) NOT NULL DEFAULT 0',
    'grupo_id'     => 'INT NULL',
    'es_principal' => 'TINYINT(1) NOT NULL DEFAULT 1',
];
foreach ($setupCols as $col => $def) {
    $r = $conn->query("SHOW COLUMNS FROM flyers LIKE '$col'");
    if ($r && $r->num_rows === 0) {
        $conn->query("ALTER TABLE flyers ADD COLUMN $col $def");
        if ($col === 'orden') $conn->query('UPDATE flyers SET orden = id');
    }
}

// ── ELIMINAR FLYER ──────────────────────────────────────────────
if (isset($_POST['eliminar_flyer'])) {
    $id = (int)$_POST['flyer_id'];
    $r  = $conn->query("SELECT imagen_url FROM flyers WHERE id = $id");
    if ($r && $row = $r->fetch_assoc()) {
        if (!empty($row['imagen_url']) && file_exists($row['imagen_url'])) {
            unlink($row['imagen_url']);
        }
        if ($conn->query("DELETE FROM flyers WHERE id = $id")) {
            $msg = "✅ Flyer eliminado correctamente."; $msg_type = "success";
        } else {
            $msg = "❌ Error al eliminar: " . $conn->error; $msg_type = "error";
        }
    } else { $msg = "❌ Flyer no encontrado."; $msg_type = "error"; }
}

// ── EDITAR TÍTULO FLYER ─────────────────────────────────────────
if (isset($_POST['editar_flyer'])) {
    $id     = (int)$_POST['flyer_id'];
    $titulo = $conn->real_escape_string(trim($_POST['titulo']));
    if ($conn->query("UPDATE flyers SET titulo = '$titulo' WHERE id = $id")) {
        $msg = "✅ Título actualizado."; $msg_type = "success";
    } else { $msg = "❌ Error: " . $conn->error; $msg_type = "error"; }
}

// ── REORDENAR FLYER ─────────────────────────────────────────────
if (isset($_POST['reordenar_flyer'])) {
    $id     = (int)$_POST['flyer_id'];
    $dir    = ($_POST['direccion'] === 'up') ? 'up' : 'down';
    $cat_id = (int)$_POST['cat_id'];
    $r = $conn->query("SELECT orden FROM flyers WHERE id = $id");
    if ($r && $row = $r->fetch_assoc()) {
        $o = (int)$row['orden'];
        $r2 = ($dir === 'up')
            ? $conn->query("SELECT id, orden FROM flyers WHERE categoria_id = $cat_id AND orden < $o ORDER BY orden DESC LIMIT 1")
            : $conn->query("SELECT id, orden FROM flyers WHERE categoria_id = $cat_id AND orden > $o ORDER BY orden ASC LIMIT 1");
        if ($r2 && $swap = $r2->fetch_assoc()) {
            $conn->query("UPDATE flyers SET orden = {$swap['orden']} WHERE id = $id");
            $conn->query("UPDATE flyers SET orden = $o WHERE id = {$swap['id']}");
            $msg = "✅ Orden actualizado."; $msg_type = "success";
        }
    }
}

// ── CREAR CATEGORÍA ─────────────────────────────────────────────
if (isset($_POST['nueva_categoria'])) {
    $nombre = $conn->real_escape_string(trim($_POST['nombre_cat']));
    $icono  = $conn->real_escape_string($_POST['icono_cat']   ?? 'fa-solid fa-tag');
    $bg     = $conn->real_escape_string($_POST['color_bg']    ?? 'bg-slate-700');
    $text   = $conn->real_escape_string($_POST['color_text']  ?? 'text-white');
    if ($conn->query("INSERT INTO categorias (nombre, icono, color_bg, color_text) VALUES ('$nombre','$icono','$bg','$text')")) {
        $msg = "✅ Categoría '$nombre' creada."; $msg_type = "success";
    } else { $msg = "❌ Error: " . $conn->error; $msg_type = "error"; }
}

// ── EDITAR CATEGORÍA ────────────────────────────────────────────
if (isset($_POST['editar_categoria'])) {
    $id     = (int)$_POST['cat_id'];
    $nombre = $conn->real_escape_string(trim($_POST['nombre_cat']));
    $icono  = $conn->real_escape_string($_POST['icono_cat']  ?? 'fa-solid fa-tag');
    $bg     = $conn->real_escape_string($_POST['color_bg']   ?? 'bg-slate-700');
    $text   = $conn->real_escape_string($_POST['color_text'] ?? 'text-white');
    if ($conn->query("UPDATE categorias SET nombre='$nombre',icono='$icono',color_bg='$bg',color_text='$text' WHERE id=$id")) {
        $msg = "✅ Categoría actualizada."; $msg_type = "success";
    } else { $msg = "❌ Error: " . $conn->error; $msg_type = "error"; }
}

// ── ELIMINAR CATEGORÍA ──────────────────────────────────────────
if (isset($_POST['eliminar_categoria'])) {
    $id = (int)$_POST['cat_id'];
    $c  = $conn->query("SELECT COUNT(*) as t FROM flyers WHERE categoria_id = $id")->fetch_assoc();
    if ((int)$c['t'] > 0) {
        $msg = "⚠️ No podés eliminar: tiene {$c['t']} producto(s). Eliminá primero los flyers."; $msg_type = "error";
    } else {
        if ($conn->query("DELETE FROM categorias WHERE id = $id")) {
            $msg = "✅ Categoría eliminada."; $msg_type = "success";
        } else { $msg = "❌ Error: " . $conn->error; $msg_type = "error"; }
    }
}

// ── TOGGLE DESTACADO ───────────────────────────────────────────
if (isset($_POST['toggle_destacado'])) {
    $id = (int)$_POST['flyer_id'];
    $conn->query("UPDATE flyers SET destacado = 1 - destacado WHERE id = $id");
    $msg = "✅ Estado 'Destacado' actualizado."; $msg_type = "success";
}

// ── TOGGLE SIN STOCK ────────────────────────────────────────────
if (isset($_POST['toggle_sin_stock'])) {
    $id = (int)$_POST['flyer_id'];
    $conn->query("UPDATE flyers SET sin_stock = 1 - sin_stock WHERE id = $id");
    $msg = "✅ Estado 'Sin Stock' actualizado."; $msg_type = "success";
}

// ── SUBIR FLYERS ────────────────────────────────────────────────
if (isset($_POST['subir_flyer'])) {
    $titulo     = $conn->real_escape_string($_POST['titulo']);
    $cat_id     = (int)$_POST['categoria_id'];
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
    $success_count = 0; $error_count = 0; $error_details = "";

    if (isset($_FILES['imagen']) && is_array($_FILES['imagen']['name'])) {
        $total_files = count($_FILES['imagen']['name']);
        $max_uploads = ini_get('max_file_uploads');
        if ($total_files > $max_uploads) {
            $msg = "⚠️ Seleccionaste $total_files archivos, el servidor acepta máx $max_uploads."; $msg_type = "error";
        }
        $max_ord_r  = $conn->query("SELECT COALESCE(MAX(orden),0) as mo FROM flyers WHERE categoria_id=$cat_id");
        $base_orden = (int)$max_ord_r->fetch_assoc()['mo'];

        for ($i = 0; $i < $total_files; $i++) {
            $fname = $_FILES['imagen']['name'][$i];
            $ftmp  = $_FILES['imagen']['tmp_name'][$i];
            $ferr  = $_FILES['imagen']['error'][$i];
            if ($ferr === UPLOAD_ERR_OK && !empty($ftmp)) {
                $ext = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                    if (getimagesize($ftmp) !== false) {
                        $newfile   = $target_dir . uniqid() . "_$i.$ext";
                        $new_orden = $base_orden + $success_count + 1;
                        if (move_uploaded_file($ftmp, $newfile)) {
                            $is_first     = ($success_count === 0);
                            $es_principal = $is_first ? 1 : 0;
                            if (isset($grupo_id_batch)) {
                                $extra_cols = ", grupo_id, es_principal";
                                $extra_vals = ", $grupo_id_batch, $es_principal";
                            } else {
                                $extra_cols = ", es_principal";
                                $extra_vals = ", 1";
                            }
                            if ($conn->query("INSERT INTO flyers (categoria_id,titulo,imagen_url,orden$extra_cols) VALUES ($cat_id,'$titulo','$newfile',$new_orden$extra_vals)")) {
                                $success_count++;
                                if ($is_first && $total_files > 1) {
                                    $grupo_id_batch = $conn->insert_id;
                                    $conn->query("UPDATE flyers SET grupo_id = $grupo_id_batch WHERE id = $grupo_id_batch");
                                }
                            } else { $error_count++; $error_details .= "DB($fname) "; }
                        } else { $error_count++; $error_details .= "Mover($fname) "; }
                    } else { $error_count++; $error_details .= "NoImagen($fname) "; }
                } else { $error_count++; $error_details .= "Formato($fname) "; }
            } elseif ($ferr !== UPLOAD_ERR_NO_FILE) {
                $error_count++;
                if ($ferr == UPLOAD_ERR_INI_SIZE || $ferr == UPLOAD_ERR_FORM_SIZE) $error_details .= "Pesado($fname) ";
            }
        }
        if ($success_count > 0) {
            $msg = "✅ Se publicaron $success_count imágenes."; $msg_type = "success";
            if ($error_count > 0) $msg .= " ($error_count errores: $error_details)";
        } elseif ($error_count > 0) { $msg = "❌ No se pudo subir ninguna. $error_details"; $msg_type = "error";
        } elseif (empty($msg))      { $msg = "⚠️ No seleccionaste imágenes."; $msg_type = "error"; }
    }
}

// ── CONSULTAS DE DISPLAY ─────────────────────────────────────────
$page       = max(1, (int)($_GET['page'] ?? 1));
$per_page   = 20;
$offset     = ($page - 1) * $per_page;
$cat_filter = (int)($_GET['cat'] ?? 0);
$where      = $cat_filter ? "WHERE flyers.categoria_id = $cat_filter" : "";
$total_r    = $conn->query("SELECT COUNT(*) as t FROM flyers $where");
$total_fly  = (int)$total_r->fetch_assoc()['t'];
$total_pages= max(1, ceil($total_fly / $per_page));

$all_flyers = $conn->query(
    "SELECT flyers.*, categorias.nombre as cat_nombre,
            (SELECT COUNT(*) FROM flyers f2 WHERE f2.grupo_id = flyers.grupo_id AND flyers.grupo_id IS NOT NULL) as grupo_count
     FROM flyers JOIN categorias ON flyers.categoria_id = categorias.id
     $where ORDER BY flyers.orden ASC, flyers.id DESC
     LIMIT $per_page OFFSET $offset"
);

$cats_res  = $conn->query("SELECT *, (SELECT COUNT(*) FROM flyers WHERE categoria_id=categorias.id) as flyer_count FROM categorias ORDER BY nombre ASC");
$cats_list = [];
while ($r = $cats_res->fetch_assoc()) $cats_list[] = $r;

// ── DATOS PARA PICKERS ───────────────────────────────────────────
$ICONS = [
    'fa-solid fa-tv'             => 'TV',
    'fa-solid fa-couch'          => 'Sofá',
    'fa-solid fa-blender'        => 'Electro',
    'fa-solid fa-mobile-screen'  => 'Celular',
    'fa-solid fa-laptop'         => 'Laptop',
    'fa-solid fa-shirt'          => 'Ropa',
    'fa-solid fa-baby-carriage'  => 'Bebé',
    'fa-solid fa-bicycle'        => 'Bici',
    'fa-solid fa-dumbbell'       => 'Deporte',
    'fa-solid fa-hammer'         => 'Herram.',
    'fa-solid fa-tag'            => 'General',
    'fa-solid fa-star'           => 'Destac.',
    'fa-solid fa-fire'           => 'Oferta',
    'fa-solid fa-box'            => 'Caja',
    'fa-solid fa-kitchen-set'    => 'Cocina',
    'fa-solid fa-bed'            => 'Dormi.',
    'fa-solid fa-fan'            => 'Ventil.',
    'fa-solid fa-gamepad'        => 'Juegos',
    'fa-solid fa-headphones'     => 'Audio',
    'fa-solid fa-camera'         => 'Cámara',
    'fa-solid fa-gem'            => 'Joyería',
    'fa-solid fa-shoe-prints'    => 'Calzado',
    'fa-solid fa-chair'          => 'Silla',
    'fa-solid fa-toolbox'        => 'Toolbox',
];
$COLORS = [
    ['bg'=>'bg-blue-600',   'text'=>'text-white', 'hex'=>'#2563eb', 'name'=>'Azul'],
    ['bg'=>'bg-purple-600', 'text'=>'text-white', 'hex'=>'#9333ea', 'name'=>'Violeta'],
    ['bg'=>'bg-green-600',  'text'=>'text-white', 'hex'=>'#16a34a', 'name'=>'Verde'],
    ['bg'=>'bg-red-600',    'text'=>'text-white', 'hex'=>'#dc2626', 'name'=>'Rojo'],
    ['bg'=>'bg-orange-500', 'text'=>'text-white', 'hex'=>'#f97316', 'name'=>'Naranja'],
    ['bg'=>'bg-yellow-500', 'text'=>'text-black', 'hex'=>'#eab308', 'name'=>'Amarillo'],
    ['bg'=>'bg-pink-600',   'text'=>'text-white', 'hex'=>'#db2777', 'name'=>'Rosa'],
    ['bg'=>'bg-indigo-600', 'text'=>'text-white', 'hex'=>'#4f46e5', 'name'=>'Índigo'],
    ['bg'=>'bg-teal-600',   'text'=>'text-white', 'hex'=>'#0d9488', 'name'=>'Teal'],
    ['bg'=>'bg-slate-700',  'text'=>'text-white', 'hex'=>'#334155', 'name'=>'Gris'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Imperio Comercial</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .icon-btn.active  { box-shadow: 0 0 0 2px #a855f7; background: #374151; color: white; }
        .color-dot.active { box-shadow: 0 0 0 3px white, 0 0 0 5px #a855f7; transform: scale(1.1); }
        .modal-bg { backdrop-filter: blur(4px); }
    </style>
</head>
<body class="bg-slate-950 text-slate-200 min-h-screen p-4 md:p-8">

<div class="max-w-5xl mx-auto">

    <!-- ── HEADER ── -->
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

    <!-- ── ALERT ── -->
    <?php if ($msg): ?>
    <div class="<?= $msg_type === 'success' ? 'bg-green-500/10 border-green-500/20 text-green-400' : 'bg-red-500/10 border-red-500/20 text-red-400' ?> border p-4 rounded-xl mb-8 flex items-center gap-3">
        <i class="fa-solid <?= $msg_type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?> text-xl flex-shrink-0"></i>
        <span class="font-medium"><?= $msg ?></span>
    </div>
    <?php endif; ?>

    <!-- ── SECCIÓN 1: SUBIR + NUEVA CATEGORÍA ── -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">

        <!-- Subir Flyers -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-white">
                <span class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center"><i class="fa-solid fa-images text-sm"></i></span>
                Publicar Flyers
            </h2>
            <form method="POST" enctype="multipart/form-data" class="space-y-5" id="uploadForm">
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-2">Categoría</label>
                    <div class="relative">
                        <select name="categoria_id" class="w-full bg-slate-950 border border-slate-800 text-white p-3 rounded-xl focus:border-blue-500 outline-none appearance-none" required>
                            <option value="" disabled selected>Selecciona...</option>
                            <?php foreach ($cats_list as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500"><i class="fa-solid fa-chevron-down text-xs"></i></div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-2">Título / Precio</label>
                    <input type="text" name="titulo" placeholder="Ej: Oferta de la Semana" class="w-full bg-slate-950 border border-slate-800 text-white p-3 rounded-xl focus:border-blue-500 outline-none" required>
                    <p class="text-xs text-slate-500 mt-1">Se aplica a todas las fotos que subas ahora.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-2">Imágenes</label>
                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-800 border-dashed rounded-xl cursor-pointer bg-slate-950 hover:bg-slate-900 hover:border-blue-500/50 transition group">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-600 group-hover:text-blue-500 mb-2 transition"></i>
                            <p class="text-sm text-slate-500 group-hover:text-slate-400 text-center px-2">Click para seleccionar <b>varias imágenes</b></p>
                        </div>
                        <input type="file" name="imagen[]" accept="image/*" class="hidden" id="fileInput" multiple>
                    </label>
                    <p id="file-name" class="text-xs text-center text-blue-400 mt-2 h-4 truncate"></p>
                    <div id="file-warning" class="hidden mt-2 p-2 bg-yellow-500/10 border border-yellow-500/20 text-yellow-400 text-xs rounded text-center"></div>
                </div>
                <button type="submit" name="subir_flyer" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3.5 rounded-xl transition active:scale-[0.98]">Publicar Todo</button>
            </form>
        </div>

        <!-- Nueva Categoría (con picker) -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-white">
                <span class="w-8 h-8 rounded-lg bg-purple-500/20 text-purple-400 flex items-center justify-center"><i class="fa-solid fa-plus text-sm"></i></span>
                Nueva Categoría
            </h2>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-2">Nombre</label>
                    <input type="text" name="nombre_cat" placeholder="Ej: Smart TV" class="w-full bg-slate-950 border border-slate-800 text-white p-3 rounded-xl focus:border-purple-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-2">Ícono</label>
                    <input type="hidden" name="icono_cat" id="c_icono" value="fa-solid fa-tag">
                    <div class="grid grid-cols-8 gap-1.5 max-h-28 overflow-y-auto pr-1">
                        <?php foreach ($ICONS as $cls => $label): ?>
                        <button type="button" onclick="pickIcon(this,'c_icono','c_preview_icon')" data-icon="<?= $cls ?>"
                            class="icon-btn aspect-square bg-slate-800 hover:bg-slate-700 rounded-lg flex items-center justify-center text-slate-400 hover:text-white transition text-sm <?= $cls==='fa-solid fa-tag' ? 'active' : '' ?>"
                            title="<?= $label ?>"><i class="<?= $cls ?>"></i></button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-400 mb-2">Color</label>
                    <input type="hidden" name="color_bg"   id="c_bg"   value="bg-slate-700">
                    <input type="hidden" name="color_text" id="c_text" value="text-white">
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($COLORS as $i => $c): ?>
                        <button type="button" onclick="pickColor(this,'c_bg','c_text','c_preview_dot')"
                            data-bg="<?= $c['bg'] ?>" data-text="<?= $c['text'] ?>"
                            class="color-dot w-7 h-7 rounded-full border-2 border-transparent transition <?= $i===9 ? 'active' : '' ?>"
                            style="background:<?= $c['hex'] ?>;" title="<?= $c['name'] ?>"></button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3 bg-slate-950 rounded-xl border border-slate-800">
                    <div id="c_preview_dot" class="w-10 h-10 bg-slate-700 rounded-xl flex items-center justify-center text-white text-lg shrink-0">
                        <i id="c_preview_icon" class="fa-solid fa-tag"></i>
                    </div>
                    <span class="text-slate-400 text-xs">Vista previa del ícono en el catálogo</span>
                </div>
                <button type="submit" name="nueva_categoria" class="w-full bg-purple-600 hover:bg-purple-500 text-white font-bold py-3 rounded-xl transition">Crear Categoría</button>
            </form>
        </div>
    </div>

    <!-- ── SECCIÓN 2: GESTIÓN DE CATEGORÍAS ── -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl mb-10">
        <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-white">
            <span class="w-8 h-8 rounded-lg bg-teal-500/20 text-teal-400 flex items-center justify-center"><i class="fa-solid fa-folder-tree text-sm"></i></span>
            Gestión de Categorías
            <span class="ml-auto text-xs font-normal text-slate-500"><?= count($cats_list) ?> categorías</span>
        </h2>
        <?php if (empty($cats_list)): ?>
            <p class="text-slate-600 text-sm text-center py-6">No hay categorías.</p>
        <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($cats_list as $cat): ?>
            <div class="flex items-center gap-3 p-3 bg-slate-950/60 border border-slate-800 rounded-xl hover:border-slate-700 transition">
                <div class="w-10 h-10 <?= htmlspecialchars($cat['color_bg']) ?> rounded-xl flex items-center justify-center <?= htmlspecialchars($cat['color_text']) ?> shrink-0">
                    <i class="<?= htmlspecialchars($cat['icono']) ?> text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-white truncate"><?= htmlspecialchars($cat['nombre']) ?></p>
                    <p class="text-xs text-slate-500"><?= $cat['flyer_count'] ?> producto(s)</p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <button onclick="openEditCat(<?= $cat['id'] ?>, '<?= htmlspecialchars(addslashes($cat['nombre'])) ?>', '<?= $cat['icono'] ?>', '<?= $cat['color_bg'] ?>', '<?= $cat['color_text'] ?>')"
                        class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-xs font-semibold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-pen-to-square"></i> Editar
                    </button>
                    <form method="POST" onsubmit="return confirm('¿Eliminar categoría «<?= htmlspecialchars(addslashes($cat['nombre'])) ?>»?')">
                        <input type="hidden" name="cat_id" value="<?= $cat['id'] ?>">
                        <button type="submit" name="eliminar_categoria" class="px-3 py-1.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 rounded-lg text-xs font-semibold transition flex items-center gap-1.5">
                            <i class="fa-solid fa-trash"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- ── SECCIÓN 3: GESTIÓN DE FLYERS ── -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl mb-10">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h2 class="text-xl font-bold flex items-center gap-2 text-white">
                <span class="w-8 h-8 rounded-lg bg-orange-500/20 text-orange-400 flex items-center justify-center"><i class="fa-solid fa-photo-film text-sm"></i></span>
                Gestión de Flyers
                <span class="text-xs font-normal text-slate-500"><?= $total_fly ?> total</span>
            </h2>
            <!-- Filtro por categoría -->
            <form method="GET" class="flex items-center gap-2">
                <select name="cat" onchange="this.form.submit()" class="bg-slate-950 border border-slate-800 text-white text-sm p-2 rounded-lg outline-none focus:border-orange-500">
                    <option value="0" <?= !$cat_filter ? 'selected' : '' ?>>Todas las categorías</option>
                    <?php foreach ($cats_list as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $cat_filter == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($cat_filter): ?><a href="admin.php" class="text-xs text-slate-500 hover:text-white transition">× Limpiar</a><?php endif; ?>
            </form>
        </div>

        <?php if (!$all_flyers || $all_flyers->num_rows === 0): ?>
            <p class="text-slate-600 text-sm text-center py-10">No hay flyers en esta categoría.</p>
        <?php else: ?>
        <div class="space-y-2">
            <?php while ($f = $all_flyers->fetch_assoc()): ?>
            <div class="flex items-center gap-3 p-2.5 bg-slate-950/50 border border-slate-800 rounded-xl hover:border-slate-700 transition group">
                <!-- Thumbnail -->
                <div class="w-12 h-12 rounded-lg bg-slate-800 bg-cover bg-center shrink-0 border border-slate-700 cursor-pointer"
                     style="background-image:url('<?= htmlspecialchars($f['imagen_url']) ?>');"
                     onclick="window.open('<?= htmlspecialchars($f['imagen_url']) ?>','_blank')"></div>
                <!-- Info -->
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate">
                        <?= htmlspecialchars($f['titulo']) ?>
                        <?php if ($f['destacado']): ?><span class="ml-1 text-yellow-400 text-xs">⭐</span><?php endif; ?>
                        <?php if ($f['sin_stock']): ?><span class="ml-1 text-red-400 text-xs font-normal">(sin stock)</span><?php endif; ?>
                    </p>
                    <p class="text-xs text-slate-500">
                        <?= htmlspecialchars($f['cat_nombre']) ?> · ID <?= $f['id'] ?>
                        <?php if (($f['grupo_count'] ?? 0) > 1): ?> · <span class="text-blue-400"><i class="fa-solid fa-images"></i> <?= $f['grupo_count'] ?> fotos</span><?php endif; ?>
                    </p>
                </div>
                <!-- Acciones -->
                <div class="flex gap-1.5 shrink-0 opacity-70 group-hover:opacity-100 transition">
                    <!-- Reordenar -->
                    <?php if ($cat_filter): ?>
                    <form method="POST" class="inline">
                        <input type="hidden" name="flyer_id"  value="<?= $f['id'] ?>">
                        <input type="hidden" name="cat_id"    value="<?= $f['categoria_id'] ?>">
                        <input type="hidden" name="direccion" value="up">
                        <button type="submit" name="reordenar_flyer" class="w-8 h-8 bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white rounded-lg transition text-xs" title="Subir">
                            <i class="fa-solid fa-arrow-up"></i>
                        </button>
                    </form>
                    <form method="POST" class="inline">
                        <input type="hidden" name="flyer_id"  value="<?= $f['id'] ?>">
                        <input type="hidden" name="cat_id"    value="<?= $f['categoria_id'] ?>">
                        <input type="hidden" name="direccion" value="down">
                        <button type="submit" name="reordenar_flyer" class="w-8 h-8 bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white rounded-lg transition text-xs" title="Bajar">
                            <i class="fa-solid fa-arrow-down"></i>
                        </button>
                    </form>
                    <?php else: ?>
                    <span class="text-xs text-slate-600 hidden sm:flex items-center px-2">Filtrá por cat. para reordenar</span>
                    <?php endif; ?>
                    <!-- Destacado -->
                    <form method="POST" class="inline">
                        <input type="hidden" name="flyer_id" value="<?= $f['id'] ?>">
                        <button type="submit" name="toggle_destacado"
                            class="w-8 h-8 <?= $f['destacado'] ? 'bg-yellow-500/30 text-yellow-400' : 'bg-slate-800 text-slate-500 hover:text-yellow-400' ?> rounded-lg transition text-xs"
                            title="<?= $f['destacado'] ? 'Quitar destacado' : 'Marcar como destacado' ?>">
                            <i class="fa-solid fa-star"></i>
                        </button>
                    </form>
                    <!-- Sin stock -->
                    <form method="POST" class="inline">
                        <input type="hidden" name="flyer_id" value="<?= $f['id'] ?>">
                        <button type="submit" name="toggle_sin_stock"
                            class="w-8 h-8 <?= $f['sin_stock'] ? 'bg-red-500/30 text-red-400' : 'bg-slate-800 text-slate-500 hover:text-red-400' ?> rounded-lg transition text-xs"
                            title="<?= $f['sin_stock'] ? 'Restaurar stock' : 'Marcar sin stock' ?>">
                            <i class="fa-solid fa-box-open"></i>
                        </button>
                    </form>
                    <!-- Editar título -->
                    <button onclick="openEditFlyer(<?= $f['id'] ?>, '<?= htmlspecialchars(addslashes($f['titulo'])) ?>')"
                        class="w-8 h-8 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 rounded-lg transition text-xs" title="Editar título">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <!-- Eliminar -->
                    <form method="POST" class="inline" onsubmit="return confirm('¿Eliminar este flyer? Esta acción no se puede deshacer.')">
                        <input type="hidden" name="flyer_id" value="<?= $f['id'] ?>">
                        <button type="submit" name="eliminar_flyer" class="w-8 h-8 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition text-xs" title="Eliminar">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- Paginación -->
        <?php if ($total_pages > 1): ?>
        <div class="flex justify-center items-center gap-2 mt-6">
            <?php if ($page > 1): ?>
            <a href="?page=<?= $page-1 ?>&cat=<?= $cat_filter ?>" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-sm transition">← Ant.</a>
            <?php endif; ?>
            <span class="text-slate-500 text-sm">Pág. <?= $page ?> de <?= $total_pages ?></span>
            <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page+1 ?>&cat=<?= $cat_filter ?>" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-sm transition">Sig. →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="text-center text-slate-600 text-sm pb-8">
        &copy; <?= date("Y") ?> Imperio Comercial — Panel de Administración
    </div>
</div>

<!-- ── MODAL: EDITAR FLYER ── -->
<div id="modal-flyer" class="fixed inset-0 z-50 hidden items-center justify-center p-4 modal-bg bg-black/70">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 w-full max-w-md shadow-2xl">
        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fa-solid fa-pen-to-square text-blue-400"></i> Editar Título del Flyer
        </h3>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="flyer_id" id="ef_id">
            <div>
                <label class="block text-sm font-semibold text-slate-400 mb-2">Nuevo título</label>
                <input type="text" name="titulo" id="ef_titulo" class="w-full bg-slate-950 border border-slate-700 text-white p-3 rounded-xl focus:border-blue-500 outline-none" required>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeModal('modal-flyer')" class="flex-1 bg-slate-800 hover:bg-slate-700 text-slate-300 py-3 rounded-xl font-semibold transition">Cancelar</button>
                <button type="submit" name="editar_flyer" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white py-3 rounded-xl font-bold transition">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- ── MODAL: EDITAR CATEGORÍA ── -->
<div id="modal-cat" class="fixed inset-0 z-50 hidden items-center justify-center p-4 modal-bg bg-black/70">
    <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 w-full max-w-md shadow-2xl max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fa-solid fa-folder-pen text-teal-400"></i> Editar Categoría
        </h3>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="cat_id" id="ec_id">
            <div>
                <label class="block text-sm font-semibold text-slate-400 mb-2">Nombre</label>
                <input type="text" name="nombre_cat" id="ec_nombre" class="w-full bg-slate-950 border border-slate-700 text-white p-3 rounded-xl focus:border-teal-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-400 mb-2">Ícono</label>
                <input type="hidden" name="icono_cat" id="ec_icono" value="fa-solid fa-tag">
                <div class="grid grid-cols-8 gap-1.5 max-h-28 overflow-y-auto pr-1">
                    <?php foreach ($ICONS as $cls => $label): ?>
                    <button type="button" onclick="pickIcon(this,'ec_icono','ec_preview_icon')" data-icon="<?= $cls ?>"
                        class="icon-btn aspect-square bg-slate-800 hover:bg-slate-700 rounded-lg flex items-center justify-center text-slate-400 hover:text-white transition text-sm"
                        title="<?= $label ?>"><i class="<?= $cls ?>"></i></button>
                    <?php endforeach; ?>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-400 mb-2">Color</label>
                <input type="hidden" name="color_bg"   id="ec_bg"   value="bg-slate-700">
                <input type="hidden" name="color_text" id="ec_text" value="text-white">
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($COLORS as $c): ?>
                    <button type="button" onclick="pickColor(this,'ec_bg','ec_text','ec_preview_dot')"
                        data-bg="<?= $c['bg'] ?>" data-text="<?= $c['text'] ?>"
                        class="color-dot w-7 h-7 rounded-full border-2 border-transparent transition"
                        style="background:<?= $c['hex'] ?>;" title="<?= $c['name'] ?>"></button>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="flex items-center gap-3 p-3 bg-slate-950 rounded-xl border border-slate-800">
                <div id="ec_preview_dot" class="w-10 h-10 bg-slate-700 rounded-xl flex items-center justify-center text-white text-lg shrink-0">
                    <i id="ec_preview_icon" class="fa-solid fa-tag"></i>
                </div>
                <span class="text-slate-400 text-xs">Vista previa</span>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeModal('modal-cat')" class="flex-1 bg-slate-800 hover:bg-slate-700 text-slate-300 py-3 rounded-xl font-semibold transition">Cancelar</button>
                <button type="submit" name="editar_categoria" class="flex-1 bg-teal-600 hover:bg-teal-500 text-white py-3 rounded-xl font-bold transition">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
// ── Validación de archivos ──────────────────────────────────────
const fileInput = document.getElementById('fileInput');
if (fileInput) {
    fileInput.addEventListener('change', function () {
        const nameEl   = document.getElementById('file-name');
        const warnEl   = document.getElementById('file-warning');
        if (this.files.length > 0) {
            nameEl.textContent = this.files.length === 1
                ? '1 archivo: ' + this.files[0].name
                : this.files.length + ' archivos seleccionados.';
            let tooBig = Array.from(this.files).find(f => f.size > 10 * 1024 * 1024);
            if (tooBig) {
                warnEl.innerHTML = `⚠️ "${tooBig.name}" pesa más de 10MB. Puede fallar si subís muchas juntas.`;
                warnEl.classList.remove('hidden');
            } else { warnEl.classList.add('hidden'); }
        } else { nameEl.textContent = ''; warnEl.classList.add('hidden'); }
    });
}

// ── Pickers ────────────────────────────────────────────────────
function pickIcon(btn, inputId, previewId) {
    const icon = btn.dataset.icon;
    document.getElementById(inputId).value = icon;
    document.getElementById(previewId).className = icon;
    btn.closest('.grid').querySelectorAll('.icon-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

function pickColor(btn, bgId, textId, previewDotId) {
    document.getElementById(bgId).value   = btn.dataset.bg;
    document.getElementById(textId).value = btn.dataset.text;
    // Update preview background
    const preview = document.getElementById(previewDotId);
    // Remove old Tailwind bg classes and apply inline style
    preview.style.backgroundColor = btn.style.backgroundColor;
    preview.style.color = btn.dataset.text === 'text-black' ? '#000' : '#fff';
    btn.closest('.flex').querySelectorAll('.color-dot').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}

// ── Modales ────────────────────────────────────────────────────
function openModal(id) {
    const el = document.getElementById(id);
    el.classList.remove('hidden');
    el.classList.add('flex');
}
function closeModal(id) {
    const el = document.getElementById(id);
    el.classList.add('hidden');
    el.classList.remove('flex');
}

// Cerrar modal al click fuera
['modal-flyer','modal-cat'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) closeModal(id);
    });
});

function openEditFlyer(id, titulo) {
    document.getElementById('ef_id').value     = id;
    document.getElementById('ef_titulo').value = titulo;
    openModal('modal-flyer');
}

function openEditCat(id, nombre, icono, colorBg, colorText) {
    document.getElementById('ec_id').value     = id;
    document.getElementById('ec_nombre').value = nombre;
    document.getElementById('ec_icono').value  = icono;
    document.getElementById('ec_bg').value     = colorBg;
    document.getElementById('ec_text').value   = colorText;

    // Actualizar preview ícono
    const previewIcon = document.getElementById('ec_preview_icon');
    previewIcon.className = icono;

    // Activar el ícono correcto en el grid
    document.querySelectorAll('#modal-cat .icon-btn').forEach(b => {
        b.classList.toggle('active', b.dataset.icon === icono);
    });

    // Actualizar preview color
    const previewDot = document.getElementById('ec_preview_dot');
    document.querySelectorAll('#modal-cat .color-dot').forEach(b => {
        const isActive = b.dataset.bg === colorBg;
        b.classList.toggle('active', isActive);
        if (isActive) {
            previewDot.style.backgroundColor = b.style.backgroundColor;
            previewDot.style.color = b.dataset.text === 'text-black' ? '#000' : '#fff';
        }
    });

    openModal('modal-cat');
}
</script>
</body>
</html>