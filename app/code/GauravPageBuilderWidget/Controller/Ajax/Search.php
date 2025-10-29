<?php
namespace Vendor\GauravPageBuilderWidget\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Controller\Result\JsonFactory;

class Search extends Action
{
    protected $productCollectionFactory;
    protected $jsonFactory;

    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        JsonFactory $jsonFactory
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->jsonFactory = $jsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $query     = trim($this->getRequest()->getParam('q'));
        $categoryId = $this->getRequest()->getParam('category');

        $result = [];
        if (strlen($query) < 1) {
            return $this->jsonFactory->create()->setData($result);
        }

        $collection = $this->productCollectionFactory->create()
            ->addAttributeToSelect(['name', 'url_key'])
            ->addAttributeToFilter('name', ['like' => '%' . $query . '%'])
            ->addAttributeToFilter('status', 1)
            ->addAttributeToFilter('visibility', ['neq' => 1]) // Exclude "Not Visible Individually"
            ->setPageSize(10);

        // ✅ Filter by category if selected
        if (!empty($categoryId)) {
            $collection->joinField(
                'category_id',
                'catalog_category_product',
                'category_id',
                'product_id=entity_id',
                null,
                'left'
            )->addAttributeToFilter('category_id', ['eq' => (int)$categoryId]);
        }

        foreach ($collection as $product) {
            $result[] = [
                'name' => $product->getName(),
                'url'  => $product->getProductUrl()
            ];
        }

        return $this->jsonFactory->create()->setData($result);
    }
}
