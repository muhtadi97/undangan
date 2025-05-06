document.addEventListener('DOMContentLoaded', function() {
    // Initialize particles.js
    if (typeof particlesJS !== 'undefined') {
        particlesJS('particles-js', {
            particles: {
                number: { 
                    value: 80, 
                    density: { 
                        enable: true, 
                        value_area: 800 
                    } 
                },
                color: { 
                    value: "#D4AF37" 
                },
                shape: { 
                    type: "circle" 
                },
                opacity: { 
                    value: 0.5 
                },
                size: { 
                    value: 3, 
                    random: true 
                },
                line_linked: { 
                    enable: true, 
                    distance: 150, 
                    color: "#D4AF37", 
                    opacity: 0.4, 
                    width: 1 
                },
                move: { 
                    enable: true, 
                    speed: 2, 
                    direction: "none" 
                }
            },
            interactivity: {
                detect_on: "canvas",
                events: {
                    onhover: { 
                        enable: true, 
                        mode: "grab" 
                    },
                    onclick: { 
                        enable: true, 
                        mode: "push" 
                    },
                    resize: true
                }
            }
        });
    }

    // Music control
    const musicToggle = document.getElementById('musicToggle');
    const musicControl = document.getElementById('musicControl');
    const weddingMusic = document.getElementById('weddingMusic');
    
    let musicPlaying = false;
    
    // Enable music after user interaction
    document.body.addEventListener('click', function initMusic() {
        if (!musicPlaying) {
            weddingMusic.play().then(() => {
                musicPlaying = true;
                musicToggle.classList.remove('fa-play');
                musicToggle.classList.add('fa-pause');
            }).catch(error => {
                console.log('Autoplay prevented:', error);
            });
        }
        document.body.removeEventListener('click', initMusic);
    });
    
    // Toggle music play/pause
    musicControl.addEventListener('click', function(e) {
        e.stopPropagation();
        
        if (musicPlaying) {
            weddingMusic.pause();
            musicToggle.classList.remove('fa-pause');
            musicToggle.classList.add('fa-play');
        } else {
            weddingMusic.play();
            musicToggle.classList.remove('fa-play');
            musicToggle.classList.add('fa-pause');
        }
        
        musicPlaying = !musicPlaying;
    });

    // Open invitation function
    window.openInvitation = function() {
        const coverSection = document.querySelector('.cover-section');
        const mainInvitation = document.getElementById('mainInvitation');
        
        coverSection.style.opacity = '0';
        coverSection.style.transform = 'translateY(-20px)';
        
        setTimeout(() => {
            coverSection.style.display = 'none';
            mainInvitation.classList.remove('hidden');
            mainInvitation.style.opacity = '0';
            mainInvitation.style.display = 'block';
            
            setTimeout(() => {
                mainInvitation.style.opacity = '1';
                mainInvitation.style.transform = 'translateY(0)';
            }, 50);
            
            // Play music when invitation is opened
            if (!musicPlaying) {
                weddingMusic.play().then(() => {
                    musicPlaying = true;
                    musicToggle.classList.remove('fa-play');
                    musicToggle.classList.add('fa-pause');
                }).catch(error => {
                    console.log('Autoplay prevented:', error);
                });
            }
        }, 500);
    };

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Gallery lightbox
    const galleryItems = document.querySelectorAll('.gallery-item');
    const lightbox = document.getElementById('galleryLightbox');
    const lightboxImg = document.getElementById('lightboxImage');
    const lightboxCaption = document.querySelector('.lightbox-caption');
    const closeBtn = document.querySelector('.close-btn');
    
    galleryItems.forEach(item => {
        item.addEventListener('click', function() {
            const imgSrc = this.querySelector('img').src;
            const imgAlt = this.querySelector('img').alt;
            
            lightboxImg.src = imgSrc;
            lightboxCaption.textContent = imgAlt;
            lightbox.classList.add('show');
            document.body.style.overflow = 'hidden';
        });
    });
    
    closeBtn.addEventListener('click', function() {
        lightbox.classList.remove('show');
        document.body.style.overflow = 'auto';
    });
    
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            lightbox.classList.remove('show');
            document.body.style.overflow = 'auto';
        }
    });

    // RSVP Form submission
    const rsvpForm = document.getElementById('rsvpForm');
    const formSuccess = document.getElementById('formSuccess');
    
    if (rsvpForm) {
        rsvpForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(rsvpForm);
            const data = Object.fromEntries(formData.entries());
            
            // Send data to server (using Fetch API)
            fetch('rsvp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Success:', data);
                // Show success message
                rsvpForm.classList.add('hidden');
                formSuccess.classList.remove('hidden');
                
                // Scroll to show the success message
                formSuccess.scrollIntoView({ behavior: 'smooth' });
            })
            .catch((error) => {
                console.error('Error:', error);
                // Fallback if fetch fails
                rsvpForm.classList.add('hidden');
                formSuccess.classList.remove('hidden');
                formSuccess.scrollIntoView({ behavior: 'smooth' });
            });
        });
    }

    // Copy bank account number
    window.copyToClipboard = function(text) {
        navigator.clipboard.writeText(text).then(() => {
            // Show copied notification
            const copyButton = event.target;
            const originalText = copyButton.textContent;
            
            copyButton.textContent = 'Tersalin!';
            copyButton.style.backgroundColor = 'rgba(212, 175, 55, 0.4)';
            
            setTimeout(() => {
                copyButton.textContent = originalText;
                copyButton.style.backgroundColor = '';
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy: ', err);
            // Fallback for older browsers
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            
            // Show notification
            const copyButton = event.target;
            const originalText = copyButton.textContent;
            
            copyButton.textContent = 'Tersalin!';
            copyButton.style.backgroundColor = 'rgba(212, 175, 55, 0.4)';
            
            setTimeout(() => {
                copyButton.textContent = originalText;
                copyButton.style.backgroundColor = '';
            }, 2000);
        });
    };

    // Add animation to elements when they come into view
    const observerOptions = {
        threshold: 0.1
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fadeInUp');
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.event-card, .couple-card, .gift-card').forEach(card => {
        observer.observe(card);
    });
});