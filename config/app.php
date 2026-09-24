<?php
// Detecta si es HTTP o HTTPS
$protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";

// Detecta la IP o Host desde donde se hace la petición (ej. 192.168.1.15 o localhost)
$host = $_SERVER['HTTP_HOST'] ?? 'localhost'; 

// Define la URL Base de forma totalmente dinámica
// Deriva la carpeta desde la que se sirve public/index.php (ej. /web_pdv_food/public/)
$directorioBase = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
define('URL_BASE', "{$protocolo}://{$host}" . rtrim($directorioBase, '/') . '/');
date_default_timezone_set('America/Tegucigalpa');

// En producción con certificado SSL configurado, cambia a true para forzar HTTPS
define('FORZAR_HTTPS', false);

define('MONEDA_SIMBOLO',  'L ');
define('MONEDA_ISO',      'HNL');
define('ISV_PORCENTAJE',  15);

if (!function_exists('formatearMoneda')) {
    function formatearMoneda($monto)
    {
        $montoLimpio = is_numeric($monto) ? (float)$monto : 0.0;
        return MONEDA_SIMBOLO . number_format($montoLimpio, 2, '.', ',');
    }
}
