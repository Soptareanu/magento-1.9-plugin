<?php

class SamedayCourier_Shipping_Block_Adminhtml_Summary extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'samedaycourier_shipping';
        $this->_controller = 'adminhtml_summary';
        $this->_headerText = $this->__('History Summary');

        $this->_addButton('back', array(
            'label'     => Mage::helper('samedaycourier_shipping/data')->__('Back'),
            'onclick'   => "location.href='".$this->getUrl('adminhtml/sales_order/view/order_id/' . $this->getRequest()->order_id)."'",
            'class'     => 'back',
        ));

        $this->_addButton('refresh', array(
            'label'     => Mage::helper('samedaycourier_shipping/data')->__('Refresh'),
            'onclick'   => "location.href='".$this->getUrl('*/*/refreshHistory/order_id/' . $this->getRequest()->order_id)."'",
            'class'     => '',
        ));

        parent::__construct();

        $this->_removeButton('add');
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-summary';
    }
}