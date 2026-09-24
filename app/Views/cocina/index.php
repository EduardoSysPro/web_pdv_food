<?php $tituloPagina = 'Cocina / Comandas'; require APP_PATH . 'Views/layouts/pos_header.php'; ?>
<link rel="stylesheet" href="<?php echo URL_BASE; ?>css/pos_catalogo.css?v=<?php echo filemtime(PUBLIC_PATH . 'css/pos_catalogo.css'); ?>">
<style>
    .cocina-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 16px; padding: 16px; }
    .cocina-tarjeta { background: var(--color-tarjeta, #fff); border: 1px solid var(--borde, #e2e8f0); border-radius: 14px; padding: 14px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
    .cocina-cabecera { display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-bottom: 4px; }
    .cocina-folio { font-size: 20px; font-weight: 800; letter-spacing: 1px; }
    .cocina-estado { font-size: 12px; font-weight: 800; padding: 3px 10px; border-radius: 999px; text-transform: uppercase; }
    .est-no_confirmada, .est-pendiente { background: #fef3c7; color: #92400e; }
    .est-en_cocina { background: #dbeafe; color: #1e40af; }
    .est-listo { background: #dcfce7; color: #166534; }
    .cocina-meta { display: flex; gap: 12px; font-size: 13px; color: var(--texto-suave, #64748b); margin-bottom: 8px; flex-wrap: wrap; }
    .cocina-item { display: flex; align-items: center; gap: 10px; padding: 7px 0; border-top: 1px dashed var(--borde, #e2e8f0); }
    .cocina-item .qty { min-width: 34px; font-weight: 800; font-size: 16px; text-align: center; }
    .cocina-item .nombre { flex: 1; font-weight: 700; line-height: 1.2; }
    .cocina-item .nota { font-weight: 400; font-size: 12px; font-style: italic; display: block; color: var(--texto-suave, #64748b); }
    .cocina-item select { padding: 4px 6px; border-radius: 8px; border: 1px solid var(--borde, #cbd5e1); font-size: 12px; background: var(--color-tarjeta, #fff); }
    .cocina-acciones { display: flex; gap: 8px; margin-top: 10px; }
    .cocina-acciones button { flex: 1; padding: 8px; border: 0; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer; }
    .btn-cocina { background: #1d4ed8; color: #fff; }
    .btn-listo { background: #16a34a; color: #fff; }
    .btn-servida { background: #0f766e; color: #fff; }
    .cocina-vacio { text-align: center; padding: 60px 20px; color: var(--texto-suave, #64748b); font-size: 16px; }
    .cocina-refresco { display: flex; justify-content: flex-end; padding: 8px 16px; }
    .cocina-refresco span { font-size: 12px; color: var(--texto-suave, #64748b); }
</style>

<section class="catalogo-encabezado"><div><span class="eyebrow">Cocina</span><h1>Comandas pendientes</h1><p>Prepara los pedidos y avanza su estado.</p></div></section>

<div class="cocina-refresco"><span id="cocina-timer">Actualizando cada <?php echo (int)($refresco_seg ?? 15); ?> s…</span></div>

<div class="cocina-grid" id="cocina-grid">
    <?php if (empty($comandas)): ?>
    <div class="cocina-vacio" id="cocina-vacio">No hay comandas pendientes en este momento.</div>
    <?php endif; ?>

    <?php foreach ($comandas as $comanda): ?>
    <div class="cocina-tarjeta" data-comanda="<?php echo (int)$comanda['id']; ?>" id="comanda-<?php echo (int)$comanda['id']; ?>">
        <div class="cocina-cabecera">
            <span class="cocina-folio"><?php echo htmlspecialchars($comanda['folio']); ?></span>
            <span class="cocina-estado est-<?php echo htmlspecialchars($comanda['estado']); ?>"><?php echo htmlspecialchars($comanda['estado']); ?></span>
        </div>
        <div class="cocina-meta">
            <span><i class="fa-solid fa-chair"></i> <?php echo htmlspecialchars(trim((string)$comanda['cliente_nombre']) !== '' ? $comanda['cliente_nombre'] : 'Mesa'); ?></span>
            <span><i class="fa-solid fa-clock"></i> <?php echo date('h:i A', strtotime((string)$comanda['creada_en'])); ?></span>
            <span><i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($comanda['vendedor'] ?? ''); ?></span>
        </div>
        <?php foreach (($comanda['detalles'] ?? []) as $d): ?>
        <div class="cocina-item">
            <span class="qty"><?php echo htmlspecialchars(trim((string)$d['cantidad'])); ?></span>
            <span class="nombre"><?php echo htmlspecialchars($d['nombre_producto'] ?? ''); ?>
                <?php if (!empty($d['nota'])): ?><span class="nota"><?php echo htmlspecialchars('Nota: ' . $d['nota']); ?></span><?php endif; ?>
            </span>
            <select data-item-id="<?php echo (int)$d['id']; ?>" data-comanda-id="<?php echo (int)$comanda['id']; ?>">
                <?php foreach (['pendiente', 'en_cocina', 'listo'] as $e):
                    $label = ['pendiente' => 'Pendiente', 'en_cocina' => 'En cocina', 'listo' => 'Listo'][$e]; ?>
                <option value="<?php echo $e; ?>" <?php echo ($d['estado_item'] ?? 'pendiente') === $e ? 'selected' : ''; ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endforeach; ?>
        <?php if (!empty($comanda['observaciones'])): ?>
        <div class="cocina-meta" style="margin-top:8px;"><span><i class="fa-solid fa-comment"></i> <?php echo htmlspecialchars($comanda['observaciones']); ?></span></div>
        <?php endif; ?>
        <div class="cocina-acciones">
            <button type="button" class="btn-cocina" data-comanda-id="<?php echo (int)$comanda['id']; ?>" data-estado="en_cocina">En cocina</button>
            <button type="button" class="btn-listo" data-comanda-id="<?php echo (int)$comanda['id']; ?>" data-estado="listo">Listo</button>
            <button type="button" class="btn-servida" data-comanda-id="<?php echo (int)$comanda['id']; ?>" data-estado="servida">Servida</button>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script>
    (function () {
        var URL_BASE = '<?php echo URL_BASE; ?>';
        var REFRESCO_SEG = <?php echo (int)($refresco_seg ?? 15); ?>;
        var meta = document.querySelector('meta[name="csrf-token"]');
        var csrf = meta ? meta.getAttribute('content') : '';

        function publicar(url, datos) {
            marcarPeticion(true);
            return fetch(URL_BASE + url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                credentials: 'same-origin',
                body: new URLSearchParams(Object.assign({ csrf_token: csrf }, datos)).toString()
            }).then(function (r) { return r.json(); }).catch(function () {
                return { exito: false, mensaje: 'No se pudo conectar con el servidor.' };
            }).finally(function () {
                marcarPeticion(false);
            });
        }

        var peticionesEnCurso = 0;

        function marcarPeticion(inicio) {
            peticionesEnCurso += inicio ? 1 : -1;
            if (peticionesEnCurso < 0) peticionesEnCurso = 0;
        }

        function recargar() {
            window.location.reload();
        }

        function actualizarBadge(tarjeta, estado) {
            var badge = tarjeta.querySelector('.cocina-estado');
            if (!badge) return;
            badge.className = 'cocina-estado est-' + estado;
            badge.textContent = estado;
        }

        function mostrarVacioSiAplica() {
            var grid = document.getElementById('cocina-grid');
            var vacio = document.getElementById('cocina-vacio');
            if (!grid || !vacio) return;
            vacio.style.display = grid.querySelectorAll('.cocina-tarjeta').length === 0 ? 'block' : 'none';
        }

        document.querySelectorAll('.cocina-acciones button').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = btn.dataset.comandaId;
                var estado = btn.dataset.estado;
                if (!window.confirm('¿Marcar la comanda como "' + estado + '"?')) return;
                publicar('cocina/estado-comanda/' + id, { estado: estado }).then(function (res) {
                    if (res && res.exito) {
                        var tarjeta = document.getElementById('comanda-' + id);
                        if (estado === 'servida') {
                            if (tarjeta) { tarjeta.remove(); mostrarVacioSiAplica(); }
                        } else if (tarjeta) {
                            actualizarBadge(tarjeta, estado);
                            recargar();
                        } else {
                            recargar();
                        }
                    } else {
                        alert((res && res.mensaje) || 'No se pudo actualizar.');
                    }
                });
            });
        });

        document.querySelectorAll('.cocina-item select').forEach(function (sel) {
            sel.addEventListener('change', function () {
                var itemId = sel.dataset.itemId;
                var estado = sel.value;
                publicar('cocina/estado-item/' + itemId, { estado: estado }).then(function (res) {
                    if (!res || !res.exito) { alert((res && res.mensaje) || 'No se pudo actualizar el artículo.'); }
                });
            });
        });

        var segundos = REFRESCO_SEG;
        var timer = document.getElementById('cocina-timer');
        window.setInterval(function () {
            if (peticionesEnCurso > 0) return;
            segundos--;
            if (timer) timer.textContent = 'Actualizando en ' + segundos + ' s…';
            if (segundos <= 0) {
                recargar();
                segundos = REFRESCO_SEG;
            }
        }, 1000);
    })();
</script>
<?php require APP_PATH . 'Views/layouts/footer.php'; ?>