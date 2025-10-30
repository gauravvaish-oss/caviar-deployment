<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\FastOrder\Model\Config\Source;

use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;

/**
 * Config category source
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class GroupCollection implements \Magento\Framework\Option\ArrayInterface
{

    protected $_CustomergroupCollectionFactory;

    public function __construct(
        CollectionFactory $CustomergroupCollectionFactory) {
        $this->_CustomergroupCollectionFactory = $CustomergroupCollectionFactory;
    }

    /**
     * Return option array
     *
     * @param bool $addEmpty
     * @return mixed
     */
    public function toOptionArray($addEmpty = true)
    {

        $collection = $this->_CustomergroupCollectionFactory->create();
        $options = [];

        foreach ($collection as $customergroups) {
            $options[] = ['label' => $customergroups->getCustomerGroupCode(), 'value' => $customergroups->getId()];
        }

        return $options;
    }
}
