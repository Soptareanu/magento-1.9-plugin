<?php

/**
 * Class SamedayCourier_Shipping_Block_Adminhtml_Awb_Edit_Form
 */
class SamedayCourier_Shipping_Block_Adminhtml_Awb_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function _prepareForm()
    {
        Mage::helper('samedaycourier_shipping/api')->sameday;
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/generateAwb', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset(
            'samedaycourier_shipping_awb',
            array('legend' => Mage::helper('samedaycourier_shipping/data')->__('Generate AWB'))
        );

        $store = Mage::app()->getStore();
        $testing = Mage::getStoreConfig('carriers/samedaycourier_shipping/is_testing', $store);

        $pickupPointSingleton = Mage::getSingleton('samedaycourier_shipping/pickuppoint');
        $pickupPoints = $pickupPointSingleton->getPickupPoints($testing);

        $serviceSingleton = Mage::getSingleton('samedaycourier_shipping/service');
        $services = $serviceSingleton->getServices($testing);

        $order = Mage::getSingleton('sales/order')->load($this->getRequest()->order_id)->getData();

        $pickupPointList = array();
        foreach ($pickupPoints as $pickupPoint) {
            $pickupPointList[$pickupPoint['sameday_id']] = $pickupPoint['sameday_alias'];
        }

        $serviceList = array();
        foreach ($services as $service) {
            if ($service['status'] > 0) {
                $serviceList[$service['sameday_id']] = $service['sameday_name'];
            }
        }

        $currentService = explode("_", $order['shipping_method'], 4);
        $currentService = isset($currentService[2]) ? $currentService[2] : null;

        $fieldset->addField('order_id', 'hidden', array(
            'name' => 'order_id',
            'value' => $this->getRequest()->order_id
        ));

        $fieldset->addField('insured_value', 'text', array(
                'name' => 'insured_value',
                'label' => Mage::helper('samedaycourier_shipping/data')->__('Insured value'),
                'title' => Mage::helper('samedaycourier_shipping/data')->__('Insured value'),
                'style' => 'required-entry',
                'value' => 0,
                'required' => true
            )
        );

        $fieldset->addField('package_weight', 'text', array(
                'name' => 'package_weight',
                'label' => Mage::helper('samedaycourier_shipping/data')->__('Package weight'),
                'title' => Mage::helper('samedaycourier_shipping/data')->__('Package weight'),
                'style' => 'required-entry',
                'value' => round($order['weight'], 2),
                'required' => true
            )
        );

        $fieldset->addField('package_length', 'text', array(
                'name' => 'package_length',
                'label' => Mage::helper('samedaycourier_shipping/data')->__('Package length'),
                'title' => Mage::helper('samedaycourier_shipping/data')->__('Package length'),
                'style' => 'required-entry',
                'value' => '',
                'required' => false
            )
        );

        $fieldset->addField('package_height', 'text', array(
                'name' => 'package_height',
                'label' => Mage::helper('samedaycourier_shipping/data')->__('Package height'),
                'title' => Mage::helper('samedaycourier_shipping/data')->__('Package height'),
                'style' => 'required-entry',
                'value' => '',
                'required' => false
            )
        );

        $fieldset->addField('package_width', 'text', array(
                'name' => 'package_width',
                'label' => Mage::helper('samedaycourier_shipping/data')->__('Package width'),
                'title' => Mage::helper('samedaycourier_shipping/data')->__('Package width'),
                'style' => 'required-entry',
                'value' => '',
                'required' => false
            )
        );

        $fieldset->addField('ramburs', 'text', array(
                'name' => 'ramburs',
                'label' => Mage::helper('samedaycourier_shipping/data')->__('Ramburs'),
                'title' => Mage::helper('samedaycourier_shipping/data')->__('Ramburs'),
                'style' => 'required-entry',
                'value' => round($order['grand_total'], 2),
                'required' => true
            )
        );

        $fieldset->addField('observation', 'text', array(
                'name' => 'observation',
                'label' => Mage::helper('samedaycourier_shipping/data')->__('Observation'),
                'title' => Mage::helper('samedaycourier_shipping/data')->__('Observation'),
                'style' => '',
                'required' => false
            )
        );

        $fieldset->addField('pickup_point', 'select', array(
                'name' => 'pickup_point',
                'label' => Mage::helper('samedaycourier_shipping/data')->__('Pickup Point'),
                'title' => Mage::helper('samedaycourier_shipping/data')->__('Pickup Point'),
                'style' => '',
                'class' => 'required-entry',
                'value' => $pickupPointSingleton->getDefaultPickupPoint(),
                'options' => $pickupPointList,
                'required' => true
            )
        );

        $fieldset->addField('package_type', 'select', array(
                'name' => 'package_type',
                'label' => Mage::helper('samedaycourier_shipping/data')->__('Package Type'),
                'title' => Mage::helper('samedaycourier_shipping/data')->__('Package Type'),
                'class' => 'required-entry',
                'style' => '',
                'options' => array(
                    \Sameday\Objects\Types\PackageType::PARCEL => 'Parcel',
                    \Sameday\Objects\Types\PackageType::ENVELOPE => 'Envelope',
                    \Sameday\Objects\Types\PackageType::LARGE => 'Large'
                ),
                'required' => true
            )
        );

        $fieldset->addField('awb_payment', 'select', array(
                'name' => 'awb_payment',
                'label' => Mage::helper('samedaycourier_shipping/data')->__('Awb Payment'),
                'title' => Mage::helper('samedaycourier_shipping/data')->__('Awb Payment'),
                'class' => 'required-entry',
                'style' => '',
                'options' => array(
                    \Sameday\Objects\Types\AwbPaymentType::CLIENT => 'Client'
                ),
                'required' => true
            )
        );

        $fieldset->addField('service', 'select', array(
                'name' => 'awb_service',
                'label' => Mage::helper('samedaycourier_shipping/data')->__('Service'),
                'title' => Mage::helper('samedaycourier_shipping/data')->__('Service'),
                'class' => 'required-entry',
                'style' => '',
                'value' => $currentService,
                'options' => $serviceList,
                'required' => true
            )
        );

        return parent::_prepareForm();
    }
}
