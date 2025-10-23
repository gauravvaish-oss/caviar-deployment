<?php
namespace Vendor\PageBannerAttributes\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory;

class ReviewOptions implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $reviewCollectionFactory;

    public function __construct(CollectionFactory $reviewCollectionFactory)
    {
        $this->reviewCollectionFactory = $reviewCollectionFactory;
    }

    /**
     * Return options for multi-select
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        $collection = $this->reviewCollectionFactory->create();
        $collection->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED);

        // Ensure review_detail is joined (already joined by default in Review\Collection)
        $collection->addFieldToSelect('review_id');
        $collection->addFieldToSelect('title', 'detail'); // fetch title from joined detail table

        foreach ($collection as $review) {
            $options[] = [
                'value' => $review->getId(),
                'label' => $review->getTitle() // uses detail.title
            ];
        }

        return $options;
    }
}