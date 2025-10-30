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
namespace Lof\FastOrder\Block\TopLinks;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Lof\FastOrder\Helper\Data;
use Magento\Framework\UrlInterface;
class TopLink extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    protected $_urlInterface;
    private $_fastOrderHelper;

    public function __construct(
        Template\Context $context,
        ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $customerSession,
        UrlInterface $urlInterface,
        Data $fastorderData,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_scopeConfig = $scopeConfig;
        $this->_urlInterface = $urlInterface;
        $this->customerSession = $customerSession;
        $this->_fastOrderHelper = $fastorderData;
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {

        $customerGroupId = $this->customerSession->getCustomerGroupId();
        $checkCustomerGroup = $this ->_scopeConfig->getvalue('loffastorder/general/enable_special_groups');
        $group = explode(",", $checkCustomerGroup);
        $checkEnablePosition = $this->_scopeConfig->getValue('loffastorder/position/position_shortcut');
        $position = explode(",", $checkEnablePosition);
        // 2 is position in the top menu
        if (in_array($customerGroupId, $group) && in_array('2',$position)){
            $router = $this->_fastOrderHelper->getFastOrderRoute();
            return '<li><a href="' . $this->_urlInterface->getUrl($router) . '" >' . $this->escapeHtml($this->getLabel()) . '</a></li>';
        }

        else{
            return '';
        }

    }

}
