<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_FastOrder
 * @copyright  Copyright (c) 2020 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\FastOrder\Model\Config\Source;
class AvailableAttributes implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'sku', 'label' => __('SKU')],
            ['value' => 'name', 'label' => __('Product Name')],
            ['value' => 'short_description', 'label' => __('Short Description')],
            ['value' => 'description', 'label' => __('Description')],
            ['value' => 'meta_description', 'label' => __('Meta Description')]
        ];
    }
}