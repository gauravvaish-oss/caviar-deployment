<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_FastOrder
 * @copyright  Copyright (c) 2020 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\FastOrder\Model;

use Exception;
use \Lof\FastOrder\Api\FastCreateOrderManagementInterface;
use \Lof\FastOrder\Helper\Data;
use \Magento\Checkout\Model\Cart;
use \Magento\Checkout\Model\Session;
use Magento\Framework\Data\Form\FormKey;
use \Magento\Framework\DataObject;
use \Magento\Framework\Exception\LocalizedException;
use \Magento\Catalog\Api\ProductRepositoryInterface;
use \Magento\Catalog\Model\ProductFactory;
use \Magento\Catalog\Model\Product;
use \Magento\Catalog\Model\Product\OptionFactory;
use \Magento\Framework\Exception\NoSuchEntityException;
use \Psr\Log\LoggerInterface;
use \Magento\Catalog\Model\ResourceModel\Product\Option\CollectionFactory;
use \Magento\Store\Model\StoreManagerInterface;

class FastCreateOrderManagement implements FastCreateOrderManagementInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManagement;

    /**
     * @var CollectionFactory
     */
    private $optionCollection;

    /**
     * @var OptionFactory
     */
    private $optionFactory;

    /**
     * @var Product
     */
    private $productModel;

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * FastCreateOrderManagement constructor.
     * @param Session $session
     * @param Data $dataHelper
     * @param Cart $cart
     * @param ProductRepositoryInterface $productRepository
     * @param LoggerInterface $logger
     * @param ProductFactory $productFactory
     * @param StoreManagerInterface $storeManagement
     * @param CollectionFactory $optionCollection
     * @param OptionFactory $optionFactory
     * @param Product $productModel
     * @param FormKey $formKey
     */
    public function __construct(
        Session $session,
        Data $dataHelper,
        Cart $cart,
        ProductRepositoryInterface $productRepository,
        LoggerInterface $logger,
        ProductFactory $productFactory,
        StoreManagerInterface $storeManagement,
        CollectionFactory $optionCollection,
        OptionFactory $optionFactory,
        Product $productModel,
        FormKey $formKey
    )
    {
        $this->session = $session;
        $this->dataHelper = $dataHelper;
        $this->cart = $cart;
        $this->productRepository = $productRepository;
        $this->logger = $logger;
        $this->productFactory = $productFactory;
        $this->storeManagement = $storeManagement;
        $this->optionCollection = $optionCollection;
        $this->optionFactory = $optionFactory;
        $this->productModel = $productModel;
        $this->formKey = $formKey;
    }

    /**
     * add product to quote
     * {@inheritdoc}
     */
    public function postFastCreateOrder($param)
    {
        $objData = $this->decodeQuote($param);

        // If have post data
        if ($objData && sizeof($objData) > 0) {
            /**
             * loop all product list
             */
            foreach ($objData as $item) {
                // if qty of product <= 0, exclude that product
                $allowProduct = true;
                // exclude product have no qty or qty <= 0, type grouped check in groupedCartParams function
                switch ($item['type_id']) {
                    case 'simple':
                    case 'downloadable':
                    case 'bundle':
                    case 'configurable':
                        $fastCsvQty = intval($item['fast_csv_qty']);
                        if ($fastCsvQty <= 0) {
                            $allowProduct = false;
                        }
                        break;
                    default:
                        break;
                }

                // just add to cart when product is eligible
                if ($allowProduct) {
                    switch ($item['type_id']) {
                        case 'simple':
                        case 'downloadable':
                            $this->simpleCartParams($item);
                            break;
                        case 'configurable':
                            $this->configurableCartParams($item);
                            break;
                        case 'bundle':
                            $this->bundleCartParams($item);
                            break;
                        case 'grouped':
                            $this->groupedCartParams($item);
                            break;
                        default:
                            break;
                    }
                }
            }
        }

        try {
            $this->cart->save();
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            return 0;
        }

        return 1;
    }

    /**
     * @param $item
     */
    private function simpleCartParams($item)
    {
        $product = null;
        try {
            $product = $this->productRepository->get($item['sku']);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }

        $request = new DataObject();
        $cartParams['qty'] = $item['fast_csv_qty'];
        $request->setData($cartParams);
        try {
            $this->cart->addProduct($product, $request);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @param $item
     */
    private function configurableCartParams($item)
    {
        $productActive = $item['fast_product_active'];
        $fastCsvQty = $item['fast_csv_qty'];
        $sku = $productActive['sku'];
        $product = null;
        try {
            $product = $this->productRepository->get($sku);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
        $request = new DataObject();
        $cartParams['qty'] = $fastCsvQty;
        $request->setData($cartParams);
        try {
            $this->cart->addProduct($product, $request);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @param $item
     */
    private function bundleCartParams($item)
    {
        $product = null;
        try {
            $product = $this->productRepository->get($item['sku']);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }

        [$bundleQty, $productOptions] = $this->getBundleFastOption($item['fast_option_label']);

        $request = new DataObject();
        $cartParams['bundle_option'] = $productOptions;
        $cartParams['bundle_option_qty'] = $bundleQty;
        $cartParams['qty'] = $item['fast_csv_qty'];

        $request->setData($cartParams);

        try {
            $this->cart->addProduct($product, $request);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * grouped add cart
     * @param $item
     */
    private function groupedCartParams($item)
    {
        $fastGroupProduct = $item['fast_grouped_products'];
        foreach ($fastGroupProduct as $simpleProduct) {
            $sku = $simpleProduct['sku'];
            $product = null;
            try {
                $product = $this->productRepository->get($sku);
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
            $request = new DataObject();
            $cartParams['sku'] = $sku;

            // convert qty to int and validation
            $qty = intval($simpleProduct['qty']);

            // just add to cart when qty > 0 and exists product object
            if ($qty > 0 && $product) {
                $cartParams['qty'] = $qty;
                $request->setData($cartParams);
                try {
                    $this->cart->addProduct($product, $request);
                } catch (Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            }
        }
    }

    /**
     * Get option by fast option, customized by js app
     * @param $fastOptionLabel
     * @return mixed
     */
    private function getBundleFastOption($fastOptionLabel)
    {
        $bundleOptions = [];
        $bundleQty = [];
        foreach (($fastOptionLabel) as $itemOption) {
            $fastOptionSelected = $itemOption['fast_option_selected'];

            if ($itemOption['option_type'] === 'radio' || $itemOption['option_type'] === 'select') {
                // get first selection_product_qty
                $bundleQty[$itemOption['option_id']] = $fastOptionSelected[0]['selection_product_qty'];
            }

            foreach ($fastOptionSelected as $optionSelected) {
                $bundleOptions[$itemOption['option_id']][] = $optionSelected['selection_id'];
            }
        }
        return [$bundleQty, $bundleOptions];
    }

    /**
     * Get current quote
     * @return mixed|mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCurrentQuote()
    {
        // retrieve quote items array
        $items = $this->session->getQuote()->getAllItems();
        return $this->dataHelper->makeQuoteFormat($items);
    }

    /**
     * Decode quote json data
     * @param $jsonData
     * @return mixed
     */
    private function decodeQuote($jsonData)
    {
        return $this->dataHelper->decodeData($jsonData);
    }
}
