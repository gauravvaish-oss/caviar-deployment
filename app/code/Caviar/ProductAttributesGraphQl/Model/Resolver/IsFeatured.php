<?php
namespace Caviar\ProductAttributesGraphQl\Model\Resolver;

use \Magento\Catalog\Model\Product;

class IsFeatured implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    protected $productdata;

    public function __construct(
        Product $productdata
    ) {
        $this->productdata = $productdata;
    }
    
    public function resolve(
        \Magento\Framework\GraphQl\Config\Element\Field $field,
        $context,
        \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $product = $value['model'];
        $productdata = $this->productdata->load($product->getId());
        $sellerId = $productdata->getIsFeatured();
        return $sellerId;
    }
}
