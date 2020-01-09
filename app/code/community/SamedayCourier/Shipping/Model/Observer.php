<?php

/**
 * Class SamedayCourier_Shipping_Model_Observer
 */
class SamedayCourier_Shipping_Model_Observer extends Varien_Object
{
    public function storeLockerIdAfterChooseShippingMethod(Varien_Event_Observer $observer)
    {
        Mage::getSingleton('core/session')->setData('samedaycourier_locker_id', $observer->getRequest()->get('locker_select'));
    }

    public function afterPlaceOrder(Varien_Event_Observer $observer)
    {
        $orderId = $observer->getData('order')['entity_id'];
        $lockerId = Mage::getSingleton('core/session')->getData('samedaycourier_locker_id');

        if ($orderId && $lockerId) {
            $params = array(
                'order_id' => $orderId,
                'locker_id' => $lockerId
            );

            Mage::getSingleton('samedaycourier_shipping/lockerOrder')->saveLockerOrder($params);
        }
    }
}
