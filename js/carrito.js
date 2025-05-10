// js/carrito.js - Funciones JavaScript para el carrito

document.addEventListener('DOMContentLoaded', function() {
   actualizarContadorCarrito();
   actualizarTotales();
   verificarCarritoVacio();
});

// Función para actualizar la cantidad del producto
function actualizarCantidad(id, accion) {
   const row = document.querySelector(`tr[data-id="${id}"]`);
   const inputCantidad = row.querySelector('.cantidad-input');
   let cantidad = parseInt(inputCantidad.value);
   
   if (accion === 'sumar') {
       cantidad++;
   } else if (accion === 'restar' && cantidad > 1) {
       cantidad--;
   } else if (accion === 'input') {
       // La cantidad ya se actualizó con el input
       if (cantidad < 1) {
           cantidad = 1;
       }
   }
   
   inputCantidad.value = cantidad;
   
   // Actualizar en el servidor
   fetch('php/carrito-actualizar-cantidad.php', {
       method: 'POST',
       headers: {
           'Content-Type': 'application/x-www-form-urlencoded',
       },
       body: `id=${id}&cantidad=${cantidad}`
   })
   .then(response => response.json())
   .then(data => {
       if (data.success) {
           // Actualizar subtotal en la interfaz
           const precio = parseFloat(inputCantidad.getAttribute('data-precio'));
           const subtotal = precio * cantidad;
           row.querySelector('.subtotal').textContent = `$${subtotal}`;
           
           // Actualizar totales
           actualizarTotales();
           
           // Mostrar mensaje
           mostrarToast(data.mensaje);
       } else {
           mostrarToast('Error: ' + data.mensaje);
       }
   })
   .catch(error => {
       console.error('Error:', error);
       mostrarToast('Error al actualizar cantidad');
   });
}

// Función para eliminar un producto
function eliminarProducto(id) {
   fetch('php/carrito-eliminar.php', {
       method: 'POST',
       headers: {
           'Content-Type': 'application/x-www-form-urlencoded',
       },
       body: `id=${id}`
   })
   .then(response => response.json())
   .then(data => {
       if (data.success) {
           // Eliminar fila de la tabla
           const row = document.querySelector(`tr[data-id="${id}"]`);
           row.remove();
           
           // Actualizar totales
           actualizarTotales();
           
           // Verificar si el carrito quedó vacío
           verificarCarritoVacio();
           
           // Actualizar contador de carrito
           document.getElementById('contador-carrito').textContent = data.num_productos;
           
           // Mostrar mensaje
           mostrarToast(data.mensaje);
       } else {
           mostrarToast('Error: ' + data.mensaje);
       }
   })
   .catch(error => {
       console.error('Error:', error);
       mostrarToast('Error al eliminar producto');
   });
}

// Función para vaciar el carrito
function vaciarCarrito() {
   if (!confirm('¿Estás seguro de vaciar el carrito?')) {
       return;
   }
   
   fetch('php/carrito-vaciar.php')
   .then(response => response.json())
   .then(data => {
       if (data.success) {
           // Eliminar todas las filas de la tabla
           document.querySelectorAll('.tabla-carrito tbody tr').forEach(row => {
               row.remove();
           });
           
           // Actualizar totales
           actualizarTotales();
           
           // Mostrar carrito vacío
           verificarCarritoVacio();
           
           // Actualizar contador de carrito
           document.getElementById('contador-carrito').textContent = '0';
           
           // Mostrar mensaje
           mostrarToast(data.mensaje);
       } else {
           mostrarToast('Error: ' + data.mensaje);
       }
   })
   .catch(error => {
       console.error('Error:', error);
       mostrarToast('Error al vaciar carrito');
   });
}

// Función para actualizar el carrito
function actualizarCarrito() {
   // Obtener todas las cantidades
   const productos = [];
   document.querySelectorAll('.tabla-carrito tbody tr').forEach(row => {
       const id = row.getAttribute('data-id');
       const cantidad = row.querySelector('.cantidad-input').value;
       productos.push({ id, cantidad });
   });
   
   // Actualizar cada producto
   let promesas = [];
   productos.forEach(producto => {
       const promesa = fetch('php/carrito-actualizar-cantidad.php', {
           method: 'POST',
           headers: {
               'Content-Type': 'application/x-www-form-urlencoded',
           },
           body: `id=${producto.id}&cantidad=${producto.cantidad}`
       });
       promesas.push(promesa);
   });
   
   Promise.all(promesas)
   .then(() => {
       // Actualizar totales
       actualizarTotales();
       
       // Mostrar mensaje
       mostrarToast('Carrito actualizado');
   })
   .catch(error => {
       console.error('Error:', error);
       mostrarToast('Error al actualizar carrito');
   });
}

// Función para actualizar los totales
function actualizarTotales() {
   let subtotal = 0;
   
   // Calcular subtotal
   document.querySelectorAll('.tabla-carrito tbody tr').forEach(row => {
       const subtotalText = row.querySelector('.subtotal').textContent;
       subtotal += parseFloat(subtotalText.replace('$', ''));
   });
   
   // Actualizar subtotal
   document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
   
   // Calcular descuento (ejemplo: 10% si es mayorista)
   // Aquí puedes implementar tu lógica de descuentos
   const descuento = 0; // En este ejemplo no hay descuento
   document.getElementById('descuento').textContent = `$${descuento.toFixed(2)}`;
   
   // Actualizar total
   const total = subtotal - descuento;
   document.getElementById('total').textContent = `$${total.toFixed(2)}`;
   
   // Actualizar contador de productos
   const numProductos = document.querySelectorAll('.tabla-carrito tbody tr').length;
   const textoProductos = numProductos === 1 ? 'producto' : 'productos';
   document.querySelector('.carrito-header p').textContent = `${numProductos} ${textoProductos} en tu carrito`;
}

// Verificar si el carrito está vacío
function verificarCarritoVacio() {
   const numProductos = document.querySelectorAll('.tabla-carrito tbody tr').length;
   
   if (numProductos === 0) {
       document.getElementById('carrito-contenido').style.display = 'none';
       document.getElementById('carrito-vacio').style.display = 'block';
   } else {
       document.getElementById('carrito-contenido').style.display = 'grid';
       document.getElementById('carrito-vacio').style.display = 'none';
   }
}

// Mostrar mensaje toast
function mostrarToast(mensaje) {
   const toast = document.getElementById('toast');
   toast.textContent = mensaje;
   toast.style.top = '20px';
   
   setTimeout(() => {
       toast.style.top = '-60px';
   }, 3000);
}

// Actualizar contador de carrito
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