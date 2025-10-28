<?php
namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Helper\UrlBuilderHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\ListProduct;
use Goomento\Core\Helper\ObjectManagerHelper;

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
                                <div class="col-lg-4 col-md-6 p-2">
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

    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $productRepository = $objectManager->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);
    $listBlock = $objectManager->get(\Magento\Catalog\Block\Product\ListProduct::class);
    $formKey = $objectManager->get(\Magento\Framework\Data\Form\FormKey::class)->getFormKey();
    $urlBuilder = $objectManager->get(\Magento\Framework\UrlInterface::class);
    $assetRepo = $objectManager->get(\Magento\Framework\View\Asset\Repository::class);
    $storeManager = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
    $mediaUrl = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

    $wishlistUrl = $urlBuilder->getUrl('wishlist/index/add');
    $compareUrl  = $urlBuilder->getUrl('catalog/product_compare/add');

    // Asset URLs for icons
    $eyeIcon     = $assetRepo->getUrl("Vendor_GauravPageBuilderWidget::images/eye.png");
    $heartIcon   = $assetRepo->getUrl("Vendor_GauravPageBuilderWidget::images/heart.png");
    $shuffleIcon = $assetRepo->getUrl("Vendor_GauravPageBuilderWidget::images/shuffle.png");
    $cartIcon    = $assetRepo->getUrl("Vendor_GauravPageBuilderWidget::images/cart.png");
    ob_start();
    ?>
    <section class="trending_product">
        <div class="main-title text-center mb-4">
            <?php if (!empty($settings['title'])): ?>
                <h3><?= htmlspecialchars($settings['title']); ?></h3>
            <?php endif; ?>
        </div>

        <div class="row">
            <?php foreach ($productArray as $sku):
                try {
                    $product = $productRepository->get($sku);
                } catch (\Exception $e) {
                    continue;
                }
                if (!$product || !$product->getId()) continue;

                $postParams = $listBlock->getAddToCartPostParams($product);
                $imageUrl = $mediaUrl . 'catalog/product' . $product->getImage();
                ?>
                <div class="col-lg-4 col-md-6 p-3">
                    <div class="product-card">
                        <div class="product-image">
                            <a href="<?= $product->getProductUrl() ?>">
                                <img class="product-img" src="<?= $imageUrl ?>" alt="<?= $product->getName() ?>">
                            </a>
                            <span class="discount-badge">New</span>
                            <div class="product-actions">

                                <!-- Quick View -->
                                <a href="<?= $product->getProductUrl() ?>" class="action-btn" title="Quick View">
                                    <img src="<?= $eyeIcon ?>" alt="Quick View">
                                </a>

                                <!-- Wishlist -->
                                <a href="#" class="action-btn towishlist" title="Add to Wishlist"
                                   data-post='<?= json_encode(['action' => $wishlistUrl, 'data' => ['product' => $product->getId()]]) ?>'>
                                    <img src="<?= $heartIcon ?>" alt="Add to Wishlist">
                                </a>

                                <!-- Compare -->
                                <a href="#" class="action-btn tocompare" title="Compare"
                                   data-post='<?= json_encode(['action' => $compareUrl, 'data' => ['product' => $product->getId()]]) ?>'>
                                    <img src="<?= $shuffleIcon ?>" alt="Compare">
                                </a>

                                <!-- Add to Cart -->
                                <form data-role="tocart-form" action="<?= $postParams['action'] ?>" method="post" style="display:inline-block;">
                                    <input type="hidden" name="product" value="<?= $postParams['data']['product'] ?>">
                                    <input type="hidden" name="<?= \Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED ?>" value="<?= $postParams['data'][\Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED] ?>">
                                    <input type="hidden" name="form_key" value="<?= $formKey ?>">
                                    <button type="submit" class="action-btn" title="Add to Cart">
                                        <img src="<?= $cartIcon ?>" alt="Add to Cart">
                                    </button>
                                </form>

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
                            <h5 class="product-title"><?= $product->getName() ?></h5>
                            <div class="product-price">
                                <span class="current-price">₹<?= number_format($product->getPrice(), 2, '.', '') ?></span>
                                <?php if ($product->getPrice() < $product->getFinalPrice()): ?>
                                    <span class="original-price">₹<?= number_format($product->getFinalPrice(), 2, '.', ''); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <script>
    require([
        'jquery', 
        'Magento_Customer/js/customer-data', 
        'Magento_Catalog/js/catalog-add-to-compare'
    ], function($, customerData){


            // Initialize Add to Cart forms
            $('[data-role="tocart-form"]').each(function(){
                if(typeof $(this).catalogAddToCart === 'function'){
                    $(this).catalogAddToCart();
                }
            });

            // Initialize Compare buttons
            $('.tocompare').catalogAddToCompare();

    });
    </script>
    <?php
    return ob_get_clean();
}






}
