document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================
    // LÓGICA DE BÚSQUEDA: USUARIOS
    // ==========================================
    const userSearchInput = document.querySelector('#section-usuarios input[name="search"]');
    const userRolSelect = document.querySelector('#section-usuarios select[name="rol"]');
    
    if (userSearchInput && userRolSelect) {
        let userTimeout;
        const fetchUsers = () => {
            const search = userSearchInput.value;
            const rol = userRolSelect.value;
            const url = `/petguard/public/api/search-users.php?search=${encodeURIComponent(search)}&rol=${rol}&page=1`;
            
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Aquí puedes actualizar la tabla de usuarios si lo deseas, 
                        // o simplemente dejar que el formulario GET tradicional haga el trabajo 
                        // si prefieres no complicar el JS. 
                        // Por ahora, si el formulario GET funciona, este JS solo valida que el endpoint responde.
                        console.log('Usuarios encontrados:', data.count);
                    }
                });
        };
        
        userSearchInput.addEventListener('input', () => {
            clearTimeout(userTimeout);
            userTimeout = setTimeout(fetchUsers, 400);
        });
        userRolSelect.addEventListener('change', fetchUsers);
    }

    // ==========================================
    // LÓGICA DE BÚSQUEDA: MASCOTAS
    // ==========================================
    const mascotaSearchInput = document.querySelector('#section-mascotas input[name="search_mascotas"]');
    const mascotaEspecieSelect = document.querySelector('#section-mascotas select[name="especie"]');
    const mascotaEstatusSelect = document.querySelector('#section-mascotas select[name="estatus"]');
    const mascotaUrgenteSelect = document.querySelector('#section-mascotas select[name="urgente"]');
    
    if (mascotaSearchInput) {
        let mascotaTimeout;
        const fetchMascotas = () => {
            const search = mascotaSearchInput.value;
            const especie = mascotaEspecieSelect ? mascotaEspecieSelect.value : '0';
            const estatus = mascotaEstatusSelect ? mascotaEstatusSelect.value : '';
            const urgente = mascotaUrgenteSelect ? mascotaUrgenteSelect.value : '';
            
            let url = `/petguard/public/api/search-mascotas.php?search=${encodeURIComponent(search)}&especie=${especie}&estatus=${encodeURIComponent(estatus)}&page=1`;
            if (urgente !== '') url += `&urgente=${urgente}`;
            
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        console.log('Mascotas encontradas:', data.count);
                        // Aquí iría la lógica para redibujar la tabla de mascotas
                        // Si ya tienes una función updateMascotasTable, llámala aquí con data.mascotas
                    }
                })
                .catch(err => console.error('Error en búsqueda mascotas:', err));
        };
        
        mascotaSearchInput.addEventListener('input', () => {
            clearTimeout(mascotaTimeout);
            mascotaTimeout = setTimeout(fetchMascotas, 400);
        });
        if (mascotaEspecieSelect) mascotaEspecieSelect.addEventListener('change', fetchMascotas);
        if (mascotaEstatusSelect) mascotaEstatusSelect.addEventListener('change', fetchMascotas);
        if (mascotaUrgenteSelect) mascotaUrgenteSelect.addEventListener('change', fetchMascotas);
    }
});