<?php
namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Helper\UrlBuilderHelper;

class ProductRow extends AbstractWidget
{
    const NAME = 'vendor_custom_product_row';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getTitle(): string
    {
        return __('Product Row');
    }

    public function getIcon(): string
    {
        return 'fa fa-image';
    }

    public function getCategories(): array
    {
        return ['general'];
    }

    protected function registerControls()
    {

       $this->startControlsSection('content_section', [
            'label' => __('Content'),
            'tab'   => Controls::TAB_CONTENT,
        ]);

        $this->addControl('title', [
            'label' => __('Title'),
            'type' => Controls::TEXT,
            'default' => __('Trending Products'),
        ]);
        $this->addControl(
            'product',
            [
                'label' => __('Product SKU(s)'),
                'type' => Controls::SELECT2,
                'multiple' => true,
                'placeholder' => __('Type SKU ...'),
                'select2options' => [
                    'ajax' => [
                        'url' => UrlBuilderHelper::getUrl('pagebuilder/catalog/search')
                    ]
                ]
            ]
        );

        $this->endControlsSection();
    }

    protected function contentTemplate()
    {
        ?>
        <section class="trending_product">
            <div class="row">
                <div class="main-title w-100">
                    <h1 class="text-center pb-lg-4">{{{settings.title}}}</h1>
                </div>
                <!-- AJAX will append product cards here -->
            </div>
            <div class="loader" style="display:none; text-align:center; margin-top:20px;">
                Loading products...
            </div>
        </section>

        <script>
        require(['jquery', 'require'], function($) {
            $(document).ready(function () {
                var eyeIcon    = require.toUrl('Vendor_GauravPageBuilderWidget/images/eye.png');
                var heartIcon  = require.toUrl('Vendor_GauravPageBuilderWidget/images/heart.png');
                var shuffleIcon= require.toUrl('Vendor_GauravPageBuilderWidget/images/shuffle.png');
                var cartIcon   = require.toUrl('Vendor_GauravPageBuilderWidget/images/cart.png');

                var productSkus = "{{{settings.product}}}"; // comma-separated SKUs from widget
                var skuArray = productSkus ? productSkus.split(",") : [];

                var $container = $('.trending_product .row');
                var $loader = $('.trending_product .loader');

                if (!skuArray.length) return;

                // Show loader
                $loader.show();

                // Clear previous content
                $container.find('.product-card').remove();

                // Track completed AJAX requests
                var completed = 0;

                skuArray.forEach(function(sku) {
                    sku = sku.trim();
                    if (!sku) return;

                    $.ajax({
                        url: '/customgoomento/ajax/getproducts', // your controller URL
                        type: 'GET',
                        dataType: 'json',
                        data: { sku: sku },
                        success: function(response) {
                            completed++;

                            if (response.success) {
                                var p = response.product;

                                var html = `
                                <div class="col-lg-4 col-md-6">
                                    <div class="product-card">
                                        <div class="product-image">
                                            <img class="product-img" src="${p.image}" alt="${p.name}">
                                            <span class="discount-badge">New</span>
                                            <div class="product-actions">
                                                <button class="action-btn"><img src="${eyeIcon}" alt=""></button>
                                                <button class="action-btn"><img src="${heartIcon}" alt=""></button>
                                                <button class="action-btn"><img src="${shuffleIcon}" alt=""></button>
                                                <button class="action-btn"><img src="${cartIcon}" alt=""></button>
                                            </div>
                                        </div>
                                        <div class="product-info">
                                            <div class="product-rating">
                                                <div class="stars">
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="fas fa-star"></i>
                                                    <i class="far fa-star"></i>
                                                    <p class="star-qty">(3)</p>
                                                </div>
                                            </div>
                                            <h5 class="product-title">${p.name}</h5>
                                            <div class="product-price">
                                                <span class="current-price">₹${p.price}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                `;

                                $container.append(html);
                            } else {
                                console.warn('Product not found:', sku);
                            }

                            // Hide loader when all requests complete
                            if (completed === skuArray.length) {
                                $loader.hide();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error for SKU ' + sku + ':', xhr.responseText);
                            completed++;
                            if (completed === skuArray.length) {
                                $loader.hide();
                            }
                        }
                    });
                });
            });
        });
        </script>
        <?php
    }


    protected function render(): string
{
    $settings = $this->getSettings();
    $productArray = isset($settings['product']) && is_array($settings['product'])
        ? array_filter(array_map('trim', $settings['product']))
        : [];
    ob_start();
    ?>
    <section class="trending_product">
        <div class="main-title text-center mb-4">
            <?php if (!empty($settings['title'])): ?>
                <h3><?= htmlspecialchars($settings['title']); ?></h3>
            <?php endif; ?>
        </div>

        <div class="row">
            <!-- AJAX will render product cards here -->
        </div>

        <div class="loader" style="display:none; text-align:center; margin-top:20px;">
            Loading products...
        </div>
    </section>

    <script>
    require(['jquery', 'mage/url', 'require'], function($, urlBuilder) {
        $(document).ready(function () {
            var eyeIcon    = require.toUrl('Vendor_GauravPageBuilderWidget/images/eye.png');
            var heartIcon  = require.toUrl('Vendor_GauravPageBuilderWidget/images/heart.png');
            var shuffleIcon= require.toUrl('Vendor_GauravPageBuilderWidget/images/shuffle.png');
            var cartIcon   = require.toUrl('Vendor_GauravPageBuilderWidget/images/cart.png');

            var productSkus = <?= json_encode($productArray) ?>;
            var $container = $('.trending_product .row');
            var $loader = $('.trending_product .loader');

            $loader.show();
            $container.find('.product-card').remove();
            var completed = 0;

            productSkus.forEach(function(sku) {
                sku = sku.trim();
                if (!sku) return;

                $.ajax({
                    url: '/customgoomento/ajax/getproducts',
                    type: 'GET',
                    dataType: 'json',
                    data: { sku: sku },
                    success: function(response) {
                        completed++;

                        if (response.success) {
                            var p = response.product;

                            // ✅ Build URLs for actions
                            var viewUrl = p.url; // should come from backend (product page URL)
                            var wishlistUrl = urlBuilder.build('wishlist/index/add?product=' + p.id);
                            var compareUrl = urlBuilder.build('catalog/product_compare/add?product=' + p.id);
                            var addToCartUrl = urlBuilder.build('checkout/cart/add?product=' + p.id + '&qty=1');

                            var html = `
                            <div class="col-lg-4 col-md-6">
                                <div class="product-card">
                                    <div class="product-image">
                                        <a href="${viewUrl}">
                                            <img class="product-img" src="${p.image}" alt="${p.name}">
                                        </a>
                                        <span class="discount-badge">New</span>
                                        <div class="product-actions">
                                            <a class="action-btn" href="${viewUrl}" title="View Product"><img src="${eyeIcon}" alt=""></a>
                                            <a class="action-btn" href="${wishlistUrl}" title="Add to Wishlist"><img src="${heartIcon}" alt=""></a>
                                            <a class="action-btn" href="${compareUrl}" title="Compare"><img src="${shuffleIcon}" alt=""></a>
                                            <a class="action-btn add-to-cart" href="${addToCartUrl}" title="Add to Cart"><img src="${cartIcon}" alt=""></a>
                                        </div>
                                    </div>
                                    <div class="product-info">
                                        <div class="product-rating">
                                            <div class="stars">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="far fa-star"></i>
                                                <p class="star-qty">(3)</p>
                                            </div>
                                        </div>
                                        <h5 class="product-title"><a href="${viewUrl}">${p.name}</a></h5>
                                        <div class="product-price">
                                            <span class="current-price">₹${p.price}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            `;

                            $container.append(html);
                        } else {
                            console.warn('Product not found:', sku);
                        }

                        if (completed === productSkus.length) {
                            $loader.hide();
                        }
                    },
                    error: function(xhr) {
                        console.error('AJAX error for SKU ' + sku + ':', xhr.responseText);
                        completed++;
                        if (completed === productSkus.length) {
                            $loader.hide();
                        }
                    }
                });
            });

            // Optional: handle add-to-cart with AJAX (prevent full page reload)
            $(document).on('click', '.add-to-cart', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                $.ajax({
                    url: url,
                    type: 'POST',
                    success: function() {
                        alert('Product added to cart!');
                    },
                    error: function() {
                        alert('Failed to add to cart.');
                    }
                });
            });
        });
    });
    </script>
    <?php
    return ob_get_clean();
}


}
