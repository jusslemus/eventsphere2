<template>
  <div class="bg-gray-50 min-h-screen">
    <nav class="navbar-eventsphere fixed w-full top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          <router-link to="/" class="logo">EventSphere</router-link>
          <div class="flex items-center space-x-4" id="navbarMenu"></div>
        </div>
      </div>
    </nav>

    <div class="pt-16">
      <div class="hero-eventsphere">
        <div class="hero-content">
          <h1 class="hero-title">
            <span class="icon-sparkles">✨</span>
            EventSphere
          </h1>
          <p class="hero-subtitle">Descubre, conecta y vive experiencias inolvidables</p>
          
          <div class="hero-buttons">
            <router-link to="/register" class="btn-hero-primary">
              👤 Únete Gratis
            </router-link>
            <router-link to="/login" class="btn-hero-secondary">
              🎯 Iniciar Sesión
            </router-link>
          </div>

          <div class="category-pills">
            <button class="category-pill">🎵 Conciertos</button>
            <button class="category-pill">⚽ Deportes</button>
            <button class="category-pill">🎤 Conferencias</button>
            <button class="category-pill">🎨 Arte</button>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="text-center mb-12">
        <h2 class="text-4xl font-bold text-gray-800 mb-4">
          🎁 ¿Por qué unirte a EventSphere?
        </h2>
        <p class="text-xl text-gray-600">
          Crea tu cuenta gratis y desbloquea todas las funciones
        </p>
      </div>

      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">🎫</div>
          <h3 class="feature-title">Compra Boletos</h3>
          <p class="feature-description">
            Accede a eventos exclusivos con tickets digitales y códigos QR
          </p>
        </div>

        <div class="feature-card">
          <div class="feature-icon">➕</div>
          <h3 class="feature-title">Crea Eventos</h3>
          <p class="feature-description">
            Organiza tus propios eventos y gestiona asistentes fácilmente
          </p>
        </div>

        <div class="feature-card">
          <div class="feature-icon">💬</div>
          <h3 class="feature-title">Interactúa</h3>
          <p class="feature-description">
            Chatea con otros asistentes y comparte fotos de los eventos
          </p>
        </div>

        <div class="feature-card">
          <div class="feature-icon">📱</div>
          <h3 class="feature-title">Valida Entradas</h3>
          <p class="feature-description">
            Escanea códigos QR en tiempo real para validar asistencia
          </p>
        </div>
      </div>
    </div>

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
  const usuario = JSON.parse(localStorage.getItem('usuario') || '{}')
  const navbarMenu = document.getElementById('navbarMenu')

  if (usuario && usuario.id && navbarMenu) {
    navbarMenu.innerHTML = `
      <a href="/eventos">Eventos</a>
      <a href="/mis-boletos">Mis Boletos</a>
      <a href="/crear-evento">Crear Evento</a>
      <a href="/mi-perfil">Mi Perfil</a>
      <button id="btn-logout" style="background: #ef4444; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 600;">
        Cerrar Sesión
      </button>
    `
    
    document.getElementById('btn-logout')?.addEventListener('click', () => {
      if (window.logout) window.logout()
    })
  } else if (navbarMenu) {
    navbarMenu.innerHTML = `
      <a href="/eventos">Eventos</a>
      <a href="/crear-evento">Crear Evento</a>
      <a href="/mi-perfil">Mi Perfil</a>
      <a href="/login">Iniciar Sesión</a>
      <a href="/register" class="btn-registrar">Registrarse</a>
    `
  }
})
</script>
