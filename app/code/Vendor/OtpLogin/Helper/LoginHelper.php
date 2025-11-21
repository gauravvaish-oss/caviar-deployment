<?php
namespace Vendor\OtpLogin\Helper;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;

class LoginHelper
{
    protected $customerRepository;
    protected $session;
    protected $searchCriteriaBuilder;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        Session $session,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->customerRepository = $customerRepository;
        $this->session = $session;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Login customer by mobile number
     */
    public function loginCustomerByMobile($mobile)
{
    try {
        // Search customer by mobile_no
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('phone', $mobile, 'eq')
            ->create();

        $customers = $this->customerRepository->getList($searchCriteria);

        if ($customers->getTotalCount() === 0) {
            return [
                'success' => false,
                'message' => 'Mobile number is not registered.'
            ];
        }

        $customerInterface = current($customers->getItems());

        /** IMPORTANT: Start Session First */
        $this->session->start();

        /** 1. Set customer data in session */
        $this->session->setCustomerData($customerInterface);

        /** 2. Set customer ID */
        $this->session->setCustomerId($customerInterface->getId());

        /** 3. Force logged-in flag */
        $this->session->setIsCustomerLoggedIn(true);

        /** 4. Regenerate session ID for security */
        $this->session->regenerateId();

        return [
            'success' => true,
            'message' => 'Login successful.',
            'customer_id' => $customerInterface->getId(),
            'customer_email' => $customerInterface->getEmail()
        ];

    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => 'Login failed.',
            'error' => $e->getMessage()
        ];
    }
}

}
