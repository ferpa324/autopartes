# AutoMarket

E-commerce educativo desarrollado con PHP, MariaDB/MySQL, Bootstrap, JavaScript y una API externa para consultar marcas/modelos de vehículos.

## Novedades de esta versión

- 34 autos cargados en la base de datos, con marcas y modelos variados.
- 36 repuestos cargados.
- Los autos tienen marca, modelo, precio, stock y kilómetros.
- Los repuestos específicos están asociados a una marca y modelo de vehículo.
- La sección **Repuestos** permite filtrar por marca y luego por modelo.
- Ejemplo: al elegir **Renault → Clio** aparecen repuestos compatibles con Renault Clio, incluida la cremallera de dirección.
- La sección **Autos** mantiene el selector de marca/modelo mediante la API vPIC de NHTSA.

## Instalación con XAMPP

1. Copiar la carpeta `autopartes` dentro de `C:\xampp\htdocs\`.
2. Iniciar **Apache** y **MySQL** desde XAMPP.
3. Abrir HeidiSQL o phpMyAdmin.
4. Ejecutar el archivo `database/automarket.sql` para crear la base de datos y cargar los productos de ejemplo.
5. Abrir `http://localhost/autopartes/`.

> Importante: el script SQL reconstruye las tablas `usuarios`, `productos`, `pedidos` y `pedido_detalles`. Para una instalación nueva funciona directamente. Si ya tenés datos que quieras conservar, hacé una copia antes de importarlo.

## API de vehículos

La página `autos.php` usa JavaScript para consultar la API vPIC de NHTSA. Necesita conexión a Internet para cargar las marcas y modelos del selector.
