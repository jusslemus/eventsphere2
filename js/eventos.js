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