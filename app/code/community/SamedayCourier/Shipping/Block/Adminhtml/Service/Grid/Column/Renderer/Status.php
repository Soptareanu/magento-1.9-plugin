<?php

/**
 * Class SamedayCourier_Shipping_Block_Adminhtml_Service_Grid_Column_Renderer_Status
 */
class SamedayCourier_Shipping_Block_Adminhtml_Service_Grid_Column_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        switch ($row->getData()['status'])
        {
            case 1 :
                $status = 'Always';
                break;
            case 2 :
                $status = 'Interval';
                break;
            default :
                $status = 'Disabled';
                break;
        }

        return $status;
    }
}