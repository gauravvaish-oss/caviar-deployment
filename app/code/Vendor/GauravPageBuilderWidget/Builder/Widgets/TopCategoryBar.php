<?php
declare(strict_types=1);

namespace Vendor\GauravPageBuilderWidget\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Elements\Repeater;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Builder\Base\ControlsStack;

class TopCategoryBar extends AbstractWidget
{
    const NAME = 'vendor_top_category_bar_search';

    public function getName() { return self::NAME; }
    public function getTitle() { return __('Categories Bar And Search'); }
    public function getIcon() { return 'fa fa-folder'; }
    public function getCategories() { return ['general']; }

    /**
     * Register a single menu item
     */
    public static function registerMenuItemInterface(ControlsStack $widget)
    {
        $widget->addControl('title', [
            'label' => __('Title'),
            'type'  => Controls::TEXT,
            'default' => __('Menu Item'),
        ]);

        $widget->addControl('icon', [
            'label' => __('Icon'),
            'type' => Controls::MEDIA,
        ]);

        $widget->addControl('link', [
            'label' => __('Link'),
            'type' => Controls::URL,
            'label_block' => true,
            'default' => [
                'url' => '#',
                'is_external' => true,
            ],
            'placeholder' => __('https://your-link.com'),
        ]);
    }

    /**
     * Register full parent menu (no submenus)
     */
    public static function registerMenuInterface(ControlsStack $widget)
    {
        $parentRepeater = new Repeater();

        // Add parent menu fields
        self::registerMenuItemInterface($parentRepeater);

        $widget->addControl('menu_items', [
            'label' => __('Menu Items'),
            'type' => Controls::REPEATER,
            'fields' => $parentRepeater->getControls(),
            'title_field' => '{{{ title }}}',
        ]);
    }

    protected function registerControls()
    {
        $categorySource = ObjectManagerHelper::get(\Goomento\PageBuilder\Model\Config\Source\CatalogCategory::class);
        $categories = $categorySource->toOptionArray();

        $options = [];
        foreach ($categories as $cat) {
            $options[$cat['value']] = $cat['label'];
        }

        // Top Categories Section
        $this->startControlsSection('content_section', [
            'label' => __('Top Categories Menu Section'),
            'tab'   => Controls::TAB_CONTENT,
        ]);

        $this->addControl('title_category', [
            'label' => __('Title Category'),
            'type'  => Controls::TEXT,
            'default' => __('Top Categories'),
        ]);

        self::registerMenuInterface($this);

        $this->endControlsSection();

        // Dropdown Category Selection
        $this->startControlsSection('dropdown_section', [
            'label' => __('Dropdown Options'),
            'tab'   => Controls::TAB_CONTENT,
        ]);

        $this->addControl("category_top_bar", [
            'label' => __("Select Category"),
            'type' => Controls::SELECT2,
            'multiple' => true,
            'options' => $options,
        ]);

        $this->endControlsSection();
    }

    protected function contentTemplate()
{
    ?>
    <div class="top-category-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3 d-flex align-items-center p-md-0">
                    <div class="category_menu">
                        <button onclick="toggleMyDiv()" class="top_category">
                            <img src="images/toggle.png" alt=""> {{{settings.title_category}}}
                        </button>
                        <div class="nav_below_item nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <div id="toggle_section" style="display: block;">
                                {{{settings.menu_items}}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <form class="d-flex search-form">
                        <input type="text" class="form-control search-input" placeholder="Search For Products">
                        <select class="form-select category-select">
                            <option value="">All Categories</option>
                           
                        </select>
                        <button class="btn search-btn" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
    require(['jquery'], function($) {
        $(document).ready(function() {
            var menu = '{{{settings.menu_items}}}';
            console.log('Menu Data:', JSON.stringify(menu)); // Debugging line
        });
    });
    </script>
    <?php
}

    protected function render(): string
    {
        $settings = $this->getSettings();
        $categorySource = ObjectManagerHelper::get(\Goomento\PageBuilder\Model\Config\Source\CatalogCategory::class);
        $categories = $categorySource->toOptionArray();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $assetRepo = $objectManager->get(\Magento\Framework\View\Asset\Repository::class);
        $toggleIcon = $assetRepo->getUrl("Vendor_GauravPageBuilderWidget::images/toggle.png");
        $options = [];
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        foreach ($categories as $cat) {
            if(in_array($cat['value'], $settings['category_top_bar'])){
                $options[$cat['value']] = preg_replace('/\s*\(ID:\s*\d+\)/', '', $cat['label']);
            }
        }
         $menu_items = $settings['menu_items'];
         ob_start();
         ?>
        <div class="top-category-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3 d-flex align-items-center p-md-0">
                    <div class="category_menu">
                        <button onclick="toggleMyDiv()" class="top_category"><img src="<?= $toggleIcon ?>" alt=""> TOP CATEGORY</button>
                        <div class="nav_below_item nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <div id="toggle_section" style="display: block;">
                                <?php foreach($menu_items as $menu){ ?>
                                    <button class="nav-link" id="v-pills-new_product-tab" data-bs-toggle="pill" data-bs-target="#v-pills-new_product" type="button" role="tab" aria-controls="v-pills-new_product" aria-selected="true">
                                    <img src="<?= $menu['icon']['url']; ?>" alt="">
                                    <?php echo $menu['title']; ?></button>
                                <?php } ?>  
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <form class="d-flex search-form">
                        <input type="text" class="form-control search-input" placeholder="Search For Products">
                        <select class="form-select category-select">
                            <option value="">All Categories</option>
                           <?php
                                foreach($options as $key => $value){?>
                                <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                <?php } ?>
                        </select>
                        <button class="btn search-btn" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
 <script>
require(['jquery', 'require'], function($){
    $(document).ready(function(){
        var searchTimeout;
        var currentRequest;
        var $input = $('.search-input');
        var $category = $('.category-select');

        // Create suggestions list if not already present
        if (!$('.search-suggestions').length) {
            $input.after('<ul class="search-suggestions" style="list-style: none;position: absolute;background: rgb(255, 255, 255);width: 668px;z-index: 999;padding: 0px;top: 40px;right: 263px;"></ul>');
        }

        var $suggestions = $('.search-suggestions');

        $input.on('keyup', function(){
            var query = $(this).val().trim();
            var category = $category.val();

            clearTimeout(searchTimeout);
            if (currentRequest) currentRequest.abort();

            if (query.length < 2) {
                $suggestions.hide().empty();
                return;
            }

            searchTimeout = setTimeout(function(){
                currentRequest = $.ajax({
                    url: '/customgoomento/ajax/search',
                    type: 'GET',
                    dataType: 'json',
                    data: { q: query, category: category },
                    success: function(data){
                        $suggestions.empty();

                        if (data.length) {
                            data.forEach(function(product){
                                $suggestions.append(
                                    '<li style="padding:9px 10px;cursor:pointer;border:1px solid #eee;font-size: 15px;">' +
                                    '<a href="'+product.url+'" style="text-decoration:none;color:#333;display:block;">' +
                                    product.name + '</a></li>'
                                );
                            });
                        } else {
                            $suggestions.append('<li style="padding:9px 10px;">No products found</li>');
                        }

                        $suggestions.show();
                    }
                });
            }, 300); // debounce for smoother typing
        });

        // Hide suggestions on outside click
        $(document).on('click', function(e){
            if(!$(e.target).closest('.search-input, .search-suggestions').length){
                $suggestions.hide();
            }
        });
    });
});
</script>


         <?php
         return ob_get_clean();
    }
}