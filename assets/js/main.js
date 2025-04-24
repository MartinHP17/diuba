document.addEventListener('DOMContentLoaded', function() {
  // Header scroll effect
  const navbar = document.querySelector('.navbar');
  
  window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });
  
  // Navbar Toggle
  const navbarToggle = document.getElementById('navbar-toggle');
  const navbarMenu = document.getElementById('navbar-menu');
  
  if(navbarToggle) {
    navbarToggle.addEventListener('click', () => {
      navbarMenu.classList.toggle('active');
      // Add rotation animation to toggle icon
      navbarToggle.querySelector('i').classList.toggle('fa-times');
      navbarToggle.style.transform = navbarToggle.style.transform === 'rotate(90deg)' ? 'rotate(0)' : 'rotate(90deg)';
    });
  }
  
  // Close mobile menu when clicking outside
  document.addEventListener('click', (e) => {
    if (navbarMenu && navbarMenu.classList.contains('active') && 
        !navbarMenu.contains(e.target) && 
        !navbarToggle.contains(e.target)) {
      navbarMenu.classList.remove('active');
      navbarToggle.querySelector('i').classList.remove('fa-times');
      navbarToggle.style.transform = 'rotate(0)';
    }
  });
  
  // Active Link
  const currentLocation = location.href;
  const navLinks = document.querySelectorAll('.navbar-link');
  
  navLinks.forEach(link => {
    if (link.href === currentLocation) {
      link.classList.add('active');
    }
  });
  
  // Back to Top Button
  const backToTopButton = document.getElementById('backToTop');
  
  if(backToTopButton) {
    window.addEventListener('scroll', () => {
      if (window.scrollY > 500) {
        backToTopButton.classList.add('show');
      } else {
        backToTopButton.classList.remove('show');
      }
    });
    
    backToTopButton.addEventListener('click', () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }
  
  // Tabs Functionality
  const tabButtons = document.querySelectorAll('.tab-btn');
  const tabContents = document.querySelectorAll('.tab-content');
  
  if(tabButtons.length > 0) {
    tabButtons.forEach(button => {
      button.addEventListener('click', () => {
        // Remove active class from all buttons and contents
        tabButtons.forEach(btn => btn.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));
        
        // Add active class to clicked button and corresponding content
        button.classList.add('active');
        const tabId = button.getAttribute('data-tab');
        const tabContent = document.getElementById(tabId);
        if(tabContent) {
          tabContent.classList.add('active');
        }
      });
    });
  }
  
  // Accordion Functionality
  const accordionButtons = document.querySelectorAll('.accordion-button');
  
  if(accordionButtons.length > 0) {
    accordionButtons.forEach((button, index) => {
      button.addEventListener('click', () => {
        const content = document.getElementById(`accordion-content-${index}`);
        
        // Toggle active class on button
        button.classList.toggle('active');
        
        // Toggle content visibility
        if (content) {
          if (content.style.maxHeight) {
            content.style.maxHeight = null;
          } else {
            content.style.maxHeight = content.scrollHeight + 'px';
          }
        }
      });
    });
  }
  
  // Carousel Functionality
  const carousel = document.getElementById('eventCarousel');
  
  if(carousel) {
    const carouselInner = document.getElementById('carouselInner');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const carouselItems = carousel.querySelectorAll('.carousel-item');
    
    let currentIndex = 0;
    const itemCount = carouselItems.length;
    
    // Set initial position
    updateCarousel();
    
    // Previous button click
    if(prevBtn) {
      prevBtn.addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + itemCount) % itemCount;
        updateCarousel();
      });
    }
    
    // Next button click
    if(nextBtn) {
      nextBtn.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % itemCount;
        updateCarousel();
      });
    }
    
    // Auto slide
    let autoSlide = setInterval(() => {
      currentIndex = (currentIndex + 1) % itemCount;
      updateCarousel();
    }, 5000);
    
    // Pause auto slide on hover
    carousel.addEventListener('mouseenter', () => {
      clearInterval(autoSlide);
    });
    
    // Resume auto slide on mouse leave
    carousel.addEventListener('mouseleave', () => {
      autoSlide = setInterval(() => {
        currentIndex = (currentIndex + 1) % itemCount;
        updateCarousel();
      }, 5000);
    });
    
    // Update carousel position
    function updateCarousel() {
      if(carouselInner) {
        carouselInner.style.transform = `translateX(-${currentIndex * 100}%)`;
      }
    }
  }
  
  // Video Player Functionality
  const videoButtons = document.querySelectorAll('.watch-video, .video-play');
  const videoPlayer = document.getElementById('videoPlayer');
  
  if(videoButtons.length > 0 && videoPlayer) {
    const youtubeVideo = document.getElementById('youtubeVideo');
    const closeVideo = document.getElementById('closeVideo');
    
    videoButtons.forEach(button => {
      button.addEventListener('click', () => {
        const videoId = button.getAttribute('data-video-id');
        if(youtubeVideo) {
          youtubeVideo.src = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
          videoPlayer.classList.add('active');
          
          // Scroll to video player
          videoPlayer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      });
    });
    
    if(closeVideo) {
      closeVideo.addEventListener('click', () => {
        if(youtubeVideo) {
          youtubeVideo.src = '';
          videoPlayer.classList.remove('active');
        }
      });
    }
  }
  
  // Form Validation
  const contactForm = document.getElementById('contactForm');
  
  if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
      let isValid = true;
      const nombre = document.getElementById('nombre');
      const email = document.getElementById('email');
      const mensaje = document.getElementById('mensaje');
      
      // Simple validation
      if (nombre && nombre.value.trim() === '') {
        isValid = false;
        nombre.classList.add('error');
      } else if(nombre) {
        nombre.classList.remove('error');
      }
      
      if (email && (email.value.trim() === '' || !isValidEmail(email.value))) {
        isValid = false;
        email.classList.add('error');
      } else if(email) {
        email.classList.remove('error');
      }
      
      if (mensaje && mensaje.value.trim() === '') {
        isValid = false;
        mensaje.classList.add('error');
      } else if(mensaje) {
        mensaje.classList.remove('error');
      }
      
      if (!isValid) {
        e.preventDefault();
      }
    });
  }
  
  // Email validation helper
  function isValidEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
  }
  
  // Add animation classes to elements on scroll
  const animatedElements = document.querySelectorAll('.animate');
  
  function animateOnScroll() {
    const windowHeight = window.innerHeight;
    const scrollY = window.scrollY;
    
    animatedElements.forEach(element => {
      const elementPosition = element.getBoundingClientRect().top + scrollY;
      const elementHeight = element.offsetHeight;
      const elementOffset = 100;
      
      if (scrollY > (elementPosition - windowHeight + elementOffset)) {
        const animationType = element.getAttribute('data-animation') || 'fadeIn';
        element.classList.add(animationType);
      }
    });
  }
  
  if(animatedElements.length > 0) {
    window.addEventListener('scroll', animateOnScroll);
    window.addEventListener('load', animateOnScroll);
  }
  
  // Initialize statistics counters
  const statElements = document.querySelectorAll('.stat-number');
  
  function animateStats() {
    statElements.forEach(stat => {
      const target = parseInt(stat.getAttribute('data-target'), 10);
      const duration = 2000; // ms
      const step = target / (duration / 16); // 60fps
      let current = 0;
      
      const updateCounter = () => {
        current += step;
        if (current < target) {
          stat.textContent = Math.floor(current);
          requestAnimationFrame(updateCounter);
        } else {
          stat.textContent = target;
        }
      };
      
      updateCounter();
    });
  }
  
  // Run stats animation when elements come into view
  if(statElements.length > 0) {
    const statsSection = document.querySelector('.stats-section');
    if(statsSection) {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            animateStats();
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.5 });
      
      observer.observe(statsSection);
    }
  }
  
  // Floating menu for mobile
  const floatingActionButton = document.querySelector('.floating-action-button');
  const floatingMenu = document.querySelector('.floating-menu');
  
  if(floatingActionButton && floatingMenu) {
    floatingActionButton.addEventListener('click', () => {
      floatingMenu.classList.toggle('active');
      floatingActionButton.classList.toggle('active');
    });
  }
  
  // Initialize alert close buttons
  const alertCloseButtons = document.querySelectorAll('.alert-close');
  
  alertCloseButtons.forEach(button => {
    button.addEventListener('click', () => {
      const alert = button.closest('.alert');
      if(alert) {
        alert.style.opacity = '0';
        setTimeout(() => {
          alert.style.display = 'none';
        }, 300);
      }
    });
  });
  
  // Add smooth scrolling to all hash links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href');
      if(targetId !== "#") {
        e.preventDefault();
        
        document.querySelector(targetId).scrollIntoView({
          behavior: 'smooth'
        });
      }
    });
  });
});
