<?php
namespace Vendor\OtpLogin\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Vendor\OtpLogin\Model\OtpManager;
use Vendor\OtpLogin\Helper\LoginHelper;

class Verify extends Action
{
    protected $jsonFactory;
    protected $otpManager;
    protected $loginHelper;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        OtpManager $otpManager,
        LoginHelper $loginHelper
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->otpManager = $otpManager;
        $this->loginHelper = $loginHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();

        $mobile = $this->getRequest()->getParam('mobile');
        $otp    = $this->getRequest()->getParam('otp');

        if ($this->otpManager->validateOtp($mobile, $otp)) {
            $this->loginHelper->loginCustomerByMobile($mobile);
            // echo 'success';die;
            return $result->setData(['success' => true, 'message' => 'OTP verified and customer logged in']);
        }

        return $result->setData(['success' => false, 'message' => 'Invalid OTP']);
    }
}
