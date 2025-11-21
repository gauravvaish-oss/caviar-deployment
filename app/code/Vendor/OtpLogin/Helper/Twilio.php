<?php
namespace Vendor\OtpLogin\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Twilio\Rest\Client;

class Twilio extends AbstractHelper
{
    const XML_PATH_SID    = 'vendor_otplogin/twilio/sid';
    const XML_PATH_TOKEN  = 'vendor_otplogin/twilio/token';
    const XML_PATH_FROM   = 'vendor_otplogin/twilio/from';
    const XML_PATH_ENABLE = 'vendor_otplogin/twilio/enabled';

    public function sendSms($to, $message)
    {
        $sid   = $this->scopeConfig->getValue(self::XML_PATH_SID);
        $token = $this->scopeConfig->getValue(self::XML_PATH_TOKEN);
        $from  = $this->scopeConfig->getValue(self::XML_PATH_FROM);

        try {
            $client = new Client($sid, $token);

            $response = $client->messages->create(
                $to,
                [
                    'from' => $from,
                    'body' => $message
                ]
            );

            return [
                'success' => true,
                'sid'     => $response->sid,
                'status'  => $response->status,
                'to'      => $to,
                'from'    => $from
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error'   => $e->getMessage(),
                'to'      => $to,
                'from'    => $from
            ];
        }
    }

}
