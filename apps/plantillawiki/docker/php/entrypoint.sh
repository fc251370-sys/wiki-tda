#!/bin/sh
set -e

set_laravel_permissions() {
    mkdir -p storage/logs bootstrap/cache
    chown -R www-data:www-data storage bootstrap/cache
    find storage bootstrap/cache -type d -exec chmod 775 {} +
    find storage bootstrap/cache -type f -exec chmod 664 {} +
}

echo "==> Configurando permisos de Laravel..."
set_laravel_permissions

echo "==> Instalando dependencias de Composer..."
composer install --no-interaction --prefer-dist --optimize-autoloader

echo "==> Verificando APP_KEY..."
php artisan key:generate --no-interaction 2>/dev/null || true

echo "==> Limpiando cache de configuracion..."
php artisan config:clear

echo "==> Corriendo migraciones..."
php artisan migrate --force

echo "==> Reaplicando permisos de Laravel..."
set_laravel_permissions

echo "==> Iniciando PHP-FPM..."
exec php-fpm