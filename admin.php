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
    'orden'           => 'INT DEFAULT 0',
    'destacado'       => 'TINYINT(1) NOT NULL DEFAULT 0',
    'sin_stock'       => 'TINYINT(1) NOT NULL DEFAULT 0',
    'grupo_id'        => 'INT NULL',
    'es_principal'    => 'TINYINT(1) NOT NULL DEFAULT 1',
    'precio_cuota'    => 'DECIMAL(10,2) NULL',
    'cantidad_cuotas' => 'INT NULL',
    'descripcion'     => 'VARCHAR(300) NULL',
    'visible'         => 'TINYINT(1) NOT NULL DEFAULT 1',
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

// ── EDITAR FLYER (título + descripción + precio) ────────────────
if (isset($_POST['editar_flyer'])) {
    $id     = (int)$_POST['flyer_id'];
    $titulo = $conn->real_escape_string(trim($_POST['titulo']));
    $desc   = $conn->real_escape_string(trim($_POST['descripcion'] ?? ''));
    $cuota  = !empty($_POST['precio_cuota'])    ? (float)$_POST['precio_cuota']    : 'NULL';
    $cuotas = !empty($_POST['cantidad_cuotas']) ? (int)$_POST['cantidad_cuotas']   : 'NULL';
    $cuota_sql  = ($cuota  === 'NULL') ? 'NULL' : $cuota;
    $cuotas_sql = ($cuotas === 'NULL') ? 'NULL' : $cuotas;
    $desc_sql   = empty($desc) ? 'NULL' : "'$desc'";
    if ($conn->query("UPDATE flyers SET titulo='$titulo', descripcion=$desc_sql, precio_cuota=$cuota_sql, cantidad_cuotas=$cuotas_sql WHERE id=$id")) {
        $msg = "✅ Producto actualizado."; $msg_type = "success";
    } else { $msg = "❌ Error: " . $conn->error; $msg_type = "error"; }
}

// ── TOGGLE VISIBLE ──────────────────────────────────────────────
if (isset($_POST['toggle_visible'])) {
    $id = (int)$_POST['flyer_id'];
    $conn->query("UPDATE flyers SET visible = 1 - visible WHERE id = $id");
    $msg = "✅ Visibilidad actualizada."; $msg_type = "success";
}

// ── BULK ACTIONS ────────────────────────────────────────────────
if (isset($_POST['bulk_action']) && !empty($_POST['sel'])) {
    $sel_ids = array_map('intval', (array)$_POST['sel']);
    $in      = implode(',', $sel_ids);
    $action  = $_POST['bulk_action'];
    if ($action === 'delete') {
        $rows = $conn->query("SELECT imagen_url FROM flyers WHERE id IN ($in)");
        while ($r = $rows->fetch_assoc()) {
            if (!empty($r['imagen_url']) && file_exists($r['imagen_url'])) unlink($r['imagen_url']);
        }
        $conn->query("DELETE FROM flyers WHERE id IN ($in)");
        $msg = "✅ " . count($sel_ids) . " producto(s) eliminados."; $msg_type = "success";
    } elseif ($action === 'hide') {
        $conn->query("UPDATE flyers SET visible=0 WHERE id IN ($in)");
        $msg = "✅ " . count($sel_ids) . " producto(s) ocultados."; $msg_type = "success";
    } elseif ($action === 'show') {
        $conn->query("UPDATE flyers SET visible=1 WHERE id IN ($in)");
        $msg = "✅ " . count($sel_ids) . " producto(s) visibles."; $msg_type = "success";
    } elseif ($action === 'feature') {
        $conn->query("UPDATE flyers SET destacado=1 WHERE id IN ($in)");
        $msg = "✅ " . count($sel_ids) . " producto(s) destacados."; $msg_type = "success";
    }
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
    $desc_up    = $conn->real_escape_string(trim($_POST['descripcion'] ?? ''));
    $cuota_up   = !empty($_POST['precio_cuota'])    ? (float)$_POST['precio_cuota']  : null;
    $cuotas_up  = !empty($_POST['cantidad_cuotas']) ? (int)$_POST['cantidad_cuotas'] : null;
    $desc_sql   = empty($desc_up)  ? 'NULL' : "'$desc_up'";
    $cuota_sql  = ($cuota_up  === null) ? 'NULL' : $cuota_up;
    $cuotas_sql = ($cuotas_up === null) ? 'NULL' : $cuotas_up;
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
                            if ($conn->query("INSERT INTO flyers (categoria_id,titulo,imagen_url,orden,descripcion,precio_cuota,cantidad_cuotas$extra_cols) VALUES ($cat_id,'$titulo','$newfile',$new_orden,$desc_sql,$cuota_sql,$cuotas_sql$extra_vals)")) {
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

// ── REORDENAR FLYER (DRAG & DROP AJAX) ────────────────────────
if (isset($_POST['action']) && $_POST['action'] === 'reorder' && !empty($_POST['ids'])) {
    $ids = array_map('intval', explode(',', $_POST['ids']));
    foreach ($ids as $orden => $id) {
        $conn->query("UPDATE flyers SET orden=$orden WHERE id=$id");
    }
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

// ── CONSULTAS DE DISPLAY ─────────────────────────────────────────
$page        = max(1, (int)($_GET['page'] ?? 1));
$per_page    = 20;
$offset      = ($page - 1) * $per_page;
$cat_filter  = (int)($_GET['cat'] ?? 0);
$vis_filter  = isset($_GET['vis']) ? (int)$_GET['vis'] : -1; // -1=todos, 0=ocultos, 1=visibles
$q_search    = $conn->real_escape_string(trim($_GET['q'] ?? ''));
$where_parts = [];
if ($cat_filter) $where_parts[] = "flyers.categoria_id = $cat_filter";
if ($vis_filter >= 0) $where_parts[] = "flyers.visible = $vis_filter";
if ($q_search)   $where_parts[] = "flyers.titulo LIKE '%$q_search%'";
$where = $where_parts ? "WHERE " . implode(' AND ', $where_parts) : "";
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

// ── STAT COUNTS ──────────────────────────────────────────────────
$stat_flyers    = (int)$conn->query("SELECT COUNT(*) FROM flyers")->fetch_row()[0];
$stat_cats      = (int)$conn->query("SELECT COUNT(*) FROM categorias")->fetch_row()[0];
$stat_destacados= (int)$conn->query("SELECT COUNT(*) FROM flyers WHERE destacado=1")->fetch_row()[0];

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
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body { font-family: var(--font-body); background: var(--bg-base); color: var(--text-primary); }
        .icon-btn.active  { box-shadow: 0 0 0 2px #F97316; background: rgba(249,115,22,0.15); color: white; }
        .color-dot.active { box-shadow: 0 0 0 3px rgba(255,255,255,0.5), 0 0 0 5px #F97316; transform: scale(1.1); }
        .modal-bg { backdrop-filter: blur(8px); }
        select, input[type="text"], input[type="password"] {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.10);
            color: var(--text-primary);
        }
        select option { background: #1A1A2E; }
        select:focus, input[type="text"]:focus { border-color: var(--warm-500); outline: none; box-shadow: 0 0 0 3px rgba(249,115,22,0.15); }
    </style>
</head>
<body class="min-h-screen p-4 md:p-8">

<div class="max-w-5xl mx-auto">

    <!-- ── HEADER ── -->
    <div class="hero-gradient rounded-2xl p-6 mb-6 relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full opacity-20 animate-float pointer-events-none"
             style="background: radial-gradient(circle, #F97316, transparent)"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 btn-warm rounded-xl flex items-center justify-center font-bold text-xl"
                     style="font-family: var(--font-heading)">IC</div>
                <div>
                    <h1 class="text-2xl font-bold text-white leading-none" style="font-family: var(--font-heading)">Panel de Control</h1>
                    <p class="text-sm mt-0.5" style="color: rgba(248,250,252,0.60)">Gestión de Catálogo</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="index.php" target="_blank" class="glass-card px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2 hover:opacity-80"
                   style="color: var(--text-primary)">
                    <i class="fa-solid fa-eye"></i> Ver Web
                </a>
                <a href="logout.php" class="px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-2"
                   style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.25); color: #f87171">
                    <i class="fa-solid fa-right-from-bracket"></i> Salir
                </a>
            </div>
        </div>
    </div>

    <!-- ── STAT CARDS ── -->
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="stat-card">
            <div class="stat-number"><?= $stat_flyers ?></div>
            <div class="stat-label">Flyers</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $stat_cats ?></div>
            <div class="stat-label">Categorías</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $stat_destacados ?></div>
            <div class="stat-label">Destacados</div>
        </div>
    </div>

    <!-- ── ALERT ── -->
    <?php if ($msg): ?>
    <div class="border p-4 rounded-xl mb-6 flex items-center gap-3 <?= $msg_type === 'success' ? 'text-green-400' : 'text-red-400' ?>"
         style="background: <?= $msg_type === 'success' ? 'rgba(52,211,153,0.08)' : 'rgba(248,113,113,0.08)' ?>; border-color: <?= $msg_type === 'success' ? 'rgba(52,211,153,0.2)' : 'rgba(248,113,113,0.2)' ?>">
        <i class="fa-solid <?= $msg_type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation' ?> text-xl flex-shrink-0"></i>
        <span class="font-medium"><?= $msg ?></span>
    </div>
    <?php endif; ?>

    <!-- ── SECCIÓN 1: SUBIR + NUEVA CATEGORÍA ── -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">

        <!-- Subir Flyers -->
        <div class="glass-card p-6">
            <h2 class="text-xl font-bold mb-6 flex items-center gap-2" style="font-family: var(--font-heading); color: var(--text-primary)">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(249,115,22,0.15); color: var(--warm-400)"><i class="fa-solid fa-images text-sm"></i></span>
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
                    <label class="block text-sm font-semibold mb-2" style="color:var(--text-secondary)">Título</label>
                    <input type="text" name="titulo" placeholder="Ej: Smart TV Samsung 65&quot;" class="admin-input" required>
                    <p class="text-xs mt-1" style="color:var(--text-muted)">Se aplica a todas las fotos de esta subida.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2" style="color:var(--text-secondary)">Descripción <span style="color:var(--text-muted)">(opcional)</span></label>
                    <textarea name="descripcion" placeholder="Ej: 65 pulgadas, 4K, Smart TV, Android 13" maxlength="300" rows="2" class="admin-input resize-none" style="height:auto"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold mb-2" style="color:var(--text-secondary)">Precio cuota $</label>
                        <input type="number" name="precio_cuota" placeholder="8500" min="0" step="0.01" class="admin-input">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2" style="color:var(--text-secondary)">Cant. cuotas</label>
                        <input type="number" name="cantidad_cuotas" placeholder="12" min="1" max="120" class="admin-input">
                    </div>
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
                <button type="submit" name="subir_flyer" class="btn-warm w-full py-3.5 font-bold" style="border-radius: var(--radius-sm)">Publicar Todo</button>
            </form>
        </div>

        <!-- Nueva Categoría (con picker) -->
        <div class="glass-card p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center gap-2" style="font-family: var(--font-heading); color: var(--text-primary)">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(168,85,247,0.15); color: #c084fc"><i class="fa-solid fa-plus text-sm"></i></span>
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
                <button type="submit" name="nueva_categoria" class="btn-warm w-full py-3 font-bold" style="border-radius: var(--radius-sm); background: linear-gradient(135deg, #9333ea, #a855f7)">Crear Categoría</button>
            </form>
        </div>
    </div>

    <!-- ── SECCIÓN 2: GESTIÓN DE CATEGORÍAS ── -->
    <div class="glass-card p-6 mb-10">
        <h2 class="text-xl font-bold mb-6 flex items-center gap-2" style="font-family: var(--font-heading); color: var(--text-primary)">
            <span class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(45,212,191,0.15); color: #2dd4bf"><i class="fa-solid fa-folder-tree text-sm"></i></span>
            Gestión de Categorías
            <span class="ml-auto text-xs font-normal" style="color: var(--text-muted)"><?= count($cats_list) ?> categorías</span>
        </h2>
        <?php if (empty($cats_list)): ?>
            <p class="text-slate-600 text-sm text-center py-6">No hay categorías.</p>
        <?php else: ?>
        <div class="space-y-3">
            <?php foreach ($cats_list as $cat): ?>
            <div class="flex items-center gap-3 p-3 rounded-xl transition" style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle)" onmouseover="this.style.borderColor='var(--border-soft)'" onmouseout="this.style.borderColor='var(--border-subtle)'">
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
    <div class="glass-card p-6 mb-10" id="flyers-section">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h2 class="text-xl font-bold flex items-center gap-2" style="font-family: var(--font-heading); color: var(--text-primary)">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(249,115,22,0.15); color: var(--warm-400)"><i class="fa-solid fa-photo-film text-sm"></i></span>
                Gestión de Flyers
                <span class="text-xs font-normal" style="color: var(--text-muted)"><?= $total_fly ?> total</span>
            </h2>
            <?php if ($cat_filter): ?>
            <p class="text-xs mt-1" style="color: var(--text-muted)"><i class="fa-solid fa-grip-dots-vertical mr-1"></i> Arrastrá los items para reordenar</p>
            <?php endif; ?>
            <!-- Filtros -->
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <input type="text" name="q" value="<?= htmlspecialchars($q_search) ?>" placeholder="Buscar producto..."
                       class="admin-input text-sm py-2 px-3 rounded-lg" style="width:180px"
                       onchange="this.form.submit()">
                <select name="cat" onchange="this.form.submit()" class="admin-input text-sm py-2 px-3 rounded-lg" style="width:auto">
                    <option value="0" <?= !$cat_filter ? 'selected' : '' ?>>Todas las categorías</option>
                    <?php foreach ($cats_list as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $cat_filter == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="vis" onchange="this.form.submit()" class="admin-input text-sm py-2 px-3 rounded-lg" style="width:auto">
                    <option value="-1" <?= $vis_filter < 0 ? 'selected' : '' ?>>Todos</option>
                    <option value="1"  <?= $vis_filter === 1 ? 'selected' : '' ?>>Visibles</option>
                    <option value="0"  <?= $vis_filter === 0 ? 'selected' : '' ?>>Ocultos</option>
                </select>
                <?php if ($cat_filter || $q_search || $vis_filter >= 0): ?>
                <a href="admin.php" class="text-xs hover:opacity-70 transition" style="color:var(--text-muted)">× Limpiar</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if (!$all_flyers || $all_flyers->num_rows === 0): ?>
            <p class="text-slate-600 text-sm text-center py-10">No hay flyers en esta categoría.</p>
        <?php else: ?>
        <!-- Bulk toolbar -->
        <form method="POST" id="bulk-form">
        <div class="flex flex-wrap items-center gap-2 mb-4 p-3 rounded-xl" style="background:rgba(255,255,255,0.03);border:1px solid var(--border-subtle)" id="bulk-toolbar">
            <label class="flex items-center gap-2 text-sm cursor-pointer" style="color:var(--text-secondary)">
                <input type="checkbox" id="sel-all" class="accent-orange-500 w-4 h-4">
                Seleccionar todo
            </label>
            <span class="text-xs px-2" style="color:var(--text-muted)">|</span>
            <button type="submit" name="bulk_action" value="hide" class="text-xs px-3 py-1.5 rounded-lg transition" style="background:rgba(255,255,255,0.06);color:var(--text-secondary)" onclick="return bulkConfirm('ocultar')">
                <i class="fa-solid fa-eye-slash mr-1"></i>Ocultar
            </button>
            <button type="submit" name="bulk_action" value="show" class="text-xs px-3 py-1.5 rounded-lg transition" style="background:rgba(255,255,255,0.06);color:var(--text-secondary)">
                <i class="fa-solid fa-eye mr-1"></i>Mostrar
            </button>
            <button type="submit" name="bulk_action" value="feature" class="text-xs px-3 py-1.5 rounded-lg transition" style="background:rgba(251,191,36,0.1);color:#fbbf24">
                <i class="fa-solid fa-star mr-1"></i>Destacar
            </button>
            <button type="submit" name="bulk_action" value="delete" class="text-xs px-3 py-1.5 rounded-lg transition" style="background:rgba(248,113,113,0.1);color:#f87171" onclick="return bulkConfirm('eliminar')">
                <i class="fa-solid fa-trash mr-1"></i>Eliminar
            </button>
        </div>

        <div class="space-y-2" id="flyers-list">
            <?php while ($f = $all_flyers->fetch_assoc()): ?>
            <div class="draggable-item flex items-center gap-3 p-2.5 rounded-xl transition group <?= $f['visible'] ? '' : 'opacity-50' ?>"
                 data-flyer-id="<?= $f['id'] ?>"
                 draggable="true"
                 style="background: rgba(255,255,255,0.03); border: 1px solid <?= $f['visible'] ? 'var(--border-subtle)' : 'rgba(248,113,113,0.2)' ?>">
                <!-- Checkbox bulk -->
                <input type="checkbox" name="sel[]" value="<?= $f['id'] ?>" class="sel-check accent-orange-500 w-4 h-4 shrink-0">
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
                    <!-- Visible -->
                    <form method="POST" class="inline">
                        <input type="hidden" name="flyer_id" value="<?= $f['id'] ?>">
                        <button type="submit" name="toggle_visible"
                            class="w-8 h-8 <?= $f['visible'] ? 'bg-slate-800 text-slate-400 hover:text-white' : 'bg-red-500/20 text-red-400' ?> rounded-lg transition text-xs"
                            title="<?= $f['visible'] ? 'Ocultar del catálogo' : 'Mostrar en catálogo' ?>">
                            <i class="fa-solid <?= $f['visible'] ? 'fa-eye' : 'fa-eye-slash' ?>"></i>
                        </button>
                    </form>
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
                    <button onclick="openEditFlyer(<?= $f['id'] ?>, '<?= htmlspecialchars(addslashes($f['titulo'])) ?>', '<?= htmlspecialchars(addslashes($f['descripcion'] ?? '')) ?>', '<?= $f['precio_cuota'] ?? '' ?>', '<?= $f['cantidad_cuotas'] ?? '' ?>')"
                        class="w-8 h-8 rounded-lg transition text-xs" style="background:rgba(99,102,241,0.15);color:#818cf8" title="Editar producto">
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

        </div><!-- /flyers-list -->
        </form><!-- /bulk-form -->

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

    <div class="text-center text-sm pb-8" style="color: var(--text-muted)">
        &copy; <?= date("Y") ?> Imperio Comercial — Panel de Administración
    </div>
</div>

<!-- ── MODAL: EDITAR FLYER ── -->
<div id="modal-flyer" class="fixed inset-0 z-50 hidden items-center justify-center p-4 modal-bg" style="background: rgba(0,0,0,0.7)">
    <div class="glass-card p-6 w-full max-w-md shadow-2xl" style="border-color: var(--border-warm)">
        <h3 class="text-lg font-bold mb-4 flex items-center gap-2" style="font-family: var(--font-heading); color: var(--text-primary)">
            <i class="fa-solid fa-pen-to-square" style="color: var(--warm-400)"></i> Editar Título del Flyer
        </h3>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="flyer_id" id="ef_id">
            <div>
                <label class="block text-sm font-semibold mb-2" style="color: var(--text-secondary)">Título</label>
                <input type="text" name="titulo" id="ef_titulo" class="admin-input" required>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2" style="color: var(--text-secondary)">Descripción <span style="color:var(--text-muted)">(opcional)</span></label>
                <textarea name="descripcion" id="ef_desc" maxlength="300" rows="2" class="admin-input resize-none" style="height:auto" placeholder="Ej: 65 pulgadas, 4K, Smart TV"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold mb-2" style="color: var(--text-secondary)">Precio cuota $</label>
                    <input type="number" name="precio_cuota" id="ef_cuota" placeholder="8500" min="0" step="0.01" class="admin-input">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2" style="color: var(--text-secondary)">Cant. cuotas</label>
                    <input type="number" name="cantidad_cuotas" id="ef_cuotas" placeholder="12" min="1" max="120" class="admin-input">
                </div>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeModal('modal-flyer')" class="flex-1 py-3 rounded-xl font-semibold transition"
                        style="background: var(--bg-card); border: 1px solid var(--border-soft); color: var(--text-secondary)">Cancelar</button>
                <button type="submit" name="editar_flyer" class="flex-1 btn-warm py-3 font-bold">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- ── MODAL: EDITAR CATEGORÍA ── -->
<div id="modal-cat" class="fixed inset-0 z-50 hidden items-center justify-center p-4 modal-bg" style="background: rgba(0,0,0,0.7)">
    <div class="glass-card p-6 w-full max-w-md shadow-2xl max-h-[90vh] overflow-y-auto" style="border-color: rgba(168,85,247,0.4)">
        <h3 class="text-lg font-bold mb-4 flex items-center gap-2" style="font-family: var(--font-heading); color: var(--text-primary)">
            <i class="fa-solid fa-folder-pen" style="color: #2dd4bf"></i> Editar Categoría
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
                <button type="button" onclick="closeModal('modal-cat')" class="flex-1 py-3 rounded-xl font-semibold transition"
                        style="background: var(--bg-card); border: 1px solid var(--border-soft); color: var(--text-secondary)">Cancelar</button>
                <button type="submit" name="editar_categoria" class="flex-1 btn-warm py-3 font-bold"
                        style="background: linear-gradient(135deg, #0d9488, #2dd4bf)">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
// ── Drag & Drop Reordenamiento ─────────────────────────────────
(function() {
    let dragSrc = null;

    function showToastAdmin(msg) {
        const t = document.createElement('div');
        t.textContent = msg;
        t.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(20px);background:linear-gradient(135deg,#F97316,#FBBF24);color:white;padding:10px 24px;border-radius:999px;font-weight:600;font-size:14px;z-index:9999;transition:all 0.3s cubic-bezier(0.34,1.56,0.64,1);opacity:0';
        document.body.appendChild(t);
        requestAnimationFrame(() => { t.style.opacity='1'; t.style.transform='translateX(-50%) translateY(0)'; });
        setTimeout(() => { t.style.opacity='0'; t.style.transform='translateX(-50%) translateY(10px)'; setTimeout(() => t.remove(), 300); }, 2200);
    }

    document.addEventListener('dragstart', function(e) {
        const item = e.target.closest('[data-flyer-id]');
        if (!item) return;
        dragSrc = item;
        setTimeout(() => item.classList.add('dragging'), 0);
        e.dataTransfer.effectAllowed = 'move';
    });

    document.addEventListener('dragend', function() {
        if (dragSrc) { dragSrc.classList.remove('dragging'); dragSrc = null; }
        document.querySelectorAll('[data-flyer-id]').forEach(el => el.classList.remove('drag-over'));
    });

    document.addEventListener('dragover', function(e) {
        const item = e.target.closest('[data-flyer-id]');
        if (!item || item === dragSrc) return;
        e.preventDefault();
        document.querySelectorAll('[data-flyer-id]').forEach(el => el.classList.remove('drag-over'));
        item.classList.add('drag-over');
    });

    document.addEventListener('dragleave', function(e) {
        const item = e.target.closest('[data-flyer-id]');
        if (item) item.classList.remove('drag-over');
    });

    document.addEventListener('drop', function(e) {
        const target = e.target.closest('[data-flyer-id]');
        if (!target || !dragSrc || target === dragSrc) return;
        e.preventDefault();
        target.classList.remove('drag-over');
        dragSrc.classList.remove('dragging');

        const parent = target.parentNode;
        const items  = [...parent.children];
        const srcIdx = items.indexOf(dragSrc);
        const tgtIdx = items.indexOf(target);
        if (srcIdx < tgtIdx) target.after(dragSrc);
        else target.before(dragSrc);

        const ids = [...parent.querySelectorAll('[data-flyer-id]')].map(el => el.dataset.flyerId).join(',');
        const fd  = new URLSearchParams({ action: 'reorder', ids });
        fetch('admin.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: fd })
            .then(r => r.json())
            .then(d => { if (d.success) showToastAdmin('Orden guardado ✓'); })
            .catch(() => {});

        dragSrc = null;
    });
})();

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

function openEditFlyer(id, titulo, desc, cuota, cuotas) {
    document.getElementById('ef_id').value     = id;
    document.getElementById('ef_titulo').value = titulo;
    document.getElementById('ef_desc').value   = desc   || '';
    document.getElementById('ef_cuota').value  = cuota  || '';
    document.getElementById('ef_cuotas').value = cuotas || '';
    openModal('modal-flyer');
}

// ── Bulk select ────────────────────────────────────────────────
const selAll = document.getElementById('sel-all');
if (selAll) {
    selAll.addEventListener('change', function() {
        document.querySelectorAll('.sel-check').forEach(cb => cb.checked = this.checked);
    });
}
function bulkConfirm(accion) {
    const n = document.querySelectorAll('.sel-check:checked').length;
    if (n === 0) { alert('Seleccioná al menos un producto.'); return false; }
    return confirm(`¿${accion.charAt(0).toUpperCase() + accion.slice(1)} ${n} producto(s)?`);
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