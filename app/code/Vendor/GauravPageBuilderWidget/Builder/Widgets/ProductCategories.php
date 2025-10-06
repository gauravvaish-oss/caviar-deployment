<?php
declare(strict_types=1);

namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

class ProductCategories extends AbstractWidget
{
    const NAME = 'vendor__product_category_tabs';

    public function getName() { return self::NAME; }
    public function getTitle() { return __('Product Categories Tab'); }
    public function getIcon() { return 'fa fa-folder'; }
    public function getCategories() { return ['general']; }

    protected function registerControls()
    {
        $categorySource = ObjectManagerHelper::get(\Goomento\PageBuilder\Model\Config\Source\CatalogCategory::class);
        $categories = $categorySource->toOptionArray();

        $options = [];
        foreach ($categories as $cat) {
            $options[$cat['value']] = $cat['label'];
        }

        $this->startControlsSection('content_section', [
            'label' => __('Content'),
            'tab'   => Controls::TAB_CONTENT,
        ]);

        $this->addControl('title', [
            'label' => __('Title'),
            'type' => Controls::TEXT,
            'default' => __('Limited Time Offer'),
        ]);

        for ($i = 1; $i <= 5; $i++) {
            $this->addControl("category_{$i}", [
                'label' => __("Category Tab {$i}"),
                'type' => Controls::SELECT,
                'options' => $options,
            ]);
             $this->addControl("category_svg_{$i}", [
                'label' => __("Category SVG {$i}"),
                'type'  => Controls::MEDIA,
            ]);

        }

        $this->endControlsSection();
    }

    protected function contentTemplate()
    {
        ?>
<div class="col-md-12 Product_category">
    <div class="row">
        <div class="main-title ">
            <h2>{{{settings.title}}}</h2>
            <!-- Swiper Navigation -->
            <div class="swiper-nav">
                <div class="swiper-button-prev custom-prev" tabindex="0" role="button"></div>
                <div class="swiper-button-next custom-next" tabindex="0" role="button"></div>
            </div>
        </div>

        <!-- Category Buttons -->
        <div class="col-md-3 remove_padding">
            <div class="category_menu product_category">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <div class="toggle_section">
                        <button class="nav-link active" id="v-pills-one_product-tab" data-bs-toggle="pill" data-bs-target="#v-pills-one_product" type="button" role="tab" aria-controls="v-pills-one_product" aria-selected="true" data-product-category="{{{settings.category_1}}}"><img src="{{{settings.category_svg_1.url}}}" alt="">{{{settings.category_1}}}</button>
                        <button class="nav-link" id="v-pills-two_product-tab" data-bs-toggle="pill" data-bs-target="#v-pills-two_product" type="button" role="tab" aria-controls="v-pills-two_product" aria-selected="false" tabindex="-1" data-product-category="{{{settings.category_2}}}"><img src="{{{settings.category_svg_2.url}}}" alt="">{{{settings.category_2}}}</button>
                        <button class="nav-link" id="v-pills-three_product-tab" data-bs-toggle="pill" data-bs-target="#v-pills-three_product" type="button" role="tab" aria-controls="v-pills-three_product" aria-selected="false" tabindex="-1" data-product-category="{{{settings.category_3}}}"><img src="{{{settings.category_svg_3.url}}}" alt="">{{{settings.category_3}}}</button>
                        <button class="nav-link" id="v-pills-four_product-tab" data-bs-toggle="pill" data-bs-target="#v-pills-four_product" type="button" role="tab" aria-controls="v-pills-four_product" aria-selected="false" tabindex="-1" data-product-category="{{{settings.category_4}}}"><img src="{{{settings.category_svg_4.url}}}" alt="">{{{settings.category_4}}}</button>
                        <button class="nav-link" id="v-pills-five_product-tab" data-bs-toggle="pill" data-bs-target="#v-pills-five_product" type="button" role="tab" aria-controls="v-pills-five_product" aria-selected="false" tabindex="-1" data-product-category="{{{settings.category_5}}}"><img src="{{{settings.category_svg_5.url}}}" alt="">{{{settings.category_5}}}</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Swiper -->
        <div class="col-md-9">
            <div class="tab-content">
                <div class="tab-pane fade active show">
                    <div class="swiper productSwiper">
                        <div class="swiper-wrapper" id="product-category-swiper"></div>
                        <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
require([
    "jquery",
    "swiper",
    'require'  // ensure swiper is mapped in requirejs-config.js
], function ($, Swiper) {

    $(document).ready(function () {
        var swiperInstance = null;
        var eyeIcon    = require.toUrl('Vendor_GauravPageBuilderWidget/images/eye.png');
        var heartIcon  = require.toUrl('Vendor_GauravPageBuilderWidget/images/heart.png');
        var shuffleIcon = require.toUrl('Vendor_GauravPageBuilderWidget/images/shuffle.png');
        var cartIcon   = require.toUrl('Vendor_GauravPageBuilderWidget/images/cart.png');

        // 🔹 Function to init Swiper
        function initSwiper() {
            if (swiperInstance) {
                swiperInstance.destroy(true, true); // clean old instance
            }
            swiperInstance = new Swiper(".productSwiper", {
                slidesPerView: 2,
                spaceBetween: 20,
                navigation: {
                    nextEl: ".custom-next",
                    prevEl: ".custom-prev",
                },
                loop: true,
                observer: true,
                observeParents: true,
                 breakpoints: {
                    0: {           // 📱 mobile
                        slidesPerView: 1,
                        spaceBetween: 10
                    },
                    768: {         // 📱 tablet
                        slidesPerView: 2,
                        spaceBetween: 15
                    },
                    1024: {        // 💻 desktop
                        slidesPerView: 2,
                        spaceBetween: 20
                    }
                }
            });
        }

        // 🔹 Function to load products for a category
        function loadProducts(btn, categoryId) {
            $.ajax({
                url: "/customgoomento/category/getproducts",
                type: "GET",
                dataType: "json",
                data: { category_id: categoryId },
                beforeSend: function () {
                    $("#product-category-swiper").html("<p>Loading...</p>");
                },
                success: function (response) {
                    if (response.success) {
                        btn.text(response.category_name);

                        var html = "";
                        $.each(response.products, function (i, product) {
                            html += `
                                <div class="swiper-slide">
                                    <div class="product-card">
                                        <div class="product-image">
                                            <img class="product-img" src="${product.image}" alt="${product.name}">
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
                                            <h5 class="product-title"><a href="${product.url}">${product.name}</a></h5>
                                            <div class="product-price">
                                                <span class="current-price">₹ ${product.price}</span>
                                                <span class="original-price">₹350</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });

                        $("#product-category-swiper").html(html);

                        // ✅ Init Swiper after products loaded
                        initSwiper();
                    } else {
                        $("#product-category-swiper").html("<p>Error: " + response.message + "</p>");
                    }
                },
                error: function () {
                    $("#product-category-swiper").html("<p>Request failed!</p>");
                }
            });
        }

        // 🔹 Initial load for first tab
        var firstBtn = $(".toggle_section .nav-link").first();
        var firstCat = parseInt(firstBtn.attr("data-product-category"), 10);
        if (Number.isInteger(firstCat)) {
            loadProducts(firstBtn, firstCat);
        }

        // 🔹 On button click → load its products
        $(document).on("click", ".toggle_section .nav-link", function () {
            var btn = $(this);
            var num = parseInt(btn.attr("data-product-category"), 10);

            if (Number.isInteger(num)) {
                $(".toggle_section .nav-link").removeClass("active");
                btn.addClass("active");
                loadProducts(btn, num);
            }
        });

    });
});
</script>
<?php
    }

    protected function render(): string
    {
        $settings = $this->getSettingsForDisplay();

        $title = $settings['title'] ?? '';
        $cat1   = $settings['category_1'] ?? '';
        $cat2   = $settings['category_2'] ?? '';
        $cat3   = $settings['category_3'] ?? '';
        $cat4   = $settings['category_4'] ?? '';
        $cat5   = $settings['category_5'] ?? '';

        // Fetch category labels
        $categorySource = ObjectManagerHelper::get(\Goomento\PageBuilder\Model\Config\Source\CatalogCategory::class);
        $categories = array_column($categorySource->toOptionArray(), 'label', 'value');

        $cat1Label = $categories[$cat1] ?? '';
        $cat2Label = $categories[$cat2] ?? '';
        $cat3Label = $categories[$cat3] ?? '';
        $cat4Label = $categories[$cat4] ?? '';
        $cat5Label = $categories[$cat5] ?? '';

        ob_start();
        ?>

<div class="col-md-12 Product_category">
    <div class="row">
        <div class="main-title ">
            <h2><?= $title ?></h2>
            <!-- Swiper Navigation -->
            <div class="swiper-nav">
                <div class="swiper-button-prev custom-prev" tabindex="0" role="button"></div>
                <div class="swiper-button-next custom-next" tabindex="0" role="button"></div>
            </div>
        </div>

        <!-- Category Buttons -->
        <div class="col-md-4 remove_padding">
            <div class="category_menu product_category">
                <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <div class="toggle_section">
                        <button class="nav-link active" id="v-pills-one_product-tab" data-bs-toggle="pill" data-bs-target="#v-pills-one_product" type="button" role="tab" aria-controls="v-pills-one_product" aria-selected="true" data-product-category="<?= $cat1 ?>"><img src="<?php echo $settings['category_svg_1']['url']; ?>" alt=""><?= preg_replace('/\s*\(ID:\s*\d+\)/', '', $cat1Label) ?></button>
                        <button class="nav-link" id="v-pills-two_product-tab" data-bs-toggle="pill" data-bs-target="#v-pills-two_product" type="button" role="tab" aria-controls="v-pills-two_product" aria-selected="false" tabindex="-1" data-product-category="<?= $cat2 ?>"><img src="<?php echo $settings['category_svg_2']['url']; ?>" alt=""><?= preg_replace('/\s*\(ID:\s*\d+\)/', '', $cat2Label) ?></button>
                        <button class="nav-link" id="v-pills-three_product-tab" data-bs-toggle="pill" data-bs-target="#v-pills-three_product" type="button" role="tab" aria-controls="v-pills-three_product" aria-selected="false" tabindex="-1" data-product-category="<?= $cat3 ?>"><img src="<?php echo $settings['category_svg_3']['url']; ?>" alt=""><?= preg_replace('/\s*\(ID:\s*\d+\)/', '', $cat3Label) ?></button>
                        <button class="nav-link" id="v-pills-four_product-tab" data-bs-toggle="pill" data-bs-target="#v-pills-four_product" type="button" role="tab" aria-controls="v-pills-four_product" aria-selected="false" tabindex="-1" data-product-category="<?= $cat4 ?>"><img src="<?php echo $settings['category_svg_4']['url']; ?>" alt=""><?= preg_replace('/\s*\(ID:\s*\d+\)/', '', $cat4Label) ?></button>
                        <button class="nav-link" id="v-pills-five_product-tab" data-bs-toggle="pill" data-bs-target="#v-pills-five_product" type="button" role="tab" aria-controls="v-pills-five_product" aria-selected="false" tabindex="-1" data-product-category="<?= $cat5 ?>"><img src="<?php echo $settings['category_svg_5']['url']; ?>" alt=""><?= preg_replace('/\s*\(ID:\s*\d+\)/', '', $cat5Label) ?></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Swiper -->
        <div class="col-md-8">
            <div class="tab-content">
                <div class="tab-pane fade active show">
                    <div class="swiper productSwiper">
                        <div class="swiper-wrapper" id="product-category-swiper"></div>
                        <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
require([
    "jquery",
    "swiper",
    "require"  // ensure swiper is mapped in requirejs-config.js
], function ($, Swiper) {

    $(document).ready(function () {
        var swiperInstance = null;
        var eyeIcon    = require.toUrl('Vendor_GauravPageBuilderWidget/images/eye.png');
        var heartIcon  = require.toUrl('Vendor_GauravPageBuilderWidget/images/heart.png');
        var shuffleIcon = require.toUrl('Vendor_GauravPageBuilderWidget/images/shuffle.png');
        var cartIcon   = require.toUrl('Vendor_GauravPageBuilderWidget/images/cart.png');

        // 🔹 Function to init Swiper
        function initSwiper() {
            if (swiperInstance) {
                swiperInstance.destroy(true, true); // clean old instance
            }
            swiperInstance = new Swiper(".productSwiper", {
                slidesPerView: 2,
                spaceBetween: 20,
                navigation: {
                    nextEl: ".custom-next",
                    prevEl: ".custom-prev",
                },
                loop: true,
                observer: true,
                observeParents: true,
                 breakpoints: {
                    0: {           // 📱 mobile
                        slidesPerView: 1,
                        spaceBetween: 10
                    },
                    768: {         // 📱 tablet
                        slidesPerView: 1,
                        spaceBetween: 15
                    },
                    1024: {        // 💻 desktop
                        slidesPerView: 2,
                        spaceBetween: 20
                    }
                }
            });
        }

        // 🔹 Function to load products for a category
        function loadProducts(btn, categoryId) {
            $.ajax({
                url: "/customgoomento/category/getproducts",
                type: "GET",
                dataType: "json",
                data: { category_id: categoryId },
                beforeSend: function () {
                    $("#product-category-swiper").html("<p>Loading...</p>");
                },
                success: function (response) {
                    if (response.success) {
                        // btn.text(response.category_name);

                        var html = "";
                        $.each(response.products, function (i, product) {
                            html += `
                                <div class="swiper-slide">
                                    <div class="product-card">
                                        <div class="product-image">
                                            <img class="product-img" src="${product.image}" alt="${product.name}">
                                            <span class="discount-badge">New</span>
                                            <div class="product-actions">
                                                <button class="action-btn"><img src="${eyeIcon}" alt=""></button>
                                                <button class="action-btn"><img src="${heartIcon}" alt=""></button>
                                                <button class="action-btn"><img src="${shuffleIcon}" alt=""></button>
                                                <button class="action-btn"><img src="${cartIcon}" alt=""></button></div>
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
                                            <h5 class="product-title"><a href="${product.url}">${product.name}</a></h5>
                                            <div class="product-price">
                                                <span class="current-price">₹ ${product.price}</span>
                                                <span class="original-price">₹350</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });

                        $("#product-category-swiper").html(html);

                        // ✅ Init Swiper after products loaded
                        initSwiper();
                    } else {
                        $("#product-category-swiper").html("<p>Error: " + response.message + "</p>");
                    }
                },
                error: function () {
                    $("#product-category-swiper").html("<p>Request failed!</p>");
                }
            });
        }

        // 🔹 Initial load for first tab
        var firstBtn = $(".toggle_section .nav-link").first();
        var firstCat = parseInt(firstBtn.attr("data-product-category"), 10);
        if (Number.isInteger(firstCat)) {
            loadProducts(firstBtn, firstCat);
        }

        // 🔹 On button click → load its products
        $(document).on("click", ".toggle_section .nav-link", function () {
            var btn = $(this);
            var num = parseInt(btn.attr("data-product-category"), 10);

            if (Number.isInteger(num)) {
                $(".toggle_section .nav-link").removeClass("active");
                btn.addClass("active");
                loadProducts(btn, num);
            }
        });

    });
});
</script>

        <?php
            return ob_get_clean();

    }
}
