<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Requisitos - Imperio Comercial</title>

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

    <div class="max-w-4xl mx-auto min-h-screen shadow-2xl shadow-black bg-slate-950 flex flex-col relative border-x border-slate-900">

        <!-- Header Simple -->
        <header class="sticky top-0 z-50 bg-slate-950/90 backdrop-blur-md border-b border-slate-800 px-4 md:px-6 py-4 flex justify-between items-center transition-all">
            <a href="index.php" class="flex items-center gap-3 cursor-pointer">
                <div class="w-9 h-9 md:w-10 md:h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-lg md:text-xl shadow-lg shadow-blue-500/20">IC</div>
                <h1 class="text-lg md:text-xl font-extrabold tracking-tight text-white">Imperio Comercial</h1>
            </a>
            <a href="index.php" class="text-slate-400 hover:text-white transition">
                <i class="fa-solid fa-arrow-left text-xl"></i>
            </a>
        </header>

        <!-- Contenido Principal -->
        <main class="flex-1 p-4 md:p-6 pb-32 md:pb-28 w-full animate-fade-in">
            
            <h2 class="text-2xl md:text-3xl font-black text-white mb-6">Requisitos y Términos</h2>
            
            <!-- TARJETA 1: REQUISITOS -->
            <div class="bg-slate-900 rounded-3xl p-6 border border-slate-800 shadow-lg mb-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

                <h3 class="text-xl font-bold text-blue-400 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-list-check"></i> Requisitos Obligatorios
                </h3>
                
                <ul class="space-y-6">
                    <li class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400 shrink-0 font-bold border border-blue-500/20 text-lg">1</div>
                        <div>
                            <span class="font-bold text-white block text-lg mb-1">DNI</span>
                            <span class="text-slate-400 text-sm leading-relaxed">Foto clara de frente y dorso (Titular y Garante).</span>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400 shrink-0 font-bold border border-blue-500/20 text-lg">2</div>
                        <div>
                            <span class="font-bold text-white block text-lg mb-1">Domicilio</span>
                            <span class="text-slate-400 text-sm leading-relaxed">Foto de una boleta de servicio (Luz, Gas o Agua) que esté a tu nombre o coincida con la dirección de tu DNI.</span>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-400 shrink-0 font-bold border border-blue-500/20 text-lg">3</div>
                        <div>
                            <span class="font-bold text-white block text-lg mb-1">Anticipo</span>
                            <span class="text-slate-400 text-sm leading-relaxed">El pago de la primera cuota se realiza al momento de recibir el producto.</span>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- TARJETA 2: TÉRMINOS Y CONDICIONES -->
            <div class="bg-slate-900 rounded-3xl p-6 border border-slate-800 shadow-lg mb-10 relative overflow-hidden">
                <h3 class="text-xl font-bold text-purple-400 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-file-contract"></i> Términos y Condiciones
                </h3>
                <p class="text-slate-400 text-sm mb-6 border-b border-slate-800 pb-4">Por favor leé atentamente cómo funciona nuestra financiación:</p>
                
                <ul class="space-y-5 text-sm">
                    <li class="flex gap-3">
                        <i class="fa-regular fa-calendar-check text-purple-400 mt-1 text-base"></i>
                        <div>
                            <strong class="text-white block">📅 Pagos</strong>
                            <span class="text-slate-400">Las cuotas vencen en la fecha pactada. Si pagás después de las 20:00 hs, se considera pago al día siguiente.</span>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-yellow-500 mt-1 text-base"></i>
                        <div>
                            <strong class="text-white block">⚠️ Mora</strong>
                            <span class="text-slate-400">El atraso genera intereses diarios (15% semanal proporcional). ¡Evitá recargos pagando a tiempo!</span>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <i class="fa-solid fa-ban text-red-400 mt-1 text-base"></i>
                        <div>
                            <strong class="text-white block">🚫 Incumplimiento</strong>
                            <span class="text-slate-400">Si se acumulan 3 cuotas impagas, la empresa procederá al retiro del artículo, sin que esto anule la deuda pendiente.</span>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <i class="fa-solid fa-lock text-green-400 mt-1 text-base"></i>
                        <div>
                            <strong class="text-white block">🔒 Cancelación</strong>
                            <span class="text-slate-400">Podés adelantar cuotas cuando quieras, pero el monto total del crédito pactado es fijo (no se reduce por pago anticipado).</span>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <i class="fa-solid fa-receipt text-blue-400 mt-1 text-base"></i>
                        <div>
                            <strong class="text-white block">🧾 Comprobantes</strong>
                            <span class="text-slate-400">Recibirás un comprobante digital por cada pago realizado.</span>
                        </div>
                    </li>
                </ul>
                
                <div class="mt-8 p-4 bg-slate-950/50 rounded-xl border border-slate-800 text-xs text-slate-500 text-center italic">
                    Al realizar el pago inicial, aceptás estos términos y la competencia de los tribunales de S.M. de Tucumán.
                </div>
            </div>

            <!-- SECCIÓN CTA DESTACADA (NUEVO DISEÑO) -->
            <div class="mt-8 mx-1 bg-gradient-to-br from-green-500 to-emerald-700 rounded-3xl p-8 text-center shadow-2xl shadow-green-500/30 border border-green-400/40 relative overflow-hidden group">
                <!-- Efectos decorativos de fondo -->
                <div class="absolute -top-10 -left-10 w-40 h-40 bg-white/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute bottom-0 right-0 w-32 h-32 bg-emerald-900/40 rounded-full blur-2xl pointer-events-none"></div>
                
                <div class="relative z-10">
                    <p class="text-green-100 font-bold mb-3 text-sm uppercase tracking-widest drop-shadow-sm border-b border-white/20 inline-block pb-1">Si no tenes un vendedor da click en el Boton de WP</p>
                    <h3 class="text-white font-black text-2xl md:text-3xl mb-2 drop-shadow-md leading-tight">Los Pedidos hacelo a tu Vendedor/a</h3>
                    <p class="text-green-50 text-sm mb-6 font-medium">Te asignaremos un asesor de confianza al instante en caso de no tenerlo.</p>
                    
                
        
                </div>
            </div>

        </main>

        <!-- Navegación Inferior -->
        <nav class="fixed bottom-0 left-0 right-0 max-w-4xl mx-auto bg-slate-950/90 backdrop-blur-xl border-t border-slate-800 h-20 md:h-20 flex justify-around items-center px-2 md:px-4 z-40 pb-safe">
            <a href="index.php" class="flex flex-col items-center gap-1 w-16 text-slate-500 hover:text-slate-300 active:scale-95 transition-transform">
                <i class="fa-solid fa-house text-xl mb-0.5"></i>
                <span class="text-[10px] font-bold">Tienda</span>
            </a>
            <a href="index.php" class="flex flex-col items-center gap-1 w-16 text-slate-500 hover:text-slate-300 active:scale-95 transition-transform">
                <i class="fa-solid fa-heart text-xl mb-0.5"></i>
                <span class="text-[10px] font-bold">Favoritos</span>
            </a>
            
            <button onclick="contactWhatsApp()" class="relative w-14 h-14 md:w-16 md:h-16 bg-green-600 rounded-full -mt-8 md:-mt-10 border-4 border-slate-950 shadow-xl shadow-green-900/20 flex items-center justify-center text-white text-2xl md:text-3xl hover:scale-105 active:scale-90 transition-all">
                <i class="fa-brands fa-whatsapp"></i>
            </button>

            <!-- SOLAPA ACTIVA -->
            <a href="requisitos.php" class="flex flex-col items-center gap-1 w-16 text-blue-500 active:scale-95 transition-transform">
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
        function contactWhatsApp() {
            const num = "+5493815447588"; // TU NUMERO AQUI
            const msg = "Hola, me gustaría solicitar un crédito y conocer más detalles sobre los requisitos.";
            window.open(`https://wa.me/${num}?text=${encodeURIComponent(msg)}`, '_blank');
        }
    </script>
</body>
</html>