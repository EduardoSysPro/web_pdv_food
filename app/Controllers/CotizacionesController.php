<?php

require_once CORE_PATH . 'Controller.php';
require_once APP_PATH . 'Models' . DIRECTORY_SEPARATOR . 'Cotizacion.php';
require_once APP_PATH . 'Models' . DIRECTORY_SEPARATOR . 'Producto.php';
require_once APP_PATH . 'Models' . DIRECTORY_SEPARATOR . 'Configuracion.php';

class CotizacionesController extends Controller
{
    private $modeloCotizacion;
    private $modeloProducto;
    private $modeloConfiguracion;

    public function __construct()
    {
        parent::__construct();
        $this->modeloCotizacion = new Cotizacion();
        $this->modeloProducto = new Producto();
        $this->modeloConfiguracion = new Configuracion();
    }

    // ------------------------------------------------------------------
    // Comandas para cocina (generación y reimpresión)
    // ------------------------------------------------------------------

    

    /**
     * Genera la comanda de la venta recién registrada y la imprime en cocina.
     * Idempotente: si la venta ya tiene una comanda, devuelve la existente
     * (evita duplicados).
     */
    public function enviarCocina()
    {
        $this->requerirAutenticacion();
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['exito' => false, 'mensaje' => 'Método HTTP no permitido.']);
            return;
        }

        $datos = $this->normalizarDatos($this->leerPeticion());
        $ventaId = (int)($datos['venta_id'] ?? 0);

        try {
            $this->asegurarEstadoComandaEnum();
            $pdo = Database::getInstancia()->getConexion();

            if ($ventaId > 0) {
                $stmtEx = $pdo->prepare("SELECT id, folio FROM cotizaciones WHERE tipo = 'comanda' AND venta_id = :vid AND estado <> 'cancelada' ORDER BY id DESC LIMIT 1");
                $stmtEx->execute([':vid' => $ventaId]);
                $existente = $stmtEx->fetch(PDO::FETCH_ASSOC);
                if ($existente) {
                    echo json_encode([
                        'exito'      => true,
                        'mensaje'    => 'La comanda de esta venta ya existe (' . $existente['folio'] . ').',
                        'folio'      => $existente['folio'],
                        'cotizacion_id' => (int)$existente['id'],
                        'url_imprimir'  => URL_BASE . 'cotizaciones/imprimir-comanda/' . (int)$existente['id']
                    ]);
                    return;
                }
            }

            $procesado = $this->procesarItems($datos['productos']);

            $pdo->beginTransaction();
            $folio = $this->modeloCotizacion->generarFolio('COMA-');
            $cotizacionId = $this->modeloCotizacion->insertarEncabezado([
                'folio'               => $folio,
                'vendedor_id'         => (int)($_SESSION['id'] ?? 0),
                'cliente_id'          => $datos['cliente_id'],
                'cliente_nombre'      => $datos['cliente_nombre'],
                'cliente_rtn'         => $datos['cliente_rtn'],
                'cliente_telefono'    => $datos['cliente_telefono'],
                'cliente_direccion'   => $datos['cliente_direccion'],
                'estado'              => 'no_confirmada',
                'tipo'                => 'comanda',
                'importe_exento'      => $procesado['importeExento'],
                'importe_exonerado'   => $procesado['importeExonerado'],
                'importe_gravado_15'  => $procesado['gravado15'],
                'isv_15'              => $procesado['isv15'],
                'importe_gravado_18'  => $procesado['gravado18'],
                'isv_18'              => $procesado['isv18'],
                'subtotal'            => $procesado['total'],
                'descuento_total'     => $procesado['descuentoTotal'],
                'total'               => $procesado['total'],
                'observaciones'       => $datos['observaciones'],
                'fecha_validez'       => $datos['fecha_validez'],
                'venta_id'            => $ventaId > 0 ? $ventaId : null
            ]);
            $this->modeloCotizacion->reemplazarDetalle($cotizacionId, $procesado['items']);
            $pdo->commit();

            echo json_encode([
                'exito'      => true,
                'mensaje'    => 'Comanda ' . $folio . ' generada.',
                'folio'      => $folio,
                'cotizacion_id' => $cotizacionId,
                'url_imprimir'  => URL_BASE . 'cotizaciones/imprimir-comanda/' . $cotizacionId
            ]);
        } catch (Throwable $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo json_encode(['exito' => false, 'mensaje' => 'Error al generar la comanda: ' . $e->getMessage()]);
        }
    }

    /**
     * Impresión de comanda para cocina (sin precios, letra grande).
     */
    public function imprimirComanda($parametros)
    {
        $this->requerirAutenticacion();
        $id = (int)($parametros['id'] ?? 0);
        $cotizacion = $this->modeloCotizacion->obtenerPorId($id);
        if (!$cotizacion || ($cotizacion['tipo'] ?? '') !== 'comanda') {
            http_response_code(404);
            echo 'Comanda no encontrada.';
            return;
        }
        $detalles = $this->modeloCotizacion->obtenerDetalles($id);
        $configuracion = $this->modeloConfiguracion->obtenerMapa();
        $configuracion = array_merge([
            'empresa_nombre' => $configuracion['nombre_negocio'] ?? 'MI NEGOCIO',
            'empresa_rtn' => $configuracion['rtn'] ?? '',
            'empresa_direccion' => $configuracion['direccion'] ?? '',
            'empresa_telefono' => $configuracion['telefono'] ?? '',
            'empresa_email' => $configuracion['email'] ?? '',
        ], $configuracion);

        require APP_PATH . 'Views' . DIRECTORY_SEPARATOR . 'cotizaciones' . DIRECTORY_SEPARATOR . 'imprimir_comanda.php';
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function leerPeticion()
    {
        $contenido = file_get_contents('php://input');
        $datos = json_decode((string)$contenido, true);
        if (is_array($datos)) {
            return $datos;
        }
        return $_POST;
    }

    private function normalizarDatos($datos)
    {
        $tipoImpuestoDefault = ($this->modeloConfiguracion->obtenerMapa()['tipo_comprobante_default'] ?? 'recibo');
        return [
            'venta_id'          => isset($datos['venta_id']) ? (int)$datos['venta_id'] : 0,
            'cliente_id'        => isset($datos['cliente_id']) ? (int)$datos['cliente_id'] : 0,
            'cliente_nombre'    => trim((string)($datos['cliente_nombre'] ?? '')),
            'cliente_rtn'       => trim((string)($datos['cliente_rtn'] ?? '')),
            'cliente_telefono'  => trim((string)($datos['cliente_telefono'] ?? '')),
            'cliente_direccion' => trim((string)($datos['cliente_direccion'] ?? '')),
            'observaciones'     => trim((string)($datos['observaciones'] ?? '')),
            'fecha_validez'     => trim((string)($datos['fecha_validez'] ?? '')),
            'productos'         => isset($datos['productos']) && is_array($datos['productos']) ? $datos['productos'] : []
        ];
    }

    /**
     * Procesa y valida cada artículo, calcula el desglose de ISV y los totales.
     * NO descuenta inventario: la comanda solo reserva la intención de venta.
     */
    private function procesarItems($productos)
    {
        if (!is_array($productos) || count($productos) === 0) {
            throw new RuntimeException('No hay productos en la orden.');
        }

        $pdo = Database::getInstancia()->getConexion();
        $items = [];
        $importeExento = 0.0;
        $importeExonerado = 0.0;
        $gravado15 = 0.0;
        $isv15 = 0.0;
        $gravado18 = 0.0;
        $isv18 = 0.0;
        $descuentoTotal = 0.0;
        $total = 0.0;

        $hasImpuesto = $this->columnaExiste('productos', 'tipo_impuesto');

        // Carga la info de TODOS los productos del carrito en una sola consulta.
        // Con carritos grandes esto evita ejecutar N consultas (una por artículo).
        $infoProductos = [];
        $idsProductos = [];
        foreach ($productos as $item) {
            $idProducto = isset($item['id']) ? (int)$item['id'] : 0;
            if ($idProducto > 0) {
                $idsProductos[$idProducto] = $idProducto;
            }
        }
        if ($idsProductos) {
            $idsTexto = implode(',', array_map('intval', $idsProductos));
            $stmtInfo = $pdo->query('SELECT id, nombre, nombre_empaque, unidades_por_empaque' . ($hasImpuesto ? ', tipo_impuesto, porcentaje_isv' : '') . ' FROM productos WHERE id IN (' . $idsTexto . ')');
            foreach ($stmtInfo->fetchAll(PDO::FETCH_ASSOC) as $fila) {
                $infoProductos[(int)$fila['id']] = $fila;
            }
        }

        foreach ($productos as $item) {
            $productoId      = isset($item['id']) ? (int)$item['id'] : 0;
            $cantidad        = isset($item['cantidad']) ? (float)$item['cantidad'] : 0.0;
            $precioFinal     = isset($item['precio_unitario']) ? round((float)$item['precio_unitario'], 2) : 0.0;
            $precioLista     = array_key_exists('precio_lista', $item) ? round((float)$item['precio_lista'], 2) : $precioFinal;
            $descuentoUnit   = array_key_exists('descuento_unitario', $item)
                ? round((float)$item['descuento_unitario'], 2)
                : round($precioLista - $precioFinal, 2);

            if ($productoId <= 0 || $cantidad <= 0) {
                throw new RuntimeException('Artículo inválido en el detalle de la orden.');
            }
            if ($precioLista < 0 || $precioFinal < 0 || $descuentoUnit < 0 || $descuentoUnit > $precioLista) {
                throw new RuntimeException('El precio o descuento del artículo no es válido.');
            }
            if (abs(round($precioLista - $descuentoUnit - $precioFinal, 2)) > 0.01) {
                throw new RuntimeException('El precio final y el descuento del artículo no coinciden.');
            }

            $subtotal = round($cantidad * $precioFinal, 2);
            $descuentoLinea = round($cantidad * $descuentoUnit, 2);
            $tipoPresentacion = (($item['tipo_presentacion'] ?? 'unidad') === 'empaque') ? 'empaque' : 'unidad';
            $nombrePres = trim((string)($item['nombre_presentacion'] ?? ($tipoPresentacion === 'empaque' ? 'Caja' : 'Unidad')));
            $factorUnidades = max(1.0, (float)($item['factor_unidades'] ?? 1.0));

            $tipoImpuesto = 'gravado_15';
            $porcentajeIsv = 15.0;
            $nombreProducto = 'Producto ' . $productoId;
            $prodInfo = $infoProductos[$productoId] ?? null;
            if ($prodInfo) {
                $nombreProducto = $prodInfo['nombre'];
                if ($tipoPresentacion === 'empaque') {
                    if ($factorUnidades <= 1.0 && (float)$prodInfo['unidades_por_empaque'] > 1.0) {
                        $factorUnidades = (float)$prodInfo['unidades_por_empaque'];
                    }
                    if (empty($nombrePres) || $nombrePres === 'Unidad' || $nombrePres === 'Caja') {
                        $nombrePres = !empty($prodInfo['nombre_empaque']) ? $prodInfo['nombre_empaque'] : 'Caja';
                    }
                }
                if ($hasImpuesto && !empty($prodInfo['tipo_impuesto'])) {
                    $tipoImpuesto = $prodInfo['tipo_impuesto'];
                    $porcentajeIsv = (float)$prodInfo['porcentaje_isv'];
                }
            }

            $esExento = $tipoImpuesto === 'exento' ? 1 : 0;
            $esExonerado = $tipoImpuesto === 'exonerado' ? 1 : 0;
            $montoIsv = $porcentajeIsv > 0 ? round($subtotal - ($subtotal / (1 + $porcentajeIsv / 100)), 2) : 0.0;
            $baseGravada = $subtotal - $montoIsv;

            if ($esExento) {
                $importeExento += $subtotal;
            } elseif ($esExonerado) {
                $importeExonerado += $subtotal;
            } elseif ($porcentajeIsv >= 18) {
                $gravado18 += $baseGravada;
                $isv18 += $montoIsv;
            } else {
                $gravado15 += $baseGravada;
                $isv15 += $montoIsv;
            }

            $total += $subtotal;
            $descuentoTotal += $descuentoLinea;

            $items[] = [
                'producto_id'         => $productoId,
                'nombre_producto'     => $nombreProducto,
                'cantidad'            => $cantidad,
                'precio_lista'        => $precioLista,
                'precio_unitario'     => $precioFinal,
                'descuento_unitario'  => $descuentoUnit,
                'subtotal'            => $subtotal,
                'tipo_presentacion'   => $tipoPresentacion,
                'nombre_presentacion' => $nombrePres,
                'factor_unidades'     => $factorUnidades,
                'porcentaje_isv'      => $porcentajeIsv,
                'monto_isv'           => $montoIsv,
                'es_exento'           => $esExento,
                'es_exonerado'        => $esExonerado,
                'estado_item'         => 'pendiente',
                'nota'                => trim((string)($item['nota'] ?? ''))
            ];
        }

        $total = round($total, 2);
        if ($total <= 0) {
            throw new RuntimeException('El total de la orden no es válido.');
        }

        // Los combos se despliegan en la comanda como cabecera + sus componentes
        // con precio 0, para que la cocina vea qué preparar sin alterar totales.
        $items = $this->modeloProducto->expandirItemsConCombos($items);

        return [
            'items'             => $items,
            'importeExento'     => round($importeExento, 2),
            'importeExonerado'  => round($importeExonerado, 2),
            'gravado15'         => round($gravado15, 2),
            'isv15'             => round($isv15, 2),
            'gravado18'         => round($gravado18, 2),
            'isv18'             => round($isv18, 2),
            'descuentoTotal'    => round($descuentoTotal, 2),
            'total'             => $total
        ];
    }

    private function columnaExiste($tabla, $columna)
    {
        $tabla = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$tabla);
        $columna = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$columna);
        if ($tabla === '' || $columna === '') return false;
        $stmt = Database::getInstancia()->getConexion()->query("SHOW COLUMNS FROM `{$tabla}` LIKE '{$columna}'");
        return $stmt !== false && $stmt->rowCount() > 0;
    }

    /**
     * Asegura que el ENUM de cotizaciones.estado acepte el valor 'no_confirmada'
     * (comanda generada tras el cobro pero aún no enviada a cocina). La columna
     * nace con un ENUM sin ese valor; este ALTER idempotente amplía el ENUM y
     * conserva todos los valores existentes.
     */
    private function asegurarEstadoComandaEnum()
    {
        try {
            $pdo = Database::getInstancia()->getConexion();
            $stmt = $pdo->query("SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cotizaciones' AND COLUMN_NAME = 'estado'");
            $tipo = $stmt->fetchColumn();
            if ($tipo !== false && strpos((string)$tipo, 'no_confirmada') !== false) {
                return;
            }
            $pdo->exec("ALTER TABLE cotizaciones
                MODIFY COLUMN estado ENUM('no_confirmada', 'pendiente', 'en_cocina', 'listo', 'servida', 'facturada', 'cancelada')
                NOT NULL DEFAULT 'pendiente'");
        } catch (Throwable $e) {
            // Si por permisos no se puede alterar, el insert/confirmación dará un mensaje claro.
        }
    }
}