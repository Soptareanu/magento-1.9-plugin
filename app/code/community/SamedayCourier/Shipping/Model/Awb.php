<?php

/**
 * Class SamedayCourier_Shipping_Model_Awb
 */
class SamedayCourier_Shipping_Model_Awb extends Mage_Core_Model_Abstract
{
    /**
     * Construct.
     */
    protected function _construct()
    {
        $this->_init('samedaycourier_shipping/awb');
    }

    /**
     * @param $order_id
     *
     * @return int|null
     */
    public function getAwbForOrderId($order_id)
    {
        $awb = Mage::getModel('samedaycourier_shipping/awb')
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('order_id', $order_id)
            ->getData();

        if (!empty($awb)) {
            return $awb[0];
        }

        return null;
    }

    /**
     * @param $data
     *
     * @throws Exception
     */
    public function saveAwb($data)
    {
        $awb = Mage::getModel('samedaycourier_shipping/awb');
        $this->setParams($awb, $data);
        try {
            $awb->save();
        } catch (Exception $e) {
            // do something with this exception
        }
    }

    /**
     * @param $data
     *
     * @throws Exception
     */
    public function updateShippingMethod($data)
    {
        $order = Mage::getModel('sales/order')->load($data['order_id']);

        $order->setShippingDescription($data['shipping_description']);
        $order->setShippingMethod($data['shipping_method']);
        $order->setShippingAmount($data['shipping_amount']);
        $order->setBaseShippingAmount($data['shipping_amount']);

        $order->save();
    }

    /**
     * @param $id
     *
     * @throws Exception
     */
    public function deleteAwb($id)
    {
        $awb = Mage::getModel('samedaycourier_shipping/awb')->load($id);
        $awb->delete();
    }

    /**
     * @param $order_id
     *
     * @param $parcel
     *
     * @throws Exception
     */
    public function updateParcels($order_id, $parcel)
    {
        $awb = $this->getAwbForOrderId($order_id);

        if (!$awb) {
            return;
        }

        $awb_id = $awb['id'];

        $parcels = json_encode(array_merge(json_decode($awb['parcels'], true), json_decode(json_encode($parcel), true)));
        $awb = Mage::getModel('samedaycourier_shipping/awb')->load($awb_id);
        $awb->setParcels($parcels);

        $awb->save();
    }

    /**
     * @param $order_id
     *
     * @return int
     */
    public function getPosition($order_id)
    {
        $awb = $this->getAwbForOrderId($order_id);
        $parcels = json_decode($awb['parcels']);
        $nrOfParcels = count($parcels);

        return $nrOfParcels + 1;
    }

    /**
     * @param $awb
     *
     * @param $data
     */
    private function setParams($awb, $data)
    {
        $awb->setOrderId($data['order_id']);
        $awb->setAwbNumber($data['awb_number']);
        $awb->setParcels($data['parcels']);
        $awb->setAwbCost($data['awb_cost']);
    }
}
