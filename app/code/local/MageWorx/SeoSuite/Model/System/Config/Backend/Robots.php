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
 * @package    MageWorx_SeoSuite
 * @copyright  Copyright (c) 2013 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * SEO Suite extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_SeoSuite_Model_System_Config_Backend_Robots extends Mage_Core_Model_Config_Data
{
    private $_filePath;
    
    public function _construct() {
        $this->_filePath = Mage::getBaseDir().'/'.'robots.txt';
       
        parent::_construct();
    }
    
    public function afterLoad() {
        $file = $this->_filePath; 
        if(!file_exists($file)) {
            $this->createRobotsFile();
        }
        return $this->setValue(file_get_contents($file));
    }
    
    public function save() {
        $file       = $this->_filePath;
        $data       = Mage::app()->getRequest()->getParam('groups');
		$robotsData = ' ';
        if(isset($data['seosuite']['fields']['robots_editor'])) {
            $robotsData = $data['seosuite']['fields']['robots_editor'];
        	$robotsData = $robotsData['value'];
		}
		if(!file_exists($file)) {
            if(!$this->createRobotsFile()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('seosuite')->__('File robots.txt can\'t be created. Please check permissions'));
            }
        }
      //	echo "<pre>"; var_dump($robotsData);// exit;
		if ($robotsData=="") {
				unlink($file);
				return true;
			}
        $result = file_put_contents($file, $robotsData,LOCK_EX);
        if($result) {
            return true;
        }
        return Mage::getSingleton('adminhtml/session')->addError(Mage::helper('seosuite')->__("File robots.txt can't be modified. Please check permissions."));
    }
    
    public function createRobotsFile()
    {
        $file = $this->_filePath;
        if(!is_writable(Mage::getBaseDir())) {
             //Mage::getSingleton('adminhtml/session')->addError(Mage::helper('seosuite')->__('Root catalog not writeble. Please check permissions'));
             return false;
        }
        try {$handle = fopen($file, 'w+');
            fwrite($handle, '');
            fclose($handle);
//            Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('seosuite')->__("File robots.txt was created."));
            return true;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('seosuite')->__($e->getMessage()));
            return false;
        }
    }
}
