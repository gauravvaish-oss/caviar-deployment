<?php
namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;

class LogoSlider extends AbstractWidget
{
    const NAME = 'vendor_custom_logo_slider';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getTitle(): string
    {
        return __('Logo Slider');
    }

    public function getIcon(): string
    {
        return 'fa fa-sliders';
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

        // Allow up to 6 logos
        for ($i = 1; $i <= 6; $i++) {
            $this->addControl("logo_image_$i", [
                'label' => __("Logo $i"),
                'type'  => Controls::MEDIA,
            ]);
        }

        $this->addControl('slide_speed', [
            'label' => __('Slide Speed (ms)'),
            'type'  => Controls::TEXT,
            'default' => '3000',
        ]);

        $this->endControlsSection();
    }

   

    protected function render(): string
    {
        $settings = $this->getSettingsForDisplay();
        $slideSpeed = $settings['slide_speed'] ?? 3000;
// dd($settings);die;
        ob_start();
        ?>
        <section class="my-0 pt-0">
            <div class="row">
                <div class="col-md-12">
                    <div class="swiper logo_slider">
                        <div class="swiper-wrapper">
                            <?php for ($i = 1; $i <= 6; $i++): 
                                $imageData = $settings['logo_image_' . $i] ?? [];
                                $imageUrl = $imageData['url'] ?? '';
                                if (!$imageUrl) continue;
                            ?>
                                <div class="swiper-slide">
                                    <div class="slider_bg">
                                        <img src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="logo" class="img-fluid">
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <script>
            require(['jquery', 'swiper'], function($, Swiper) {
                new Swiper('.logo_slider', {
                    slidesPerView: 6,
                    spaceBetween: 10,
                    freeMode: true,
                    loop: true,
                    speed: <?= intval($slideSpeed); ?>,
                    observer: true,
                observeParents: true,
                    breakpoints: {
                        0: {           // 📱 mobile
                        slidesPerView: 4,
                        spaceBetween: 10
                    },
                    768: { // tablet and above
                        slidesPerView: 6,
                        autoplay: {
                            delay: 2000, // auto-slide every 2 seconds
                            disableOnInteraction: false
                        }
                    }
        }
                });
            });
        </script>
        <?php
        return ob_get_clean();
    }
}
