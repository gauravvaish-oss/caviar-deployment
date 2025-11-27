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
        $catUrl = [];
        foreach($settings['category_top_bar'] as $categoryId){
            $object_manager = $objectManager->create('Magento\Catalog\Model\Category')->load($categoryId);
            $catUrl[$categoryId] = $object_manager->getUrlPath();
        }

        foreach ($categories as $cat) {
            if(in_array($cat['value'], $settings['category_top_bar'])){
                $options[$cat['value']] = preg_replace('/\s*\(ID:\s*\d+\)/', '', $cat['label']);
            }
        }
         $menu_items = $settings['menu_items'];

         $categoryFactory = $objectManager->get(\Magento\Catalog\Model\CategoryFactory::class);
            $categoryRepository = $objectManager->get(\Magento\Catalog\Api\CategoryRepositoryInterface::class);
            $categoryUrlModel = $objectManager->get(\Magento\Catalog\Helper\Category::class);

            $categoryId = 2; // Set the category ID here, e.g., 2 for the root category
            $category = $categoryRepository->get($categoryId);

            // Function to recursively get child categories
            function getCategoryHierarchy($category, $categoryUrlModel) {
                $categoryData = [
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                    'url' => $categoryUrlModel->getCategoryUrl($category)
                ];

                $children = [];
                foreach ($category->getChildrenCategories() as $child) {
                    $children[] = getCategoryHierarchy($child, $categoryUrlModel);  // Recursive call
                }

                if (!empty($children)) {
                    $categoryData['children'] = $children;
                }

                return $categoryData;
            }

            $categoryHierarchy = getCategoryHierarchy($category, $categoryUrlModel);
//             echo '<pre>';
// print_r($categoryHierarchy["children"]);die;
         ob_start();
         ?>
        <div class="top-category-bar">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-3 d-flex align-items-center p-md-0 justify-content-between">
                        <div class="category_menu">
                            <button id="topCategoryBtn" class="top_category"><img src="<?= $toggleIcon ?>" alt=""> TOP CATEGORY</button>
                            <div class="nav_below_item nav flex-column nav-pills me-3" id="v-pills-tab" role="tablist" aria-orientation="vertical" style="padding:0px;">
                                <div id="toggle_section">
                                   <?php 
                                   if(isset($categoryHierarchy["children"])){
                                   
                                   foreach($categoryHierarchy["children"] as $menu){ ?>
                                        <div class="nav-item">
                                            <a class="nav-link" href="<?= $menu['url']; ?>">
                                                <?= $menu['name']; ?>
                                            </a>

                                            <?php if (isset($menu['children'])): ?>
                                                <div class="sub_list">
                                                    <ul>
                                                        <?php foreach($menu['children'] as $sub): ?>
                                                            <li>
                                                                <a href="<?= $sub['url']; ?>">
                                                                    <?= $sub['name']; ?>
                                                                </a>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php } 
                                   }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="mob-search d-flex">
                            <!-- SEARCH TOGGLE BUTTON -->
                            <button class="search_toggle_btn ms-3 d-md-none d-flex" onclick="openSearch()">
                            <i class="bi bi-search"></i>
                            </button>

                            <!-- SLIDE-IN SEARCH BAR -->
                            <form class="search_slide_box d-md-none d-flex search-form" id="searchSlideBox">
                                <input type="text" class="form-control search-input" placeholder="Search products...">

                                <select class="form-select category-select">
                                    <option>All Categories</option>
                                    <?php
                                        foreach($options as $key => $value){?>
                                        <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                    <?php } ?>
                                </select>
                                <button class="close_search_btn btn search-btn" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <form class="d-none search-form d-md-flex">
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
 <script type="text/javascript">
var catUrl = <?= json_encode($catUrl); ?>;

require(['jquery'], function($){
    window.openSearch = function() {
        $("#searchSlideBox").addClass("active");
    }

    // Close slide search
  $(document).on("click", function (e) {
    // If click is NOT inside #searchSlideBox AND NOT on the toggle button
    if (!$(e.target).closest("#searchSlideBox, .search_toggle_btn").length) {
        $("#searchSlideBox").removeClass("active");
    }
});

// Prevent close when clicking inside
$("#searchSlideBox").on("click", function (e) {
    e.stopPropagation();
});
 // Toggle top category menu
         $('#toggle_section').hide();

         $('#topCategoryBtn').on('click', function(e){
            e.preventDefault(); // prevent default action
            $('#toggle_section').slideToggle(300); // smooth toggle
        });

        // Optional: hide toggle section if clicked outside
        $(document).on('click', function(e){
            if(!$(e.target).closest('#topCategoryBtn, #toggle_section').length){
                $('#toggle_section').slideUp(300);
            }
        });
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
                        }
                        $suggestions.show();
                    }
                });
            }, 300);
        });

        // Form submit logic
        $('.search-form').on('submit', function(e){
            e.preventDefault(); // prevent normal submit

            var query = $(this).find('.search-input').val().trim();
            var category = $(this).find('.category-select').val();

            if(query === '' && category !== '' && catUrl[category]){
                // No input, category selected → go to category page
                window.location.href = '/' + catUrl[category];
            } else if(query !== ''){
                // Input present → go to search results page
                window.location.href = '/catalogsearch/result/?q=' + encodeURIComponent(query);
            } 
            // Optional: else do nothing if input empty and no category selected
        });

        // Hide suggestions on outside click
        $(document).on('click', function(e){
            if(!$(e.target).closest('.search-input, .search-suggestions').length){
                $suggestions.hide();
            }
        });
        $('#toggle_section .nav-link').on('mouseenter', function () {
            $(this).find('.sub_list').show();
        }).on('mouseleave', function () {
            $(this).find('.sub_list').hide();
        });

        $(document).ready(function () {
    // Initially hide all submenus
    $(".sub_list").hide();

    // Hover effect for anchor
    $(".nav-item > a.nav-link").hover(
        function () {
            // Hide other submenus except the one for this link
            $(".nav-item > .sub_list").not($(this).next(".sub_list")).slideUp(200);

            // Show this link's submenu
            $(this).next(".sub_list").stop(true, true).slideDown(200);
        },
        function () {
            // Optional: do nothing here; hiding handled by mouseleave
        }
    );

    // Hide submenu when mouse leaves nav-item
    $(".nav-item").mouseleave(function () {
        $(this).children(".sub_list").stop(true, true).slideUp(200);
    });
});

});
</script>



         <?php
         return ob_get_clean();
    }
}
