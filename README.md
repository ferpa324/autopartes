# AutoMarket

E-commerce educativo con PHP, MariaDB/MySQL, Bootstrap y JavaScript.

## Instalación con XAMPP

1. Copiar la carpeta `autopartes` dentro de `C:\xampp\htdocs\`.
2. Iniciar **Apache** y **MySQL** desde XAMPP.
3. En phpMyAdmin o HeidiSQL ejecutar **uno** de estos scripts:
   - `database/automarket.sql` → instalación nueva (borra y recrea las tablas).
   - `database/migracion_admin.sql` → si ya tenés la base cargada y querés conservar tus datos.
4. Abrir `http://localhost/autopartes/`.

## Cómo volverte administrador

1. Registrate normalmente en `/autopartes/registro.php`.
2. Ejecutá en la base de datos (con tu email):

   ```sql
   UPDATE usuarios SET rol = 'admin' WHERE email = 'tu@email.com';
   ```
3. Cerrá sesión y volvé a ingresar: aparece el enlace **⚙️ Admin** en el menú.

## Panel de administración (`/admin`)

| Función | Dónde |
|---|---|
| Ver todas las publicaciones, filtrar por tipo/estado y buscar | `admin/index.php` |
| **Reponer stock** (suma unidades al stock actual) | botón `+ Reponer` en cada fila |
| **Publicar** una publicación nueva (con foto) | `➕ Nueva publicación` |
| **Modificar** nombre, precio, stock, km, descripción, foto | `✏️ Editar` |
| Ocultar / volver a publicar sin borrar | `🙈 Ocultar` / `👁️ Publicar` |
| Eliminar definitivamente | `🗑️` (no permite borrar lo que ya está en pedidos) |

Reglas: los **autos exigen imagen** (JPG/PNG/WEBP, hasta 5 MB). Las imágenes subidas se
guardan en `img/autos/` o `img/repuestos/`. Todos los formularios llevan token CSRF y el
rol de admin se verifica contra la base de datos en cada acción.

## Estructura

```
admin/       panel de administración
backend/     endpoints (login, registro, chatbot)
config/      configuración, base de datos, sesión/roles/CSRF
includes/    header, footer, consultas, lista de productos, subida de imágenes
css/ js/     estilos y scripts
img/autos/   fotos de los autos
database/    scripts SQL
```

## API de vehículos

`autos.php` consulta la API vPIC de NHTSA para los selectores de marca y modelo (requiere Internet).
