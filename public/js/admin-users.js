/**
 * ============================================================
 * PETGUARD - Búsqueda AJAX de Usuarios con Debounce y Paginación
 * ============================================================
 */

(function() {
    'use strict';
    
    let searchTimeout = null;
    let currentSearch = '';
    let currentRol = '0';
    let currentPage = 1;
    let totalPages = 1;
    let totalUsers = 0;
    
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
    
    function searchUsers(searchTerm, rolFilter, page) {
        page = page || 1;
        const url = `/petguard/public/api/search-users.php?search=${encodeURIComponent(searchTerm)}&rol=${rolFilter}&page=${page}`;
        
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                currentPage = data.current_page;
                totalPages = data.total_pages;
                totalUsers = data.total;
                updateUsersTable(data.users, data.count);
                updatePagination();
            } else {
                console.error('Error:', data.error);
            }
        })
        .catch(error => {
            console.error('Error en la búsqueda:', error);
        });
    }
    
    function updateUsersTable(users, count) {
        const tableContainer = document.querySelector('.table-responsive .data-table');
        if (!tableContainer) return;
        
        const sectionTitle = document.querySelector('#section-usuarios .section-title');
        if (sectionTitle) {
            sectionTitle.textContent = `Usuarios Registrados (${count})`;
        }
        
        const existingRows = tableContainer.querySelectorAll('.data-table-row');
        existingRows.forEach(row => row.remove());
        
        const noResultsMsg = tableContainer.querySelector('div[style*="text-align: center"]');
        if (noResultsMsg) {
            noResultsMsg.remove();
        }
        
        if (users.length === 0) {
            const noResults = document.createElement('div');
            noResults.style.cssText = 'padding: 40px; text-align: center; color: var(--text-secondary); font-weight: 600;';
            noResults.textContent = 'No se encontraron usuarios con los filtros aplicados.';
            tableContainer.appendChild(noResults);
            return;
        }
        
        users.forEach(u => {
            const row = document.createElement('div');
            row.className = 'data-table-row users-table-row';
            row.style.cssText = 'grid-template-columns: 2.5fr 1fr 1fr 1.5fr;';
            
            const userCell = document.createElement('div');
            userCell.className = 'col-user';
            
            const userCellContent = document.createElement('div');
            userCellContent.className = 'user-cell';
            
            const avatar = document.createElement('div');
            avatar.className = 'user-avatar-small';
            if (u.foto_url) {
                const img = document.createElement('img');
                img.src = u.foto_url;
                img.alt = 'Avatar';
                img.style.cssText = 'width: 100%; height: 100%; border-radius: 50%; object-fit: cover;';
                avatar.appendChild(img);
            } else {
                avatar.textContent = u.initials;
            }
            
            const userInfo = document.createElement('div');
            const userName = document.createElement('div');
            userName.className = 'user-name';
            userName.textContent = `${u.nombre} ${u.apellido_paterno}`;
            
            if (u.isCurrentUser) {
                const badge = document.createElement('span');
                badge.className = 'current-user-badge';
                badge.textContent = '(Tú)';
                userName.appendChild(badge);
            }
            
            if (u.isSistemas) {
                const badge = document.createElement('span');
                badge.className = 'current-user-badge';
                badge.style.color = '#8b5cf6';
                badge.textContent = '(Sistemas)';
                userName.appendChild(badge);
            }
            
            const userEmail = document.createElement('div');
            userEmail.className = 'user-email';
            userEmail.textContent = u.email;
            
            userInfo.appendChild(userName);
            userInfo.appendChild(userEmail);
            userCellContent.appendChild(avatar);
            userCellContent.appendChild(userInfo);
            userCell.appendChild(userCellContent);
            
            const roleCell = document.createElement('div');
            roleCell.className = 'col-role';
            
            if (!u.isSistemas) {
                const roleForm = document.createElement('form');
                roleForm.method = 'POST';
                roleForm.className = 'role-change-form';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'change_user_role';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                idInput.value = u.id;
                
                const sectionInput = document.createElement('input');
                sectionInput.type = 'hidden';
                sectionInput.name = 'section';
                sectionInput.value = 'usuarios';
                
                const roleSelect = document.createElement('select');
                roleSelect.name = 'rol_id';
                roleSelect.className = 'form-select role-select';
                roleSelect.onchange = function() {
                    if (confirm(`¿Cambiar rol de ${u.nombre}?`)) {
                        this.form.submit();
                    }
                };
                
                const existingRoleSelect = document.querySelector('.filter-form select[name="rol"]');
                if (existingRoleSelect) {
                    Array.from(existingRoleSelect.options).forEach(option => {
                        if (option.value !== '0') {
                            const opt = document.createElement('option');
                            opt.value = option.value;
                            opt.textContent = option.textContent;
                            if (parseInt(option.value) === u.rol_id) {
                                opt.selected = true;
                            }
                            roleSelect.appendChild(opt);
                        }
                    });
                }
                
                roleForm.appendChild(actionInput);
                roleForm.appendChild(idInput);
                roleForm.appendChild(sectionInput);
                roleForm.appendChild(roleSelect);
                roleCell.appendChild(roleForm);
            }
            
            const statusCell = document.createElement('div');
            statusCell.className = 'col-status';
            
            const statusBadge = document.createElement('span');
            statusBadge.className = `status-badge ${u.bloqueado == 1 ? 'status-blocked' : 'status-active'}`;
            statusBadge.textContent = u.bloqueado == 1 ? 'Bloqueado' : 'Activo';
            statusCell.appendChild(statusBadge);
            
            const actionsCell = document.createElement('div');
            actionsCell.className = 'col-actions';
            
            if (u.isSistemas) {
                const sistemasMsg = document.createElement('div');
                sistemasMsg.style.cssText = 'font-size: 11px; color: var(--text-secondary); font-style: italic;';
                sistemasMsg.textContent = 'Solo Sistemas puede gestionar esta cuenta';
                actionsCell.appendChild(sistemasMsg);
            } else {
                const actionButtons = document.createElement('div');
                actionButtons.className = 'action-buttons';
                
                const editBtn = document.createElement('button');
                editBtn.type = 'button';
                editBtn.className = 'action-btn action-edit';
                editBtn.title = 'Editar usuario';
                editBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg> Editar';
                editBtn.onclick = function() {
                    openEditModal(u);
                };
                actionButtons.appendChild(editBtn);
                
                if (u.bloqueado == 1) {
                    const unblockForm = document.createElement('form');
                    unblockForm.method = 'POST';
                    unblockForm.className = 'action-form';
                    
                    const unblockAction = document.createElement('input');
                    unblockAction.type = 'hidden';
                    unblockAction.name = 'action';
                    unblockAction.value = 'unblock_user';
                    
                    const unblockId = document.createElement('input');
                    unblockId.type = 'hidden';
                    unblockId.name = 'id';
                    unblockId.value = u.id;
                    
                    const unblockSection = document.createElement('input');
                    unblockSection.type = 'hidden';
                    unblockSection.name = 'section';
                    unblockSection.value = 'usuarios';
                    
                    const unblockBtn = document.createElement('button');
                    unblockBtn.type = 'submit';
                    unblockBtn.className = 'action-btn action-approve';
                    unblockBtn.title = 'Desbloquear usuario';
                    unblockBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"></path></svg> Desbloquear';
                    
                    unblockForm.appendChild(unblockAction);
                    unblockForm.appendChild(unblockId);
                    unblockForm.appendChild(unblockSection);
                    unblockForm.appendChild(unblockBtn);
                    actionButtons.appendChild(unblockForm);
                } else {
                    const blockForm = document.createElement('form');
                    blockForm.method = 'POST';
                    blockForm.className = 'action-form';
                    
                    const blockAction = document.createElement('input');
                    blockAction.type = 'hidden';
                    blockAction.name = 'action';
                    blockAction.value = 'block_user';
                    
                    const blockId = document.createElement('input');
                    blockId.type = 'hidden';
                    blockId.name = 'id';
                    blockId.value = u.id;
                    
                    const blockSection = document.createElement('input');
                    blockSection.type = 'hidden';
                    blockSection.name = 'section';
                    blockSection.value = 'usuarios';
                    
                    const blockMotivo = document.createElement('input');
                    blockMotivo.type = 'hidden';
                    blockMotivo.name = 'motivo_bloqueo';
                    blockMotivo.value = 'Bloqueado por administrador';
                    
                    const blockBtn = document.createElement('button');
                    blockBtn.type = 'submit';
                    blockBtn.className = 'action-btn action-reject';
                    blockBtn.title = 'Bloquear usuario';
                    if (u.isCurrentUser) {
                        blockBtn.disabled = true;
                    }
                    blockBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg> Bloquear';
                    
                    blockForm.appendChild(blockAction);
                    blockForm.appendChild(blockId);
                    blockForm.appendChild(blockSection);
                    blockForm.appendChild(blockMotivo);
                    blockForm.appendChild(blockBtn);
                    actionButtons.appendChild(blockForm);
                }
                
                if (!u.isCurrentUser) {
                    const deleteForm = document.createElement('form');
                    deleteForm.method = 'POST';
                    deleteForm.className = 'action-form';
                    deleteForm.onsubmit = function() {
                        return confirm(`¿Desactivar a ${u.nombre}?`);
                    };
                    
                    const deleteAction = document.createElement('input');
                    deleteAction.type = 'hidden';
                    deleteAction.name = 'action';
                    deleteAction.value = 'deactivate_user';
                    
                    const deleteId = document.createElement('input');
                    deleteId.type = 'hidden';
                    deleteId.name = 'id';
                    deleteId.value = u.id;
                    
                    const deleteSection = document.createElement('input');
                    deleteSection.type = 'hidden';
                    deleteSection.name = 'section';
                    deleteSection.value = 'usuarios';
                    
                    const deleteBtn = document.createElement('button');
                    deleteBtn.type = 'submit';
                    deleteBtn.className = 'action-btn action-delete';
                    deleteBtn.title = 'Eliminar usuario';
                    deleteBtn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg> Eliminar';
                    
                    deleteForm.appendChild(deleteAction);
                    deleteForm.appendChild(deleteId);
                    deleteForm.appendChild(deleteSection);
                    deleteForm.appendChild(deleteBtn);
                    actionButtons.appendChild(deleteForm);
                }
                
                actionsCell.appendChild(actionButtons);
            }
            
            row.appendChild(userCell);
            row.appendChild(roleCell);
            row.appendChild(statusCell);
            row.appendChild(actionsCell);
            tableContainer.appendChild(row);
        });
    }
    
    function updatePagination() {
        const paginationContainer = document.getElementById('pagination-container');
        if (!paginationContainer) return;
        
        if (totalUsers === 0 || totalPages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }
        
        let html = '<div class="pagination-info">';
        html += `<span>Página ${currentPage} de ${totalPages} (${totalUsers} usuarios)</span>`;
        html += '</div>';
        
        html += '<div class="pagination-buttons">';
        
        if (currentPage > 1) {
            html += `<button type="button" class="pagination-btn" onclick="changePage(${currentPage - 1})">‹ Anterior</button>`;
        }
        
        for (let i = 1; i <= totalPages; i++) {
            if (i === currentPage) {
                html += `<button type="button" class="pagination-btn active">${i}</button>`;
            } else {
                html += `<button type="button" class="pagination-btn" onclick="changePage(${i})">${i}</button>`;
            }
        }
        
        if (currentPage < totalPages) {
            html += `<button type="button" class="pagination-btn" onclick="changePage(${currentPage + 1})">Siguiente ›</button>`;
        }
        
        html += '</div>';
        
        paginationContainer.innerHTML = html;
    }
    
    window.changePage = function(page) {
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        searchUsers(currentSearch, currentRol, page);
    };
    
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="search"]');
        const rolSelect = document.querySelector('select[name="rol"]');
        
        if (searchInput) {
            const debouncedSearch = debounce(function() {
                currentSearch = searchInput.value;
                currentPage = 1;
                searchUsers(currentSearch, currentRol, currentPage);
            }, 300);
            
            searchInput.addEventListener('input', debouncedSearch);
        }
        
        if (rolSelect) {
            rolSelect.addEventListener('change', function() {
                currentRol = this.value;
                currentPage = 1;
                searchUsers(currentSearch, currentRol, currentPage);
            });
        }
    });
})();