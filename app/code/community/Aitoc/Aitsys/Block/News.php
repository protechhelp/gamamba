<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_Block_News extends Aitoc_Aitsys_Abstract_Adminhtml_Block
{
    /**
     * @var Aitoc_Aitsys_Model_News_Recent
     */
    protected $_news;
    
    /**
     * @return Aitoc_Aitsys_Model_News_Recent
     */
    public function getNews()
    {
        if (!$this->_news) {
            $this->_news = Mage::getModel('aitsys/news_recent');
            $this->_news->loadData();
        }
        return $this->_news->getNews();
    }
}