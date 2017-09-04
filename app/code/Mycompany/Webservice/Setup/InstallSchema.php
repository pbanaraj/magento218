<?php
/**
 * Collection InstallSchema Setup class to implement login
 * @category Mycompany
 * @package Mycompany_Webservice
 * @module Webservicevar
 * @author Tavant Developer
 */

namespace Mycompany\Webservice\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    
   /**
    * @var Mycompany\Webservice\Setup\InstallSchema
    * 
    * {@inheritDoc}
    * @see \Magento\Framework\Setup\InstallSchemaInterface::install()
    */

    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
	$installer = $setup;
	$installer->startSetup();


	$installer->getConnection()->dropTable($installer->getTable('naylorlogins'));
	
	$table = $installer->getConnection()
	    ->newTable($installer->getTable('naylorlogins'))
	    ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT, null, array(
		    'identity'  => true,
		    'nullable'  => false,
		    'primary'   => true,
		), 'ID')
		->addColumn('customer_id', \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT, null, array(
		    'nullable'  => false,
		    'unsigned' => true,
		), 'Customer Id')
	  
		->addColumn('naylor_email', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
		    'nullable'  => false,
		), 'Naylor Username')
		->addColumn('access_token', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
		        'nullable'  => false,
		), 'Naylor  Access Token')
		
		->addColumn('association_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
		    'nullable'  => false,
		), 'Association Id')
		
		->addColumn('created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
		), 'Creation Time')
	    
	    ->setComment('Naylors API Call Log Table');

	$installer->getConnection()->createTable($table);
	
	$installer->getConnection()->dropTable($installer->getTable('naylorinstalls'));
	
	$table = $installer->getConnection()
	->newTable($installer->getTable('naylorinstalls'))
	->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT, null, array(
	    'identity'  => true,
	    'nullable'  => false,
	    'primary'   => true,
	), 'ID')
	->addColumn('customer_id', \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT, null, array(
	    'nullable'  => false,
	    'unsigned' => true,
	), 'Customer Id')
	
	->addColumn('naylor_email', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
	    'nullable'  => false,
	), 'Naylor Username')
	->addColumn('sku', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 19, array(
	    'nullable'  => false,
	), 'SKU')
	
	->addColumn('association_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
	    'nullable'  => false,
	), 'Association Id')
	
	->addColumn('store', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
	    'nullable'  => false,
	), 'Store')
	
	->addColumn('website_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
	    'nullable'  => false,
	), 'Website Id')
	
	->addColumn('order_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
	    'nullable'  => false,
	), 'Order Id')
	
	->addColumn('processed', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 8, array(
	    'nullable'  => false,
	), 'Processed')
	
	->addColumn('created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
	), 'Creation Time')
	
	->addColumn('run_result', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(
	    'nullable'  => false,
	), 'Run Result')
	
	->setComment('Naylors Install DetailsTable');
	
	$installer->getConnection()->createTable($table);

	$installer->endSetup();
}

}
