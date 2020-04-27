<?php

$installer = $this;
$installer->startSetup();

$openPackageTable = $installer->getConnection()
    ->newTable($installer->getTable('samedaycourier_shipping/openPackageOrder'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Id')
    ->addColumn('is_open_package', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Check if client choose Open Package for this order')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Order id');

$installer->getConnection()->createTable($openPackageTable);
$installer->endSetup();
