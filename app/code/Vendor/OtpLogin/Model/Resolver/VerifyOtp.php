<?php
namespace Vendor\OtpLogin\Model\Resolver;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Vendor\OtpLogin\Model\OtpManager;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\Integration\Model\Oauth\TokenFactory; // <- new

class VerifyOtp implements ResolverInterface
{
    private $otpManager;
    private $customerRepo;
    private $searchCriteriaBuilder;
    private $tokenService;
    private $tokenFactory; // <- new

    public function __construct(
        OtpManager $otpManager,
        CustomerRepositoryInterface $customerRepo,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerTokenServiceInterface $tokenService,
        TokenFactory $tokenFactory // <- inject
    ) {
        $this->otpManager = $otpManager;
        $this->customerRepo = $customerRepo;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->tokenService = $tokenService;
        $this->tokenFactory = $tokenFactory; // <- assign
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $identifier = $args['input']['identifier'] ?? null; // email OR phone
        $otp = $args['input']['code'] ?? null;

        if (!$identifier || !$otp) {
            return ['success' => false, 'message' => 'Identifier and OTP required', 'token' => null];
        }

        /** Validate OTP */
        if (!$this->otpManager->validateOtp($identifier, $otp)) {
            return ['success' => false, 'message' => 'Invalid or expired OTP', 'token' => null];
        }

        /** Find customer */
        $customer = null;

        try {
            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $customer = $this->customerRepo->get($identifier);
            } else {
                $searchCriteria = $this->searchCriteriaBuilder
                    ->addFilter('phone', $identifier, 'eq')
                    ->create();

                $result = $this->customerRepo->getList($searchCriteria);

                if ($result->getTotalCount() > 0) {
                    $customer = current($result->getItems());
                }
            }
        } catch (\Exception $e) {
            $customer = null;
        }

        if (!$customer) {
            return [
                'success' => false,
                'message' => 'Customer not found for this phone/email',
                'token' => null
            ];
        }

        /** DEBUG: Print all available methods of token service */
        // $methods = get_class_methods($this->tokenService);
        // echo "<pre>";
        // print_r($methods);
        // echo "</pre>";
        // die;

        /** Generate customer access token using TokenFactory (works in 2.4.6) */
        
        try {
            $tokenModel = $this->tokenFactory->create();
            $tokenModel->createCustomerToken($customer->getId());
            $token = $tokenModel->getToken();
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Token generation failed: ' . $e->getMessage(),
                'token' => null
            ];
        }
        

        return [
            'success' => true,
            'message' => 'OTP verified successfully',
            'token'   => $token ?? null
        ];
    }
}
