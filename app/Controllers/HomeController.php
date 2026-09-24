<?php

require_once CORE_PATH . 'Controller.php';

class HomeController extends Controller
{
    public function index()
    {
        if ($this->estaAutenticado()) {
            $rol = $_SESSION['rol'] ?? 'cajero';
            if (in_array($rol, ['vendedor', 'cajero_movil'], true)) {
                $rol = 'mesero';
            }
            if ($rol === 'cocina') {
                $this->redirigir('cocina');
            } else {
                $this->redirigir('ventas');
            }
        } else {
            $this->redirigir('login');
        }
    }
}
