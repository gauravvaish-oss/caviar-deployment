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

use Lof\FastOrder\Api\FastorderRepositoryInterface;
use Lof\FastOrder\Api\Data\FastorderSearchResultsInterfaceFactory;
use Lof\FastOrder\Api\Data\FastorderInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Lof\FastOrder\Model\ResourceModel\Fastorder as ResourceFastorder;
use Lof\FastOrder\Model\ResourceModel\Fastorder\CollectionFactory as FastorderCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;

class FastorderRepository implements FastorderRepositoryInterface
{

    protected $resource;

    protected $fastorderFactory;

    protected $fastorderCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataFastorderFactory;

    protected $extensionAttributesJoinProcessor;

    private $storeManager;

    private $collectionProcessor;

    protected $extensibleDataObjectConverter;

    /**
     * @param ResourceFastorder $resource
     * @param FastorderFactory $fastorderFactory
     * @param FastorderInterfaceFactory $dataFastorderFactory
     * @param FastorderCollectionFactory $fastorderCollectionFactory
     * @param FastorderSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceFastorder $resource,
        FastorderFactory $fastorderFactory,
        FastorderInterfaceFactory $dataFastorderFactory,
        FastorderCollectionFactory $fastorderCollectionFactory,
        FastorderSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->fastorderFactory = $fastorderFactory;
        $this->fastorderCollectionFactory = $fastorderCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataFastorderFactory = $dataFastorderFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Lof\FastOrder\Api\Data\FastorderInterface $fastorder
    ) {
        /* if (empty($fastorder->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $fastorder->setStoreId($storeId);
        } */
        
        $fastorderData = $this->extensibleDataObjectConverter->toNestedArray(
            $fastorder,
            [],
            \Lof\FastOrder\Api\Data\FastorderInterface::class
        );
        
        $fastorderModel = $this->fastorderFactory->create()->setData($fastorderData);
        
        try {
            $this->resource->save($fastorderModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the fastorder: %1',
                $exception->getMessage()
            ));
        }
        return $fastorderModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($fastorderId)
    {
        $fastorder = $this->fastorderFactory->create();
        $this->resource->load($fastorder, $fastorderId);
        if (!$fastorder->getId()) {
            throw new NoSuchEntityException(__('Fastorder with id "%1" does not exist.', $fastorderId));
        }
        return $fastorder->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->fastorderCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Lof\FastOrder\Api\Data\FastorderInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Lof\FastOrder\Api\Data\FastorderInterface $fastorder
    ) {
        try {
            $fastorderModel = $this->fastorderFactory->create();
            $this->resource->load($fastorderModel, $fastorder->getFastorderId());
            $this->resource->delete($fastorderModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Fastorder: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($fastorderId)
    {
        return $this->delete($this->getById($fastorderId));
    }
}
