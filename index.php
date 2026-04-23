<?php
require 'db.php';

// ── SETUP: Columnas nuevas (try/catch por si el usuario DB no tiene ALTER privilege) ──
$newCols = [
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
$colsExist = [];
foreach ($newCols as $col => $def) {
    try {
        $r = $conn->query("SHOW COLUMNS FROM flyers LIKE '$col'");
        $colsExist[$col] = ($r && $r->num_rows > 0);
        if (!$colsExist[$col]) {
            $conn->query("ALTER TABLE flyers ADD COLUMN $col $def");
            if ($col === 'orden') $conn->query('UPDATE flyers SET orden = id WHERE orden = 0 OR orden IS NULL');
            $colsExist[$col] = true;
        }
    } catch (\Exception $e) { $colsExist[$col] = false; }
}

// ── CATEGORÍAS con conteo de productos ───────────────────────────
$result_cats = $conn->query(
    "SELECT c.*, COUNT(f.id) as producto_count
     FROM categorias c
     LEFT JOIN flyers f ON f.categoria_id = c.id AND f.es_principal = 1 AND f.visible = 1
     GROUP BY c.id ORDER BY c.nombre ASC"
);
$categories  = [];
while ($row = $result_cats->fetch_assoc()) $categories[] = $row;

// ── FLYERS PRINCIPALES (ORDER BY dinámico: safe si columnas aún no existen) ──
$orderParts = [];
if ($colsExist['destacado']) $orderParts[] = 'destacado DESC';
if ($colsExist['orden'])     $orderParts[] = 'orden ASC';
$orderParts[] = 'id DESC';
$orderBy  = implode(', ', $orderParts);
$whereEsP = $colsExist['es_principal'] ? 'WHERE es_principal = 1 AND visible = 1' : '';
$result_flyers = $conn->query("SELECT * FROM flyers $whereEsP ORDER BY $orderBy");
$flyers = [];
while ($row = $result_flyers->fetch_assoc()) $flyers[] = $row;

// ── IMÁGENES EXTRA (para galería lightbox) ────────────────────────
$allImages = [];
if ($colsExist['grupo_id']) {
    $orderGrupo = $colsExist['orden'] ? 'orden ASC, id ASC' : 'id ASC';
    $result_extra = $conn->query("SELECT id, grupo_id, imagen_url FROM flyers WHERE grupo_id IS NOT NULL ORDER BY $orderGrupo");
    while ($row = $result_extra->fetch_assoc()) $allImages[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Imperio Comercial - Catálogo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="antialiased" style="background: var(--bg-base); color: var(--text-primary)">

    <div id="app" class="max-w-4xl mx-auto min-h-screen flex flex-col relative border-x"
         style="border-color: var(--border-subtle)">

        <!-- Header -->
        <header class="sticky top-0 z-50 px-4 md:px-6 py-4 flex justify-between items-center transition-all border-b"
                style="background: rgba(26,26,46,0.90); backdrop-filter: blur(16px); border-color: var(--border-soft)">
            <div onclick="app.changeView('home')" class="flex items-center gap-3 cursor-pointer">
                <img src="img/icono.jpg" alt="Logo" class="w-10 h-10 rounded-xl object-contain p-0.5 border"
                     style="border-color: var(--border-soft); background: var(--bg-card)">
                <h1 class="text-lg md:text-xl font-bold tracking-tight"
                    style="font-family: var(--font-heading); color: var(--text-primary)">Imperio Comercial</h1>
            </div>
            <div class="flex items-center gap-3 md:gap-4">
                <button onclick="app.toggleSearch(true)" class="p-2 rounded-full transition active:scale-95"
                        style="color: var(--text-secondary)">
                    <i class="fa-solid fa-magnifying-glass text-lg"></i>
                </button>
                <div class="relative">
                    <i class="fa-regular fa-bell text-xl" style="color: var(--text-secondary)"></i>
                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2"
                          style="border-color: var(--bg-base)"></span>
                </div>
            </div>
        </header>

        <!-- Contenido Dinámico -->
        <main id="view-container" class="flex-1 p-4 md:p-6 pb-28 md:pb-24 w-full"></main>

        <!-- LIGHTBOX con galería -->
        <div id="lightbox" class="fixed inset-0 z-[80] bg-black/95 hidden items-center justify-center p-4 transition-all duration-300 opacity-0" onclick="app.closeLightbox()">
            <!-- Cerrar -->
            <button class="absolute top-4 right-4 text-white/70 hover:text-white text-3xl z-20 w-10 h-10 flex items-center justify-center" onclick="event.stopPropagation(); app.closeLightbox()">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <!-- Prev -->
            <button id="lb-prev" class="hidden absolute left-3 top-1/2 -translate-y-1/2 z-20 w-11 h-11 md:w-14 md:h-14 bg-white/10 hover:bg-white/20 rounded-full items-center justify-center text-white text-lg transition active:scale-90"
                onclick="event.stopPropagation(); app.prevImage()">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <!-- Imagen -->
            <img id="lightbox-img" src="" class="max-w-full max-h-[82vh] rounded-lg shadow-2xl object-contain transition-transform duration-300 scale-95" onclick="event.stopPropagation()">
            <!-- Next -->
            <button id="lb-next" class="hidden absolute right-3 top-1/2 -translate-y-1/2 z-20 w-11 h-11 md:w-14 md:h-14 bg-white/10 hover:bg-white/20 rounded-full items-center justify-center text-white text-lg transition active:scale-90"
                onclick="event.stopPropagation(); app.nextImage()">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
            <!-- Contador -->
            <div id="lb-counter" class="hidden absolute bottom-5 left-1/2 -translate-x-1/2 bg-black/60 text-white text-xs px-3 py-1.5 rounded-full backdrop-blur-sm font-semibold"></div>
        </div>

        <!-- Overlay de Búsqueda -->
        <div id="search-overlay" class="fixed inset-0 z-[60] hidden flex flex-col p-4 md:p-6 view-transition"
             style="background: var(--bg-base)">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl md:text-2xl font-bold" style="font-family: var(--font-heading); color: var(--text-primary)">Buscar</h2>
                <button onclick="app.toggleSearch(false)" class="p-2 rounded-full transition" style="background: var(--bg-card); color: var(--text-secondary)">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="relative mb-6">
                <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2" style="color: var(--text-muted)"></i>
                <input type="text" id="search-input" placeholder="¿Qué buscás?"
                       class="admin-input rounded-2xl py-3.5 pl-12 pr-4 text-base md:text-lg">
            </div>
            <div id="search-results" class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4 overflow-y-auto pb-20"></div>
        </div>

        <!-- Bottom Sheet — Detalle de Producto -->
        <div id="product-sheet" class="fixed inset-0 z-[75] hidden" onclick="app.closeSheet(event)">
            <!-- Backdrop -->
            <div class="absolute inset-0" style="background:rgba(0,0,0,0.65);backdrop-filter:blur(4px)"></div>
            <!-- Panel -->
            <div id="sheet-panel" class="absolute bottom-0 left-0 right-0 max-w-4xl mx-auto rounded-t-3xl overflow-hidden"
                 style="background:var(--bg-surface);border-top:1px solid var(--border-soft);transform:translateY(100%);transition:transform 0.35s cubic-bezier(0.16,1,0.3,1);max-height:90vh;overflow-y:auto;padding-bottom:6rem"
                 onclick="event.stopPropagation()">
                <!-- Handle -->
                <div class="sticky top-0 pt-3 pb-1 flex justify-center" style="background:var(--bg-surface)">
                    <div class="w-10 h-1 rounded-full" style="background:rgba(255,255,255,0.15)"></div>
                </div>
                <div class="px-5 pb-6">
                    <!-- Imagen principal -->
                    <div class="relative mb-4">
                        <img id="sheet-img" src="" loading="lazy" alt=""
                             class="w-full rounded-2xl object-cover" style="max-height:280px">
                        <!-- Botón galería (solo si hay más fotos) -->
                        <button id="sheet-gallery-btn" onclick="app.openLightboxFromSheet()"
                                class="absolute bottom-3 right-3 glass-card px-3 py-1.5 text-xs font-semibold hidden"
                                style="color:var(--text-primary)">
                            <i class="fa-solid fa-images mr-1"></i><span id="sheet-gallery-count"></span>
                        </button>
                    </div>
                    <!-- Badge + título -->
                    <p class="text-xs font-bold uppercase tracking-widest gradient-text mb-1">Oferta</p>
                    <h2 id="sheet-title" class="text-xl font-bold mb-2" style="font-family:var(--font-heading);color:var(--text-primary)"></h2>
                    <!-- Descripción -->
                    <p id="sheet-desc" class="text-sm mb-4 hidden leading-relaxed" style="color:var(--text-secondary)"></p>
                    <!-- Precio -->
                    <div id="sheet-price-box" class="glass-card p-4 mb-5 hidden">
                        <div id="sheet-cuota" class="gradient-text text-2xl font-bold mb-0.5" style="font-family:var(--font-heading)"></div>
                        <div id="sheet-total" class="text-xs" style="color:var(--text-secondary)"></div>
                    </div>
                    <!-- Sin stock -->
                    <div id="sheet-sinstock" class="hidden mb-4 p-3 rounded-xl text-center text-sm font-semibold"
                         style="background:rgba(248,113,113,0.1);border:1px solid rgba(248,113,113,0.2);color:#f87171">
                        <i class="fa-solid fa-box-open mr-2"></i>Sin Stock — consultá disponibilidad
                    </div>
                    <!-- Botones CTA -->
                    <button id="sheet-wa-btn" onclick="app.sheetWhatsApp()"
                            class="btn-warm w-full py-4 font-bold text-base mb-3">
                        <i class="fa-brands fa-whatsapp text-xl"></i> Consultar por WhatsApp
                    </button>
                    <button id="sheet-share-btn" onclick="app.sheetShare()"
                            class="glass-card glass-card-hover w-full py-3 font-semibold text-sm"
                            style="color:var(--text-secondary)">
                        <i class="fa-solid fa-share-nodes mr-2"></i>Compartir
                    </button>
                </div>
            </div>
        </div>

        <!-- Toast -->
        <div id="toast-notification" class="fixed bottom-24 left-1/2 -translate-x-1/2 z-[70] px-6 py-3 rounded-full shadow-2xl flex items-center gap-3 toast whitespace-nowrap w-max max-w-[90%]"
             style="background: var(--warm-gradient); color: white; border: 1px solid rgba(255,255,255,0.2)">
            <i class="fa-solid fa-circle-check"></i>
            <span id="toast-message" class="font-bold text-sm truncate"></span>
        </div>

        <!-- Navegación Inferior -->
        <nav class="fixed bottom-0 left-0 right-0 max-w-4xl mx-auto border-t h-20 flex justify-around items-center px-2 md:px-4 z-40"
             style="background: rgba(26,26,46,0.92); backdrop-filter: blur(20px); border-color: var(--border-soft)">
            <button onclick="app.changeView('home')" id="nav-home" class="flex flex-col items-center gap-1 w-16 active:scale-95 transition-transform"
                    style="color: var(--warm-500)">
                <i class="fa-solid fa-house text-xl mb-0.5"></i>
                <span class="text-[10px] font-bold">Tienda</span>
            </button>
            <button onclick="app.changeView('favorites')" id="nav-favorites" class="flex flex-col items-center gap-1 w-16 active:scale-95 transition-transform hover:opacity-80"
                    style="color: var(--text-muted)">
                <i class="fa-solid fa-heart text-xl mb-0.5"></i>
                <span class="text-[10px] font-bold">Favoritos</span>
            </button>
            <button onclick="app.contactWhatsApp()" class="relative w-14 h-14 md:w-16 md:h-16 bg-green-600 rounded-full -mt-8 md:-mt-10 border-4 flex items-center justify-center text-white text-2xl md:text-3xl hover:scale-105 active:scale-90 transition-all shadow-xl"
                    style="border-color: var(--bg-base)">
                <i class="fa-brands fa-whatsapp"></i>
            </button>
            <a href="requisitos.php" class="flex flex-col items-center gap-1 w-16 active:scale-95 transition-transform hover:opacity-80"
               style="color: var(--text-muted)">
                <i class="fa-solid fa-file-invoice-dollar text-xl mb-0.5"></i>
                <span class="text-[10px] font-bold">Requisitos</span>
            </a>
            <a href="login.php" class="flex flex-col items-center gap-1 w-16 active:scale-95 transition-transform hover:opacity-80"
               style="color: var(--text-muted)">
                <i class="fa-solid fa-user-gear text-xl mb-0.5"></i>
                <span class="text-[10px] font-bold">Admin</span>
            </a>
        </nav>
    </div>

    <script>
    // ── DATOS ─────────────────────────────────────────────────────
    const DB = {
        categories: <?php echo json_encode($categories); ?>,
        flyers:     <?php echo json_encode($flyers); ?>,       // solo principals
        allImages:  <?php echo json_encode($allImages); ?>     // para galería
    };

    // ── APP ───────────────────────────────────────────────────────
    const app = {
        state: {
            currentView:      'home',
            selectedCategory: null,
            catPage:          1,
            favorites:        JSON.parse(localStorage.getItem('ic_favs')) || [],
        },
        gallery:    [],
        galleryIdx: 0,
        PAGE_SIZE:  20,

        init() { this.render(); this.setupListeners(); },

        setupListeners() {
            document.getElementById('search-input').addEventListener('input', e => this.performSearch(e.target.value));
            // Swipe en lightbox
            let tsX = 0;
            document.getElementById('lightbox').addEventListener('touchstart', e => { tsX = e.touches[0].clientX; }, { passive: true });
            document.getElementById('lightbox').addEventListener('touchend', e => {
                const diff = tsX - e.changedTouches[0].clientX;
                if (Math.abs(diff) > 50) { diff > 0 ? this.nextImage() : this.prevImage(); }
            });
        },

        changeView(view, payload = null) {
            this.state.currentView      = view;
            this.state.selectedCategory = payload;
            this.state.catPage          = 1;
            this.render();
            window.scrollTo(0, 0);
        },

        loadMore() {
            this.state.catPage++;
            this.renderCategory(document.getElementById('view-container'));
        },

        // ── Favoritos ─────────────────────────────────────────────
        toggleFavorite(flyerId) {
            const id  = String(flyerId);
            const idx = this.state.favorites.indexOf(id);
            if (idx === -1) { this.state.favorites.push(id); this.showToast('Guardado en favoritos'); }
            else            { this.state.favorites.splice(idx, 1); this.showToast('Eliminado de favoritos'); }
            localStorage.setItem('ic_favs', JSON.stringify(this.state.favorites));
            this.render();
        },

        // ── Lightbox / Galería ────────────────────────────────────
        openLightbox(flyerId) {
            const flyer = DB.flyers.find(f => String(f.id) === String(flyerId));
            if (!flyer) return;

            if (flyer.grupo_id) {
                // Galería: todas las fotos del grupo
                this.gallery    = DB.allImages.filter(img => img.grupo_id == flyer.grupo_id);
                this.galleryIdx = Math.max(0, this.gallery.findIndex(img => String(img.id) === String(flyerId)));
            } else {
                // Imagen única
                this.gallery    = [{ imagen_url: flyer.imagen_url }];
                this.galleryIdx = 0;
            }

            this._updateLightboxImage();
            const lb  = document.getElementById('lightbox');
            const img = document.getElementById('lightbox-img');
            lb.classList.remove('hidden');
            lb.classList.add('flex');
            setTimeout(() => { lb.classList.remove('opacity-0'); img.classList.replace('scale-95','scale-100'); }, 10);
        },

        _updateLightboxImage() {
            const img  = this.gallery[this.galleryIdx];
            const el   = document.getElementById('lightbox-img');
            const prev = document.getElementById('lb-prev');
            const next = document.getElementById('lb-next');
            const ctr  = document.getElementById('lb-counter');

            el.src = img.imagen_url;

            const multi = this.gallery.length > 1;
            ctr.textContent = `${this.galleryIdx + 1} / ${this.gallery.length}`;
            ctr.classList.toggle('hidden', !multi);

            const show = (btn, visible) => {
                btn.classList.toggle('hidden', !visible);
                btn.classList.toggle('flex', visible);
            };
            show(prev, multi && this.galleryIdx > 0);
            show(next, multi && this.galleryIdx < this.gallery.length - 1);
        },

        prevImage() { if (this.galleryIdx > 0) { this.galleryIdx--; this._updateLightboxImage(); } },
        nextImage() { if (this.galleryIdx < this.gallery.length - 1) { this.galleryIdx++; this._updateLightboxImage(); } },

        closeLightbox() {
            const lb  = document.getElementById('lightbox');
            const img = document.getElementById('lightbox-img');
            lb.classList.add('opacity-0');
            img.classList.replace('scale-100','scale-95');
            setTimeout(() => { lb.classList.replace('flex','hidden'); img.src = ''; }, 300);
        },

        // ── Compartir ─────────────────────────────────────────────
        async shareProduct(titulo, imageUrl) {
            const absUrl = imageUrl.startsWith('http')
                ? imageUrl
                : `${window.location.origin}/${imageUrl.replace(/^\//,'')}`;
            const waShare = () => {
                const num = '<?= WA_NUM ?>';
                const msg = `Hola! Vi este producto en Imperio Comercial:\n*${titulo}*\n${absUrl}`;
                window.open(`https://wa.me/${num}?text=${encodeURIComponent(msg)}`, '_blank');
            };

            if (!navigator.share) { waShare(); return; }

            // 1️⃣ Intentar compartir la imagen como ARCHIVO (nativo en móvil)
            try {
                const resp = await fetch(absUrl);
                const blob = await resp.blob();
                const ext  = (blob.type || 'image/jpeg').split('/')[1] || 'jpg';
                const file = new File([blob], `${titulo.substring(0,40)}.${ext}`, { type: blob.type });
                if (navigator.canShare && navigator.canShare({ files: [file] })) {
                    await navigator.share({ files: [file], title: `Imperio Comercial — ${titulo}`, text: titulo });
                    return;
                }
            } catch(e) {}

            // 2️⃣ Compartir URL directa de la imagen
            try {
                await navigator.share({ title: `Imperio Comercial — ${titulo}`, text: titulo, url: absUrl });
                return;
            } catch(e) {}

            // 3️⃣ Fallback: WhatsApp con la URL de la imagen en el texto
            waShare();
        },

        // ── Otros helpers ─────────────────────────────────────────
        showToast(message) {
            const t = document.getElementById('toast-notification');
            document.getElementById('toast-message').textContent = message;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 2200);
        },

        toggleSearch(show) {
            document.getElementById('search-overlay').classList.toggle('hidden', !show);
            if (show) document.getElementById('search-input').focus();
        },

        performSearch(query) {
            const div = document.getElementById('search-results');
            if (!query.trim()) { div.innerHTML = ''; return; }
            const q = query.toLowerCase();
            const r = DB.flyers.filter(f => f.titulo.toLowerCase().includes(q));
            div.innerHTML = r.map(f => this.createFlyerHTML(f, false)).join('');
        },

        contactWhatsApp(item = null) {
            const num = '<?= WA_NUM ?>';
            const msg = item ? `Hola! Me interesa: ${item}` : 'Hola! Vi el catálogo, me gustaría información.';
            window.open(`https://wa.me/${num}?text=${encodeURIComponent(msg)}`, '_blank');
        },

        // ── Tarjeta de Flyer ──────────────────────────────────────
        createFlyerHTML(f, showShare = true) {
            const isFav       = this.state.favorites.includes(String(f.id));
            const isSinStock  = f.sin_stock  == 1;
            const isDestacado = f.destacado  == 1;
            const grupoImgs   = f.grupo_id ? DB.allImages.filter(img => img.grupo_id == f.grupo_id) : [];
            const imgCount    = grupoImgs.length;

            return `
            <div class="glass-card product-card-wrap overflow-hidden group flex flex-col h-full"
                 style="${isDestacado ? 'border-color: rgba(251,191,36,0.3)' : ''}">
                <div class="flyer-card relative cursor-pointer" style="background-image:url('${f.imagen_url}')"
                     onclick="app.openLightbox('${f.id}')">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-60"></div>

                    ${isSinStock ? `
                    <div class="absolute inset-0 flex items-center justify-center z-10" style="background: rgba(26,26,46,0.75)">
                        <span class="text-[11px] font-black px-3 py-1 rounded-full uppercase tracking-wider glass-card" style="color: var(--text-secondary)">Sin Stock</span>
                    </div>` : ''}

                    ${isDestacado ? `
                    <span class="absolute top-2 left-2 z-20 bg-yellow-500 text-black text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider flex items-center gap-1">
                        <i class="fa-solid fa-star text-[8px]"></i> Destacado
                    </span>` : ''}

                    ${imgCount > 1 ? `
                    <span class="absolute bottom-2 left-2 z-10 bg-black/60 text-white text-[10px] px-2 py-0.5 rounded-full backdrop-blur-sm flex items-center gap-1 font-semibold">
                        <i class="fa-solid fa-images"></i> ${imgCount}
                    </span>` : ''}

                    <button onclick="event.stopPropagation(); app.toggleFavorite('${f.id}')"
                            class="absolute top-3 right-3 w-10 h-10 rounded-full backdrop-blur-md flex items-center justify-center transition z-20 border active:scale-90"
                            style="background: rgba(26,26,46,0.5); border-color: rgba(255,255,255,0.1)">
                        <i class="fa-${isFav ? 'solid' : 'regular'} fa-heart text-lg ${isFav ? 'text-red-500' : 'text-white'}"></i>
                    </button>
                </div>

                <div class="p-3 flex flex-col flex-1 gap-2">
                    <p class="text-[10px] font-bold uppercase tracking-widest gradient-text">Oferta</p>
                    <h4 class="font-bold text-sm truncate" style="font-family: var(--font-heading); color: var(--text-primary)">${f.titulo}</h4>
                    ${showShare ? `
                    <button onclick="event.stopPropagation(); app.shareProduct('${f.titulo.replace(/'/g,"\\'")}','${f.imagen_url}')"
                        class="mt-auto w-full py-2 rounded-xl font-semibold text-xs transition flex items-center justify-center gap-2 active:scale-95"
                        style="background: rgba(37,211,102,0.12); color: #34d399; border: 1px solid rgba(37,211,102,0.2)">
                        <i class="fa-brands fa-whatsapp"></i> Compartir
                    </button>` : ''}
                </div>
            </div>`;
        },

        // ── Render Principal ──────────────────────────────────────
        render() {
            const navHome = document.getElementById('nav-home');
            const navFavs = document.getElementById('nav-favorites');
            const activeColor = 'var(--warm-500)';
            const mutedColor  = 'var(--text-muted)';
            navHome.style.color = this.state.currentView === 'home'      ? activeColor : mutedColor;
            navFavs.style.color = this.state.currentView === 'favorites' ? activeColor : mutedColor;

            const container = document.getElementById('view-container');
            if      (this.state.currentView === 'home')      this.renderHome(container);
            else if (this.state.currentView === 'category')  this.renderCategory(container);
            else if (this.state.currentView === 'favorites') this.renderFavorites(container);
        },

        renderHome(container) {
            const destacados = DB.flyers.filter(f => f.destacado == 1).slice(0, 4);

            const destSection = destacados.length > 0 ? `
            <div class="mb-8">
                <h3 class="text-xl font-bold mb-4 flex items-center gap-2"
                    style="font-family: var(--font-heading); color: var(--text-primary)">
                    <span class="w-8 h-8 rounded-xl flex items-center justify-center" style="background: rgba(251,191,36,0.15)">
                        <i class="fa-solid fa-star text-yellow-400 text-sm"></i>
                    </span>
                    Destacados
                </h3>
                <div class="grid grid-cols-2 gap-3">
                    ${destacados.map(f => this.createFlyerHTML(f, true)).join('')}
                </div>
            </div>` : '';

            container.innerHTML = `
            <div class="view-transition">

                <!-- Hero Banner -->
                <section class="hero-gradient relative rounded-[2rem] p-6 md:p-8 mb-8 overflow-hidden text-white border"
                         style="border-color: var(--border-soft)">
                    <!-- Decorative blob -->
                    <div class="absolute -top-16 -right-16 w-56 h-56 rounded-full opacity-25 animate-float pointer-events-none"
                         style="background: radial-gradient(circle, #F97316, transparent)"></div>
                    <div class="absolute -bottom-10 -left-10 w-40 h-40 rounded-full opacity-15 animate-float pointer-events-none"
                         style="background: radial-gradient(circle, #FBBF24, transparent); animation-delay: -3s"></div>

                    <div class="relative z-10">
                        <!-- Badge -->
                        <div class="inline-flex items-center gap-2 glass-card px-3 py-1.5 mb-4 text-sm font-semibold animate-pulse-glow"
                             style="color: var(--amber-400)">
                            🔥 Nuevas ofertas disponibles
                        </div>
                        <h3 class="text-3xl md:text-5xl font-bold leading-tight mb-2"
                            style="font-family: var(--font-heading)">Renova tu Hogar Hoy</h3>
                        <p class="mb-5 text-sm md:text-base" style="color: rgba(248,250,252,0.70)">Llevate lo que querés, en cómodas cuotas.</p>
                        <button onclick="document.querySelector('.grid.grid-cols-1').scrollIntoView({behavior:'smooth'})"
                                class="btn-warm px-6 py-2.5 text-sm font-bold">
                            Ver Categorías →
                        </button>
                    </div>
                </section>

                ${destSection}

                <div class="flex justify-between items-end mb-4">
                    <h3 class="text-xl md:text-2xl font-bold" style="font-family: var(--font-heading); color: var(--text-primary)">Categorías</h3>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    ${DB.categories.map(c => `
                    <div onclick="app.changeView('category','${c.id}')"
                         class="glass-card glass-card-hover category-card flex items-center gap-4 p-4 cursor-pointer group active:scale-[0.98]">
                        <div class="w-12 h-12 shrink-0 ${c.color_bg || 'bg-slate-800'} rounded-2xl flex items-center justify-center ${c.color_text || 'text-white'}">
                            <i class="${c.icono || 'fa-solid fa-tag'} text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-base truncate" style="font-family: var(--font-heading); color: var(--text-primary)">${c.nombre}</h4>
                            <p class="text-xs transition" style="color: var(--text-secondary)">Ver productos</p>
                        </div>
                        <i class="fa-solid fa-chevron-right cat-chevron ml-2" style="color: var(--text-muted)"></i>
                    </div>`).join('')}
                </div>
            </div>`;
        },

        renderCategory(container) {
            const cat      = DB.categories.find(c => c.id == this.state.selectedCategory);
            const allItems = DB.flyers.filter(f => f.categoria_id == this.state.selectedCategory);
            const visible  = allItems.slice(0, this.state.catPage * this.PAGE_SIZE);
            const hasMore  = visible.length < allItems.length;

            const grid = `
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                ${visible.map(f => this.createFlyerHTML(f, true)).join('')}
            </div>
            ${allItems.length === 0 ? `
            <div class="py-20 text-center text-slate-500 bg-slate-900/50 rounded-3xl border border-slate-800 border-dashed">
                <i class="fa-solid fa-box-open text-4xl mb-3 opacity-50"></i><p>Próximamente más productos...</p>
            </div>` : ''}
            ${hasMore ? `
            <div class="text-center mt-6">
                <button onclick="app.loadMore()" class="glass-card glass-card-hover px-8 py-3 font-semibold rounded-2xl transition active:scale-95 inline-flex items-center gap-2"
                        style="color: var(--text-secondary)">
                    <i class="fa-solid fa-angles-down"></i>
                    Ver más (${allItems.length - visible.length} restantes)
                </button>
            </div>` : ''}`;

            // Al cargar más, reemplazar solo el grid (no el header de categoría)
            if (this.state.catPage > 1) {
                const existing = container.querySelector('.cat-grid-wrap');
                if (existing) { existing.innerHTML = grid; return; }
            }

            container.innerHTML = `
            <div class="view-transition">
                <button onclick="app.changeView('home')" class="mb-6 flex items-center gap-2 font-bold hover:opacity-80 transition group active:scale-95 origin-left"
                        style="color: var(--text-secondary)">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center" style="background: var(--bg-card); border: 1px solid var(--border-soft)">
                        <i class="fa-solid fa-arrow-left"></i>
                    </div>
                    <span>Volver</span>
                </button>
                <div class="mb-6">
                    <h2 class="text-2xl md:text-3xl font-bold break-words" style="font-family: var(--font-heading); color: var(--text-primary)">${cat ? cat.nombre : ''}</h2>
                    <p class="text-sm" style="color: var(--text-secondary)">${allItems.length} productos disponibles.</p>
                </div>
                <div class="cat-grid-wrap">${grid}</div>
            </div>`;
        },

        renderFavorites(container) {
            const favFlyers = DB.flyers.filter(f => this.state.favorites.includes(String(f.id)));
            container.innerHTML = `
            <div class="view-transition">
                <h2 class="text-2xl md:text-3xl font-bold mb-2" style="font-family: var(--font-heading); color: var(--text-primary)">Mis Favoritos</h2>
                <p class="mb-6 text-sm" style="color: var(--text-secondary)">Productos guardados.</p>
                ${favFlyers.length > 0 ? `
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    ${favFlyers.map(f => this.createFlyerHTML(f, true)).join('')}
                </div>` : `
                <div class="py-24 text-center">
                    <div class="w-20 h-20 glass-card rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-regular fa-heart text-3xl" style="color: var(--text-muted)"></i>
                    </div>
                    <p class="font-medium" style="color: var(--text-secondary)">Aún no guardaste favoritos.</p>
                    <button onclick="app.changeView('home')" class="mt-6 font-bold hover:opacity-70 transition active:scale-95 gradient-text">Ir a la tienda</button>
                </div>`}
            </div>`;
        }
    };

    window.onload = () => app.init();
    </script>
</body>
</html>