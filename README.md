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
