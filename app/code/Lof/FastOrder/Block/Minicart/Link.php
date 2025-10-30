<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the landofcoder.com license that is
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
 * @copyright  Copyright (c) 2019 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\FastOrder\Block\Minicart;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\DefaultPathInterface;
use Magento\Framework\View\Element\Html\Link\Current;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\UrlInterface;
use Lof\FastOrder\Helper\Data;
//class Link extends Current
class Link extends Current
{
    protected $_customerSession;
    protected $_urlInterface;
    protected $_fastOrderHelper;

    public function __construct(
        UrlInterface $urlInterface,
        Context $context,
        DefaultPathInterface $defaultPath,
        CustomerSession $customerSession,
        Data $fastorderData,
        array $data = []
    ) {
        $this->_urlInterface = $urlInterface;
        $this->_customerSession = $customerSession;
        $this->_fastOrderHelper = $fastorderData;
        parent::__construct($context, $defaultPath, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {

        $customerGroupId = $this->_customerSession->getCustomerGroupId();
        $checkCustomerGroup = $this->_scopeConfig->getvalue('loffastorder/general/enable_special_groups');
        $group = explode(",", $checkCustomerGroup);
        $checkEnablePosition = $this->_scopeConfig->getValue('loffastorder/position/position_shortcut');
        $checkdontshow = $this->_scopeConfig->getValue('loffastorder/position/not_show_menu');
        $position = explode(",", $checkEnablePosition);
        // 1 when enable don't show menu
        if($checkdontshow==1)
        {
            return '';
        }
        // 1 is position fastorder menu in near mini cart
        if (in_array($customerGroupId, $group) && in_array('1', $position)) {
            $router = $this->_fastOrderHelper->getFastOrderRoute();
            return '<div class="minicart-wrapper">
                <a style =" color :#333;font-size: 15.3px;font-style: normal;position:relative;bottom:-7px;left:-2px"
                 href="' . $this->_urlInterface->getUrl($router)
                . '" title="fastorder">'.__("FastOrder").'</a>
        </div>';
        }
    }
}
