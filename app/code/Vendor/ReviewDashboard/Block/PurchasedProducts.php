<?php
namespace Vendor\ReviewDashboard\Block;

use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class PurchasedProducts extends Template
{
    protected $orderCollectionFactory;
    protected $customerSession;

    public function __construct(
        Template\Context $context,
        CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    public function getPurchasedProducts()
    {
        $customerId = $this->customerSession->getId();

        $orders = $this->orderCollectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', $customerId);

        $productData = [];

        foreach ($orders as $order) {
            foreach ($order->getAllVisibleItems() as $item) {
                $productData[$item->getProductId()] = [
                    'name' => $item->getName(),
                    'id'   => $item->getProductId()
                ];
            }
        }

        return $productData;
    }
}
