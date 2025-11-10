<template>
  <div class="login-container">
    <router-link to="/" class="back-button">
      ← Volver al inicio
    </router-link>

    <div class="login-card">
      <div class="flex flex-col md:flex-row">
        <div class="login-panel-left md:w-1/2">
          <div style="position: relative; z-index: 10;">
            <h1 class="text-4xl font-bold mb-2 flex items-center gap-2">
              📅 EventSphere
            </h1>
            <p class="text-lg mb-8 opacity-90">
              Transforma eventos en experiencias colaborativas
            </p>
            
            <div class="space-y-4">
              <div class="login-feature">
                <span class="text-3xl">🎫</span>
                <div>
                  <h3 class="font-bold">Compra Boletos</h3>
                  <p class="text-sm opacity-80">Accede con tickets digitales</p>
                </div>
              </div>
              
              <div class="login-feature">
                <span class="text-3xl">💬</span>
                <div>
                  <h3 class="font-bold">Conecta</h3>
                  <p class="text-sm opacity-80">Chat con otros asistentes</p>
                </div>
              </div>
              
              <div class="login-feature">
                <span class="text-3xl">📊</span>
                <div>
                  <h3 class="font-bold">Organiza</h3>
                  <p class="text-sm opacity-80">Crea tus propios eventos</p>
                </div>
              </div>

              <div class="login-feature">
                <span class="text-3xl">✅</span>
                <div>
                  <h3 class="font-bold">Valida</h3>
                  <p class="text-sm opacity-80">Escanea códigos QR</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="p-8 md:p-12 md:w-1/2">
          <div class="mb-8 flex justify-center gap-4">
            <router-link to="/login" class="tab-button">
              Iniciar Sesión
            </router-link>
            <button class="tab-button active">
              Registrarse
            </button>
          </div>

          <form id="form-register" class="space-y-6">
            <div>
              <h2 class="text-3xl font-bold text-gray-800 mb-2">
                Crear cuenta ✨
              </h2>
              <p class="text-gray-600 mb-6">
                Únete a la comunidad de EventSphere
              </p>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-gray-700 font-semibold mb-2">
                  Nombre
                </label>
                <input type="text" id="register-nombre" required
                       class="form-input"
                       placeholder="Juan">
              </div>
              <div>
                <label class="block text-gray-700 font-semibold mb-2">
                  Apellido
                </label>
                <input type="text" id="register-apellido" required
                       class="form-input"
                       placeholder="Pérez">
              </div>
            </div>

            <div>
              <label class="block text-gray-700 font-semibold mb-2">
                📧 Email
              </label>
              <input type="email" id="register-email" required
                     class="form-input"
                     placeholder="tu@email.com">
            </div>

            <div>
              <label class="block text-gray-700 font-semibold mb-2">
                🔒 Contraseña
              </label>
              <input type="password" id="register-password" required minlength="6"
                     class="form-input"
                     placeholder="Mínimo 6 caracteres">
              <p class="text-xs text-gray-500 mt-1">
                La contraseña debe tener al menos 6 caracteres
              </p>
            </div>

            <div id="register-error" class="hidden alert-error"></div>
            <div id="register-success" class="hidden alert-success"></div>

            <button type="submit" class="btn-submit">
              Crear Cuenta 🎉
            </button>

            <p class="text-center text-gray-600 text-sm">
              ¿Ya tienes cuenta? 
              <router-link to="/login" class="text-blue-600 font-semibold hover:underline">
                Inicia sesión aquí
              </router-link>
            </p>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Uses legacy auth.js form handlers loaded in index.html
</script>

<style scoped>
.login-container {
  background: linear-gradient(135deg, #1e3a5f 0%, #2d5a8f 50%, #4a7bb7 100%);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  position: relative;
  overflow: hidden;
}

.login-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(circle at 20% 30%, rgba(74, 144, 226, 0.3) 0%, transparent 50%),
              radial-gradient(circle at 80% 70%, rgba(94, 114, 228, 0.3) 0%, transparent 50%);
  animation: pulse 8s ease-in-out infinite;
}

.login-card {
  background: white;
  border-radius: 1.5rem;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  overflow: hidden;
  max-width: 1000px;
  width: 100%;
  position: relative;
  z-index: 10;
}

.login-panel-left {
  background: linear-gradient(135deg, #1e3a5f 0%, #2d5a8f 100%);
  color: white;
  padding: 3rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

.login-panel-left::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
  animation: float 6s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translate(0, 0); }
  50% { transform: translate(-20px, -20px); }
}

.login-feature {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.75rem;
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border-radius: 0.75rem;
  margin-bottom: 1rem;
  transition: all 0.3s ease;
}

.login-feature:hover {
  background: rgba(255, 255, 255, 0.15);
  transform: translateX(5px);
}

.tab-button {
  padding: 0.75rem 2rem;
  border-radius: 0.75rem;
  font-weight: 600;
  transition: all 0.3s ease;
  background: #f1f5f9;
  color: #64748b;
  text-decoration: none;
  display: inline-block;
}

.tab-button.active {
  background: linear-gradient(135deg, #1e3a5f 0%, #2d5a8f 100%);
  color: white;
  box-shadow: 0 4px 15px rgba(30, 58, 95, 0.3);
}

.form-input {
  width: 100%;
  padding: 0.875rem 1rem;
  border: 2px solid #e2e8f0;
  border-radius: 0.75rem;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: #f8fafc;
}

.form-input:focus {
  outline: none;
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
  background: white;
}

.btn-submit {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #1e3a5f 0%, #2d5a8f 100%);
  color: white;
  border-radius: 0.75rem;
  font-weight: 700;
  font-size: 1.1rem;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(30, 58, 95, 0.3);
}

.btn-submit:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(30, 58, 95, 0.4);
}

.alert-error {
  background: #fee2e2;
  border: 2px solid #ef4444;
  color: #991b1b;
  padding: 1rem;
  border-radius: 0.75rem;
  font-weight: 500;
}

.alert-success {
  background: #d1fae5;
  border: 2px solid #10b981;
  color: #065f46;
  padding: 1rem;
  border-radius: 0.75rem;
  font-weight: 500;
}

.back-button {
  position: absolute;
  top: 2rem;
  left: 2rem;
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(10px);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 0.75rem;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
  z-index: 20;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.back-button:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: translateX(-5px);
}
</style>
