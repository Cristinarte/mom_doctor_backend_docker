#!/bin/bash
# Liberar el puerto 80 si está ocupado
fuser -k 80/tcp || true
# Iniciar PHP-FPM en segundo plano
php-fpm -D
# Iniciar Nginx en primer plano
nginx -g "daemon off;"