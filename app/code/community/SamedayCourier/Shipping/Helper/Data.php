<?php

/**
 * Class SamedayCourier_Shipping_Helper_Data
 */
class SamedayCourier_Shipping_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->_getRequest()->order_id;
    }

    /**
     * @return array|null
     */
    public function getShippingMethodSameday()
    {
        $data = array();
        $order_id = $this->getOrderId();

        $awb = Mage::getModel('samedaycourier_shipping/awb')->getAwbForOrderId($order_id);

        if ($awb !== null) {
            $data['awb_number'] = $awb['awb_number'];
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getLockerList()
    {
        $lockers = Mage::getModel('samedaycourier_shipping/locker');

        $is_testing = Mage::getStoreConfig('carriers/samedaycourier_shipping/is_testing');

        return $lockers->getLockers($is_testing);
    }

    /**
     * @return int
     */
    public function isOpenPackageEnabled()
    {
        return (int) Mage::getStoreConfig('carriers/samedaycourier_shipping/open_package');
    }

    /**
     * @return string
     */
    public function openPackageLabel()
    {
        return (string) Mage::getStoreConfig('carriers/samedaycourier_shipping/open_package_label');
    }

    /**
     * @param $serviceSamedayId
     *
     * @return bool|mixed
     */
    public function serviceHasOpcg($serviceSamedayId)
    {
        $serviceHasOpcg = false;
        $service = Mage::getModel('samedaycourier_shipping/service')->getServiceSameday(
            $serviceSamedayId,
            Mage::getStoreConfig('carriers/samedaycourier_shipping/is_testing')
        )[0];

        $optionalTaxes = json_decode($service['service_optional_taxes']);
        if (! isset($optionalTaxes)) {
            return $serviceHasOpcg;
        }

        foreach ($optionalTaxes as $optionalTax) {
            if ($optionalTax->code === 'OPCG' && (int) $optionalTax->type === 0) {
                $serviceHasOpcg = $service['sameday_id']; break;
            }
        }

        return $serviceHasOpcg;
    }

    /**
     * @param $data     *
     * @return false|string
     */
    public function _serializeData($data)
    {
        $_serializedData = array();
        foreach ($data as $object) {
            $_serializedData[] = (array) $object;
        }

        return json_encode($_serializedData, true);
    }

    /**
     * @param \Sameday\Objects\PostAwb\ParcelObject $parcelObject
     *
     * @return string $parcelObjectSerialized
     */
    public function serializeAwbParcel(Sameday\Objects\PostAwb\ParcelObject $parcelObject)
    {
        return json_encode(
            array(
                'position' => $parcelObject->getPosition(),
                'awbNumber' => $parcelObject->getAwbNumber()
            )
        );
    }

    /**
     * @param \Sameday\Objects\ParcelStatusHistory\HistoryObject $historyObject
     *
     * @return string $historyObjectSerialized
     */
    public function serializeHistory(Sameday\Objects\ParcelStatusHistory\HistoryObject $historyObject)
    {
        return json_encode(
            array(
                'id' => $historyObject->getId(),
                'state' => $historyObject->getState(),
                'reason' => $historyObject->getReason(),
                'name' => $historyObject->getName(),
                'label' => $historyObject->getLabel(),
                'date' => $historyObject->getDate(),
                'county' => $historyObject->getCounty(),
                'transitLocation' => $historyObject->getTransitLocation()
            )
        );
    }

    /**
     * @param \Sameday\Objects\ParcelStatusHistory\SummaryObject $summary
     *
     * @return string $serializedSummary
     */
    public function serializeSummary(\Sameday\Objects\ParcelStatusHistory\SummaryObject $summary)
    {
        return json_encode([
            'delivered' => $summary->isDelivered(),
            'canceled' => $summary->isCanceled(),
            'deliveryAttempts' => $summary->getDeliveryAttempts(),
            'parcelAwbNumber' => $summary->getParcelAwbNumber(),
            'parcelWeight' => $summary->getParcelWeight(),
            'isPickedUp' => $summary->isPickedUp(),
            'deliveredAt' => $summary->getDeliveredAt(),
            'lastDeliveryAttempt' => $summary->getLastDeliveryAttempt(),
            'pickedUpAt' => $summary->getPickedUpAt()
        ]);
    }

    /**
     * @param \Sameday\Objects\ParcelStatusHistory\ExpeditionObject $expeditionObject
     *
     * @return string $serializedExpeditionStatus
     */
    public function serializeExpeditionStatus(\Sameday\Objects\ParcelStatusHistory\ExpeditionObject $expeditionObject)
    {
        return json_encode([
            'id' => $expeditionObject->getId(),
            'expeditionDetails' =>$expeditionObject->getExpeditionDetails(),
            'county' => $expeditionObject->getCounty(),
            'data' => $expeditionObject->getDate(),
            'label' => $expeditionObject->getLabel(),
            'name' => $expeditionObject->getName(),
            'reason' => $expeditionObject->getReason(),
            'state' => $expeditionObject->getState()
        ]);
    }

    /**
     * @param string $json
     *
     * @return \Sameday\Objects\ParcelStatusHistory\SummaryObject
     */
    public function unserializeSummary($json)
    {
        $array = json_decode($json, true);

        return new \Sameday\Objects\ParcelStatusHistory\SummaryObject(
            $array['delivered'],
            $array['canceled'],
            $array['deliveryAttempts'],
            $array['parcelAwbNumber'],
            $array['parcelWeight'],
            $array['isPickedUp'],
            $array['deliveredAt'],
            $array['lastDeliveryAttempt'],
            $array['pickedUpAt']
        );
    }
}
