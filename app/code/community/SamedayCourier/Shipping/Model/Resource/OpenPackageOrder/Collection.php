<?php

class SamedayCourier_Shipping_Model_Resource_OpenPackageOrder_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('samedaycourier_shipping/openPackageOrder');
    }
}
