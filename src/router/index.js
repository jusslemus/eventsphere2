import { createRouter, createWebHistory } from 'vue-router'

// Lazy-load views
const Home = () => import('../views/Home.vue')
const Comunidad = () => import('../views/Comunidad.vue')
const Eventos = () => import('../views/Eventos.vue')
const EventoDetalle = () => import('../views/EventoDetalle.vue')
const Login = () => import('../views/Login.vue')
const Register = () => import('../views/Register.vue')
const CrearEvento = () => import('../views/CrearEvento.vue')
const MiPerfil = () => import('../views/MiPerfil.vue')
const MisBoletos = () => import('../views/MisBoletos.vue')
const ValidarBoleto = () => import('../views/ValidarBoleto.vue')

const routes = [
  { path: '/', name: 'Home', component: Home },
  { path: '/comunidad', name: 'Comunidad', component: Comunidad },
  { path: '/eventos', name: 'Eventos', component: Eventos },
  { path: '/evento/:id', name: 'EventoDetalle', component: EventoDetalle },
  { path: '/login', name: 'Login', component: Login },
  { path: '/register', name: 'Register', component: Register },
  { path: '/crear-evento', name: 'CrearEvento', component: CrearEvento },
  { path: '/mi-perfil', name: 'MiPerfil', component: MiPerfil },
  { path: '/mis-boletos', name: 'MisBoletos', component: MisBoletos },
  { path: '/validar-boleto', name: 'ValidarBoleto', component: ValidarBoleto }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
