<?php
class SamedayCourier_Shipping_Block_Adminhtml_Service_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setDefaultSort('sameday_id');
        $this->setId('samedaycourier_shipping_service_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);

    }

    protected function _getCollectionClass()
    {
        return 'samedaycourier_shipping/service_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        // Add the columns that should appear in the grid
        $this->addColumn('sameday_id',
            array(
                'header'=> $this->__('Sameday ID'),
                'align' =>'left',
                'width' => '50px',
                'index' => 'sameday_id'
            )
        );

        $this->addColumn('name',
            array(
                'header'=> $this->__('Internal Name'),
                'index' => 'sameday_name'
            )
        );

        $this->addColumn('sameday_name',
            array(
                'header'=> $this->__('Name'),
                'index' => 'name'
            )
        );

        $this->addColumn('price',
            array(
                'header'=> $this->__('Price'),
                'index' => 'price'
            )
        );

        $this->addColumn('price_free',
            array(
                'header'=> $this->__('Free delivery price'),
                'index' => 'price_free'
            )
        );

        $this->addColumn('status',
            array(
                'header'=> $this->__('Status'),
                'index' => 'status',
                'type'      => 'options',
                'options' => Mage::getModel('samedaycourier_shipping/service')->getAvailableStatuses(),
                'renderer' => 'samedaycourier_shipping/adminhtml_service_grid_column_renderer_status'
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * Retrives row click URL
     *
     * @param  mixed $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}