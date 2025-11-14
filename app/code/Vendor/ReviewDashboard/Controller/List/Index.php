<?php
namespace Vendor\ReviewDashboard\Controller\List;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Customer\Model\Session;

class Index extends Action
{
    protected $orderCollectionFactory;
    protected $customerSession;

    public function __construct(
        Context $context,
        CollectionFactory $orderCollectionFactory,
        Session $customerSession
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    public function execute()
    {
        if (!$this->customerSession->isLoggedIn()) {
            return $this->_redirect('customer/account/login');
        }

        return $this->resultFactory
            ->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);
    }
}
