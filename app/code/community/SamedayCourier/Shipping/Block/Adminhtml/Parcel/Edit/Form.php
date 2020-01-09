<?php

/**
 * Class SamedayCourier_Shipping_Block_Adminhtml_Parcel_Edit_Form
 */
class SamedayCourier_Shipping_Block_Adminhtml_Parcel_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        Mage::helper('samedaycourier_shipping/api')->sameday;
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/addParcel', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset(
            'samedaycourier_shipping_parcel',
            array('legend' => Mage::helper('samedaycourier_shipping/data')->__('Add parcel'))
        );

        $fieldset->addField('order_id', 'hidden', array(
            'name'               => 'order_id',
            'value'              => $this->getRequest()->order_id
        ));

        $fieldset->addField('package_weight', 'text', array(
                'name' => 'package_weight',
                'label' => Mage::helper('samedaycourier_shipping/data')->__('Package weight'),
                'title' => Mage::helper('samedaycourier_shipping/data')->__('Package weight'),
                'style' => 'required-entry',
                'value' => '1',
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

        $fieldset->addField('is_last', 'select', array(
                'name' => 'is_last',
                'label' => Mage::helper('samedaycourier_shipping/data')->__('Is Last'),
                'title' => Mage::helper('samedaycourier_shipping/data')->__('Is Last'),
                'style' => 'required-entry',
                'value' => '',
                'options' => array(
                    '1' => 'Yes',
                    '0' => 'No'
                ),
                'required' => true
            )
        );

        $fieldset->addField('observation', 'text', array(
                'name' => 'observation',
                'label' => Mage::helper('samedaycourier_shipping/data')->__('Observation'),
                'title' => Mage::helper('samedaycourier_shipping/data')->__('Observation'),
                'style' => 'required-entry',
                'value' => '',
                'required' => false
            )
        );

        return parent::_prepareForm();
    }
}