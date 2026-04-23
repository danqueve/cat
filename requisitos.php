<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Requisitos - Imperio Comercial</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="antialiased" style="background: var(--bg-base); color: var(--text-primary)">

    <div class="max-w-4xl mx-auto min-h-screen flex flex-col relative border-x"
         style="border-color: var(--border-subtle)">

        <!-- Header -->
        <header class="sticky top-0 z-50 border-b px-4 md:px-6 py-4 flex justify-between items-center"
                style="background: rgba(26,26,46,0.90); backdrop-filter: blur(16px); border-color: var(--border-soft)">
            <a href="index.php" class="flex items-center gap-3 cursor-pointer">
                <div class="w-9 h-9 md:w-10 md:h-10 btn-warm rounded-xl flex items-center justify-center font-bold text-lg md:text-xl"
                     style="font-family: var(--font-heading)">IC</div>
                <h1 class="text-lg md:text-xl font-bold tracking-tight"
                    style="font-family: var(--font-heading); color: var(--text-primary)">Imperio Comercial</h1>
            </a>
            <a href="index.php" class="hover:opacity-70 transition" style="color: var(--text-secondary)">
                <i class="fa-solid fa-arrow-left text-xl"></i>
            </a>
        </header>

        <!-- Contenido Principal -->
        <main class="flex-1 p-4 md:p-6 pb-32 md:pb-28 w-full animate-fade-in">

            <h2 class="text-2xl md:text-3xl font-bold mb-6"
                style="font-family: var(--font-heading); color: var(--text-primary)">
                Requisitos y Términos
            </h2>

            <!-- TARJETA 1: REQUISITOS -->
            <div class="glass-card p-6 mb-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 rounded-full opacity-10 blur-3xl pointer-events-none -mr-16 -mt-16"
                     style="background: var(--warm-500)"></div>

                <h3 class="text-xl font-bold mb-6 flex items-center gap-2"
                    style="font-family: var(--font-heading); color: var(--warm-400)">
                    <i class="fa-solid fa-list-check"></i> Requisitos Obligatorios
                </h3>

                <ul class="space-y-6">
                    <li class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 font-bold border text-lg"
                             style="background: rgba(249,115,22,0.12); border-color: rgba(249,115,22,0.25); font-family: var(--font-heading)">
                            <span class="gradient-text">1</span>
                        </div>
                        <div>
                            <span class="font-bold block text-lg mb-1" style="color: var(--text-primary)">DNI</span>
                            <span class="text-sm leading-relaxed" style="color: var(--text-secondary)">Foto clara de frente y dorso (Titular y Garante).</span>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 font-bold border text-lg"
                             style="background: rgba(249,115,22,0.12); border-color: rgba(249,115,22,0.25); font-family: var(--font-heading)">
                            <span class="gradient-text">2</span>
                        </div>
                        <div>
                            <span class="font-bold block text-lg mb-1" style="color: var(--text-primary)">Domicilio</span>
                            <span class="text-sm leading-relaxed" style="color: var(--text-secondary)">Foto de una boleta de servicio (Luz, Gas o Agua) que esté a tu nombre o coincida con la dirección de tu DNI.</span>
                        </div>
                    </li>
                    <li class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 font-bold border text-lg"
                             style="background: rgba(249,115,22,0.12); border-color: rgba(249,115,22,0.25); font-family: var(--font-heading)">
                            <span class="gradient-text">3</span>
                        </div>
                        <div>
                            <span class="font-bold block text-lg mb-1" style="color: var(--text-primary)">Anticipo</span>
                            <span class="text-sm leading-relaxed" style="color: var(--text-secondary)">El pago de la primera cuota se realiza al momento de recibir el producto.</span>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- TARJETA 2: TÉRMINOS Y CONDICIONES -->
            <div class="glass-card p-6 mb-8 relative overflow-hidden">
                <h3 class="text-xl font-bold mb-4 flex items-center gap-2"
                    style="font-family: var(--font-heading); color: #a78bfa">
                    <i class="fa-solid fa-file-contract"></i> Términos y Condiciones
                </h3>
                <p class="text-sm mb-6 pb-4 border-b" style="color: var(--text-secondary); border-color: var(--border-subtle)">
                    Por favor leé atentamente cómo funciona nuestra financiación:
                </p>

                <ul class="space-y-5 text-sm">
                    <li class="flex gap-3">
                        <i class="fa-regular fa-calendar-check mt-1 text-base" style="color: #a78bfa"></i>
                        <div>
                            <strong class="block" style="color: var(--text-primary)">📅 Pagos</strong>
                            <span style="color: var(--text-secondary)">Las cuotas vencen en la fecha pactada. Si pagás después de las 20:00 hs, se considera pago al día siguiente.</span>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <i class="fa-solid fa-triangle-exclamation mt-1 text-base text-yellow-500"></i>
                        <div>
                            <strong class="block" style="color: var(--text-primary)">⚠️ Mora</strong>
                            <span style="color: var(--text-secondary)">El atraso genera intereses diarios (15% semanal proporcional). ¡Evitá recargos pagando a tiempo!</span>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <i class="fa-solid fa-ban mt-1 text-base text-red-400"></i>
                        <div>
                            <strong class="block" style="color: var(--text-primary)">🚫 Incumplimiento</strong>
                            <span style="color: var(--text-secondary)">Si se acumulan 3 cuotas impagas, la empresa procederá al retiro del artículo, sin que esto anule la deuda pendiente.</span>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <i class="fa-solid fa-lock mt-1 text-base text-green-400"></i>
                        <div>
                            <strong class="block" style="color: var(--text-primary)">🔒 Cancelación</strong>
                            <span style="color: var(--text-secondary)">Podés adelantar cuotas cuando quieras, pero el monto total del crédito pactado es fijo (no se reduce por pago anticipado).</span>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <i class="fa-solid fa-receipt mt-1 text-base" style="color: var(--warm-400)"></i>
                        <div>
                            <strong class="block" style="color: var(--text-primary)">🧾 Comprobantes</strong>
                            <span style="color: var(--text-secondary)">Recibirás un comprobante digital por cada pago realizado.</span>
                        </div>
                    </li>
                </ul>

                <div class="mt-8 p-4 rounded-xl border text-xs text-center italic"
                     style="background: rgba(255,255,255,0.03); border-color: var(--border-subtle); color: var(--text-muted)">
                    Al realizar el pago inicial, aceptás estos términos y la competencia de los tribunales de S.M. de Tucumán.
                </div>
            </div>

            <!-- SECCIÓN CTA -->
            <div class="relative rounded-3xl p-8 text-center overflow-hidden"
                 style="background: linear-gradient(135deg, #059669, #047857); box-shadow: 0 20px 40px rgba(5,150,105,0.3); border: 1px solid rgba(52,211,153,0.3)">
                <!-- Efectos decorativos -->
                <div class="absolute -top-10 -left-10 w-40 h-40 rounded-full opacity-20 blur-3xl pointer-events-none"
                     style="background: white"></div>
                <div class="absolute bottom-0 right-0 w-32 h-32 rounded-full opacity-30 blur-2xl pointer-events-none"
                     style="background: #065f46"></div>

                <div class="relative z-10">
                    <p class="font-bold mb-3 text-sm uppercase tracking-widest pb-2 border-b border-white/20 inline-block"
                       style="color: rgba(209,250,229,0.9)">
                        Si no tenés un vendedor hacé click en el botón de WP
                    </p>
                    <h3 class="font-black text-2xl md:text-3xl mb-2 drop-shadow-md leading-tight text-white"
                        style="font-family: var(--font-heading)">
                        Los Pedidos hacelos a tu Vendedor/a
                    </h3>
                    <p class="text-sm mb-8 font-medium" style="color: rgba(209,250,229,0.85)">
                        Te asignaremos un asesor de confianza al instante en caso de no tenerlo.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <button onclick="contactWhatsApp()"
                                class="animate-pulse-glow w-full sm:w-auto px-8 py-4 rounded-2xl font-bold text-base flex items-center justify-center gap-3"
                                style="background: white; color: #059669; box-shadow: 0 4px 20px rgba(0,0,0,0.2)">
                            <i class="fa-brands fa-whatsapp text-2xl" style="color: #25d366"></i>
                            Consultar por WhatsApp
                        </button>
                        <a href="index.php"
                           class="w-full sm:w-auto px-8 py-4 rounded-2xl font-semibold text-base flex items-center justify-center gap-2 transition hover:bg-white/20"
                           style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: white">
                            <i class="fa-solid fa-store"></i>
                            Ver Catálogo
                        </a>
                    </div>
                </div>
            </div>

        </main>

        <!-- Navegación Inferior -->
        <nav class="fixed bottom-0 left-0 right-0 max-w-4xl mx-auto border-t h-20 flex justify-around items-center px-2 md:px-4 z-40"
             style="background: rgba(26,26,46,0.92); backdrop-filter: blur(20px); border-color: var(--border-soft)">
            <a href="index.php" class="flex flex-col items-center gap-1 w-16 hover:opacity-80 active:scale-95 transition-transform"
               style="color: var(--text-muted)">
                <i class="fa-solid fa-house text-xl mb-0.5"></i>
                <span class="text-[10px] font-bold">Tienda</span>
            </a>
            <a href="index.php" class="flex flex-col items-center gap-1 w-16 hover:opacity-80 active:scale-95 transition-transform"
               style="color: var(--text-muted)">
                <i class="fa-solid fa-heart text-xl mb-0.5"></i>
                <span class="text-[10px] font-bold">Favoritos</span>
            </a>

            <button onclick="contactWhatsApp()"
                    class="relative w-14 h-14 md:w-16 md:h-16 bg-green-600 rounded-full -mt-8 md:-mt-10 border-4 flex items-center justify-center text-white text-2xl md:text-3xl hover:scale-105 active:scale-90 transition-all shadow-xl"
                    style="border-color: var(--bg-base)">
                <i class="fa-brands fa-whatsapp"></i>
            </button>

            <a href="requisitos.php" class="flex flex-col items-center gap-1 w-16 active:scale-95 transition-transform"
               style="color: var(--warm-500)">
                <i class="fa-solid fa-file-invoice-dollar text-xl mb-0.5"></i>
                <span class="text-[10px] font-bold">Requisitos</span>
            </a>

            <a href="login.php" class="flex flex-col items-center gap-1 w-16 hover:opacity-80 active:scale-95 transition-transform"
               style="color: var(--text-muted)">
                <i class="fa-solid fa-user-gear text-xl mb-0.5"></i>
                <span class="text-[10px] font-bold">Admin</span>
            </a>
        </nav>
    </div>

    <script>
        function contactWhatsApp() {
            const num = "+5493815447588";
            const msg = "Hola, me gustaría solicitar un crédito y conocer más detalles sobre los requisitos.";
            window.open(`https://wa.me/${num}?text=${encodeURIComponent(msg)}`, '_blank');
        }
    </script>
</body>
</html>
