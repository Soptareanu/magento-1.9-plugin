<?php

/**
 * Class SamedayCourier_Shipping_Model_Observer
 */
class SamedayCourier_Shipping_Model_Observer extends Varien_Object
{
    /**
     * @return Mage_Core_Model_Abstract|null
     */
    public function getCoreSession()
    {
        return Mage::getSingleton('core/session');
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function storeSamedayCourierCheckoutAdditionalInfo(Varien_Event_Observer $observer)
    {
        if (null !== $this->getCoreSession()) {
            $this->getCoreSession()->setData('samedaycourier_locker_id', null);
            if ($observer->getRequest()->get('locker_select')) {
                $this->getCoreSession()->setData('samedaycourier_locker_id', $observer->getRequest()->get('locker_select'));
            }

            $this->getCoreSession()->setData('sameday_open_package', null);
            if ($observer->getRequest()->get('sameday_open_package')
                && $observer->getRequest()->get('sameday_open_package') === $observer->getRequest()->get('shipping_method')
            ) {
                $this->getCoreSession()->setData('sameday_open_package', 1);
            }
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function afterPlaceOrder(Varien_Event_Observer $observer)
    {
        $orderId = $observer->getData('order')['entity_id'];

        $lockerId = null;
        $isOpenPackage = null;

        if (null !== $this->getCoreSession()) {
            $lockerId = $this->getCoreSession()->getData('samedaycourier_locker_id');
            $isOpenPackage = $this->getCoreSession()->getData('sameday_open_package');
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
