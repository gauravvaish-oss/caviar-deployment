<?php
namespace Vendor\GauravPageBuilderWidget\Plugin;

use Goomento\PageBuilder\Builder\Managers\Widgets;
use Psr\Log\LoggerInterface;

class EntryPointPlugin
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function beforeRegisterWidgets($subject, Widgets $widgetsManager)
    {
        $this->logger->info('Registering GauravWidget');
        
        $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\LatestProducts::class
        );
        $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\PrivacyPolicyTabs::class
        );
         $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\LogoSlider::class
        );
        $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\StayInTouch::class
        );
        $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\LatestBlogs::class
        );
        $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\ContactForm::class
        );
        $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\AddressBlock::class
        );
         $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\TopSellingCategory::class
        );
         $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\SvgPoster::class
        );
         $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\ImageBox2::class
        );
         $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\ProductRow::class
        );
        $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\CustomBanner2::class
        );
         $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\MultiLevelMenu::class
        );
        $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\TopCategoryBar::class
        );
        $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\ShopByCategory::class
        );
        $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\CustomBanner::class
        );
        $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\CategoriesView::class
        );
        $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\ProductCategories::class
        );
        $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\ProductCountdown::class
        );
        $widgetsManager->registerWidgetType(
            \Vendor\GauravPageBuilderWidget\Builder\Widgets\TrendingProducts::class
        );
        return [$widgetsManager];
    }
}
