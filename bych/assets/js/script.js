// BeyChoc JavaScript - Frontend Functionality

// Global variables
let currentSlide = 0;
let allProducts = [];
let filteredProducts = [];

// DOM elements
const hamburger = document.getElementById('hamburger');
const navMenu = document.getElementById('nav-menu');
const searchInput = document.getElementById('searchInput');
const categoryFilter = document.getElementById('categoryFilter');
const productsGrid = document.getElementById('productsGrid');
const loading = document.getElementById('loading');
const productModal = document.getElementById('productModal');
const closeModal = document.getElementById('closeModal');
const modalBody = document.getElementById('modalBody');
const navbar = document.getElementById('navbar');

// Initialize on DOM loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

// Initialize application
function initializeApp() {
    setupEventListeners();
    startSlideshow();
    loadProducts();
    setupScrollEffects();
    setupAnimations();
}

// Event Listeners Setup
function setupEventListeners() {
    // Hamburger menu toggle
    hamburger?.addEventListener('click', toggleMobileMenu);
    
    // Search functionality
    searchInput?.addEventListener('input', debounce(filterProducts, 300));
    categoryFilter?.addEventListener('change', filterProducts);
    
    // Modal close events
    closeModal?.addEventListener('click', closeProductModal);
    window.addEventListener('click', function(event) {
        if (event.target === productModal) {
            closeProductModal();
        }
    });
    
    // Scroll events
    window.addEventListener('scroll', handleScroll);
    
    // Smooth scroll for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                scrollToSection(this.getAttribute('href').substring(1));
            }
        });
    });
    
    // Close mobile menu when clicking nav links
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', closeMobileMenu);
    });
}

// Mobile menu toggle
function toggleMobileMenu() {
    hamburger.classList.toggle('active');
    navMenu.classList.toggle('active');
}

function closeMobileMenu() {
    hamburger.classList.remove('active');
    navMenu.classList.remove('active');
}

// Enhanced slideshow functionality with smooth transitions
function startSlideshow() {
    const slides = document.querySelectorAll('.slide');
    if (slides.length === 0) return;
    
    // Preload all banner images for smooth transitions
    slides.forEach((slide, index) => {
        const img = slide.querySelector('img');
        if (img && img.src) {
            const preloadImg = new Image();
            preloadImg.src = img.src;
        }
    });
    
    // Auto-advance slides every 5 seconds with smooth animation
    setInterval(() => {
        // Add fade-out effect to current slide
        slides[currentSlide].style.opacity = '0';
        
        setTimeout(() => {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            
            // Reset opacity and add active class
            slides[currentSlide].style.opacity = '0';
            slides[currentSlide].classList.add('active');
            
            // Fade in new slide
            setTimeout(() => {
                slides[currentSlide].style.opacity = '1';
            }, 50);
        }, 300);
    }, 5000);
}

// Scroll effects
function handleScroll() {
    // Navbar scroll effect
    if (window.scrollY > 100) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
    
    // Reveal animations on scroll
    animateOnScroll();
}

function animateOnScroll() {
    const elements = document.querySelectorAll('.product-card, .section-header');
    
    elements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const elementVisible = 150;
        
        if (elementTop < window.innerHeight - elementVisible) {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }
    });
}

// Setup initial animations
function setupAnimations() {
    // Add animation delays to product cards
    setTimeout(() => {
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
    }, 100);
}

// Smooth scroll to section
function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        const offsetTop = section.getBoundingClientRect().top + window.pageYOffset - 80;
        window.scrollTo({
            top: offsetTop,
            behavior: 'smooth'
        });
    }
    closeMobileMenu();
}

// Load products from server
async function loadProducts() {
    showLoading();
    
    try {
        const response = await fetch('api/get_products.php');
        const data = await response.json();
        
        if (data.success) {
            allProducts = data.products;
            filteredProducts = [...allProducts];
            displayProducts(filteredProducts);
        } else {
            showError('Failed to load products');
        }
    } catch (error) {
        console.error('Error loading products:', error);
        showError('Error connecting to server');
        // Show placeholder products for demo
        loadPlaceholderProducts();
    } finally {
        hideLoading();
    }
}

// Placeholder products for demo (when backend is not ready)
function loadPlaceholderProducts() {
    allProducts = [
        {
            id: 1,
            name: 'Premium Dark Chocolate',
            code: 'DC001',
            description: 'Rich 85% cocoa dark chocolate with intense flavor profile',
            image: 'assets/images/products/dark-chocolate.jpg',
            category: 'Dark Chocolate',
            weight: null
        },
        {
            id: 2,
            name: 'Silky Milk Chocolate',
            code: 'MC001',
            description: 'Creamy milk chocolate made with finest Belgian cocoa',
            image: 'assets/images/products/milk-chocolate.jpg',
            category: 'Milk Chocolate',
            weight: null
        },
        {
            id: 3,
            name: 'White Chocolate Dreams',
            code: 'WC001',
            description: 'Pure white chocolate with vanilla bean essence',
            image: 'assets/images/products/white-chocolate.jpg',
            category: 'White Chocolate',
            weight: null
        },
        {
            id: 4,
            name: 'Artisan Chocolate Bar',
            code: 'BAR001',
            description: 'Hand-crafted chocolate bar with sea salt finish',
            image: 'assets/images/products/chocolate-bar.jpg',
            category: 'Bars',
            weight: '100g'
        },
        {
            id: 5,
            name: 'Luxury Gift Package',
            code: 'PKG001',
            description: 'Assorted chocolates in elegant gift packaging',
            image: 'assets/images/products/gift-package.jpg',
            category: 'Packages',
            weight: '500g'
        },
        {
            id: 6,
            name: 'Light Chocolate Delights',
            code: 'LC001',
            description: 'Low-calorie chocolate option without compromising taste',
            image: 'assets/images/products/light-chocolate.jpg',
            category: 'Light Chocolate',
            weight: null
        }
    ];
    
    filteredProducts = [...allProducts];
    displayProducts(filteredProducts);
}

// Display products in grid
function displayProducts(products) {
    if (!productsGrid) return;
    
    if (products.length === 0) {
        productsGrid.innerHTML = '<div class="no-products"><p>No products found matching your criteria.</p></div>';
        return;
    }
    
    productsGrid.innerHTML = products.map(product => `
        <div class="product-card" data-category="${product.category}">
            <img src="${product.image}" alt="${product.name}" class="product-image" 
                 onerror="this.src='assets/images/placeholder.jpg'">
            <div class="product-info">
                <div class="product-category">${product.category}</div>
                <h3 class="product-name">${product.name}</h3>
                <div class="product-code">Code: ${product.code}</div>
                <p class="product-description">${product.description}</p>
                <button class="view-details-btn" onclick="showProductDetails(${product.id})">
                    View Details
                </button>
            </div>
        </div>
    `).join('');
    
    // Animate new products
    animateProductCards();
}

// Animate product cards on load
function animateProductCards() {
    const cards = document.querySelectorAll('.product-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// Filter products based on search and category
function filterProducts() {
    const searchTerm = searchInput?.value.toLowerCase() || '';
    const selectedCategory = categoryFilter?.value || '';
    
    filteredProducts = allProducts.filter(product => {
        const matchesSearch = product.name.toLowerCase().includes(searchTerm) || 
                            product.code.toLowerCase().includes(searchTerm);
        const matchesCategory = !selectedCategory || product.category === selectedCategory;
        
        return matchesSearch && matchesCategory;
    });
    
    displayProducts(filteredProducts);
}

// Show product details in modal
function showProductDetails(productId) {
    const product = allProducts.find(p => p.id == productId);
    if (!product) return;
    
    modalBody.innerHTML = `
        <img src="${product.image}" alt="${product.name}" class="modal-product-image"
             onerror="this.src='assets/images/placeholder.jpg'">
        <div class="modal-product-info">
            <h3>${product.name}</h3>
            <div class="modal-product-code">Product Code: ${product.code}</div>
            <div class="product-category">${product.category}</div>
            <p class="modal-product-description">${product.description}</p>
            ${product.weight ? `
                <div class="weight-info">
                    <strong>Weight:</strong> ${product.weight}
                </div>
            ` : ''}
        </div>
    `;
    
    productModal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

// Close product modal
function closeProductModal() {
    productModal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Show loading spinner
function showLoading() {
    if (loading) {
        loading.style.display = 'block';
    }
}

// Hide loading spinner
function hideLoading() {
    if (loading) {
        loading.style.display = 'none';
    }
}

// Show error message
function showError(message) {
    if (productsGrid) {
        productsGrid.innerHTML = `
            <div class="error-message">
                <p>${message}</p>
                <button onclick="loadProducts()" class="retry-btn">Retry</button>
            </div>
        `;
    }
}

// Debounce function for search
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Utility functions for animations
function fadeIn(element, duration = 300) {
    element.style.opacity = 0;
    element.style.display = 'block';
    
    let start = null;
    function animate(timestamp) {
        if (!start) start = timestamp;
        const progress = timestamp - start;
        const opacity = Math.min(progress / duration, 1);
        
        element.style.opacity = opacity;
        
        if (progress < duration) {
            requestAnimationFrame(animate);
        }
    }
    
    requestAnimationFrame(animate);
}

function fadeOut(element, duration = 300) {
    let start = null;
    const initialOpacity = parseFloat(getComputedStyle(element).opacity);
    
    function animate(timestamp) {
        if (!start) start = timestamp;
        const progress = timestamp - start;
        const opacity = Math.max(initialOpacity - (progress / duration), 0);
        
        element.style.opacity = opacity;
        
        if (progress < duration) {
            requestAnimationFrame(animate);
        } else {
            element.style.display = 'none';
        }
    }
    
    requestAnimationFrame(animate);
}

// Performance optimization
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

// Add smooth reveal animations for elements
function observeElements() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    // Observe all animatable elements
    document.querySelectorAll('.product-card, .section-header, .contact-link').forEach(el => {
        observer.observe(el);
    });
}

// Initialize intersection observer when DOM is ready
if ('IntersectionObserver' in window) {
    document.addEventListener('DOMContentLoaded', observeElements);
}

// Error handling for images
document.addEventListener('DOMContentLoaded', function() {
    // Create placeholder image if it doesn't exist
    const img = new Image();
    img.onload = function() {
        // Placeholder exists, do nothing
    };
    img.onerror = function() {
        // Create a simple placeholder
        createPlaceholderImage();
    };
    img.src = 'assets/images/placeholder.jpg';
});

function createPlaceholderImage() {
    // Create a canvas-based placeholder
    const canvas = document.createElement('canvas');
    canvas.width = 400;
    canvas.height = 300;
    const ctx = canvas.getContext('2d');
    
    // Draw placeholder background
    ctx.fillStyle = '#CFBB99';
    ctx.fillRect(0, 0, 400, 300);
    
    // Draw text
    ctx.fillStyle = '#4C3D19';
    ctx.font = '20px Poppins, sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText('BeyChoc', 200, 140);
    ctx.font = '14px Poppins, sans-serif';
    ctx.fillText('Image Not Available', 200, 170);
    
    // Convert to blob and create URL
    canvas.toBlob(function(blob) {
        const placeholderURL = URL.createObjectURL(blob);
        // Store for use in error handlers
        window.placeholderImageURL = placeholderURL;
    });
}

// Add CSS for error and loading states
const additionalCSS = `
.error-message {
    text-align: center;
    padding: 3rem;
    background: #fff;
    border-radius: 10px;
    box-shadow: var(--shadow);
    grid-column: 1 / -1;
}

.error-message p {
    color: var(--chocolate-brown);
    margin-bottom: 1rem;
    font-size: 1.1rem;
}

.retry-btn {
    padding: 0.8rem 2rem;
    background: var(--gradient-primary);
    color: white;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-weight: 600;
    transition: var(--transition);
}

.retry-btn:hover {
    background: var(--gradient-secondary);
    transform: translateY(-2px);
}

.no-products {
    text-align: center;
    padding: 3rem;
    grid-column: 1 / -1;
}

.no-products p {
    color: var(--olive);
    font-size: 1.1rem;
}
`;

// Inject additional CSS
const style = document.createElement('style');
style.textContent = additionalCSS;
document.head.appendChild(style);

// Export functions for global use
window.scrollToSection = scrollToSection;
window.showProductDetails = showProductDetails;
