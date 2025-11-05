<?php
namespace Vendor\Module\Model\Resolver;

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
            "image" => "https://yourdomain.com/media/banners/middle-banner.jpg",
            "title" => "Daily Essentials Sale",
            "button_text" => "Shop Essentials",
            "button_link" => "/daily-essentials"
        ];

        return $banner;
    }
}
