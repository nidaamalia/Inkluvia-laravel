// Enhanced navbar scroll behavior with hide/show
        let lastScrollTop = 0;
        let scrollThreshold = 100;
        let isScrolling = false;

        window.addEventListener('scroll', function() {
            if (!isScrolling) {
                window.requestAnimationFrame(function() {
                    const navbar = document.getElementById('navbar');
                    const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
                    
                    // Add scrolled class when past threshold
                    if (currentScroll > 50) {
                        navbar.classList.add('scrolled');
                    } else {
                        navbar.classList.remove('scrolled');
                    }
                    
                    // Hide/show navbar based on scroll direction
                    if (currentScroll > scrollThreshold) {
                        if (currentScroll > lastScrollTop && currentScroll > 200) {
                            // Scrolling down - hide navbar
                            navbar.classList.add('hidden');
                        } else {
                            // Scrolling up - show navbar
                            navbar.classList.remove('hidden');
                        }
                    } else {
                        // At top of page - always show navbar
                        navbar.classList.remove('hidden');
                    }
                    
                    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
                    isScrolling = false;
                });
                isScrolling = true;
            }
        });

        // Smooth scrolling function
        function scrollToSection(sectionId) {
            const section = document.getElementById(sectionId);
            if (section) {
                const offsetTop = section.offsetTop - 80; // Account for navbar height
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        }

        // Enhanced smooth scrolling for navigation links
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                scrollToSection(targetId);
                
                // Update active navigation
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                }
            });
        }, observerOptions);

        // Observe all elements with animate-on-scroll class
        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });

        // Navigation highlighting based on scroll position
        const sections = document.querySelectorAll('.section');
        const navLinks = document.querySelectorAll('.nav-link');

        const highlightNavigation = () => {
            let current = '';
            const scrollPos = window.pageYOffset + 120;

            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                const sectionId = section.getAttribute('id');

                if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                    current = sectionId;
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${current}`) {
                    link.classList.add('active');
                }
            });
        };

        window.addEventListener('scroll', highlightNavigation);

        // Enhanced card hover effects
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-12px) scale(1.02)';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
            const navLinks = document.querySelector('.nav-links');
            navLinks.classList.toggle('active');
            
            // Toggle icon between bars and times
            const icon = this.querySelector('i');
            if (icon.classList.contains('fa-bars')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            const navLinks = document.querySelector('.nav-links');
            const mobileBtn = document.querySelector('.mobile-menu-btn');
            
            if (!navLinks.contains(e.target) && !mobileBtn.contains(e.target)) {
                navLinks.classList.remove('active');
                const icon = mobileBtn.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Stats animation when in view
        const statsObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const stat = entry.target;
                    const number = stat.querySelector('.number');
                    const finalValue = number.textContent;
                    
                    // Animate number counting (for numeric values)
                    if (finalValue.includes('+') || !isNaN(finalValue)) {
                        const numericValue = parseInt(finalValue.replace(/\D/g, ''));
                        let current = 0;
                        const increment = numericValue / 50;
                        const timer = setInterval(() => {
                            current += increment;
                            if (current >= numericValue) {
                                current = numericValue;
                                clearInterval(timer);
                            }
                            number.textContent = Math.floor(current) + (finalValue.includes('+') ? '+' : '');
                        }, 30);
                    }
                }
            });
        }, { threshold: 0.5 });

        document.querySelectorAll('.stat').forEach(stat => {
            statsObserver.observe(stat);
        });

        // Loading animation
        window.addEventListener('load', function() {
            document.body.classList.add('loading');
            
            // Stagger animation for hero content
            const heroElements = document.querySelectorAll('.hero-content > *');
            heroElements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.2}s`;
            });
        });

        // Enhanced keyboard navigation
        document.addEventListener('keydown', function(e) {
            // Press 'Escape' to show navbar if hidden
            if (e.key === 'Escape') {
                document.getElementById('navbar').classList.remove('hidden');
                document.activeElement.blur();
            }
            
            // Arrow keys for section navigation
            if (e.key === 'ArrowDown' && e.ctrlKey) {
                e.preventDefault();
                const currentSection = getCurrentSection();
                const nextSection = getNextSection(currentSection);
                if (nextSection) {
                    scrollToSection(nextSection.id);
                }
            }
            
            if (e.key === 'ArrowUp' && e.ctrlKey) {
                e.preventDefault();
                const currentSection = getCurrentSection();
                const prevSection = getPreviousSection(currentSection);
                if (prevSection) {
                    scrollToSection(prevSection.id);
                }
            }
        });

        function getCurrentSection() {
            const scrollPos = window.pageYOffset + 120;
            for (let section of sections) {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                    return section;
                }
            }
            return sections[0];
        }

        function getNextSection(currentSection) {
            const currentIndex = Array.from(sections).indexOf(currentSection);
            return sections[currentIndex + 1] || null;
        }

        function getPreviousSection(currentSection) {
            const currentIndex = Array.from(sections).indexOf(currentSection);
            return sections[currentIndex - 1] || null;
        }

        // Mobile menu functionality (if needed in future)
        document.querySelector('.mobile-menu-btn')?.addEventListener('click', function() {
            // Toggle mobile menu (implement based on design needs)
            console.log('Mobile menu toggled');
        });

        // Enhanced button click effects
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                // Create ripple effect
                const rect = this.getBoundingClientRect();
                const ripple = document.createElement('span');
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.6);
                    transform: scale(0);
                    animation: ripple 0.6s linear;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                `;
                
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // Add ripple animation CSS
        const rippleStyle = document.createElement('style');
        rippleStyle.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(rippleStyle);

        // Performance optimization: Throttle scroll events
        function throttle(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            }
        }

        // Apply throttling to scroll events
        window.addEventListener('scroll', throttle(highlightNavigation, 100));

        // Preload images for better performance
        function preloadImages() {
            const imageUrls = [
                '{{ asset("assets/braille_paper.png") }}',
                '{{ asset("assets/mockup.png") }}',
                '{{ asset("assets/braille.png") }}'
            ];
            
            imageUrls.forEach(url => {
                const img = new Image();
                img.src = url;
            });
        }

        // Initialize preloading
        preloadImages();

        // Add focus management for better accessibility
        document.addEventListener('focusin', function(e) {
            // Ensure navbar is visible when navigating with keyboard
            if (e.target.closest('.navbar')) {
                document.getElementById('navbar').classList.remove('hidden');
            }
        });

        // Initialize all animations and interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add entrance animations with stagger
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });

            // Initialize any other components
            console.log('Inkluvia website initialized successfully!');
        });