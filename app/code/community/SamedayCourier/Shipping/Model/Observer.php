<?php

/**
 * Class SamedayCourier_Shipping_Model_Observer
 */
class SamedayCourier_Shipping_Model_Observer extends Varien_Object
{
    public function storeSamedayCourierCheckoutAdditionalInfo(Varien_Event_Observer $observer)
    {
        if ($observer->getRequest()->get('locker_select')) {
            Mage::getSingleton('core/session')->setData('samedaycourier_locker_id', $observer->getRequest()->get('locker_select'));
        }

        if ($observer->getRequest()->get('sameday_open_package')
            && $observer->getRequest()->get('sameday_open_package') === $observer->getRequest()->get('shipping_method')
        ) {
            Mage::getSingleton('core/session')->setData('sameday_open_package', 1);
        }
    }

    public function afterPlaceOrder(Varien_Event_Observer $observer)
    {
        $orderId = $observer->getData('order')['entity_id'];

        $lockerId = Mage::getSingleton('core/session')->getData('samedaycourier_locker_id');
        $isOpenPackage = Mage::getSingleton('core/session')->getData('sameday_open_package');

        if (!$orderId) {
            return;
        }

        if ($lockerId) {
            $params = array(
                'order_id' => $orderId,
                'locker_id' => $lockerId
            );

            Mage::getSingleton('samedaycourier_shipping/lockerOrder')->saveLockerOrder($params);
        }

        if ($isOpenPackage) {
            $params = array(
                'order_id' => $orderId,
                'is_open_package' => $isOpenPackage
            );

            Mage::getSingleton('samedaycourier_shipping/lockerOrder')->saveOpenPackageOrder($params);
        }
    }
}
