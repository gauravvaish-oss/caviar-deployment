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

namespace Lof\FastOrder\Helper;

use Exception;
use Magento\Catalog\Model\Product\Option\Value;
use Magento\Bundle\Model\Option;
use Magento\Bundle\Model\Selection;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Directory\Model\CurrencyFactory;

class Data extends AbstractHelper
{
    /**
     * @var string
     */
    private static $CONFIGURABLE_TYPE_ID = "configurable";

    /**
     * @var string
     */
    private static $BUNDLE_TYPE_ID = "bundle";

    /**
     * @var string
     */
    private static $GROUPED_TYPE_ID = "grouped";

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var Filesystem $filesystem
     */
    protected $filesystem;

    /**
     * @var UploaderFactory $fileUploader
     */
    protected $fileUploader;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var StockItemRepository
     */
    private $stockItemRepository;

    /** @var PriceCurrencyInterface $priceCurrency */
    private $priceCurrency;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * @var Session
     */
    private $_customerSession;

    /**
     * @var StoreManagerInterface
     */
    private $storeManagerInterface;

    /**
     * @var CurrencyFactory
     */
    private $currencyFactory;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    protected $pricingHelper;

    protected $_catalogData;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $currency;

    protected $productStatus;
    protected $productVisibility;
    protected $_currency;

    protected $_filter_by_attribute = null;
    protected $_translate_text = null;

    /**
     * @param Context $context
     * @param UploaderFactory $fileUploader
     * @param Filesystem $filesystem
     * @param LoggerInterface $logger
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param CollectionFactory $productCollectionFactory
     * @param StockItemRepository $stockItemRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param ProductRepositoryInterface $productRepository
     * @param StoreManager $storeManagement
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManagerInterface
     * @param CurrencyFactory $currencyFactory
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Directory\Model\Currency $currency
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @throws FileSystemException
     */
    public function __construct(
        Context $context,
        UploaderFactory $fileUploader,
        Filesystem $filesystem,
        LoggerInterface $logger,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        CollectionFactory $productCollectionFactory,
        StockItemRepository $stockItemRepository,
        PriceCurrencyInterface $priceCurrency,
        ProductRepositoryInterface $productRepository,
        StoreManager $storeManagement,
        Session $customerSession,
        StoreManagerInterface $storeManagerInterface,
        CurrencyFactory $currencyFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Catalog\Helper\Data $catalogData
    ) {
        parent::__construct($context);
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->filesystem = $filesystem;
        $this->fileUploader = $fileUploader;
        $this->logger = $logger;
        $this->jsonHelper = $jsonHelper;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->stockItemRepository = $stockItemRepository;
        $this->priceCurrency = $priceCurrency;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManagement;
        $this->_customerSession = $customerSession;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->currencyFactory = $currencyFactory;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->_currency = $currency;
        $this->jsonEncoder = $jsonEncoder;
        $this->pricingHelper = $pricingHelper;
        $this->_catalogData = $catalogData;
    }

    /**
     * get json encode
     *
     * @param mixed|array $data
     * @return string
     */
    public function getJsonEncode($data)
    {
        return $this->jsonEncoder->encode($data);
    }

    /**
     * get config
     *
     * @param string $key
     * @param mixed|null $store
     * @return mixed
     */
    public function getConfig($key, $store = null)
    {
        $store = $this->storeManager->getStore($store);
        $result = $this->scopeConfig->getValue(
            'loffastorder/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $result;
    }

    /**
     * get fast order route
     *
     * @return string
     */
    public function getFastOrderRoute()
    {
        $router = $this->getConfig('general/route');
        if(!$router){
            $router = "loffastforder/fastorder/quickview";
        }
        return $router;
    }

    /**
     * get search attributes
     *
     * @return string
     */
    public function getSearchAttributes()
    {
        return $this->getConfig("search/apply_search_attributes");
    }

    /**
     * get filter by attribute
     *
     * @return mixed|array
     */
    public function getFilterByAttribute()
    {
        if (!$this->_filter_by_attribute) {
            $this->_filter_by_attribute = [];
            $enable_filter_attribute = $this->getConfig("search/enable_filter_attribute");
            $attribute_code = $this->getConfig("search/attribute_code");
            $attribute_code = !empty($attribute_code) ? trim($attribute_code) : "";
            $filter_label = $this->getConfig("search/filter_label");
            $filter_placeholder = $this->getConfig("search/filter_placeholder");
            $filter_input_width = $this->getConfig("search/filter_input_width");
            if ($enable_filter_attribute && $attribute_code && (strlen($attribute_code) >= 3 )) {
                $this->_filter_by_attribute = ["attribute_code" => $attribute_code,
                                                "filter_label" => $filter_label,
                                                "filter_placeholder" => $filter_placeholder,
                                                "filter_input_width" => $filter_input_width
                                                ];
            }
        }
        return $this->_filter_by_attribute;
    }

    /**
     * get translate text
     *
     * @return mixed|array
     */
    public function getTranslateText()
    {
        if (!$this->_translate_text) {
            $text_fastorder = $this->getConfig("translate/text_fastorder");
            $text_products = $this->getConfig("translate/text_products");
            $text_sku = $this->getConfig("translate/text_sku");
            $text_subtotal = $this->getConfig("translate/text_subtotal");
            $text_action = $this->getConfig("translate/text_action");
            $text_qty = $this->getConfig("translate/text_qty");
            $text_total_qty = $this->getConfig("translate/text_total_qty");
            $text_sub_total = $this->getConfig("translate/text_sub_total");
            $text_add_to_cart = $this->getConfig("translate/text_add_to_cart");
            $text_checkout = $this->getConfig("translate/text_checkout");
            $text_add_from_file = $this->getConfig("translate/text_add_from_file");
            $text_choose_file = $this->getConfig("translate/text_choose_file");
            $text_download_a_samle_csv_file = $this->getConfig("translate/text_download_a_samle_csv_file");
            $text_enter_multiple_skus = $this->getConfig("translate/text_enter_multiple_skus");
            $text_enter_multiple_skus_note = $this->getConfig("translate/text_enter_multiple_skus_note");
            $text_add_to_list = $this->getConfig("translate/text_add_to_list");

            $this->_translate_text = [
                                    "text_fastorder" => $text_fastorder,
                                    "text_products" => $text_products,
                                    "text_sku" => $text_sku,
                                    "text_subtotal" => $text_subtotal,
                                    "text_action" => $text_action,
                                    "text_qty" => $text_qty,
                                    "text_total_qty" => $text_total_qty,
                                    "text_add_to_cart" => $text_add_to_cart,
                                    "text_checkout" => $text_checkout,
                                    "text_sub_total" => $text_sub_total,
                                    "text_add_from_file" => $text_add_from_file,
                                    "text_choose_file" => $text_choose_file,
                                    "text_download_a_samle_csv_file" => $text_download_a_samle_csv_file,
                                    "text_enter_multiple_skus" => $text_enter_multiple_skus,
                                    "text_enter_multiple_skus_note" => $text_enter_multiple_skus_note,
                                    "text_add_to_list" => $text_add_to_list
                                    ];
        }
        return $this->_translate_text;
    }

    /**
     * is module enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        $is_enabled = $this->getConfig("general/enabled");
        $available_customer_groups = $this->getConfig("general/enable_special_groups");
        if ($available_customer_groups) {
            $groups = is_array($available_customer_groups) ? $available_customer_groups : explode(",", $available_customer_groups);
            if ($groups) {
                $customer_group_id = $this->getCustomerGroupId();
                if (!in_array($customer_group_id, $groups)) {
                    $is_enabled = false;
                }
            }
        }

        return $is_enabled;
    }

    public function getCustomerGroupId()
    {
        if ($customer = $this->getCurrentCustomer()) {
            return (int)$customer->getGroupId();
        }
        return 0;
    }

    public function getCurrentCustomer()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return $this->_customerSession->getCustomer();
        }
        return null;
    }

    /**
     * Loop quote item to get new format standard
     * @param $items
     * @return mixed
     */
    public function makeQuoteFormat($items)
    {
        $result = [];
        if ($items && sizeof($items) > 0) {
            foreach ($items as $item) {
                $result[] = [
                    'id' => $item->getProductId(),
                    'name' => $item->getName(),
                    'sku' => $item->getSku(),
                    'qty' => $item->getQty(),
                    'price' => $item->getPrice()
                ];
            }
        }
        return $result;
    }

    /**
     * get current currency symbol
     *
     * @return string
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->_currency->getCurrencySymbol();
    }

    /**
     * Upload file using for upload in frontend with ajax form
     * @param RequestInterface $request
     * @param string $yourFolderName $yourFolderName this folder will be created inside "pub/media" folder
     * @param string $yourInputFileName $yourInputFileName  "your_input_file_name" is the HTML input file name
     * @param array $allowType
     * @return bool|string
     */
    public function uploadFile(
        RequestInterface $request,
        $yourFolderName = 'fast_order',
        $yourInputFileName = 'input_file',
        $allowType = ['jpg', 'png', 'jpge']
    ) {
        try {
            $file = $request->getFiles($yourInputFileName);
            $fileName = ($file && array_key_exists('name', $file)) ? $file['name'] : null;
            if ($file && $fileName) {
                $target = $this->mediaDirectory->getAbsolutePath($yourFolderName);

                /** @var $uploader Uploader */
                $uploader = $this->fileUploader->create(['fileId' => $yourInputFileName]);

                // set allowed file extensions
                $uploader->setAllowedExtensions($allowType);

                // allow folder creation
                $uploader->setAllowCreateFolders(true);

                // rename file name if already exists
                $uploader->setAllowRenameFiles(true);

                // rename the file name into lowercase
                // but this one is not working
                // we can simply use strtolower() function to rename filename to lowercase
                // $uploader->setFilenamesCaseSensitivity(true);

                // enabling file dispersion will
                // rename the file name into lowercase
                // and create nested folders inside the upload directory based on the file name
                // for example, if uploaded file name is IMG_123.jpg then file will be uploaded in
                // pub/media/your-upload-directory/i/m/img_123.jpg
                // $uploader->setFilesDispersion(true);

                // upload file in the specified folder
                $result = $uploader->save($target);

                if (!$result['file']) {
                    // write file not done
                    $this->logger->critical('Uploader, save file error');
                }

                $pathFileFinal = $target . $uploader->getUploadedFileName();
                return $pathFileFinal;
            }
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
        return false;
    }

    /**
     * get list product by list sku & qty
     * @param $csvData
     * @param int $storeId
     * @return mixed
     */
    public function getDataByListSkuQty($csvData, $storeId = 0)
    {
        $sizeData = sizeof($csvData);
        $skuList = [];
        if ($csvData && $sizeData > 1) { // have to check > 1, because row 1 = babel
            // Loop from index 1 because index 0 is label of csv file
            for ($i = 1; $i < $sizeData; $i++) {
                $item = $csvData[$i];
                $sku = $item[0];
                // If have no qty, then set qty = 1
                $qty = isset($item[1]) ? (int)$item[1] : 1;

                // If qty is equal 0, then set default to 1 value
                if ($qty == 0 || $qty < 0) {
                    $qty = 1;
                }

                // Push to array return
                $listCsv[$sku] = ['qty' => $qty];

                // Push to array sku using to select to db
                $skuList[] = $sku;
            }
        }

        $mainCurrencySymbol = $this->getCurrentCurrencySymbol();
        $mainCurrencyCode = $this->getMainCurrencyCode();
        /** @var Collection $productCollection */
        $productCollection = $this->productCollectionFactory->create();

        // Filters by sku
        $productList = $productCollection->addAttributeToSelect(["name","sku","price","attribute_set_id","description","has_options","parent_id","required_options","short_description","status","store_id","tax_class_id","type_id","url_key","visibility","msrp","msrp_display_actual_price_type","options_container","quantity_and_stock_status","image","swatch_image","thumbnail","allow_stock"])
            ->addFieldToFilter('sku', ['in' => $skuList])
            ->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()])
            ->addStoreFilter($storeId);

        $productList->setVisibility($this->productVisibility->getVisibleInSiteIds());

        $productLisFull = [];
        // Loop product list to get all detail attribute
        foreach ($productList as $item) {
            $product = $item->getData();

            $product['currency_code'] = $mainCurrencyCode;
            $product['currency_symbol'] = $mainCurrencySymbol;

            $sku = $product['sku'];
            $stock = 0;
            $productObj = null;

            // match qty from cvs file to product, match by sku
            $product['fast_csv_qty'] = $listCsv[$sku]['qty'];

            // add product url
            $product['product_url'] = $item->getProductUrl();

            // reformat currency
            if (isset($product['price'])) {
                $productPrice = isset($product['price']) ? $product['price'] : 0;
                $newPrice =  $this->priceCurrency->convert($productPrice);
                $product['base_price_value'] = $product['price'];
                $product['converted_new_price_value'] = $newPrice;
                $product['price'] = $this->priceCurrency->convertAndFormat($product['price'], false);

            }

            // find product configurable by id
            $productId = $product['entity_id'];

            try {
                $productObj = $this->productRepository->getById($productId);
                $stock = $this->stockItemRepository->get($productId)->getQty();
            } catch (Exception $e) {
                $this->logger->critical($e->getMessage());
            }

            switch ($product['type_id']) {
                case self::$CONFIGURABLE_TYPE_ID:
                    $productAttOptions = $productObj->getTypeInstance(true)->getConfigurableAttributesAsArray($productObj);
                    // get attribute array and option data
                    list($attributeArray, $attributeOptions) = $this->getAttributeData($productAttOptions);

                    // get used products
                    $usedProducts = $this->getUsedProduct($productObj);
                    $product['fast_attribute_array'] = $attributeArray;
                    $product['fast_attribute_options'] = $attributeOptions;
                    $product['fast_used_products'] = $usedProducts;
                    break;
                case self::$BUNDLE_TYPE_ID:
                    /* @var \Magento\Bundle\Model\ResourceModel\Selection\Collection $selectionCollection */
                    $selectionCollection = $productObj->getTypeInstance(true)->getSelectionsCollection($productObj->getTypeInstance(true)->getOptionsIds($productObj), $productObj);

                    // DO NOR REMOVE BELOW CODE FOR CHECK MODEL FUNCTION for suggestions
                    /** @var $selection Selection */
                    // $selection = $selectionCollection->getFirstItem();

                    $selectionArr = [];
                    foreach ($selectionCollection as $proSelection) {
                        $tempArr = [];
                        $tempArr['selection_id'] = $proSelection->getSelectionId();
                        $tempArr['option_id'] = $proSelection->getOptionId();
                        $tempArr['selection_product_name'] = $proSelection->getName();
                        $tempArr['selection_base_price_value'] = $this->priceCurrency->convert($proSelection->getPrice());
                        $tempArr['selection_product_price_format'] = $this->priceCurrency->convertAndFormat($proSelection->getPrice(), false);
                        $tempArr['selection_product_qty'] = (int)$proSelection->getSelectionQty();
                        $tempArr['selection_product_id'] = $proSelection->getProductId();
                        $tempArr['selection_is_default'] = $proSelection->getIsDefault();
                        $tempArr['selection_price_type'] = $proSelection->getSelectionPriceType();
                        $tempArr['selection_is_default'] = $proSelection->getIsDefault();
                        $tempArr['selection_position'] = $proSelection->getPosition();

                        $selectionArr[$proSelection->getOptionId()][] = $tempArr;
                    }

                    //get all option of product
                    /* @var \Magento\Bundle\Model\ResourceModel\Option\Collection $optionsCollection */
                    $optionsCollection = $productObj->getTypeInstance(true)->getOptionsCollection($productObj);

                    // DO NOT REMOVE BELOW CODE FOR CHECK MODEL FUNCTION for suggestions
                    /** @var $option Option */
                    //  $option = $optionsCollection->getFirstItem();
                    $optionLabel = [];
                    foreach ($optionsCollection as $option) {
                        $optionLabel[$option->getOptionId()]['option_id'] = $option->getOptionId();
                        $optionLabel[$option->getOptionId()]['option_title'] = $option->getDefaultTitle();
                        $optionLabel[$option->getOptionId()]['option_type'] = $option->getType();
                        $optionLabel[$option->getOptionId()]['require'] = $option->getRequired();
                        $optionLabel[$option->getOptionId()]['position'] = $option->getPosition();
                        $optionLabel[$option->getOptionId()]['fast_option_selected'] = []; // for selected option type, store object type
                    }
                    $product['fast_option_label'] = $optionLabel;
                    $product['fast_selection_array'] = $selectionArr;
                    break;
                case self::$GROUPED_TYPE_ID:
                    $associatedProducts = $productObj->getTypeInstance(true)->getAssociatedProducts($productObj);
                    $listProduct = [];
                    foreach ($associatedProducts as $productItem) {
                        $productTemp = $productItem->getData();
                        // reformat currency
                        if (isset($productTemp['price'])) {
                            $productTmpPrice = $productTemp['price'];
                            $newTmpPrice = $this->priceCurrency->convert($productTmpPrice);
                            $productTemp['base_price_value'] = $productTemp['price'];
                            $productTemp['converted_new_price_value'] = $newTmpPrice;
                            $productTemp['price'] = $this->priceCurrency->convertAndFormat($productTemp['price'], false);
                        }

                        // convert qty to int, get qty settings in database
                        $productTemp['qty'] = (int)$productTemp['qty'];
                        $listProduct[] = $productTemp;
                    }
                    $product['fast_grouped_products'] = $listProduct;
                    break;
                default:
                    break;
            }

            if(($product['has_options']) && (int)$product['has_options'] == 1) {
                if(isset($product['required_options']) && (int)$product['required_options'] == 1){
                    $productObj = $this->productRepository->getById($item->getId());
                    $options = $productObj->getOptions();
                    $config = [];
                    foreach ($options as $option) {
                        /* @var $option \Magento\Catalog\Model\Product\Option */
                        if ($option->hasValues()) {
                            $tmpPriceValues = [];
                            foreach ($option->getValues() as $valueId => $value) {
                                $tmpPriceValues[$valueId] = $this->_getPriceConfiguration($value);
                            }
                            $priceValue = $tmpPriceValues;
                        } else {
                            $priceValue = $this->_getPriceConfiguration($option);
                        }
                        $config[$option->getId()] = $option->getData();
                        $config[$option->getId()]["price"] = $priceValue;
                    }
                    $product['custom_options'] = $config;
                }
            }

            $product['stock'] = $stock;

            // Filter attribute option visible
            $productLisFull[] = $product;
        }

        return $productLisFull;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMainCurrencyCode()
    {
        $baseCurrencyCode = $this->storeManagerInterface->getStore()->getBaseCurrencyCode();
        $currentCurrencyCode = $this->storeManagerInterface->getStore()->getCurrentCurrencyCode();
        $allowedCurrencies = $this->storeManagerInterface->getStore()->getAvailableCurrencyCodes(true);

        if (sizeof($allowedCurrencies) > 1) {
            $mainCurrencyCode = $currentCurrencyCode;
        } else {
            $mainCurrencyCode = $baseCurrencyCode;
        }
        return $mainCurrencyCode;
    }

    /**
     * @param $productAttOptions
     * @return mixed
     */
    private function getAttributeData($productAttOptions)
    {
        $attributeArray = [];
        $attributeOptions = [];

        foreach ($productAttOptions as $productAttribute) {
            $attributeArray[] = $productAttribute;
            foreach ($productAttribute['values'] as $attribute) {
                $attributeOptions[$productAttribute['label']][$attribute['value_index']] = $attribute['store_label'];
            }
        }
        return [$attributeArray, $attributeOptions];
    }

    /**
     * Converts the amount value from one currency to another.
     * If the $currencyCodeFrom is not specified the current currency will be used.
     * If the $currencyCodeTo is not specified the base currency will be used.
     *
     * @param float $amountValue like 13.54
     * @param string|null $currencyCodeTo like 'BYN'
     * @return float
     */
    public function convertPrice($amountValue, $currencyCodeTo = null)
    {
        $store = null;
        $currencyCodeFrom = '';
        try {
            $store = $this->storeManager->getStore();
            $currencyCodeFrom = $this->storeManagerInterface->getStore()->getBaseCurrencyCode();
        } catch (NoSuchEntityException $e) {
            $this->logger->critical($e->getMessage());
        }

        if ($store) {
            /**
             * If is not specified the currency code from which we want to convert - use current currency
             */
            if (!$currencyCodeFrom) {
                $currencyCodeFrom = $store->getCurrentCurrency()->getCode();
            }

            /**
             * If is not specified the currency code to which we want to convert - use base currency
             */
            if (!$currencyCodeTo) {
                $currencyCodeTo = $store->getBaseCurrency()->getCode();
            }

            /**
             * Do not convert if currency is same
             */
            if ($currencyCodeFrom == $currencyCodeTo) {
                return $amountValue;
            }

            /** @var float $rate */
            // Get rate
            $rate = $this->currencyFactory->create()->load($currencyCodeFrom)->getAnyRate($currencyCodeTo);
            // Get amount in new currency
            $amountValue = $amountValue * $rate;

            return $amountValue;
        }
    }

    /**
     * /**
     * @param $product
     * @return mixed
     */
    private function getUsedProduct($product)
    {
        $usedProduct = $product->getTypeInstance()->getUsedProducts($product);
        $arrProduct = [];
        if (!empty($usedProduct)) {
            /* @var Product $product */
            foreach ($usedProduct as $product) {
                try {
                    $stock = $this->stockItemRepository->get($product->getId())->getQty();
                } catch (Exception $e) {
                    $this->logger->critical($e->getMessage());
                }
                $productArray = $product->toArray();
                $productArray['stock'] = $stock;
                // convert price
                $productArray['price'] = $this->priceCurrency->convertAndFormat($productArray['price'], false);
                $productArray['base_price_value'] = $productArray['price'];
                $arrProduct[] = $productArray;
            }
        }

        return $arrProduct;
    }

    /**
     * Get price configuration
     *
     * @param \Magento\Catalog\Model\Product\Option\Value|\Magento\Catalog\Model\Product\Option $option
     * @return array
     */
    protected function _getPriceConfiguration($option)
    {
        $optionPrice = $option->getPrice(true);
        if ($option->getPriceType() !== Value::TYPE_PERCENT) {
            $optionPrice = $this->pricingHelper->currency($optionPrice, false, false);
        }
        $data = [
            'prices' => [
                'oldPrice' => [
                    'amount' => $this->pricingHelper->currency($option->getRegularPrice(), false, false),
                    'adjustments' => [],
                ],
                'basePrice' => [
                    'amount' => $this->_catalogData->getTaxPrice(
                        $option->getProduct(),
                        $optionPrice,
                        false,
                        null,
                        null,
                        null,
                        null,
                        null,
                        false
                    ),
                ],
                'finalPrice' => [
                    'amount' => $this->_catalogData->getTaxPrice(
                        $option->getProduct(),
                        $optionPrice,
                        true,
                        null,
                        null,
                        null,
                        null,
                        null,
                        false
                    ),
                ],
            ],
            'type' => $option->getPriceType(),
            'name' => $option->getTitle(),
        ];
        return $data;
    }

    /**
     * Decode json data string to object or array
     * @param $jsonData
     * @return mixed
     */
    public function decodeData($jsonData)
    {
        return $this->jsonHelper->jsonDecode($jsonData);
    }
}
