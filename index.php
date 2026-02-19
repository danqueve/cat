<?php
require 'db.php';

// ── SETUP: Columnas nuevas (try/catch por si el usuario DB no tiene ALTER privilege) ──
$newCols = [
    'orden'        => 'INT DEFAULT 0',
    'destacado'    => 'TINYINT(1) NOT NULL DEFAULT 0',
    'sin_stock'    => 'TINYINT(1) NOT NULL DEFAULT 0',
    'grupo_id'     => 'INT NULL',
    'es_principal' => 'TINYINT(1) NOT NULL DEFAULT 1',
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

// ── CATEGORÍAS ────────────────────────────────────────────────────
$result_cats = $conn->query("SELECT * FROM categorias ORDER BY nombre ASC");
$categories  = [];
while ($row = $result_cats->fetch_assoc()) $categories[] = $row;

// ── FLYERS PRINCIPALES (ORDER BY dinámico: safe si columnas aún no existen) ──
$orderParts = [];
if ($colsExist['destacado']) $orderParts[] = 'destacado DESC';
if ($colsExist['orden'])     $orderParts[] = 'orden ASC';
$orderParts[] = 'id DESC';
$orderBy  = implode(', ', $orderParts);
$whereEsP = $colsExist['es_principal'] ? 'WHERE es_principal = 1' : '';
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="antialiased bg-slate-950 text-slate-200">

    <div id="app" class="max-w-4xl mx-auto min-h-screen shadow-2xl shadow-black bg-slate-950 flex flex-col relative border-x border-slate-900">

        <!-- Header -->
        <header class="sticky top-0 z-50 bg-slate-950/90 backdrop-blur-md border-b border-slate-800 px-4 md:px-6 py-4 flex justify-between items-center transition-all">
            <div onclick="app.changeView('home')" class="flex items-center gap-3 cursor-pointer">
                <img src="img/icono.jpg" alt="Logo" class="w-10 h-10 rounded-xl shadow-lg shadow-blue-500/20 object-contain bg-slate-900 border border-slate-800 p-0.5">
                <h1 class="text-lg md:text-xl font-extrabold tracking-tight text-white">Imperio Comercial</h1>
            </div>
            <div class="flex items-center gap-3 md:gap-4">
                <button onclick="app.toggleSearch(true)" class="p-2 text-slate-400 hover:bg-slate-800 rounded-full transition active:scale-95">
                    <i class="fa-solid fa-magnifying-glass text-lg"></i>
                </button>
                <div class="relative">
                    <i class="fa-regular fa-bell text-xl text-slate-400"></i>
                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-red-500 border-2 border-slate-950 rounded-full"></span>
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
        <div id="search-overlay" class="fixed inset-0 z-[60] bg-slate-950 hidden flex flex-col p-4 md:p-6 view-transition">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl md:text-2xl font-black text-white">Buscar</h2>
                <button onclick="app.toggleSearch(false)" class="p-2 bg-slate-800 rounded-full text-slate-400 hover:text-white transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="relative mb-6">
                <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-500"></i>
                <input type="text" id="search-input" placeholder="¿Qué buscás?" class="w-full bg-slate-900 border border-slate-800 rounded-2xl py-3.5 pl-12 pr-4 text-base md:text-lg text-white focus:ring-2 focus:ring-blue-500 outline-none placeholder-slate-600">
            </div>
            <div id="search-results" class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4 overflow-y-auto pb-20"></div>
        </div>

        <!-- Toast -->
        <div id="toast-notification" class="fixed bottom-24 left-1/2 -translate-x-1/2 z-[70] bg-blue-600 text-white px-6 py-3 rounded-full shadow-2xl flex items-center gap-3 toast border border-blue-400/20 whitespace-nowrap w-max max-w-[90%]">
            <i class="fa-solid fa-circle-check text-white"></i>
            <span id="toast-message" class="font-bold text-sm truncate"></span>
        </div>

        <!-- Navegación Inferior -->
        <nav class="fixed bottom-0 left-0 right-0 max-w-4xl mx-auto bg-slate-950/90 backdrop-blur-xl border-t border-slate-800 h-20 flex justify-around items-center px-2 md:px-4 z-40 pb-safe">
            <button onclick="app.changeView('home')" id="nav-home" class="flex flex-col items-center gap-1 w-16 text-blue-500 active:scale-95 transition-transform">
                <i class="fa-solid fa-house text-xl mb-0.5"></i>
                <span class="text-[10px] font-bold">Tienda</span>
            </button>
            <button onclick="app.changeView('favorites')" id="nav-favorites" class="flex flex-col items-center gap-1 w-16 text-slate-500 hover:text-slate-300 active:scale-95 transition-transform">
                <i class="fa-solid fa-heart text-xl mb-0.5"></i>
                <span class="text-[10px] font-bold">Favoritos</span>
            </button>
            <button onclick="app.contactWhatsApp()" class="relative w-14 h-14 md:w-16 md:h-16 bg-green-600 rounded-full -mt-8 md:-mt-10 border-4 border-slate-950 shadow-xl shadow-green-900/20 flex items-center justify-center text-white text-2xl md:text-3xl hover:scale-105 active:scale-90 transition-all">
                <i class="fa-brands fa-whatsapp"></i>
            </button>
            <a href="requisitos.php" class="flex flex-col items-center gap-1 w-16 text-slate-500 hover:text-slate-300 active:scale-95 transition-transform">
                <i class="fa-solid fa-file-invoice-dollar text-xl mb-0.5"></i>
                <span class="text-[10px] font-bold">Requisitos</span>
            </a>
            <a href="login.php" class="flex flex-col items-center gap-1 w-16 text-slate-500 hover:text-slate-300 active:scale-95 transition-transform">
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
                const num = '+5493815447588';
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
            const num = '+5493815447588';
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
            <div class="bg-slate-900 rounded-3xl overflow-hidden border ${isDestacado ? 'border-yellow-500/30' : 'border-slate-800'} shadow-lg group flex flex-col h-full hover:border-slate-700 transition">
                <div class="flyer-card relative cursor-pointer" style="background-image:url('${f.imagen_url}')" onclick="app.openLightbox('${f.id}')">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 to-transparent opacity-60"></div>

                    ${isSinStock ? `
                    <div class="absolute inset-0 bg-slate-950/75 flex items-center justify-center z-10">
                        <span class="bg-slate-800 text-slate-400 text-[11px] font-black px-3 py-1 rounded-full border border-slate-600 uppercase tracking-wider">Sin Stock</span>
                    </div>` : ''}

                    ${isDestacado ? `
                    <span class="absolute top-2 left-2 z-20 bg-yellow-500 text-black text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider flex items-center gap-1">
                        <i class="fa-solid fa-star text-[8px]"></i> Destacado
                    </span>` : ''}

                    ${imgCount > 1 ? `
                    <span class="absolute bottom-2 left-2 z-10 bg-black/60 text-white text-[10px] px-2 py-0.5 rounded-full backdrop-blur-sm flex items-center gap-1 font-semibold">
                        <i class="fa-solid fa-images"></i> ${imgCount}
                    </span>` : ''}

                    <button onclick="event.stopPropagation(); app.toggleFavorite('${f.id}')" class="absolute top-3 right-3 w-10 h-10 rounded-full backdrop-blur-md bg-slate-900/40 flex items-center justify-center transition hover:bg-slate-900/80 z-20 border border-white/10 active:scale-90">
                        <i class="fa-${isFav ? 'solid' : 'regular'} fa-heart text-lg ${isFav ? 'text-red-500' : 'text-slate-200'}"></i>
                    </button>
                </div>

                <div class="p-3 flex flex-col flex-1 gap-2">
                    <p class="text-[10px] font-bold text-blue-400 uppercase tracking-widest">Oferta</p>
                    <h4 class="font-bold text-white text-sm truncate">${f.titulo}</h4>
                    ${showShare ? `
                    <button onclick="event.stopPropagation(); app.shareProduct('${f.titulo.replace(/'/g,"\\'")}','${f.imagen_url}')"
                        class="mt-auto w-full bg-green-600/15 hover:bg-green-600/30 text-green-400 border border-green-600/20 py-2 rounded-xl font-semibold text-xs transition flex items-center justify-center gap-2 active:scale-95">
                        <i class="fa-brands fa-whatsapp"></i> Compartir
                    </button>` : ''}
                </div>
            </div>`;
        },

        // ── Render Principal ──────────────────────────────────────
        render() {
            const navHome = document.getElementById('nav-home');
            const navFavs = document.getElementById('nav-favorites');
            navHome.className = `flex flex-col items-center gap-1 w-16 ${this.state.currentView === 'home' ? 'text-blue-500' : 'text-slate-500'} active:scale-95 transition-transform`;
            navFavs.className = `flex flex-col items-center gap-1 w-16 ${this.state.currentView === 'favorites' ? 'text-blue-500' : 'text-slate-500 hover:text-slate-300'} active:scale-95 transition-transform`;

            const container = document.getElementById('view-container');
            if      (this.state.currentView === 'home')      this.renderHome(container);
            else if (this.state.currentView === 'category')  this.renderCategory(container);
            else if (this.state.currentView === 'favorites') this.renderFavorites(container);
        },

        renderHome(container) {
            const destacados = DB.flyers.filter(f => f.destacado == 1).slice(0, 4);

            const destSection = destacados.length > 0 ? `
            <div class="mb-8">
                <h3 class="text-xl font-black text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-yellow-500/20 rounded-xl flex items-center justify-center">
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
                <section class="relative bg-gradient-to-br from-blue-600 to-indigo-900 rounded-[2rem] p-6 md:p-8 mb-8 overflow-hidden text-white shadow-xl shadow-blue-900/20 border border-white/5">
                    <div class="relative z-10">
                        <h3 class="text-3xl md:text-5xl font-black mt-3 leading-tight">Renova tu Hogar Hoy</h3>
                    </div>
                    <i class="fa-solid fa-house-circle-check absolute -bottom-10 -right-10 text-[10rem] md:text-[15rem] text-white/5 rotate-12"></i>
                </section>

                ${destSection}

                <div class="flex justify-between items-end mb-4">
                    <h3 class="text-xl md:text-2xl font-black text-white">Categorías</h3>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    ${DB.categories.map(c => `
                    <div onclick="app.changeView('category','${c.id}')"
                         class="flex items-center gap-4 p-4 bg-slate-900 border border-slate-800 rounded-3xl hover:border-blue-500/30 hover:bg-slate-800 transition cursor-pointer group shadow-lg active:scale-[0.98]">
                        <div class="w-12 h-12 shrink-0 ${c.color_bg || 'bg-slate-800'} rounded-2xl flex items-center justify-center ${c.color_text || 'text-white'}">
                            <i class="${c.icono || 'fa-solid fa-tag'} text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-white text-base truncate">${c.nombre}</h4>
                            <p class="text-xs text-slate-400 group-hover:text-slate-300 transition">Ver productos</p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-slate-600 group-hover:text-white group-hover:translate-x-1 transition ml-2"></i>
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
                <button onclick="app.loadMore()" class="px-8 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold rounded-2xl transition active:scale-95 flex items-center gap-2 mx-auto">
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
                <button onclick="app.changeView('home')" class="mb-6 flex items-center gap-2 text-slate-400 font-bold hover:text-white transition group active:scale-95 origin-left">
                    <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center group-hover:bg-slate-700">
                        <i class="fa-solid fa-arrow-left"></i>
                    </div>
                    <span>Volver</span>
                </button>
                <div class="mb-6">
                    <h2 class="text-2xl md:text-3xl font-black text-white break-words">${cat ? cat.nombre : ''}</h2>
                    <p class="text-slate-400 text-sm">${allItems.length} productos disponibles.</p>
                </div>
                <div class="cat-grid-wrap">${grid}</div>
            </div>`;
        },

        renderFavorites(container) {
            const favFlyers = DB.flyers.filter(f => this.state.favorites.includes(String(f.id)));
            container.innerHTML = `
            <div class="view-transition">
                <h2 class="text-2xl md:text-3xl font-black text-white mb-2">Mis Favoritos</h2>
                <p class="text-slate-400 mb-6 text-sm">Productos guardados.</p>
                ${favFlyers.length > 0 ? `
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    ${favFlyers.map(f => this.createFlyerHTML(f, true)).join('')}
                </div>` : `
                <div class="py-24 text-center">
                    <div class="w-20 h-20 bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-800">
                        <i class="fa-regular fa-heart text-slate-600 text-3xl"></i>
                    </div>
                    <p class="text-slate-400 font-medium">Aún no guardaste favoritos.</p>
                    <button onclick="app.changeView('home')" class="mt-6 text-blue-400 font-bold hover:text-blue-300 transition active:scale-95">Ir a la tienda</button>
                </div>`}
            </div>`;
        }
    };

    window.onload = () => app.init();
    </script>
</body>
</html>