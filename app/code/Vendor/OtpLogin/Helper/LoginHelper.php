<?php
namespace Vendor\OtpLogin\Helper;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Integration\Api\CustomerTokenServiceInterface;

class LoginHelper
{
    protected $customerRepository;
    protected $searchCriteriaBuilder;
    protected $tokenService;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerTokenServiceInterface $tokenService
    ) {
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->tokenService = $tokenService;
    }

    /**
     * Login customer using mobile (OTP login)
     */
    public function loginCustomerByMobile($mobile)
    {
        try {

            /** 1. Get customer by custom mobile attribute "phone" */
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
            $customerId = $customer->getId();

            /** 2. Generate Magento customer token */
            $token = $this->tokenService->createCustomerAccessToken($customerId);

            /** 3. Return success + customer data */
            return [
                'success' => true,
                'message' => 'Login successful.',
                'customer_id' => $customerId,
                'customer_email' => $customer->getEmail(),
                'token' => $token
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
