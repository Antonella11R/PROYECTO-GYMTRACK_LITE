# GymTrack Lite

GymTrack Lite es una aplicacion web desarrollada en PHP para gestionar el flujo basico de un gimnasio o seguimiento deportivo: autenticacion de usuarios, administracion de atletas y entrenadores, catalogo de ejercicios, registro de sesiones de entrenamiento y visualizacion de progreso.

El proyecto fue estructurado siguiendo principios de DDD y arquitectura limpia, separando la logica de negocio, los casos de uso, la persistencia y la capa web. No depende de un framework completo como Laravel o Symfony; en su lugar implementa una capa propia de bootstrap, router, middleware, controladores, vistas y servicios de aplicacion.

## Tabla de contenido

- [Objetivo del repositorio](#objetivo-del-repositorio)
- [Funcionalidades principales](#funcionalidades-principales)
- [Funcionalidades por rol](#funcionalidades-por-rol)
- [Arquitectura](#arquitectura)
- [Estructura del proyecto](#estructura-del-proyecto)
- [Modelo de datos](#modelo-de-datos)
- [Requisitos](#requisitos)
- [Instalacion y ejecucion con XAMPP](#instalacion-y-ejecucion-con-xampp)
- [Configuracion del entorno](#configuracion-del-entorno)
- [Datos de demo](#datos-de-demo)
- [Pruebas](#pruebas)
- [Rutas principales](#rutas-principales)

## Objetivo del repositorio

Este repositorio sirve como base para una aplicacion web de gestion deportiva enfocada en un alcance reducido pero funcional. La idea principal es cubrir un flujo realista con un codigo facil de mantener y de extender:

- un administrador crea y mantiene usuarios y ejercicios
- un entrenador trabaja con atletas asignados
- un usuario registra o consulta sus entrenamientos
- el sistema genera vistas de progreso a partir del historial almacenado

Ademas del caso funcional, el repositorio tambien muestra una organizacion de codigo mas disciplinada que una aplicacion PHP monolitica tradicional mezclada con consultas SQL en las vistas.

## Funcionalidades principales

- Inicio de sesion con correo y contrasena.
- Manejo de sesion de usuario con `$_SESSION`.
- Proteccion CSRF para formularios sensibles.
- Control de acceso con middleware de autenticacion, invitado y roles.
- Dashboard segun el rol autenticado.
- Gestion de usuarios por parte del administrador.
- Asignacion de atletas a entrenadores.
- Gestion del catalogo de ejercicios.
- Registro de sesiones de entrenamiento con multiples items por sesion.
- Consulta de historial de entrenamientos.
- Reporte de progreso por atleta y ejercicio.
- Datos de ejemplo para arrancar rapidamente.
- Suite de pruebas unitarias e integracion.

## Funcionalidades por rol

### Administrador

- Accede al dashboard administrativo.
- Puede listar usuarios.
- Puede crear usuarios nuevos.
- Puede editar usuarios existentes.
- Puede desactivar usuarios.
- Puede listar ejercicios.
- Puede crear, editar y desactivar ejercicios.
- Puede consultar informacion global por medio del dashboard.

### Entrenador

- Accede a su dashboard con metricas propias.
- Consulta el listado de atletas asignados.
- Registra entrenamientos para atletas bajo su cargo.
- Consulta historial y reportes de progreso de sus atletas.
- No puede acceder a rutas administrativas.

### Usuario

- Accede a su dashboard personal.
- Consulta su propio historial.
- Registra sus entrenamientos.
- Consulta su reporte de progreso.
- No puede acceder a datos de otros usuarios.

## Arquitectura

La aplicacion esta organizada por contextos funcionales y por capas. La carpeta `src/` contiene cinco modulos principales:

- `Identity`: usuarios, roles, autenticacion y asignacion de entrenadores.
- `Catalog`: ejercicios y su administracion.
- `Training`: sesiones de entrenamiento y su detalle.
- `Reporting`: dashboards, metricas y dataset para progreso.
- `Shared`: componentes transversales del sistema.

Cada modulo sigue una separacion por capas:

- `Domain`: entidades, value objects y contratos de repositorio.
- `Application`: servicios de aplicacion y casos de uso.
- `Infrastructure`: implementaciones concretas, principalmente persistencia con PDO.
- `Presentation/Web`: controladores HTTP y vistas PHP server-rendered.

### Flujo de arranque de la aplicacion

1. Apache sirve `index.php` desde la raiz del proyecto.
2. Ese archivo delega en `public/index.php`.
3. `public/bootstrap.php` carga `vendor/autoload.php`.
4. `AppFactory` inicializa configuracion, sesion, PDO, servicios, middleware, controladores y rutas.
5. La peticion se encapsula en un objeto `Request`.
6. El `Router` busca la ruta y ejecuta la cadena de middleware.
7. El controlador llama a servicios de aplicacion.
8. Los servicios usan repositorios para persistencia o lectura.
9. Se devuelve un objeto `Response` al navegador.

### Componentes tecnicos destacados

- Router propio con soporte para parametros en rutas.
- Middleware de autenticacion, invitado y roles.
- Generacion centralizada de URLs.
- Renderizado de vistas con layout compartido.
- Hasheo de contrasenas con bcrypt.
- Carga de configuracion desde `.env` con `vlucas/phpdotenv`.
- Persistencia basada en PDO y consultas SQL manuales.
- Read model especifico para reportes.

## Estructura del proyecto

```text
.
|-- .env.example
|-- composer.json
|-- composer.phar
|-- index.php
|-- public/
|   |-- .htaccess
|   |-- bootstrap.php
|   `-- index.php
|-- script/
|   |-- migrations/
|   |   `-- 001_initial_schema.sql
|   |-- seeds/
|   |   `-- 001_demo_seed.sql
|   `-- reset_database.php
|-- src/
|   |-- Catalog/
|   |-- Identity/
|   |-- Reporting/
|   |-- Shared/
|   `-- Training/
|-- test/
|   |-- Integration/
|   `-- Unit/
`-- vendor/
```

## Modelo de datos

La base principal usa cuatro tablas actuales:

- `users`
- `exercises`
- `training_sessions`
- `training_session_items`

### Relaciones principales

- Un usuario puede tener rol `admin`, `trainer` o `user`.
- Un usuario con rol `user` puede estar asignado a un entrenador mediante `trainer_id`.
- Una sesion de entrenamiento pertenece a un atleta (`athlete_user_id`).
- Una sesion tambien guarda quien la registro (`recorded_by_user_id`).
- Cada sesion tiene uno o varios items asociados.
- Cada item de sesion referencia un ejercicio del catalogo.

### Scripts incluidos

- `script/migrations/001_initial_schema.sql`: crea la base y el esquema inicial.
- `script/seeds/001_demo_seed.sql`: carga usuarios, ejercicios y sesiones demo.
- `script/reset_database.php`: reconstruye toda la base usando la configuracion actual del `.env`.

## Requisitos

Para ejecutar el proyecto localmente tienes dos opciones principales.

**Opción 1: Usando Docker (Recomendado)**
- Docker Desktop (o Docker Engine y Docker Compose instalados)
- PowerShell (En Windows)

**Opción 2: Usando entorno local**
- PHP 8.2 o superior
- extensiones `pdo` y `pdo_mysql`
- MySQL o MariaDB
- Apache
- Composer, o alternativamente el `composer.phar` incluido
- XAMPP en Windows si quieres usar el flujo manual

## Instalación y ejecución con Docker (Recomendado)

La forma más sencilla de compilar y levantar el proyecto completo es mediante Docker, ya que preconfigura automáticamente PHP, Apache, MySQL, las dependencias y la base de datos con un solo comando.

### 1. Clonar el repositorio
Ubica el repositorio en una carpeta de tu preferencia y abre **PowerShell** en esa ubicación.

### 2. Ejecutar el script de inicio
Ejecuta el script proporcionado para compilar y levantar todos los servicios automáticamente:
```powershell
.\start.ps1
```

Este script se encarga de:
- Configurar tu archivo `.env` automáticamente.
- Construir (compilar) la imagen de Docker para la aplicación.
- Levantar los contenedores de la aplicación y base de datos.
- Instalar dependencias mediante Composer.
- Ejecutar migraciones y datos de prueba (seeds) de la base de datos.

Una vez que termine, la aplicación estará disponible en [http://localhost:8000](http://localhost:8000).

## Instalacion y ejecucion con XAMPP

Las instrucciones de esta seccion estan pensadas para un entorno Windows con XAMPP.

### 1. Clonar o copiar el repositorio

Ubica el repositorio en una carpeta de trabajo. Por ejemplo:

```powershell
C:\GIT\UniversidadGuayaquil\PROYECTO-GYMTRACK_LITE
```

### 2. Exponer el proyecto en `htdocs`

La opcion recomendada es apuntar `C:\xampp\htdocs\gymtrack-lite` a la raiz del repo. Puedes hacerlo copiando la carpeta o usando una junction:

```powershell
New-Item -ItemType Junction `
  -Path "C:\xampp\htdocs\gymtrack-lite" `
  -Target "C:\GIT\UniversidadGuayaquil\PROYECTO-GYMTRACK_LITE"
```

Este repositorio ya incluye:

- un `index.php` en la raiz
- reglas `.htaccess`
- el entry point original en `public/index.php`

Con eso Apache puede servir la app directamente desde la carpeta del proyecto sin necesidad de reconfigurar un virtual host.

### 3. Configurar variables de entorno

Usa `.env.example` como base para crear tu archivo `.env`.

Ejemplo:

```env
APP_ENV=local
APP_NAME="GymTrack Lite"
APP_URL=http://localhost
SESSION_NAME=gymtrack_lite_session

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gymtrack_lite
DB_USERNAME=root
DB_PASSWORD=
```

Notas:

- `APP_URL` puede mantenerse como `http://localhost`.
- La aplicacion detecta la subruta en tiempo de ejecucion cuando se sirve como `http://localhost/gymtrack-lite`.
- Si cambias el nombre del directorio publicado, no necesitas hardcodear esa ruta en el `.env`.

### 4. Instalar dependencias

Si necesitas reinstalar o actualizar dependencias:

```powershell
C:\xampp\php\php.exe .\composer.phar install
```

### 5. Iniciar Apache y MySQL

Desde el panel de XAMPP inicia:

- Apache
- MySQL

### 6. Crear la base de datos de trabajo

El proyecto incluye un script para reconstruir la base desde cero:

```powershell
C:\xampp\php\php.exe .\script\reset_database.php
```

Ese comando:

- lee la configuracion del `.env`
- conecta al servidor MySQL
- elimina la base definida en `DB_DATABASE` si ya existe
- vuelve a crear el esquema
- inserta datos semilla de demostracion

### 7. Abrir la aplicacion

Con Apache y MySQL activos:

```text
http://localhost/gymtrack-lite
```

La pantalla inicial esperada es el formulario de login.

## Configuracion del entorno

### Variables disponibles

| Variable       | Descripcion                                            |
| -------------- | ------------------------------------------------------ |
| `APP_ENV`      | Entorno de ejecucion, por ejemplo `local` o `testing`. |
| `APP_NAME`     | Nombre mostrado en la interfaz.                        |
| `APP_URL`      | URL base de referencia de la aplicacion.               |
| `SESSION_NAME` | Nombre de la cookie de sesion.                         |
| `DB_HOST`      | Host de MySQL.                                         |
| `DB_PORT`      | Puerto de MySQL.                                       |
| `DB_DATABASE`  | Nombre de la base de datos.                            |
| `DB_USERNAME`  | Usuario de la base.                                    |
| `DB_PASSWORD`  | Contrasena de la base.                                 |

### Observaciones sobre `APP_URL`

Aunque existe `APP_URL`, la clase de configuracion tambien puede resolver el base path usando `$_SERVER['SCRIPT_NAME']`. Esto ayuda a que el proyecto funcione tanto en raiz como en subcarpetas de Apache sin obligarte a reescribir rutas internas.

## Datos de demo

Despues de ejecutar `script/reset_database.php`, el sistema queda con cuentas listas para probar.

### Credenciales

- Administrador: `admin@gymtrack.test`
- Entrenador: `trainer@gymtrack.test`
- Usuario: `user@gymtrack.test`
- Clave para todas las cuentas: `Demo123!`

### Datos precargados

- usuarios de ejemplo para cada rol
- ejercicios base del catalogo
- varias sesiones de entrenamiento historicas
- detalle de ejercicios dentro de esas sesiones

## Pruebas

La suite de pruebas usa PHPUnit y esta separada en pruebas unitarias y de integracion.

### Ejecutar pruebas con el PHP de XAMPP

```powershell
C:\xampp\php\php.exe .\vendor\phpunit\phpunit\phpunit --configuration phpunit.xml.dist
```

### Ejecutar pruebas con Composer

Si `php` esta disponible en tu `PATH`:

```powershell
php .\composer.phar test
```

### Organizacion

- `test/Unit`: validacion de entidades, reglas y servicios aislados.
- `test/Integration`: validacion de flujos HTTP, autenticacion, autorizacion, administracion y entrenamiento.

### Casos cubiertos actualmente

- redireccion de invitados a login
- login y logout
- restriccion de rutas administrativas para entrenadores
- creacion de usuarios y ejercicios por administrador
- registro de entrenamientos para atletas asignados
- acceso al reporte de progreso para el usuario autenticado

## Rutas principales

La configuracion de rutas se centraliza en `AppFactory`.

### Autenticacion

- `GET /login`
- `POST /login`
- `POST /logout`

### Dashboard

- `GET /dashboard`

### Administracion de usuarios

- `GET /admin/users`
- `GET /admin/users/create`
- `POST /admin/users`
- `GET /admin/users/{id}/edit`
- `POST /admin/users/{id}`
- `POST /admin/users/{id}/deactivate`

### Administracion de ejercicios

- `GET /admin/exercises`
- `GET /admin/exercises/create`
- `POST /admin/exercises`
- `GET /admin/exercises/{id}/edit`
- `POST /admin/exercises/{id}`
- `POST /admin/exercises/{id}/deactivate`

### Entrenamientos y atletas

- `GET /trainer/athletes`
- `GET /trainings`
- `GET /trainings/create`
- `POST /trainings`

### Reportes

- `GET /reports/progress`

## Decisiones tecnicas relevantes

### 1. Sin framework full-stack

Se opto por una base liviana en PHP para mantener control total sobre el flujo HTTP y la estructura del codigo. Eso obliga a implementar varias piezas manualmente, pero tambien deja mas visible la separacion de responsabilidades.

### 2. Persistencia por repositorios

Los servicios de aplicacion no dependen directamente de SQL embebido. En su lugar consumen contratos de repositorio, mientras que la implementacion concreta vive en infraestructura con PDO.

### 3. Read model para reportes

La lectura de metricas y progreso esta desacoplada del modelo de escritura, lo que simplifica consultas orientadas a dashboard y reportes.

### 4. Restricciones por rol en la capa de aplicacion

Aunque las rutas usan middleware, varias validaciones importantes tambien viven en servicios. Por ejemplo:

- un usuario no puede consultar entrenamientos de otro usuario
- un entrenador solo puede operar sobre atletas asignados
- un ejercicio inactivo no puede usarse en una nueva sesion

### 5. Front controller compatible con XAMPP

La raiz del repositorio contiene un `index.php` que reenvia a `public/index.php`. Esto evita depender de configuraciones especiales del servidor para apuntar el document root directamente a `public/`.

## Resumen

GymTrack Lite es un proyecto PHP orientado a mostrar una base funcional, clara y extendible para gestion de entrenamiento. Combina una interfaz web sencilla con una estructura interna ordenada, roles bien definidos, persistencia relacional y pruebas automatizadas suficientes para cubrir el flujo principal del sistema.
