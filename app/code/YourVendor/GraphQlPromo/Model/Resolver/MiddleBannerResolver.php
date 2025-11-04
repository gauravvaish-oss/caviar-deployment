<?php
namespace YourVendor\GraphQlPromo\Model\Resolver;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class MiddleBannerResolver implements ResolverInterface
{

 
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        // $collection = $this->bannerCollectionFactory->create();
        // $collection->addFieldToFilter('is_active', 1);
        // $collection->setPageSize(1);

        // $item = $collection->getFirstItem();
        // if (!$item || !$item->getId()) {
        //     return null;
        // }
        // return [
        //     'banner_id' => (int)$item->getId(),
        //     'title' => $item->getTitle(),
        //     'image' => $item->getImage(),
        //     'link' => $item->getLink(),
        //     'is_active' => (int)$item->getIsActive()
        // ];
    }
}
