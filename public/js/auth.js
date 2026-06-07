document.addEventListener("DOMContentLoaded", function() {
    
    let currentStep = 1;
    const totalSteps = 3;
    const indicators = document.querySelectorAll('.wizard-step-indicator');
    const progressBar = document.getElementById('progressBar');
    const btnBack = document.getElementById('btnBack');
    const btnNext = document.getElementById('btnNext');
    const btnSubmit = document.getElementById('btnSubmit');

    function goToStep(targetStep, direction) {
        if (targetStep < 1 || targetStep > totalSteps || targetStep === currentStep) return;

        const currentStepEl = document.querySelector('.wizard-step[data-step="' + currentStep + '"]');
        const targetStepEl = document.querySelector('.wizard-step[data-step="' + targetStep + '"]');

        gsap.to(currentStepEl, {
            x: direction === 'forward' ? -30 : 30,
            opacity: 0,
            duration: 0.3,
            ease: "power2.in",
            onComplete: function() {
                currentStepEl.classList.remove('active');
                currentStepEl.style.transform = 'translateX(0)';
            }
        });

        targetStepEl.style.transform = 'translateX(' + (direction === 'forward' ? 30 : -30) + 'px)';
        targetStepEl.style.opacity = '0';
        targetStepEl.classList.add('active');

        gsap.to(targetStepEl, {
            x: 0,
            opacity: 1,
            duration: 0.4,
            delay: 0.15,
            ease: "power2.out"
        });

        currentStep = targetStep;
        updateProgressBar();
        updateIndicators();
        updateButtons();
    }

    function updateProgressBar() {
        const progressPercent = ((currentStep - 1) / (totalSteps - 1)) * 100;
        progressBar.style.width = progressPercent + '%';
    }

    function updateIndicators() {
        indicators.forEach(function(indicator, index) {
            const stepNum = index + 1;
            indicator.classList.remove('active', 'completed');
            
            if (stepNum < currentStep) {
                indicator.classList.add('completed');
                indicator.querySelector('.wizard-step-circle').innerHTML = '✓';
            } else if (stepNum === currentStep) {
                indicator.classList.add('active');
                indicator.querySelector('.wizard-step-circle').innerHTML = stepNum;
            } else {
                indicator.querySelector('.wizard-step-circle').innerHTML = stepNum;
            }
        });
    }

    function updateButtons() {
        btnBack.style.display = currentStep > 1 ? 'block' : 'none';
        btnNext.style.display = currentStep < totalSteps ? 'block' : 'none';
        btnSubmit.style.display = currentStep === totalSteps ? 'block' : 'none';
    }

    function validateStep(step) {
        let isValid = true;

        if (step === 1) {
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirm');

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value)) {
                showError('email');
                isValid = false;
            } else {
                hideError('email');
            }

            if (password.value.length < 8) {
                showError('password');
                isValid = false;
            } else {
                hideError('password');
            }

            if (password.value !== passwordConfirm.value || passwordConfirm.value === '') {
                showError('password-confirm');
                isValid = false;
            } else {
                hideError('password-confirm');
            }
        }

        if (step === 2) {
            const nombre = document.getElementById('nombre').value.trim();
            const apellido = document.getElementById('apellido_paterno').value.trim();
            const telefono = document.getElementById('telefono').value.trim();
            const curp = document.getElementById('curp').value.trim();

            if (!nombre || !apellido || !telefono || !curp) {
                isValid = false;
                if (!nombre) document.getElementById('nombre').classList.add('error');
                if (!apellido) document.getElementById('apellido_paterno').classList.add('error');
                if (!telefono) document.getElementById('telefono').classList.add('error');
                if (!curp) document.getElementById('curp').classList.add('error');
            } else {
                document.getElementById('nombre').classList.remove('error');
                document.getElementById('apellido_paterno').classList.remove('error');
                document.getElementById('telefono').classList.remove('error');
                document.getElementById('curp').classList.remove('error');
            }
        }

        return isValid;
    }

    function showError(field) {
        const input = document.getElementById(field);
        const error = document.getElementById('error-' + field);
        if (input) input.classList.add('error');
        if (error) error.classList.add('visible');
    }

    function hideError(field) {
        const input = document.getElementById(field);
        const error = document.getElementById('error-' + field);
        if (input) input.classList.remove('error');
        if (error) error.classList.remove('visible');
    }

    btnNext.addEventListener('click', function() {
        if (validateStep(currentStep)) {
            goToStep(currentStep + 1, 'forward');
        }
    });

    btnBack.addEventListener('click', function() {
        goToStep(currentStep - 1, 'backward');
    });

    window.selectRole = function(element) {
        document.querySelectorAll('.role-card').forEach(function(card) { 
            card.classList.remove('active'); 
        });
        element.classList.add('active');
        
        const roleId = element.getAttribute('data-role');
        document.getElementById('selectedRole').value = roleId;

        document.querySelectorAll('.role-fields').forEach(function(fields) {
            fields.style.display = 'none';
        });

        let targetId = '';
        if (roleId === '4') targetId = 'fields-adoptante';
        if (roleId === '2') targetId = 'fields-voluntario';
        if (roleId === '3') targetId = 'fields-veterinario';

        const targetFields = document.getElementById(targetId);
        if (targetFields) {
            targetFields.style.display = 'block';
            gsap.fromTo(targetFields, 
                { opacity: 0, y: 10 }, 
                { opacity: 1, y: 0, duration: 0.4, ease: "power2.out" }
            );
        }
    };

    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('fileInput');
    const fileList = document.getElementById('fileList');

    if (dropzone && fileInput) {
        dropzone.addEventListener('click', function() { fileInput.click(); });
        dropzone.addEventListener('dragover', function(e) { e.preventDefault(); dropzone.classList.add('dragover'); });
        dropzone.addEventListener('dragleave', function() { dropzone.classList.remove('dragover'); });
        dropzone.addEventListener('drop', function(e) {
            e.preventDefault(); 
            dropzone.classList.remove('dragover');
            fileInput.files = e.dataTransfer.files; 
            updateFileList();
        });
        fileInput.addEventListener('change', updateFileList);

        function updateFileList() {
            if (fileInput.files.length > 0) {
                const names = Array.from(fileInput.files).map(function(f) { return f.name; }).join(', ');
                fileList.textContent = fileInput.files.length + ' archivo(s): ' + names;
            } else { 
                fileList.textContent = ''; 
            }
        }
    }

    gsap.from(".auth-left-content", { x: -50, opacity: 0, duration: 1, ease: "power3.out", delay: 0.2 });
    gsap.from(".auth-logo", { y: -20, opacity: 0, duration: 0.6, ease: "power2.out" });
    gsap.from(".auth-title", { y: 20, opacity: 0, duration: 0.6, delay: 0.1, ease: "power2.out" });
    gsap.from(".wizard-progress", { y: 20, opacity: 0, duration: 0.6, delay: 0.2, ease: "power2.out" });
    gsap.from(".wizard-step.active", { y: 20, opacity: 0, duration: 0.6, delay: 0.3, ease: "power2.out" });

    updateProgressBar();
    updateIndicators();
    updateButtons();
});