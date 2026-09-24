<?php

require_once CORE_PATH . 'Controller.php';
require_once APP_PATH . 'Models' . DIRECTORY_SEPARATOR . 'Producto.php';
require_once APP_PATH . 'Models' . DIRECTORY_SEPARATOR . 'Categoria.php';

class ProductosController extends Controller
{
    private $modeloProducto;
    private $modeloCategoria;

    public function __construct()
    {
        parent::__construct();
        $this->modeloProducto = new Producto();
        $this->modeloCategoria = new Categoria();
    }

    public function index()
    {
        $this->requerirAdministrador();
        $busqueda = trim($_GET['busqueda'] ?? '');
        $categoriaId = $_GET['categoria_id'] ?? null;
        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $resultado = $this->modeloProducto->obtenerTodos($busqueda, $categoriaId, $pagina);
        $categorias = $this->modeloCategoria->obtenerTodas();
        $mensaje = $_SESSION['mensaje_productos'] ?? null;
        unset($_SESSION['mensaje_productos']);
        require APP_PATH . 'Views/productos/index.php';
    }

    public function exportarCsv()
    {
        $this->requerirAdministrador();
        $busqueda = trim($_GET['busqueda'] ?? '');
        $categoriaId = $_GET['categoria_id'] ?? null;
        $productos = $this->modeloProducto->obtenerParaExportar($busqueda, $categoriaId);

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="productos_etiquetas_' . date('Ymd_His') . '.csv"');

        $salida = fopen('php://output', 'w');
        fwrite($salida, "\xEF\xBB\xBF"); // BOM para que Excel detecte UTF-8
        fputcsv($salida, ['codigo_barras', 'descripcion', 'precio_venta']);
        foreach ($productos as $producto) {
            fputcsv($salida, [
                $producto['codigo_barras'],
                $producto['nombre'],
                number_format((float)$producto['precio_venta'], 2, '.', ''),
            ]);
        }
        fclose($salida);
        exit;
    }

    public function crear()
    {
        $this->requerirAdministrador();
        $producto = $this->datosVacios();
        unset($_SESSION['datos_producto']);
        $categorias = $this->modeloCategoria->obtenerTodas();
        $todosProductos = $this->modeloProducto->obtenerTodosSimples();
        $componentes = $producto['componentes'] ?? [];
        $errores = $_SESSION['errores_productos'] ?? [];
        unset($_SESSION['errores_productos']);
        require APP_PATH . 'Views/productos/crear.php';
    }

    public function guardar()
    {
        $this->requerirAdministrador();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('productos/crear');
        }
        $datos = $this->leerDatos();
        if (trim((string)($datos['codigo_barras'] ?? '')) === '') {
            $datos['codigo_barras'] = $this->generarCodigoInterno();
        }
        $errores = $this->validarDatos($datos);
        $imagenProcesada = $this->procesarImagenProducto('');
        if ($imagenProcesada['error'] !== '') {
            $errores[] = $imagenProcesada['error'];
        }
        $datos['imagen'] = $imagenProcesada['nombre'];
        if (!$errores && $this->modeloProducto->codigoBarrasExiste($datos['codigo_barras'])) {
            $errores[] = 'El código de barras ya está registrado.';
        }
        if (!$errores && !empty($datos['codigo_barras_empaque']) && $this->modeloProducto->codigoEmpaqueExiste($datos['codigo_barras_empaque'])) {
            $errores[] = 'El código de barras del empaque ya está registrado por otro producto.';
        }
        if ($errores) {
            $_SESSION['errores_productos'] = $errores;
            $_SESSION['datos_producto'] = $datos;
            $this->redirigir('productos/crear');
        }
        $idNuevo = $this->modeloProducto->insertar($datos);
        if ($idNuevo > 0 && !empty($datos['es_combo'])) {
            $this->modeloProducto->guardarComponentesCombo($idNuevo, $datos['componentes'] ?? []);
        }
        $_SESSION['mensaje_productos'] = 'Producto creado correctamente.';
        $this->redirigir('productos');
    }

    public function editar($parametros)
    {
        $this->requerirAdministrador();
        $id = (int)($parametros['id'] ?? 0);
        $producto = $this->modeloProducto->obtenerPorId($id);
        if (!$producto) {
            $_SESSION['mensaje_productos'] = 'El producto no existe.';
            $this->redirigir('productos');
        }
        if (!empty($_SESSION['datos_producto'])) {
            $producto = array_merge($producto, $_SESSION['datos_producto']);
            unset($_SESSION['datos_producto']);
        }
        $categorias = $this->modeloCategoria->obtenerTodas();
        $todosProductos = $this->modeloProducto->obtenerTodosSimples();
        $componentes = $producto['componentes'] ?? $this->modeloProducto->obtenerComponentesCombo($id);
        $errores = $_SESSION['errores_productos'] ?? [];
        unset($_SESSION['errores_productos']);
        require APP_PATH . 'Views/productos/editar.php';
    }

    public function actualizar($parametros)
    {
        $this->requerirAdministrador();
        $id = (int)($parametros['id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('productos/editar/' . $id);
        }
        $datos = $this->leerDatos();
        if (trim((string)($datos['codigo_barras'] ?? '')) === '') {
            $datos['codigo_barras'] = $this->generarCodigoInterno();
        }
        $errores = $this->validarDatos($datos);
        $producto = $this->modeloProducto->obtenerPorId($id);
        $imagenProcesada = $this->procesarImagenProducto($producto ? ($producto['imagen'] ?? '') : '');
        if ($imagenProcesada['error'] !== '') {
            $errores[] = $imagenProcesada['error'];
        }
        $datos['imagen'] = $imagenProcesada['nombre'];
        if (!$errores && $this->modeloProducto->codigoBarrasExiste($datos['codigo_barras'], $id)) {
            $errores[] = 'El código de barras ya está registrado por otro producto.';
        }
        if (!$errores && !empty($datos['codigo_barras_empaque']) && $this->modeloProducto->codigoEmpaqueExiste($datos['codigo_barras_empaque'], $id)) {
            $errores[] = 'El código de barras del empaque ya está registrado por otro producto.';
        }
        if ($errores) {
            $_SESSION['errores_productos'] = $errores;
            $_SESSION['datos_producto'] = $datos;
            $this->redirigir('productos/editar/' . $id);
        }
        try {
            if (!$this->modeloProducto->actualizar($id, $datos)) {
                throw new RuntimeException('No se pudo actualizar el producto.');
            }
            if (!empty($datos['es_combo'])) {
                $this->modeloProducto->guardarComponentesCombo($id, $datos['componentes'] ?? []);
            } else {
                $this->modeloProducto->guardarComponentesCombo($id, []);
            }
        } catch (Throwable $e) {
            $_SESSION['errores_productos'] = ['No se pudo guardar el producto. Verifica la conexión con la base de datos.'];
            $_SESSION['datos_producto'] = $datos;
            $this->redirigir('productos/editar/' . $id);
        }
        $_SESSION['mensaje_productos'] = 'Producto actualizado correctamente.';
        $this->redirigir('productos');
    }

    public function eliminar($parametros)
    {
        $this->requerirAdministrador();
        $id = (int)($parametros['id'] ?? 0);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
            $producto = $this->modeloProducto->obtenerPorId($id);
            $this->modeloProducto->eliminar($id);
            if ($producto && !empty($producto['imagen'])) {
                $this->eliminarArchivoImagen((string)$producto['imagen']);
            }
            $_SESSION['mensaje_productos'] = 'Producto eliminado correctamente.';
        }
        $this->redirigir('productos');
    }

    private function procesarImagenProducto($nombreActual)
    {
        $resultado = ['nombre' => (string)$nombreActual, 'error' => ''];
        $quitar = isset($_POST['quitar_imagen']);
        $subio = !empty($_FILES['imagen']['name']);

        if (!$subio && !$quitar) {
            return $resultado;
        }

        if ($quitar && !$subio) {
            $this->eliminarArchivoImagen((string)$nombreActual);
            $resultado['nombre'] = '';
            return $resultado;
        }

        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            $resultado['error'] = 'No se pudo cargar la imagen del producto.';
            return $resultado;
        }

        $tmp = $_FILES['imagen']['tmp_name'];
        $info = @getimagesize($tmp);
        if ($info === false) {
            $resultado['error'] = 'El archivo seleccionado no es una imagen válida.';
            return $resultado;
        }

        $mimePermitidos = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];
        $mime = $info['mime'] ?? '';
        if (!isset($mimePermitidos[$mime])) {
            $resultado['error'] = 'Solo se permiten imágenes en formato JPG, PNG o WebP.';
            return $resultado;
        }
        if ($_FILES['imagen']['size'] > 2 * 1024 * 1024) {
            $resultado['error'] = 'La imagen no puede superar los 2 MB.';
            return $resultado;
        }

        $directorio = PUBLIC_PATH . 'uploads' . DIRECTORY_SEPARATOR . 'productos' . DIRECTORY_SEPARATOR;
        if (!is_dir($directorio) && !mkdir($directorio, 0755, true) && !is_dir($directorio)) {
            $resultado['error'] = 'No se pudo crear el directorio de imágenes de productos.';
            return $resultado;
        }

        $nombre = 'prod_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $mimePermitidos[$mime];
        if (!move_uploaded_file($tmp, $directorio . $nombre)) {
            $resultado['error'] = 'No se pudo guardar la imagen del producto.';
            return $resultado;
        }

        if ((string)$nombreActual !== '' && $nombreActual !== $nombre) {
            $this->eliminarArchivoImagen((string)$nombreActual);
        }

        $resultado['nombre'] = $nombre;
        return $resultado;
    }

    private function eliminarArchivoImagen($nombre)
    {
        if ($nombre === '') {
            return;
        }
        $ruta = PUBLIC_PATH . 'uploads' . DIRECTORY_SEPARATOR . 'productos' . DIRECTORY_SEPARATOR . $nombre;
        if (is_file($ruta)) {
            @unlink($ruta);
        }
    }

    private function datosVacios()
    {
        return $_SESSION['datos_producto'] ?? [
            'codigo_barras' => '', 'nombre' => '', 'precio_costo' => '0.00',
            'precio_venta' => '0.00', 'stock' => 0, 'stock_minimo' => 1,
            'unidad_medida' => 'unidad', 'permite_decimales' => 0, 'categoria_id' => null,
            'controlar_stock' => 1,
            'es_combo' => 0,
            'componentes' => [],
            'tipo_venta' => 'solo_unidad', 'nombre_empaque' => 'Caja',
            'unidades_por_empaque' => 2, 'precio_empaque' => '0.00', 'codigo_barras_empaque' => '',
            'tipo_impuesto' => 'gravado_15', 'imagen' => ''
        ];
    }

    private function leerDatos()
    {
        $categoria = trim($_POST['categoria_id'] ?? '');
        $controlarStock = isset($_POST['controlar_stock']) ? 1 : 0;
        $esCombo = isset($_POST['es_combo']) ? 1 : 0;
        if ($esCombo) {
            // Un combo nunca controla inventario: lo controlan sus componentes.
            $controlarStock = 0;
        }

        // Venta siempre en unidades enteras: la opción de peso/granel se eliminó.
        $unidad = 'unidad';
        $permiteDecimales = 0;

        // Venta solo por unidad: la opción de empaque (Caja/Bulto/Fardo) se eliminó.
        $tipoVenta = 'solo_unidad';
        $nombreEmpaque = 'Caja';
        $unidadesPorEmpaque = 1.0;
        $precioEmpaque = 0.0;
        $codigoBarrasEmpaque = '';

        $tipoImpuesto = trim((string)($_POST['tipo_impuesto'] ?? 'gravado_15'));
        if (!in_array($tipoImpuesto, ['exento', 'gravado_15', 'gravado_18', 'exonerado'], true)) {
            $tipoImpuesto = 'gravado_15';
        }

        // Componentes enviados desde el editor de combos.
        $componentes = [];
        if ($esCombo) {
            $idsComp = (array)($_POST['componente_id'] ?? []);
            $cantsComp = (array)($_POST['componente_cantidad'] ?? []);
            foreach ($idsComp as $indice => $pidRaw) {
                $pid = (int)$pidRaw;
                $cant = (float)($cantsComp[$indice] ?? 0);
                if ($pid > 0 && $cant > 0) {
                    $componentes[] = ['producto_id' => $pid, 'cantidad' => $cant];
                }
            }
        }

        // Los productos "preparados al momento" no controlan inventario:
        // sin stock, sin stock mínimo y sin venta por empaque mayorista.
        $stock = $controlarStock ? max(0, (float)($_POST['stock'] ?? 0)) : 0;
        $stockMinimo = $controlarStock ? max(0, (float)($_POST['stock_minimo'] ?? 0)) : 0;

        return [
            'codigo_barras' => trim($_POST['codigo_barras'] ?? ''),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'precio_costo' => (float)($_POST['precio_costo'] ?? 0),
            'precio_venta' => (float)($_POST['precio_venta'] ?? 0),
            'stock' => $stock,
            'stock_minimo' => $stockMinimo,
            'unidad_medida' => 'unidad',
            'permite_decimales' => 0,
            'categoria_id' => $categoria === '' ? null : (int)$categoria,
            'controlar_stock' => $controlarStock,
            'es_combo' => $esCombo,
            'componentes' => $componentes,
            'tipo_venta' => $tipoVenta,
            'nombre_empaque' => $nombreEmpaque,
            'unidades_por_empaque' => $unidadesPorEmpaque,
            'precio_empaque' => $precioEmpaque,
            'codigo_barras_empaque' => $codigoBarrasEmpaque !== '' ? $codigoBarrasEmpaque : null,
            'tipo_impuesto' => $tipoImpuesto,
            'imagen' => ''
        ];
    }

    private function validarDatos($datos)
    {
        $errores = [];
        if (trim((string)($datos['codigo_barras'] ?? '')) === '') {
            $errores[] = 'El código de barras es obligatorio.';
        }
        if ($datos['nombre'] === '') $errores[] = 'El nombre del producto es obligatorio.';
        if ($datos['precio_costo'] < 0 || $datos['precio_venta'] < 0) $errores[] = 'Los precios no pueden ser negativos.';
        if ((float)$datos['stock'] < 0 || (float)$datos['stock_minimo'] < 0) $errores[] = 'El stock no puede ser negativo.';

        if ($datos['tipo_venta'] !== 'solo_unidad') {
            if ($datos['unidades_por_empaque'] <= 1) {
                $errores[] = 'La cantidad de unidades contenidas en el empaque (' . htmlspecialchars($datos['nombre_empaque']) . ') debe ser mayor a 1.';
            }
            if ($datos['precio_empaque'] <= 0) {
                $errores[] = 'Debes definir un precio de venta para el empaque (' . htmlspecialchars($datos['nombre_empaque']) . ').';
            }
            if (!empty($datos['codigo_barras_empaque']) && $datos['codigo_barras_empaque'] === $datos['codigo_barras']) {
                $errores[] = 'El código de barras del empaque no puede ser igual al de la unidad.';
            }
        }

        // Un combo debe tener al menos un componente y no puede incluir otros combos.
        if (!empty($datos['es_combo'])) {
            $comps = [];
            foreach ((array)($datos['componentes'] ?? []) as $comp) {
                if ((int)($comp['producto_id'] ?? 0) > 0 && (float)($comp['cantidad'] ?? 0) > 0) {
                    $comps[] = $comp;
                }
            }
            if (!$comps) {
                $errores[] = 'El combo debe incluir al menos un producto como componente.';
            } else {
                $esComboMap = [];
                foreach ($this->modeloProducto->obtenerTodosSimples() as $p) {
                    if (!empty($p['es_combo'])) {
                        $esComboMap[(int)$p['id']] = true;
                    }
                }
                foreach ($comps as $comp) {
                    if (isset($esComboMap[(int)$comp['producto_id']])) {
                        $errores[] = 'Los componentes de un combo no pueden ser otros combos.';
                        break;
                    }
                }
            }
        }
        return $errores;
    }

    private function generarCodigoInterno()
    {
        $prefijo = '20';
        $aleatorio = str_pad((string)mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
        $codigo = $prefijo . $aleatorio;
        if (strlen($codigo) > 13) {
            $codigo = substr($codigo, 0, 13);
        }
        if (!preg_match('/^\d{12,13}$/', $codigo)) {
            return $this->generarCodigoInterno();
        }
        return $codigo;
    }
}
