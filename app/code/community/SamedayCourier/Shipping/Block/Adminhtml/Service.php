<?php

/**
 * Class SamedayCourier_Shipping_Block_Adminhtml_Service
 */
class SamedayCourier_Shipping_Block_Adminhtml_Service extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'samedaycourier_shipping';
        $this->_controller = 'adminhtml_service';
        $this->_headerText = $this->__('Services');

        $this->_addButton('refresh', array(
            'label'     => Mage::helper('sales')->__('Refresh Service'),
            'onclick'   => "location.href='".$this->getUrl('*/*/refresh')."'",
            'class'     => '',
        ));

        parent::__construct();

        $this->_removeButton('add');
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-service';
    }
}