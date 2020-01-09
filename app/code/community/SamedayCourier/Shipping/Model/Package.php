<?php

class SamedayCourier_Shipping_Model_Package extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('samedaycourier_shipping/package');
    }

    /**
     * @param $order_id
     * @return mixed
     */
    public function getPackagesForOrderId($order_id)
    {
        $parcels = Mage::getModel('samedaycourier_shipping/package')
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('order_id', $order_id)
            ->getData();

        foreach ($parcels as $key => $value) {
            $parcels[$key]['summary'] = Mage::helper('samedaycourier_shipping/data')->unserializeSummary($value['summary']);
            $parcels[$key]['history'] = json_encode($value['history']);
            $parcels[$key]['expedition_status'] = json_encode($value['expedition_status']);
            $parcels[$key]['sync'] = json_encode($value['sync']);
        }

        return $parcels;
    }

    public function refreshHistory($order_id, $awb_parcel, $parcelStatusSummary, $parcelStatusHistory, $parcelStatusExpedition)
    {
        $awbPackage = Mage::getModel('samedaycourier_shipping/package')
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('order_id', $order_id)
            ->addFieldToFilter('awb_parcel', $awb_parcel)
            ->getData();

        if (empty($awbPackage)) {
            $parcel = Mage::getModel('samedaycourier_shipping/package');
        } else {
            $parcel = Mage::getModel('samedaycourier_shipping/package')->load($awbPackage[0]['id']);
        }

        $parcel->setOrderId($order_id);
        $parcel->setAwbParcel($awb_parcel);
        $parcel->setSummary($parcelStatusSummary);
        $parcel->setHistory($parcelStatusHistory);
        $parcel->setExpeditionStatus($parcelStatusExpedition);

        $parcel->save();
    }
}
