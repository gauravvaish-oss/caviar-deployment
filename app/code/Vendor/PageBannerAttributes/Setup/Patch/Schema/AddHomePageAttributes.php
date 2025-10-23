<?php
namespace Vendor\PageBannerAttributes\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class AddHomePageAttributes implements SchemaPatchInterface
{
    private $setup;

    public function __construct(SchemaSetupInterface $setup)
    {
        $this->setup = $setup;
    }

    public function apply()
    {
        $this->setup->startSetup();
        $connection = $this->setup->getConnection();
        $table = $this->setup->getTable('cms_page');

        $columns = [

            // Banner
            'banner_image' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Banner Image'],
            'banner_subtitle' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Banner Subtitle'],
            'banner_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Banner Title'],
            'banner_description' => ['type'=>Table::TYPE_TEXT, 'length'=>'64k', 'nullable'=>true, 'comment'=>'Banner Description'],
            'banner_button_text' => ['type'=>Table::TYPE_TEXT, 'length'=>100, 'nullable'=>true, 'comment'=>'Banner Button Text'],
            'banner_button_link' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Banner Button Link'],

            // Special Trend Products
            'special_trend_product_id' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Special Trend Product ID(s) JSON'],
            'special_trend_countdown' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Countdown JSON'],
            'special_trend_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Special Trend Title'],

            // Services
            'service_main_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Main title for Services Section'],
            'service_1_image' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Service 1 Image'],
            'service_1_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Service 1 Title'],
            'service_1_description' => ['type'=>Table::TYPE_TEXT, 'length'=>'64k', 'nullable'=>true, 'comment'=>'Service 1 Description'],
            'service_2_image' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Service 2 Image'],
            'service_2_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Service 2 Title'],
            'service_2_description' => ['type'=>Table::TYPE_TEXT, 'length'=>'64k', 'nullable'=>true, 'comment'=>'Service 2 Description'],
            'service_3_image' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Service 3 Image'],
            'service_3_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Service 3 Title'],
            'service_3_description' => ['type'=>Table::TYPE_TEXT, 'length'=>'64k', 'nullable'=>true, 'comment'=>'Service 3 Description'],

            // Latest & Featured Products
            'latest_products_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Latest Products Title'],
            'latest_products_product_id' => ['type'=>Table::TYPE_TEXT, 'length'=>'64k', 'nullable'=>true, 'comment'=>'Latest Products Selected Product IDs JSON'],
            'featured_products_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Featured Products Title'],
            'featured_products_product_id' => ['type'=>Table::TYPE_TEXT, 'length'=>'64k', 'nullable'=>true, 'comment'=>'Featured Products Selected Product IDs JSON'],
            'bestseller_products_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Bestseller Products Title'],
            'bestseller_products_product_id' => ['type'=>Table::TYPE_TEXT, 'length'=>'64k', 'nullable'=>true, 'comment'=>'Bestseller Products Selected Product IDs JSON'],

            // Sales Banner
            'sales_banner_bg' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Sales Banner Background'],
            'sales_product_image' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Sales Product Image'],
            'sales_label' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Sale Label'],
            'sales_product_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Sales Product Title'],
            'sales_product_price' => ['type'=>Table::TYPE_TEXT, 'length'=>100, 'nullable'=>true, 'comment'=>'Sales Product Price'],
            'sales_product_link' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Sales Product Link'],

            // Testimonials & Enquiry
            'testimonials_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Testimonials Title'],
            'testimonials_review_id' => ['type'=>Table::TYPE_TEXT, 'length'=>'64k', 'nullable'=>true, 'comment'=>'Reviews IDs'],
            'enquiry_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Enquiry Section Title'],
            'enquiry_phone' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Enquiry Phone Number'],
            'enquiry_button_text' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Enquiry Button Text'],
            'enquiry_button_link' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Enquiry Button URL'],

            // Offer Cards & Banners
            'offer_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Offer Card Title'],
            'offer_price' => ['type'=>Table::TYPE_TEXT, 'length'=>100, 'nullable'=>true, 'comment'=>'Offer Card Price'],
            'offer_product_image' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Offer Product Image'],

            'banner_1' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Banner 1 Image'],
            'banner_2' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Banner 2 Image'],
            'banner_3' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Banner 3 Image'],

            'offer_label_1' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Offer Label 1'],
            'offer_title_1' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Offer Title 1'],
            'offer_image_1' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Offer Image 1'],

            'offer_label_2' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Offer Label 2'],
            'offer_title_2' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Offer Title 2'],
            'offer_image_2' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Offer Image 2'],

            'shop_banner_image' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Shop Banner Image'],
            'shop_banner_subtitle' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Shop Banner Subtitle'],
            'shop_banner_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Shop Banner Title'],
            'shop_banner_description' => ['type'=>Table::TYPE_TEXT, 'length'=>'64k', 'nullable'=>true, 'comment'=>'Shop Banner Description'],
            'shop_banner_button_text' => ['type'=>Table::TYPE_TEXT, 'length'=>100, 'nullable'=>true, 'comment'=>'Shop Banner Button Text'],
            'shop_banner_button_link' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Shop Banner Button Link'],

            // Trending & Category Products
            'trending_products_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Trending Products Title'],
            'trending_product_categories' => ['type'=>Table::TYPE_TEXT, 'length'=>'64k', 'nullable'=>true, 'comment'=>'Trending Product Categories JSON'],
            'product_category_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Product Category Title'],
            'product_category_categories' => ['type'=>Table::TYPE_TEXT, 'length'=>'64k', 'nullable'=>true, 'comment'=>'Product Category IDs JSON'],
            'top_products_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Top Products Title'],
            'top_products_categories' => ['type'=>Table::TYPE_TEXT, 'length'=>'64k', 'nullable'=>true, 'comment'=>'Top Products Category IDs JSON'],

            // About, Vision, Mission & Quality
            'about_banner1_image' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'About Banner 1 Image'],
            'about_banner1_subtitle' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'About Banner 1 Subtitle'],
            'about_banner1_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'About Banner 1 Title'],
            'about_banner1_button_text' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'About Banner 1 Button Text'],
            'about_banner1_button_link' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'About Banner 1 Button Link'],

            'about_banner2_image' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'About Banner 2 Image'],
            'about_banner2_subtitle' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'About Banner 2 Subtitle'],
            'about_banner2_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'About Banner 2 Title'],
            'about_banner2_button_text' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'About Banner 2 Button Text'],
            'about_banner2_button_link' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'About Banner 2 Button Link'],

            'about_banner_image' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'About Page Banner Image'],

            'vision_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Vision Section Title'],
            'vision_description' => ['type'=>Table::TYPE_TEXT, 'length'=>'64k', 'nullable'=>true, 'comment'=>'Vision Section Description'],
            'vision_image' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Vision Image'],

            'mission_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Mission Section Title'],
            'mission_description' => ['type'=>Table::TYPE_TEXT, 'length'=>'64k', 'nullable'=>true, 'comment'=>'Mission Section Description'],
            'mission_image' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Mission Image'],

            'quality_1_image' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Quality Item 1 Image'],
            'quality_1_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Quality Item 1 Title'],
            'quality_1_description' => ['type'=>Table::TYPE_TEXT, 'length'=>'64k', 'nullable'=>true, 'comment'=>'Quality Item 1 Description'],

            'quality_2_image' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Quality Item 2 Image'],
            'quality_2_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Quality Item 2 Title'],
            'quality_2_description' => ['type'=>Table::TYPE_TEXT, 'length'=>'64k', 'nullable'=>true, 'comment'=>'Quality Item 2 Description'],

            'quality_3_image' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Quality Item 3 Image'],
            'quality_3_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Quality Item 3 Title'],
            'quality_3_description' => ['type'=>Table::TYPE_TEXT, 'length'=>'64k', 'nullable'=>true, 'comment'=>'Quality Item 3 Description'],

            // Top Products Slider & Brand Images
            'top_product_slider_title' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Top Product Slider Title'],
            'top_product_slider_categories' => ['type'=>Table::TYPE_TEXT, 'length'=>'64k', 'nullable'=>true, 'comment'=>'Top Product Slider Categories JSON'],

            'about_brand_1_image' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Brand 1 Logo'],
            'about_brand_2_image' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Brand 2 Logo'],
            'about_brand_3_image' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Brand 3 Logo'],
            'about_brand_4_image' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Brand 4 Logo'],
            'about_brand_5_image' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Brand 5 Logo'],
            'about_brand_6_image' => ['type'=>Table::TYPE_TEXT, 'length'=>255, 'nullable'=>true, 'comment'=>'Brand 6 Logo'],

        ];

        foreach ($columns as $name => $definition) {
            if (!$connection->tableColumnExists($table, $name)) {
                $connection->addColumn($table, $name, $definition);
            }
        }

        $this->setup->endSetup();
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
