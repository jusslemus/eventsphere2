// EventSphere - Sistema de Autenticación

// Verificar si el usuario está autenticado
const verificarAuth = () => {
    const token = localStorage.getItem('token');
    const usuario = localStorage.getItem('usuario');
    
    if (!token || !usuario) {
        if (!window.location.pathname.includes('login.html') && 
            !window.location.pathname.includes('index.html')) {
            redirect('login.html');
        }
        return false;
    }
    return true;
};

// Login
const login = async (email, password) => {
    try {
        const response = await fetch(`${API_URL}/auth/login.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        if (data.success) {
            localStorage.setItem('token', data.token);
            localStorage.setItem('usuario', JSON.stringify(data.usuario));
            
            showToast('¡Bienvenido a EventSphere!', 'success');
            
            setTimeout(() => {
                redirect('eventos.html');
            }, 1000);
        } else {
            throw new Error(data.message || 'Error al iniciar sesión');
        }
    } catch (error) {
        showToast(error.message, 'error');
        throw error;
    }
};

// Registro
const register = async (nombre, apellido, email, password) => {
    try {
        const response = await fetch(`${API_URL}/auth/register.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ nombre, apellido, email, password })
        });

        const data = await response.json();

        if (data.success) {
            showToast('¡Cuenta creada exitosamente!', 'success');
            
            setTimeout(() => {
                login(email, password);
            }, 1500);
        } else {
            throw new Error(data.message || 'Error al registrarse');
        }
    } catch (error) {
        showToast(error.message, 'error');
        throw error;
    }
};

// Logout
const logout = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('usuario');
    showToast('Sesión cerrada', 'info');
    setTimeout(() => {
        redirect('index.html');
    }, 1000);
};

// Obtener usuario actual
const getUsuarioActual = () => {
    const usuarioJSON = localStorage.getItem('usuario');
    return usuarioJSON ? JSON.parse(usuarioJSON) : null;
};

// Event listeners para formularios
document.addEventListener('DOMContentLoaded', () => {
    const formLogin = document.getElementById('form-login');
    if (formLogin) {
        formLogin.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            const errorDiv = document.getElementById('login-error');
            
            if (!isValidEmail(email)) {
                errorDiv.textContent = 'Email inválido';
                errorDiv.classList.remove('hidden');
                return;
            }
            
            errorDiv.classList.add('hidden');
            
            try {
                await login(email, password);
            } catch (error) {
                errorDiv.textContent = error.message;
                errorDiv.classList.remove('hidden');
            }
        });
    }

    const formRegister = document.getElementById('form-register');
    if (formRegister) {
        formRegister.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const nombre = document.getElementById('register-nombre').value;
            const apellido = document.getElementById('register-apellido').value;
            const email = document.getElementById('register-email').value;
            const password = document.getElementById('register-password').value;
            const errorDiv = document.getElementById('register-error');
            const successDiv = document.getElementById('register-success');
            
            if (!nombre || !apellido) {
                errorDiv.textContent = 'Nombre y apellido son requeridos';
                errorDiv.classList.remove('hidden');
                successDiv.classList.add('hidden');
                return;
            }
            
            if (!isValidEmail(email)) {
                errorDiv.textContent = 'Email inválido';
                errorDiv.classList.remove('hidden');
                successDiv.classList.add('hidden');
                return;
            }
            
            if (password.length < 6) {
                errorDiv.textContent = 'La contraseña debe tener al menos 6 caracteres';
                errorDiv.classList.remove('hidden');
                successDiv.classList.add('hidden');
                return;
            }
            
            errorDiv.classList.add('hidden');
            
            try {
                await register(nombre, apellido, email, password);
                successDiv.textContent = 'Cuenta creada exitosamente. Redirigiendo...';
                successDiv.classList.remove('hidden');
            } catch (error) {
                errorDiv.textContent = error.message;
                errorDiv.classList.remove('hidden');
                successDiv.classList.add('hidden');
            }
        });
    }
});