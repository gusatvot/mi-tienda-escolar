<?php
// productos.php - Solo accesible si estás logueado
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Catálogo - Tienda Escolar</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto :wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/styles.css" />
</head>
<body>

  <!-- Encabezado -->
  <header>
    <div class="container">
      <div class="logo">
        <img src="images/logo.png" alt="Logo" width="50">
        <h1>Tienda Escolar S.A.</h1>
      </div>

      <!-- Menú de navegación -->
      <nav>
        <ul>
          <li><a href="index.html">Inicio</a></li>
          <li><a href="tienda.html">Tienda</a></li>
          <li><a href="contacto.html">Contacto</a></li>
        </ul>
      </nav>

      <!-- Barra de usuario y carrito -->
      <div class="usuario-carrito">
        <p><?= htmlspecialchars($_SESSION['usuario']['email']) ?></p>
        <a href="logout.php">Cerrar Sesión</a>
        <div class="carrito">
          <a href="carrito.html">
            <img src="images/carrito-icon.png" alt="Carrito" width="30">
            <span id="contador-carrito">0</span>
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- Contenido principal -->
  <main class="container">
    <section class="contenido-pagina">
      <h2>Catálogo de Productos</h2>
      <p>Accede a precios minoristas y mayoristas.</p>

      <!-- Filtros -->
      <div class="filtros">
        <div class="filtro-grupo">
          <label for="filtro-nombre">Buscar:</label>
          <input type="text" id="filtro-nombre" placeholder="Nombre del producto...">
        </div>

        <div class="filtro-grupo">
          <label for="filtro-categoria">Categoría:</label>
          <select id="filtro-categoria">
            <option value="">Todas las categorías</option>
          </select>
        </div>

        <div class="filtro-grupo">
          <label for="filtro-marca">Marca:</label>
          <select id="filtro-marca">
            <option value="">Todas las marcas</option>
          </select>
        </div>

        <div class="filtro-grupo">
          <label for="filtro-precio-min">Precio entre:</label>
          <input type="number" id="filtro-precio-min" placeholder="Min">
        </div>
        <div class="filtro-grupo">
          <label for="filtro-precio-max">&nbsp;</label>
          <input type="number" id="filtro-precio-max" placeholder="Max">
        </div>
      </div>

      <!-- Lista de productos -->
      <div id="lista-productos" class="grid-productos">
        <p>Cargando productos...</p>
      </div>

      <!-- Paginación -->
      <div class="paginacion">
        <button class="btn secondary" onclick="paginaAnterior()">Anterior</button>
        <span id="info-pagina">Página 1</span>
        <button class="btn secondary" onclick="paginaSiguiente()">Siguiente</button>
      </div>
    </section>
  </main>

  <!-- Pie de página -->
  <footer>
    <p>&copy; 2025 Tienda Escolar S.A. | Hecho con ayuda de un gran mentor 💡</p>
  </footer>

  <!-- Cargar productos con JavaScript -->
  <script>
    let productos = [];
    let productosFiltrados = [];
    const productosPorPagina = 12;
    let paginaActual = 1;

    document.addEventListener("DOMContentLoaded", function () {
      Promise.all([
        fetch('php/productos-lista.php').then(res => res.json()),
        fetch('php/categorias-lista.php').then(res => res.json()),
        fetch('php/marcas-lista.php').then(res => res.json())
      ])
      .then(([productosData, categoriasData, marcasData]) => {
        productos = productosData;
        productosFiltrados = [...productos];

        // Cargar categorías en filtro
        const filtroCategoria = document.getElementById('filtro-categoria');
        filtroCategoria.innerHTML = '<option value="">Todas las categorías</option>';
        categoriasData.forEach(cat => {
          const option = document.createElement('option');
          option.value = cat.nombre;
          option.textContent = cat.nombre;
          filtroCategoria.appendChild(option);
        });

        // Cargar marcas en filtro
        const filtroMarca = document.getElementById('filtro-marca');
        filtroMarca.innerHTML = '<option value="">Todas las marcas</option>';
        marcasData.forEach(marca => {
          const option = document.createElement('option');
          option.value = marca.nombre;
          option.textContent = marca.nombre;
          filtroMarca.appendChild(option);
        });

        mostrarProductos();
        actualizarContadorCarrito();
      })
      .catch(error => {
        console.error('Error al cargar los datos:', error);
        document.getElementById('lista-productos').innerHTML = "<p>Error al cargar los productos.</p>";
      });
    });

    function mostrarProductos() {
      const listaDiv = document.getElementById('lista-productos');
      listaDiv.innerHTML = "";

      if (productosFiltrados.length === 0) {
        listaDiv.innerHTML = "<p>No hay productos disponibles.</p>";
        return;
      }

      const inicio = (paginaActual - 1) * productosPorPagina;
      const fin = inicio + productosPorPagina;
      const paginaActualProductos = productosFiltrados.slice(inicio, fin);

      paginaActualProductos.forEach(p => {
        const card = document.createElement('div');
        card.className = 'producto-card';

        let precioMostrar = `<p><strong>\$${parseFloat(p.precio_minorista).toFixed(2)} (Minorista)</strong></p>`;
        
        if ("<?= $_SESSION['usuario']['tipo'] ?>" === "mayorista") {
          precioMostrar += `<p>\$${parseFloat(p.precio_mayorista).toFixed(2)} (Mayorista)</p>`;
        }

        card.innerHTML = `
          <img src="${p.imagen_url || 'images/productos/default.jpg'}" alt="${p.nombre}">
          <h4>${p.nombre}</h4>
          ${precioMostrar}
          <a href="carrito-agregar.php?id=${p.id}" class="btn secondary">Agregar al carrito</a>
        `;

        listaDiv.appendChild(card);
      });

      // Actualizar info de página
      document.getElementById('info-pagina').textContent = `Página ${paginaActual}`;
    }

    function aplicarFiltros() {
      let filtrados = [...productos];
      const nombre = document.getElementById('filtro-nombre').value.toLowerCase();
      const categoria = document.getElementById('filtro-categoria').value.toLowerCase();
      const marca = document.getElementById('filtro-marca').value.toLowerCase();
      const precioMin = parseFloat(document.getElementById('filtro-precio-min').value);
      const precioMax = parseFloat(document.getElementById('filtro-precio-max').value);

      if (nombre) {
        filtrados = filtrados.filter(p => p.nombre.toLowerCase().includes(nombre));
      }

      if (categoria) {
        filtrados = filtrados.filter(p => p.categoria_nombre?.toLowerCase().includes(categoria));
      }

      if (marca) {
        filtrados = filtrados.filter(p => p.marca_nombre?.toLowerCase().includes(marca));
      }

      if (!isNaN(precioMin)) {
        filtrados = filtrados.filter(p => parseFloat(p.precio_minorista) >= precioMin);
      }

      if (!isNaN(precioMax)) {
        filtrados = filtrados.filter(p => parseFloat(p.precio_minorista) <= precioMax);
      }

      productosFiltrados = filtrados;
      paginaActual = 1;
      mostrarProductos();
    }

    function paginaAnterior() {
      if (paginaActual > 1) {
        paginaActual--;
        mostrarProductos();
      }
    }

    function paginaSiguiente() {
      const totalPaginas = Math.ceil(productosFiltrados.length / productosPorPagina);
      if (paginaActual < totalPaginas) {
        paginaActual++;
        mostrarProductos();
      }
    }

    function actualizarContadorCarrito() {
      fetch('php/carrito-actualizar.php')
        .then(response => response.json())
        .then(data => {
          document.getElementById('contador-carrito').textContent = data.cantidad;
        })
        .catch(error => {
          console.error('Error al cargar el carrito:', error);
        });
    }

    setInterval(actualizarContadorCarrito, 3000); // Cada 3 segundos
    document.addEventListener("DOMContentLoaded", () => {
      actualizarContadorCarrito();
    });

    // Eventos de filtro
    document.getElementById('filtro-nombre').addEventListener('input', aplicarFiltros);
    document.getElementById('filtro-categoria').addEventListener('change', aplicarFiltros);
    document.getElementById('filtro-marca').addEventListener('change', aplicarFiltros);
    document.getElementById('filtro-precio-min').addEventListener('input', aplicarFiltros);
    document.getElementById('filtro-precio-max').addEventListener('input', aplicarFiltros);
  </script>

  <!-- Mensaje Toast -->
  <div id="toast">Producto agregado al carrito</div>

</body>
</html>