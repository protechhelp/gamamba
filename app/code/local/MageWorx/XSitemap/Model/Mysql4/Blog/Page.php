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
class MageWorx_XSitemap_Model_Mysql4_Blog_Page extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct() {
        $this->_init('blog/blog', 'post_id');
    }

    public function getCollection($storeId) {
        $read  = $this->_getReadAdapter();
        $select = $read->select()
            ->from(array('main_table' => $this->getTable('blog')), array('post_id', 'identifier AS url','update_time AS date'))
            ->join(
                array('store_table' => $this->getTable('store')),
                'main_table.post_id=store_table.post_id',
                array()
            )
            ->where('store_table.store_id IN(?)', array(0, $storeId));

        $query = $read->query($select);
        while ($row = $query->fetch()) {
            $post = $this->_prepareObject($row);
            $posts[$post->getId()] = $post;
        }
        return $posts;
    }

    protected function _prepareObject(array $data) {
        $object = new Varien_Object();
        $object->setId($data['post_id']);
        $object->setUrl($data['url']);
        $object->setDate($data['date']);

        return $object;
    }

}