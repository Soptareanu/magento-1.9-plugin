<?php

class SamedayCourier_Shipping_Block_Adminhtml_Pickuppoint extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'samedaycourier_shipping';
        $this->_controller = 'adminhtml_pickuppoint';
        $this->_headerText = $this->__('Pickup Points');

        $this->_addButton('refresh', array(
            'label'     => Mage::helper('samedaycourier_shipping/data')->__('Refresh Pickup Points'),
            'onclick'   => "location.href='".$this->getUrl('*/*/refresh')."'",
            'class'     => '',
        ));

        parent::__construct();

        $this->_removeButton('add');
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-pickuppoint';
    }
}