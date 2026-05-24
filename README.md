# BookRoulette

BookRoulette es una aplicacion web en PHP, MySQL, HTML, CSS y JavaScript que recomienda libros mediante una ruleta interactiva. El usuario elige un genero literario y un estado de animo, la app calcula las mejores opciones del catalogo y despues permite girar la ruleta para obtener una recomendacion.

## Funcionalidades

- Recomendaciones segun genero y estado de animo.
- Sistema de puntuacion basado en tono, profundidad y energia.
- Ruleta visual para elegir entre los libros mejor puntuados.
- Registro e inicio de sesion de usuarios.
- Guardado y eliminacion de libros favoritos.
- Panel de administracion para anadir, editar y eliminar libros.
- Catalogo inicial ampliado con libros de varios generos.

## Tecnologias

- PHP
- MySQL / MariaDB
- JavaScript modular
- HTML5
- CSS3
- XAMPP

## Estructura del proyecto

```text
bookroulette/
+-- admin/              # Panel de administracion y CRUD de libros
+-- api/                # Endpoints JSON usados por JavaScript
+-- auth/               # Login, registro y cierre de sesion
+-- css/                # Estilos de la aplicacion
+-- db/                 # Conexion y scripts SQL
+-- img/                # Imagenes del proyecto
+-- js/                 # Logica de ruleta, API y scoring
+-- index.php           # Pantalla principal
```

## Instalacion en XAMPP

1. Copia la carpeta del proyecto en:

```text
C:\xampp\htdocs\bookroulette
```

2. Abre XAMPP y activa:

- Apache
- MySQL

3. Entra en phpMyAdmin:

```text
http://localhost/phpmyadmin
```

4. Importa el script principal:

```text
db/bookroulette.sql
```

Este archivo crea la base de datos `bookroulette`, las tablas necesarias y carga el catalogo inicial.

5. Abre la aplicacion en el navegador:

```text
http://localhost/bookroulette/index.php
```

## Ampliar una base de datos ya existente

Si ya tenias creada la base de datos antes de ampliar el catalogo, no hace falta borrar nada. Importa este archivo desde phpMyAdmin:

```text
db/ampliar_catalogo.sql
```

El script esta preparado para insertar solo los libros que no existan ya con el mismo titulo y autor.

## Configuracion de base de datos

La conexion esta definida en:

```text
db/conexion.php
```

Valores por defecto:

```php
$host = "localhost";
$user = "root";
$pass = "";
$db = "bookroulette";
```

Estos valores coinciden con una instalacion habitual de XAMPP en local.

## Uso de la aplicacion

1. Selecciona un genero literario.
2. Selecciona un estado de animo.
3. Pulsa `Generar opciones`.
4. Revisa los libros mejor puntuados.
5. Pulsa `Girar ruleta`.
6. Si has iniciado sesion, puedes guardar el resultado en favoritos.

## Estados de animo disponibles

- Feliz
- Relajado
- Reflexivo
- Curioso
- Aventurero
- Romantico
- Con ganas de algo intenso
- Oscuro

Cada mood se compara con los valores de cada libro:

- `tono`: indica si el libro es mas oscuro, neutro o luminoso.
- `profundidad`: indica si la lectura es ligera o mas reflexiva.
- `energia`: indica si el ritmo es pausado o intenso.

## Panel de administracion

El panel admin esta disponible en:

```text
http://localhost/bookroulette/admin/dashboard.php
```

Desde ahi se puede:

- Ver el catalogo.
- Anadir libros.
- Editar libros existentes.
- Eliminar libros.
- Revisar contadores de libros, usuarios y favoritos.

Los usuarios creados desde el registro tienen el rol `usuario` por defecto. Para convertir un usuario en administrador, cambia su campo `rol` a `admin` en la tabla `usuarios`.

Ejemplo SQL:

```sql
UPDATE usuarios
SET rol = 'admin'
WHERE email = 'tu-email@example.com';
```

## Archivos SQL importantes

- `db/bookroulette.sql`: instalacion completa desde cero.
- `db/ampliar_catalogo.sql`: ampliacion del catalogo para bases ya existentes.

## Notas de desarrollo

- La API devuelve JSON desde la carpeta `api/`.
- La funcion de recomendacion principal esta en `js/scoring.js`.
- La ruleta visual esta en `js/roulette.js`.
- La conexion a base de datos tambien asegura algunas columnas y tablas necesarias durante el desarrollo local.
