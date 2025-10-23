<?php
namespace Vendor\PageBannerAttributes\Plugin;

use Magento\Cms\Controller\Adminhtml\Page\Save;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class CmsPageSavePlugin
{
    protected $uploaderFactory;
    protected $filesystem;

    public function __construct(
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
    }

    public function beforeExecute(Save $subject)
    {
        $request = $subject->getRequest();
        $data = $request->getPostValue();
// dd($data);die;
        if (isset($data['service_1_image'][0]['name'])) {
             $data['service_1_image'] = json_encode(
                        [
                            'name' => $data['service_1_image'][0]['name'],
                            'url'  => $data['service_1_image'][0]['url']
                        ]);
          
        }
                if (isset($data['service_2_image'][0]['name'])) {
             $data['service_2_image'] = json_encode(
                        [
                            'name' => $data['service_2_image'][0]['name'],
                            'url'  => $data['service_2_image'][0]['url']
                        ]);
          
        }
                if (isset($data['service_3_image'][0]['name'])) {
             $data['service_3_image'] = json_encode(
                        [
                            'name' => $data['service_3_image'][0]['name'],
                            'url'  => $data['service_3_image'][0]['url']
                        ]);
          
        }
         if (isset($data['sales_banner_bg'][0]['name'])) {
             $data['sales_banner_bg'] = json_encode(
                        [
                            'name' => $data['sales_banner_bg'][0]['name'],
                            'url'  => $data['sales_banner_bg'][0]['url']
                        ]);
          
        } if (isset($data['sales_product_image'][0]['name'])) {
             $data['sales_product_image'] = json_encode(
                        [
                            'name' => $data['sales_product_image'][0]['name'],
                            'url'  => $data['sales_product_image'][0]['url']
                        ]);
          
        } if (isset($data['offer_product_image'][0]['name'])) {
             $data['offer_product_image'] = json_encode(
                        [
                            'name' => $data['offer_product_image'][0]['name'],
                            'url'  => $data['offer_product_image'][0]['url']
                        ]);
          
        } if (isset($data['banner_1'][0]['name'])) {
             $data['banner_1'] = json_encode(
                        [
                            'name' => $data['banner_1'][0]['name'],
                            'url'  => $data['banner_1'][0]['url']
                        ]);
          
        }  if (isset($data['banner_2'][0]['name'])) {
             $data['banner_2'] = json_encode(
                        [
                            'name' => $data['banner_2'][0]['name'],
                            'url'  => $data['banner_2'][0]['url']
                        ]);
          
        } if (isset($data['banner_3'][0]['name'])) {
             $data['banner_3'] = json_encode(
                        [
                            'name' => $data['banner_3'][0]['name'],
                            'url'  => $data['banner_3'][0]['url']
                        ]);
          
        } if (isset($data['offer_image_1'][0]['name'])) {
             $data['offer_image_1'] = json_encode(
                        [
                            'name' => $data['offer_image_1'][0]['name'],
                            'url'  => $data['offer_image_1'][0]['url']
                        ]);
          
        } if (isset($data['offer_image_2'][0]['name'])) {
             $data['offer_image_2'] = json_encode(
                        [
                            'name' => $data['offer_image_2'][0]['name'],
                            'url'  => $data['offer_image_2'][0]['url']
                        ]);
          
        } if (isset($data['shop_banner_image'][0]['name'])) {
             $data['shop_banner_image'] = json_encode(
                        [
                            'name' => $data['shop_banner_image'][0]['name'],
                            'url'  => $data['shop_banner_image'][0]['url']
                        ]);
          
        }
        if (!empty($data['testimonials_review_id'])) {
             $data['testimonials_review_id'] = implode(",",$data['testimonials_review_id']);
        }

        if (!empty($data['bestseller_products_product_id'])) {
            $data['bestseller_products_product_id'] = implode(",",$data['bestseller_products_product_id']);
        }
        if (!empty($data['featured_products_product_id'])) {
            $data['featured_products_product_id'] = implode(",",$data['featured_products_product_id']);
        }       
        if (!empty($data['latest_products_product_id'])) {
            $data['latest_products_product_id'] = implode(",",$data['latest_products_product_id']);
        }
        if (!empty($data['trending_product_categories'])) {
            $data['trending_product_categories'] = implode(",",$data['trending_product_categories']);
        }
        if (!empty($data['product_category_categories'])) {
            $data['product_category_categories'] = implode(",",$data['product_category_categories']);
        }
        if (!empty($data['top_products_categories'])) {
            $data['top_products_categories'] = implode(",",$data['top_products_categories']);
        }

        // dd($data);die;
          try {
                   
                    $request->setPostValue($data);

            } catch (\Exception $e) {
                // Log if needed
            }
    }
}
