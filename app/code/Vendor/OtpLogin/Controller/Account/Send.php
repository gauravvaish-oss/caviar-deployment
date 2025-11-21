<?php
namespace Vendor\OtpLogin\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Vendor\OtpLogin\Model\OtpManager;

class Send extends Action
{
    protected $jsonFactory;
    protected $otpManager;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        OtpManager $otpManager
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->otpManager = $otpManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->jsonFactory->create();
        $mobile = $this->getRequest()->getParam('mobile');

        if (!$mobile) {
            return $result->setData(['success' => false, 'message' => 'Mobile is required']);
        }

        $response = $this->otpManager->sendOtp($mobile);

        return $result->setData(['success' => true, 'message' => $response]);
    }
}
