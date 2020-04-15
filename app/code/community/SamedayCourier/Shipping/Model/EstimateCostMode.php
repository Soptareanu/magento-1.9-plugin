<?php

class SamedayCourier_Shipping_Model_EstimateCostMode
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 0,
                'label'=> Mage::helper('adminhtml')->__('Never')
            ],
            [
                'value' => 1,
                'label'=> Mage::helper('adminhtml')->__('Always')
            ],
            [
                'value' => 2,
                'label'=> Mage::helper('adminhtml')->__('If its value is bigger than fixed price ')
            ],
        ];
    }
}

