<?php

class SamedayCourier_Shipping_Model_Resource_Package extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {
        $this->_init('samedaycourier_shipping/package', 'id');
    }
}