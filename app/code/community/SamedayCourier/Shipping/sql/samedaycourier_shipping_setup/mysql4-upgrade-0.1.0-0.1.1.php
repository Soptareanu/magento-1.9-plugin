<?php

$installer = $this;
$installer->startSetup();

$lockerTable = $installer->getConnection()
    ->newTable($installer->getTable('samedaycourier_shipping/locker'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Id')
    ->addColumn('locker_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Locker Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
        'lenght' => 255,
    ), 'Name')
    ->addColumn('city', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
        'lenght' => 255,
    ), 'City')
    ->addColumn('county', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
        'lenght' => 255,
    ), 'County')
    ->addColumn('address', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
        'lenght' => 255,
    ), 'Address')
    ->addColumn('lat', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
        'lenght' => 255,
    ), 'Lat')
    ->addColumn('lng', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
    ), 'Lng')
    ->addColumn('postal_code', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
        'lenght' => 255,
    ), 'Postal code')
    ->addColumn('boxes', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
        'lenght' => 255,
    ), 'Boxes')
    ->addColumn('is_testing', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => true,
    ), 'Is testing');

$lockerOrderTable = $installer->getConnection()
    ->newTable($installer->getTable('samedaycourier_shipping/lockerOrder'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Id')
    ->addColumn('locker_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Order Id')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Locker Id');

$installer->getConnection()
    ->addColumn($installer->getTable('samedaycourier_shipping/service'), 'sameday_code', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
        'length' => 255
    ), 'Sameday Code');

$installer->getConnection()->createTable($lockerTable);
$installer->getConnection()->createTable($lockerOrderTable);


$installer->endSetup();