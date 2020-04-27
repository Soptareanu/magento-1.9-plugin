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

    /**
     * @param Varien_Event_Observer $observer
     */
    public function afterPlaceOrder(Varien_Event_Observer $observer)
    {
        $coreSession = Mage::getSingleton('core/session');
        $orderId = $observer->getData('order')['entity_id'];

        $lockerId = null;
        $isOpenPackage = null;

        if (null !== $coreSession) {
            $lockerId = $coreSession->getData('samedaycourier_locker_id');
            $isOpenPackage = $coreSession->getData('sameday_open_package');
        }


        if (!$orderId) {
            return;
        }

        if (null !== $lockerId) {
            $params = array(
                'order_id' => $orderId,
                'locker_id' => $lockerId
            );

            if (null !== ($lockerOrder = Mage::getSingleton('samedaycourier_shipping/lockerOrder'))) {
                $lockerOrder->saveLockerOrder($params);
            }
        }

        if (null !== $isOpenPackage) {
            $params = array(
                'order_id' => $orderId,
                'is_open_package' => $isOpenPackage
            );

            if (null !== ($openPackageOrder = Mage::getSingleton('samedaycourier_shipping/openPackageOrder'))) {
                $openPackageOrder->saveOpenPackageOrder($params);
            }
        }
    }
}
