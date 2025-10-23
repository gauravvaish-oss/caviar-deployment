<?php
namespace Vendor\PageBannerAttributes\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class CategoryOptions implements OptionSourceInterface
{
    protected $categoryCollectionFactory;

    public function __construct(CollectionFactory $categoryCollectionFactory)
    {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    public function toOptionArray()
    {
        $collection = $this->categoryCollectionFactory->create()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToFilter('level', ['gt' => 1]); // skip root category

        $options = [];
        foreach ($collection as $category) {
            $options[] = [
                'label' => $category->getName(),
                'value' => $category->getId()
            ];
        }
        return $options;
    }
}
