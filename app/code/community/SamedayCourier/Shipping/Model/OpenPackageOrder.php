<?php

class SamedayCourier_Shipping_Model_OpenPackageOrder extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('samedaycourier_shipping/openPackageOrder');
    }

    /**
     * @param array $params
     *
     * @throws Exception
     */
    public function saveOpenPackageOrder($params)
    {
        $openPackageOrder = Mage::getModel('samedaycourier_shipping/openPackageOrder');
        $this->setParams($openPackageOrder, $params);
        $openPackageOrder->save();
    }

    /**
     * @param $orderId
     *
     * @return bool
     */
    public function getOpenPackageByOrderId($orderId)
    {
        $openPackageOrder = Mage::getModel('samedaycourier_shipping/openPackageOrder')
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('order_id', $orderId)
            ->getData();

        return isset($openPackageOrder[0]) ? $openPackageOrder[0]['is_open_package'] : false;
    }

    /**
     * @param object $openPackageOrder
     *
     * @param $params
     */
    private function setParams($openPackageOrder, $params)
    {
        $openPackageOrder->setIsOpenPackage($params['is_open_package']);
        $openPackageOrder->setOrderId($params['order_id']);
    }
}
