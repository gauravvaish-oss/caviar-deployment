<?php
namespace Vendor\PageBannerAttributes\Model;

use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Exception\LocalizedException;

class ImageUploader
{
    protected $uploaderFactory;
    protected $filesystem;
    protected $adapterFactory;
    protected $mediaDirectory;

    public function __construct(
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        AdapterFactory $adapterFactory
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        $this->adapterFactory = $adapterFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    public function saveFileToTmpDir($fileId)
    {
        $baseTmpPath = 'vendor/tmp';
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'webp']);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);

        $result = $uploader->save($this->mediaDirectory->getAbsolutePath($baseTmpPath));
        if (!$result) {
            throw new LocalizedException(__('File cannot be saved to path: %1', $baseTmpPath));
        }

        $result['url'] = '/media/' . $baseTmpPath . $result['file'];
        return $result;
    }
}
