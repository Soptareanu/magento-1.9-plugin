<?php

class SamedayCourier_Shipping_Block_Adminhtml_Service_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl(
                'adminhtml/service/edit',
                array(
                    '_current' => true,
                    'continue' => 0,
                )
            ),
            'method' => 'post',
        ));
        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset(
            'general',
            array(
                'legend' => $this->__('Service Details')
            )
        );

        $service = Mage::getModel(
            'samedaycourier_shipping/service'
        );

        $fields = array(
            'name' => array(
                'label' => $this->__('Display Name'),
                'input' => 'text',
                'required' => true,
            ),
            'price' => array(
                'label' => $this->__('Price'),
                'input' => 'text',
                'required' => true,
            ),
            'price_free' => array(
                'label' => $this->__('Free Delivery Price'),
                'input' => 'text',
                'required' => false,
            ),
            'status' => array(
                'label' => $this->__('Status'),
                'input' => 'select',
                'required' => true,
                'options' => $service->getAvailableStatuses()
            )
        );

        $intervals = array('monday_from',
            'monday_until',
            'tuesday_from',
            'tuesday_until',
            'wednesday_from',
            'wednesday_until',
            'thursday_from',
            'thursday_until',
            'friday_from',
            'friday_until',
            'saturday_from',
            'saturday_until',
            'sunday_from',
            'sunday_until'
        );

        $additionalElements = array();
        foreach ($intervals as $interval) {
            $label = explode("_", $interval, '2');
            $br = $label[1] === 'until' ? '</span><br><br><hr>' : '';
            $fields['working_days_' . $interval] = array(
                'input' => 'time',
                'label' => $label[1] === 'until' ? '' : ucwords(Mage::helper('samedaycourier_shipping/data')->__(ucwords($label[0]))),
                'class' => 'required-entry',
                'required' => false,
                'name' => 'working_days',
                'onclick' => "",
                'onchange' => "",
                'disabled' => false,
                'readonly' => false,
                'after_element_html' => '<span style="color: #ed6502">' . Mage::helper('samedaycourier_shipping/data')->__(ucwords($label[1])) . $br,
                'tabindex' => 1
            );

            if ($label[1] === 'from') {
                $additionalElements[] = array(
                    'name' => 'working_days_' . $interval
                );
            }
        }

        $this->addFieldsToFieldset($fieldset, $fields);

        $this->addAdditionalElementToField($form, $additionalElements);

        $this->toggleInterval($intervals);

        return $this;
    }

    private function addAdditionalElementToField(Varien_Data_Form $form, array $additionalElements)
    {
        foreach ($additionalElements as $element) {
            $form->getElement($element['name'])->setRenderer(
                Mage::app()->getLayout()
                    ->createBlock('samedaycourier_shipping/adminhtml_service_edit_tab_renderer_disabled')
            );
        }
    }

    private function toggleInterval($intervals)
    {
        $status = $this->getForm()->getElement('status');
        if ($status) {
            $status->setAfterElementHtml(
                '<script type="text/javascript">'
                . "
                //<![CDATA[
                function changeStatus() {
                    status = parseInt($('status').value);
                    intervals = JSON.parse('".json_encode($intervals)."');
                    nrOfIntervals = intervals.length;
                    for (i=0; i < nrOfIntervals; i++) {
                        _tr = $('working_days_' + intervals[i]).parentNode.parentNode;
                        if (status == 2) {
                            _tr.style.display = '';
                        } else {
                            _tr.style.display = 'none';
                        }
                    }
                }

                document.observe('dom:loaded', function() {
                        $('status').observe('change', changeStatus);
                        changeStatus();
                });
                //]]>
                "
                . '</script>'
            );
        }
    }

    private function addFieldsToFieldset(Varien_Data_Form_Element_Fieldset $fieldset, $fields)
    {
        $requestData = new Varien_Object($this->getRequest()
            ->getPost('serviceData'));

        foreach ($fields as $name => $_data) {
            if ($requestValue = $requestData->getData($name)) {
                $_data['value'] = $requestValue;
            }

            $_data['name'] = "serviceData[$name]";

            $_data['title'] = $_data['label'];

            if (!array_key_exists('value', $_data)) {
                $_data['value'] = $this->_getService()->getData($name);
            }

            $fieldset->addField($name, $_data['input'], $_data);
        }

        return $this;
    }

    protected function _getService()
    {
        if (!$this->hasData('service')) {
            $service = Mage::registry('current_service');

            if (!$service instanceof
                SamedayCourier_Shipping_Model_Service) {
                $service = Mage::getModel(
                    'samedaycourier_shipping/service'
                );
            }

            $working_days = (array) json_decode($service->getData()['working_days']);

            if (!empty($working_days)) {
                foreach ($working_days as $key => $val) {
                    $day = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
                    if (is_array($working_days[$key])) {
                        $service->$day = implode(',', $val);
                    } else {
                        $service->$day = $val;
                    }
                }
            }

            $this->setData('service', $service);
        }

        return $this->getData('service');
    }
}
