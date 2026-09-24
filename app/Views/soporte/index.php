<?php $tituloPagina = 'Soporte'; require APP_PATH . 'Views/layouts/pos_header.php'; ?>

<section class="catalogo-encabezado soporte-encabezado">
    <div>
        <span class="eyebrow">Administración</span>
        <h1>Soporte y guía del sistema</h1>
        <p>Conoce cómo funciona cada módulo del punto de venta y contáctanos directamente por WhatsApp si necesitas ayuda.</p>
    </div>
</section>

<!-- ============ PUNTO DE VENTA ============ -->
<h2 class="soporte-seccion"><i class="fa-solid fa-cash-register" aria-hidden="true"></i> Punto de venta</h2>
<div class="soporte-guia-grid">

    <!-- Ventas -->
    <section class="tarjeta soporte-modulo">
        <div class="soporte-modulo-top">
            <div class="soporte-modulo-icon"><i class="fa-solid fa-cart-shopping"></i></div>
            <div>
                <div class="soporte-modulo-titulo">Ventas (POS)</div>
                <span class="soporte-modulo-ruta">/ventas</span>
            </div>
        </div>
        <p class="soporte-modulo-desc">El corazón del sistema. Agrega productos escaneando el código de barras o buscando por nombre, mantén varias cuentas (tickets) abiertas a la vez —se conservan aunque recargues la página—, ajusta la cantidad y el precio de cada artículo y registra artículos "varios" que no están en el catálogo. Al cobrar eliges el método de pago (efectivo, tarjeta, transferencia o crédito), el comprobante (recibo o factura) e imprimes en la impresora de red (LAN) o por el navegador.</p>
        <div class="soporte-chips">
            <span class="soporte-chip">Multiticket</span>
            <span class="soporte-chip">Recibo / Factura</span>
            <span class="soporte-chip">Impresión LAN</span>
            <span class="soporte-chip">Crédito a clientes</span>
        </div>
    </section>

    <!-- Modo Restaurante -->
    <section class="tarjeta soporte-modulo">
        <div class="soporte-modulo-top">
            <div class="soporte-modulo-icon soporte-icon-verde"><i class="fa-solid fa-utensils"></i></div>
            <div>
                <div class="soporte-modulo-titulo">Modo Restaurante</div>
                <span class="soporte-modulo-ruta">/ventas</span>
            </div>
        </div>
        <p class="soporte-modulo-desc">Cuando el negocio opera en modo restaurante, la pantalla de ventas se convierte en un menú visual: tarjetas con foto, precio y existencias organizadas por categorías. Cada pedido se marca como "Comer aquí" o "Para llevar", se le asigna una mesa y, al cobrar, el sistema imprime automáticamente la comanda de cocina (letra grande y sin precios) para que preparen el pedido. También se manejan combos.</p>
        <div class="soporte-chips">
            <span class="soporte-chip">Menú visual</span>
            <span class="soporte-chip">Mesas</span>
            <span class="soporte-chip">Comanda de cocina</span>
            <span class="soporte-chip">Combos</span>
        </div>
    </section>

</div>

<!-- ============ ADMINISTRACIÓN ============ -->
<h2 class="soporte-seccion"><i class="fa-solid fa-briefcase" aria-hidden="true"></i> Administración del negocio</h2>
<div class="soporte-guia-grid">

    <!-- Clientes -->
    <section class="tarjeta soporte-modulo">
        <div class="soporte-modulo-top">
            <div class="soporte-modulo-icon"><i class="fa-solid fa-users"></i></div>
            <div>
                <div class="soporte-modulo-titulo">Clientes</div>
                <span class="soporte-modulo-ruta">/clientes</span>
            </div>
        </div>
        <p class="soporte-modulo-desc">Registra clientes con su RTN/identidad, teléfono y dirección; controla su límite y saldo de crédito, revisa el estado de cuenta (cargos y abonos) y registra pagos de créditos con su comprobante. Durante el cobro también puedes buscarlos y autocompletar sus datos por nombre o RTN.</p>
        <div class="soporte-chips">
            <span class="soporte-chip">Estado de cuenta</span>
            <span class="soporte-chip">Abonos</span>
            <span class="soporte-chip">Crédito</span>
        </div>
    </section>

    <!-- Productos -->
    <section class="tarjeta soporte-modulo">
        <div class="soporte-modulo-top">
            <div class="soporte-modulo-icon soporte-icon-ambar"><i class="fa-solid fa-box"></i></div>
            <div>
                <div class="soporte-modulo-titulo">Productos</div>
                <span class="soporte-modulo-ruta">/productos</span>
            </div>
        </div>
        <p class="soporte-modulo-desc">Da de alta tu mercadería con nombre, códigos de barras (unidad y empaque), costo, precio de venta y tipo de impuesto (exento, exonerado, ISV 15% o 18%). Configura presentaciones por unidad o caja, fotos, stock mínimo, control de existencias, combos y las categorías del menú.</p>
        <div class="soporte-chips">
            <span class="soporte-chip">Códigos de barras</span>
            <span class="soporte-chip">Presentaciones</span>
            <span class="soporte-chip">ISV</span>
            <span class="soporte-chip">Combos</span>
        </div>
    </section>

    <!-- Inventario -->
    <section class="tarjeta soporte-modulo">
        <div class="soporte-modulo-top">
            <div class="soporte-modulo-icon soporte-icon-rojo"><i class="fa-solid fa-boxes-stacked"></i></div>
            <div>
                <div class="soporte-modulo-titulo">Inventario</div>
                <span class="soporte-modulo-ruta">/inventario</span>
            </div>
        </div>
        <p class="soporte-modulo-desc">Consulta existencias en tiempo real, realiza ajustes (entradas, salidas o correcciones) y vigila los productos con stock mínimo para reabastecer a tiempo. Cada venta o compra actualiza el inventario automáticamente y un aviso destaca los productos por agotarse.</p>
        <div class="soporte-chips">
            <span class="soporte-chip">Ajustes</span>
            <span class="soporte-chip">Alertas de stock</span>
        </div>
    </section>

    <!-- Compras -->
    <section class="tarjeta soporte-modulo">
        <div class="soporte-modulo-top">
            <div class="soporte-modulo-icon soporte-icon-verde"><i class="fa-solid fa-truck-ramp-box"></i></div>
            <div>
                <div class="soporte-modulo-titulo">Compras</div>
                <span class="soporte-modulo-ruta">/compras</span>
            </div>
        </div>
        <p class="soporte-modulo-desc">Registra la entrada de mercadería seleccionando productos, cantidades y costos, con su proveedor y factura. El stock se actualiza al guardar; soporta compras a crédito con días de vencimiento, pagos y abonos al proveedor, gastos operativos y anulación de compras equivocadas.</p>
        <div class="soporte-chips">
            <span class="soporte-chip">Entradas de stock</span>
            <span class="soporte-chip">Crédito a proveedor</span>
            <span class="soporte-chip">Gastos</span>
        </div>
    </section>

    <!-- Proveedores -->
    <section class="tarjeta soporte-modulo">
        <div class="soporte-modulo-top">
            <div class="soporte-modulo-icon soporte-icon-ambar"><i class="fa-solid fa-handshake"></i></div>
            <div>
                <div class="soporte-modulo-titulo">Proveedores</div>
                <span class="soporte-modulo-ruta">/proveedores</span>
            </div>
        </div>
        <p class="soporte-modulo-desc">Mantén el catálogo de tus abastecedores con sus datos de contacto y el saldo pendiente por compras a plazos, facilitando la gestión de los pagos y el historial de cada uno.</p>
        <div class="soporte-chips">
            <span class="soporte-chip">Saldos</span>
            <span class="soporte-chip">Historial</span>
        </div>
    </section>

    <!-- Reportes -->
    <section class="tarjeta soporte-modulo">
        <div class="soporte-modulo-top">
            <div class="soporte-modulo-icon soporte-icon-violeta"><i class="fa-solid fa-chart-line"></i></div>
            <div>
                <div class="soporte-modulo-titulo">Reportes</div>
                <span class="soporte-modulo-ruta">/reportes</span>
            </div>
        </div>
        <p class="soporte-modulo-desc">Analiza la marcha del negocio: ventas, ganancias, productos más vendidos y desglose por método de pago dentro de un rango de fechas. Los resultados se imprimen o se exportan a Excel (CSV).</p>
        <div class="soporte-chips">
            <span class="soporte-chip">Ganancias</span>
            <span class="soporte-chip">Rango de fechas</span>
            <span class="soporte-chip">Exportar CSV</span>
        </div>
    </section>

    <!-- Usuarios -->
    <section class="tarjeta soporte-modulo">
        <div class="soporte-modulo-top">
            <div class="soporte-modulo-icon soporte-icon-rojo"><i class="fa-solid fa-user-gear"></i></div>
            <div>
                <div class="soporte-modulo-titulo">Usuarios</div>
                <span class="soporte-modulo-ruta">/usuarios</span>
            </div>
        </div>
        <p class="soporte-modulo-desc">Crea los perfiles que usan el sistema: administradores con acceso total y cajeros (deben abrir su turno de caja antes de vender), cada uno con su sucursal y caja asignada.</p>
        <div class="soporte-chips">
            <span class="soporte-chip">Roles</span>
            <span class="soporte-chip">Caja asignada</span>
            <span class="soporte-chip">Sucursal</span>
        </div>
    </section>

</div>

<!-- ============ OPERACIÓN DIARIA ============ -->
<h2 class="soporte-seccion"><i class="fa-solid fa-clipboard-check" aria-hidden="true"></i> Operación diaria</h2>
<div class="soporte-guia-grid">

    <!-- Corte de Caja -->
    <section class="tarjeta soporte-modulo">
        <div class="soporte-modulo-top">
            <div class="soporte-modulo-icon soporte-icon-violeta"><i class="fa-solid fa-calculator"></i></div>
            <div>
                <div class="soporte-modulo-titulo">Corte de Caja</div>
                <span class="soporte-modulo-ruta">/caja</span>
            </div>
        </div>
        <p class="soporte-modulo-desc">Abre tu turno indicando el fondo inicial (paso obligatorio para los cajeros) y registra ingresos y egresos durante la jornada. Al cerrar, el sistema genera el corte con el resumen de ventas por método de pago y el arqueo de caja, listo para imprimir o revisar.</p>
        <div class="soporte-chips">
            <span class="soporte-chip">Turnos</span>
            <span class="soporte-chip">Arqueo</span>
            <span class="soporte-chip">Impresión</span>
        </div>
    </section>

    <!-- Reimpresión -->
    <section class="tarjeta soporte-modulo">
        <div class="soporte-modulo-top">
            <div class="soporte-modulo-icon"><i class="fa-solid fa-receipt"></i></div>
            <div>
                <div class="soporte-modulo-titulo">Reimpresión de Comprobantes</div>
                <span class="soporte-modulo-ruta">/comprobantes</span>
            </div>
        </div>
        <p class="soporte-modulo-desc">Ubica cualquier venta por folio, cliente o fecha y vuelve a imprimir su comprobante —por la impresora de red (LAN) o en la ventana del navegador— cuando un recibo se pierde o el cliente lo solicita de nuevo.</p>
        <div class="soporte-chips">
            <span class="soporte-chip">Búsqueda por folio</span>
            <span class="soporte-chip">Impresión LAN</span>
        </div>
    </section>

    <!-- Configuración -->
    <section class="tarjeta soporte-modulo">
        <div class="soporte-modulo-top">
            <div class="soporte-modulo-icon soporte-icon-neutro"><i class="fa-solid fa-gear"></i></div>
            <div>
                <div class="soporte-modulo-titulo">Configuración</div>
                <span class="soporte-modulo-ruta">/configuracion</span>
            </div>
        </div>
        <p class="soporte-modulo-desc">Datos del negocio (nombre, RTN, teléfono, dirección y logotipo), formato del comprobante (ancho 58/80 mm, fuente, tamaño, mensaje), ISV y moneda. Incluye la facturación fiscal SAR (C.A.I., rangos autorizados y correlativo) y la impresora de red, con escaneo automático de la LAN y prueba de impresión.</p>
        <div class="soporte-chips">
            <span class="soporte-chip">Ticket 58/80mm</span>
            <span class="soporte-chip">SAR / CAI</span>
            <span class="soporte-chip">Impresora LAN</span>
        </div>
    </section>

</div>

<!-- Contacto de soporte directo -->
<section class="soporte-whatsapp">
    <div class="soporte-whatsapp-inner">
        <div class="soporte-whatsapp-icono">
            <i class="fa-brands fa-whatsapp"></i>
        </div>
        <div class="soporte-whatsapp-texto">
            <h2>¿Necesitas ayuda con el sistema?</h2>
            <p>Escríbenos y con gusto te atiende <strong>Carlos Martínez</strong>: dudas, configuraciones, impresora de red o asesoría en cualquier módulo.</p>
            <div class="soporte-whatsapp-numero"><i class="fa-solid fa-phone"></i> +504 9552-4118 (Solo WhatsApp)</div>
        </div>
        <a class="soporte-whatsapp-btn" href="https://wa.me/50495524118?text=Hola%2C%20te%20escribo%20por%20el%20sistema%20Web%20PDV%2C%20necesito%20ayuda." target="_blank" rel="noopener">
            <i class="fa-brands fa-whatsapp"></i>
            Escribir por WhatsApp
        </a>
    </div>
</section>

<?php require APP_PATH . 'Views/layouts/footer.php'; ?>