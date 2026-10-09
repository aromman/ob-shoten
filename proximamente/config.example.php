<?php

/*
 * Configuración del formulario de "Próximamente".
 *
 * NO va en este archivo. Copiarlo como proximamente.php a una carpeta
 * shoten-config/ ubicada un nivel ARRIBA del directorio donde vive suscribir.php:
 *
 *   Servidor (cPanel):  /home/<usuario>/shoten-config/proximamente.php
 *                       (suscribir.php está en /home/<usuario>/public_html/)
 *   Local (WinNMP):     C:\WinNMP\WWW\shoten\ob-shoten\shoten-config\proximamente.php
 *
 * También se puede indicar otra ruta con la variable de entorno SHOTEN_CONFIG.
 */

return array(
    'db' => array(
        'host' => 'localhost',
        'name' => 'usuario_shoten',
        'user' => 'usuario_shoten',
        'pass' => 'CAMBIAR',
    ),

    // Si se completa, envía un aviso a esta dirección por cada nuevo suscriptor.
    'notify_email' => '',
    'notify_from' => 'no-responder@shoten.com.ar',

    // Altas permitidas por IP por hora.
    'max_per_ip_hour' => 5,
);
