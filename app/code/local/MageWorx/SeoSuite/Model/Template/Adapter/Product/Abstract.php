<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * SEO Suite extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @author     MageWorx Dev Team
 */

class MageWorx_SeoSuite_Model_Template_Adapter_Product_Abstract extends MageWorx_SeoSuite_Model_Template_Adapter_Abstract
{
    public function apply($from,$limit,$template,$attribute_name)
    {
        $attributes = array();
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string)Mage::getConfig()->getTablePrefix();
        
        $select = $connection->select()
                ->from($tablePrefix.'eav_entity_type')
                ->where("entity_type_code = 'catalog_product'");
        $productTypeId = $connection->fetchOne($select);
        
        foreach ($attribute_name as $_attrName) {
            $select = $connection->select()
                    ->from($tablePrefix.'eav_attribute')
                    ->where("entity_type_id = $productTypeId AND (attribute_code = '".$_attrName."')");
            $attributes[$_attrName] = $connection->fetchRow($select);
        }
        //echo "<pre>"; print_r($attributes); exit;
        $select = $connection->select()
                ->from($tablePrefix.'catalog_product_entity')
                ->limit($limit, $from);
        $products = $connection->fetchAll($select);
        
        $select = $connection->select()
                ->from(array('main_table'=>$tablePrefix.'core_store'))
                ->joinLeft(array('seo_store_template'=>$tablePrefix.'seosuite_template_store'),'main_table.store_id=seo_store_template.store_id','seo_store_template.template_key')
                ->where('seo_store_template.template_id ='.Mage::registry('seosuite_template_current_model')->getId().' OR seo_store_template.template_id IS NULL');
        $stores = $connection->fetchAll($select);
        foreach ($stores as $key=>$storeArray) {
            $store = Mage::app()->getStore($storeArray['store_id']);
            if ($store->isAdmin()) {
                $store->setData('template_key',$storeArray['template_key']);
                $this->_defaultStore = $store;
         //       unset($stores[$key]);
            }
        }
    
        foreach ($products as $_product) {
           
            foreach ($stores as $store) {
                 $storeId = $store['store_id'];
                $templateString = ($store['template_key']) ? $store['template_key'] : $this->_defaultStore->getTemplateKey();
                $product = Mage::getModel('catalog/product')->setStoreId($storeId)->load($_product['entity_id']);
                $template->setTemplate($templateString)
                            ->setProduct($product);
                $attributeName = $template->process();
				
                foreach ($attributes as $attribute) {
                    $select = $connection->select()->from($tablePrefix.'catalog_product_entity_'.$attribute['backend_type'])->
                                where("entity_type_id = $productTypeId AND attribute_id = '$attribute[attribute_id]' AND entity_id = {$product->getId()} AND store_id = {$storeId}");
                 //   echo $tablePrefix.'catalog_product_entity_varchar'."entity_type_id = $productTypeId AND attribute_id = '$attributeId' AND entity_id = {$product->getId()} AND store_id = {$storeId}"; exit;
                    $row = $connection->fetchRow($select);
                    if ($row) {
                    $connection->update($tablePrefix.'catalog_product_entity_'.$attribute['backend_type'], array('value' => $attributeName), "entity_type_id = $productTypeId AND attribute_id = '$attribute[attribute_id]' AND entity_id = {$product->getId()} AND store_id = {$storeId}");
                    } else {
                        $data = array(
                                'entity_type_id' => $productTypeId,
                                'attribute_id' => $attribute['attribute_id'],
                                'entity_id' => $product->getId(),
                                'store_id' => $storeId,
                                'value' => $attributeName
                            );
                        $connection->insert($tablePrefix.'catalog_product_entity_'.$attribute['backend_type'], $data);
                    }
                }
            }
        }
    }
}