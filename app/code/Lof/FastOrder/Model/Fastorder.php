<?php
/**
 * Copyright (c) 2019  Landofcoder
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Lof\FastOrder\Model;

use Lof\FastOrder\Api\Data\FastorderInterface;
use Lof\FastOrder\Api\Data\FastorderInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Fastorder extends \Magento\Framework\Model\AbstractModel
{

    protected $fastorderDataFactory;

    protected $dataObjectHelper;

    protected $_eventPrefix = 'lof_fastorder_fastorder';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param FastorderInterfaceFactory $fastorderDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Lof\FastOrder\Model\ResourceModel\Fastorder $resource
     * @param \Lof\FastOrder\Model\ResourceModel\Fastorder\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        FastorderInterfaceFactory $fastorderDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Lof\FastOrder\Model\ResourceModel\Fastorder $resource,
        \Lof\FastOrder\Model\ResourceModel\Fastorder\Collection $resourceCollection,
        array $data = []
    ) {
        $this->fastorderDataFactory = $fastorderDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve fastorder model with fastorder data
     * @return FastorderInterface
     */
    public function getDataModel()
    {
        $fastorderData = $this->getData();
        
        $fastorderDataObject = $this->fastorderDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $fastorderDataObject,
            $fastorderData,
            FastorderInterface::class
        );
        
        return $fastorderDataObject;
    }
}
