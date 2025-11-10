// EventSphere - Lógica de Eventos

// Cargar todos los eventos
const cargarEventos = async (search = '', categoria = '') => {
    const container = document.getElementById('eventos-container');
    const loading = document.getElementById('loading');
    const noEventos = document.getElementById('no-eventos');

    if (loading) loading.classList.remove('hidden');
    if (container) container.innerHTML = '';
    if (noEventos) noEventos.classList.add('hidden');

    try {
        let url = `${API_URL}/eventos/index.php?`;
        if (search) url += `search=${encodeURIComponent(search)}&`;
        if (categoria) url += `categoria=${categoria}&`;

        const response = await fetch(url, {
            headers: getHeaders()
        });

        const data = await response.json();

        if (loading) loading.classList.add('hidden');

        if (data.success && data.eventos.length > 0) {
            renderEventos(data.eventos, container);
        } else {
            if (noEventos) noEventos.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error cargando eventos:', error);
        if (loading) loading.classList.add('hidden');
        showToast('Error cargando eventos', 'error');
    }
};

// Renderizar lista de eventos
const renderEventos = (eventos, container) => {
    container.innerHTML = eventos.map(evento => `
        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition event-card">
            <img src="${API_URL}/uploads/${evento.imagen_portada}"
                 alt="${evento.titulo}"
                 class="w-full h-48 object-cover"
                 onerror="this.src='assets/images/default-event.jpg'">
            <div class="p-6">
                <span class="badge bg-indigo-100 text-indigo-600">${evento.categoria || 'General'}</span>
                <h3 class="text-xl font-bold text-gray-800 mt-3 mb-2">${evento.titulo}</h3>
                <p class="text-gray-600 text-sm mb-2">${evento.descripcion.substring(0, 100)}...</p>
                <div class="flex items-center text-gray-500 text-sm mb-4">
                    <span>📅 ${formatDate(evento.fecha_evento)}</span>
                </div>
                <div class="flex items-center text-gray-500 text-sm mb-4">
                    <span>📍 ${evento.ubicacion}</span>
                </div>
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-2xl font-bold text-indigo-600">${formatPrice(evento.precio_boleto)}</span>
                        <p class="text-xs text-gray-500">${evento.boletos_disponibles} disponibles</p>
                    </div>
                    <a href="evento-detalle.html?id=${evento.id}"
                       class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                        Ver Detalles
                    </a>
                </div>
            </div>
        </div>
    `).join('');
};

// Obtener detalle de un evento
const obtenerEvento = async (id) => {
    try {
        const response = await fetch(`${API_URL}/eventos/detail.php?id=${id}`, {
            headers: getHeaders()
        });

        const data = await response.json();

        if (data.success) {
            return data.evento;
        } else {
            throw new Error(data.message || 'Evento no encontrado');
        }
    } catch (error) {
        console.error('Error obteniendo evento:', error);
        showToast('Error cargando evento', 'error');
        return null;
    }
};

// Renderizar detalle completo del evento - ACTUALIZADO CON VALIDACIÓN DE ORGANIZADOR
const renderDetalleEvento = (evento, container, usuarioActual) => {
    // Verificar si el usuario actual es el organizador del evento
    const esOrganizador = usuarioActual && evento.organizador_id === usuarioActual.id;
    
    container.innerHTML = `
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <img src="${API_URL}/uploads/${evento.imagen_portada}"
                 alt="${evento.titulo}"
                 class="w-full h-96 object-cover"
                 onerror="this.src='assets/images/default-event.jpg'">

            <div class="p-8">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <span class="badge bg-indigo-100 text-indigo-600 text-sm">${evento.categoria || 'General'}</span>
                        <h1 class="text-4xl font-bold text-gray-800 mt-3">${evento.titulo}</h1>
                        ${esOrganizador ? '<p class="text-sm text-indigo-600 mt-2 font-semibold">👤 Eres el organizador de este evento</p>' : ''}
                    </div>
                    <div class="text-right">
                        <p class="text-4xl font-bold text-indigo-600">${formatPrice(evento.precio_boleto)}</p>
                        <p class="text-sm text-gray-500">${evento.boletos_disponibles} boletos disponibles</p>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <div class="flex items-center space-x-3 text-gray-700">
                        <span class="text-2xl">📅</span>
                        <div>
                            <p class="font-semibold">Fecha y Hora</p>
                            <p class="text-sm">${formatDate(evento.fecha_evento)}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 text-gray-700">
                        <span class="text-2xl">📍</span>
                        <div>
                            <p class="font-semibold">Ubicación</p>
                            <p class="text-sm">${evento.ubicacion}</p>
                            ${evento.direccion ? `<p class="text-xs text-gray-500">${evento.direccion}</p>` : ''}
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Descripción</h3>
                    <p class="text-gray-600 leading-relaxed">${evento.descripcion}</p>
                </div>

                ${renderBotonAccion(evento, esOrganizador)}
            </div>
        </div>
    `;
};

// Nueva función para renderizar el botón correcto según el tipo de usuario
const renderBotonAccion = (evento, esOrganizador) => {
    // Si es el organizador
    if (esOrganizador) {
        return `
            <div class="space-y-4">
                <button onclick="verEstadisticas()"
                        class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white py-4 rounded-xl font-bold text-lg hover:from-purple-700 hover:to-indigo-700 transition">
                    📊 Ver Mis Estadísticas
                </button>
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                    <p class="text-sm text-blue-700">
                        💡 <strong>Eres el organizador:</strong> Puedes ver estadísticas, validar boletos y gestionar este evento desde tu perfil.
                    </p>
                </div>
            </div>
        `;
    }
    
    // Si NO es el organizador
    if (evento.boletos_disponibles > 0) {
        return `
            <button onclick="comprarBoleto(${evento.id})"
                    class="w-full bg-indigo-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-indigo-700 transition">
                🎟️ Comprar Boletos
            </button>
        `;
    } else {
        return `
            <div class="bg-red-50 text-red-600 py-4 rounded-xl text-center font-bold">
                ⚠️ Boletos Agotados
            </div>
        `;
    }
};

// Crear nuevo evento
const crearEvento = async (formData) => {
    try {
        const response = await fetch(`${API_URL}/eventos/create.php`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showToast('¡Evento creado exitosamente!', 'success');
            setTimeout(() => {
                redirect('eventos.html');
            }, 1500);
        } else {
            throw new Error(data.message || 'Error creando evento');
        }
    } catch (error) {
        console.error('Error creando evento:', error);
        showToast(error.message, 'error');
        throw error;
    }
};

// Función para comprar boletos
const realizarCompra = async (eventoId, cantidad, metodoPago) => {
    const usuario = getUsuarioActual();

    if (!usuario) {
        showToast('Debes iniciar sesión', 'error');
        redirect('login.html');
        return;
    }

    try {
        const response = await fetch(`${API_URL}/boletos/comprar.php`, {
            method: 'POST',
            headers: getHeaders(),
            body: JSON.stringify({
                usuario_id: usuario.id,
                evento_id: eventoId,
                cantidad: cantidad,
                metodo_pago: metodoPago
            })
        });

        const data = await response.json();

        if (data.success) {
            return data;
        } else {
            throw new Error(data.message || 'Error en la compra');
        }
    } catch (error) {
        console.error('Error en compra:', error);
        throw error;
    }
};
