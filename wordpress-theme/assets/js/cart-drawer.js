/**
 * Luxury Fashion - Cart Drawer Functionality
 * 
 * Mini-cart drawer with smooth animations and real-time updates
 */

(function($) {
    'use strict';
    
    // Initialize cart drawer when DOM is ready
    $(document).ready(function() {
        initCartDrawer();
    });
    
    /**
     * Initialize Cart Drawer
     */
    function initCartDrawer() {
        var $cartDrawer = $('#cart-drawer');
        if (!$cartDrawer.length) return;
        
        // Store cart data reference
        window.luxuryCartData = {};
        
        // Setup event listeners
        setupEventListeners();
        
        // Initial cart count update (if AJAX cart counter exists)
        setTimeout(function() {
            updateCartCount();
        }, 100);
    }
    
    /**
     * Setup Cart Drawer Event Listeners
     */
    function setupEventListeners() {
        var $cartDrawer = $('#cart-drawer');
        
        // Open cart drawer when clicking cart icon or add-to-cart button
        $(document).on('click', '.cart-icon, .add-to-cart-button, [data-open-cart]', function(e) {
            e.preventDefault();
            
            if ($cartDrawer.hasClass('open')) return;
            
            $cartDrawer.addClass('open');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
            
            window.luxuryCartData = getCartContents();
        });
        
        // Close cart drawer when clicking close button or outside
        $(document).on('click', '.close-cart-btn, [data-close-cart]', function(e) {
            e.preventDefault();
            $cartDrawer.removeClass('open');
            document.body.style.overflow = '';
        });
        
        // Close on Escape key press
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $cartDrawer.hasClass('open')) {
                $cartDrawer.removeClass('open');
                document.body.style.overflow = '';
            }
        });
    }
    
    /**
     * Get Cart Contents from WooCommerce
     */
    function getCartContents() {
        // Use existing cart contents if available (AJAX)
        if (typeof wc_cart_fragments !== 'undefined' && wc_cart_fragments.get('contents')) {
            return JSON.parse(wc_cart_fragments.get('contents'));
        }
        
        // Fallback: use localStorage if exists
        var storedCart = localStorage.getItem('luxury-cart-contents');
        if (storedCart) {
            try {
                return JSON.parse(storedCart);
            } catch(e) {
                console.error('Error parsing cart data:', e);
            }
        }
        
        // Return empty array if no cart data
        return [];
    }
    
    /**
     * Update Cart Count Badge
     */
    function updateCartCount() {
        var $cartBadge = $('.cart-count');
        var cartContents = getCartContents();
        
        if ($cartBadge.length) {
            // Get total items from cart contents
            var itemCount = 0;
            
            $.each(cartContents, function(index, item) {
                itemCount += parseInt(item.quantity) || 0;
            });
            
            $cartBadge.text(itemCount);
        }
    }
    
    /**
     * Add Item to Cart (with drawer animation)
     */
    $(document).on('added_to_cart', function(event, fragments, cart_hash, response) {
        // Open the cart drawer when item is added
        var $cartDrawer = $('#cart-drawer');
        
        if ($cartDrawer.length && !$cartDrawer.hasClass('open')) {
            setTimeout(function() {
                $cartDrawer.addClass('open');
                document.body.style.overflow = 'hidden';
            }, 300); // Wait for add-to-cart animation to complete
        }
        
        // Update cart data
        window.luxuryCartData = getCartContents();
    });
    
    /**
     * Remove Item from Cart (with confirmation)
     */
    $(document).on('click', '.remove-from-cart, .cart-item-remove', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $row = $button.closest('.cart-item');
        var productId = $data || $button.data('product_id') || $button.attr('href').split('/').pop();
        
        // Show confirmation (optional - can remove for smoother UX)
        if (!window.luxuryCartData || !confirm('Remove this item from your bag?')) {
            return;
        }
        
        // Animate row removal
        $row.addClass('removing');
        
        setTimeout(function() {
            $row.fadeOut(300, function() {
                $(this).remove();
                
                // Update cart total after item removed
                updateCartTotal();
                
                // Close drawer if empty
                if ($('.cart-item').length === 0) {
                    setTimeout(function() {
                        $('#cart-drawer').removeClass('open');
                        document.body.style.overflow = '';
                    }, 300);
                }
            });
        }, 300);
    });
    
    /**
     * Update Cart Total Display
     */
    function updateCartTotal() {
        // Use WooCommerce's built-in total calculation if available
        if (typeof wc_cart_fragments !== 'undefined') {
            $.ajax({
                url: wc_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_cart_total'
                },
                success: function(response) {
                    $('.cart-subtotal .woocommerce-Price-amount').html(response);
                }
            });
        }
    }
    
})(jQuery);

/**
 * Cart Drawer HTML Template (for dynamic rendering)
 */
function getCartDrawerHTML() {
    return '<div id="cart-drawer" class="cart-drawer">' +
        
        // Header
        '<div class="cart-drawer-header">' +
            '<h3>Shopping Bag</h3>' +
            '<button type="button" class="close-cart-btn">&times;</button>' +
        '</div>' +
        
        // Cart Items Container
        '<div class="cart-items-container">' +
            '<p id="empty-cart-message" style="display:none;text-align:center;padding:3rem 0;color:#737373;">' +
                'Your bag is empty.' +
            '</p>' +
            '<ul id="cart-items-list"></ul>' +
        '</div>' +
        
        // Footer with Subtotal and Checkout
        '<div class="cart-drawer-footer">' +
            '<div class="cart-subtotal">' +
                '<span>Subtotal</span>' +
                '<span class="woocommerce-Price-amount"><ins>0.00</ins></span>' +
            '</div>' +
            '<a href="/checkout/" class="btn-primary" style="display:block;text-align:center;">Checkout &rsaquo;</a>' +
        '</div>' +
        
    '</div>';
}

// Make cart drawer template available globally
if (typeof window.luxuryCartTemplate === 'undefined') {
    window.luxuryCartTemplate = getCartDrawerHTML();
}
