<?php

require_once(Mage::getBaseDir('lib') . '/samedaycourier-php-sdk/src/Sameday/autoload.php');

class SamedayCourier_Shipping_Helper_Api extends Mage_Core_Helper_Abstract
{
    public $sameday;

    public function __construct()
    {
        $this->sameday = new \Sameday\Sameday($this->initClient());
    }

    /**
     * @param null $username
     * @param null $password
     * @param null $testing
     * @return \Sameday\SamedayClient
     * @throws Mage_Core_Model_Store_Exception
     * @throws \Sameday\Exceptions\SamedaySDKException
     */
    private function initClient($username = null, $password = null, $testing = null)
    {
        $store = Mage::app()->getStore();
        if ($username === null && $password === null && $testing === null) {
            $username = Mage::getStoreConfig('carriers/samedaycourier_shipping/user', $store);
            $password = Mage::helper('core')->decrypt(Mage::getStoreConfig('carriers/samedaycourier_shipping/password', $store));
            $testing = Mage::getStoreConfig('carriers/samedaycourier_shipping/is_testing', $store);
        }

        return new \Sameday\SamedayClient(
            $username,
            $password,
            $testing ? 'https://sameday-api.demo.zitec.com' : 'https://api.sameday.ro',
            'MAGENTO',
            '1.9.*'
        );
    }
}
