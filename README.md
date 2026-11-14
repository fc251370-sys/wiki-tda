# Plantillas Repos - Monorepositorio

Este proyecto es un monorepositorio gestionado con **pnpm workspaces** y **Turborepo**, diseñado para alojar múltiples aplicaciones y paquetes compartidos. Actualmente contiene una aplicación frontend en Vue.js y un backend en Laravel, todo orquestado con **Docker Compose** para facilitar el desarrollo local.

## 🚀 Tecnologías Principales

- **Gestor de paquetes:** [pnpm](https://pnpm.io/)
- **Gestor de monorepositorio:** [Turborepo](https://turbo.build/)
- **Frontend:** Vue.js + Vite (`apps/plantilla-front`)
- **Backend:** Laravel 11 (`apps/plantilla`)
- **Infraestructura local:** Docker & Docker Compose (Nginx, PHP-FPM, PostgreSQL, MongoDB, Redis)

## 📁 Estructura del Proyecto

```text
plantillas-repos/
├── apps/
│   ├── plantilla/          # Backend en Laravel (API)
│   └── plantilla-front/    # Frontend en Vue.js + Vite
├── docker-compose.yml      # Orquestación de contenedores para desarrollo local
├── package.json            # Dependencias globales y scripts de Turborepo
├── pnpm-workspace.yaml     # Configuración de los workspaces de pnpm
├── turbo.json              # Configuración de tareas de Turborepo
└── .gitignore              # Archivos ignorados por git
```

## 🛠️ Requisitos Previos

Para ejecutar este proyecto en tu entorno local, necesitas tener instalado:

- [Node.js](https://nodejs.org/) (v18 o superior)
- [pnpm](https://pnpm.io/installation) (v8 o superior)
- [Docker](https://www.docker.com/) y [Docker Compose](https://docs.docker.com/compose/)
- [Git](https://git-scm.com/)

## 🚦 Primeros Pasos

### 1. Clonar el repositorio e instalar dependencias

```bash
git clone <url-del-repositorio>
cd plantillas-repos

# Instalar dependencias en todos los workspaces
pnpm install
```

### 2. Configurar variables de entorno

Asegúrate de que los archivos `.env` estén configurados correctamente en cada aplicación.

**Backend (`apps/plantilla/.env`):**
```env
APP_URL=http://localhost:8006
FRONTEND_URL=http://localhost:3005

DB_CONNECTION=pgsql
DB_HOST=plantilla_db
DB_PORT=5432
DB_DATABASE=plantilla
DB_USERNAME=postgres
DB_PASSWORD=irwin
```

**Frontend (`apps/plantillawiki-front/.env`):**
```env
VITE_API_URL=/api
API_PROXY_TARGET=http://plantillawiki_nginx
```

### 3. Levantar la infraestructura con Docker

El proyecto utiliza Docker Compose para levantar todos los servicios necesarios (Nginx, PHP, PostgreSQL, MongoDB, Redis y el Frontend).

```bash
# Levantar los contenedores en segundo plano
docker compose up -d
```

### 4. Ejecutar migraciones de la base de datos

Una vez que los contenedores estén corriendo, ejecuta las migraciones o los seeders de Laravel dentro del contenedor de PHP:

```bash
docker exec plantillawiki_php php artisan migrate
docker exec plantillawiki_php php artisan db:seed
```

## 🌐 Acceso a las Aplicaciones

Una vez que los contenedores estén en ejecución, puedes acceder a las aplicaciones en las siguientes URLs:

- **Frontend (Vue.js):** [http://localhost:3005](http://localhost:3005)
- **Backend (Laravel API):** [http://localhost:8006](http://localhost:8006)

## 🏃‍♂️ Comandos Útiles (Turborepo)

Turborepo está configurado para ejecutar tareas en paralelo a través de los diferentes workspaces. Puedes ejecutar estos comandos desde la raíz del proyecto:

- `pnpm build`: Construye todas las aplicaciones para producción.
- `pnpm dev`: Inicia los servidores de desarrollo (si no usas Docker para el frontend).
- `pnpm lint`: Ejecuta el linter en todos los workspaces.

## 🐳 Servicios de Docker

El archivo `docker-compose.yml` define los siguientes servicios:

- `frontend`: Contenedor Node.js sirviendo la aplicación Vue (Puerto 3005).
- `nginx`: Servidor web para Laravel (Puerto 8006).
- `php`: Contenedor PHP-FPM 8.4 para ejecutar el código de Laravel.
- `pgsql`: Base de datos PostgreSQL 17 (Puerto expuesto 5437).
- `mongodb`: Base de datos MongoDB 4.4 (Puerto expuesto 27018).
- `redis`: Servidor Redis para caché y colas (Puerto expuesto 6379).

## 📝 Notas Adicionales

- **CORS y Autenticación:** El backend está configurado para aceptar peticiones desde `http://localhost:3005` y soporta el envío de credenciales (cookies) para la autenticación con Laravel Sanctum.
- **Gestión de dependencias:** Al usar pnpm workspaces, las dependencias compartidas se instalan en la raíz, ahorrando espacio en disco y tiempo de instalación. Para añadir una dependencia a un workspace específico, usa: `pnpm add <paquete> --filter <nombre-del-workspace>`.

```
wiki-tda
├─ .turbo
│  └─ cache
│     ├─ 7dd9dc7e6b787613-manifest.json
│     ├─ 7dd9dc7e6b787613-meta.json
│     ├─ 7dd9dc7e6b787613.tar.zst
│     ├─ dbafd04265a02ba3-manifest.json
│     ├─ dbafd04265a02ba3-meta.json
│     ├─ dbafd04265a02ba3.tar.zst
│     ├─ f3fba2f474075029-manifest.json
│     ├─ f3fba2f474075029-meta.json
│     └─ f3fba2f474075029.tar.zst
├─ README.md
├─ apps
│  ├─ plantillawiki
│  │  ├─ .editorconfig
│  │  ├─ .env
│  │  ├─ .env.example
│  │  ├─ .npmrc
│  │  ├─ AGENTS.md
│  │  ├─ Dockerfile
│  │  ├─ README.md
│  │  ├─ app
│  │  │  ├─ Http
│  │  │  │  └─ Controllers
│  │  │  │     ├─ Api
│  │  │  │     │  ├─ Carrera
│  │  │  │     │  │  └─ CarreraController.php
│  │  │  │     │  ├─ Evento
│  │  │  │     │  │  ├─ EventController.php
│  │  │  │     │  │  └─ EventSubjectController.php
│  │  │  │     │  ├─ Proyecto
│  │  │  │     │  │  ├─ ProjectController.php
│  │  │  │     │  │  └─ ProjectFileController.php
│  │  │  │     │  ├─ Usuario
│  │  │  │     │  │  └─ UserController.php
│  │  │  │     │  └─ Wiki
│  │  │  │     │     ├─ WikiArticleController.php
│  │  │  │     │     ├─ WikiCategoryController.php
│  │  │  │     │     └─ WikiTagController.php
│  │  │  │     ├─ Auth
│  │  │  │     │  ├─ LoginController.php
│  │  │  │     │  └─ RolesController.php
│  │  │  │     └─ Controller.php
│  │  │  ├─ Models
│  │  │  │  ├─ ArticleTag.php
│  │  │  │  ├─ Carrera.php
│  │  │  │  ├─ Event.php
│  │  │  │  ├─ EventSubject.php
│  │  │  │  ├─ Project.php
│  │  │  │  ├─ ProjectFile.php
│  │  │  │  ├─ ProjectMember.php
│  │  │  │  ├─ User.php
│  │  │  │  ├─ WikiArticle.php
│  │  │  │  ├─ WikiCategory.php
│  │  │  │  └─ WikiTag.php
│  │  │  ├─ Providers
│  │  │  │  └─ AppServiceProvider.php
│  │  │  └─ Response
│  │  │     └─ ApiResponse.php
│  │  ├─ artisan
│  │  ├─ boost.json
│  │  ├─ bootstrap
│  │  │  ├─ app.php
│  │  │  ├─ cache
│  │  │  │  ├─ packages.php
│  │  │  │  └─ services.php
│  │  │  └─ providers.php
│  │  ├─ composer.json
│  │  ├─ composer.lock
│  │  ├─ config
│  │  │  ├─ app.php
│  │  │  ├─ auth.php
│  │  │  ├─ cache.php
│  │  │  ├─ cors.php
│  │  │  ├─ database.php
│  │  │  ├─ filesystems.php
│  │  │  ├─ jwt.php
│  │  │  ├─ logging.php
│  │  │  ├─ mail.php
│  │  │  ├─ permission.php
│  │  │  ├─ queue.php
│  │  │  ├─ sanctum.php
│  │  │  ├─ scramble.php
│  │  │  ├─ services.php
│  │  │  └─ session.php
│  │  ├─ database
│  │  │  ├─ factories
│  │  │  │  └─ UserFactory.php
│  │  │  ├─ migrations
│  │  │  │  ├─ 0001_01_01_000000_a_create_carreras_table.php
│  │  │  │  ├─ 0001_01_01_000000_create_users_table.php
│  │  │  │  ├─ 0001_01_01_000001_create_cache_table.php
│  │  │  │  ├─ 0001_01_01_000002_create_jobs_table.php
│  │  │  │  ├─ 2026_07_20_145024_create_personal_access_tokens_table.php
│  │  │  │  ├─ 2026_07_20_145455_create_permission_tables.php
│  │  │  │  ├─ 2026_09_05_014938_create_wiki_categories_table.php
│  │  │  │  ├─ 2026_09_05_020110_create_wiki_tags_table.php
│  │  │  │  ├─ 2026_09_05_020607_create_wiki_articles_table.php
│  │  │  │  ├─ 2026_09_05_022420_create_article_tag_table.php
│  │  │  │  ├─ 2026_09_05_023818_create_events_table.php
│  │  │  │  ├─ 2026_09_05_024940_create_event_subjects_table.php
│  │  │  │  ├─ 2026_09_05_025959_create_projects_table.php
│  │  │  │  ├─ 2026_09_05_030936_create_project_members_table.php
│  │  │  │  └─ 2026_09_05_031739_create_project_files_table.php
│  │  │  └─ seeders
│  │  │     ├─ DatabaseSeeder.php
│  │  │     ├─ PermissionSeeder.php
│  │  │     ├─ RolSeeder.php
│  │  │     └─ UserSeeder.php
│  │  ├─ docker
│  │  │  ├─ nginx
│  │  │  │  └─ default.conf
│  │  │  └─ php
│  │  │     └─ entrypoint.sh
│  │  ├─ package-lock.json
│  │  ├─ package.json
│  │  ├─ phpunit.xml
│  │  ├─ public
│  │  │  ├─ .htaccess
│  │  │  ├─ favicon.ico
│  │  │  ├─ index.php
│  │  │  └─ robots.txt
│  │  ├─ resources
│  │  │  ├─ css
│  │  │  │  └─ app.css
│  │  │  ├─ js
│  │  │  │  └─ app.js
│  │  │  └─ views
│  │  │     ├─ prueva.blade.php
│  │  │     └─ welcome.blade.php
│  │  ├─ routes
│  │  │  ├─ api.php
│  │  │  ├─ console.php
│  │  │  └─ web.php
│  │  ├─ storage
│  │  │  ├─ app
│  │  │  │  ├─ private
│  │  │  │  └─ public
│  │  │  ├─ framework
│  │  │  │  ├─ cache
│  │  │  │  │  └─ data
│  │  │  │  ├─ sessions
│  │  │  │  ├─ testing
│  │  │  │  └─ views
│  │  │  │     ├─ 095dfb32a4c8e6ee6128f434f66ddc77.php
│  │  │  │     ├─ 09a58fcacf748a6562ed927c268fc96c.php
│  │  │  │     ├─ 111c8bd84c7c9e6c09e066c54ac92a66.php
│  │  │  │     ├─ 187f828346b000af3c7029fb05171792.php
│  │  │  │     ├─ 212da3f638d1f1195259b56e0af39eee.php
│  │  │  │     ├─ 21bc38208629a9f734816e4deb770d23.php
│  │  │  │     ├─ 2333078959a3098e22a1fd96031b9e05.php
│  │  │  │     ├─ 2b13b1b6581a0c5647c7b55227e47768.php
│  │  │  │     ├─ 335d34d09a5273ce82d86e931ec56e0e.php
│  │  │  │     ├─ 33788d033013eb30d1d969807a431d23.php
│  │  │  │     ├─ 3568f1f070a4ddb2ef87caa159b4b8a7.php
│  │  │  │     ├─ 37422ffb80460090c907197a73241c7b.php
│  │  │  │     ├─ 3c558def73ad31a849f219250515b5be.php
│  │  │  │     ├─ 42c4f7f7fc1219a0a7ddb45818a02ba7.php
│  │  │  │     ├─ 46f6c5084d5a5a5f15244fd8fa8eb16a.php
│  │  │  │     ├─ 4a27e0dbbcd06c81174dba832ab33c9f.php
│  │  │  │     ├─ 5df7b3a2fb7a31b0806578492acd4602.php
│  │  │  │     ├─ 712d9a3a538128e68ab3553093f35080.php
│  │  │  │     ├─ 76899a55b4cdd9b984592d1f168abea0.php
│  │  │  │     ├─ 774fd802c1c156552eec3da22fe85f10.php
│  │  │  │     ├─ 7777fcbb2ee4f07a3f03e03c2db6b1a7.php
│  │  │  │     ├─ 79d077736a92f72faf4623831507bedb.php
│  │  │  │     ├─ 7c0fa9d5752819705c76cde7627bb892.php
│  │  │  │     ├─ 7fff6527dbb84566804f417d3625f752.php
│  │  │  │     ├─ 81050c42d51539fe1e3ffa9a429455a9.php
│  │  │  │     ├─ 827b9671896bf6b11f2324b0b54d56d4.php
│  │  │  │     ├─ 84cdb55e30fa69ec79332eba1aa3c3fd.php
│  │  │  │     ├─ 8759b0131fe68d3d549de39f9d115034.php
│  │  │  │     ├─ 894576e06406b6b4d03f75f78870fa1c.php
│  │  │  │     ├─ 8b2c51aef984d684eb2d69a7148483c7.php
│  │  │  │     ├─ 97570e7f401c1518e896fac0a72db289.php
│  │  │  │     ├─ 9c14a7bc0279cd675bf161a9b41f6b11.php
│  │  │  │     ├─ a659332a6df8fcf9930b0e272656166e.php
│  │  │  │     ├─ a84c1ac64c9b1cb48cb31da938cf15ab.php
│  │  │  │     ├─ b47f25a1669de2c05599a357d082c632.php
│  │  │  │     ├─ cda2b2571d744abe89419057b79d7a0b.php
│  │  │  │     ├─ dbb84313ac11f5807c35103f5e3b5b9a.php
│  │  │  │     ├─ dbd34cf2a0e8f495192d8ec88435c848.php
│  │  │  │     ├─ dcef3a6360220751e751ff71764a26ba.php
│  │  │  │     ├─ e33b4dc5dcf5f0445d9e25c782b28c67.php
│  │  │  │     ├─ ed7a64bc810b2acb76c75bd48f67c0c9.php
│  │  │  │     ├─ f2ff37f225994e30074ec1d4e398aa1d.php
│  │  │  │     ├─ fa803f2e7cf41df20c209588f0a152ad.php
│  │  │  │     └─ fe368d42e17e9fc5cedc35d9663ea762.php
│  │  │  └─ logs
│  │  │     └─ laravel.log
│  │  ├─ tests
│  │  │  ├─ Feature
│  │  │  │  ├─ AuthRefreshTest.php
│  │  │  │  ├─ CarreraApiTest.php
│  │  │  │  └─ ExampleTest.php
│  │  │  ├─ Pest.php
│  │  │  ├─ TestCase.php
│  │  │  └─ Unit
│  │  │     └─ ExampleTest.php
│  │  └─ vite.config.js
│  └─ plantillawiki-front
│     ├─ .env
│     ├─ Dockerfile
│     ├─ PASOS_CONEXION_ACCIONES_CARRERAS.md
│     ├─ README.md
│     ├─ index.html
│     ├─ package-lock.json
│     ├─ package.json
│     ├─ public
│     │  ├─ favicon.svg
│     │  └─ icons.svg
│     ├─ src
│     │  ├─ App.vue
│     │  ├─ assets
│     │  │  ├─ hero.png
│     │  │  ├─ vite.svg
│     │  │  └─ vue.svg
│     │  ├─ components
│     │  │  ├─ AppBar.vue
│     │  │  ├─ AppShell.vue
│     │  │  ├─ DataTableComponent.vue
│     │  │  ├─ GlobalAlert.vue
│     │  │  ├─ GlobalLoader.vue
│     │  │  ├─ HelloWorld.vue
│     │  │  ├─ MenuLateral.vue
│     │  │  ├─ Pagination.vue
│     │  │  ├─ SidebarMenu.vue
│     │  │  ├─ ThemeToggle.vue
│     │  │  └─ TitleComponent.vue
│     │  ├─ directives
│     │  │  ├─ permission.js
│     │  │  └─ role.js
│     │  ├─ main.js
│     │  ├─ plugins
│     │  │  └─ vuetify.js
│     │  ├─ router
│     │  │  └─ router.js
│     │  ├─ services
│     │  │  ├─ auth
│     │  │  │  └─ login.service.js
│     │  │  ├─ carrera
│     │  │  │  └─ carrera.service.js
│     │  │  ├─ users
│     │  │  │  └─ users.service.js
│     │  │  └─ wiki
│     │  │     ├─ articulo.service.js
│     │  │     ├─ categoria.service.js
│     │  │     └─ tag.service.js
│     │  ├─ store
│     │  │  ├─ auth
│     │  │  │  └─ useLoginStore.js
│     │  │  ├─ carrera
│     │  │  │  └─ useCarrera.js
│     │  │  ├─ useAlertStore.js
│     │  │  ├─ useLoadingStore.js
│     │  │  ├─ users
│     │  │  │  └─ useuserStore.js
│     │  │  └─ wiki
│     │  │     ├─ useArticulo.js
│     │  │     ├─ useCategoria.js
│     │  │     └─ useTag.js
│     │  ├─ style.css
│     │  ├─ utils
│     │  │  ├─ access.js
│     │  │  ├─ authSession.js
│     │  │  ├─ axios.js
│     │  │  └─ menuDb.js
│     │  └─ view
│     │     ├─ AdminDashboard.vue
│     │     ├─ Home.vue
│     │     ├─ auth
│     │     │  ├─ Login
│     │     │  │  ├─ Login.vue
│     │     │  │  └─ composable.js
│     │     │  └─ router.js
│     │     ├─ carrera
│     │     │  ├─ composable
│     │     │  │  └─ carreraComposable.js
│     │     │  ├─ index.vue
│     │     │  └─ router.js
│     │     ├─ configuracion
│     │     │  ├─ index.vue
│     │     │  └─ router.js
│     │     ├─ evento
│     │     │  ├─ composable
│     │     │  ├─ index.vue
│     │     │  └─ router.js
│     │     ├─ layout
│     │     │  ├─ admin-layout.vue
│     │     │  └─ guest-layout.vue
│     │     ├─ proyecto
│     │     │  ├─ composable
│     │     │  ├─ index.vue
│     │     │  └─ router.js
│     │     ├─ users
│     │     │  ├─ composable
│     │     │  │  └─ userComposable.js
│     │     │  ├─ index.vue
│     │     │  └─ router.js
│     │     └─ wiki
│     │        ├─ articulo
│     │        │  ├─ composable
│     │        │  │  └─ articuloComposable.js
│     │        │  ├─ index.vue
│     │        │  └─ router.js
│     │        ├─ categoria
│     │        │  ├─ composable
│     │        │  │  └─ categoriaComposable.js
│     │        │  ├─ index.vue
│     │        │  └─ router.js
│     │        ├─ index.vue
│     │        ├─ router.js
│     │        └─ tag
│     │           ├─ composable
│     │           │  └─ tagComposable.js
│     │           ├─ index.vue
│     │           └─ router.js
│     └─ vite.config.js
├─ comandos.md
├─ docker-compose.yml
├─ package.json
├─ pnpm-lock.yaml
├─ pnpm-workspace.yaml
└─ turbo.json

```