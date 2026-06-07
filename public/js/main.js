document.addEventListener("DOMContentLoaded", function() {
    
    // =========================================================
    // 1. LÓGICA DE MODO OSCURO
    // =========================================================
    const themeToggleBtn = document.getElementById('themeToggle');
    const themeIcon = themeToggleBtn ? themeToggleBtn.querySelector('i') : null;
    const body = document.body;

    function updateThemeIcon(isDark) {
        if (!themeIcon) return;
        if (isDark) {
            themeIcon.classList.remove('bi-moon-fill');
            themeIcon.classList.add('bi-sun-fill');
        } else {
            themeIcon.classList.remove('bi-sun-fill');
            themeIcon.classList.add('bi-moon-fill');
        }
    }

    function applyTheme(isDark) {
        if (isDark) {
            body.classList.add('dark-mode');
        } else {
            body.classList.remove('dark-mode');
        }
        updateThemeIcon(isDark);
        localStorage.setItem('petguard_theme', isDark ? 'dark' : 'light');
    }

    const savedTheme = localStorage.getItem('petguard_theme');
    
    if (savedTheme) {
        applyTheme(savedTheme === 'dark');
    } else {
        const currentHour = new Date().getHours();
        const isNightTime = (currentHour >= 19 || currentHour < 7);
        applyTheme(isNightTime);
    }

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function() {
            const isCurrentlyDark = body.classList.contains('dark-mode');
            applyTheme(!isCurrentlyDark);
        });
    }

    // =========================================================
    // 2. ANIMACIONES GSAP
    // =========================================================
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
        console.warn('GSAP o ScrollTrigger no están cargados.');
        return;
    }
    
    gsap.registerPlugin(ScrollTrigger);

    // 2.1. Efecto de texto animado en el Hero
    const heroLines = document.querySelectorAll('.hero-line-inner');
    gsap.to(heroLines, {
        y: 0,
        duration: 1.2,
        ease: "power3.out",
        stagger: 0.2,
        delay: 0.3
    });

    gsap.to(".hero p", {
        opacity: 1,
        y: 0,
        duration: 1,
        delay: 1,
        ease: "power3.out"
    });

    // 2.2. Animación de la imagen de fondo de la Hero Card
    gsap.from(".hero-card-bg", {
        scale: 1.2,
        opacity: 0,
        duration: 1.5,
        delay: 0.5,
        ease: "power2.out"
    });

    gsap.from(".hero-card-content", {
        y: 40,
        opacity: 0,
        duration: 1,
        delay: 0.8,
        ease: "power3.out"
    });

    // 2.3. Animación escalonada de todas las Cards al hacer scroll
    gsap.utils.toArray('.card').forEach(function(card) {
        gsap.from(card, {
            scrollTrigger: {
                trigger: card,
                start: "top 88%",
                toggleActions: "play none none reverse"
            },
            y: 50,
            opacity: 0,
            duration: 0.8,
            ease: "power3.out"
        });
    });

    // 2.4. Animación de los Títulos de Sección
    gsap.utils.toArray('.section-title').forEach(function(title) {
        gsap.from(title, {
            scrollTrigger: {
                trigger: title,
                start: "top 85%"
            },
            y: 40,
            opacity: 0,
            duration: 1,
            ease: "power3.out"
        });
    });

    // 2.5. Efecto Parallax en las imágenes de fondo
    const parallaxImages = document.querySelectorAll('.hero-card-bg, .feature-card-image');
    parallaxImages.forEach(function(img) {
        gsap.to(img, {
            scrollTrigger: {
                trigger: img,
                start: "top bottom",
                end: "bottom top",
                scrub: 1
            },
            y: -40,
            ease: "none"
        });
    });

    // 2.6. Animación del Buscador Sticky
    gsap.from(".sticky-search", {
        scrollTrigger: {
            trigger: ".sticky-search-wrapper",
            start: "top 85%"
        },
        y: 30,
        opacity: 0,
        duration: 0.8,
        ease: "power3.out"
    });

    // 2.7. Efecto de escala en el Hero al hacer scroll
    gsap.to(".hero h1", {
        scrollTrigger: {
            trigger: ".hero",
            start: "top top",
            end: "bottom top",
            scrub: 1
        },
        scale: 0.8,
        opacity: 0.5,
        ease: "none"
    });

    // 2.8. EFECTO MARAVILLOSO DE HUELLITAS
    const pawPrints = document.querySelectorAll('.paw-print');
    
    if (pawPrints.length > 0) {
        // Animación de entrada con ScrollTrigger
        pawPrints.forEach((paw, index) => {
            const randomRotation = gsap.utils.random(-60, 60);
            
            gsap.to(paw, {
                scrollTrigger: {
                    trigger: ".hero",
                    start: "top center",
                    end: "bottom center",
                    scrub: 0.8,
                },
                opacity: 0.5,
                scale: 1.1,
                rotation: randomRotation,
                duration: 1,
                delay: index * 0.08,
                ease: "elastic.out(1, 0.6)"
            });
        });

        // Efecto de flotación continua después de aparecer
        setTimeout(() => {
            pawPrints.forEach((paw, index) => {
                gsap.to(paw, {
                    y: -15,
                    duration: 2 + (index * 0.2),
                    repeat: -1,
                    yoyo: true,
                    ease: "sine.inOut",
                    delay: index * 0.3
                });
            });
        }, 2000);
    }

    // =========================================================
    // 3. APPLE SLIDER
    // =========================================================
    const slider = document.getElementById('appleSlider');
    const prevBtn = document.getElementById('sliderPrev');
    const nextBtn = document.getElementById('sliderNext');

    if (slider && prevBtn && nextBtn) {
        prevBtn.addEventListener('click', () => {
            slider.scrollBy({ left: -374, behavior: 'smooth' });
        });

        nextBtn.addEventListener('click', () => {
            slider.scrollBy({ left: 374, behavior: 'smooth' });
        });

        let isDown = false;
        let startX;
        let scrollLeft;

        slider.addEventListener('mousedown', (e) => {
            isDown = true;
            slider.style.cursor = 'grabbing';
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        });

        slider.addEventListener('mouseleave', () => {
            isDown = false;
            slider.style.cursor = 'grab';
        });

        slider.addEventListener('mouseup', () => {
            isDown = false;
            slider.style.cursor = 'grab';
        });

        slider.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - startX) * 2;
            slider.scrollLeft = scrollLeft - walk;
        });
    }
});