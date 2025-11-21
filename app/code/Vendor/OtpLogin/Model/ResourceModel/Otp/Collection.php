<?php
namespace Vendor\OtpLogin\Model\ResourceModel\Otp;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Vendor\OtpLogin\Model\Otp::class,
            \Vendor\OtpLogin\Model\ResourceModel\Otp::class
        );
    }
}
