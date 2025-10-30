<?php

namespace Lof\FastOrder\Model\Config\Source;
class PositionOption implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Near mini cart')],
            ['value' => '2', 'label' => __('In top menu')],
            ['value' => '3', 'label' => __('In footer')],
            ['value' => '4', 'label' => __('Display on customer dashboard')]
        ];
    }
}