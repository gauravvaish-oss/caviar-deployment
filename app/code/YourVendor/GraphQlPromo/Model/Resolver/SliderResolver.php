<?php
namespace YourVendor\GraphQlPromo\Model\Resolver;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class SliderResolver implements ResolverInterface
{
   
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        // $collection = $this->sliderCollectionFactory->create();
        // $collection->addFieldToFilter('is_active', 1);
        // $collection->setOrder('sort_order', 'ASC');

        // $items = [];
        // foreach ($collection as $row) {
        //     $items[] = [
        //         'slider_id' => (int)$row->getId(),
        //         'title' => $row->getTitle(),
        //         'image' => $row->getImage(),
        //         'link' => $row->getLink(),
        //         'sort_order' => (int)$row->getSortOrder(),
        //         'is_active' => (int)$row->getIsActive()
        //     ];
        // }
        // return $items;
    }
}
