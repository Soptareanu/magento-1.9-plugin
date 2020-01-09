<?php

/**
 * Class SamedayCourier_Shipping_Block_Cart_Shipping
 */
class SamedayCourier_Checkout_Block_Cart_Shipping extends Mage_Checkout_Block_Cart_Shipping
{
    /**
     * Show City in ShippingModule Estimation
     *
     * @return bool
     */
    public function getCityActive()
    {
        return (bool) parent::getCityActive()
            || (bool) Mage::getStoreConfig('carriers/samedaycourier_shipping/active');
    }
}
