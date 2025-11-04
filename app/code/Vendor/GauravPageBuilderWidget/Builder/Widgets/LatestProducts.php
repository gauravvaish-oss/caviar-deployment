<?php
namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Helper\UrlBuilderHelper;
use Magento\Framework\App\ObjectManager;

class LatestProducts extends AbstractWidget
{
    const NAME = 'vendor_product_list_simple';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getTitle(): string
    {
        return __('Product List Simple');
    }

    public function getIcon(): string
    {
        return 'fa fa-list';
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
            'default' => __('Latest Product'),
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

    protected function render(): string
    {
        $settings = $this->getSettings();
        $productArray = isset($settings['product']) && is_array($settings['product'])
            ? array_filter(array_map('trim', $settings['product']))
            : [];

        $title = $settings['title'] ?? 'Latest Product';

        $om = ObjectManager::getInstance();
        $productRepo = $om->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $imageHelper = $om->get(\Magento\Catalog\Helper\Image::class);
        $assetRepo = $om->get(\Magento\Framework\View\Asset\Repository::class);

        ob_start();
        ?>

<div class="best_seller">
    <div class="main-title">
        <h4><?= htmlspecialchars($title) ?></h4>
    </div>

    <?php foreach ($productArray as $sku): ?>
        <?php
        try {
            $product = $productRepo->get($sku);
        } catch (\Exception $e) {
            continue;
        }
        if (!$product || !$product->getId()) continue;
        ?>

        <div class="product_detail">
            <a href="<?= $product->getProductUrl(); ?>">

                <img src="<?= $imageHelper->init($product, 'product_small_image')->getUrl(); ?>"
                     alt="<?= $product->getName(); ?>"
                     class="img-fluid product_img">

                <h6><?= $product->getName(); ?></h6>

                    <div class="stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="far fa-star"></i>
                    </div>
                <p>₹<?= number_format($product->getFinalPrice(), 2) ?></p>
            </a>
        </div>

    <?php endforeach; ?>
</div>

        <?php
        return ob_get_clean();
    }
}
