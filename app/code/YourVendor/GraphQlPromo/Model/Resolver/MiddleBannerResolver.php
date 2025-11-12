<?php
namespace YourVendor\GraphQlPromo\Model\Resolver;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class MiddleBannerResolver implements ResolverInterface
{
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $banner = [
            "image" => "https://vixo.nians.in/media/.thumbswysiwyg/placeholder.png?rand=1762326161",
            "title" => "Daily Essentials Sale",
            "button_text" => "Shop Essentials",
            "button_link" => "/daily-essentials"
        ];

        return $banner;
    }
}
