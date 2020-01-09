<?php

/**
 * Class SamedayCourier_Shipping_Block_Adminhtml_Service_Edit_Tab_Renderer_Disabled
 */
class SamedayCourier_Shipping_Block_Adminhtml_Service_Edit_Tab_Renderer_Disabled extends Mage_Adminhtml_Block_Widget implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->setDisabled(false);
        $disabled = $element->getEscapedValue() === '1' ? true : false;
        $title = Mage::helper('samedaycourier_shipping/data')->__('If you want the service to be inactive this day, select the disabled button !');
        $html = '<td class="label">' . $element->getLabelHtml().'</td>';
        $html .= '<td class = "value">' . $element->getElementHtml() . '&nbsp&nbsp';
        $html.= '<input id="status" name="serviceData['.$element->getId().']" 
                type="checkbox" 
                value="1" 
                class="checkbox config-inherit" '.($disabled ? 'checked="checked"' : '').'/>
                <label for="serviceData['.$element->getId().']" class="inherit" title="'.$title.'">'.Mage::helper('samedaycourier_shipping/data')->__('Disabled').'</label>
                </td></td>';

        return $html;
    }
}