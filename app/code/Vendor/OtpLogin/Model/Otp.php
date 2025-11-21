<?php
namespace Vendor\OtpLogin\Model;

use Magento\Framework\Model\AbstractModel;

class Otp extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Vendor\OtpLogin\Model\ResourceModel\Otp::class);
    }
}
