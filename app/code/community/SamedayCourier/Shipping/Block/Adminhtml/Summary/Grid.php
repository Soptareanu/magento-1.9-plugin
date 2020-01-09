<?php
class SamedayCourier_Shipping_Block_Adminhtml_Summary_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setDefaultSort('id');
        $this->setId('samedaycourier_shipping_summary_grid');
        $this->setDefaultDir('desc');
        $this->setFilterVisibility(false);
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Varien_Data_Collection
     * @throws Exception
     */
    protected function _getCollectionClass()
    {
        Mage::helper('samedaycourier_shipping/api')->sameday;

        $order_id = $this->getRequest()->order_id;
        $packages = Mage::getModel('samedaycourier_shipping/package')->getPackagesForOrderId($order_id);

        $gridData = array();
        foreach ($packages as $package) {
            $gridData[] = array(
                'parcel_number' => $package['summary']->getParcelAwbNumber(),
                'parcel_weight' => $package['summary']->getParcelWeight(),
                'delivered' => $package['summary']->isDelivered(),
                'delivery_attempts' => $package['summary']->getDeliveryAttempts(),
                'is_picked_up' => $package['summary']->isPickedUp() ? 'Yes' : 'No',
                'picked_up_at' => $package['summary']->getPickedUpAt() ? $package['summary']->getPickedUpAt()->getPickedUpAt()->format('Y-m-d H:i:s') : ''
            );
        }

        $collection = new Varien_Data_Collection();

        foreach ($gridData as $item) {
            $rowObject = new Varien_Object();
            $collection->addItem($rowObject->setData($item));
        }

        return $collection;
    }

    protected function _prepareCollection()
    {
        $collection = $this->_getCollectionClass();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('parcel_number',
            array(
                'header'=> $this->__('Parcel Number'),
                'align' =>'left',
                'width' => '50px',
                'index' => 'parcel_number',
                'filter' => false
            )
        );

        $this->addColumn('parcel_weight',
            array(
                'header'=> $this->__('Parcel Weight'),
                'index' => 'parcel_weight',
                'filter' => false
            )
        );

        $this->addColumn('delivered',
            array(
                'header'=> $this->__('Delivered'),
                'index' => 'delivered',
                'filter' => false
            )
        );

        $this->addColumn('delivery_attempts',
            array(
                'header'=> $this->__('Delivery Attempts'),
                'index' => 'delivery_attempts',
                'filter' => false
            )
        );

        $this->addColumn('is_picked_up',
            array(
                'header'=> $this->__('Is Picked Up'),
                'index' => 'is_picked_up',
                'filter' => false
            )
        );

        $this->addColumn('picked_up_at',
            array(
                'header'=> $this->__('Picked up at'),
                'index' => 'picked_up_at',
                'filter' => false
            )
        );

        return parent::_prepareColumns();
    }
}