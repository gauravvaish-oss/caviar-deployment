<?php
namespace Vendor\OtpLogin\Model\ResourceModel;

use Vendor\OtpLogin\Model\OtpFactory;
use Vendor\OtpLogin\Model\ResourceModel\Otp as OtpResource;
use Vendor\OtpLogin\Model\ResourceModel\Otp\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class OtpRepository
{
    private $otpFactory;
    private $resource;
    private $collectionFactory;

    public function __construct(
        OtpFactory $otpFactory,
        OtpResource $resource,
        CollectionFactory $collectionFactory
    ) {
        $this->otpFactory = $otpFactory;
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
    }

    public function saveNew($identifier, $otpHash, $expiresAt)
    {
        $model = $this->otpFactory->create();
        $model->setData([
            'identifier' => $identifier,
            'otp_hash' => $otpHash,
            'expires_at' => $expiresAt,
            'attempts' => 0,
            'used' => 0
        ]);
        $this->resource->save($model);
        return $model;
    }

    public function getLatestByIdentifier($identifier)
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('identifier', $identifier);
        $collection->setOrder('created_at', 'DESC');
        $collection->setPageSize(1);
        $item = $collection->getFirstItem();
        if (!$item || !$item->getId()) {
            return null;
        }
        return $item;
    }

    public function markUsed($id)
    {
        $model = $this->otpFactory->create();
        $this->resource->load($model, $id);
        $model->setData('used', 1);
        $this->resource->save($model);
    }

    public function incrementAttempts($id)
    {
        $model = $this->otpFactory->create();
        $this->resource->load($model, $id);
        $attempts = (int)$model->getData('attempts') + 1;
        $model->setData('attempts', $attempts);
        $this->resource->save($model);
    }
}
