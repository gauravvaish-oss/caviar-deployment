<?php
namespace Vendor\CmsImage\Model;

use Magento\Cms\Model\PageFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class CmsImage
{
    protected $fileSystem;
    protected $pageFactory;
    protected $mediaDirectory;

    public function __construct(
        Filesystem $fileSystem,
        PageFactory $pageFactory
    ) {
        $this->fileSystem = $fileSystem;
        $this->pageFactory = $pageFactory;
        $this->mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
    }

    public function saveImage($imageData, $pageId)
    {
        // Save the uploaded image to the media folder
        $imageName = $imageData['name'];
        $imageTmpPath = $imageData['tmp_name'];
        
        // Generate a unique name for the image and save it
        $newImageName = uniqid() . '_' . $imageName;
        $mediaPath = 'cms/custom_images/';
        $targetPath = $this->mediaDirectory->getAbsolutePath($mediaPath . $newImageName);
        
        move_uploaded_file($imageTmpPath, $targetPath);

        // Save the image path to the CMS page
        $page = $this->pageFactory->create()->load($pageId);
        $page->setCustomImage($newImageName);
        $page->save();
        
        return $newImageName;
    }
}
