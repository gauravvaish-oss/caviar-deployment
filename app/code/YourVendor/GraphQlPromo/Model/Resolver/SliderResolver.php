<?php
namespace YourVendor\GraphQlPromo\Model\Resolver;

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
                "image" => "http://vixo.local.com/media/.thumbswysiwyg/bios.jpg?rand=1762333872",
                "title" => "Big Sale!",
                "subtitle" => "Up to 50% off on top products",
                "button_text" => "Shop Now",
                "button_link" => "/sale"
            ],
            [
                "image" => "http://vixo.local.com/media/.thumbswysiwyg/unnamed.jpg?rand=1762333874",
                "title" => "New Arrivals",
                "subtitle" => "Check out the latest trends",
                "button_text" => "Explore",
                "button_link" => "/new-arrivals"
            ]
        ];

        return ['items' => $sliderData];
    }
}
