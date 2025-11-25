<?php
namespace Vendor\OtpLogin\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Url as CustomerUrl;
use Vendor\OtpLogin\Model\OtpManager;
use Vendor\OtpLogin\Helper\LoginHelper;
use Magento\Framework\Controller\Result\JsonFactory;

class Verify extends Action
{
    protected $otpManager;
    protected $loginHelper;
    protected $customerUrl;
    protected $resultJsonFactory;

    public function __construct(
        Context $context,
        OtpManager $otpManager,
        LoginHelper $loginHelper,
        CustomerUrl $customerUrl,
        JsonFactory $resultJsonFactory
    ) {
        $this->otpManager = $otpManager;
        $this->loginHelper = $loginHelper;
        $this->customerUrl = $customerUrl;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $mobile = $this->getRequest()->getParam('mobile');
        $otp    = $this->getRequest()->getParam('otp');

        if ($this->otpManager->validateOtp($mobile, $otp)) {
            $login = $this->loginHelper->loginCustomerByMobile($mobile);
            if ($login['success']) {
                return $resultJson->setData([
                    'success' => true,
                    'message' => 'OTP Verified. Logging in...',
                    'redirect_url' => $this->_url->getUrl('customer/account')
                ]);
            } else {
                return $resultJson->setData([
                    'success' => false,
                    'message' => 'Login failed: ' . $login['message']
                ]);
            }
        }

        return $resultJson->setData([
            'success' => false,
            'message' => 'Invalid OTP.'
        ]);
    }
}
