/**
 * ============================================================
 * PETGUARD - Búsqueda AJAX de Mascotas con Debounce
 * ============================================================
 */

(function() {
    'use strict';
    
    console.log('✅ Script admin-mascotas.js cargado correctamente');
    
    let searchTimeout = null;
    let currentSearch = '';
    let currentEspecie = '0';
    let currentEstatus = '';
    let currentUrgente = '';
    let currentPage = 1;
    let totalPages = 1;
    let totalMascotas = 0;
    
    function debounce(func, wait) {
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(searchTimeout);
                func(...args);
            };
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(later, wait);
        };
    }
    
    function searchMascotas(searchTerm, especieFilter, estatusFilter, urgenteFilter, page) {
        page = page || 1;
        
        let url = `/petguard/public/api/search-mascotas.php?search=${encodeURIComponent(searchTerm)}&especie=${especieFilter}&estatus=${encodeURIComponent(estatusFilter)}&page=${page}`;
        
        if (urgenteFilter !== '') {
            url += `&urgente=${urgenteFilter}`;
        }
        
        console.log('🔍 Buscando mascotas:', url);
        
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                currentPage = data.current_page;
                totalPages = data.total_pages;
                totalMascotas = data.total;
                updateMascotasTable(data.mascotas, data.count);
                updatePagination();
            } else {
                console.error('❌ Error en la respuesta:', data.error);
            }
        })
        .catch(error => {
            console.error('❌ Error en la búsqueda AJAX:', error);
        });
    }
    
    function updateMascotasTable(mascotas, count) {
        const tableContainer = document.querySelector('#section-mascotas .table-responsive .data-table');
        if (!tableContainer) {
            console.error('❌ No se encontró el contenedor de la tabla de mascotas');
            return;
        }
        
        const sectionTitle = document.querySelector('#section-mascotas .section-title');
        if (sectionTitle) {
            sectionTitle.textContent = `Mascotas Registradas (${count})`;
        }
        
        // Limpiar filas existentes
        const existingRows = tableContainer.querySelectorAll('.data-table-row');
        existingRows.forEach(row => row.remove());
        
        const noResultsMsg = tableContainer.querySelector('div[style*="text-align: center"]');
        if (noResultsMsg) {
            noResultsMsg.remove();
        }
        
        if (mascotas.length === 0) {
            const noResults = document.createElement('div');
            noResults.style.cssText = 'padding: 40px; text-align: center; color: var(--text-secondary); font-weight: 600;';
            noResults.textContent = 'No se encontraron mascotas con los filtros aplicados.';
            tableContainer.appendChild(noResults);
            return;
        }
        
        const iconos = {
            syringe: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2l4 4M7.5 20.5l-4 4M15 5l4 4M2 22l4-4M17 7l-9.5 9.5M9.5 14.5L14.5 19.5"/></svg>',
            scissors: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/><line x1="8.12" y1="8.12" x2="12" y2="12"/></svg>',
            chip: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="1" x2="9" y2="4"/><line x1="15" y1="1" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="23"/><line x1="15" y1="20" x2="15" y2="23"/><line x1="20" y1="9" x2="23" y2="9"/><line x1="20" y1="14" x2="23" y2="14"/><line x1="1" y1="9" x2="4" y2="9"/><line x1="1" y1="14" x2="4" y2="14"/></svg>'
        };
        
        const estatusLabels = {
            'rescatado': 'Rescatado',
            'en_albergue': 'En Albergue',
            'en_adopcion': 'En Adopción',
            'proceso_adopcion': 'En Proceso',
            'adoptado': 'Adoptado',
            'fallecido': 'Fallecido',
            'extraviado': 'Extraviado'
        };
        
        mascotas.forEach(m => {
            const row = document.createElement('div');
            row.className = 'data-table-row';
            row.style.cssText = 'grid-template-columns: 2.5fr 1fr 1fr 1.5fr;';
            
            // Columna Mascota
            const colMascota = document.createElement('div');
            colMascota.className = 'col-mascota';
            
            const userCell = document.createElement('div');
            userCell.className = 'user-cell';
            
            const avatar = document.createElement('div');
            avatar.className = 'user-avatar-small';
            avatar.style.cssText = 'font-size: 20px; display: flex; align-items: center; justify-content: center;';
            
            if (m.foto_principal) {
                const img = document.createElement('img');
                img.src = m.foto_principal;
                img.alt = m.nombre;
                img.style.cssText = 'width: 100%; height: 100%; border-radius: 50%; object-fit: cover;';
                avatar.appendChild(img);
            } else {
                avatar.innerHTML = '🐾';
            }
            
            const info = document.createElement('div');
            const nombre = document.createElement('div');
            nombre.className = 'user-name';
            nombre.textContent = m.nombre;
            
            if (m.urgente == 1) {
                const badge = document.createElement('span');
                badge.className = 'current-user-badge';
                badge.style.cssText = 'color: #ef4444; display: inline-flex; align-items: center; gap: 4px; margin-left: 6px;';
                badge.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg> URGENTE';
                nombre.appendChild(badge);
            }
            
            const email = document.createElement('div');
            email.className = 'user-email';
            email.textContent = `${m.nombre_interno || 'Sin código'} • ${m.especie_nombre || 'N/A'}${m.raza_nombre ? ' - ' + m.raza_nombre : ''}`;
            
            info.appendChild(nombre);
            info.appendChild(email);
            userCell.appendChild(avatar);
            userCell.appendChild(info);
            colMascota.appendChild(userCell);
            
            // Columna Detalles
            const colDetalles = document.createElement('div');
            colDetalles.className = 'col-detalles';
            
            const detallesDiv = document.createElement('div');
            detallesDiv.style.cssText = 'font-size: 12px; color: var(--text-secondary); display: flex; align-items: center; gap: 6px; flex-wrap: wrap;';
            
            const edadText = m.edad_aprox_meses ? `${m.edad_aprox_meses} meses` : '?';
            const pesoText = m.peso_kg ? `${m.peso_kg}kg` : '?';
            
            detallesDiv.innerHTML = `
                <span>${edadText}</span>
                <span>•</span>
                <span>${m.genero}</span>
                <span>•</span>
                <span>${pesoText}</span>
                ${m.vacunado == 1 ? `<span title="Vacunado" style="color: #10b981;">${iconos.syringe}</span>` : ''}
                ${m.esterilizado == 1 ? `<span title="Esterilizado" style="color: #3b82f6;">${iconos.scissors}</span>` : ''}
                ${m.microchip ? `<span title="Microchip: ${m.microchip}" style="color: #8b5cf6;">${iconos.chip}</span>` : ''}
            `;
            
            colDetalles.appendChild(detallesDiv);
            
            // Columna Estatus
            const colEstatus = document.createElement('div');
            colEstatus.className = 'col-estatus';
            
            const formEstatus = document.createElement('form');
            formEstatus.method = 'POST';
            formEstatus.className = 'action-form';
            
            [
                {name: 'action', value: 'change_mascota_status'},
                {name: 'id', value: m.id},
                {name: 'section', value: 'mascotas'}
            ].forEach(input => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = input.name;
                inp.value = input.value;
                formEstatus.appendChild(inp);
            });
            
            const select = document.createElement('select');
            select.name = 'estatus';
            select.className = 'form-select role-select';
            select.onchange = function() {
                if (confirm('¿Cambiar estatus?')) {
                    this.form.submit();
                }
            };
            
            Object.keys(estatusLabels).forEach(key => {
                const option = document.createElement('option');
                option.value = key;
                option.textContent = estatusLabels[key];
                if (m.estatus === key) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
            
            formEstatus.appendChild(select);
            colEstatus.appendChild(formEstatus);
            
            // Columna Acciones
            const colActions = document.createElement('div');
            colActions.className = 'col-actions';
            
            const actionButtons = document.createElement('div');
            actionButtons.className = 'action-buttons';
            
            const btnEditar = document.createElement('button');
            btnEditar.type = 'button';
            btnEditar.className = 'action-btn action-edit';
            btnEditar.title = 'Editar mascota';
            btnEditar.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg> Editar';
            btnEditar.onclick = function() {
                if (typeof openEditMascotaModal === 'function') {
                    openEditMascotaModal(m);
                }
            };
            actionButtons.appendChild(btnEditar);
            
            const formUrgente = document.createElement('form');
            formUrgente.method = 'POST';
            formUrgente.className = 'action-form';
            
            [
                {name: 'action', value: 'toggle_urgente'},
                {name: 'id', value: m.id},
                {name: 'section', value: 'mascotas'}
            ].forEach(input => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = input.name;
                inp.value = input.value;
                formUrgente.appendChild(inp);
            });
            
            const btnUrgente = document.createElement('button');
            btnUrgente.type = 'submit';
            btnUrgente.className = `action-btn ${m.urgente == 1 ? 'action-reject' : 'action-approve'}`;
            btnUrgente.title = m.urgente == 1 ? 'Quitar urgencia' : 'Marcar urgente';
            btnUrgente.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg> ' + (m.urgente == 1 ? 'Urgente' : 'Normal');
            
            formUrgente.appendChild(btnUrgente);
            actionButtons.appendChild(formUrgente);
            
            const formEliminar = document.createElement('form');
            formEliminar.method = 'POST';
            formEliminar.className = 'action-form';
            formEliminar.onsubmit = function() {
                return confirm('¿Eliminar esta mascota?');
            };
            
            [
                {name: 'action', value: 'deactivate_mascota'},
                {name: 'id', value: m.id},
                {name: 'section', value: 'mascotas'}
            ].forEach(input => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = input.name;
                inp.value = input.value;
                formEliminar.appendChild(inp);
            });
            
            const btnEliminar = document.createElement('button');
            btnEliminar.type = 'submit';
            btnEliminar.className = 'action-btn action-delete';
            btnEliminar.title = 'Eliminar mascota';
            btnEliminar.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg> Eliminar';
            
            formEliminar.appendChild(btnEliminar);
            actionButtons.appendChild(formEliminar);
            
            colActions.appendChild(actionButtons);
            
            // Ensamblar fila
            row.appendChild(colMascota);
            row.appendChild(colDetalles);
            row.appendChild(colEstatus);
            row.appendChild(colActions);
            tableContainer.appendChild(row);
        });
    }
    
    function updatePagination() {
        const paginationContainer = document.getElementById('pagination-mascotas-container');
        if (!paginationContainer) return;
        
        if (totalMascotas === 0 || totalPages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }
        
        let html = '<div class="pagination-info">';
        html += `<span>Página ${currentPage} de ${totalPages} (${totalMascotas} mascotas)</span>`;
        html += '</div>';
        
        html += '<div class="pagination-buttons">';
        
        if (currentPage > 1) {
            html += `<button type="button" class="pagination-btn" onclick="changeMascotaPage(${currentPage - 1})">‹ Anterior</button>`;
        }
        
        for (let i = 1; i <= totalPages; i++) {
            if (i === currentPage) {
                html += `<button type="button" class="pagination-btn active">${i}</button>`;
            } else {
                html += `<button type="button" class="pagination-btn" onclick="changeMascotaPage(${i})">${i}</button>`;
            }
        }
        
        if (currentPage < totalPages) {
            html += `<button type="button" class="pagination-btn" onclick="changeMascotaPage(${currentPage + 1})">Siguiente ›</button>`;
        }
        
        html += '</div>';
        
        paginationContainer.innerHTML = html;
    }
    
    window.changeMascotaPage = function(page) {
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        searchMascotas(currentSearch, currentEspecie, currentEstatus, currentUrgente, page);
    };
    
    // Inicialización
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('#section-mascotas input[name="search_mascotas"]');
        const especieSelect = document.querySelector('#section-mascotas select[name="especie"]');
        const estatusSelect = document.querySelector('#section-mascotas select[name="estatus"]');
        const urgenteSelect = document.querySelector('#section-mascotas select[name="urgente"]');
        
        if (searchInput) {
            const debouncedSearch = debounce(function() {
                currentSearch = searchInput.value;
                currentPage = 1;
                searchMascotas(currentSearch, currentEspecie, currentEstatus, currentUrgente, currentPage);
            }, 300);
            
            searchInput.addEventListener('input', debouncedSearch);
        }
        
        if (especieSelect) {
            especieSelect.addEventListener('change', function() {
                currentEspecie = this.value;
                currentPage = 1;
                searchMascotas(currentSearch, currentEspecie, currentEstatus, currentUrgente, currentPage);
            });
        }
        
        if (estatusSelect) {
            estatusSelect.addEventListener('change', function() {
                currentEstatus = this.value;
                currentPage = 1;
                searchMascotas(currentSearch, currentEspecie, currentEstatus, currentUrgente, currentPage);
            });
        }
        
        if (urgenteSelect) {
            urgenteSelect.addEventListener('change', function() {
                currentUrgente = this.value;
                currentPage = 1;
                searchMascotas(currentSearch, currentEspecie, currentEstatus, currentUrgente, currentPage);
            });
        }
    });
})();