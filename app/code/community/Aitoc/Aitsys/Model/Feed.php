<?php

class Aitoc_Aitsys_Model_Feed extends Mage_AdminNotification_Model_Feed
{
    const XML_FREQUENCY_PATH    = 'aitsys/feed/frequency';
    const XML_LAST_UPDATE_PATH  = 'aitsys/feed/last_update';
    const XML_ITERESTS          = 'aitsys/feed/interests';
    const XML_FEED_URL_PATH     = 'aitsys/feed/feed_url';

    /**
     * Check feed for modification
     *
     * @return Mage_AdminNotification_Model_Feed
     */
    public function checkUpdate()
    {
        if (($this->getFrequency() + $this->getLastUpdate()) > time()) {
            return $this;
        }

        $this->setLastUpdate();
        $feedData = array();

        $feedXml = $this->getFeedData();

        $installDate = date('Y-m-d H:i:s', Mage::getStoreConfig('aitsys/feed/install_date'));

        if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
            foreach ($feedXml->channel->item as $item) {
                $date = $this->getDate((string)$item->pubDate);

                if($date < $installDate || !$this->_isInteresting($item))
                {
                    continue;
                }

                $feedData[] = array(
                    'severity'      => (int)$item->severity,
                    'date_added'    => $this->getDate((string)$item->pubDate),
                    'title'         => (string)$item->title,
                    'description'   => (string)$item->description,
                    'url'           => (string)$item->link,
                );
            }

            if ($feedData) {
                Mage::getModel('adminnotification/inbox')->parse(array_reverse($feedData));
            }

        }

        return $this;
    }

    protected function _isInteresting($item)
    {
        $interests = explode(',', $this->_getInterests());
        if(in_array((string)$item->type, $interests))
        {
            return true;
        }

        if($item->type == Aitoc_Aitsys_Model_System_Config_Source_Interest::EXTENSION_UPDATE
            && in_array(Aitoc_Aitsys_Model_System_Config_Source_Interest::EXTENSION_UPDATE_CUSTOMER, $interests))
        {
            list($extension, $platform) = explode('-', (string)$item->extension);
            $isMagentoEE = Aitoc_Aitsys_Model_Platform::getInstance()->isMagentoEnterprise();
            if($isMagentoEE && $platform == 'EE'
                || !$isMagentoEE && empty($platform))
            {
                return Mage::helper('core')->isModuleEnabled($extension);
            }

        }
        return false;
    }

    /**
     * Retrieve feed url
     *
     * @return string
     */
    public function getFeedUrl()
    {
        if (is_null($this->_feedUrl)) {
            $this->_feedUrl = Mage::getStoreConfig(self::XML_FEED_URL_PATH);
        }
        return $this->_feedUrl;
    }


    public function getFrequency()
    {
        return Mage::getStoreConfig(self::XML_FREQUENCY_PATH);
    }

    public function getLastUpdate()
    {
        return Mage::app()->loadCache('aitoc_notifications_lastcheck');
    }

    public function setLastUpdate()
    {
        Mage::app()->saveCache(time(), 'aitoc_notifications_lastcheck');
        return $this;
    }

    protected function _getInterests()
    {
        return Mage::getStoreConfig(self::XML_ITERESTS);
    }
}