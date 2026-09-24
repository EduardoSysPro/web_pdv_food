<?php $tituloPagina = $titulo; require APP_PATH . 'Views/layouts/pos_header.php'; ?>
<section class="catalogo-encabezado"><div><span class="eyebrow">F3 / Catálogo</span><h1><?php echo htmlspecialchars($titulo); ?></h1><p>Completa la información comercial y de inventario.</p></div><a class="btn-pos btn-secondary" href="<?php echo URL_BASE; ?>productos">Volver al catálogo</a></section>
<section class="tarjeta formulario-producto">
    <?php foreach (($errores ?? []) as $error): ?><div class="alerta alerta-error"><?php echo htmlspecialchars($error); ?></div><?php endforeach; ?>
    <form method="POST" action="<?php echo $accion; ?>" id="form-producto" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
        <div class="form-grid">
            <div class="campo">
                <label for="codigo_barras">Código de barras</label>
                <div style="display:flex; gap:8px; align-items:center;">
                    <input id="codigo_barras" name="codigo_barras" value="<?php echo htmlspecialchars($producto['codigo_barras'] ?? ''); ?>" maxlength="50" class="codigo-barras-input" style="flex:1;">
                    <button type="button" id="generar-codigo-interno" class="btn-pos btn-secondary" style="white-space:nowrap;">⚡ Generar Código</button>
                </div>
                <div id="barcode-preview-wrapper" style="display:none; margin-top:10px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:8px; text-align:center;">
                    <svg id="barcode-preview" style="max-width:100%; height:70px;"></svg>
                </div>
            </div>
            <div class="campo campo-ancho"><label for="nombre">Descripción / nombre</label><input id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre'] ?? ''); ?>" maxlength="150" required></div>
            <div class="campo"><label for="categoria_id">Categoría</label><select id="categoria_id" name="categoria_id"><option value="">Sin categoría</option><?php foreach ($categorias as $categoria): ?><option value="<?php echo (int)$categoria['id']; ?>" <?php echo (string)($producto['categoria_id'] ?? '') === (string)$categoria['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($categoria['nombre']); ?></option><?php endforeach; ?></select></div>
            <div class="campo"><label for="precio_costo">Precio costo (L)</label><input id="precio_costo" name="precio_costo" type="number" min="0" step="0.01" value="<?php echo htmlspecialchars($producto['precio_costo'] ?? '0.00'); ?>" required></div>
            <div class="campo"><label for="precio_venta">Precio venta (L)</label><input id="precio_venta" name="precio_venta" type="number" min="0" step="0.01" value="<?php echo htmlspecialchars($producto['precio_venta'] ?? '0.00'); ?>" required></div>
            <div class="campo">
                <label for="tipo_impuesto">Impuesto (ISV)</label>
                <select id="tipo_impuesto" name="tipo_impuesto">
                    <?php $tipoImpuestoActual = $producto['tipo_impuesto'] ?? 'gravado_15'; ?>
                    <option value="exento" <?php echo $tipoImpuestoActual === 'exento' ? 'selected' : ''; ?>>Exento 0%</option>
                    <option value="gravado_15" <?php echo $tipoImpuestoActual === 'gravado_15' ? 'selected' : ''; ?>>ISV 15%</option>
                    <option value="gravado_18" <?php echo $tipoImpuestoActual === 'gravado_18' ? 'selected' : ''; ?>>ISV 18%</option>
                    <option value="exonerado" <?php echo $tipoImpuestoActual === 'exonerado' ? 'selected' : ''; ?>>Exonerado 0%</option>
                </select>
            </div>
            <div class="campo" id="campo-stock"><label for="stock">Stock actual</label><input id="stock" name="stock" type="number" min="0" step="0.001" value="<?php echo htmlspecialchars((string)($producto['stock'] ?? '0')); ?>" required></div>
            <div class="campo" id="campo-stock-minimo"><label for="stock_minimo">Stock mínimo</label><input id="stock_minimo" name="stock_minimo" type="number" min="0" step="0.001" value="<?php echo htmlspecialchars((string)($producto['stock_minimo'] ?? '1')); ?>" required></div>
            <div class="campo campo-ancho">
                <label for="controlar_stock" style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:700;">
                    <input type="checkbox" id="controlar_stock" name="controlar_stock" value="1" <?php echo (($producto['controlar_stock'] ?? 1) == 1) ? 'checked' : ''; ?>>
                    Controlar inventario (descontar del stock al vender)
                </label>
                <small style="color:#64748b; font-size:12px;">Desmárcalo para platos y comidas preparadas al momento: se venden sin límite de existencias y no descuentan inventario.</small>
            </div>
            <div class="campo campo-ancho">
                <label for="es_combo" style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:700;">
                    <input type="checkbox" id="es_combo" name="es_combo" value="1" <?php echo (($producto['es_combo'] ?? 0) == 1) ? 'checked' : ''; ?>>
                    🍽️ Es un combo / paquete (ej.: 1 pieza de pollo + 1 papa + 1 refresco)
                </label>
                <small style="color:#64748b; font-size:12px;">El combo se vende como una sola línea en el ticket. En la comanda de cocina se muestran sus componentes; cada componente descuenta inventario según su propia configuración (solo los que controlan inventario).</small>
            </div>

            <div id="contenedor-componentes-combo" class="campo campo-ancho" style="margin-top:14px; padding:16px; background:#f0fdf4; border:1px solid #86efac; border-radius:8px; <?php echo (($producto['es_combo'] ?? 0) == 1) ? '' : 'display:none;'; ?>">
                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:12px;">
                    <strong style="font-size:14px; color:#166534;">Componentes del combo</strong>
                    <small style="color:#64748b; font-size:12px;">La cantidad se repite por cada combo vendido</small>
                </div>
                <div class="form-grid" id="filas-componentes" style="grid-template-columns:1fr 130px 40px; gap:8px; align-items:center;">
                    <?php if (count($componentes ?? []) === 0): ?>
                        <div style="display:contents;"><div class="campo" style="margin:0;"><select name="componente_id[]" required><?php foreach ($todosProductos as $opc): ?><?php if ((int)$opc['id'] === (int)($producto['id'] ?? 0)) continue; ?><?php if (!empty($opc['es_combo'])) continue; ?><option value="<?php echo (int)$opc['id']; ?>"><?php echo htmlspecialchars($opc['nombre']); ?> (L <?php echo number_format((float)$opc['precio_venta'], 2); ?>)</option><?php endforeach; ?></select></div><div class="campo" style="margin:0;"><input type="number" name="componente_cantidad[]" value="1" min="0.001" step="0.001" required></div><button type="button" class="btn-pos btn-secondary quitar-componente" style="padding:8px; line-height:1;" title="Quitar">✕</button></div>
                    <?php else: ?>
                        <?php foreach (($componentes ?? []) as $comp): ?>
                            <div style="display:contents;"><div class="campo" style="margin:0;"><select name="componente_id[]" required><?php foreach ($todosProductos as $opc): ?><?php if ((int)$opc['id'] === (int)($producto['id'] ?? 0)) continue; ?><?php if (!empty($opc['es_combo'])) continue; ?><option value="<?php echo (int)$opc['id']; ?>" <?php echo (int)($comp['producto_id'] ?? 0) === (int)$opc['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($opc['nombre']); ?> (L <?php echo number_format((float)$opc['precio_venta'], 2); ?>)</option><?php endforeach; ?></select></div><div class="campo" style="margin:0;"><input type="number" name="componente_cantidad[]" value="<?php echo htmlspecialchars((string)($comp['cantidad'] ?? 1)); ?>" min="0.001" step="0.001" required></div><button type="button" class="btn-pos btn-secondary quitar-componente" style="padding:8px; line-height:1;" title="Quitar">✕</button></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <button type="button" id="agregar-componente" class="btn-pos btn-secondary" style="margin-top:12px;">+ Agregar componente</button>
                <input type="hidden" id="opciones-productos" value="<?php echo htmlspecialchars(json_encode($todosProductos, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" id="id-producto-actual" value="<?php echo (int)($producto['id'] ?? 0); ?>">
            </div>
        </div>

        <!-- ================= FOTO DEL PRODUCTO (OPCIONAL) ================= -->
        <div class="campo campo-ancho" style="margin-top:20px; padding-top:16px; border-top:1px solid #dbe1ea;">
            <label for="imagen" style="font-weight:700; font-size:14px; color:#1e293b;">📷 Foto del producto (opcional)</label>
            <input id="imagen" name="imagen" type="file" accept="image/jpeg,image/png,image/webp" style="margin-top:6px;">
            <small style="color:#64748b; font-size:12px;">JPG, PNG o WebP de hasta 2 MB. Se mostrará en el buscador del vendedor y del POS.</small>
            <?php if (!empty($producto['imagen'])): ?>
                <div id="imagen-actual" style="margin-top:10px; display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                    <img src="<?php echo URL_BASE; ?>uploads/productos/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="Foto del producto" style="width:64px; height:64px; object-fit:cover; border-radius:8px; border:1px solid #cbd5e1;">
                    <label style="display:flex; align-items:center; gap:6px; font-size:13px; cursor:pointer;">
                        <input type="checkbox" name="quitar_imagen" value="1" id="quitar_imagen"> Quitar foto actual
                    </label>
                </div>
            <?php else: ?>
                <div id="imagen-actual"></div>
            <?php endif; ?>
            <div id="vista-previa-imagen" style="margin-top:10px; display:none;">
                <img id="img-previa-producto" alt="Vista previa" style="width:64px; height:64px; object-fit:cover; border-radius:8px; border:1px solid #cbd5e1;">
            </div>
        </div>
        <div class="ganancia-calculo"><span>Ganancia (Unidad): <strong id="ganancia">0.00%</strong></span><button type="button" class="btn-pos btn-secondary" id="sugerir-precio">Sugerir venta +30%</button></div>
        <div class="form-acciones"><a class="btn-pos btn-secondary" href="<?php echo URL_BASE; ?>productos">Cancelar</a><button class="btn-pos btn-success" type="submit">Guardar Producto</button></div>
    </form>
</section>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
(function () {
    const costo = document.getElementById('precio_costo');
    const venta = document.getElementById('precio_venta');
    const ganancia = document.getElementById('ganancia');
    const codigoInput = document.getElementById('codigo_barras');
    const botonGenerar = document.getElementById('generar-codigo-interno');
    const previewWrapper = document.getElementById('barcode-preview-wrapper');

    function generarCodigoInterno() {
        const prefijo = '20';
        const aleatorio = String(Math.floor(Math.random() * 9999999999)).padStart(10, '0');
        let codigo = prefijo + aleatorio;
        if (codigo.length > 13) {
            codigo = codigo.substring(0, 13);
        }
        if (!/^\d{12,13}$/.test(codigo)) {
            return generarCodigoInterno();
        }
        return codigo;
    }

    function actualizarPreviewCodigo() {
        const valor = (codigoInput.value || '').trim();
        if (!valor || !/^\d{12,13}$/.test(valor) || typeof window.JsBarcode === 'undefined') {
            if (previewWrapper) previewWrapper.style.display = 'none';
            return;
        }
        if (previewWrapper) previewWrapper.style.display = 'block';
        try {
            window.JsBarcode('#barcode-preview', valor, {
                format: 'CODE128',
                displayValue: true,
                fontSize: 13,
                margin: 10,
                width: 2,
                height: 50,
                background: '#ffffff'
            });
        } catch (error) {
            console.warn('No se pudo dibujar el código de barras:', error);
        }
    }

    function calcular() { const c = Number(costo.value) || 0; const v = Number(venta.value) || 0; ganancia.textContent = (c ? ((v - c) / c * 100).toFixed(2) : '0.00') + '%'; }
    costo.addEventListener('input', calcular); venta.addEventListener('input', calcular);
    document.getElementById('sugerir-precio').addEventListener('click', function () { venta.value = ((Number(costo.value) || 0) * 1.3).toFixed(2); calcular(); });
    if (botonGenerar) {
        botonGenerar.addEventListener('click', function () {
            const codigo = generarCodigoInterno();
            if (codigoInput) codigoInput.value = codigo;
            actualizarPreviewCodigo();
        });
    }
    if (codigoInput) {
        codigoInput.addEventListener('input', actualizarPreviewCodigo);
        codigoInput.addEventListener('change', actualizarPreviewCodigo);
    }
    calcular();
    actualizarPreviewCodigo();

    // ================= FOTO DEL PRODUCTO (PREVIEW) =================
    const inputImagen = document.getElementById('imagen');
    const previaImagen = document.getElementById('vista-previa-imagen');
    const imagenPreviaImg = document.getElementById('img-previa-producto');
    const chkQuitar = document.getElementById('quitar_imagen');
    if (inputImagen && previaImagen) {
        inputImagen.addEventListener('change', function () {
            if (this.files && this.files.length > 0) {
                const archivo = this.files[0];
                if (archivo.size > 2 * 1024 * 1024) {
                    alert('La imagen no puede superar los 2 MB.');
                    this.value = '';
                    return;
                }
                imagenPreviaImg.src = URL.createObjectURL(archivo);
                previaImagen.style.display = 'block';
                if (chkQuitar) chkQuitar.checked = false;
            } else {
                previaImagen.style.display = 'none';
            }
        });
    }

    // ================= CONTROL DE INVENTARIO OPCIONAL =================
    const controlarStockChk = document.getElementById('controlar_stock');
    const campoStock = document.getElementById('campo-stock');
    const campoStockMinimo = document.getElementById('campo-stock-minimo');

    function actualizarVisibilidadInventario() {
        const activo = controlarStockChk ? controlarStockChk.checked : true;
        if (campoStock) campoStock.style.display = activo ? '' : 'none';
        if (campoStockMinimo) campoStockMinimo.style.display = activo ? '' : 'none';
    }

    if (controlarStockChk) {
        controlarStockChk.addEventListener('change', actualizarVisibilidadInventario);
    }
    actualizarVisibilidadInventario();

    // ================= COMBO / PAQUETE =================
    const esComboChk = document.getElementById('es_combo');
    const contenedorComponentes = document.getElementById('contenedor-componentes-combo');
    const filasComponentes = document.getElementById('filas-componentes');
    const btnAgregarComponente = document.getElementById('agregar-componente');
    let opcionesProductos = [];
    try {
        opcionesProductos = JSON.parse(document.getElementById('opciones-productos')?.value || '[]');
    } catch (error) {
        opcionesProductos = [];
    }
    const idProductoActual = parseInt(document.getElementById('id-producto-actual')?.value || '0', 10);

    function escaparEtiqueta(texto) {
        const d = document.createElement('div');
        d.textContent = texto;
        return d.innerHTML;
    }

    function opcionesSelectComponente(seleccionado) {
        return opcionesProductos
            .filter(p => Number(p.id) !== idProductoActual && !Number(p.es_combo))
            .map(p => '<option value="' + p.id + '"' + (Number(p.id) === Number(seleccionado) ? ' selected' : '') + '>' +
                escaparEtiqueta(p.nombre) + ' (L ' + (Number(p.precio_venta) || 0).toFixed(2) + ')</option>')
            .join('');
    }

    function crearFilaComponente(sel, cant) {
        const fila = document.createElement('div');
        fila.style.cssText = 'display:contents;';
        fila.innerHTML =
            '<div class="campo" style="margin:0;"><select name="componente_id[]" required>' + opcionesSelectComponente(sel) + '</select></div>' +
            '<div class="campo" style="margin:0;"><input type="number" name="componente_cantidad[]" value="' + cant + '" min="0.001" step="0.001" required></div>' +
            '<button type="button" class="btn-pos btn-secondary quitar-componente" style="padding:8px; line-height:1;" title="Quitar">✕</button>';
        fila.querySelector('.quitar-componente').addEventListener('click', function () { fila.remove(); });
        return fila;
    }

    if (esComboChk) {
        esComboChk.addEventListener('change', function () {
            const esCombo = this.checked;
            if (contenedorComponentes) contenedorComponentes.style.display = esCombo ? 'block' : 'none';
            if (controlarStockChk && esCombo) controlarStockChk.checked = false;
            actualizarVisibilidadInventario();
        });
    }
    if (btnAgregarComponente && filasComponentes) {
        btnAgregarComponente.addEventListener('click', function () {
            filasComponentes.appendChild(crearFilaComponente('', '1'));
        });
        filasComponentes.querySelectorAll('.quitar-componente').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const contenedor = btn.closest('div[style*="display:contents"]');
                if (contenedor) contenedor.remove();
            });
        });
    }
}());
</script>
<?php require APP_PATH . 'Views/layouts/footer.php'; ?>
