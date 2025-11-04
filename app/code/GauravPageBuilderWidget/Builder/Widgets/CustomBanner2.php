<?php
namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;

class CustomBanner2 extends AbstractWidget
{
    const NAME = 'vendor_custom_banner_two';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getTitle(): string
    {
        return __('Custom Banner 2');
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
        $this->addControl('description', [
            'label' => __('Description'),
            'type' => Controls::TEXTAREA,
            'default' => __('Description Text'),
        ]);
        $this->addControl("banner_img", [
            'label' => __("Banner Image"),
            'type'  => Controls::MEDIA,
        ]);

        $this->endControlsSection();
    }

    protected function contentTemplate()
    {
        ?>
                <div class="main-title">
                    <h3>{{{settings.title}}}</h3>
                    <p>{{{settings.description}}}</p>
                    <img src="{{{settings.banner_img.url}}}" alt="img" class="img-fluid w-100">
                </div>
        <?php
    }

    protected function render(): string
    {
        $settings = $this->getSettings();
        ob_start();
        ?>
                <div class="main-title">
                    <h3><?= $settings['title']; ?></h3>
                    <p><?= $settings['description']; ?></p>
                    <img src="<?= $settings['banner_img']['url']; ?>" alt="img" class="img-fluid w-100">
                </div>
        <?php
        return ob_get_clean();
    }
}
