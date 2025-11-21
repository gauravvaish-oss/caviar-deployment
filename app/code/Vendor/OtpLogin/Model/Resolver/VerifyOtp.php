<?php
namespace Vendor\OtpLogin\Model\Resolver;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Vendor\OtpLogin\Model\OtpManager;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Integration\Model\Oauth\TokenFactory;
use Magento\Customer\Model\CustomerFactory;

class VerifyOtp implements ResolverInterface
{
    private $otpManager;
    private $customerRepo;
    private $tokenFactory;
    private $customerFactory;

    public function __construct(
        OtpManager $otpManager,
        CustomerRepositoryInterface $customerRepo,
        TokenFactory $tokenFactory,
        CustomerFactory $customerFactory
    ) {
        $this->otpManager = $otpManager;
        $this->customerRepo = $customerRepo;
        $this->tokenFactory = $tokenFactory;
        $this->customerFactory = $customerFactory;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $identifier = $args['input']['identifier'] ?? null;
        $code = $args['input']['code'] ?? null;

        if (!$identifier || !$code) {
            return ['success' => false, 'message' => 'Identifier and code required', 'token' => null];
        }

        if (!$this->otpManager->verifyOtp($identifier, $code)) {
            return ['success' => false, 'message' => 'Invalid or expired code', 'token' => null];
        }

        $customer = null;
        try {
            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $customer = $this->customerRepo->get($identifier);
            } else {
                $model = $this->customerFactory->create();
                $model->setWebsiteId(1);
                $model->loadByAttribute('mobile', $identifier);
                if ($model && $model->getId()) {
                    $customer = $this->customerRepo->getById($model->getId());
                }
            }
        } catch (\Exception $e) {
            $customer = null;
        }

        if (!$customer) {
            return ['success' => false, 'message' => 'Customer not found', 'token' => null];
        }

        // create customer token
        $tokenModel = $this->tokenFactory->create();
        $token = $tokenModel->createCustomerToken($customer->getId())->getToken();

        return ['success' => true, 'message' => 'OTP verified', 'token' => $token];
    }
}
