<?php
namespace Vendor\OtpLogin\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Vendor\OtpLogin\Model\ResourceModel\Otp as OtpResource;
use Vendor\OtpLogin\Model\OtpFactory;
use Vendor\OtpLogin\Helper\Twilio;

class OtpManager
{
    protected $otpFactory;
    protected $otpResource;
    protected $date;
    protected $twilio;

    public function __construct(
        OtpFactory $otpFactory,
        OtpResource $otpResource,
        DateTime $date,
        Twilio $twilio
    ) {
        $this->otpFactory  = $otpFactory;
        $this->otpResource = $otpResource;
        $this->date        = $date;
        $this->twilio      = $twilio;
    }

    public function sendOtp($mobile)
    {
        $otp = rand(100000, 999999);
        $hash = password_hash($otp, PASSWORD_DEFAULT);

        // Delete previous OTPs for this number
        $this->otpResource->deleteOldOtps($mobile);

        // Create new OTP row
        $otpModel = $this->otpFactory->create();
        $otpModel->setData([
            'identifier'  => $mobile,
            'otp_hash'    => $hash,
            'created_at'  => $this->date->gmtDate(),
            'expires_at'  => date("Y-m-d H:i:s", strtotime("+5 minutes")),
            'attempts'    => 0,
            'used'        => 0
        ]);

        $this->otpResource->save($otpModel);

        // Send SMS using Twilio
        $data = $this->twilio->sendSms($mobile, "Your OTP is: " . $otp);

        return $data;
    }

    public function validateOtp($mobile, $otpInput)
    {
        $otpModel = $this->otpFactory->create();
        $this->otpResource->loadByIdentifier($otpModel, $mobile);
        if (!$otpModel->getId()) {
            return false;
        }

        // Check expiration
        if (strtotime($otpModel->getExpiresAt()) < strtotime($this->date->gmtDate())) {
            return false;
        }

        // Check if already used
        if ($otpModel->getUsed()) {
            return false;
        }

        // Validate hash
        if (!password_verify($otpInput, $otpModel->getOtpHash())) {
            return false;
        }

        // Mark OTP as used
        $otpModel->setUsed(1);
        $this->otpResource->save($otpModel);

        return true;
    }
}
