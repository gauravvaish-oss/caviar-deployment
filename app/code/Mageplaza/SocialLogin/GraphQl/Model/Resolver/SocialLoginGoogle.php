<?php

namespace Mageplaza\SocialLogin\GraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\SocialLogin\Helper\CustomData as SocialHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Integration\Model\Oauth\TokenFactory;
use Magento\Framework\Controller\Result\JsonFactory;

class SocialLoginGoogle implements ResolverInterface
{
    protected $accountManagement;
    protected $customerRepository;
    protected $socialHelper;
    protected $session;
    protected $tokenFactory;

    public function __construct(
        AccountManagementInterface $accountManagement,
        CustomerRepositoryInterface $customerRepository,
        Session $session,
        SocialHelper $socialHelper,
        TokenFactory $tokenFactory
    ) {
        $this->accountManagement = $accountManagement;
        $this->customerRepository = $customerRepository;
        $this->socialHelper = $socialHelper;
        $this->session = $session;
        $this->tokenFactory = $tokenFactory;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $idToken = $args['input']['idToken'] ?? null;
        if (!$idToken) {
            throw new LocalizedException(__('Missing Google ID token.'));
        }

        try {
            // ✅ 1. Verify Google Token
            $googleUser = $this->socialHelper->getGoogleUserData($idToken);
            if (empty($googleUser['email'])) {
                throw new LocalizedException(__('Invalid Google token.'));
            }

            // ✅ 2. Check if user already exists
            try {
                $customer = $this->customerRepository->get($googleUser['email']);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                // ✅ 3. Create new customer if not exists
                $customer = $this->accountManagement->createAccount(
                    new \Magento\Customer\Model\Data\Customer([
                        'firstname' => $googleUser['given_name'] ?? 'Google',
                        'lastname'  => $googleUser['family_name'] ?? 'User',
                        'email'     => $googleUser['email']
                    ]),
                    bin2hex(random_bytes(8)) // random password
                );
            }

            // ✅ 4. Generate token for login
            $token = $this->tokenFactory->create()->createCustomerToken($customer->getId())->getToken();

            // ✅ 5. Login user in session (optional)
            $this->session->setCustomerDataAsLoggedIn($customer);

            return [
                'success'  => true,
                'message'  => __('Google login successful.'),
                'customer' => $customer,
                'token'    => $token
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'customer' => null,
                'token' => null
            ];
        }
    }
}
