<?php
namespace Vendor\PageBannerAttributes\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class Products implements OptionSourceInterface
{
    protected $collectionFactory;

    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        $options = [];
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect('name')->setPageSize(100); // limit for performance

        foreach ($collection as $product) {
            $options[] = [
                'value' => $product->getId(),
                'label' => $product->getName()
            ];
        }

        return $options;
    }
}
