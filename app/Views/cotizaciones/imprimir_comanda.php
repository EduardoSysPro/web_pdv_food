<?php
/**
 * Impresión de comanda para cocina — ancho térmico 80mm, letra grande, sin precios.
 */
$nombreComanda  = (string)($configuracion['nombre_comanda'] ?? 'COMANDA');
$empresaNombre  = $configuracion['empresa_nombre'] ?? 'MI NEGOCIO';
$empresaDir     = $configuracion['empresa_direccion'] ?? '';
$empresaTel     = $configuracion['empresa_telefono'] ?? '';
$logoArchivo = $configuracion['logotipo_path'] ?? ($configuracion['logo'] ?? '');
$rutaLogo = !empty($logoArchivo) && file_exists(PUBLIC_PATH . 'uploads/' . $logoArchivo)
    ? URL_BASE . 'uploads/' . $logoArchivo : '';
$nombreClient = trim((string)($cotizacion['cliente_nombre'] ?? '')) !== ''
    ? $cotizacion['cliente_nombre'] : 'MESA';
$esParaLlevar = mb_stripos($nombreClient, 'para llevar') !== false;
$observaciones = (string)($cotizacion['observaciones'] ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($empresaNombre . ' - ' . $cotizacion['folio']); ?></title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    @page { size: 80mm auto; margin: 4mm; }
    body {
        width: 72mm;
        margin: 0 auto;
        font-family: 'Arial', sans-serif;
        font-size: 12px;
        color: #000;
        background: #fff;
    }

    .comanda-titulo {
        text-align: center;
        border: 3px solid #000;
        border-radius: 8px;
        padding: 6px 4px;
        margin: 4px 0 6px;
        font-size: 22px;
        font-weight: 900;
        letter-spacing: 2px;
    }

    .cabecera { text-align: center; margin-bottom: 8px; }
    .cabecera .negocio { font-size: 15px; font-weight: 800; text-transform: uppercase; }
    .cabecera .dato { font-size: 10px; }
    .cabecera img.logo { max-width: 48mm; max-height: 16mm; margin-bottom: 2px; }

    .folio {
        text-align: center;
        font-size: 24px;
        font-weight: 900;
        letter-spacing: 1px;
        padding: 2px 0;
        border-top: 2px solid #000;
        border-bottom: 2px solid #000;
        margin: 4px 0 6px;
    }

    .info { font-size: 11px; margin-bottom: 6px; }
    .info .fila { display: flex; justify-content: space-between; gap: 6px; }
    .info .etiqueta { font-weight: 800; }
    .info .mesa { font-size: 14px; font-weight: 900; }

    .llevar-tag {
        text-align: center;
        border: 2px solid #000;
        border-radius: 6px;
        padding: 4px;
        font-size: 15px;
        font-weight: 900;
        letter-spacing: 2px;
        margin: 4px 0 6px;
    }

    .leyenda { text-align: center; font-size: 13px; font-weight: 800; margin: 4px 0; }

    .articulos { border-top: 2px solid #000; padding-top: 4px; }
    .articulo { display: flex; gap: 6px; padding: 3px 0; align-items: flex-start; }
    .articulo .qty { font-weight: 900; font-size: 15px; min-width: 30px; text-align: center; }
    .articulo .nombre { font-size: 15px; font-weight: 800; flex: 1; line-height: 1.15; }
    .articulo .nota { font-size: 10px; font-style: italic; font-weight: 400; display: block; }

    .obs {
        border: 1px dashed #000;
        padding: 4px;
        margin-top: 6px;
        font-size: 11px;
    }
    .obs .titulo { font-weight: 800; }

    .fin { text-align: center; font-size: 11px; font-weight: 800; margin-top: 8px; letter-spacing: 1px; }
</style>
</head>
<body onload="window.print()">

    <?php if ($rutaLogo): ?>
        <div class="cabecera"><img class="logo" src="<?php echo htmlspecialchars($rutaLogo); ?>" alt="Logo"></div>
    <?php endif; ?>

    <div class="cabecera">
        <div class="negocio"><?php echo htmlspecialchars($empresaNombre); ?></div>
        <?php if ($empresaDir !== ''): ?>
            <div class="dato"><?php echo htmlspecialchars($empresaDir); ?></div>
        <?php endif; ?>
        <?php if ($empresaTel !== ''): ?>
            <div class="dato"><?php echo htmlspecialchars('Tel: ' . $empresaTel); ?></div>
        <?php endif; ?>
    </div>

    <div class="comanda-titulo"><?php echo htmlspecialchars($nombreComanda); ?></div>

    <div class="folio"><?php echo htmlspecialchars($cotizacion['folio']); ?></div>

    <?php if ($esParaLlevar): ?>
    <div class="llevar-tag">PARA LLEVAR</div>
    <?php endif; ?>

    <div class="info">
        <div class="fila">
            <span class="etiqueta">Mesa / Cliente:</span>
            <span class="mesa"><?php echo htmlspecialchars($nombreClient); ?></span>
        </div>
        <div class="fila">
            <span class="etiqueta">Fecha:</span>
            <span><?php echo date('d/m/Y h:i A', strtotime((string)$cotizacion['creada_en'])); ?></span>
        </div>
        <?php if (!empty($cotizacion['vendedor'])): ?>
        <div class="fila">
            <span class="etiqueta">Atiende:</span>
            <span><?php echo htmlspecialchars($cotizacion['vendedor']); ?></span>
        </div>
        <?php endif; ?>
    </div>

    <div class="leyenda">- - - - - - - - - -</div>

    <div class="articulos">
        <?php foreach ($detalles as $d):
            $nombreArticulo = (string)($d['nombre_producto'] ?? '');
            $presentacion = (string)($d['nombre_presentacion'] ?? '');
            $tipoPres = (string)($d['tipo_presentacion'] ?? 'unidad');
            if ($tipoPres === 'empaque' && $presentacion !== ''):
                $factor = rtrim(rtrim(number_format((float)($d['factor_unidades'] ?? 1), 2, '.', ''), '0'), '.');
                $nombreArticulo = '[' . $presentacion . ' x' . $factor . '] ' . $nombreArticulo;
            endif;
            $notaArticulo = (string)($d['nota'] ?? '');
        ?>
        <div class="articulo">
            <span class="qty"><?php echo htmlspecialchars(trim((string)($d['cantidad'] ?? ''))); ?></span>
            <span class="nombre">
                <?php echo htmlspecialchars($nombreArticulo); ?>
                <?php if ($notaArticulo !== ''): ?>
                    <span class="nota"><?php echo htmlspecialchars('Nota: ' . $notaArticulo); ?></span>
                <?php endif; ?>
            </span>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if ($observaciones !== ''): ?>
        <div class="obs">
            <span class="titulo">Nota general:</span>
            <?php echo htmlspecialchars($observaciones); ?>
        </div>
    <?php endif; ?>

    <div class="fin"><?php echo htmlspecialchars('- - - FIN DE ' . strtoupper($nombreComanda) . ' - - -'); ?></div>

</body>
</html>