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
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @copyright  Copyright (c) 2011 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Extended Sitemap extension
 *
 * @category   MageWorx
 * @package    MageWorx_XSitemap
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */
class MageWorx_XSitemap_Model_Sitemap extends Mage_Core_Model_Abstract {

    protected $_filePath;
    public $_sitemapInc = 1;
    public $_linkInc = 0;
    public $_totalProducts = 0;
    public $_currentInc = 0;

    protected function _construct() {
        $this->_init('xsitemap/sitemap');
    }

    protected function _beforeSave() {
        $io = new Varien_Io_File();
        $realPath = $io->getCleanPath(Mage::getBaseDir() . '/' . $this->getSitemapPath());

        if (!$io->allowedPath($realPath, Mage::getBaseDir())) {
            Mage::throwException(Mage::helper('xsitemap')->__('Please define correct path'));
        }

        if (!$io->fileExists($realPath, false)) {
            Mage::throwException(Mage::helper('xsitemap')->__('Please create the specified folder "%s" before saving the sitemap.', $this->getSitemapPath()));
        }

        if (!$io->isWriteable($realPath)) {
            Mage::throwException(Mage::helper('xsitemap')->__('Please make sure that "%s" is writable by web-server.', $this->getSitemapPath()));
        }

        if (!preg_match('#^[a-zA-Z0-9_\.]+$#', $this->getSitemapFilename())) {
            Mage::throwException(Mage::helper('xsitemap')->__('Please use only letters (a-z or A-Z), numbers (0-9) or underscore (_) in the filename. No spaces or other characters are allowed.'));
        }
        if (!preg_match('#\.xml$#', $this->getSitemapFilename())) {
            $this->setSitemapFilename($this->getSitemapFilename() . '.xml');
        }

        $this->setSitemapPath(rtrim(str_replace(str_replace('\\', '/', Mage::getBaseDir()), '', $realPath), '/') . '/');

        return parent::_beforeSave();
    }

    protected function getPath() {
        if (is_null($this->_filePath)) {
            $this->_filePath = str_replace('//', '/', Mage::getBaseDir() .
                            $this->getSitemapPath());
        }
        return $this->_filePath;
    }

    public function getPreparedFilename() {
        return $this->getPath() . $this->getSitemapFilename();
    }

    
    //$entity = 'category', 'product', 'tag', 'cms', 'additional_links', 'sitemap_finish'
    
    public function generateXml($entity=false) {
        $enableTrailingSlash = Mage::getStoreConfigFlag('mageworx_seo/seosuite/trailing_slash');
        $this->_useIndex = Mage::getStoreConfigFlag('mageworx_seo/google_sitemap/use_index');
        $this->_splitSize = (int) Mage::getStoreConfig('mageworx_seo/google_sitemap/split_size') * 1024;
        $this->_maxLinks = (int) Mage::getStoreConfig('mageworx_seo/google_sitemap/max_links');

        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);

        $io->open(array('path' => $this->getPath()));
        
        // partial open or first open
        if (!$entity || $entity=='category') $this->_openXml($io); else $this->_openXml($io, true);
        
        $storeId = $this->getStoreId();
        $date = Mage::getSingleton('core/date')->gmtDate('Y-m-d');
        //$testUrl = "http://www.domain.com/?___store=en";
        $url = Mage::app()->getStore($storeId)->getUrl();
        $mageUrl = $baseUrl = (strpos($url, "?")) ? substr($url,0,strpos($url,"?")) : $url;
        
        // generate categories
        if (!$entity || $entity=='category') {
            $changefreq = (string) Mage::getStoreConfig('mageworx_seo/google_sitemap/category_changefreq');
            $priority = (string) Mage::getStoreConfig('mageworx_seo/google_sitemap/category_priority');
            $collection = Mage::getResourceModel('xsitemap/catalog_category')->getCollection($storeId);
            foreach ($collection as $item) {
                $upTime = Mage::getModel('catalog/category')->load($item->getId())->getUpdatedAt();
                if($upTime=='0000-00-00 00:00:00') {
                    $upTime = Mage::getModel('catalog/category')->load($item->getId())->getCreatedAt();
                }
                
                $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                    htmlspecialchars($baseUrl . $item->getUrl()),
                    substr($upTime,0,10),
                    $changefreq,
                    $priority
                );
                $io->streamWrite($xml);

                $this->_checkSitemapLimits($io);
            }
            unset($collection);
        }     

        // generate products
        if (!$entity || $entity=='product') {
            $isProductImages = Mage::getStoreConfigFlag('mageworx_seo/google_sitemap/product_images');
            $imagesSize = (string) Mage::getStoreConfig('mageworx_seo/google_sitemap/product_images_size');
            if (!preg_match('/^\d+x\d+$/', $imagesSize)) {
                $imagesSize = false;
            }
            $changefreq = (string) Mage::getStoreConfig('mageworx_seo/google_sitemap/product_changefreq');
            $priority = (string) Mage::getStoreConfig('mageworx_seo/google_sitemap/product_priority');
            
            
            $this->_totalProducts = Mage::getResourceModel('xsitemap/catalog_product')->getCollection($storeId, true);
            if ($this->_totalProducts>0) {
                $limit = Mage::getStoreConfig('mageworx_seo/google_sitemap/xml_limit'); 
                if ($this->_currentInc<$this->_totalProducts) {
                    $collection = Mage::getResourceModel('xsitemap/catalog_product')->getCollection($storeId, false, $limit, $this->_currentInc);
                    $this->_currentInc += $limit;            
                    if ($this->_currentInc>=$this->_totalProducts) {
                        $this->_currentInc = $this->_totalProducts;
                        $result['stop'] = 1;
                    }                    
                } 

                $useCategories = Mage::getStoreConfigFlag('catalog/seo/product_use_categories');

                foreach ($collection as $item) {            
                    $images = '';
                    if($isProductImages) {
                        $gallery = $item->getGallery();
                        if (is_array($gallery)) {
                            foreach ($gallery as $image) {
                                if ($image['disabled'] != 1) {
                                    $images .= '<image:image><image:loc>' . htmlspecialchars($baseUrl . 'media/catalog/product' . ($imagesSize ? '/image/size/'.$imagesSize : '') . $image['file']) . '</image:loc></image:image>';
                                }
                            }
                        }
                    }
                    $url=$baseUrl . $item->getUrl();
                    if($enableTrailingSlash) {
                $url = trim($url,'/');
                $url .='/';
            }
                    $upTime = $item->getUpdatedAt();
                    if(!$upTime) {
                        $upTime = date("Y-m-d H:i:s");
                    }
                    $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority>%s</url>',
                                    htmlspecialchars($url),
                                    substr($upTime,0,10),
                                    $changefreq,
                                    $priority,
                                    $images
                    );
                    $io->streamWrite($xml);
                    $this->_checkSitemapLimits($io);
                }
                unset($collection);
            }   
        }    

        // generate tags
        if (!$entity || $entity=='tag') {
            $productTags = Mage::getStoreConfigFlag('mageworx_seo/google_sitemap/product_tags');
            if ($productTags) {
                $changefreq = (string) Mage::getStoreConfig('mageworx_seo/google_sitemap/product_tags_changefreq');
                $priority = (string) Mage::getStoreConfig('mageworx_seo/google_sitemap/product_tags_priority');
                $tags = Mage::getModel('tag/tag')->setStoreId($storeId)->getPopularCollection()
                                ->joinFields($storeId)
                                ->load();
                //                echo $tags->getSelect()->__toString(); exit;
                foreach ($tags as $item) {
                    $tagUrl = $mageUrl .'tag/'.$item->getName();
                    $url = str_replace("index.php/", '', $tagUrl);
                    if($enableTrailingSlash) {
                        $url = trim($url,'/');
                        $url .='/';
                    }

                    $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                                    htmlspecialchars($url),
                                    $date,
                                    $changefreq,
                                    $priority
                    );
                    $io->streamWrite($xml);

                    $this->_checkSitemapLimits($io);
                }
                unset($collection);
            }
        }    

        // generate cms
        if (!$entity || $entity=='cms') {
            $changefreq = (string) Mage::getStoreConfig('mageworx_seo/google_sitemap/page_changefreq');
            $priorityDefault = (string) Mage::getStoreConfig('mageworx_seo/google_sitemap/page_priority');
            $collection = Mage::getResourceModel('xsitemap/cms_page')->getCollection($storeId);
            foreach ($collection as $item) {
                if($item->getUrl() == "") {
	                $priority = 1;
                }
				else {
					$priority = $priorityDefault;
				}
                $url = $baseUrl . $item->getUrl();
                if($enableTrailingSlash) {
                    $url = trim($url,'/');
                    $url .='/';
                }

                $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                                htmlspecialchars($url),
                                $date,
                                $changefreq,
                                $priority
                );
                $io->streamWrite($xml);
                $this->_checkSitemapLimits($io);
            }
            unset($collection);
        }       
        // generate AW_Blog
        if ((!$entity || $entity=='blog') && Mage::getStoreConfig('blog/blog/enabled')) {
            $defaultRote = (string) Mage::getStoreConfig('blog/blog/route');
            if(!$defaultRote) {
                $defaultRote = 'blog';
            }
            $changefreq = (string) Mage::getStoreConfig('mageworx_seo/google_sitemap/blog_changefreq');
            $priority = (string) Mage::getStoreConfig('mageworx_seo/google_sitemap/blog_priority');
            $collection = Mage::getResourceModel('xsitemap/blog_page')->getCollection($storeId);
            foreach ($collection as $item) {
            			list($dDate,$dTime) = explode(' ',$item->getDate());
		$url = $baseUrl . $defaultRote ."/". $item->getUrl();	//	echo $dDate; exit;
                    if($enableTrailingSlash) {
                    $url = trim($url,'/');
                    $url .='/';
                }
                if(!$dDate) $dDate = date('Y-m-d');
                $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                                htmlspecialchars($url),
                                $dDate,
                                $changefreq,
                                $priority
                );
                $io->streamWrite($xml);
                $this->_checkSitemapLimits($io);
            }
            unset($collection);
        }  
        
        //Fishpig_Blog
        if((!$entity && (string)Mage::getConfig()->getModuleConfig('Fishpig_Wordpress')->active=='true') || $entity=='fishpig') {
			
            $changefreq = (string)Mage::getStoreConfig('mageworx_seo/google_sitemap/blog_changefreq');
            $priority   = (string)Mage::getStoreConfig('mageworx_seo/google_sitemap/blog_priority');
            $url = Mage::helper('wordpress')->getUrl();
            $baseURI = str_replace('/index.php','',Mage::getBaseUrl());
            if($mageUrl!=$baseURI) {
                $url = str_replace($baseURI,$mageUrl,$url);    
            }
            if($enableTrailingSlash) {
                $url = trim($url,'/');
                $url .='/';
            }
            $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>'."\n",
                htmlspecialchars($url),
                $date,
                $changefreq,
                $priority
            );
            $io->streamWrite($xml);
            
            // Posts & Pages
            foreach (array('post', 'page') as $type) {
                $items = Mage::getResourceModel('wordpress/' . $type . '_collection')
                        ->addIsPublishedFilter()
                        ->setOrderByPostDate();

                if (count($items) > 0) { 
                    foreach ($items as $item) {
                        $url = $item->getPermalink();
                        if($mageUrl!=$baseURI) {
                            $url = str_replace($baseURI,$mageUrl,$url);    
                        }
                        if($enableTrailingSlash) {
                            $url = trim($url,'/');
                            $url .='/';
                        }
                        $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                            htmlspecialchars($url),
                            $item->getPostModifiedDate('Y-m-d') ? $item->getPostModifiedDate('Y-m-d') : $date,
                            $changefreq,
                            $priority
                         );
                        $io->streamWrite($xml);
                        $this->_checkSitemapLimits($io);
                    }
                    unset($items);
                }
            }
        }	
        
        if (!$entity || $entity=='additional_links') {
            $changefreq = (string) Mage::getStoreConfig('mageworx_seo/google_sitemap/link_changefreq');
            $priority = (string) Mage::getStoreConfig('mageworx_seo/google_sitemap/link_priority');
            $addLinks = array_filter(preg_split('/\r?\n/', Mage::getStoreConfig(MageWorx_XSitemap_Block_Links::XML_PATH_ADD_LINKS, $storeId)));
            if (count($addLinks)) {
                foreach ($addLinks as $link) {
                    $_link = explode(',', $link, 2);
                    if (count($_link) == 2) {
                        $url = Mage::getUrl((string) $_link[0]);
                        if($enableTrailingSlash) {
                            $url = trim($url,'/');
                            $url .='/';
                        }
                        $links[] = new Varien_Object(array('url' => $url));
                    }
                }
            }
        
            $xml = Mage::getStoreConfig(MageWorx_XSitemap_Block_Links::XML_PATH_ADD_LINKS, $storeId);
            try {
                $xmlLinks = simplexml_load_string($xml);
            } catch (Exception $e) {

            }
            if (!empty($xmlLinks) && count($xmlLinks)) {
                foreach ($xmlLinks as $link) {
                    $url = (string) $link->href;
                    if($enableTrailingSlash) {
                            $url = trim($url,'/');
                            $url .='/';
                        }
                    $links[] = new Varien_Object(array('url' => $url));
                }
            }
            if (!empty($links) && count($links)) {
                foreach ($links as $item) {
                    $xml = sprintf('<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                                    htmlspecialchars($baseUrl . $item->getUrl()),
                                    $date,
                                    $changefreq,
                                    $priority
                    );
                    $io->streamWrite($xml);
                    $this->_checkSitemapLimits($io);
                }
                unset($links);
            }
        }    

        if (!$entity || $entity=='sitemap_finish') Mage::dispatchEvent('xsitemap_sitemap_generate_after', array('io_sitemap' => $io));
        
        // partial close or final close
        if (!$entity || $entity=='sitemap_finish') { $this->_closeXml($io); } else  { $this->_closeXml($io, true); }

        if (!$entity || $entity=='sitemap_finish') {
            $this->_generateSitemapIndex($io);
            $this->setSitemapTime(Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s'));
            $this->save();
        }    

        return $this;
    }

    protected function _getSitemapFilename() {
        if ($this->_useIndex) {
            $sitemapFilename = $this->getData('sitemap_filename');
            $ext = strrchr($sitemapFilename, '.');
            $sitemapFilename = substr($sitemapFilename, 0, strlen($sitemapFilename) - strlen($ext)) . '_' . sprintf('%03s', $this->_sitemapInc) . $ext;

            return $sitemapFilename;
        }
        return $this->getData('sitemap_filename');
    }

    protected function _checkSitemapLimits($io) {
       
        if ($this->_useIndex) {
            if ($this->_linkInc == $this->_maxLinks || $io->streamStat('size') >= $this->_splitSize - 10240) {
                $this->_linkInc = 0;
                $this->_sitemapInc++;
                $this->_closeXml($io);
                $this->_openXml($io);
            }
            $this->_linkInc++;
        }
    }

    protected function _openXml($io, $append = false) {
        if ($io->fileExists($this->_getSitemapFilename()) && !$io->isWriteable($this->_getSitemapFilename())) {
            Mage::throwException(Mage::helper('xsitemap')->__('File "%s" cannot be saved. Please, make sure the directory "%s" is writeable by web server.', $this->_getSitemapFilename(), $this->getPath()));
        }

        if ($append) $mode = 'a+'; else $mode = 'w+';       
        $io->streamOpen($this->_getSitemapFilename(), $mode);

        if (!$append) {
            $add = '';
            $io->streamWrite('<?xml version="1.0" encoding="UTF-8"?>' . "\n");
            if(Mage::getStoreConfigFlag('mageworx_seo/google_sitemap/product_images')) {
                $add =  "\n" . ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"';
            }
            $io->streamWrite('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'.$add.'>');
            
        }    
    }

    protected function _closeXml($io, $append = false) {
        if (!$append) $io->streamWrite('</urlset>');
        $io->streamClose();
    }

    protected function _generateSitemapIndex($io) {
        if (!$this->_useIndex) {
            return;
        }

        if ($io->fileExists($this->getSitemapFilename()) && !$io->isWriteable($this->getSitemapFilename())) {
            Mage::throwException(Mage::helper('xsitemap')->__('File "%s" cannot be saved. Please, make sure the directory "%s" is writeable by web server.', $this->getSitemapFilename(), $this->getPath()));
        }

        $io->streamOpen($this->getSitemapFilename());

        $io->streamWrite('<?xml version="1.0" encoding="UTF-8"?>' . "\n");
        $io->streamWrite('<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');

        $storeId = $this->getStoreId();
        $date = Mage::getSingleton('core/date')->gmtDate('Y-m-d');
        $baseUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

        $i = $this->_sitemapInc;
        for ($this->_sitemapInc = 1; $this->_sitemapInc <= $i; $this->_sitemapInc++) {
            $fileName = preg_replace('/^\//', '', $this->getSitemapPath() . $this->_getSitemapFilename());
            if (file_exists(BP . DS . $fileName)) {
                $xml = sprintf('<sitemap><loc>%s</loc><lastmod>%s</lastmod></sitemap>',
                                htmlspecialchars($baseUrl . $fileName),
                                $date
                );
                $io->streamWrite($xml);
            }
        }

        $io->streamWrite('</sitemapindex>');
        $io->streamClose();
    }

}