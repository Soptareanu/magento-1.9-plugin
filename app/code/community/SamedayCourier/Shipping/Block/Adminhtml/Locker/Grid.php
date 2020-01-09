<?php
class SamedayCourier_Shipping_Block_Adminhtml_Locker_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setDefaultSort('id');
        $this->setId('samedaycourier_shipping_locker_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);

    }

    protected function _getCollectionClass()
    {
        return 'samedaycourier_shipping/locker_collection';
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
        $this->addColumn('locker_id',
            array(
                'header'=> $this->__('Locker ID'),
                'align' =>'left',
                'width' => '50px',
                'index' => 'locker_id'
            )
        );

        $this->addColumn('name',
            array(
                'header'=> $this->__('Name'),
                'index' => 'name'
            )
        );

        $this->addColumn('city',
            array(
                'header'=> $this->__('City'),
                'index' => 'city'
            )
        );

        $this->addColumn('county',
            array(
                'header'=> $this->__('County'),
                'index' => 'county'
            )
        );

        $this->addColumn('address',
            array(
                'header'=> $this->__('Address'),
                'index' => 'address'
            )
        );

        $this->addColumn('lat',
            array(
                'header'=> $this->__('Latitude'),
                'index' => 'lat'
            )
        );

        $this->addColumn('lng',
            array(
                'header'=> $this->__('Longitude'),
                'index' => 'lng'
            )
        );

        $this->addColumn('postal_code',
            array(
                'header'=> $this->__('Postal code'),
                'index' => 'postal_code'
            )
        );

        return parent::_prepareColumns();
    }
}