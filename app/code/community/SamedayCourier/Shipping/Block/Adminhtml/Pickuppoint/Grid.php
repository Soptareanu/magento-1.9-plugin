<?php
class SamedayCourier_Shipping_Block_Adminhtml_Pickuppoint_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setDefaultSort('id');
        $this->setId('samedaycourier_shipping_pickuppoint_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);

    }

    protected function _getCollectionClass()
    {
        return 'samedaycourier_shipping/pickuppoint_collection';
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

        $this->addColumn('sameday_alias',
            array(
                'header'=> $this->__('Sameday Alias'),
                'index' => 'sameday_alias'
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

        $this->addColumn('is_default',
            array(
                'header'=> $this->__('Is Default'),
                'index' => 'is_default',
                'type' => 'options',
                'options' => array(
                    "1" => 'Default',
                    "0" => 'No'
                )
            )
        );

        return parent::_prepareColumns();
    }
}