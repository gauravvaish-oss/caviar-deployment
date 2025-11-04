<?php
namespace Vendor\GauravPageBuilderWidget\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\LayoutInterface;
use Magento\Catalog\Block\Product\AbstractProduct;

class GetProducts extends Action
{
    protected $productRepository;
    protected $jsonFactory;
    protected $layout;

    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        JsonFactory $jsonFactory,
        LayoutInterface $layout
    ) {
        $this->productRepository = $productRepository;
        $this->jsonFactory = $jsonFactory;
        $this->layout = $layout;
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

            /** @var AbstractProduct $block */
            $block = $this->layout->createBlock(AbstractProduct::class);
            $addToCartPost = $block->getAddToCartPostParams($product);

           return $product;

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
