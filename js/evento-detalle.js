// evento-detalle.js - Lógica completa del detalle del evento

verificarAuth();

const eventoId = getUrlParameter('id');
const usuarioActual = getUsuarioActual();
let eventoActual = null;
let calificacionSeleccionada = 0;
let tieneBoleto = false;

if (!eventoId) {
    redirect('eventos.html');
}

// ==================== INICIALIZACIÓN ====================
(async () => {
    const evento = await obtenerEvento(eventoId);
    if (evento) {
        eventoActual = evento;
        await cargarDatosEvento(evento);
        
        // Verificar si el usuario tiene boleto para este evento
        tieneBoleto = true;
        
        if (tieneBoleto) {
            // Mostrar pestañas solo si tiene boleto
            document.getElementById('tabs-section').classList.remove('hidden');
            await cargarResenas();
            await cargarChat();
            await cargarFotos();
            inicializarEventosChat();
        }
        
        document.getElementById('loading').classList.add('hidden');
        document.getElementById('main-content').classList.remove('hidden');
    } else {
        redirect('eventos.html');
    }
})();

// ==================== VERIFICAR SI TIENE BOLETO ====================
async function verificarBoleto() {
    try {
        const response = await fetch(`${API_URL}/boletos/mis-boletos.php`, {
            headers: getHeaders()
        });
        const data = await response.json();
        
        if (data.success) {
            return data.boletos.some(b => b.evento_id == eventoId);
        }
        return false;
    } catch (error) {
        console.error('Error verificando boleto:', error);
        return false;
    }
}

// ==================== CARGAR DATOS DEL EVENTO ====================
async function cargarDatosEvento(evento) {
    // Banner principal
    document.getElementById('evento-imagen').src = `${API_URL}/uploads/${evento.imagen_portada}`;
    document.getElementById('evento-imagen').alt = evento.titulo;
    document.getElementById('evento-categoria').textContent = evento.categoria || 'General';
    document.getElementById('evento-titulo').textContent = evento.titulo;
    document.getElementById('evento-descripcion').textContent = evento.descripcion;
    document.getElementById('evento-fecha').textContent = formatDate(evento.fecha_evento);
    document.getElementById('evento-ubicacion').textContent = evento.ubicacion;
    if (evento.direccion) {
        document.getElementById('evento-direccion').textContent = evento.direccion;
    }

    // Sidebar
    document.getElementById('info-fecha').textContent = formatDate(evento.fecha_evento);
    document.getElementById('info-lugar').textContent = evento.ubicacion;
    if (evento.direccion) {
        document.getElementById('info-direccion').textContent = evento.direccion;
    }
    document.getElementById('info-precio').textContent = formatPrice(evento.precio_boleto);
    document.getElementById('info-disponibles').textContent = `${evento.boletos_disponibles} boletos disponibles`;

    // Botón de acción
    const esOrganizador = usuarioActual && evento.organizador_id === usuarioActual.id;
    renderBotonAccionDetalle(esOrganizador, evento.boletos_disponibles);
}

function renderBotonAccionDetalle(esOrganizador, boletosDisponibles) {
    const container = document.getElementById('boton-accion');
    
    if (esOrganizador) {
        container.innerHTML = `
            <button onclick="verEstadisticas()" 
                    class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-3 rounded-lg font-bold hover:from-purple-700 hover:to-indigo-700 mb-3">
                📊 Ver Estadísticas
            </button>
            <button onclick="eliminarEvento()" 
                    class="w-full bg-red-600 text-white py-3 rounded-lg font-bold hover:bg-red-700">
                🗑️ Eliminar Evento
            </button>
            <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded mt-4">
                <p class="text-xs text-blue-700">💡 Eres el organizador de este evento</p>
            </div>
        `;
    } else if (boletosDisponibles > 0) {
        container.innerHTML = `
            <button onclick="abrirModalCompra()" 
                    class="w-full bg-indigo-600 text-white py-3 rounded-lg font-bold hover:bg-indigo-700">
                🎟️ Comprar Boletos
            </button>
        `;
    } else {
        container.innerHTML = `
            <div class="bg-red-50 text-red-600 py-3 rounded-lg text-center font-bold">
                ⚠️ Boletos Agotados
            </div>
        `;
    }
}

// ==================== SISTEMA DE PESTAÑAS ====================
const tabBtns = document.querySelectorAll('.tab-btn');
const tabContents = document.querySelectorAll('.tab-content');

tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        const tabName = btn.dataset.tab;
        
        // Actualizar botones
        tabBtns.forEach(b => b.classList.remove('tab-active'));
        btn.classList.add('tab-active');
        
        // Actualizar contenido
        tabContents.forEach(content => content.classList.add('hidden'));
        document.getElementById(`tab-${tabName}`).classList.remove('hidden');
    });
});

// ==================== RESEÑAS ====================
async function cargarResenas() {
    try {
        const response = await fetch(`${API_URL}/resenas/index.php?evento_id=${eventoId}`, {
            headers: getHeaders()
        });
        const data = await response.json();
        
        if (data.success) {
            // Actualizar promedio
            document.getElementById('promedio-calificacion').textContent = data.promedio || '0.0';
            document.getElementById('total-resenas').textContent = `${data.total} reseña${data.total !== 1 ? 's' : ''}`;
            renderEstrellas(data.promedio, 'estrellas-promedio');
            
            // Mostrar reseñas
            const container = document.getElementById('lista-resenas');
            if (data.resenas.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center py-8">Aún no hay reseñas. ¡Sé el primero!</p>';
            } else {
                container.innerHTML = data.resenas.map(r => `
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-semibold text-gray-800">${r.nombre}</p>
                                <div class="flex">${'★'.repeat(r.calificacion)}${'☆'.repeat(5-r.calificacion)}</div>
                            </div>
                            <span class="text-xs text-gray-500">${formatDate(r.fecha_resena)}</span>
                        </div>
                        ${r.comentario ? `<p class="text-gray-600 text-sm">${r.comentario}</p>` : ''}
                    </div>
                `).join('');
            }
        }
    } catch (error) {
        console.error('Error cargando reseñas:', error);
    }
}

function renderEstrellas(promedio, containerId) {
    const container = document.getElementById(containerId);
    const estrellas = Math.round(promedio);
    container.innerHTML = '★'.repeat(estrellas) + '☆'.repeat(5 - estrellas);
}

// Sistema de calificación por estrellas
const stars = document.querySelectorAll('#rating-stars .star');
stars.forEach(star => {
    star.addEventListener('click', () => {
        calificacionSeleccionada = parseInt(star.dataset.rating);
        stars.forEach((s, idx) => {
            s.classList.toggle('active', idx < calificacionSeleccionada);
        });
    });
});

// Enviar reseña
document.getElementById('form-resena').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    if (calificacionSeleccionada === 0) {
        showToast('Selecciona una calificación', 'error');
        return;
    }
    
    const comentario = document.getElementById('comentario-resena').value;
    
    try {
        const response = await fetch(`${API_URL}/resenas/index.php`, {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({
                evento_id: eventoId,
                usuario_id: usuarioActual.id,
                calificacion: calificacionSeleccionada,
                comentario: comentario
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('¡Reseña publicada!', 'success');
            document.getElementById('comentario-resena').value = '';
            calificacionSeleccionada = 0;
            stars.forEach(s => s.classList.remove('active'));
            await cargarResenas();
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        showToast('Error publicando reseña', 'error');
    }
});

// ==================== CHAT ====================
let chatInterval;

async function cargarChat() {
    try {
        const response = await fetch(`${API_URL}/chat/index.php?evento_id=${eventoId}`, {
            headers: getHeaders()
        });
        const data = await response.json();
        
        if (data.success) {
            renderMensajes(data.mensajes);
        }
    } catch (error) {
        console.error('Error cargando chat:', error);
    }
}

function renderMensajes(mensajes) {
    const container = document.getElementById('chat-mensajes');
    
    if (mensajes.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-8">Aún no hay mensajes. ¡Inicia la conversación!</p>';
    } else {
        container.innerHTML = mensajes.map(m => {
            const esPropio = m.usuario_id == usuarioActual.id;
            return `
                <div class="chat-message mb-3 ${esPropio ? 'text-right' : ''}">
                    <div class="inline-block ${esPropio ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-800'} px-4 py-2 rounded-lg max-w-xs">
                        ${!esPropio ? `<p class="text-xs font-semibold mb-1">${m.nombre}</p>` : ''}
                        <p class="text-sm">${m.mensaje}</p>
                        <p class="text-xs opacity-75 mt-1">${formatDate(m.fecha_envio)}</p>
                    </div>
                </div>
            `;
        }).join('');
    }
    
    // Scroll al final
    container.scrollTop = container.scrollHeight;
}

function inicializarEventosChat() {
    // Actualizar chat cada 5 segundos
    chatInterval = setInterval(cargarChat, 5000);
}

// Enviar mensaje
document.getElementById('form-chat').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const input = document.getElementById('mensaje-chat');
    const mensaje = input.value.trim();
    
    if (!mensaje) return;
    
    try {
        const response = await fetch(`${API_URL}/chat/index.php`, {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({
                evento_id: eventoId,
                usuario_id: usuarioActual.id,
                mensaje: mensaje
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            input.value = '';
            await cargarChat();
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        showToast('Error enviando mensaje', 'error');
    }
});

// ==================== FOTOS ====================
async function cargarFotos() {
    try {
        const response = await fetch(`${API_URL}/fotos/index.php?evento_id=${eventoId}`, {
            headers: getHeaders()
        });
        const data = await response.json();
        
        if (data.success) {
            renderFotos(data.fotos);
        }
    } catch (error) {
        console.error('Error cargando fotos:', error);
    }
}

function renderFotos(fotos) {
    const container = document.getElementById('galeria-fotos');
    
    if (fotos.length === 0) {
        container.innerHTML = '<p class="col-span-full text-gray-500 text-center py-8">Aún no hay fotos. ¡Sube la primera!</p>';
    } else {
        container.innerHTML = fotos.map(f => `
            <div class="relative group">
                <img src="/eventsphere2/uploads/${f.ruta_foto}"
                     alt="${f.descripcion || 'Foto del evento'}"
                     class="w-full h-48 object-cover rounded-lg shadow hover:shadow-xl transition">
                <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-60 text-white p-2 rounded-b-lg opacity-0 group-hover:opacity-100 transition">
                    <p class="text-xs">${f.nombre}</p>
                    ${f.descripcion ? `<p class="text-xs opacity-75">${f.descripcion}</p>` : ''}
                </div>
            </div>
        `).join('');
    }
}

// Subir foto
document.getElementById('form-foto').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const input = document.getElementById('input-foto');
    const descripcion = document.getElementById('descripcion-foto').value;
    
    if (!input.files || input.files.length === 0) {
        showToast('Selecciona una foto', 'error');
        return;
    }
    
    const formData = new FormData();
    formData.append('foto', input.files[0]);
    formData.append('evento_id', eventoId);
    formData.append('usuario_id', usuarioActual.id);
    formData.append('descripcion', descripcion);
    
    showToast('Subiendo foto...', 'info');
    
    try {
        const response = await fetch(`${API_URL}/fotos/index.php`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('¡Foto subida exitosamente!', 'success');
            input.value = '';
            document.getElementById('descripcion-foto').value = '';
            await cargarFotos();
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        showToast('Error subiendo foto', 'error');
    }
});

// ==================== COMPRA DE BOLETOS ====================
function abrirModalCompra() {
    if (eventoActual.organizador_id === usuarioActual.id) {
        showToast('❌ No puedes comprar boletos de tu propio evento', 'error');
        return;
    }
    
    document.getElementById('modal-compra').classList.remove('hidden');
    document.getElementById('precio-unitario').textContent = formatPrice(eventoActual.precio_boleto);
    actualizarTotal();
}

window.verEstadisticas = () => {
    redirect('mi-perfil.html');
};

document.getElementById('btn-cancelar').addEventListener('click', () => {
    document.getElementById('modal-compra').classList.add('hidden');
});

document.getElementById('cantidad').addEventListener('input', actualizarTotal);

document.getElementById('metodo-pago').addEventListener('change', (e) => {
    const tarjeta = document.getElementById('datos-tarjeta');
    const paypal = document.getElementById('datos-paypal');
    if (e.target.value === 'paypal') {
        tarjeta.classList.add('hidden');
        paypal.classList.remove('hidden');
    } else {
        tarjeta.classList.remove('hidden');
        paypal.classList.add('hidden');
    }
});

function actualizarTotal() {
    const cantidad = parseInt(document.getElementById('cantidad').value);
    const precio = parseFloat(eventoActual.precio_boleto);
    document.getElementById('precio-total').textContent = formatPrice(cantidad * precio);
}

document.getElementById('form-compra').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    if (eventoActual.organizador_id === usuarioActual.id) {
        showToast('❌ No puedes comprar boletos de tu propio evento', 'error');
        document.getElementById('modal-compra').classList.add('hidden');
        return;
    }
    
    const cantidad = parseInt(document.getElementById('cantidad').value);
    const metodoPago = document.getElementById('metodo-pago').value;
    
    if (metodoPago === 'tarjeta') {
        const numeroTarjeta = document.getElementById('numero-tarjeta').value;
        const vencimiento = document.getElementById('vencimiento').value;
        const cvv = document.getElementById('cvv').value;
        
        if (!isValidCard(numeroTarjeta)) {
            showToast('Número de tarjeta inválido', 'error');
            return;
        }
        if (!isValidExpiry(vencimiento)) {
            showToast('Fecha de vencimiento inválida', 'error');
            return;
        }
        if (!isValidCVV(cvv)) {
            showToast('CVV inválido', 'error');
            return;
        }
    } else if (metodoPago === 'paypal') {
        const emailPaypal = document.getElementById('email-paypal').value;
        if (!isValidEmail(emailPaypal)) {
            showToast('Email de PayPal inválido', 'error');
            return;
        }
    }
    
    showToast('Procesando compra...', 'info');
    
    try {
        const result = await realizarCompra(eventoActual.id, cantidad, metodoPago);
        showToast('¡Compra exitosa! Revisa tus boletos', 'success');
        document.getElementById('modal-compra').classList.add('hidden');
        setTimeout(() => redirect('mis-boletos.html'), 2000);
    } catch (error) {
        showToast(error.message, 'error');
    }
});

document.getElementById('btn-logout').addEventListener('click', logout);

// ==================== ELIMINAR EVENTO ====================
window.eliminarEvento = async () => {
    const confirmacion = confirm('⚠️ ¿Estás seguro de eliminar este evento?\n\nEsta acción NO se puede deshacer y eliminará:\n- Todos los boletos vendidos\n- Todas las reseñas\n- Todos los mensajes del chat\n- Todas las fotos subidas');
    
    if (!confirmacion) return;
    
    showToast('Eliminando evento...', 'info');
    
    try {
        const response = await fetch(`${API_URL}/eventos/delete.php`, {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({
                evento_id: eventoId,
                usuario_id: usuarioActual.id
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('✅ Evento eliminado exitosamente', 'success');
            setTimeout(() => redirect('mi-perfil.html'), 1500);
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error eliminando evento:', error);
        showToast('Error al eliminar el evento', 'error');
    }
};

// Limpiar intervalo al salir
window.addEventListener('beforeunload', () => {
    if (chatInterval) clearInterval(chatInterval);
});
