<?php
namespace YourVendor\GraphQlPromo\Model\Resolver;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\App\ObjectManager;

class TopDealsProducts implements ResolverInterface
{
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $objectManager = ObjectManager::getInstance();

        $collection = $objectManager
            ->create(\Magento\Catalog\Model\ResourceModel\Product\Collection::class);

        $collection->addAttributeToSelect('*')
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('is_top_deals', 1);

        $visibility = $objectManager->get(\Magento\Catalog\Model\Product\Visibility::class);
        $collection->setVisibility($visibility->getVisibleInCatalogIds());
        $store = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class)->getStore();
        $mediaUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product';

        $items = [];
        foreach ($collection as $product) {
                        $image = $product->getData('image');
    $smallImage = $product->getData('small_image');
    $thumbnail = $product->getData('thumbnail');
            $items[] = [
                'entity_id'                     => (int)$product->getData('entity_id'),
                'attribute_set_id'              => (int)$product->getData('attribute_set_id'),
                'type_id'                       => $product->getData('type_id'),
                'sku'                           => $product->getData('sku'),
                'has_options'                   => (int)$product->getData('has_options'),
                'required_options'              => (int)$product->getData('required_options'),
                'created_at'                    => $product->getData('created_at'),
                'updated_at'                    => $product->getData('updated_at'),
                'status'                        => (int)$product->getData('status'),
                'is_trending'                   => (int)$product->getData('is_trending'),
                'cat_index_position'            => (int)$product->getData('cat_index_position'),
                'description'                   => $product->getData('description'),
                'visibility'                    => (int)$product->getData('visibility'),
                'quantity_and_stock_status'     => (int)$product->getData('quantity_and_stock_status'),
                'tax_class_id'                  => (int)$product->getData('tax_class_id'),
                'price'                         => (float)$product->getData('price'),
                'special_price'                 => (float)$product->getData('special_price'),
                'name'                          => $product->getData('name'),
                'meta_title'                    => $product->getData('meta_title'),
                'image'                         => $image ? $mediaUrl . $image : null,
        'small_image'                   => $smallImage ? $mediaUrl . $smallImage : null,
        'thumbnail'                     => $thumbnail ? $mediaUrl . $thumbnail : null,
                'options_container'             => $product->getData('options_container'),
                'url_key'                       => $product->getData('url_key'),
                'msrp_display_actual_price_type'=> (int)$product->getData('msrp_display_actual_price_type'),
                'gift_message_available'        => (int)$product->getData('gift_message_available'),
                'special_from_date'             => $product->getData('special_from_date'),
                'store_id'                      => (int)$product->getData('store_id')
            ];
        }

        return [
            'items' => $items,
            'total_count' => (int)$collection->getSize()
        ];
    }
}
