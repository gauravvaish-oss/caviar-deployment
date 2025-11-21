<?php
namespace Vendor\OtpLogin\Model\Sender;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

class Twilio
{
    const XML_PATH_ENABLED = 'vendor_otplogin/twilio/enabled';
    const XML_PATH_SID = 'vendor_otplogin/twilio/sid';
    const XML_PATH_TOKEN = 'vendor_otplogin/twilio/token';
    const XML_PATH_FROM = 'vendor_otplogin/twilio/from';

    private $scopeConfig;
    private $logger;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    public function send($identifier, $otp)
    {
        // if disabled in admin, skip
        if (!$this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED)) {
            $this->logger->info('Twilio disabled in config; OTP: ' . $otp);
            return;
        }
        $sid = $this->scopeConfig->getValue(self::XML_PATH_SID);
        $token = $this->scopeConfig->getValue(self::XML_PATH_TOKEN);
        $from = $this->scopeConfig->getValue(self::XML_PATH_FROM);

        // very simple cURL call to Twilio API (ensure allow_url_fopen/cURL available)
        $body = http_build_query([
            'From' => $from,
            'To' => $identifier,
            'Body' => "Your OTP code: {$otp}"
        ]);

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $sid . ':' . $token);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            $this->logger->error('Twilio cURL error: ' . $err);
        } else {
            $this->logger->info('Twilio response: ' . $response);
        }
    }
}
