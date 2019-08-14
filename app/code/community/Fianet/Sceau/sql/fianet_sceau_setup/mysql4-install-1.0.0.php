<?php
$installer = $this;


$installer->startSetup();

$installer->addAttribute('order', 'fianet_sceau_order_sent_prod', array('type' => 'int', 'visible' => false, 'required' => true, 'default_value' => 0));
$installer->getConnection()->addColumn($installer->getTable('sales_flat_order_grid'), 'fianet_sceau_order_sent_prod', 'varchar(255) default 0');


$installer->addAttribute('order', 'fianet_sceau_order_sent_preprod', array('type' => 'int', 'visible' => false, 'required' => true, 'default_value' => 0));
$installer->getConnection()->addColumn($installer->getTable('sales_flat_order_grid'), 'fianet_sceau_order_sent_preprod', 'varchar(255) default 0');

$installer->addAttribute('order', 'fianet_sceau_order_sent_error', array('type' => 'int', 'visible' => false, 'required' => true, 'default_value' => 0));
$installer->getConnection()->addColumn($installer->getTable('sales_flat_order_grid'), 'fianet_sceau_order_sent_error', 'varchar(255) default 0');


$installer->endSetup();
