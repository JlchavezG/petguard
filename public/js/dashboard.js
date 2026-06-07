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

// Cerrar sesión
function logout() {
    if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
        window.location.href = '/petguard/public/login.php';
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

// === FUNCIONES DE SUBIDA DE IMÁGENES ===
function initImageUpload(zoneId, inputId, previewId, fileInfoId, errorId, existingUrl) {
    const zone = document.getElementById(zoneId);
    const input = document.getElementById(inputId);
    const previewContainer = document.getElementById(previewId);
    const fileInfo = document.getElementById(fileInfoId);
    const errorMsg = document.getElementById(errorId);
    
    if (!zone || !input) return;
    
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    const maxSize = 5 * 1024 * 1024;
    
    function validateAndPreview(file) {
        if (errorMsg) errorMsg.classList.remove('visible');
        
        if (!allowedTypes.includes(file.type)) {
            if (errorMsg) {
                errorMsg.textContent = 'Tipo no permitido. Solo JPG, PNG o WEBP.';
                errorMsg.classList.add('visible');
            }
            return false;
        }
        
        if (file.size > maxSize) {
            if (errorMsg) {
                errorMsg.textContent = 'Archivo muy grande. Máximo 5MB.';
                errorMsg.classList.add('visible');
            }
            return false;
        }
        
        if (fileInfo) {
            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            fileInfo.textContent = '✓ ' + file.name + ' (' + sizeMB + ' MB)';
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            if (previewContainer) {
                const img = previewContainer.querySelector('img') || document.createElement('img');
                img.src = e.target.result;
                img.className = 'upload-preview-image';
                if (!previewContainer.querySelector('img')) {
                    previewContainer.appendChild(img);
                }
                previewContainer.classList.add('active');
            }
        };
        reader.readAsDataURL(file);
        
        return true;
    }
    
    function removeImage() {
        input.value = '';
        if (fileInfo) fileInfo.textContent = '';
        if (previewContainer) {
            previewContainer.classList.remove('active');
            const img = previewContainer.querySelector('img');
            if (img) img.remove();
        }
        if (errorMsg) errorMsg.classList.remove('visible');
    }
    
    zone.addEventListener('click', function(e) {
        if (e.target.classList.contains('upload-remove-btn')) return;
        input.click();
    });
    
    zone.addEventListener('dragover', function(e) {
        e.preventDefault();
        zone.classList.add('dragover');
    });
    
    zone.addEventListener('dragleave', function() {
        zone.classList.remove('dragover');
    });
    
    zone.addEventListener('drop', function(e) {
        e.preventDefault();
        zone.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            input.files = files;
            validateAndPreview(files[0]);
        }
    });
    
    input.addEventListener('change', function() {
        if (this.files.length > 0) {
            validateAndPreview(this.files[0]);
        }
    });
    
    // Botón remover
    const removeBtn = previewContainer ? previewContainer.querySelector('.upload-remove-btn') : null;
    if (removeBtn) {
        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            removeImage();
        });
    }
    
    // Mostrar imagen existente si hay
    if (existingUrl && previewContainer) {
        const img = document.createElement('img');
        img.src = existingUrl;
        img.className = 'upload-preview-image';
        previewContainer.appendChild(img);
        previewContainer.classList.add('active');
    }
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
    
    // Inicializar zonas de subida de imágenes
    initImageUpload('create-foto-zone', 'create-foto-file', 'create-foto-preview', 'create-foto-info', 'create-foto-error', null);
    initImageUpload('edit-foto-zone', 'edit-foto-file', 'edit-foto-preview', 'edit-foto-info', 'edit-foto-error', null);
});