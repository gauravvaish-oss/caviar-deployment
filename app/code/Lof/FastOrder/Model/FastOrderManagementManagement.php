<?php

namespace Lof\FastOrder\Model;

use Lof\FastOrder\Api\FastOrderManagementInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\Helper\Data;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class FastOrderManagementManagement implements FastOrderManagementInterface
{
    /**
     * @var Data
     */
    private $jsonHelper;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var \Lof\FastOrder\Helper\Data
     */
    private $dataHelper;

    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManagerInterface;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * FastAddMultipleSkuManagement constructor.
     * @param Data $jsonHelper
     * @param \Lof\FastOrder\Helper\Data $dataHelper
     * @param Session $customerSession
     * @param StoreManager $storeManager
     * @param StoreManagerInterface $storeManagerInterface
     * @param LoggerInterface $logger
     */
    public function __construct(
        Data $jsonHelper,
        \Lof\FastOrder\Helper\Data $dataHelper,
        Session $customerSession,
        StoreManager $storeManager,
        StoreManagerInterface $storeManagerInterface,
        LoggerInterface $logger
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->dataHelper = $dataHelper;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->logger = $logger;
    }

    /**
     * @param string $param
     * @param $storeCode
     * @return mixed
     */
    public function postFastAddMultipleSKu($param, $storeCode)
    {
        $listStore = $this->storeManager->getStores(true, true);
        $storeId = 0;
        if (isset($listStore[$storeCode])) {
            $storeId = $listStore[$storeCode]->getId();
        }

        $csvData = [];

        // push default label data
        $csvData[] = ['sku', 'qty'];

        $arrayData = explode("\n", $param);
        if (sizeof($arrayData) > 0) {
            foreach ($arrayData as $item) {
                $csvData[] = explode(":", $item);
            }
        }

        return $this->dataHelper->getDataByListSkuQty($csvData, $storeId);
    }

    /**
     * get current currency
     * @return mixed
     */
    public function getCurrency()
    {
        try {
            $currencyCode = $this->dataHelper->getMainCurrencyCode();
            return $currencyCode;
        } catch (NoSuchEntityException $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
