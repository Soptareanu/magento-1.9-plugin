<?php

class SamedayCourier_Shipping_Block_Adminhtml_Service_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'samedaycourier_shipping';
        $this->_controller = 'adminhtml_service';

        $this->_mode = 'edit';

        $newOrEdit = $this->getRequest()->getParam('id')
            ? $this->__('Edit')
            : $this->__('New');
        $this->_headerText =  $newOrEdit . ' ' . $this->__('Service');
    }
}