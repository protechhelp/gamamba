<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc. 
 */
class Aitoc_Aitsys_Model_News_Recent extends Aitoc_Aitsys_Abstract_Model
{
    const CACHE_LIVE_TIME = 86400;

    /**
     * @var array
     */
    protected $_news = array();
    
    /**
     * @var string
     */
    protected $_cacheKey = 'AITOC_AITSYS_NEWS';

    /**
     * @var string
     */
    protected $_type = 'news';
    
    /**
     * @return Aitoc_Aitsys_Model_Mysql4_News
     */
    protected function _getNewsResource()
    {
        return Mage::getResourceSingleton('aitsys/news');
    }
    
    /**
     * @return Aitoc_Aitsys_Model_Mysql4_News_Collection
     */
    protected function _getNewsCollection()
    {
        return Mage::getResourceModel('aitsys/news_collection')->addTypeFilter($this->_type);
    }
    
    /**
     * @return Aitoc_Aitsys_Model_News_Recent
     */
    public function loadData()
    {
        try {
            $latest = $this->_getNewsResource()->getLatest($this->_type);
        } catch (Exception $exc) {
            Mage::logException($exc);
            return $this;
        }
        
        if (!$latest->isOld()) {
            foreach ($this->_getNewsCollection() as $model) {
                $this->addNews(array(
                    'title' => $model->getTitle(),
                    'content' => $model->getDescription()
                ));
            }
            return $this;
        }
        
        try {
            $news = $this->_getPageData();
            if(!empty($news))
            {
                $this->addNews($news);
                $this->saveData();
            }
        } catch (Exception $exc) {
            Mage::logException($exc);
        }

        return $this;
    }

    protected function _getStaticPage()
    {

        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, Mage::getStoreConfig('aitsys/feed/store_url'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }


    protected function _getPageData()
    {
        $aPageData = array();
        try {
            $oPage = $this->_getStaticPage();
            if ($oPage != '')
            {
                $aPageData['title'] = '';
                $aPageData['content'] = $oPage;
                $aPageData['pubDate'] = time();
            }
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
        }
        return $aPageData;
    }
    /**
     * @return Aitoc_Aitsys_Model_News_Recent
     */
    public function saveData()
    {
        $this->_getNewsResource()->clear($this->_type);
        if (!$this->_news) {
            Mage::getModel('aitsys/news')->setData(array(
                'date_added'  => date('Y-m-d H:i:s'),
                'title'       => '',
                'description' => '',
                'type'        => $this->_type
            ))->save();
        } else {
            foreach ($this->_news as $item) {
                Mage::getModel('aitsys/news')->setData(array(
                    'date_added'  => date('Y-m-d H:i:s'),
                    'title'       => $item['title'],
                    'description' => $item['content'],
                    'type'        => $this->_type
                ))->save();
            }
        }
        return $this;
    }
    
    /**
     * @param array $item
     * @return Aitoc_Aitsys_Model_News_Recent
     */
    public function addNews( $item )
    {
        if ($item && !empty($item['content'])) {
            $this->_news[] = $item;
        }
        return $this;
    }
    
    public function getNews()
    {
        return $this->_news;
    }
}