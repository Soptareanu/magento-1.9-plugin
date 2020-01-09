<?php

class SamedayCourier_Shipping_Model_Service extends Mage_Core_Model_Abstract
{
    const STATUS_DISABLE = '0';
    const STATUS_ENABLE = '1';
    const STATUS_INTERVAL = '2';

    protected function _construct()
    {
        $this->_init('samedaycourier_shipping/service');
    }

    public function getAvailableStatuses()
    {
        return array(
            self::STATUS_DISABLE
            => Mage::helper('samedaycourier_shipping')->__('Disable'),
            self::STATUS_ENABLE
            => Mage::helper('samedaycourier_shipping')->__('Always'),
            self::STATUS_INTERVAL
            => Mage::helper('samedaycourier_shipping')
                ->__('Interval')
        );
    }

    /**
     * @param $testing
     * @return array
     */
    public function getServices($testing)
    {
        $services = Mage::getModel('samedaycourier_shipping/service')
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('is_testing', $testing)
            ->getData();

        return $services;
    }

    /**
     * @param $sameday_id
     * @param $testing
     * @return array|null
     */
    public function getServiceSameday($sameday_id, $testing)
    {
        $service = Mage::getModel('samedaycourier_shipping/service')
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('sameday_id', $sameday_id)
            ->addFieldToFilter('is_testing', $testing)
            ->getData();

        if (!empty($service)) {
            return $service;
        }

        return null;
    }

    /**
     * @param $id
     * @throws Exception
     */
    public function deleteService($id)
    {
        $service = Mage::getModel('samedaycourier_shipping/service');
        $service->load($id);
        $service->delete();
    }

    /**
     * @param $serviceObject
     * @param $testing
     * @throws Exception
     */
    public function addService($serviceObject, $testing)
    {
        $service = Mage::getModel('samedaycourier_shipping/service');
        $this->setParams($service, $serviceObject, $testing);
        $service->save();
    }

    /**
     * @param int $id
     *
     * @param object $serviceObject
     *
     * @param bool $testing
     *
     * @throws Exception
     */
    public function updateService($id, $serviceObject, $testing)
    {
        $service = Mage::getModel('samedaycourier_shipping/service');
        $service->load($id);
        $this->setParams($service, $serviceObject, $testing);
        $service->save();
    }

    /**
     * @param $service
     * @param $serviceObject
     * @param $testing
     */
    private function setParams($service, $serviceObject, $testing)
    {
        $service->setSamedayId($serviceObject->getId());
        $service->setSamedayName($serviceObject->getName());
        $service->setSamedayCode($serviceObject->getCode());
        $service->setIsTesting($testing);
    }
}