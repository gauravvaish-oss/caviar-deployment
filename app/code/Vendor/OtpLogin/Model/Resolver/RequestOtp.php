<?php
namespace Vendor\OtpLogin\Model\Resolver;

use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Vendor\OtpLogin\Model\OtpManager;
use Vendor\OtpLogin\Model\Sender\Twilio;

class RequestOtp implements ResolverInterface
{
    private $otpManager;
    private $twilio;

    public function __construct(OtpManager $otpManager, Twilio $twilio) {
        $this->otpManager = $otpManager;
        $this->twilio = $twilio;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $identifier = $args['input']['identifier'] ?? null;
        if (!$identifier) {
            return ['success' => false, 'message' => 'Identifier required'];
        }

        // basic rate-limiting or validation should be added by caller
        $data = $this->otpManager->sendOtp($identifier);
        $data = json_encode($data);
        return ['success' => true, 'message' => "{$data}"];
    }
}
