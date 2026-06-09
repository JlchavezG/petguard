/**
 * ============================================================
 * PETGUARD - Dashboard JavaScript
 * ============================================================
 */

// Función para mostrar sección
function showSection(sectionName) {
    document.querySelectorAll('.content-section').forEach(section => {
        section.classList.remove('active');
    });

    const targetSection = document.getElementById('section-' + sectionName);
    if (targetSection) {
        targetSection.classList.add('active');
    }

    document.querySelectorAll('.sidebar-icon').forEach(icon => {
        icon.classList.remove('active');
    });

    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });

    const sidebarIcons = document.querySelectorAll('.sidebar-icon');
    const sectionMap = {
        'dashboard': 0,
        'usuarios': 1,
        'voluntarios': 2,
        'veterinarios': 3,
        'mascotas': 4,
        'donaciones': 5
    };

    if (sectionMap[sectionName] !== undefined && sidebarIcons[sectionMap[sectionName]]) {
        sidebarIcons[sectionMap[sectionName]].classList.add('active');
    }

    const navItems = document.querySelectorAll('.nav-item');
    const navMap = {
        'dashboard': 0,
        'usuarios': 1,
        'voluntarios': 2,
        'veterinarios': 3,
        'mascotas': 4,
        'donaciones': 5
    };

    if (navMap[sectionName] !== undefined && navItems[navMap[sectionName]]) {
        navItems[navMap[sectionName]].classList.add('active');
    }

    const url = new URL(window.location);
    url.searchParams.set('section', sectionName);
    window.history.replaceState({}, '', url);
}

// Toggle Sidebar
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('expanded');
    
    const toggleLabel = sidebar.querySelector('.toggle-label');
    if (sidebar.classList.contains('expanded')) {
        toggleLabel.textContent = 'Contraer';
    } else {
        toggleLabel.textContent = 'Expandir';
    }
}

// Cerrar sesión (CORREGIDO: ahora incluye ?logout=1 para destruir la sesión)
function logout() {
    if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
        window.location.href = '/petguard/public/login.php?logout=1';
    }
}

// === FUNCIONES DE MODALES ===
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function openEditModal(userData) {
    document.getElementById('edit-user-id').value = userData.id;
    document.getElementById('edit-nombre').value = userData.nombre;
    document.getElementById('edit-apellido-paterno').value = userData.apellido_paterno;
    document.getElementById('edit-apellido-materno').value = userData.apellido_materno || '';
    document.getElementById('edit-telefono').value = userData.telefono || '';
    document.getElementById('edit-email').value = userData.email;
    
    openModal('modal-edit-user');
}

// Cerrar modal al hacer clic fuera
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        event.target.classList.remove('active');
        document.body.style.overflow = '';
    }
});

// Cerrar modal con tecla Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(modal => {
            modal.classList.remove('active');
        });
        document.body.style.overflow = '';
    }
});

// Al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const section = urlParams.get('section');
    
    if (section && document.getElementById('section-' + section)) {
        showSection(section);
    }
});