<?php
namespace Vendor\OtpLogin\Model\Resolver;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Vendor\OtpLogin\Model\OtpManager;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Encryption\EncryptorInterface;

class ResetPasswordByOtp implements ResolverInterface
{
    private $otpManager;
    private $customerRepo;
    private $searchCriteriaBuilder;
    private $customerRegistry;
    private $encryptor;

    public function __construct(
        OtpManager $otpManager,
        CustomerRepositoryInterface $customerRepo,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerRegistry $customerRegistry,
        EncryptorInterface $encryptor
    ) {
        $this->otpManager = $otpManager;
        $this->customerRepo = $customerRepo;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerRegistry = $customerRegistry;
        $this->encryptor = $encryptor;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $identifier = $args['input']['identifier'] ?? null;
        $otp        = $args['input']['code'] ?? null;
        $password   = $args['input']['new_password'] ?? null;

        if (!$identifier || !$otp || !$password) {
            return [
                'success' => false,
                'message' => 'Identifier, OTP and new password are required'
            ];
        }

        /** Validate OTP */
        if (!$this->otpManager->validateOtp($identifier, $otp)) {
            return [
                'success' => false,
                'message' => 'Invalid or expired OTP'
            ];
        }

        /** Find customer */
        try {
            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $customer = $this->customerRepo->get($identifier);
            } else {
                $searchCriteria = $this->searchCriteriaBuilder
                    ->addFilter('phone', $identifier, 'eq')
                    ->create();

                $result = $this->customerRepo->getList($searchCriteria);

                if ($result->getTotalCount() === 0) {
                    throw new \Exception("Customer not found");
                }

                $customer = current($result->getItems());
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Customer not found for this phone/email'
            ];
        }

        /** Reset password with secure data */
        try {
            $customerId = $customer->getId();

            // Secure data for password change
            $customerSecure = $this->customerRegistry->retrieveSecureData($customerId);

            $customerSecure->setRpToken(null);
            $customerSecure->setRpTokenCreatedAt(null);
            $customerSecure->setPasswordHash(
                $this->encryptor->getHash($password, true)
            );

            // Retrieve full customer model for the push()
            $customerModel = $this->customerRegistry->retrieve($customerId);
            $this->customerRegistry->push($customerModel);

            // Save customer entity
            $this->customerRepo->save($customer);

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Password update failed: ' . $e->getMessage()
            ];
        }

        return [
            'success' => true,
            'message' => 'Password reset successfully'
        ];
    }
}
