#!/bin/sh
set -e

echo "==> Instalando dependencias de Composer..."
composer install --no-interaction --prefer-dist --optimize-autoloader

echo "==> Verificando APP_KEY..."
php artisan key:generate --no-interaction 2>/dev/null || true

echo "==> Limpiando cache de configuracion..."
php artisan config:clear

echo "==> Corriendo migraciones..."
php artisan migrate --force

echo "==> Iniciando PHP-FPM..."
exec php-fpm