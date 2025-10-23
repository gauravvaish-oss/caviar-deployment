<?php
namespace Vendor\CmsImage\Block;

use Magento\Cms\Model\PageFactory;
use Magento\Framework\View\Element\Template;

class Image extends Template
{
    protected $pageFactory;

    public function __construct(
        Template\Context $context,
        PageFactory $pageFactory,
        array $data = []
    ) {
        $this->pageFactory = $pageFactory;
        parent::__construct($context, $data);
    }

    public function getCmsPageImage($pageId)
    {
        $page = $this->pageFactory->create()->load($pageId);
        return $page->getCustomImage(); // This retrieves the custom image path from the CMS page model
    }
}
