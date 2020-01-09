<?php

class SamedayCourier_Shipping_Model_LockerOrder extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('samedaycourier_shipping/lockerOrder');
    }

    /**
     * @param array $params
     *
     * @throws Exception
     */
    public function saveLockerOrder($params)
    {
        $lockerOrder = Mage::getModel('samedaycourier_shipping/lockerOrder');
        $this->setParams($lockerOrder, $params);
        $lockerOrder->save();
    }

    /**
     * @param $orderId
     *
     * @return int|null
     */
    public function getLockerIdByOrderId($orderId)
    {
        $locker = Mage::getModel('samedaycourier_shipping/lockerOrder')
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('order_id', $orderId)
            ->getData();

        return isset($locker[0]) ? $locker[0]['locker_id'] : null;
    }

    /**
     * @param object $lockerOrder
     *
     * @param $params
     */
    private function setParams($lockerOrder, $params)
    {
        $lockerOrder->setLockerId($params['locker_id']);
        $lockerOrder->setOrderId($params['order_id']);
    }
}
