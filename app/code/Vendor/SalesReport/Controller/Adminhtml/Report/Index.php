<?php
namespace Vendor\SalesReport\Controller\Adminhtml\Report;


use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;


class Index extends Action
{
const ADMIN_RESOURCE = 'Vendor_SalesReport::post';


/** @var PageFactory */
protected $resultPageFactory;


public function __construct(Context $context, PageFactory $resultPageFactory)
{
parent::__construct($context);
$this->resultPageFactory = $resultPageFactory;
}


public function execute()
{
$resultPage = $this->resultPageFactory->create();
$resultPage->setActiveMenu('Vendor_SalesReport::post');
$resultPage->getConfig()->getTitle()->prepend(__('Customer Sales Report'));
return $resultPage;
}
}