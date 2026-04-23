# Mejoras Totales — Imperio Comercial Catálogo

## Goal
Llevar el catálogo de un ~40% de madurez a un producto completo y profesional: con precios, descripciones, panel de detalle por producto, mejor admin y UX de catálogo real.

---

## Decisiones confirmadas
- **Precio**: 2 campos — `precio_cuota` + `cantidad_cuotas` → muestra "12 cuotas de $8.500"
- **Descripción**: campo corto (max 300 chars) visible en card y panel
- **Detalle**: bottom sheet deslizable al tocar una card
- **Seguridad**: no en esta iteración

---

## FASE 1 — Base de datos (prerequisito de todo)

### Archivo: `index.php` y `admin.php` — bloque `$newCols` / `$setupCols`

Agregar estas columnas via el mecanismo de ALTER TABLE ya existente en ambos archivos:

```php
'precio_cuota'    => 'DECIMAL(10,2) NULL',
'cantidad_cuotas' => 'INT NULL',
'descripcion'     => 'VARCHAR(300) NULL',
'visible'         => 'TINYINT(1) NOT NULL DEFAULT 1',
```

> **Verificación**: abrir `http://localhost/cat/` sin errores; `SHOW COLUMNS FROM flyers` muestra las 4 columnas nuevas.

---

## FASE 2 — Admin panel (gestión de contenido)

### 2.1 Campos precio + descripción en formulario de subida
**Archivo**: `admin.php` — sección "Publicar Flyers"

Agregar después del campo "Título / Precio":
- Input `descripcion` (textarea, max 300, optional) — "Ej: 65 pulgadas, 4K, Smart TV, Android 13"
- Input `precio_cuota` (number, optional) — "Cuota $"
- Input `cantidad_cuotas` (number, optional) — "Cant. cuotas"
- PHP: incluir en INSERT de `subir_flyer`

### 2.2 Modal "Editar Flyer" ampliado
**Archivo**: `admin.php` — `#modal-flyer`

Agregar en el modal de edición:
- Textarea `descripcion`
- Input `precio_cuota`
- Input `cantidad_cuotas`
- PHP: incluir en UPDATE de `editar_flyer`

### 2.3 Toggle de visibilidad (`visible`)
**Archivo**: `admin.php` — fila de cada flyer

- Agregar botón `toggle_visible` (ojo abierto/cerrado) junto a los botones actuales
- PHP: `UPDATE flyers SET visible = 1 - visible WHERE id = $id`
- En index.php: `WHERE es_principal = 1 AND visible = 1`
- **Diferencia con `sin_stock`**: visible=0 oculta el producto del catálogo completamente; sin_stock=1 lo muestra con overlay

### 2.4 Operaciones masivas (bulk)
**Archivo**: `admin.php` — sección "Gestión de Flyers"

- Agregar checkbox en cada fila de flyer (`name="sel[]" value="<?= $f['id'] ?>"`)
- Agregar toolbar con: "Seleccionar todo", "Eliminar seleccionados", "Ocultar seleccionados", "Destacar seleccionados"
- PHP: procesar arrays de IDs con `IN ($ids_sanitizados)`
- Toast de confirmación con conteo

### 2.5 Búsqueda en admin
**Archivo**: `admin.php` — filtro de flyers

- Input de búsqueda por título (GET param `q`)
- Agregar al WHERE: `AND flyers.titulo LIKE '%$q%'`
- Mostrar término buscado con badge "x Limpiar"

### 2.6 Conteo de productos por categoría (stat cards)
**Archivo**: `admin.php` — ya tiene stat cards

- En "Gestión de Categorías": agregar badge con `$cat['flyer_count']` (ya existe en query)
- Mejorar visual: mostrar `(N productos)` inline en la fila de categoría

> **Verificación**: subir un producto con precio y descripción, verlo en la lista admin, editarlo, ocultarlo con visible=0, confirmar que no aparece en index.

---

## FASE 3 — Catálogo público (index.php)

### 3.1 Bottom Sheet — Panel de Detalle de Producto
**Archivo**: `index.php`

Nuevo componente HTML fijo al fondo (oculto por defecto):
```html
<div id="product-sheet" class="fixed inset-0 z-[75] hidden">
  <!-- Fondo oscuro -->
  <div id="sheet-backdrop" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
  <!-- Panel deslizable desde abajo -->
  <div id="sheet-panel" class="absolute bottom-0 left-0 right-0 max-w-4xl mx-auto
       glass-card rounded-t-3xl p-6 pb-32 max-h-[85vh] overflow-y-auto
       translate-y-full transition-transform duration-300 ease-out">
    <div class="w-10 h-1 bg-white/20 rounded-full mx-auto mb-5"></div>
    <img id="sheet-img" src="" class="w-full rounded-2xl mb-4 object-cover max-h-64">
    <div id="sheet-badge" class="text-xs font-bold gradient-text uppercase tracking-widest mb-1">Oferta</div>
    <h2 id="sheet-title" class="text-xl font-bold mb-2" style="font-family:var(--font-heading)"></h2>
    <p id="sheet-desc" class="text-sm mb-4 hidden" style="color:var(--text-secondary)"></p>
    <div id="sheet-price" class="glass-card p-3 mb-5 hidden">
      <div id="sheet-cuota" class="gradient-text text-2xl font-bold"></div>
      <div id="sheet-total" class="text-xs" style="color:var(--text-secondary)"></div>
    </div>
    <button id="sheet-wa" onclick="" class="btn-warm w-full py-3.5 font-bold text-base mb-3">
      <i class="fa-brands fa-whatsapp text-xl"></i> Consultar por WhatsApp
    </button>
    <button id="sheet-share" onclick="" class="w-full py-3 rounded-xl font-semibold text-sm"
            style="background:var(--bg-card);border:1px solid var(--border-soft);color:var(--text-secondary)">
      <i class="fa-solid fa-share-nodes"></i> Compartir
    </button>
  </div>
</div>
```

**JS**: `openProductSheet(flyerId)` — busca el flyer en `DB.flyers`, llena campos, anima panel con `translate-y-0`.
**Cambio en cards**: el click ya no abre lightbox directamente sino `openProductSheet()`. El lightbox se activa desde un botón "Ver fotos" dentro del sheet (si el producto tiene galería).

### 3.2 Tarjeta de producto actualizada
**Archivo**: `index.php` — `createFlyerHTML()`

Agregar debajo del título:
```js
// Descripción (si existe)
${f.descripcion ? `<p class="text-xs line-clamp-2" style="color:var(--text-secondary)">${f.descripcion}</p>` : ''}

// Precio
${f.precio_cuota ? `
<div class="mt-auto pt-2 border-t" style="border-color:var(--border-subtle)">
  <span class="gradient-text font-bold text-sm">${f.cantidad_cuotas}x $${Number(f.precio_cuota).toLocaleString('es-AR')}</span>
</div>` : ''}
```

### 3.3 Badges de cantidad por categoría
**Archivo**: `index.php` — PHP query de categorías

Cambiar query de categorías para incluir conteo:
```php
$result_cats = $conn->query(
  "SELECT c.*, COUNT(f.id) as producto_count
   FROM categorias c
   LEFT JOIN flyers f ON f.categoria_id = c.id AND f.es_principal = 1 AND f.visible = 1
   GROUP BY c.id ORDER BY c.nombre ASC"
);
```

En el JS de `renderHome()`, agregar al card de categoría:
```js
<span class="text-xs px-2 py-0.5 rounded-full" style="background:var(--bg-muted);color:var(--text-muted)">
  ${c.producto_count || 0}
</span>
```

### 3.4 Búsqueda mejorada
**Archivo**: `index.php` — `performSearch()`

- Buscar también en `descripcion` además de `titulo`
- Mostrar conteo de resultados: `"${r.length} resultados para '${query}'"`
- Debounce: agregar `clearTimeout` + `setTimeout(300ms)` en el listener de input
- Mensaje de vacío si no hay resultados: ícono + "No encontramos '${query}'. ¿Consultamos por WhatsApp?"

### 3.5 Out-of-stock UX mejorado
**Archivo**: `index.php` — `createFlyerHTML()` + `openProductSheet()`

- Si `sin_stock == 1`: deshabilitar `openProductSheet()` al click en la card
- Mostrar botón "Avisar cuando haya stock" → abre WhatsApp con mensaje "Hola, quiero que me avisen cuando esté disponible: [título]"
- Card con opacidad reducida: `opacity-60`

### 3.6 Lazy loading de imágenes
**Archivo**: `index.php` — templates JS de cards

Agregar `loading="lazy"` a todas las `<img>` y cambiar `.flyer-card` de `background-image` inline a `<img>` real para aprovechar lazy loading nativo:
```js
<img src="${f.imagen_url}" loading="lazy" class="flyer-card object-cover w-full" alt="${f.titulo}">
```

### 3.7 Skeleton loaders
**Archivo**: `index.php`

Mostrar skeletons mientras `renderHome()` genera el contenido:
```js
// Antes del render real, mostrar 4 skeletons
container.innerHTML = `<div class="grid grid-cols-2 gap-3">
  ${[...Array(4)].map(() => `
    <div class="glass-card overflow-hidden">
      <div class="skeleton aspect-[4/5]"></div>
      <div class="p-3 space-y-2">
        <div class="skeleton h-3 rounded w-3/4"></div>
        <div class="skeleton h-3 rounded w-1/2"></div>
      </div>
    </div>`).join('')}
</div>`;
setTimeout(() => { /* render real */ }, 50);
```

### 3.8 Filtro y orden en vista de categoría
**Archivo**: `index.php` — `renderCategory()`

Agregar toolbar encima de la grilla:
- Sort: "Destacados primero" (default) | "Más recientes" | "Precio menor"
- Aplicado via `.sort()` en JS sobre `allItems` antes de render

> **Verificación**: abrir el catálogo, tocar un producto → aparece panel desde abajo con precio y descripción. Botón "Consultar por WA" abre WhatsApp con el nombre del producto. Buscar "Samsung" → resultados con conteo. Categoría muestra N productos.

---

## FASE 4 — Pulido final

### 4.1 Número de WhatsApp centralizado
**Archivo**: `index.php`, `requisitos.php`, `admin.php`

Extraer el número a una constante en `db.php`:
```php
define('WA_NUM', '+5493815447588');
```
Reemplazar todas las ocurrencias hardcodeadas (`+5493815447588` aparece 3 veces en index.php, 1 en requisitos.php).

### 4.2 Mensaje de WhatsApp por producto
**Archivo**: `index.php` — función `contactWhatsApp()` ya existente

Cuando se abre el sheet de un producto:
```js
const msg = `Hola! Vi este producto en Imperio Comercial:\n*${titulo}*\n¿Me podés dar más información?`;
```

### 4.3 Ocultar productos con `visible=0` en catálogo
**Archivo**: `index.php` — query PHP de flyers

```php
$whereEsP = "WHERE es_principal = 1 AND visible = 1";
```

---

## Archivos modificados

| Archivo | Cambios |
|---|---|
| `index.php` | Columnas nuevas en setup, query con visible=1, categorías con count, renderHome+Category, createFlyerHTML, bottom sheet, search mejorado, lazy loading, skeletons |
| `admin.php` | Columnas nuevas en setup, campos precio+desc en form+modal, toggle visible, bulk ops, búsqueda admin |
| `db.php` | `define('WA_NUM', ...)` |

---

## Orden de ejecución

1. **db.php** — constante WA_NUM (5 min)
2. **index.php + admin.php** — agregar 4 columnas al bloque setup (10 min)
3. **admin.php** — campos precio + descripción en form y modal (30 min)
4. **admin.php** — toggle visible + bulk ops + búsqueda (45 min)
5. **index.php** — bottom sheet HTML + JS (45 min)
6. **index.php** — cards con precio + descripción (20 min)
7. **index.php** — badges categoría + count query (20 min)
8. **index.php** — búsqueda mejorada + debounce (20 min)
9. **index.php** — out-of-stock UX + lazy loading + skeletons (20 min)
10. **index.php** — sort en categorías (15 min)

**Total estimado**: ~4 horas

---

## Done When
- [ ] Productos tienen precio y descripción editables desde admin
- [ ] Al tocar un producto en el catálogo sube un panel con toda la info
- [ ] El botón de WhatsApp dentro del panel incluye el nombre del producto
- [ ] Las categorías muestran cuántos productos tienen
- [ ] Buscar "samsung" encuentra resultados en título Y descripción
- [ ] Productos con `visible=0` no aparecen en el catálogo pero sí en admin
- [ ] Admin puede seleccionar varios flyers y eliminarlos/ocultarlos juntos
- [ ] Imágenes usan `loading="lazy"`
