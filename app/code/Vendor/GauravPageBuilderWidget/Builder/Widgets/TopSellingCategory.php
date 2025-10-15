<?php
namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

class TopSellingCategory extends AbstractWidget
{
    const NAME = 'vendor_top_selling_categories';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getTitle(): string
    {
        return __('Top Selling Categories');
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
            'default' => __('Top Categories Slider'),
        ]);

        $this->addControl("category", [
            'label' => __("Select Category"),
            'type' => Controls::SELECT2,
            'multiple' => true,
            'options' => $options,
            'description' => __("Please select at least 3 categories."),
            'validation' => function ($value) {
                if (!is_array($value) || count($value) < 3) {
                    return __("You must select at least 3 categories.");
                }
                return true;
            },
        ]);
        $this->addControl('array_png', [
            'label' => __('Arrow Picture'),
            'type'  => Controls::MEDIA,
        ]);
    $this->endControlsSection();
}

    protected function contentTemplate()
    {
        ?>
        <div class="row top_product_slider" id="top-product-categories">
            <div class="main-title">
                <div class="text-center pb-lg-4">
                    <h2>{{{settings.title}}}</h2>
                </div>
            </div>
                <div class="trending-category-items row"></div>

        </div>

        <script>
        require(['jquery', 'swiper'], function($, Swiper) {
            $(document).ready(function () {
                var categories = "{{{settings.category}}}";
                var categoryArray = categories ? categories.split(",") : [];
                var formKey = $('input[name="form_key"]').val();
                var $sliderWrapper = $(".trending-category-items");
                $sliderWrapper.html(""); // Clear any static fallback

                if(categoryArray.length === 0) {
                    return; // No categories, nothing to show
                }

                var ajaxRequests = [];
                categoryArray.forEach(function(categoryId, index) {
                    categoryId = categoryId.trim();
                    var request = $.ajax({
                        url: '/customgoomento/category/categoriesview',
                        type: 'POST',
                        dataType: 'json',
                        data: { category_id: categoryId, form_key: formKey },
                        success: function(response) {
                            if (response.success) {
                                var html = `
                                    <div class="col-md-4">
                                        <div class="top_product_section_bg">
                                            <img src="${response.category_image}" alt="${response.category_name}" class="img-fluid">
                                            <h5>${response.category_name}</h5>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span>${response.product_count} ( Items )</span>
                                                <a href="${response.category_url}">
                                                    <img src="{{{settings.array_png.url}}}" alt="Go to ${response.category_name}" class="img-fluid">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                $sliderWrapper.append(html);
                            } else {
                                console.error("Failed to load category:", categoryId);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX error for category " + categoryId + ":", xhr.responseText);
                        }
                    });

                    ajaxRequests.push(request);
                });

                $.when.apply($, ajaxRequests).done(function() {
                    // Only show navigation arrows if more than 3 categories
                    var navigation = categoryArray.length > 3 ? {
                        nextEl: '.top_product-next',
                        prevEl: '.top_product-prev',
                    } : false;

                    if (!navigation) {
                        $('#top_product_nav').hide(); // hide arrows
                    }

                    new Swiper('.top_product_slider', {
                        slidesPerView: 3,
                        spaceBetween: 10,
                        navigation: navigation,
                        breakpoints: {
                            768: { slidesPerView: 2 },
                            480: { slidesPerView: 1 }
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
         $settings = $this->getSettingsForDisplay();
          $categoryArray = isset($settings['category']) && is_array($settings['category'])
        ? array_filter(array_map('trim', $settings['category']))
        : [];
    ob_start();
        ?>
        <div class="row top_product_slider" id="top-product-categories">
            <div class="main-title">
                <div class="text-center pb-lg-4">
                    <h2><?php echo $settings['title'] ?? 'Top Selling Categories'; ?></h2>
                </div>
            </div>
                <div class="trending-category-items row"></div>

        </div>

        <script>
        require(['jquery', 'swiper'], function($, Swiper) {
            $(document).ready(function () {
                var categoryArray = <?= json_encode($categoryArray) ?>;
                var formKey = $('input[name="form_key"]').val();
                var $sliderWrapper = $(".trending-category-items");
                $sliderWrapper.html(""); // Clear any static fallback

                if(categoryArray.length === 0) {
                    return; // No categories, nothing to show
                }

                var ajaxRequests = [];
                categoryArray.forEach(function(categoryId, index) {
                    categoryId = categoryId.trim();
                    var request = $.ajax({
                        url: '/customgoomento/category/categoriesview',
                        type: 'POST',
                        dataType: 'json',
                        data: { category_id: categoryId, form_key: formKey },
                        success: function(response) {
                            if (response.success) {
                                var html = `
                                    <div class="col-md-4 p-3">
                                        <div class="top_product_section_bg">
                                            <img src="${response.category_image}" alt="${response.category_name}" class="img-fluid">
                                            <h5>${response.category_name}</h5>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span>${response.product_count} ( Items )</span>
                                                <a href="${response.category_url}">
                                                    <img src="<?=$settings['array_png']['url']; ?>" alt="Go to ${response.category_name}" class="img-fluid">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                $sliderWrapper.append(html);
                            } else {
                                console.error("Failed to load category:", categoryId);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX error for category " + categoryId + ":", xhr.responseText);
                        }
                    });

                    ajaxRequests.push(request);
                });

                $.when.apply($, ajaxRequests).done(function() {
                    // Only show navigation arrows if more than 3 categories
                    var navigation = categoryArray.length > 3 ? {
                        nextEl: '.top_product-next',
                        prevEl: '.top_product-prev',
                    } : false;

                    if (!navigation) {
                        $('#top_product_nav').hide(); // hide arrows
                    }

                    new Swiper('.top_product_slider', {
                        slidesPerView: 3,
                        spaceBetween: 10,
                        navigation: navigation,
                        breakpoints: {
                            768: { slidesPerView: 2 },
                            480: { slidesPerView: 1 }
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
