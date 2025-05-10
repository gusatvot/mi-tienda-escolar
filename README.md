# Mi Tienda Escolar

Este proyecto es una tienda en línea dedicada a la venta de útiles escolares. A continuación se detallan los archivos y su propósito dentro del proyecto.

## Estructura del Proyecto

- **index.html**: Página principal de la tienda escolar. Contiene la estructura básica de HTML y enlaces a otros recursos.
  
- **productos.html**: Muestra una lista de productos disponibles en la tienda, incluyendo enlaces a las páginas de detalle de cada producto.
  
- **producto-detalle.html**: Presenta la información detallada de un producto específico, incluyendo imágenes, descripción y precio.
  
- **carrito.html**: Muestra el contenido del carrito de compras del usuario, permitiendo ver y modificar los productos seleccionados.
  
- **contacto.html**: Proporciona un formulario de contacto para que los usuarios puedan comunicarse con la tienda.
  
- **login.html**: Contiene un formulario para que los usuarios inicien sesión en su cuenta.
  
- **registro.html**: Permite a los nuevos usuarios registrarse en la tienda.
  
- **mi-cuenta.html**: Muestra la información de la cuenta del usuario, permitiendo editar datos personales y ver el historial de pedidos.
  
- **admin/dashboard.html**: Panel de control para los administradores, donde pueden ver estadísticas y gestionar la tienda.
  
- **admin/gestion-productos.html**: Permite a los administradores gestionar los productos de la tienda, incluyendo agregar, editar y eliminar productos.
  
- **admin/gestion-pedidos.html**: Permite a los administradores gestionar los pedidos realizados por los usuarios.
  
- **css/styles.css**: Contiene los estilos CSS para el diseño de la tienda, definiendo la apariencia de los elementos HTML.
  
- **js/main.js**: Contiene el código JavaScript para la funcionalidad interactiva de la tienda, como la gestión del carrito y la validación de formularios.
  
- **images/logo.png**: Imagen del logo de la tienda.
  
- **images/productos/**: Carpeta que contiene imágenes de los productos disponibles en la tienda:
  - cuaderno.jpg
  - mochila.jpg
  - lapices.jpg
  
- **php/db.php**: Contiene la conexión a la base de datos y las funciones necesarias para interactuar con ella.
  
- **php/login.php**: Maneja la lógica de inicio de sesión de los usuarios, validando credenciales y estableciendo sesiones.
  
- **php/register.php**: Maneja la lógica de registro de nuevos usuarios, insertando datos en la base de datos.
  
- **php/funciones.php**: Contiene funciones auxiliares que pueden ser utilizadas en otros archivos PHP.
  
- **php/carrito.php**: Maneja la lógica del carrito de compras, incluyendo agregar, eliminar y mostrar productos en el carrito.
  
- **sql/crear-tablas.sql**: Contiene las instrucciones SQL para crear las tablas necesarias en la base de datos para la tienda.

## Instrucciones de Instalación

1. Clona este repositorio en tu máquina local.
2. Asegúrate de tener un servidor web con soporte para PHP y una base de datos MySQL.
3. Importa el archivo `sql/crear-tablas.sql` en tu base de datos.
4. Configura la conexión a la base de datos en `php/db.php`.
5. Abre `index.html` en tu navegador para comenzar a usar la tienda.

## Uso

Navega por las diferentes páginas para explorar los productos, gestionar tu carrito y acceder a tu cuenta. Los administradores pueden acceder a las secciones de gestión a través del panel de control.