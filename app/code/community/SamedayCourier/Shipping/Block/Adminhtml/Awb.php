<?php

class SamedayCourier_Shipping_Block_Adminhtml_Awb extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        // add my own button
        $buttonBack = array(
            'label' =>  'Back to orders',
            'onclick'   => "setLocation('".$this->getUrl('adminhtml/sales_order/view/order_id/' . $this->getRequest()->order_id)."')",
            'class'     =>  'back'
        );
        $this->addButton ('button_back', $buttonBack, 2, 10,  'header');

        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'samedaycourier_shipping';
        $this->_controller = 'adminhtml_awb';

        // remove the default button
        $this->_removeButton('back');
    }

    public function getHeaderText()
    {
        return Mage::helper('sales')->__('Generate Awb');
    }
}
