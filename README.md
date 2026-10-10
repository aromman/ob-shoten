# ob-shoten

Sitio institucional (onboarding) de **Shoten**, el software de gestión para librerías.
Sitio estático (HTML + CSS + JS, sin build) más una página "Próximamente" con lista de interesados.

```
index.html              Home
precios.html            Planes y precios
css/styles.css          Estilos
js/main.js              Menú, demo del hero, animaciones y links a la app
img/                    Logo y favicon
.htaccess               noindex mientras está en desarrollo
proximamente/           Página "Próximamente" (se despliega en la raíz del dominio)
  index.html            Página + formulario
  suscribir.php         Guarda el email en MySQL
  config.example.php    Modelo de configuración (no se despliega)
db/suscriptores.sql     Tabla de interesados
```

## Estado actual: en desarrollo

| URL                              | Qué muestra                         |
|----------------------------------|-------------------------------------|
| `shoten.com.ar`                  | "Próximamente" + formulario         |
| `shoten.com.ar/ob-shoten/`       | Sitio institucional (con noindex)   |
| `app.shoten.com.ar`              | Sistema (`fe_shoten`), demo         |

## Link a la aplicación

Los botones "Probar gratis" e "Iniciar sesión" usan `data-app="register|login"` y los dos
llevan a `login.php` de fe (la prueba es la demo). En local apuntan a `../fe_shoten`.
En producción (configurado al inicio de `js/main.js`):
- "Probar gratis" va a `https://app.shoten.com.ar/login.php` (`PROD_DIRECT_URLS`).
- "Iniciar sesión" va a la lista de interesados de la raíz mientras `PROD_APP_URL` esté vacío.
  Al lanzar, completarlo con `https://app.shoten.com.ar`.

## Despliegue

GitHub Actions (`.github/workflows/deploy.yml`) en cada push a `main` (o manual desde Actions → "Run workflow"):

1. Valida la sintaxis PHP.
2. Sube `proximamente/` a `/public_html/`.
3. Sube el sitio a `/public_html/ob-shoten/`.

Cada paso guarda su propio estado de sincronización, así que uno no borra los archivos del otro.

Configurar en GitHub → Settings → Environments → `production`:

| Tipo     | Nombre           | Valor                                                  |
|----------|------------------|--------------------------------------------------------|
| Secret   | `FTP_HOST`       | Servidor FTP del hosting                               |
| Secret   | `FTP_USERNAME`   | Usuario FTP                                            |
| Secret   | `FTP_PASSWORD`   | Contraseña FTP                                         |
| Variable | `FTP_ROOT_DIR`   | Raíz del dominio (por defecto `/public_html/`)         |
| Variable | `FTP_REMOTE_DIR` | Carpeta de OB (por defecto `/public_html/ob-shoten/`)  |

## Configuración en cPanel (una sola vez)

1. **Base de datos** — *Bases de datos MySQL*: crear una base y un usuario, y asignarle al usuario
   todos los privilegios sobre esa base.
2. **Tabla** — *phpMyAdmin*: elegir la base y ejecutar `db/suscriptores.sql`.
3. **Credenciales** — *Administrador de archivos*: crear la carpeta `shoten-config/` en el home
   (al mismo nivel que `public_html`, **no adentro**) y dentro el archivo `proximamente.php`
   copiando `proximamente/config.example.php` con los datos de la base.
   ```
   /home/<usuario>/shoten-config/proximamente.php
   /home/<usuario>/public_html/suscribir.php
   ```
4. **Página de inicio** — si en `public_html` ya existe un `index.php` (página por defecto del
   hosting), borrarlo: Apache lo prioriza sobre el `index.html` de "Próximamente".
5. **Aviso por mail (opcional)** — completar `notify_email` en `proximamente.php`.
6. **Subdominio de la app (más adelante)** — *Dominios*: crear `app.shoten.com.ar` con raíz
   **fuera** de `public_html` (p. ej. `/home/<usuario>/app.shoten.com.ar`).

Los emails se consultan y exportan desde phpMyAdmin (tabla `suscriptores`).

## Desarrollo local (WinNMP)

- Sitio: http://localhost/shoten/ob-shoten/
- Próximamente: http://localhost/shoten/ob-shoten/proximamente/
- Base `shoten_local` (MariaDB de WinNMP) con la tabla de `db/suscriptores.sql`.
- Credenciales locales en `shoten-config/proximamente.php` (ignorado por git).

## Lanzamiento

1. Workflow: borrar el paso "Próximamente" y cambiar `FTP_REMOTE_DIR` a `/public_html/`.
2. Quitar el noindex: `.htaccess` y el `<meta name="robots">` de cada página.
3. En el servidor, borrar el `index.html` y el `favicon.svg` de "Próximamente" (OB los reemplaza)
   y dejar `suscribir.php` si se quiere seguir usando el formulario desde OB.
4. Redirigir `/ob-shoten/*` a `/` con un 301 para no romper links compartidos.

## Pendientes antes de publicar

- Precios reales (`data-price-*` en `precios.html`).
- Testimonios reales (hoy son de muestra).
- Links de Blog, Centro de Ayuda, Academia, redes y legales (hoy `#`).
- Mails de contacto definitivos.
