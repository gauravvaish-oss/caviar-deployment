<?php
namespace Vendor\OtpLogin\Helper;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Customer\Model\Session as CustomerSession;

class LoginHelper
{
    protected $customerRepository;
    protected $searchCriteriaBuilder;
    protected $customerSession;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerSession $customerSession
    ) {
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerSession = $customerSession;
    }

    /**
     * Login customer using mobile (frontend)
     */
    public function loginCustomerByMobile($mobile)
    {
        try {
            // 1. Get customer by custom mobile attribute "phone"
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('phone', $mobile, 'eq')
                ->create();

            $customers = $this->customerRepository->getList($searchCriteria);

            if ($customers->getTotalCount() == 0) {
                return [
                    'success' => false,
                    'message' => 'Mobile number not registered.'
                ];
            }

            $customer = current($customers->getItems());

            // 2. Login customer in frontend
            $this->customerSession->setCustomerDataAsLoggedIn($customer);
            $this->customerSession->regenerateId(); // Optional: security

            return [
                'success' => true,
                'message' => 'Login successful.',
                'customer_id' => $customer->getId(),
                'customer_email' => $customer->getEmail()
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Login failed.',
                'error'   => $e->getMessage()
            ];
        }
    }
}
