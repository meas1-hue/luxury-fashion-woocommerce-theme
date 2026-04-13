/**
 * Luxury Fashion Preview Site - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add simple hover effect logging for demo purposes
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach((card, index) => {
        card.addEventListener('mouseenter', function() {
            console.log(`Product ${index + 1} hovered - this is a preview!`);
        });
        
        card.addEventListener('mouseleave', function() {
            // Reset any hover states
            const overlay = card.querySelector('.add-to-cart-overlay');
            if (overlay) {
                overlay.style.transform = 'translateY(100%)';
            }
        });
    });

    // Newsletter form submission handler
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get email input
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value;
            
            // Simple validation
            if (!validateEmail(email)) {
                alert('Please enter a valid email address.');
                return;
            }
            
            // Show success message (in real site, would send to backend)
            alert(`Thank you for subscribing with ${email}!`);
            this.reset();
        });
    }

    // Add navigation scroll effect
    const navBar = document.querySelector('.nav-bar');
    if (navBar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navBar.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
            } else {
                navBar.style.boxShadow = 'none';
            }
        });
    }

    console.log('🎨 Luxury Fashion Preview Site Loaded Successfully!');
    console.log('This is a static preview of the WordPress theme.');
});

/**
 * Email validation helper function
 */
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
}
