migraciones 

docker exec plantillawiki_php artisan migrate   (migracion primaria)

docker exec plantillawiki_php artisan make:migration create_carreras_table  (nueva migracion)

docker exec plantillawiki_php artisan make:migration create_users_table --create=users (creacion con tabla predefinida)

comit2ejje

docker exec plantillawiki_php artisan make:migration create_wiki_categories_table

docker exec plantillawiki_php artisan make:migration create_wiki_tags_table

docker exec plantillawiki_php artisan make:migration create_wiki_articles_table

docker exec plantillawiki_php artisan make:migration create_article_tag_table

docker exec plantillawiki_php artisan make:migration create_events_table


docker exec plantillawiki_php artisan make:migration create_event_subjects_table


docker exec plantillawiki_php artisan make:migration create_projects_table


docker exec plantillawiki_php artisan make:migration create_project_members_table


docker exec plantillawiki_php artisan make:migration create_project_files_table

------------------------------------------------------------------------------------------------
crear controladoress 

docker exec plantillawiki_php php artisan make:controller Api/Carrera/CarreraController --api
docker exec plantillawiki_php php artisan make:controller Api/Usuario/UserController --api
docker exec plantillawiki_php php artisan make:controller Api/Wiki/WikiCategoryController --api
docker exec plantillawiki_php php artisan make:controller Api/Wiki/WikiTagController --api
docker exec plantillawiki_php php artisan make:controller Api/Wiki/WikiArticleController --api
docker exec plantillawiki_php php artisan make:controller Api/Evento/EventController --api
docker exec plantillawiki_php php artisan make:controller Api/Evento/EventSubjectController --api
docker exec plantillawiki_php php artisan make:controller Api/Proyecto/ProjectController --api
docker exec plantillawiki_php php artisan make:controller Api/Proyecto/ProjectFileController --api
----------------------------------------------------------------------------------------------------

docker exec plantillawiki_php php artisan migrate:fresh --seed
-------------------------------------------------------------------------------------------------------

