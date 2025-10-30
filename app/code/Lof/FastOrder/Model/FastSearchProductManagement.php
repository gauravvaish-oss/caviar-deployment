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

use Lof\FastOrder\Api\FastSearchProductManagementInterface;
use Lof\FastOrder\Helper\Data;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class FastSearchProductManagement implements FastSearchProductManagementInterface
{
    /**
     * @var CollectionFactory
     */
    protected $productCollection;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /** @var PriceCurrencyInterface $priceCurrency */
    private $priceCurrency;

    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManagerInterface;

    /**
     * @var Resolver
     */
    private $localeResolver;

    /**
     * @var CurrencyInterface
     */
    private $currencyInterface;

    /**
     * @var CurrencyFactory
     */
    private $currencyFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Data
     */
    private $dataHelper;

    protected $productStatus;
    protected $productVisibility;
    protected $_currency;

    /**
     * FastSearchProductManagement constructor.
     * @param CollectionFactory $productCollection
     * @param ScopeConfigInterface $scopeConfig
     * @param PriceCurrencyInterface $priceCurrency
     * @param StoreManager $storeManager
     * @param StoreManagerInterface $storeManagerInterface
     * @param Resolver $localeResolver
     * @param CurrencyFactory $currencyFactory
     * @param Data $dataHelper
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Directory\Model\Currency
     */
    public function __construct(
        CollectionFactory $productCollection,
        ScopeConfigInterface $scopeConfig,
        PriceCurrencyInterface $priceCurrency,
        StoreManager $storeManager,
        StoreManagerInterface $storeManagerInterface,
        Resolver $localeResolver,
        CurrencyFactory $currencyFactory,
        Data $dataHelper,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
       \Magento\Catalog\Model\Product\Visibility $productVisibility,
       \Magento\Directory\Model\Currency $currency
    ) {
        $this->productCollection = $productCollection;
        $this->scopeConfig = $scopeConfig;
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->localeResolver = $localeResolver;
        $this->currencyFactory = $currencyFactory;
        $this->dataHelper = $dataHelper;
        $this->productStatus = $productStatus;
        $this->productVisibility = $productVisibility;
        $this->_currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->_currency->getCurrencySymbol();
    }

    /**
     * {@inheritdoc}
     */
    public function getFastSearchProduct($value, $storeCode, $filterAttribute = null)
    {
        $mainCurrencyCode = $this->dataHelper->getMainCurrencyCode();
        $listStore = $this->storeManager->getStores(true, true);
        $mainCurrencySymbol = $this->getCurrentCurrencySymbol();
        $storeId = 0;
        if (isset($listStore[$storeCode])) {
            $storeId = $listStore[$storeCode]->getId();
        }
        $limit = $this->scopeConfig->getValue('loffastorder/search/limit_search_results');
        /** @var Collection $collection */
        $collection = $this->productCollection->create();
        $collection
                ->addAttributeToSelect(["name","sku","price","attribute_set_id","description","has_options","parent_id","required_options","short_description","status","store_id","tax_class_id","type_id","url_key","visibility","msrp","msrp_display_actual_price_type","options_container","image","quantity_and_stock_status","swatch_image","thumbnail","allow_stock"]);

        $apply_for_attributes = [];
        if($filterAttribute) {
            $apply_for_attributes = explode("|", $filterAttribute);
        } else {
            $searchAttributes = $this->dataHelper->getSearchAttributes();
            $searchAttributes = $searchAttributes?$searchAttributes:'sku,name';
            $apply_for_attributes = explode(",", $searchAttributes);
        }

        $search_conditions = [];
        foreach($apply_for_attributes as $attribute_code){
            $attribute_code = trim($attribute_code);
            if($attribute_code){
                $_conditions = ['attribute' => $attribute_code, 'like' => ("%" . $value . "%")];
                $search_conditions[] = $_conditions;
            }
        }
        $collection->addFieldToFilter($search_conditions);

        $collection->addAttributeToFilter('status', ['in' => $this->productStatus->getVisibleStatusIds()])
                    ->addStoreFilter($storeId);

        $collection->setVisibility($this->productVisibility->getVisibleInSiteIds());
        // Using join left for get product parent only
        $collection->getSelect()->joinLeft(
                'catalog_product_super_link',
                'e.entity_id = catalog_product_super_link.product_id'
            )->where("catalog_product_super_link.product_id IS NULL")->limit($limit);

        $dataProductList = [];

        foreach ($collection as $item) {
            $product = $item->getData();
            $productPrice = isset($product['price']) ? $product['price'] : 0;
            if ($productPrice) {
                $newPrice = $this->convert($productPrice, $mainCurrencyCode);
                $product['base_price_value'] = $product['price'];
                $product['converted_new_price_value'] = $newPrice;
                $product['currency_code'] = $mainCurrencyCode;
                $product['currency_symbol'] = $mainCurrencySymbol;
                $product['price'] = $this->priceCurrency->format($newPrice, false);

            }
            $dataProductList[] = $product;
        }

        return $dataProductList;
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
    public function convert($amountValue, $currencyCodeTo = null)
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
}
