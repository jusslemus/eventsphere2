<template>
  <div class="bg-gray-50">
    <nav class="bg-white shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <router-link to="/" class="text-2xl font-bold text-indigo-600">📅 EventSphere</router-link>
          </div>
          <div class="flex items-center space-x-4">
            <router-link to="/eventos" class="text-gray-700 hover:text-indigo-600">Explorar</router-link>
            <router-link to="/mis-boletos" class="text-gray-700 hover:text-indigo-600">Mis Boletos</router-link>
            <button id="btn-logout" class="text-red-600 hover:text-red-700">Cerrar Sesión</button>
          </div>
        </div>
      </div>
    </nav>

    <div id="loading" class="text-center py-12">
      <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
      <p class="mt-4 text-gray-600">Cargando evento...</p>
    </div>

    <div id="main-content" class="hidden max-w-7xl mx-auto px-4 py-8">
      <p class="text-gray-600">Detalle del evento se cargará aquí (usar js/evento-detalle.js)</p>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

onMounted(() => {
  // Load evento-detalle.js logic here or migrate it to Vue
  console.log('Evento ID:', route.params.id)
  
  const btnLogout = document.getElementById('btn-logout')
  if (btnLogout && window.logout) {
    btnLogout.addEventListener('click', window.logout)
  }
})
</script>
