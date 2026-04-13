<?php
/**
 * Luxury Fashion Child Theme Functions
 * 
 * Custom WooCommerce hooks, filters, and theme functionality
 * Designed for minimalist luxury fashion e-commerce
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Enqueue Parent Theme Styles & Assets
 */
function luxury_fashion_enqueue_styles() {
    // Get parent theme directory
    $parent_theme = wp_get_theme()->get_template();
    
    // Load WooCommerce styles (only on product pages)
    if (is_product() || is_add_to_cart_page()) {
        wp_enqueue_style(
            'woocommerce-styles',
            wc_get_theme_url('assets/css/' . apply_filters('woocommerce_parent_style', 'woocommerce.css')),
            array(),
            WC()->version
        );
    }
    
    // Load custom styles
    $asset_path = get_template_directory_uri() . '/assets/css/';
    wp_enqueue_style(
        'luxury-fashion-styles',
        $asset_path . 'main.css',
        array('woocommerce-styles'),
        file_get_contents(__DIR__ . '/../style.css')
    );
}
add_action('wp_enqueue_scripts', 'luxury_fashion_enqueue_styles');

/**
 * Enqueue Custom JavaScript
 */
function luxury_fashion_enqueue_scripts() {
    // Cart drawer functionality (only on cart/checkout pages)
    if (is_cart() || is_checkout()) {
        wp_enqueue_script(
            'luxury-fashion-cart',
            get_template_directory_uri() . '/assets/js/cart-drawer.js',
            array('jquery'),
            filemtime(__DIR__ . '/../assets/js/cart-drawer.js'),
            true
        );
    }
    
    // Product zoom functionality (on product pages)
    if (is_product()) {
        wp_enqueue_script(
            'luxury-fashion-zoom',
            get_template_directory_uri() . '/assets/js/product-zoom.js',
            array('jquery'),
            filemtime(__DIR__ . '/../assets/js/product-zoom.js'),
            true
        );
        
        // Pass zoom settings to JavaScript
        wp_localize_script('luxury-fashion-zoom', 'zoomSettings', array(
            'enabled' => true,
            'initialZoom' => 1,
            'maxZoom' => 3,
            'animationDuration' => 250
        ));
    }
}
add_action('wp_enqueue_scripts', 'luxury_fashion_enqueue_scripts');

/**
 * Add Custom Product Meta Box for Size Guide Link
 */
function luxury_fashion_add_size_guide_meta() {
    add_meta_box(
        'size-guide-link',
        __('Size Guide', 'luxury-fashion'),
        'render_size_guide_link_callback',
        'product',
        'side',
        'high'
    );
}
add_action('add_meta_boxes_product', 'luxury_fashion_add_size_guide_meta');

function render_size_guide_link_callback($post) {
    $size_guide_url = get_post_meta($post->ID, '_custom_size_guide_url', true);
    
    if (!$size_guide_url) {
        echo '<p>' . __('No size guide link set. Add one in Product Data > Additional Information.', 'luxury-fashion') . '</p>';
        return;
    }
    
    $guide_text = get_post_meta($post->ID, '_custom_size_guide_text', true);
    if (empty($guide_text)) {
        $guide_text = __('View Size Guide');
    }
    
    echo '<a href="' . esc_url($size_guide_url) . '" target="_blank" class="button-secondary">' . esc_html($guide_text) . '</a>';
}

/**
 * Register Custom Product Attributes (Size, Color Swatches)
 */
function luxury_fashion_register_product_attributes($attributes) {
    // Size attribute with custom display
    $size_options = array(
        'XS' => __('Extra Small', 'luxury-fashion'),
        'S'  => __('Small', 'luxury-fashion'),
        'M'  => __('Medium', 'luxury-fashion'),
        'L'  => __('Large', 'luxury-fashion'),
        'XL' => __('Extra Large', 'luxury-fashion')
    );
    
    $attributes['size'] = array(
        'name'          => 'Size',
        'label'         => __('Size', 'luxury-fashion'),
        'type'          => 'select', // or 'color' for swatches
        'description'   => __('Select your size', 'luxury-fashion'),
        'default'       => '',
        'required'      => true,
        'options'       => apply_filters('luxury_size_options', $size_options)
    );
    
    // Color attribute with visual swatches
    $color_options = array(
        'black'   => __('Black', 'luxury-fashion'),
        'white'   => __('White', 'luxury-fashion'),
        'beige'   => __('Beige', 'luxury-fashion'),
        'navy'    => __('Navy Blue', 'luxury-fashion')
    );
    
    $attributes['color'] = array(
        'name'          => 'Color',
        'label'         => __('Color', 'luxury-fashion'),
        'type'          => 'select', // Change to 'visual' for swatches (requires plugin)
        'description'   => __('Choose your color', 'luxury-fashion'),
        'default'       => '',
        'required'      => false,
        'options'       => apply_filters('luxury_color_options', $color_options)
    );
    
    return $attributes;
}
add_filter('woocommerce_product_attributes', 'luxury_fashion_register_product_attributes');

/**
 * Customize Product Page Layout - Minimalist Design
 */
function luxury_fashion_single_product_styles() {
    echo '<style>';
    echo '.single-product-container {';
    echo '    display: grid;';
    echo '    grid-template-columns: 1fr 1fr;';
    echo '    gap: 4rem;';
    echo '    padding: 3rem 0;';
    echo '}';
    
    echo '@media (max-width: 768px) {';
    echo '.single-product-container {';
    echo '    grid-template-columns: 1fr;';
    echo '    gap: 2rem;';
    echo '}';
    echo '}';
    
    // Clean product title styling
    echo 'h1.product_title {';
    echo '    font-size: clamp(2.5rem, 4vw + 1rem, 3.75rem);';
    echo '    line-height: 1.1;';
    echo '    margin-bottom: 0.5rem;';
    echo '}';
    
    // Clean price styling
    echo '.woocommerce-Price-price {';
    echo '    font-size: 1.5rem;';
    echo '    color: #1a1a1a;';
    echo '}';
    
    echo '</style>';
}
add_action('wp_head', 'luxury_fashion_single_product_styles');

/**
 * Hide WooCommerce Elements That Clutter the Design
 */
function luxury_fashion_hide_woocommerce_elements() {
    // Remove "Short description" section - we don't need it for fashion
    remove_meta_box('product_short_description', 'product', 'normal');
    
    // Clean up some default styles
    echo '<style>';
    echo '.woocommerce-message, .woocommerce-notices-wrapper {';
    echo '    background-color: #f8f8f8;';
    echo '    border-left: 4px solid #1a1a1a;';
    echo '}';
    
    // Clean up product page spacing
    echo '.product_meta, .woocommerce-extra-data {';
    echo '    margin-top: 2rem;';
    echo '    padding-top: 2rem;';
    echo '    border-top: 1px solid #e5e5e5;';
    echo '}';
    
    // Clean up "Related Products" section if using it
    echo '.related.products {';
    echo '    margin-top: 4rem;';
    echo '}';
    
    // Remove unnecessary padding from product page wrapper
    echo '#reorder, .woocommerce-product-details {';
    echo '    padding-bottom: 0 !important;';
    echo '}';
    
    echo '</style>';
}
add_action('wp_head', 'luxury_fashion_hide_woocommerce_elements');

/**
 * Add Quick View Button to Product Cards
 */
function luxury_fashion_add_quick_view_button($button) {
    $button .= '<a href="' . esc_url(apply_filters('the_permalink', get_permalink())) . '" class="btn-primary quick-view-btn" data-quick-view>Quick View</a>';
    
    return $button;
}
add_filter('woocommerce_template_loop_add_to_cart', 'luxury_fashion_add_quick_view_button');

/**
 * Customize Product Add to Cart Button Text
 */
function luxury_fashion_custom_add_to_cart_text() {
    global $product;
    
    // Different text for different products (customize per product type)
    if ($product->get_type() === 'variable') {
        echo __('Add to Bag', 'luxury-fashion');
    } else {
        echo __('Buy Now', 'luxury-fashion');
    }
}
add_filter('woocommerce_product_add_to_cart_text', 'luxury_fashion_custom_add_to_cart_text');

/**
 * Remove Quantity Input from Add to Cart (Cleaner Look)
 */
function luxury_fashion_remove_quantity_input() {
    remove_action('woocommerce_single_add_to_cart_form', 'woocommerce_quantity_input', 10, 3);
}
add_action('init', 'luxury_fashion_remove_quantity_input');

/**
 * Add Custom Product Gallery Layout
 */
function luxury_fashion_custom_product_gallery($columns) {
    // Remove thumbnail column if not needed (cleaner look)
    unset($columns['thumbnail']);
    
    return $columns;
}
add_filter('woocommerce_loop_item_columns', 'luxury_fashion_custom_product_gallery');

/**
 * Add Custom Checkout Field for Gift Message
 */
function luxury_fashion_add_gift_message_field() {
    if (is_checkout()) {
        ?>
        <div class="form-row form-row-wide">
            <label for="gift_message"><?php _e('Gift Message', 'luxury-fashion'); ?></label>
            <textarea id="gift_message" name="gift_message" cols="5" rows="3"></textarea>
        </div>
        
        <!-- Hide gift message if not on checkout -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Only show gift message field if order is for gift
                var giftMessage = document.querySelector('#gift_message');
                if (giftMessage && !window.location.href.includes('?order=0')) {
                    giftMessage.style.display = 'none';
                }
            });
        </script>
        <?php
    }
}
add_action('woocommerce_after_checkout_validation', 'luxury_fashion_add_gift_message_field');

/**
 * Add Custom Order Confirmation Email Template
 */
function luxury_fashion_customize_email_template() {
    if (is_admin()) return;
    
    // Remove default email styles and use our custom template
    wp_enqueue_style(
        'woocommerce-email-styles',
        get_template_directory_uri() . '/assets/css/email-template.css',
        array(),
        filemtime(__DIR__ . '/../assets/css/email-template.css')
    );
}
add_action('wp_head', 'luxury_fashion_customize_email_template');

/**
 * Add Custom Product Filter Sidebar (AJAX)
 */
function luxury_fashion_product_filter_sidebar() {
    ?>
    <div class="product-filters" id="product-filter-sidebar">
        <!-- Category Filters -->
        <div class="filter-group">
            <h4><?php _e('Category', 'luxury-fashion'); ?></h4>
            
            <?php 
            $categories = get_terms(array(
                'taxonomy'   => 'product_cat',
                'orderby'    => 'name',
                'hide_empty' => true,
            ));
            
            foreach ($categories as $category) {
                ?>
                <label class="filter-checkbox">
                    <input type="checkbox" name="filter_category[]" value="<?php echo esc_attr($category->term_id); ?>">
                    <span class="checkbox-indicator"></span>
                    <?php echo esc_html($category->name); ?>
                </label>
                <?php
            }
            ?>
        </div>
        
        <!-- Size Filters -->
        <div class="filter-group">
            <h4><?php _e('Size', 'luxury-fashion'); ?></h4>
            
            <?php foreach (array('XS', 'S', 'M', 'L', 'XL') as $size) { ?>
                <label class="filter-checkbox">
                    <input type="checkbox" name="filter_size[]" value="<?php echo esc_attr($size); ?>">
                    <span class="checkbox-indicator"></span>
                    <?php echo esc_html($size); ?>
                </label>
            <?php } ?>
        </div>
        
        <!-- Color Filters -->
        <div class="filter-group">
            <h4><?php _e('Color', 'luxury-fashion'); ?></h4>
            
            <?php foreach (array('Black', 'White', 'Beige', 'Navy') as $color) { ?>
                <label class="filter-checkbox">
                    <input type="checkbox" name="filter_color[]" value="<?php echo esc_attr($color); ?>">
                    <span class="checkbox-indicator"></span>
                    <?php echo esc_html($color); ?>
                </label>
            <?php } ?>
        </div>
        
        <!-- Price Range -->
        <div class="filter-group">
            <h4><?php _e('Price', 'luxury-fashion'); ?></h4>
            
            <input type="range" id="price-range" min="0" max="1000" step="10">
            <div class="price-display">
                <span id="min-price">$<span id="current-min">0</span></span> - 
                <span id="max-price">$<span id="current-max">1000</span></span>
            </div>
        </div>
        
        <!-- Filter Results -->
        <p class="filter-results"><?php _e('Showing all products', 'luxury-fashion'); ?></p>
    </div>
    
    <style>
        .product-filters {
            position: sticky;
            top: calc(3rem * 2);
            height: fit-content;
            background-color: #ffffff;
            padding: 1.5rem;
            border-radius: 4px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
        }
        
        .filter-group {
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e5e5e5;
        }
        
        .filter-group:last-child {
            border-bottom: none;
        }
        
        .filter-group h4 {
            font-size: 1rem;
            margin-bottom: 0.75rem;
        }
        
        .filter-checkbox {
            display: flex;
            align-items: center;
            padding: 0.25rem 0;
            cursor: pointer;
            color: #525252;
            font-size: 0.875rem;
        }
        
        .filter-checkbox input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            width: 1px;
            height: 1px;
            overflow: hidden;
        }
        
        .checkbox-indicator {
            width: 20px;
            height: 20px;
            border: 2px solid #d1d1d1;
            border-radius: 4px;
            margin-right: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .filter-checkbox input[type="checkbox"]:checked + .checkbox-indicator::after {
            content: '✓';
            color: #ffffff;
            font-size: 12px;
        }
        
        .checkbox-indicator.checked {
            background-color: #1a1a1a;
            border-color: #1a1a1a;
        }
    </style>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter checkboxes - add click handlers
        var checkboxes = document.querySelectorAll('.filter-checkbox input[type="checkbox"]');
        
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                updateFilterResults();
            });
        });
        
        function updateFilterResults() {
            // Count checked boxes
            var checkedCount = 0;
            checkboxes.forEach(function(cb) {
                if (cb.checked) checkedCount++;
            });
            
            // Update results count
            document.querySelector('.filter-results').textContent = 
                'Showing ' + checkedCount + ' filter' + (checkedCount !== 1 ? 's' : '') + ': ' + 
                Array.from(checkboxes).filter(cb => cb.checked).map(cb => 
                    document.querySelector(cb.closest('label') + ' .checkbox-indicator').textContent
                ).join(', ');
        }
    });
    </script>
    <?php
}

/**
 * Add Custom Product Image Zoom on Hover
 */
function luxury_fashion_product_image_zoom() {
    ?>
    <style>
        .product-gallery-main img, 
        .woocommerce-product-gallery__image main img {
            cursor: zoom-in;
            transition: transform 0.3s ease;
        }
        
        .product-gallery-main:hover img,
        .woocommerce-product-gallery__image main:hover img {
            transform: scale(1.1); /* Smooth zoom effect */
        }
    </style>
    <?php
}

/**
 * Add Custom Footer for Luxury Brand Feel
 */
function luxury_fashion_custom_footer() {
    ?>
    <footer style="text-align: center; padding: 3rem 0; border-top: 1px solid #e5e5e5; margin-top: 4rem;">
        <p style="color: #737373; font-size: 0.875rem; letter-spacing: 0.1em; text-transform: uppercase;">
            <?php echo esc_html(get_bloginfo('name')); ?> &copy; <?php echo date('Y'); ?> - <?php _e('Crafted with Precision', 'luxury-fashion'); ?>
        </p>
    </footer>
    <?php
}
add_action('wp_footer', 'luxury_fashion_custom_footer');

/**
 * Add Custom Product Page Meta Data (SKU, Barcode) Display
 */
function luxury_fashion_add_product_meta_display() {
    if (!is_product()) return;
    
    ?>
    <div class="product-meta" style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e5e5;">
        <?php 
        wc_get_template(
            'content-single-product.php', // Use default template
            array(
                'product' => get_the_product()
            )
        );
        ?>
    </div>
    <?php
}

/**
 * Optimize Product Page Load Performance
 */
function luxury_fashion_optimize_product_load() {
    // Defer non-critical JavaScript
    if (!is_admin()) {
        echo '<script>window.addEventListener("load",function(){document.getElementsByTagName("body")[0].className+=" lazyloaded";});</script>';
    }
}

/**
 * Add Custom Product Page Schema Markup (SEO)
 */
function luxury_fashion_product_schema_markup() {
    if (!is_product()) return;
    
    $product = get_the_product();
    ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org/",
        "@type": "Product",
        "name": "<?php echo esc_js($product->get_name()); ?>",
        "image": [
            "<?php echo esc_url(wp_get_attachment_image_src(get_post_thumbnail_id(), 'large')[0]); ?>"
        ],
        "description": "<?php echo wp_kses_post(strip_tags($product->get_short_description())); ?>",
        "sku": "<?php echo esc_js($product->get_sku()); ?>",
        "brand": {
            "@type": "Brand",
            "name": "<?php echo esc_js(get_bloginfo('name')); ?>"
        },
        "offers": {
            "@type": "Offer",
            "url": "<?php the_permalink(); ?>",
            "priceCurrency": "<?php echo esc_attr($product->get_price_currency()); ?>",
            "price": "<?php echo esc_attr(str_replace('$', '', $product->get_price())); ?>",
            "availability": "<?php echo $product->is_in_stock() ? 'http://schema.org/InStock' : 'http://schema.org/OutOfStock'; ?>"
        }
    }
    </script>
    <?php
}

/**
 * Add Custom Cart Drawer Functionality
 */
function luxury_fashion_cart_drawer_script() {
    ?>
    <style>
        /* Cart drawer styles - inline for performance */
        .cart-drawer {
            position: fixed;
            top: 0;
            right: calc(100vw - 450px);
            width: 450px;
            height: 100vh;
            background-color: #ffffff;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
            z-index: 400;
            transition: right 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        
        .cart-drawer.open {
            right: 0;
        }
        
        .cart-drawer-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e5e5;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .close-cart-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #737373;
            padding: 0.25rem;
        }
    </style>
    
    <script>
    // Cart drawer functionality
    (function() {
        var cartDrawer = document.querySelector('.cart-drawer');
        
        if (!cartDrawer) return;
        
        // Open/close functions
        function openCartDrawer() {
            cartDrawer.classList.add('open');
            setTimeout(function() {
                document.body.style.overflow = 'hidden';
            }, 300);
        }
        
        function closeCartDrawer() {
            cartDrawer.classList.remove('open');
            setTimeout(function() {
                document.body.style.overflow = '';
            }, 300);
        }
        
        // Add click handlers to trigger buttons
        var addToCartButtons = document.querySelectorAll('.single_add_to_cart_button, .add-to-cart-button');
        addToCartButtons.forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                openCartDrawer();
            });
        });
        
        // Close on close button click
        var closeBtn = cartDrawer.querySelector('.close-cart-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeCartDrawer);
        }
        
        // Close when clicking outside drawer
        document.addEventListener('click', function(e) {
            if (cartDrawer.classList.contains('open') && 
                !cartDrawer.contains(e.target) && 
                !e.target.closest('.add-to-cart-button')) {
                closeCartDrawer();
            }
        });
    })();
    </script>
    <?php
}

/**
 * Initialize Theme on Activation (Optional Setup Hooks)
 */
function luxury_fashion_activation() {
    // Set default theme options
    update_option('luxury_fashion_active', true);
    
    // Register custom post meta for size guides
    register_post_meta('product', '_custom_size_guide_url', array(
        'type'       => 'string',
        'description' => __('Size guide URL for product', 'luxury-fashion'),
        'single'     => true,
        'sanitize_callback' => 'esc_url_raw'
    ));
    
    register_post_meta('product', '_custom_size_guide_text', array(
        'type'       => 'string',
        'description' => __('Size guide button text', 'luxury-fashion'),
        'single'     => true,
        'sanitize_callback' => 'esc_attr'
    ));
    
    echo '<div class="notice notice-success is-dismissible">';
    echo '<p><strong>' . __('Luxury Fashion theme activated successfully!', 'luxury-fashion') . '</strong></p>';
    echo '</div>';
}
add_action('after_switch_theme', 'luxury_fashion_activation');

/**
 * Prevent Theme Switching (Optional - Uncomment if needed)
 */
/*
function luxury_fashion_prevent_switching() {
    switch_to_theme(get_template());
}
add_filter('switch_themes', 'luxury_fashion_prevent_switching');
*/
