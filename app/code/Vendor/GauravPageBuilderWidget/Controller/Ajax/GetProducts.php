<?php
namespace Vendor\GauravPageBuilderWidget\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class GetProducts extends Action
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        JsonFactory $jsonFactory
    ) {
        $this->productRepository = $productRepository;
        $this->jsonFactory = $jsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $sku = $this->getRequest()->getParam('sku');

        if (!$sku) {
            return $resultJson->setData([
                'success' => false,
                'message' => 'SKU is required.'
            ]);
        }

        try {
            $product = $this->productRepository->get($sku);
            return $resultJson->setData([
                'success' => true,
                'product' => [
                    'id' => $product->getId(),
                    'sku' => $product->getSku(),
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                    'url' => $product->getProductUrl(),
                    'image' => $product->getMediaGalleryImages()->getFirstItem()->getUrl() ?? ''
                ]
            ]);
        } catch (NoSuchEntityException $e) {
            return $resultJson->setData([
                'success' => false,
                'message' => 'Product not found.'
            ]);
        } catch (\Exception $e) {
            return $resultJson->setData([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
