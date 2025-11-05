<?php
namespace Vendor\Module\Model\Resolver;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class SliderResolver implements ResolverInterface
{
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        // Example static data — replace with DB collection or config data
        $sliderData = [
            [
                "image" => "https://yourdomain.com/media/slider/slide1.jpg",
                "title" => "Big Sale!",
                "subtitle" => "Up to 50% off on top products",
                "button_text" => "Shop Now",
                "button_link" => "/sale"
            ],
            [
                "image" => "https://yourdomain.com/media/slider/slide2.jpg",
                "title" => "New Arrivals",
                "subtitle" => "Check out the latest trends",
                "button_text" => "Explore",
                "button_link" => "/new-arrivals"
            ]
        ];

        return ['items' => $sliderData];
    }
}
