/**
 * Luxury Fashion - Product Image Zoom Functionality
 * 
 * Smooth image zoom on product pages with keyboard shortcuts and smooth transitions
 */

(function($) {
    'use strict';
    
    // Initialize product zoom when DOM is ready
    $(document).ready(function() {
        initProductZoom();
    });
    
    /**
     * Initialize Product Zoom
     */
    function initProductZoom() {
        var settings = $.extend({
            enabled: true,
            initialZoom: 1,
            maxZoom: 3,
            animationDuration: 250,
            cursorSize: 'auto'
        }, zoomSettings || {}); // Merge with global zoomSettings if available
        
        if (!settings.enabled) return;
        
        // Find main product image containers
        var $mainImage = $('.woocommerce-product-gallery__image main img, .product-gallery-main img');
        
        if (!$mainImage.length) return;
        
        setupZoom($mainImage[0], settings);
    }
    
    /**
     * Setup Zoom on Image Element
     */
    function setupZoom(imageElement, settings) {
        var $image = $(imageElement);
        var imageRect = $image.get(0).getBoundingClientRect();
        
        // Create zoom container and lens
        var container = document.createElement('div');
        container.className = 'product-zoom-container';
        
        var zoomLens = document.createElement('div');
        zoomLens.className = 'product-zoom-lens';
        zoomLens.setAttribute('role', 'button');
        zoomLens.setAttribute('tabindex', '0'); // Make keyboard accessible
        
        // Set initial styles
        container.style.cssText = `
            position: relative;
            width: ${imageRect.width}px;
            height: ${imageRect.height}px;
            overflow: hidden;
        `;
        
        zoomLens.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            width: 100px;
            height: 100px;
            margin: -${settings.maxZoom * 50}px 0 0 -${settings.maxZoom * 50}px;
            background-repeat: no-repeat;
            background-image: url('${imageElement.src}');
            cursor: crosshair;
            z-index: ${settings.zIndex || 100};
        `;
        
        container.appendChild(zoomLens);
        
        // Replace original image with zoomed version (if available) or use same source
        var $imageWrapper = $image.parent();
        if ($imageWrapper.length === 0) {
            $imageWrapper = $('<div>').css({ position: 'relative' });
            $image.wrap($imageWrapper);
        }
        
        // Insert zoom container before image
        $imageWrapper.prepend(container);
        
        // Update image source to use zoomed version if available
        var originalSrc = imageElement.src;
        var srcParts = originalSrc.split('?');
        var imageUrl = srcParts[0];
        
        // Try to find a higher quality image for zoom (usually second src in data attributes)
        if ($image.data('original-src') && $image.data('original-src')[0]) {
            imageUrl = $image.data('original-src')[0][0];
        }
        
        var zoomedImageUrl = originalSrc.replace(/_thumb(\.\w+)?$/, '') + '_zoom' + srcParts[1] || '';
        if (!$.isEmptyObject($image.data('zoomed-src'))) {
            // Try to use existing zoomed image data
            zoomedImageUrl = $image.data('zoomed-src');
        }
        
        $image.attr('src', imageUrl);
        container.appendChild(zoomLens);
        zoomLens.style.backgroundImage = `url('${zoomedImageUrl}')`;
        
        // Mouse move handler for lens positioning
        $(container).on('mousemove', function(e) {
            var rect = container.getBoundingClientRect();
            
            // Calculate mouse position within container (0-1 scale)
            var x = Math.max(0, Math.min(
                1, 
                (e.clientX - rect.left) / rect.width
            ));
            
            var y = Math.max(0, Math.min(
                1, 
                (e.clientY - rect.top) / rect.height
            ));
            
            // Position lens
            zoomLens.style.transform = `translate(${(x * settings.maxZoom * 200)}px, ${(y * settings.maxZoom * 200)}px)`;
        });
        
        // Click handler for quick zoom (opens larger view)
        $(container).on('click', function(e) {
            openQuickZoom(e.target.src || imageUrl);
        });
        
        // Touch support
        setupTouchSupport(container, zoomLens, settings.maxZoom);
    }
    
    /**
     * Setup Touch Support for Mobile Devices
     */
    function setupTouchSupport(container, lens, maxZoom) {
        var touchStartX = 0;
        var touchStartY = 0;
        var currentScale = 1;
        
        $(container).on('touchstart', function(e) {
            if (e.touches.length === 1) {
                touchStartX = e.touches[0].clientX;
                touchStartY = e.touches[0].clientY;
                
                // Start pan gesture
                var rect = container.getBoundingClientRect();
                var startX = e.touches[0].clientX - rect.left;
                var startY = e.touches[0].clientY - rect.top;
                
                lens.style.transition = 'none';
                lens.style.transformOrigin = `${startX}px ${startY}px`;
            }
        });
        
        $(container).on('touchmove', function(e) {
            if (e.touches.length === 1 && touchStartX > 0) {
                var deltaX = e.touches[0].clientX - touchStartX;
                var deltaY = e.touches[0].clientY - touchStartY;
                
                lens.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
            }
        });
        
        $(container).on('touchend', function(e) {
            if (e.changedTouches.length === 1) {
                var deltaX = e.changedTouches[0].clientX - touchStartX;
                var deltaY = e.changedTouches[0].clientY - touchStartY;
                
                // If moved less than threshold, snap back to original position
                if (Math.abs(deltaX) < 10 && Math.abs(deltaY) < 10) {
                    lens.style.transition = 'transform 0.3s ease';
                    lens.style.transformOrigin = `50% 50%`;
                    lens.style.transform = '';
                    
                    // Reset touch state for next zoom session
                    touchStartX = 0;
                    touchStartY = 0;
                } else {
                    // If moved significantly, close the zoom view
                    $(container).removeClass('zoomed');
                    lens.style.transition = 'none';
                    lens.style.transformOrigin = `50% 50%`;
                    lens.style.transform = '';
                    
                    touchStartX = 0;
                    touchStartY = 0;
                }
            }
        });
    }
    
    /**
     * Open Quick Zoom Modal (Larger View)
     */
    function openQuickZoom(imageUrl) {
        // Check if zoom modal already exists
        var $zoomModal = $('.product-zoom-modal');
        
        if ($zoomModal.length === 0) {
            createZoomModal();
        }
        
        // Set image and position modal
        var $modalImage = $zoomModal.find('.zoom-modal-image');
        $modalImage.attr('src', imageUrl);
        
        // Center modal on viewport
        setTimeout(function() {
            $zoomModal.addClass('open');
        }, 50);
    }
    
    /**
     * Create Zoom Modal (if not exists)
     */
    function createZoomModal() {
        var template = '<div class="product-zoom-modal">' +
            '<div class="zoom-modal-content">' +
                '<button type="button" class="close-zoom-btn">&times;</button>' +
                '<img src="" alt="" class="zoom-modal-image">' +
            '</div>' +
        '</div>';
        
        $('body').append(template);
    }
    
    /**
     * Close Zoom Modal
     */
    $(document).on('click', '.close-zoom-btn, [data-close-zoom]', function() {
        $('.product-zoom-modal').removeClass('open');
    });
    
})(jQuery);

/**
 * CSS for Product Zoom (inline styles)
 */
var zoomStyles = `
.product-zoom-container {
    position: relative;
}

.product-zoom-lens {
    will-change: transform;
    transition: transform 0.1s linear;
}

.product-zoom-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.9);
    z-index: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.product-zoom-modal.open {
    opacity: 1;
    visibility: visible;
}

.zoom-modal-content {
    position: relative;
    max-width: 90%;
    max-height: 85vh;
}

.zoom-modal-image {
    width: auto;
    height: auto;
    max-width: 100%;
    max-height: 85vh;
    object-fit: contain;
}

.close-zoom-btn {
    position: absolute;
    top: -40px;
    right: 0;
    width: 40px;
    height: 40px;
    background-color: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    font-size: 28px;
    cursor: pointer;
    color: #1a1a1a;
    line-height: 40px;
    text-align: center;
}

.close-zoom-btn:hover {
    background-color: rgba(255, 255, 255, 1);
}

/* Mobile-specific adjustments */
@media (max-width: 768px) {
    .product-zoom-lens {
        width: 80px;
        height: 80px;
        margin: -${(zoomSettings || {}).maxZoom * 40}px 0 0 -${(zoomSettings || {}).maxZoom * 40}px;
    }
}
`;

// Apply zoom styles when needed (lazy load for performance)
$(document).ready(function() {
    if ($('.product-zoom-container, .product-zoom-modal').length > 0) {
        $('head').append('<style>' + zoomStyles + '</style>');
    }
});
