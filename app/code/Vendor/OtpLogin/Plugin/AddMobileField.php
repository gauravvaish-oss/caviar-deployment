<?php
namespace Vendor\OtpLogin\Plugin;

class AddMobileField
{
    public function afterGetFormFields(
        \Magento\Customer\Block\Form\Register $subject,
        $result
    ) {
        $result['mobile'] = [
            'type'     => 'text',
            'label'    => __('Mobile Number'),
            'required' => false,
            'value'    => ''
        ];

        return $result;
    }
}
