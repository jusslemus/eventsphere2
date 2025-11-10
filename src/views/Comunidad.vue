<template>
  <div class="bg-gray-50">
    <!-- Navbar simplified: use existing legacy navbar markup if needed inside the app -->
    <nav class="bg-white shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <a href="/" class="text-2xl font-bold text-indigo-600">EventSphere</a>
          </div>
          <div class="flex items-center space-x-4">
            <a href="/eventos" class="text-gray-700 hover:text-indigo-600">Explorar</a>
            <button id="btn-logout" class="text-red-600 hover:text-red-700">Cerrar Sesión</button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Chat Comunidad -->
    <div class="max-w-5xl mx-auto px-4 py-12">
      <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white p-6">
          <h1 id="evento-titulo" class="text-2xl font-bold mb-2">Comunidad del Evento</h1>
          <p class="opacity-90">💬 <span id="miembros-count">0</span> miembros conectados</p>
        </div>

        <!-- Mensajes -->
        <div id="mensajes-container" class="h-96 overflow-y-auto p-6 space-y-4 bg-gray-50">
          <div class="text-center text-gray-500 py-8">
            <div class="text-4xl mb-2">💬</div>
            <p>Cargando mensajes...</p>
          </div>
        </div>

        <!-- Input Mensaje -->
        <div class="border-t p-4">
          <form id="form-mensaje" class="flex space-x-4">
            <input type="text" id="mensaje-input" placeholder="Escribe un mensaje..." required
                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 font-semibold">
              Enviar
            </button>
          </form>
        </div>
      </div>

      <!-- Galería de Fotos -->
      <div class="mt-8 bg-white rounded-2xl shadow-xl p-8">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-800">📸 Galería del Evento</h2>
          <button id="btn-subir-foto" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
            Subir Foto
          </button>
        </div>

        <div id="galeria-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <div class="col-span-full text-center text-gray-500 py-8">
            <div class="text-4xl mb-2">🖼️</div>
            <p>No hay fotos todavía. ¡Sé el primero en compartir!</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Subir Foto -->
    <div id="modal-foto" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
        <h3 class="text-2xl font-bold text-gray-800 mb-6">Subir Foto</h3>
        <form id="form-foto" class="space-y-4">
          <div>
            <label class="block text-gray-700 font-medium mb-2">Seleccionar Imagen</label>
            <input type="file" id="foto-input" accept="image/*" required
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
          </div>

          <div>
            <label class="block text-gray-700 font-medium mb-2">Descripción (opcional)</label>
            <textarea id="foto-descripcion" rows="3"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                      placeholder="Describe tu foto..."></textarea>
          </div>

          <div class="flex space-x-4">
            <button type="submit" class="flex-1 bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700">
              Subir
            </button>
            <button type="button" id="btn-cancelar-foto" class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-400">
              Cancelar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue'

let mensajesInterval = null

onMounted(() => {
  // Use legacy global helpers from /js/config.js and /js/auth.js which are loaded in index.html
  if (window && typeof window.verificarAuth === 'function') {
    window.verificarAuth()
  }

  const usuario = (window.getUsuarioActual && window.getUsuarioActual()) || null
  const eventoId = (window.getUrlParameter && window.getUrlParameter('evento')) || null

  if (!eventoId) {
    if (window.redirect) window.redirect('/eventos')
    return
  }

  // Load comunidad info
  ;(async () => {
    try {
      const response = await fetch(`${window.API_URL || '/api'}/comunidades/info.php?evento=${eventoId}`, {
        headers: (window.getHeaders && window.getHeaders()) || { 'Content-Type': 'application/json' }
      })
      const data = await response.json()
      if (data.success) {
        const tituloEl = document.getElementById('evento-titulo')
        const miembrosEl = document.getElementById('miembros-count')
        if (tituloEl) tituloEl.textContent = `Comunidad: ${data.evento.titulo}`
        if (miembrosEl) miembrosEl.textContent = data.miembros_count || 0
      }
    } catch (error) {
      console.error('Error cargando comunidad:', error)
    }
  })()

  const cargarMensajes = async () => {
    try {
      const response = await fetch(`${window.API_URL || '/api'}/comunidades/mensajes.php?evento=${eventoId}`, {
        headers: (window.getHeaders && window.getHeaders()) || { 'Content-Type': 'application/json' }
      })
      const data = await response.json()
      if (data.success && data.mensajes && data.mensajes.length > 0) {
        renderMensajes(data.mensajes)
      } else {
        const container = document.getElementById('mensajes-container')
        if (container) container.innerHTML = `
          <div class="text-center text-gray-500 py-8">
            <div class="text-4xl mb-2">💬</div>
            <p>No hay mensajes. ¡Inicia la conversación!</p>
          </div>`
      }
    } catch (error) {
      console.error('Error cargando mensajes:', error)
    }
  }

  function renderMensajes(mensajes) {
    const container = document.getElementById('mensajes-container')
    if (!container) return
    container.innerHTML = mensajes
      .map((msg) => {
        const esMio = usuario && msg.usuario_id === usuario.id
        return `
          <div class="flex ${esMio ? 'justify-end' : 'justify-start'}">
            <div class="max-w-xs lg:max-w-md">
              ${!esMio ? `<p class="text-xs text-gray-500 mb-1">${msg.usuario_nombre}</p>` : ''}
              <div class="${esMio ? 'bg-indigo-600 text-white' : 'bg-white border border-gray-300'} rounded-lg px-4 py-2">
                <p>${msg.mensaje}</p>
                <p class="text-xs ${esMio ? 'text-indigo-200' : 'text-gray-400'} mt-1">
                  ${new Date(msg.fecha_envio).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' })}
                </p>
              </div>
            </div>
          </div>`
      })
      .join('')

    container.scrollTop = container.scrollHeight
  }

  // Form submit
  const formMensaje = document.getElementById('form-mensaje')
  if (formMensaje) {
    formMensaje.addEventListener('submit', async (e) => {
      e.preventDefault()
      const mensajeInput = document.getElementById('mensaje-input')
      const mensaje = mensajeInput ? mensajeInput.value.trim() : ''
      if (!mensaje) return
      try {
        const response = await fetch(`${window.API_URL || '/api'}/comunidades/enviar.php`, {
          method: 'POST',
          headers: (window.getHeaders && window.getHeaders()) || { 'Content-Type': 'application/json' },
          body: JSON.stringify({ evento_id: eventoId, mensaje })
        })
        const data = await response.json()
        if (data.success) {
          if (mensajeInput) mensajeInput.value = ''
          cargarMensajes()
        }
      } catch (error) {
        if (window.showToast) window.showToast('Error enviando mensaje', 'error')
      }
    })
  }

  // Subir foto modal logic
  const btnSubir = document.getElementById('btn-subir-foto')
  const modalFoto = document.getElementById('modal-foto')
  const btnCancelarFoto = document.getElementById('btn-cancelar-foto')
  const formFoto = document.getElementById('form-foto')

  if (btnSubir && modalFoto) btnSubir.addEventListener('click', () => modalFoto.classList.remove('hidden'))
  if (btnCancelarFoto && modalFoto) btnCancelarFoto.addEventListener('click', () => modalFoto.classList.add('hidden'))
  if (formFoto) {
    formFoto.addEventListener('submit', (e) => {
      e.preventDefault()
      if (window.showToast) window.showToast('Foto subida exitosamente', 'success')
      if (modalFoto) modalFoto.classList.add('hidden')
      formFoto.reset()
    })
  }

  // Logout button
  const btnLogout = document.getElementById('btn-logout')
  if (btnLogout && window.logout) btnLogout.addEventListener('click', window.logout)

  // Start periodic messages loader
  cargarMensajes()
  mensajesInterval = setInterval(cargarMensajes, 5000)
})

onUnmounted(() => {
  if (mensajesInterval) clearInterval(mensajesInterval)
})
</script>

<style scoped>
/* keep existing page styles in global css files; add small adjustments here if needed */
</style>
