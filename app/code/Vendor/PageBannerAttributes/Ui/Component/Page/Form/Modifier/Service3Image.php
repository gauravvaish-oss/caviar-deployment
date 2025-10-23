<?php
namespace Vendor\PageBannerAttributes\Ui\DataProvider\CmsPage\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Store\Model\StoreManagerInterface;

class Service3Image implements ModifierInterface
{
    protected $storeManager;

    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * Modify data before rendering the form
     */
    public function modifyData(array $data)
    {
        foreach ($data as $pageId => $page) {
            if (!empty($page['service_3_image'])) {
                // Decode JSON from DB
                $imageJson = $page['service_3_image'];
                $image = json_decode($imageJson, true);

                if ($image && isset($image['name'])) {
                    // Convert into array format for imageUploader
                    $data[$pageId]['service_3_image'] = [
                        [
                            'name' => $image['name'],
                            // If you saved 'url' in DB
                            'url'  => $image['url'] ?? $this->storeManager->getStore()
                                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                                . ltrim($image['file'] ?? '', '/')
                        ]
                    ];
                }
            }
        }

        return $data;
    }

    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
