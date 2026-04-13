# 🚀 SETUP GUIDE - Luxury Fashion WooCommerce Theme

Complete installation and configuration instructions for your luxury fashion e-commerce store.

---

## Table of Contents

1. [Quick Start (5 minutes)](#quick-start)
2. [Installation Steps](#installation-steps)
3. [Plugin Setup](#plugin-setup)
4. [WooCommerce Configuration](#woocommerce-configuration)
5. [Adding Your First Product](#adding-your-first-product)
6. [Testing Before Going Live](#testing-before-going-live)

---

## Quick Start

### 1. Install WordPress & WooCommerce

```bash
# Access your WordPress admin dashboard
http://localhost:9000/wp-admin

# OR access via browser and navigate to wp-admin/login.php
```

**Login Credentials:** (from your initial setup)
- Username: [your username]
- Password: [your password]

### 2. Upload Theme Files

**Method A: WordPress Admin (Easiest)**

1. Go to **Appearance → Themes → Add New**
2. Click **Upload Theme** button
3. Choose `wordpress-theme.zip` from your computer
4. Click **Install Now** then **Activate**

**Method B: Manual Upload (Advanced)**

```bash
# Copy theme directory to WordPress installation
cp -r ~/luxury-fashion-woocommerce/wordpress-theme /path/to/your/wordpress/wp-content/themes/luxury-fashion-child

# Or if using Docker container:
sudo docker cp ~/luxury-fashion-woocommerce/wordpress-theme/* wp-dev-wp:/wp-content/themes/luxury-fashion-child/
```

Then activate theme in WordPress admin:
1. Go to **Appearance → Themes**
2. Find "Luxury Fashion Child Theme"
3. Click **Activate**

---

## Installation Steps

### Step 1: Verify Theme Activation

After uploading, confirm you see the luxury aesthetic:
- Clean white background with subtle gray sections (#f8f8f8)
- Deep charcoal/black buttons (#1a1a1a)
- Product grid with smooth hover effects
- Cart drawer accessible from top-right cart icon

### Step 2: Install Required Plugins

Go to **Plugins → Add New** and install these:

#### Essential Free Plugins (Install All)

| Plugin Name | Author | Purpose | Priority |
|-------------|--------|---------|----------|
| WooCommerce | Automattic | E-commerce engine | ⭐⭐⭐⭐⭐ |
| YITH WooCommerce Products Filter | YITH | Product filtering by size/color/price | ⭐⭐⭐⭐⭐ |
| ShortPixel Image Optimizer | SumoDigital | WebP compression, lazy loading | ⭐⭐⭐⭐⭐ |

#### Premium Plugin (Recommended)

| Plugin Name | Cost | Why It's Worth It | Priority |
|-------------|------|-------------------|----------|
| WP Rocket | $59/year | Best caching + minification for luxury sites | ⭐⭐⭐⭐ |

**To Install:**
1. Search plugin name in "Search plugins before installing" box
2. Click **Install Now** button
3. Once installed, click **Activate**
4. Complete any setup wizards that appear

---

## Plugin Setup Guide

### WooCommerce Installation (5 minutes)

#### 1. Run WooCommerce Setup Wizard

After activating WooCommerce:

```bash
# A new popup will appear - complete these steps:
1. Business details → Select country, currency (USD/EUR/etc.)
2. Product types → Physical products only for fashion
3. Payment methods → Enable Stripe/PayPal if available
4. Shipping zones → Set up your delivery areas
```

#### 2. Configure Essential Settings

**Products Tab:**
- Go to **WooCommerce → Settings → Products**
- Under "General settings", enable: ☑ "Enable product search by SKU"
- Leave other options at defaults for now

**Inventory Tab:**
- Under "Stock management", enable: ☑ "Manage stock quantity"
- Set "Low stock threshold" to **5 items** (you'll get alerts)
- Click **Save changes**

#### 3. Enable Payment Gateways

Go to **WooCommerce → Settings → Payments**:

```bash
# For Stripe (recommended):
1. Click "Set up" next to "Credit cards (Stripe)"
2. Follow the setup wizard to connect your Stripe account
3. Set as default payment method if you prefer it first

# For PayPal:
1. If available in your country, click "Set up" for PayPal
2. Connect your PayPal Business account
```

#### 4. Configure Shipping Zones

Still in **Payments** section → **Shipping**:

```bash
# Create shipping zones based on delivery areas:

Zone 1 - Local Pickup (Free)
- Rate: "Free local pickup"
- Applicable for customers in same city as your store
- Processing time: Same day if in stock

Zone 2 - Domestic Standard ($15, 3-5 business days)
- Flat rate shipping: $15.00
- Processing time: 1-2 business days

Zone 3 - International Express ($40, 7-14 business days)
- Flat rate shipping: $40.00
- Applicable for international customers
```

---

## Adding Your First Product (10 minutes)

### Step 1: Create Basic Product Information

1. Go to **Products → Add New**
2. Enter these details:

```
Product Name: [e.g., "The Silk Evening Dress"]
Short Description: [Brief overview - optional]
Full Description: [Detailed product story, fabric care, styling tips]
Category: [e.g., "Dresses", "Accessories", "Outerwear"]
Tags: [e.g., "evening wear", "silk", "luxury", "black dress"]
```

### Step 2: Upload Product Images (CRITICAL!)

**Image Specifications:**
- **Format:** WebP or JPG
- **Size:** 1920x2400px (vertical fashion ratio)
- **File size:** <500KB each
- **Background:** Clean, neutral, or lifestyle shot
- **Quality:** Professional studio photography

**Upload Process:**
1. Click **Set product image** → Upload main gallery photo
2. Click **Add product gallery images** → Add additional angles/details
3. Ensure all images are high-quality (no phone photos!)

### Step 3: Set Product Variants (Size & Color)

#### Create Variable Product:

```bash
1. Scroll down to "Product Data" section at bottom of page
2. Select "Variable product" from dropdown menu
3. Click "Add variation" button
```

**For Size Variations:**
- Add each size option individually (XS, S, M, L, XL)
- For each size, set:
  - Regular price: $[Enter price]
  - Stock quantity: [e.g., 15 units per size]
  - SKU: [Unique identifier like "DRESS-SILK-BLACK-M"]

**For Color Variations:**
- Add color swatch images (small square, ~200x200px)
- Enter clear color names ("Midnight Blue" not just "Blue")
- Set stock quantities per color

#### Example: Creating All Size/Color Combinations

If you have 5 sizes × 2 colors = 10 variations to create:

```bash
For EACH combination, click "Add variation":
1. Select size from dropdown (or enter manually)
2. Enter price for this specific variant (optional - can use base price)
3. Set stock quantity
4. Add SKU if needed
5. Click "Save changes" after each

Repeat until all combinations are created!
```

### Step 4: Configure Additional Information

**Important Fields:**

1. **Size Guide Link:**
   - Go to **Products → Attributes** (sidebar menu)
   - Create attribute named "Size Guide" with link to sizing chart
   - Back in product editor, add this link to description or use custom field plugin

2. **Care Instructions:**
   ```html
   <h4>Care Instructions</h4>
   <ul>
     <li>Dry clean only</li>
     <li>Do not bleach</li>
     <li>Lay flat to dry</li>
     <li>Store on padded hanger</li>
   </ul>
   ```

3. **Product Details:**
   - Material composition (e.g., "100% Silk")
   - Country of origin
   - Measurements/guide link

### Step 5: Set Pricing & Inventory

```bash
Regular Price: $[Enter price]
Sale Price: [Leave blank unless on sale]
Stock Status: In Stock / Out of Stock / Backorder (select as needed)
Manage Stock: ☑ Yes (to track inventory levels)
SKU: [Unique product identifier - e.g., "DRESS-SILK-BLACK-M"]
Low Stock Threshold: 5 items
```

### Step 6: Publish!

Click the green **Publish** button at top right.

Your product is now live on your website! 🎉

---

## Testing Before Going Live

### Checklist (Do NOT skip - critical!)

#### Functionality Tests

☐ **Add Product to Cart:**
   - Select different sizes and colors
   - Verify "Add to Cart" button works for each variant
   - Check that mini-cart drawer appears smoothly

☐ **Complete Checkout Flow:**
   - Go through entire checkout process with test payment
   - Use PayPal sandbox or Stripe test mode if available
   - Verify order confirmation email is received
   - Check shipping cost calculation is correct

☐ **Mobile Responsiveness Test:**
   - Open site on actual mobile device (not just browser dev tools)
   - Scroll through product catalog smoothly
   - Try adding items to cart from different pages
   - Complete purchase flow on phone

#### Content Quality Checks

☐ **Product Images:**
   - All images load without errors
   - No broken image icons visible
   - Gallery navigation works (thumbnails, zoom)

☐ **Text & Formatting:**
   - Product descriptions display properly
   - No spelling/grammar errors
   - Links work correctly

### Performance Testing

#### Google PageSpeed Insights

1. Go to https://developers.google.com/speed/pagespeed/insights/
2. Enter your homepage URL
3. Run test for **Mobile** and **Desktop** separately
4. Aim for:
   - Mobile score: 90+
   - Desktop score: 85+

#### GTmetrix (Optional)

1. Go to https://gtmetrix.com/
2. Enter your site URL
3. Check overall grade and waterfall chart
4. Look for optimization opportunities

### Security Checklist

☐ **SSL Certificate:** Verify HTTPS is enabled (green lock in browser)
☐ **WordPress Updated:** All plugins updated to latest versions
☐ **Strong Passwords:** Admin accounts use strong, unique passwords
☐ **Backup Created:** Full site backup stored securely before launch

---

## Troubleshooting Common Issues

### Issue: Cart Drawer Not Appearing

**Solution:**
1. Open browser console (F12) and check for JavaScript errors
2. Verify jQuery is loaded properly (check page source for `$` symbol)
3. Try clearing cache + hard refresh (Ctrl+Shift+R on Windows, Cmd+Shift+R on Mac)
4. If using caching plugin, purge all caches

### Issue: Product Images Not Loading

**Solution:**
1. Check image file paths in product editor are correct
2. Verify images aren't too large (>5MB may cause issues)
3. Try regenerating thumbnails: Install "Regenerate Thumbnails" plugin
4. Clear browser cache and try again

### Issue: Styles Look Broken or Missing

**Solution:**
1. Ensure theme is properly activated (Appearance → Themes)
2. Check if parent theme (Astra/GeneratePress) is installed
3. Purge all caches (WordPress, ShortPixel, WP Rocket if using)
4. Hard refresh browser (Ctrl+Shift+R / Cmd+Shift+R)

### Issue: Products Not Appearing in Shop

**Solution:**
1. Check product status is "Published" not "Draft"
2. Verify products are assigned to a category
3. Clear WooCommerce cache: **WooCommerce → Settings → Advanced → Permalink Structure → Save Changes**
4. Visit shop page directly (not via navigation menu)

---

## Next Steps After Setup

1. **[Read ADMIN_WALKTHROUGH.md](./ADMIN_WALKTHROUGH.md)** - Learn how to manage your store daily
2. **Add More Products** - Upload at least 5-10 products for a complete catalog feel
3. **Set Up Analytics** - Install Google Analytics 4 before going live
4. **Test on Multiple Devices** - Verify site works perfectly on iPhone, Android, tablet

---

## Support Resources

### Official Documentation

- [WooCommerce Docs](https://woocommerce.com/documentation/)
- [WordPress.org Forums](https://wordpress.org/support/forums/)
- [Stack Exchange WordPress](https://stackoverflow.com/questions/tagged/wordpress)

### Community Help

If you encounter issues not covered here:

1. Check error logs at **WooCommerce → Status → Logs**
2. Review browser console for JavaScript errors (F12)
3. Search the community forums using your specific error message
4. Contact hosting provider support if server-related issue

---

## Congratulations! 🎉

Your luxury fashion e-commerce store is now fully operational! 

Remember: **Consistency is key** to running a successful online store. Check orders daily, update inventory weekly, and review performance monthly.

Good luck with your brand! ✨👗🧥👜
