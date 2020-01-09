<?php

class SamedayCourier_Shipping_Block_Adminhtml_History extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'samedaycourier_shipping';
        $this->_controller = 'adminhtml_history';
        $this->_headerText = $this->__('History Status');

        parent::__construct();

        $this->_removeButton('add');
    }

    public function getHeaderCssClass()
    {
        return 'icon-head head-history';
    }
}