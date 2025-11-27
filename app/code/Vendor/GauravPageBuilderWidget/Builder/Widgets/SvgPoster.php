<?php
namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;

class SvgPoster extends AbstractWidget
{
    const NAME = 'vendor_custom_svg_poster';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getTitle(): string
    {
        return __('Svg Poster');
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

    // Start tabs for posters
    $this->startControlsTabs('posters_tabs');

    for ($i = 1; $i <= 3; $i++) {
        $this->startControlsTab("poster_svg_tab_$i", [
            'label' => __("Poster $i"),
        ]);

        $this->addControl("poster_svg_title_$i", [
            'label' => __("Title"),
            'type' => Controls::TEXT,
            'default' => __("Title Text $i"),
        ]);

        $this->addControl("poster_svg_description_$i", [
            'label' => __("Description"),
            'type' => Controls::TEXT,
            'default' => __("Subtitle Text $i"),
        ]);

        $this->addControl("poster_svg_image_$i", [
            'label' => __("Image"),
            'type' => Controls::MEDIA,
        ]);

        $this->endControlsTab();
    }

    $this->endControlsTabs();
    $this->endControlsSection();
}


       protected function contentTemplate()
    {
        ?>
        <section class="quality_warranty">
            <div class="row">
                <# for ( var i = 1; i <= 3; i++ ) { #>
                    <div class="col">
                        <ul>
                            <li>
                                <img src="{{ settings['poster_svg_image_' + i].url ? settings['poster_svg_image_' + i].url : 'images/default.png' }}" alt="img" class="img-fluid">
                            </li>
                            <li>
                                <h5>{{{ settings['poster_svg_title_' + i] }}}</h5>
                                <p>{{{ settings['poster_svg_description_' + i] }}}</p>
                            </li>
                        </ul>
                    </div>
                <# } #>
            </div>
                </section>
        <?php
    }

   protected function render(): string
{
    $settings = $this->getSettingsForDisplay();
    ob_start();
    ?>
    <section class="quality_warranty">
        <div class="row">
            <?php for ($i = 1; $i <= 3; $i++): ?>
                <?php 
                    $title = $settings['poster_svg_title_' . $i] ?? '';
                    $description = $settings['poster_svg_description_' . $i] ?? '';
                    $imageData = $settings['poster_svg_image_' . $i] ?? [];
                    $imageUrl = $imageData['url'] ?? 'images/default.png';
                ?>
                <div class="col">
                    <ul>
                        <li>
                            <img src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="img" class="img-fluid">
                        </li>
                        <li>
                            <h5><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h5>
                            <p><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></p>
                        </li>
                    </ul>
                </div>
            <?php endfor; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}


}