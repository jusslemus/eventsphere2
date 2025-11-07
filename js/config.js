// EventSphere - Configuración Global

// URL base de la API (CAMBIAR según tu servidor)
const API_URL = '/eventsphere2/api';

// Configuración de headers para las peticiones
const getHeaders = () => {
    const token = localStorage.getItem('token');
    const headers = {
        'Content-Type': 'application/json'
    };
    
    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }
    
    return headers;
};

// Función para mostrar notificaciones toast
const showToast = (message, type = 'info') => {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
};

// Función para formatear fechas
const formatDate = (dateString) => {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString('es-ES', options);
};

// Función para formatear precios
const formatPrice = (price) => {
    return `$${parseFloat(price).toFixed(2)}`;
};

// Validar formato de email
const isValidEmail = (email) => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
};

// Validar número de tarjeta (formato básico)
const isValidCard = (cardNumber) => {
    const cleaned = cardNumber.replace(/\s|-/g, '');
    return /^\d{16}$/.test(cleaned);
};

// Validar CVV
const isValidCVV = (cvv) => {
    return /^\d{3,4}$/.test(cvv);
};

// Validar fecha de expiración (MM/YY)
const isValidExpiry = (expiry) => {
    const re = /^(0[1-9]|1[0-2])\/\d{2}$/;
    if (!re.test(expiry)) return false;
    
    const [month, year] = expiry.split('/');
    const now = new Date();
    const currentYear = now.getFullYear() % 100;
    const currentMonth = now.getMonth() + 1;
    
    const expiryYear = parseInt(year);
    const expiryMonth = parseInt(month);
    
    if (expiryYear < currentYear) return false;
    if (expiryYear === currentYear && expiryMonth < currentMonth) return false;
    
    return true;
};

// Obtener parámetros de URL
const getUrlParameter = (name) => {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
};

// Función para redireccionar
const redirect = (url) => {
    window.location.href = url;
};
