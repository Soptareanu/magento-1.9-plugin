<?php

$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('samedaycourier_shipping/service'), 'service_optional_taxes', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false
    ), 'Sameday Service optional taxes');

$installer->endSetup();
