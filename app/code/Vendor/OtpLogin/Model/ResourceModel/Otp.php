<?php
namespace Vendor\OtpLogin\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Otp extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('vendor_otplogin_otp', 'otp_id');
    }

    public function loadByIdentifier($model, $identifier)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable())
            ->where('identifier = ?', $identifier)
            ->order('otp_id DESC')
            ->limit(1);

        $data = $connection->fetchRow($select);

        if ($data) {
            $model->setData($data);
        }

        return $model;
    }

    public function deleteOldOtps($identifier)
    {
        $this->getConnection()->delete(
            $this->getMainTable(),
            ['identifier = ?' => $identifier]
        );
    }
}
