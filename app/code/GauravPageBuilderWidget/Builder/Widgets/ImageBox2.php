<?php
namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;

class ImageBox2 extends AbstractWidget
{
    const NAME = 'vendor_custom_image_box';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getTitle(): string
    {
        return __('Image Box 2');
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
            'default' => __('Title Text'),
        ]);
        $this->addControl('subtitle', [
            'label' => __('Sub Title'),
            'type' => Controls::TEXT,
            'default' => __('Subtitle Text'),
        ]);
        $this->addControl("image", [
            'label' => __("Image"),
            'type'  => Controls::MEDIA,
        ]);

        $this->endControlsSection();
    }

       protected function contentTemplate()
    {
        ?>
            <div class="offer-card">
                <h5 class="offer-title">{{{settings.title}}}</h5>
                <p class="offer-price">{{{settings.subtitle}}}</p>
                <img src="{{{settings.image.url}}}" class="offer-product" alt="image">
            </div>
        <?php
    }

    protected function render(): string
    {
                $settings = $this->getSettingsForDisplay();
                        ob_start();
        ?>
            <div class="offer-card">
                <h5 class="offer-title"><?= $settings['title'] ?></h5>
                <p class="offer-price"><?= $settings['subtitle'] ?></p>
                <img src="<?= $settings['image']['url'] ?>" class="offer-product" alt="image">
            </div>
        <?php
        return ob_get_clean();
    }
}