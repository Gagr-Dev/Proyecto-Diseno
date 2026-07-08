(() => {
    'use strict'

    // Determinar el tema almacenado o por defecto (oscuro)
    const getStoredTheme = () => localStorage.getItem('theme')
    const setStoredTheme = theme => localStorage.setItem('theme', theme)

    const getPreferredTheme = () => {
        const storedTheme = getStoredTheme()
        if (storedTheme) {
            return storedTheme
        }
        return 'dark'; // Tema predeterminado
    }

    const setTheme = theme => {
        document.documentElement.setAttribute('data-bs-theme', theme)
    }

    // Aplicar tema inmediatamente para evitar parpadeos
    setTheme(getPreferredTheme())

    // Crear y actualizar el botón flotante
    const createThemeToggle = () => {
        // Verificar si ya existe para evitar duplicados
        if (document.getElementById('floatingThemeToggle')) return;

        const btn = document.createElement('button');
        btn.id = 'floatingThemeToggle';
        btn.className = 'btn btn-primary rounded-circle shadow d-flex align-items-center justify-content-center theme-toggle';
        btn.style.cssText = 'position: fixed; bottom: 20px; right: 20px; width: 45px; height: 45px; z-index: 9999; transition: all 0.3s ease;';
        btn.innerHTML = '<i class="bi bi-moon-stars-fill fs-5"></i>';
        
        btn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            setTheme(newTheme);
            setStoredTheme(newTheme);
            updateThemeIcon(newTheme);
        });
        
        document.body.appendChild(btn);
    }

    const updateThemeIcon = (theme) => {
        const btn = document.getElementById('floatingThemeToggle');
        if (btn) {
            const icon = btn.querySelector('i');
            if (icon) {
                if (theme === 'dark') {
                    icon.className = 'bi bi-sun-fill fs-5';
                    btn.setAttribute('title', 'Cambiar a modo claro');
                    btn.classList.remove('btn-dark');
                    btn.classList.add('btn-primary');
                } else {
                    icon.className = 'bi bi-moon-stars-fill fs-5';
                    btn.setAttribute('title', 'Cambiar a modo oscuro');
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-dark');
                }
            }
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        createThemeToggle();
        const currentTheme = getPreferredTheme();
        updateThemeIcon(currentTheme);
    });
})()
