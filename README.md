# Fragancias Prime

Fragancias Prime es una aplicacion web de e-commerce para la venta de perfumes y fragancias. El sistema permite navegar productos, buscar fragancias, ver detalles, gestionar un carrito de compras, registrar usuarios y administrar productos, usuarios y pedidos desde un panel privado.

## Tecnologias

- PHP
- MySQL / MariaDB
- HTML, CSS y JavaScript
- Bootstrap 5
- Bootstrap Icons
- Composer
- Laminas Mail y Laminas Mime

## Funcionalidades principales

- Catalogo de productos destacados.
- Busqueda de productos por nombre, subtitulo o descripcion.
- Vista de detalle de producto.
- Registro e inicio de sesion de usuarios.
- Carrito de compras.
- Finalizacion de pedidos.
- Consulta de pedidos del usuario.
- Panel administrador.
- Alta, baja y modificacion de productos.
- Gestion de usuarios.
- Gestion de pedidos pendientes.
- Subida y visualizacion de imagenes de productos.
- Fallback de imagen cuando un producto no tiene imagen disponible.

## Requisitos

- PHP 8 o superior.
- MySQL o MariaDB.
- Apache, recomendado mediante XAMPP.
- Composer instalado.

## Instalacion

1. Clonar o copiar el proyecto dentro de la carpeta publica de Apache.

   En XAMPP, por ejemplo:

   ```text
   C:\xampp\htdocs\fragancias-prime
   ```

2. Entrar a la carpeta del proyecto.

   ```bash
   cd C:\xampp\htdocs\fragancias-prime
   ```

3. Instalar dependencias de Composer.

   ```bash
   composer install
   ```

4. Crear la base de datos en MySQL.

   ```sql
   CREATE DATABASE fragancias_prime CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
   ```

5. Importar el archivo SQL del proyecto en esa base de datos.

   Archivo:

   ```text
   bdcarritocompras.sql
   ```

6. Revisar la configuracion de conexion en:

   ```text
   app/config/database.php
   ```

   Configuracion por defecto:

   ```php
   host: localhost
   dbname: fragancias_prime
   user: root
   pass: ''
   ```

7. Iniciar Apache y MySQL desde XAMPP.

8. Abrir la aplicacion en el navegador.

   ```text
   http://localhost/fragancias-prime/public/
   ```

## Estructura del proyecto

```text
app/
  config/        Configuracion de base de datos y servicios
  control/       Controladores de la aplicacion
  helpers/       Funciones auxiliares para vistas, assets y header
  middleware/    Validaciones de autenticacion y roles
  model/         Modelos y acceso a datos
  vista/         Vistas publicas y de usuario
  vista-admin/   Vistas del panel administrador

public/
  css/           Estilos
  img/           Imagenes publicas
  js/            JavaScript del frontend
  upload/        Imagenes subidas desde el panel
  index.php      Punto de entrada de la aplicacion

vendor/          Dependencias instaladas por Composer
```

## Rutas utiles

La aplicacion usa un router simple mediante parametros GET:

```text
?controller=home&action=index
?controller=producto&action=buscar&q=azzaro
?controller=producto&action=ver&id=1
?controller=auth&action=login
?controller=auth&action=registro
?controller=carrito&action=index
?controller=pedido&action=misPedidos
?controller=admin&action=dashboard
?controller=producto&action=listar
```

## Imagenes de productos

Las imagenes publicas del proyecto se guardan en:

```text
public/img
```

Las imagenes subidas desde el panel administrador se guardan en:

```text
public/upload/productos
```

Si un producto no tiene imagen valida, el sistema utiliza:

```text
public/img/no-image.png
```

## Roles

El sistema diferencia funcionalidades segun el usuario autenticado:

- Usuario invitado: puede navegar, buscar productos, ver detalles, registrarse e iniciar sesion.
- Usuario cliente: puede agregar productos al carrito, finalizar compras y ver sus pedidos.
- Usuario administrador: puede acceder al panel de administracion, gestionar productos, usuarios y pedidos.
