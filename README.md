# La Brújula

Catálogo y gestión de pedidos para librería, construido con Laravel 12, SQLite y un frontend responsive.

## Incluye

- Catálogo público con búsqueda y carrito.
- Pedidos con datos de contacto.
- Panel administrativo en `/admin`.
- Productos, stock, costos y fotos.
- Factor `monedas`: `precio final = costo × monedas`.
- Importación del archivo `LA BRUJULA SRL lista de precios.xlsx`.
- Imagenes y base de datos persistentes en Docker.

## Despliegue en EasyPanel

1. Crear una aplicación desde GitHub usando `https://github.com/tecnoexit/libreria.git`.
2. Seleccionar **Dockerfile** como método de build.
3. Configurar el puerto interno `8080`.
4. Crear un volumen persistente montado en `/var/www/html/database`.
5. Crear otro volumen persistente montado en `/var/www/html/storage/app/public`.
6. Agregar estas variables en **Environment**:

```dotenv
APP_NAME=La Brujula
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com
APP_KEY=generar-una-clave-laravel-segura
ADMIN_USER=admin
ADMIN_PASSWORD=una-clave-larga-y-segura
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public
LOG_CHANNEL=stderr
```

7. Generar `APP_KEY` antes del primer despliegue con `php artisan key:generate --show`, o usar una clave Laravel válida generada localmente.
8. Activar el dominio y HTTPS desde EasyPanel.

El contenedor ejecuta automáticamente migraciones, crea el enlace de almacenamiento, optimiza Laravel e importa los productos si la base está vacía.

## Desarrollo local

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan products:import
php artisan serve
```

En Linux/Debian usar `cp .env.example .env` en lugar de `copy`.

## Comandos útiles

```bash
php artisan products:import
php artisan migrate --force
php artisan test
```

El panel administrativo utiliza autenticación HTTP Basic cuando existe `ADMIN_PASSWORD`. No publicar el sitio en producción sin configurar esa variable.
