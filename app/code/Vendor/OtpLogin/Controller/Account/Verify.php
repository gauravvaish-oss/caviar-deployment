<?php
namespace Vendor\OtpLogin\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Url as CustomerUrl;
use Vendor\OtpLogin\Model\OtpManager;
use Vendor\OtpLogin\Helper\LoginHelper;

class Verify extends Action
{
    protected $otpManager;
    protected $loginHelper;
    protected $customerUrl;

    public function __construct(
        Context $context,
        OtpManager $otpManager,
        LoginHelper $loginHelper,
        CustomerUrl $customerUrl
    ) {
        $this->otpManager = $otpManager;
        $this->loginHelper = $loginHelper;
        $this->customerUrl = $customerUrl;
        parent::__construct($context);
    }

    public function execute()
    {
        $mobile = $this->getRequest()->getParam('mobile');
        $otp    = $this->getRequest()->getParam('otp');

        if ($this->otpManager->validateOtp($mobile, $otp)) {
            $login = $this->loginHelper->loginCustomerByMobile($mobile);
            if ($login['success']) {
                // Redirect to My Account Dashboard
                return $this->_redirect('customer/account');
            }
        }

        // If OTP fails → redirect back to login page
        $this->messageManager->addErrorMessage(__('Invalid OTP.'));
        return $this->_redirect('customer/account/login');
    }
}
