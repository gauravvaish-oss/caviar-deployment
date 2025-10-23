<?php
namespace Vendor\PageBannerAttributes\Controller\Adminhtml\Image;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Vendor\PageBannerAttributes\Model\ImageUploader;

class Upload extends Action
{
    protected $imageUploader;
    protected $jsonFactory;

    public function __construct(
        Action\Context $context,
        ImageUploader $imageUploader,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
        $this->jsonFactory = $jsonFactory;
    }

    public function execute()
    {
        try {
            $result = $this->imageUploader->saveFileToTmpDir('image');
            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->jsonFactory->create()->setData($result);
    }
}
