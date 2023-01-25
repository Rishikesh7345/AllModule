<?php
/**
* @author 18th DigiTech Team
* @copyright Copyright (c) 2022 18th DigiTech (https://www.18thdigitech.com)
* @package Eighteentech_Buildbox
*/
namespace Eighteentech\Buildbox\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class BuildboxAttr implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
   private $_moduleDataSetup;

   /**
     * @var EavSetupFactory
     */
   private $_eavSetupFactory;

   /**
    * Construct
    *
    * @param ModuleDataSetupInterface $moduleDataSetup
    * @param EavSetupFactory $eavSetupFactory
    */
   public function __construct(
       ModuleDataSetupInterface $moduleDataSetup,
       EavSetupFactory $eavSetupFactory
   ) {
       $this->_moduleDataSetup = $moduleDataSetup;
       $this->_eavSetupFactory = $eavSetupFactory;
   }

   /**
    * Apply
    */
   public function apply()
   {
       /** @var EavSetup $eavSetup */
       $eavSetup = $this->_eavSetupFactory->create(['setup' => $this->_moduleDataSetup]);

       $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 
       'kit_height', [
           'type' => 'text',
           'backend' => '',
           'frontend' => '',
           'label' => __('Kit Height'),
           'input' => 'text',
           'class' => 'kit_class',
           'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
           'visible' => true,
           'required' => false,
           'user_defined' => false,
           'default' => '',
           'searchable' => false,
           'filterable' => false,
           'comparable' => false,
           'visible_on_front' => false,
           'used_in_product_listing' => true,
           'unique' => false,
       ]);
       
       $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY,'kit_width', [
        'type' => 'text',
        'backend' => '',
        'frontend' => '',
        'label' => __('Kit Width'),
        'input' => 'text',
        'class' => 'kit_class',
        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'used_in_product_listing' => true,
        'unique' => false,
       ]);

        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY,'kit_lenght', [
        'type' => 'text',
        'backend' => '',
        'frontend' => '',
        'label' => __('Kit Lenght'),
        'input' => 'text',
        'class' => 'kit_class',
        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => false,
        'used_in_product_listing' => true,
        'unique' => false,
    ]);
   }

   /**
    * GetDependencies
    */
   public static function getDependencies()
   {
       return [];
   }

   /**
    * GetAliases
    */
   public function getAliases()
   {
       return [];
   }

   /**
    * GetVersion
    */
   public static function getVersion()
   {
      return '1.0.0';
   }
}