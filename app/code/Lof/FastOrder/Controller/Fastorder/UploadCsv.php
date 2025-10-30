<?php
/**
 * Copyright (c) 2019  Landofcoder
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Lof\FastOrder\Controller\Fastorder;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManager;
use Psr\Log\LoggerInterface;

class UploadCsv extends Action
{
    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Filesystem $filesystem
     */
    protected $filesystem;

    /**
     * @var UploaderFactory $fileUploader
     */
    protected $fileUploader;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var Data
     */
    private $jsonHelper;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * CSV Processor
     */
    protected $csvProcessor;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var \Lof\FastOrder\Helper\Data
     */
    private $dataHelper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var StockItemRepository
     */
    private $stockItemRepository;

    /** @var PriceCurrencyInterface $priceCurrency */
    private $priceCurrency;

    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * Constructor
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $jsonHelper
     * @param Filesystem $filesystem
     * @param Csv $csvProcessor
     * @param ProductRepositoryInterface $productRepository
     * @param CollectionFactory $productCollectionFactory
     * @param \Lof\FastOrder\Helper\Data $dataHelper
     * @param LoggerInterface $logger
     * @param StockItemRepository $stockItemRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param StoreManager $storeManager
     * @throws FileSystemException
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $jsonHelper,
        Filesystem $filesystem,
        Csv $csvProcessor,
        ProductRepositoryInterface $productRepository,
        CollectionFactory $productCollectionFactory,
        \Lof\FastOrder\Helper\Data $dataHelper,
        LoggerInterface $logger,
        StockItemRepository $stockItemRepository,
        PriceCurrencyInterface $priceCurrency,
        StoreManager $storeManager
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->csvProcessor = $csvProcessor;
        $this->productRepository = $productRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->dataHelper = $dataHelper;
        $this->logger = $logger;
        $this->stockItemRepository = $stockItemRepository;
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;

        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $uploadedFile = $this->uploadFile();
        if ($uploadedFile == false) {
            return $this->jsonResponse([]);
        } else {
            return $this->jsonResponse(['product' => $uploadedFile]);
        }
    }

    /**
     * upload file
     * @return mixed
     */
    public function uploadFile()
    {
        // this folder will be created inside "pub/media" folder
        $yourFolderName = 'fast-order-csv/';
        // "my_custom_file" is the HTML input file name
        $yourInputFileName = 'csv_file';

        $pathFileFinal = $this->dataHelper->uploadFile(
            $this->getRequest(),
            $yourFolderName,
            $yourInputFileName,
            ['csv']
        );
        return $this->readCsvFile($pathFileFinal);
    }

    /**
     * create json response
     *
     * @param string $response
     * @return ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }

    /**
     * Read data from csv file
     * @param $pathFile
     * @return mixed
     */
    private function readCsvFile($pathFile)
    {
        $csvData = [];
        try {
            $csvData = $this->csvProcessor->getData($pathFile);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        $storeCode = $this->getRequest()->getParam('storeCode');
        $listStore = $this->storeManager->getStores(true, true);

        $storeId = 0;
        if (isset($listStore[$storeCode])) {
            $storeId = $listStore[$storeCode]->getId();
        }

        $productLisFull = $this->dataHelper->getDataByListSkuQty($csvData, $storeId);
        return $productLisFull;
    }
}
