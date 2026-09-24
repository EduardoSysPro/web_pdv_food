<?php

require_once CORE_PATH . 'Controller.php';
require_once APP_PATH . 'Models' . DIRECTORY_SEPARATOR . 'Cotizacion.php';
require_once APP_PATH . 'Models' . DIRECTORY_SEPARATOR . 'Configuracion.php';

class CocinaController extends Controller
{
    private $modeloCotizacion;
    private $modeloConfiguracion;

    public function __construct()
    {
        parent::__construct();
        $this->modeloCotizacion = new Cotizacion();
        $this->modeloConfiguracion = new Configuracion();
    }

    public function index()
    {
        $this->requerirAutenticacion();

        $comandas = $this->modeloCotizacion->obtenerComandasCocina();
        foreach ($comandas as &$comanda) {
            $comanda['detalles'] = $this->modeloCotizacion->obtenerDetalles((int)$comanda['id']);
        }
        unset($comanda);

        $configuracion = $this->modeloConfiguracion->obtenerMapa();
        $refrescoSeg = max(5, (int)($configuracion['cocina_refresco_seg'] ?? 15));

        $this->vista('cocina/index', [
            'comandas'    => $comandas,
            'refresco_seg' => $refrescoSeg
        ]);
    }

    public function cambiarEstadoComanda($parametros)
    {
        $this->requerirAutenticacion();
        header('Content-Type: application/json; charset=utf-8');

        $id = (int)($parametros['id'] ?? 0);
        $estado = isset($_POST['estado']) ? trim($_POST['estado']) : '';

        if ($id <= 0 || $estado === '') {
            echo json_encode(['exito' => false, 'mensaje' => 'Datos inválidos.']);
            return;
        }

        if ($this->modeloCotizacion->cambiarEstadoComanda($id, $estado)) {
            echo json_encode(['exito' => true, 'mensaje' => 'Comanda actualizada.']);
        } else {
            echo json_encode(['exito' => false, 'mensaje' => 'No se pudo actualizar la comanda.']);
        }
    }

    public function cambiarEstadoItem($parametros)
    {
        $this->requerirAutenticacion();
        header('Content-Type: application/json; charset=utf-8');

        $id = (int)($parametros['id'] ?? 0);
        $estado = isset($_POST['estado']) ? trim($_POST['estado']) : '';

        if ($id <= 0 || $estado === '') {
            echo json_encode(['exito' => false, 'mensaje' => 'Datos inválidos.']);
            return;
        }

        if ($this->modeloCotizacion->cambiarEstadoItem($id, $estado)) {
            echo json_encode(['exito' => true, 'mensaje' => 'Artículo actualizado.']);
        } else {
            echo json_encode(['exito' => false, 'mensaje' => 'No se pudo actualizar el artículo.']);
        }
    }
}