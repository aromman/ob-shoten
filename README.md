# ob-shoten

Sitio institucional (onboarding) de **Shoten**, el software de gestión para librerías.
Sitio estático: HTML + CSS + JS, sin build.

```
index.html     Home
precios.html   Planes y precios
css/styles.css Estilos
js/main.js     Menú, demo del hero, animaciones y links a la app
img/           Logo y favicon
```

## Link a la aplicación

Los botones "Probar gratis" e "Iniciar sesión" usan `data-app="register|login"`.
La URL base de la app (`fe_shoten`) se configura en `APP_URL` al inicio de `js/main.js`.

## Pendientes antes de publicar

- Precios reales (`data-price-*` en `precios.html`).
- Testimonios reales (hoy son de muestra).
- Links de Blog, Centro de Ayuda, Academia, redes y legales (hoy `#`).
- Mails de contacto definitivos.
