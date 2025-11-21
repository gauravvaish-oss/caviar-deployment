<?php

namespace Vendor\OtpLogin\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class SaveMobile implements ObserverInterface
{
    protected $customerRepository;

    public function __construct(
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
    }

    public function execute(Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $request  = $observer->getEvent()->getAccountController()->getRequest();

        $mobile = $request->getParam('phone');

        if ($mobile) {
            $customer->setCustomAttribute('phone', $mobile);
            $this->customerRepository->save($customer);
        }
    }
}
