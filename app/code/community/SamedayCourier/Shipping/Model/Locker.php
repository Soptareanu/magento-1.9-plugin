<?php

class SamedayCourier_Shipping_Model_Locker extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('samedaycourier_shipping/locker');
    }

    /**
     * @param $testing
     * @return array
     */
    public function getLockers($testing)
    {
        $lockers = Mage::getModel('samedaycourier_shipping/locker')
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('is_testing', $testing)
            ->getData();

        return $lockers;
    }

    /**
     * @param $locker_id
     * @param $testing
     * @return array|null
     */
    public function getLockerSameday($locker_id, $testing)
    {
        $locker = Mage::getModel('samedaycourier_shipping/locker')
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('locker_id', $locker_id)
            ->addFieldToFilter('is_testing', $testing)
            ->getData();

        if (!empty($locker)) {
            return $locker;
        }

        return null;
    }

    /**
     * @param object $lockerObject
     * @param bool $testing
     * @throws Exception
     */
    public function addLocker($lockerObject, $testing)
    {
        $locker = Mage::getModel('samedaycourier_shipping/locker');
        $this->setParams($locker, $lockerObject, $testing);
        $locker->save();
    }

    /**
     * @param $lockerObject
     * @param $testing
     * @throws Exception
     */
    public function updateLocker($id, $lockerObject, $testing)
    {
        $locker = Mage::getModel('samedaycourier_shipping/locker');
        $locker->load($id);
        $this->setParams($locker, $lockerObject, $testing);
        //$locker->save();
    }

    /**
     * @param $id
     * @throws Exception
     */
    public function deleteLocker($id)
    {
        $locker = Mage::getModel('samedaycourier_shipping/locker');
        $locker->load($id);
        $locker->delete();
    }

    /**
     * @param object $locker
     * @param object $lockerObject
     * @param bool $testing
     */
    private function setParams($locker, $lockerObject, $testing)
    {
        $locker->setLockerId($lockerObject->getId());
        $locker->setName($lockerObject->getName());
        $locker->setCity($lockerObject->getCity());
        $locker->setCounty($lockerObject->getCounty());
        $locker->setAddress($lockerObject->getAddress());
        $locker->setLat($lockerObject->getLat());
        $locker->setLng($lockerObject->getLong());
        $locker->setPostalCode($lockerObject->getPostalCode());
        $locker->setBoxes(Mage::helper('samedaycourier_shipping')->_serializeData($lockerObject->getBoxes()));
        $locker->setIsTesting($testing);
    }
}
