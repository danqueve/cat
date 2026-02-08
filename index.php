<?php
require 'db.php';

// 1. Obtener Categorías
$sql_cats = "SELECT * FROM categorias ORDER BY nombre ASC";
$result_cats = $conn->query($sql_cats);
$categories = [];
while($row = $result_cats->fetch_assoc()) {
    $categories[] = $row;
}

// 2. Obtener Flyers
$sql_flyers = "SELECT * FROM flyers ORDER BY id DESC";
$result_flyers = $conn->query($sql_flyers);
$flyers = [];
while($row = $result_flyers->fetch_assoc()) {
    $flyers[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Imperio Comercial - Catálogo</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <!-- Estilos Personalizados -->
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="antialiased bg-slate-950 text-slate-200">

    <div id="app" class="max-w-4xl mx-auto min-h-screen shadow-2xl shadow-black bg-slate-950 flex flex-col relative border-x border-slate-900">

        <!-- Header -->
        <header class="sticky top-0 z-50 bg-slate-950/90 backdrop-blur-md border-b border-slate-800 px-4 md:px-6 py-4 flex justify-between items-center transition-all">
            <div onclick="app.changeView('home')" class="flex items-center gap-3 cursor-pointer">
                <!-- CAMBIO: Icono de Imagen -->
                <!-- Asegúrate de que el nombre del archivo sea correcto (logo.png, logo.jpg, etc) -->
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

        <!-- LIGHTBOX (Visor de Imágenes Grande) -->
        <div id="lightbox" class="fixed inset-0 z-[80] bg-black/95 hidden flex items-center justify-center p-4 transition-all duration-300 opacity-0" onclick="app.closeLightbox()">
            <div class="relative max-w-full max-h-full">
                <img id="lightbox-img" src="" class="max-w-full max-h-[90vh] rounded-lg shadow-2xl object-contain transition-transform duration-300 scale-95">
                <button class="absolute -top-10 right-0 text-white/70 hover:text-white text-3xl" onclick="app.closeLightbox()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
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
                <input type="text" id="search-input" placeholder="¿Qué buscas?" class="w-full bg-slate-900 border border-slate-800 rounded-2xl py-3.5 pl-12 pr-4 text-base md:text-lg text-white focus:ring-2 focus:ring-blue-500 outline-none placeholder-slate-600">
            </div>
            <div id="search-results" class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4 overflow-y-auto pb-20"></div>
        </div>

        <!-- Notificación Toast -->
        <div id="toast-notification" class="fixed bottom-24 left-1/2 -translate-x-1/2 z-[70] bg-blue-600 text-white px-6 py-3 rounded-full shadow-2xl flex items-center gap-3 toast border border-blue-400/20 whitespace-nowrap w-max max-w-[90%]">
            <i class="fa-solid fa-circle-check text-white"></i>
            <span id="toast-message" class="font-bold text-sm truncate"></span>
        </div>

        <!-- Navegación Inferior -->
        <nav class="fixed bottom-0 left-0 right-0 max-w-4xl mx-auto bg-slate-950/90 backdrop-blur-xl border-t border-slate-800 h-20 md:h-20 flex justify-around items-center px-2 md:px-4 z-40 pb-safe">
            <!-- Tienda (Home SPA) -->
            <button onclick="app.changeView('home')" id="nav-home" class="flex flex-col items-center gap-1 w-16 text-blue-500 active:scale-95 transition-transform">
                <i class="fa-solid fa-house text-xl mb-0.5"></i>
                <span class="text-[10px] font-bold">Tienda</span>
            </button>
            
            <!-- Favoritos (SPA) -->
            <button onclick="app.changeView('favorites')" id="nav-favorites" class="flex flex-col items-center gap-1 w-16 text-slate-500 hover:text-slate-300 active:scale-95 transition-transform">
                <i class="fa-solid fa-heart text-xl mb-0.5"></i>
                <span class="text-[10px] font-bold">Favoritos</span>
            </button>
            
            <!-- WhatsApp Central -->
            <button onclick="app.contactWhatsApp()" class="relative w-14 h-14 md:w-16 md:h-16 bg-green-600 rounded-full -mt-8 md:-mt-10 border-4 border-slate-950 shadow-xl shadow-green-900/20 flex items-center justify-center text-white text-2xl md:text-3xl hover:scale-105 active:scale-90 transition-all">
                <i class="fa-brands fa-whatsapp"></i>
            </button>

            <!-- Requisitos (Enlace Externo) -->
            <a href="requisitos.php" class="flex flex-col items-center gap-1 w-16 text-slate-500 hover:text-slate-300 active:scale-95 transition-transform">
                <i class="fa-solid fa-file-invoice-dollar text-xl mb-0.5"></i>
                <span class="text-[10px] font-bold">Requisitos</span>
            </a>

            <!-- Admin (Enlace Externo) -->
            <a href="login.php" class="flex flex-col items-center gap-1 w-16 text-slate-500 hover:text-slate-300 active:scale-95 transition-transform">
                <i class="fa-solid fa-user-gear text-xl mb-0.5"></i>
                <span class="text-[10px] font-bold">Admin</span>
            </a>
        </nav>
    </div>

    <script>
        // --- INYECCIÓN DE DATOS ---
        const DB = {
            categories: <?php echo json_encode($categories); ?>,
            flyers: <?php echo json_encode($flyers); ?>
        };

        // --- LÓGICA DE LA APLICACIÓN ---
        const app = {
            state: {
                currentView: 'home',
                selectedCategory: null,
                favorites: JSON.parse(localStorage.getItem('ic_favs')) || []
            },

            init() {
                this.render();
                this.setupListeners();
            },

            setupListeners() {
                document.getElementById('search-input').addEventListener('input', (e) => {
                    this.performSearch(e.target.value);
                });
            },

            changeView(view, payload = null) {
                this.state.currentView = view;
                this.state.selectedCategory = payload;
                this.render();
                window.scrollTo(0, 0);
            },

            toggleFavorite(flyerId) {
                const idStr = String(flyerId);
                const index = this.state.favorites.indexOf(idStr);
                
                if (index === -1) {
                    this.state.favorites.push(idStr);
                    this.showToast('Guardado en favoritos');
                } else {
                    this.state.favorites.splice(index, 1);
                    this.showToast('Eliminado de favoritos');
                }
                localStorage.setItem('ic_favs', JSON.stringify(this.state.favorites));
                this.render();
            },

            // --- LIGHTBOX ---
            openLightbox(imageUrl) {
                const lightbox = document.getElementById('lightbox');
                const img = document.getElementById('lightbox-img');
                img.src = imageUrl;
                lightbox.classList.remove('hidden');
                setTimeout(() => {
                    lightbox.classList.remove('opacity-0');
                    img.classList.remove('scale-95');
                    img.classList.add('scale-100');
                }, 10);
            },

            closeLightbox() {
                const lightbox = document.getElementById('lightbox');
                const img = document.getElementById('lightbox-img');
                lightbox.classList.add('opacity-0');
                img.classList.remove('scale-100');
                img.classList.add('scale-95');
                setTimeout(() => {
                    lightbox.classList.add('hidden');
                    img.src = '';
                }, 300);
            },

            showToast(message) {
                const toast = document.getElementById('toast-notification');
                const msgEl = document.getElementById('toast-message');
                msgEl.textContent = message;
                toast.classList.add('show');
                setTimeout(() => toast.classList.remove('show'), 2000);
            },

            toggleSearch(show) {
                const overlay = document.getElementById('search-overlay');
                overlay.classList.toggle('hidden', !show);
                if (show) document.getElementById('search-input').focus();
            },

            performSearch(query) {
                const resultsDiv = document.getElementById('search-results');
                if (!query) {
                    resultsDiv.innerHTML = '';
                    return;
                }
                const q = query.toLowerCase();
                const filtered = DB.flyers.filter(f => f.titulo.toLowerCase().includes(q));
                resultsDiv.innerHTML = filtered.map(f => this.createFlyerHTML(f, true)).join('');
            },

            contactWhatsApp(item = null) {
                const num = "+5493815447588"; // TU NUMERO AQUI
                const msg = item ? `Hola! Me interesa: ${item}` : "Hola! Vi el catalogo me interesaría información.";
                window.open(`https://wa.me/${num}?text=${encodeURIComponent(msg)}`, '_blank');
            },

            // --- GENERADOR DE HTML ---
            createFlyerHTML(f, showButton = true) {
                const isFav = this.state.favorites.includes(String(f.id));
                return `
                <div class="bg-slate-900 rounded-3xl overflow-hidden border border-slate-800 shadow-lg group animate-in fade-in zoom-in-95 flex flex-col h-full hover:border-slate-700 transition">
                    <!-- Imagen con Lightbox -->
                    <div class="flyer-card relative cursor-pointer" style="background-image: url('${f.imagen_url}')" onclick="app.openLightbox('${f.imagen_url}')">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 to-transparent opacity-60"></div>
                        
                        <button onclick="event.stopPropagation(); app.toggleFavorite('${f.id}')" class="absolute top-3 right-3 w-10 h-10 rounded-full backdrop-blur-md bg-slate-900/40 flex items-center justify-center transition hover:bg-slate-900/80 z-10 border border-white/10 active:scale-90">
                            <i class="fa-${isFav ? 'solid' : 'regular'} fa-heart text-lg ${isFav ? 'text-red-500' : 'text-slate-200'}"></i>
                        </button>
                    </div>
                    
                    <div class="p-4 flex flex-col flex-1">
                        <p class="text-[10px] font-bold text-blue-400 uppercase tracking-widest mb-1">Oferta</p>
                        <h4 class="font-bold text-white text-sm truncate mb-3">${f.titulo}</h4>
                        
                       
                    </div>
                </div>`;
            },

            render() {
                const container = document.getElementById('view-container');
                const navHome = document.getElementById('nav-home');
                const navFavs = document.getElementById('nav-favorites');

                // Estilos de Navegación
                navHome.className = `flex flex-col items-center gap-1 w-16 ${this.state.currentView === 'home' ? 'text-blue-500' : 'text-slate-500'} active:scale-95 transition-transform`;
                navFavs.className = `flex flex-col items-center gap-1 w-16 ${this.state.currentView === 'favorites' ? 'text-blue-500' : 'text-slate-500'} active:scale-95 transition-transform`;

                if (this.state.currentView === 'home') this.renderHome(container);
                else if (this.state.currentView === 'category') this.renderCategory(container);
                else if (this.state.currentView === 'favorites') this.renderFavorites(container);
            },

            renderHome(container) {
                container.innerHTML = `
                <div class="view-transition">
                    <section class="relative bg-gradient-to-br from-blue-600 to-indigo-900 rounded-[2rem] md:rounded-[2.5rem] p-6 md:p-8 mb-8 md:mb-10 overflow-hidden text-white shadow-xl shadow-blue-900/20 border border-white/5">
                        <div class="relative z-10">
                            
                            <h3 class="text-3xl md:text-5xl font-black mt-3 md:mt-4 leading-tight">Renova tu Hogar Hoy</h3>
                            
                            
                        </div>
                        <i class="fa-solid fa-house-circle-check absolute -bottom-10 -right-10 text-[10rem] md:text-[15rem] text-white/5 rotate-12"></i>
                    </section>
                    
                    <div class="flex justify-between items-end mb-4 md:mb-6">
                        <h3 class="text-xl md:text-2xl font-black text-white">Categorías</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-3 md:gap-4">
                        ${DB.categories.map(c => `
                            <div onclick="app.changeView('category', '${c.id}')" 
                                 class="flex items-center gap-4 p-4 md:p-5 bg-slate-900 border border-slate-800 rounded-3xl hover:border-blue-500/30 hover:bg-slate-800 transition cursor-pointer group shadow-lg active:scale-[0.98]">
                                <div class="w-12 h-12 md:w-16 md:h-16 shrink-0 ${c.color_bg || 'bg-slate-800'} rounded-2xl flex items-center justify-center ${c.color_text || 'text-white'}">
                                    <i class="${c.icono || 'fa-solid fa-tag'} text-xl md:text-2xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-white text-base md:text-lg truncate">${c.nombre}</h4>
                                    <p class="text-xs text-slate-400 group-hover:text-slate-300 transition truncate">Ver productos</p>
                                </div>
                                <i class="fa-solid fa-chevron-right text-slate-600 group-hover:text-white group-hover:translate-x-1 transition ml-2"></i>
                            </div>
                        `).join('')}
                    </div>
                </div>`;
            },

            renderCategory(container) {
                const cat = DB.categories.find(c => c.id == this.state.selectedCategory);
                const items = DB.flyers.filter(f => f.categoria_id == this.state.selectedCategory);

                container.innerHTML = `
                <div class="view-transition">
                    <button onclick="app.changeView('home')" class="mb-6 flex items-center gap-2 text-slate-400 font-bold hover:text-white transition group active:scale-95 origin-left">
                        <div class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center group-hover:bg-slate-700">
                            <i class="fa-solid fa-arrow-left"></i>
                        </div>
                        <span>Volver</span>
                    </button>
                    <div class="mb-6 md:mb-8">
                        <h2 class="text-2xl md:text-3xl font-black text-white break-words">${cat.nombre}</h2>
                        <p class="text-slate-400 text-sm md:text-base">${items.length} productos disponibles.</p>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4">
                        ${items.map(f => this.createFlyerHTML(f, false)).join('')}
                    </div>
                    ${items.length === 0 ? '<div class="py-20 text-center text-slate-500 bg-slate-900/50 rounded-3xl border border-slate-800 border-dashed mx-4"><i class="fa-solid fa-box-open text-4xl mb-3 opacity-50"></i><p>Próximamente más productos...</p></div>' : ''}
                </div>`;
            },

            renderFavorites(container) {
                const favFlyers = DB.flyers.filter(f => this.state.favorites.includes(String(f.id)));
                container.innerHTML = `
                <div class="view-transition">
                    <h2 class="text-2xl md:text-3xl font-black text-white mb-2">Mis Favoritos</h2>
                    <p class="text-slate-400 mb-6 md:mb-8 text-sm md:text-base">Productos guardados.</p>
                    ${favFlyers.length > 0 ? `
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4">
                            ${favFlyers.map(f => this.createFlyerHTML(f, true)).join('')}
                        </div>
                    ` : `
                        <div class="py-24 text-center">
                            <div class="w-20 h-20 bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-800">
                                <i class="fa-regular fa-heart text-slate-600 text-3xl"></i>
                            </div>
                            <p class="text-slate-400 font-medium">Aún no has guardado favoritos.</p>
                            <button onclick="app.changeView('home')" class="mt-6 text-blue-400 font-bold hover:text-blue-300 transition active:scale-95">Ir a la tienda</button>
                        </div>
                    `}
                </div>`;
            }
        };

        window.onload = () => app.init();
    </script>
</body>
</html>