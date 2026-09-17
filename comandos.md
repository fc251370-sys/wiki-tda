migraciones 

docker exec plantilla_php php artisan migrate   (migracion primaria)

docker exec plantilla_php php artisan make:migration create_carreras_table  (nueva migracion)

docker exec plantilla_php php artisan make:migration create_users_table --create=users (creacion con tabla predefinida)

comit2ejje

docker exec plantilla_php php artisan make:migration create_wiki_categories_table

docker exec plantilla_php php artisan make:migration create_wiki_tags_table

docker exec plantilla_php php artisan make:migration create_wiki_articles_table

docker exec plantilla_php php artisan make:migration create_article_tag_table

docker exec plantilla_php php artisan make:migration create_events_table


docker exec plantilla_php php artisan make:migration create_event_subjects_table


docker exec plantilla_php php artisan make:migration create_projects_table


docker exec plantilla_php php artisan make:migration create_project_members_table


docker exec plantilla_php php artisan make:migration create_project_files_table