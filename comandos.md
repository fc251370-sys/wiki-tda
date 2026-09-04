migraciones 

docker exec plantilla_php php artisan migrate   (migracion primaria)

docker exec plantilla_php php artisan make:migration create_carreras_table  (nueva migracion)

docker exec plantilla_php php artisan make:migration create_users_table --create=users (creacion con tabla predefinida)