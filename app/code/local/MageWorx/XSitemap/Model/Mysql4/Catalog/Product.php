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
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Extended Sitemap extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @author     MageWorx Dev Team
 */
class MageWorx_XSitemap_Model_Mysql4_Catalog_Product extends Mage_Core_Model_Mysql4_Abstract {

    /**
     * Collection Zend Db select
     *
     * @var Zend_Db_Select
     */
    protected $_select;
    /**
     * Attribute cache
     *
     * @var array
     */
    protected $_attributesCache = array();

    /**
     * Init resource model (catalog/category)
     */
    protected function _construct() {
        $this->_init('catalog/product', 'entity_id');
    }

    /**
     * Add attribute to filter
     *
     * @param int $storeId
     * @param string $attributeCode
     * @param mixed $value
     * @param string $type
     *
     * @return Zend_Db_Select
     */
    protected function _addFilter($storeId, $attributeCode, $value, $type = '=') {
        if (!isset($this->_attributesCache[$attributeCode])) {
            $attribute = Mage::getSingleton('catalog/product')->getResource()->getAttribute($attributeCode);

            $this->_attributesCache[$attributeCode] = array(
                'entity_type_id' => $attribute->getEntityTypeId(),
                'attribute_id' => $attribute->getId(),
                'table' => $attribute->getBackend()->getTable(),
                'is_global' => $attribute->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
                'backend_type' => $attribute->getBackendType()
            );
        }

        $attribute = $this->_attributesCache[$attributeCode];

        if (!$this->_select instanceof Zend_Db_Select) {
            return false;
        }

        switch ($type) {
            case '=':
                $conditionRule = '=?';
                break;
            case 'in':
                $conditionRule = ' IN(?)';
                break;
            default:
                return false;
                break;
        }

        if ($attribute['backend_type'] == 'static') {
            $this->_select->where('e.' . $attributeCode . $conditionRule, $value);
        } else {
            $this->_select->join(
                            array('t1_' . $attributeCode => $attribute['table']),
                            'e.entity_id=t1_' . $attributeCode . '.entity_id AND t1_' . $attributeCode . '.store_id=0',
                            array()
                    )
                    ->where('t1_' . $attributeCode . '.attribute_id=?', $attribute['attribute_id']);

            if ($attribute['is_global']) {
                $this->_select->where('t1_' . $attributeCode . '.value' . $conditionRule, $value);
            } else {
                $this->_select->joinLeft(
                                array('t2_' . $attributeCode => $attribute['table']),
                                $this->_getWriteAdapter()->quoteInto('t1_' . $attributeCode . '.entity_id = t2_' . $attributeCode . '.entity_id AND t1_' . $attributeCode . '.attribute_id = t2_' . $attributeCode . '.attribute_id AND t2_' . $attributeCode . '.store_id=?', $storeId),
                                array()
                        )
                        ->where('IFNULL(t2_' . $attributeCode . '.value, t1_' . $attributeCode . '.value)' . $conditionRule, $value);
            }
        }

        return $this->_select;
    }

    /**
     * Get category collection array
     *
     * @return array
     */
    public function getCollection($storeId, $onlyCount=false, $limit=4000000000, $from=0) {
        $products = array();

        $store = Mage::app()->getStore($storeId);
        /* @var $store Mage_Core_Model_Store */

        if (!$store) {
            return false;
        }
        
        $read  = $this->_getReadAdapter();

        $this->_select = $read->select()
                ->distinct()
                ->from(array('e' => $this->getMainTable()), array(($onlyCount?'COUNT(*)':$this->getIdFieldName())))
                ->join(
                        array('w' => $this->getTable('catalog/product_website')),
                        'e.entity_id=w.product_id',
                        array()
                )
                ->where('w.website_id=?', $store->getWebsiteId())
                ->limit($limit, $from);

        $excludeAttr = Mage::getModel('catalog/product')->getResource()->getAttribute('exclude_from_sitemap');
        if ($excludeAttr) {
            $this->_select->joinLeft(
                        array('exclude_tbl' => $excludeAttr->getBackend()->getTable()),
                        'exclude_tbl.entity_id = e.entity_id AND exclude_tbl.attribute_id = ' . $excludeAttr->getAttributeId() . ' AND exclude_tbl.store_id = 0',
                        array()
                )
                ->where('exclude_tbl.value=0 OR exclude_tbl.value IS NULL');
        }
                
        $this->_addFilter($storeId, 'visibility', Mage::getSingleton('catalog/product_visibility')->getVisibleInSiteIds(), 'in');
        $this->_addFilter($storeId, 'status', Mage::getSingleton('catalog/product_status')->getVisibleStatusIds(), 'in');

        
        
        if ($onlyCount) {
            return $read->fetchOne($this->_select);
        }
                       
        
        $productCanonicalUrl = Mage::getStoreConfig('mageworx_seo/seosuite/product_canonical_url');

        $suffix = '';
        if($productCanonicalUrl) {
            $suffix = "AND canonical_url_rewrite.category_id IS NOT NULL";
            $suffix2 = "AND category_id IS NOT NULL";
        }
        
        if ($productCanonicalUrl==1) {
            $sort = 'DESC';
        } else if ($productCanonicalUrl == 2) {
            $sort = 'ASC';
        } else {
            $sort = '';
        }
        
        $canonicalAttr = Mage::getModel('catalog/product')->getResource()->getAttribute('canonical_url');
        $urlPathAttr = Mage::getModel('catalog/product')->getResource()->getAttribute('url_path');
        if ($canonicalAttr) {            
            $this->_select->columns(array('url' => new Zend_Db_Expr("
            IFNULL(
                (IFNULL((SELECT canonical_url_rewrite.`request_path`
                    FROM `".$canonicalAttr->getBackend()->getTable()."` AS canonical_path
                    LEFT JOIN `".$this->getTable('core/url_rewrite')."` AS canonical_url_rewrite ON canonical_url_rewrite.`id_path` = canonical_path.`value`
                    WHERE canonical_path.`entity_id` = e.`entity_id` AND canonical_path.`attribute_id` = ".$canonicalAttr->getAttributeId()." AND canonical_url_rewrite.`store_id` IN (0,".$storeId.") $suffix".
                    ($sort?" ORDER BY LENGTH(canonical_url_rewrite.`request_path`) ".$sort:"").
                    " LIMIT 1),
                    (SELECT `request_path` 
                    FROM `".$this->getTable('core/url_rewrite')."`
                    WHERE `product_id`=e.`entity_id` AND `store_id` IN (0,".$storeId.") AND `is_system`=1 AND `request_path` IS NOT NULL $suffix2".
                    ($sort?" ORDER BY LENGTH(`request_path`) ".$sort:"").
                    " LIMIT 1) 
                )),
                (SELECT p.`value` FROM `".$urlPathAttr->getBackend()->getTable()."` AS p
                 WHERE p.`entity_id` = e.`entity_id` AND p.`attribute_id` = ".$urlPathAttr->getAttributeId()." AND p.`store_id` IN (0,".$storeId.")  LIMIT 1
                )
            )")));
        } else {
            $this->_select->columns(array('url' => new Zend_Db_Expr("(SELECT `request_path` 
                FROM `".$this->getTable('core/url_rewrite')."`
                WHERE `product_id`=e.`entity_id` AND `store_id`=".intval($storeId)." AND `is_system`=1 AND `request_path` IS NOT NULL".
                ($sort?" ORDER BY LENGTH(`request_path`) ".$sort:"").
                " LIMIT 1)")));
        }
        
        $crossDomainAttr = Mage::getModel('catalog/product')->getResource()->getAttribute('canonical_cross_domain');
        if ($crossDomainAttr) {
            $this->_select->joinLeft(
                        array('cross_domain_tbl' => $crossDomainAttr->getBackend()->getTable()),
                        'cross_domain_tbl.entity_id = e.entity_id AND cross_domain_tbl.attribute_id = ' . $crossDomainAttr->getAttributeId() ,
                        array('canonical_cross_domain' => 'cross_domain_tbl.value')
                );
        }
        $updatedAt = Mage::getModel('catalog/product')->getResource()->getAttribute('updated_at');
        if ($updatedAt) {
            $this->_select->joinLeft(
                        array('updatedat_tbl' => $updatedAt->getBackend()->getTable()),
                        'updatedat_tbl.entity_id = e.entity_id' ,
                        array('updated_at' => 'updatedat_tbl.updated_at')
                );
        }
     //   echo $this->_select->assemble(); exit;
        $query = $read->query($this->_select);
        
        
        
        while ($row = $query->fetch()) {
            $product = $this->_prepareProduct($row);
           
            if ($productCanonicalUrl==3) { // use root
                $urlArr = explode('/', $product->getUrl());
                $product->setUrl(end($urlArr));
            }
            $products[$product->getId()] = $product;
        }
	
        return $products;
    }

    /**
     * Prepare product
     *
     * @param array $productRow
     * @return Varien_Object
     */
    protected function _prepareProduct(array $productRow) {
        $attribute = Mage::getSingleton('catalog/product')->getResource()->getAttribute('media_gallery');
        $media = Mage::getResourceSingleton('catalog/product_attribute_backend_media');
        $product = new Varien_Object();
        $product->setId($productRow[$this->getIdFieldName()]);
        $productUrl = !empty($productRow['url']) ? $productRow['url'] : 'catalog/product/view/id/' . $product->getId();
        $product->setUrl($productUrl);    
        $product->setUpdatedAt($productRow['updated_at']);    
        if (isset($productRow['canonical_cross_domain'])) $product->setCanonicalCrossDomain($productRow['canonical_cross_domain']);
        
        $gallery = $media->loadGallery($product, new Varien_Object(array('attribute' => $attribute)));
        if (count($gallery)) {
            $product->setGallery($gallery);
        }
        return $product;
    }

}