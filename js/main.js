// js/main.js - Funciones principales

// Menú móvil
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.createElement('button');
    menuToggle.className = 'menu-toggle';
    menuToggle.innerHTML = '☰';
    
    const logo = document.querySelector('.logo');
    if (logo) {
        logo.appendChild(menuToggle);
    }
    
    menuToggle.addEventListener('click', function() {
        const nav = document.querySelector('nav');
        if (nav) {
            nav.classList.toggle('active');
            menuToggle.innerHTML = nav.classList.contains('active') ? '✕' : '☰';
        }
    });
    
    // Cerrar menú cuando se hace clic en un enlace
    const navLinks = document.querySelectorAll('nav a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            const nav = document.querySelector('nav');
            if (nav && nav.classList.contains('active')) {
                nav.classList.remove('active');
                menuToggle.innerHTML = '☰';
            }
        });
    });
    
    // Actualizar contador de carrito
    actualizarContadorCarrito();
});

// Esta función ya existe en tus archivos, pero la incluyo por completitud
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

// Ejecutar cada 3 segundos (ya existe en tus archivos)
setInterval(actualizarContadorCarrito, 3000);