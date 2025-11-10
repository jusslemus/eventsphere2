<template>
  <div>
    <!-- Navbar -->
    <nav class="navbar-eventsphere fixed w-full top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          <router-link to="/" class="logo">EventSphere</router-link>
          <div class="flex items-center space-x-4" id="navbarMenu">
            <!-- Links dinámicos se cargarán aquí -->
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <div class="pt-16">
      <div class="hero-eventsphere" style="min-height: 350px;">
        <div class="hero-content">
          <h1 class="text-5xl font-bold mb-4">
            Descubre Eventos Increíbles
          </h1>
          <p class="text-xl mb-6">
            Encuentra conciertos, conferencias, deportes y mucho más
          </p>
        </div>
      </div>
    </div>

    <!-- Sección de Filtros -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="search-section" style="margin-top: -3rem;">
        <div class="search-title">
          🔍 Buscar eventos
        </div>
        
        <div class="flex flex-col md:flex-row gap-4">
          <input type="text" 
                 id="search-input" 
                 placeholder="¿Qué evento buscas?"
                 class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">

          <select id="filter-categoria" 
                  class="px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            <option value="">Todas las categorías</option>
            <option value="1">🎸 Conciertos</option>
            <option value="2">🎤 Conferencias</option>
            <option value="3">🏆 Deportes</option>
            <option value="4">🛠️ Talleres</option>
            <option value="5">🎉 Fiestas</option>
            <option value="6">🍕 Gastronómico</option>
          </select>

          <button id="btn-buscar" class="btn-hero-primary">
            Buscar 🔍
          </button>
        </div>
      </div>
    </div>

    <!-- Lista de Eventos -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="flex justify-between items-center mb-8">
        <h2 class="text-3xl font-bold text-gray-800">
          Todos los Eventos
        </h2>
        <div class="text-gray-600">
          <span id="eventos-count">0</span> eventos encontrados
        </div>
      </div>

      <div id="eventos-container" class="eventos-grid">
        <!-- Se llena dinámicamente -->
      </div>

      <div id="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
        <p class="mt-4 text-gray-600 font-semibold">Cargando eventos...</p>
      </div>

      <div id="no-eventos" class="hidden text-center py-12">
        <div class="text-6xl mb-4">📭</div>
        <p class="text-gray-500 text-xl mb-2">No se encontraron eventos</p>
        <p class="text-gray-400">Intenta con otros filtros o crea tu propio evento</p>
        <router-link to="/crear-evento" class="inline-block mt-6 btn-hero-primary">
          Crear Evento
        </router-link>
      </div>
    </div>

    <!-- Footer -->
    <footer class="footer-eventsphere">
      <div class="max-w-7xl mx-auto">
        <p class="footer-title">EventSphere 2025</p>
        <p class="footer-subtitle">Transformando eventos en experiencias colaborativas</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'

onMounted(() => {
  if (window.verificarAuth) window.verificarAuth()

  // Navbar dinámico
  const usuario = JSON.parse(localStorage.getItem('usuario') || '{}')
  const navbarMenu = document.getElementById('navbarMenu')

  if (usuario && usuario.id) {
    navbarMenu.innerHTML = `
      <a href="/eventos" style="color: #fbbf24; font-weight: 600;">Eventos</a>
      <a href="/mis-boletos">Mis Boletos</a>
      <a href="/crear-evento">Crear Evento</a>
      <a href="/mi-perfil">Mi Perfil</a>
      <button id="btn-logout" style="background: #ef4444; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 600;">
        Cerrar Sesión
      </button>
    `
  } else {
    navbarMenu.innerHTML = `
      <a href="/eventos" style="color: #fbbf24; font-weight: 600;">Eventos</a>
      <a href="/crear-evento">Crear Evento</a>
      <a href="/mi-perfil">Mi Perfil</a>
      <a href="/login">Iniciar Sesión</a>
      <a href="/register" class="btn-registrar">Registrarse</a>
    `
  }

  // Cargar eventos usando la función global (si existe en js/eventos.js)
  if (window.cargarEventos) {
    const originalCargarEventos = window.cargarEventos
    window.cargarEventos = async function(search, categoria) {
      await originalCargarEventos(search, categoria)
      
      const eventosContainer = document.getElementById('eventos-container')
      const eventosCount = eventosContainer ? eventosContainer.children.length : 0
      const countEl = document.getElementById('eventos-count')
      if (countEl) countEl.textContent = eventosCount
    }

    window.cargarEventos()
  }

  // Buscar eventos
  const btnBuscar = document.getElementById('btn-buscar')
  const searchInput = document.getElementById('search-input')
  const filterCategoria = document.getElementById('filter-categoria')

  if (btnBuscar) {
    btnBuscar.addEventListener('click', () => {
      const search = searchInput ? searchInput.value : ''
      const categoria = filterCategoria ? filterCategoria.value : ''
      if (window.cargarEventos) window.cargarEventos(search, categoria)
    })
  }

  if (searchInput) {
    searchInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        btnBuscar?.click()
      }
    })
  }

  // Logout
  document.addEventListener('click', (e) => {
    if (e.target.id === 'btn-logout') {
      if (confirm('¿Estás seguro que deseas cerrar sesión?')) {
        if (window.logout) window.logout()
      }
    }
  })

  // Filtros desde URL
  const urlParams = new URLSearchParams(window.location.search)
  const categoriaParam = urlParams.get('categoria')
  if (categoriaParam && filterCategoria) {
    const categoriaMap = {
      'Conciertos': '1',
      'Deportes': '3',
      'Conferencias': '2',
      'Arte': '6'
    }
    const categoriaId = categoriaMap[categoriaParam]
    if (categoriaId) {
      filterCategoria.value = categoriaId
      if (window.cargarEventos) window.cargarEventos('', categoriaId)
    }
  }
})
</script>

<style scoped>
/* Estilos adicionales si son necesarios */
</style>
