<?php

require_once(Mage::getBaseDir('lib') . '/samedaycourier-php-sdk/src/Sameday/autoload.php');
require_once(Mage::getModuleDir('controllers','Mage_Adminhtml') . DS . 'System' . DS . 'ConfigController.php');

class SamedayCourier_Shipping_Adminhtml_System_ConfigController extends Mage_Adminhtml_System_ConfigController
{
    /**
     * @return mixed
     */
    protected function _saveCarriers()
    {
        $groups = $this->getRequest()->getPost('groups');
        if (isset($groups['samedaycourier_shipping'])) {
            $user = $groups['samedaycourier_shipping']['fields']['user']['value'];
            $password = Mage::helper('core')->decrypt(Mage::getStoreConfig('carriers/samedaycourier_shipping/password', Mage::app()->getStore()));
            if ($groups['samedaycourier_shipping']['fields']['password']['value'] !== '******') {
                $password = $groups['samedaycourier_shipping']['fields']['password']['value'];
            }

            $is_testing = (bool) $groups['samedaycourier_shipping']['fields']['is_testing']['value'];

            $sameday = $this->initClient($user, $password, $is_testing);

            if (!$sameday->login()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('samedaycourier_shipping/data')->__('Connection with sameday was unsuccessful')
                );
            } else {
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('samedaycourier_shipping/data')->__('Connection with sameday was successful !')
                );
            }
        }
    }

    /**
     * @param $user
     * @param $password
     * @param $is_testing
     * @return \Sameday\SamedayClient
     * @throws \Sameday\Exceptions\SamedaySDKException
     */
    protected function initClient($user, $password, $is_testing)
    {
        return new \Sameday\SamedayClient(
            $user,
            $password,
            $is_testing ? 'https://sameday-api.demo.zitec.com' : 'https://api.sameday.ro',
            'MAGENTO',
            '1.9.*'
        );
    }
}
