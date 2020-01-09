<?php

class SamedayCourier_Shipping_Model_Pickuppoint extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('samedaycourier_shipping/pickuppoint');
    }

    public function getDefaultPickupPoint()
    {
        $testing = Mage::getStoreConfig('carriers/samedaycourier_shipping/is_testing', Mage::app()->getStore());
        $pickupPoints = Mage::getModel('samedaycourier_shipping/pickuppoint')
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('is_testing', $testing)
            ->addFieldToFilter('is_default', 1)
            ->getData();

        if (!empty($pickupPoints)) {
            return $pickupPoints[0]['sameday_id'];
        }
    }

    /**
     * @param $testing
     * @return array
     */
    public function getPickupPoints($testing)
    {
        $pickupPoints = Mage::getModel('samedaycourier_shipping/pickuppoint')
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('is_testing', $testing)
            ->getData();

        return $pickupPoints;
    }

    /**
     * @param $sameday_id
     * @param $testing
     * @return array|null
     */
    public function getPickupPointSameday($sameday_id, $testing)
    {
        $pickupPoint = Mage::getModel('samedaycourier_shipping/pickuppoint')
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('sameday_id', $sameday_id)
            ->addFieldToFilter('is_testing', $testing)
            ->getData();

        if (!empty($pickupPoint)) {
            return $pickupPoint;
        }

        return null;
    }

    /**
     * @param $pickupPointObject
     * @param $testing
     * @throws Exception
     */
    public function addPickupPoint($pickupPointObject, $testing)
    {
        $pickupPoint = Mage::getModel('samedaycourier_shipping/pickuppoint');
        $this->setParams($pickupPoint, $pickupPointObject, $testing);
        $pickupPoint->save();
    }

    /**
     * @param $pickupPointObject
     * @param $testing
     * @throws Exception
     */
    public function updatePickupPoint($id, $pickupPointObject, $testing)
    {
        $pickupPoint = Mage::getModel('samedaycourier_shipping/pickuppoint');
        $pickupPoint->load($id);
        $this->setParams($pickupPoint, $pickupPointObject, $testing);
        $pickupPoint->save();
    }

    /**
     * @param $id
     * @throws Exception
     */
    public function deletePickupPoint($id)
    {
        $pickupPoint = Mage::getModel('samedaycourier_shipping/pickuppoint');
        $pickupPoint->load($id);
        $pickupPoint->delete();
    }

    /**
     * @param $pickupPoint
     * @param $pickupPointObject
     * @param $testing
     */
    private function setParams($pickupPoint, $pickupPointObject, $testing)
    {
        $pickupPoint->setSamedayId($pickupPointObject->getId());
        $pickupPoint->setSamedayAlias($pickupPointObject->getAlias());
        $pickupPoint->setIsTesting($testing);
        $pickupPoint->setIsDefault($pickupPointObject->isDefault());
        $pickupPoint->setCity($pickupPointObject->getCity()->getName());
        $pickupPoint->setCounty($pickupPointObject->getCounty()->getName());
        $pickupPoint->setContactPersons($pickupPointObject->getContactPersons());
    }
}

