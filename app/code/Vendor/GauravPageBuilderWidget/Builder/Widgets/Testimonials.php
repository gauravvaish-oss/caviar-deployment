<?php
declare(strict_types=1);

namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Elements\Repeater;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Exception\BuilderException;

class Testimonials extends AbstractWidget
{
    const NAME = 'vendor_testimonials_widget';

    public function getName() { return self::NAME; }
    public function getTitle() { return __('Testimonials Sidebar'); }
    public function getIcon() { return 'fa fa-bars'; }
    public function getCategories() { return ['general']; }

    /**
     * Register single menu item fields
     */
    public static function registerMenuItemInterface(ControlsStack $widget)
    {
        $options = [];
        for ($i = 1; $i <= 5; $i++) {
            $options[$i] = str_repeat('★', $i) . str_repeat('☆', 5 - $i);
        }

        $widget->addControl('testimonial_title', [
            'label' => __('Slide Title'),
            'type'  => Controls::TEXT,
            'default' => __('Menu Item'),
        ]);
        $widget->addControl('testimonial_user_name', [
            'label' => __('User Name'),
            'type'  => Controls::TEXT,
            'default' => __('User Name'),
        ]);
        $widget->addControl("testimonial_desc", [
            'label' => __("What they say"),
            'type' => Controls::TEXT,
            'default' => __("Subtitle Text"),
        ]);

        $widget->addControl("testimonial_img", [
            'label' => __("User Profile Image"),
            'type' => Controls::MEDIA,
        ]);
         $widget->addControl("testimonial_ratings", [
                'label' => __("Ratings"),
                'type' => Controls::SELECT,
                'options' => $options,
            ]);
    }

    /**
     * Register repeater with children
     */
    public static function registerMenuInterface(ControlsStack $widget)
    {
        // Child repeater
        $childRepeater = new Repeater();
        self::registerMenuItemInterface($childRepeater);

        // Parent repeater
        $parentRepeater = new Repeater();
        self::registerMenuItemInterface($parentRepeater);

        // Add children field inside parent repeater
        $parentRepeater->addControl(
            'children',
            [
                'label' => __('Children'),
                'type' => Controls::REPEATER,
                'fields' => $childRepeater->getControls(),
                'title_field' => '{{{ testimonial_title }}}',
            ]
        );

        // Register main repeater
        $widget->addControl(
            'menu_items', // name must match render() and contentTemplate()
            [
                'label' => __('Menu Items'),
                'type' => Controls::REPEATER,
                'fields' => $parentRepeater->getControls(),
                'title_field' => '{{{ testimonial_title }}}',
            ]
        );
    }

    protected function registerControls()
    {
        $this->startControlsSection(
            'section_menu',
            ['label' => __('Menu')]
        );

        self::registerMenuInterface($this);

        $this->endControlsSection();
    }

  protected function contentTemplate()
{
    ?>
    <div class="testimonial_swiper">
        <div class="swiper testimonialSwiper swiper-initialized swiper-horizontal swiper-backface-hidden">

            <div class="swiper-wrapper" aria-live="polite">

                <# if (settings.menu_items) { #>
                    <# _.each(settings.menu_items, function(parent, index) { #>

                        <div class="swiper-slide" 
                             role="group" 
                             aria-label="{{ index + 1 }} / {{ settings.menu_items.length }}"
                             style="width: 306px; margin-right: 20px;">

                            <div class="testimonial-card">

                                <h5 class="testimonial-heading">
                                    {{ parent.testimonial_title }}
                                </h5>

                                <# if (parent.testimonial_img && parent.testimonial_img.url) { #>
                                    <img src="{{ parent.testimonial_img.url }}" 
                                         class="testimonial-img" 
                                         alt="user">
                                <# } #>

                                <h6 class="testimonial-name">
                                    {{ parent.testimonial_user_name }}
                                </h6>

                                <!-- Ratings -->
                                <div class="testimonial-rating">
                                    <# 
                                        var rating = parent.testimonial_ratings ? parseFloat(parent.testimonial_ratings) : 0;
                                        for (var i = 1; i <= 5; i++) { 
                                    #>
                                        <# if (i <= rating) { #>
                                            <i class="fas fa-star"></i>
                                        <# } else if (i - rating === 0.5) { #>
                                            <i class="fas fa-star-half-alt"></i>
                                        <# } else { #>
                                            <i class="far fa-star"></i>
                                        <# } #>
                                    <# } #>
                                </div>

                                <p class="testimonial-text">
                                    “{{ parent.testimonial_desc }}”
                                </p>

                                <div class="testimonial-bg"></div>

                            </div>
                        </div>

                    <# }); #>
                <# } #>

            </div>

            <!-- Pagination -->
            <div class="swiper-pagination swiper-pagination-clickable swiper-pagination-bullets swiper-pagination-horizontal"></div>

            <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
        </div>
    </div>
    <?php
}


public function render()
{
    $settings = $this->getSettingsForDisplay();
    $menuItems = $settings['menu_items'] ?? [];
    // dd($menuItems);die;
    ?>

<div class="testimonial_swiper">
        <div class="swiper testimonialSwiper">

            <div class="swiper-wrapper">

                <?php if (!empty($menuItems)) : ?>
                    <?php foreach ($menuItems as $index => $parent) : ?>

                        <div class="swiper-slide">

                            <div class="testimonial-card">

                                <!-- Title -->
                                <?php if (!empty($parent['testimonial_title'])) : ?>
                                    <h5 class="testimonial-heading">
                                        <?php echo $parent['testimonial_title']; ?>
                                    </h5>
                                <?php endif; ?>

                                <!-- Image -->
                                <?php if (!empty($parent['testimonial_img']['url'])) : ?>
                                    <img src="<?php echo $parent['testimonial_img']['url']; ?>"
                                         class="testimonial-img"
                                         alt="testimonial">
                                <?php endif; ?>

                                <!-- User Name -->
                                <?php if (!empty($parent['testimonial_user_name'])) : ?>
                                    <h6 class="testimonial-name">
                                        <?php echo $parent['testimonial_user_name']; ?>
                                    </h6>
                                <?php endif; ?>

                                <!-- Ratings -->
                                <?php
                                $rating = isset($parent['testimonial_ratings'])
                                    ? floatval($parent['testimonial_ratings'])
                                    : 0;
                                ?>
                                <div class="testimonial-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= $rating): ?>
                                            <i class="fa fa-star"></i>
                                        <?php elseif ($i - $rating == 0.5): ?>
                                            <i class="fa fa-star-half-alt"></i>
                                        <?php else: ?>
                                            <i class="fa fa-star"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>

                                <!-- Description -->
                                <?php if (!empty($parent['testimonial_desc'])) : ?>
                                    <p class="testimonial-text">
                                        “<?php echo $parent['testimonial_desc']; ?>”
                                    </p>
                                <?php endif; ?>

                                <div class="testimonial-bg"></div>

                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php endif; ?>

            </div>

            <div class="swiper-pagination"></div>
        </div>
    </div>
<script type="text/javascript">
require([
    'jquery',
    'swiper'
], function ($, Swiper) {

    $(document).ready(function () {

        new Swiper(".testimonialSwiper", {
            loop: true,
            slidesPerView: 1,
            spaceBetween: 20,

            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },

            autoplay: {
                delay: 3000,
            },

            breakpoints: {
                768: {
                    slidesPerView: 1,
                    spaceBetween: 20,
                },
                1024: {
                    slidesPerView: 1,
                    spaceBetween: 20,
                }
            }
        });

    });

});
</script>

    <?php
}


}
