<?php

namespace Lof\FastOrder\Api;

interface FastOrderManagementInterface
{
    /**
     * @param string $param
     * @param string $storeCode
     * @return mixed
     */
    public function postFastAddMultipleSKu($param, $storeCode);

    /**
     * get current currency
     * @return mixed
     */
    public function getCurrency();
}