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

namespace Lof\FastOrder\Block\Fastorder;

use Lof\FastOrder\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManager;

class Quickview extends Template
{

    /**
     * @var StoreManager
     */
    private $storeManager;

    private $_fastOrderHelper;

    /**
     * Constructor
     *
     * @param Context $context
     * @param StoreManager $storeManager
     * @param Data $fastOrderHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        StoreManager $storeManager,
        Data $fastOrderHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeManager = $storeManager;
        $this->_fastOrderHelper = $fastOrderHelper;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @return string
     */
    public function getStoreCode()
    {
        return $this->storeManager->getStore()->getCode();
    }

    /**
     * @inheritdoc
     */
    public function _toHtml()
    {
        if (!$this->getConfig('general/enabled')) {
            return;
        }
        return parent::_toHtml();
    }

    /**
     * @inheritdoc
     */
    protected function _addBreadcrumbs()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        $page_title = $this->getConfig('general/page_title');
        $show_breadcrumbs = $this->getConfig('general/show_breadcrumbs');

        if ($show_breadcrumbs && $breadcrumbsBlock) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link'  => $baseUrl
                ]
            );

            $breadcrumbsBlock->addCrumb(
                'faqpage',
                [
                    'label' => $page_title,
                    'title' => $page_title,
                    'link'  => ''
                ]
            );
        }
    }

    /**
     * get config
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getConfig($key, $default = '')
    {
        if ($this->hasData($key)) {
            return $this->getData($key);
        }
        $result = $this->_fastOrderHelper->getConfig($key);
        $c = explode("/", $key);
        if ($this->hasData($c[1])) {
            return $this->getData($c[1]);
        }
        if ($result == "") {
            $this->setData($c[1], $default);
            return $default;
        }
        $this->setData($c[1], $result);
        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        $page_title = $this->getConfig('general/page_title');
        $meta_description = $this->getConfig('general/meta_description');
        $meta_keywords = $this->getConfig('general/meta_keywords');

        $this->_addBreadcrumbs();
        $this->pageConfig->addBodyClass('fastorder-page');
        if ($page_title) {
            $this->pageConfig->getTitle()->set($page_title);
        }
        if ($meta_keywords) {
            $this->pageConfig->setKeywords($meta_keywords);
        }
        if ($meta_description) {
            $this->pageConfig->setDescription($meta_description);
        }
        return parent::_prepareLayout();
    }
}
