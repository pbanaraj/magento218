<?php
/**
 * Diglin GmbH - Switzerland
 *
 * @author      Sylvain Rayé <support at diglin.com>
 * @category    Diglin
 * @package     Training\CustomerComment\Setup
 * @copyright   Copyright (c) 2011-2016 Diglin (http://www.diglin.com)
 */

namespace Diglin\Username\Setup;

use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Setup\EavSetup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var Config
     */
    private $eavConfig;
    /**
     * @var AttributeSetFactory
     */
    private $attributeSet;
    /**
     * @var EavSetup
     */
    private $eavSetup;

    /**
     * InstallSchema constructor.
     */
    public function __construct(
        AttributeSetFactory $attributeSet,
        Config $eavConfig,
        EavSetup $eavSetup
    )
    {
        $this->attributeSet = $attributeSet;
        $this->eavConfig = $eavConfig;
        $this->eavSetup = $eavSetup;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $entityType = $this->eavConfig->getEntityType(Customer::ENTITY);
        $attributeSet = $this->attributeSet->create();
        $groupId = $attributeSet->getDefaultGroupId($entityType->getDefaultAttributeSetId());

        /*$this->eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'zoom_image',
            [
                'type' => 'varchar',
                'backend' => 'Diglin\Username\Model\Attribute\Product\Image',
                'frontend' => '',
                'label' => 'Zoom Image(1000*1000)',
                'input' => 'image',
                'class' => '',
                'source' => '',
                'group' =>'My custom group',
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => ''
            ]
            );*/
       
       $this->eavSetup->addAttribute(Customer::ENTITY, 'association_name',
            [
                'label'                 => 'Association Name',
                'input'                 => 'text',
                'required'              => 0,
                'user_defined'          => 1,
                'unique'                => 0,
                'system'                => 0,
                'group'                 => $groupId,
                'is_used_in_grid'       => 0,
                'is_visible_in_grid'    => 1,
                'is_filterable_in_grid' => 1,
                'is_searchable_in_grid' => 1
                
            ]
            );
        
        
        
        
        $this->eavSetup->addAttribute(Customer::ENTITY, 'association_id',
            [
                'label'                 => 'Association Id',
                'input'                 => 'text',
                'required'              => 0,
                'user_defined'          => 1,
                'unique'                => 0,
                'system'                => 0,
                'group'                 => $groupId,
                'is_used_in_grid'       => 0,
                'is_visible_in_grid'    => 1,
                'is_filterable_in_grid' => 1,
                'is_searchable_in_grid' => 1
                
            ]
            );
        
        
        $this->eavSetup->addAttribute(Customer::ENTITY, 'config_url',
            [
                'label'                 => 'Config Url',
                'input'                 => 'text',
                'required'              => 0,
                'user_defined'          => 1,
                'unique'                => 0,
                'system'                => 0,
                'group'                 => $groupId,
                'is_used_in_grid'       => 0,
                'is_visible_in_grid'    => 1,
                'is_filterable_in_grid' => 1,
                'is_searchable_in_grid' => 1
               
            ]
            );
        
        
        $this->eavSetup->addAttribute(Customer::ENTITY, 'product_type',
            [
                'label'                 => 'Product Type',
                'input'                 => 'text',
                'required'              => 0,
                'user_defined'          => 1,
                'unique'                => 0,
                'system'                => 0,
                'group'                 => $groupId,
                'is_used_in_grid'       => 0,
                'is_visible_in_grid'    => 1,
                'is_filterable_in_grid' => 1,
                'is_searchable_in_grid' => 1
                
            ]
            );
        
        
        $this->eavSetup->addAttribute(Customer::ENTITY, 'source_sys_uid',
            [
                'label'                 => 'Source Sys UID',
                'input'                 => 'text',
                'required'              => 0,
                'user_defined'          => 1,
                'unique'                => 0,
                'system'                => 0,
                'group'                 => $groupId,
                'is_used_in_grid'       => 0,
                'is_visible_in_grid'    => 1,
                'is_filterable_in_grid' => 1,
                'is_searchable_in_grid' => 1
                
            ]
            );

        
            
          

        $setup->endSetup();
    }
}
