<?php
/**
 * MagPleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE-CE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE-CE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * MagPleasure does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Magpleasure does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   MagPleasure
 * @package    Magpleasure_Version
 * @version    0.8.1
 * @copyright  Copyright (c) 2012-2015 MagPleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE-CE.txt
 */

class Magpleasure_Common_Model_Core_Email_Template extends Mage_Core_Model_Email_Template
{
    protected $_isFake = false;
    protected $_viewPort = true;
    protected $_registryContentKey = "send_content";
    protected $_registrySubjectKey = "send_subject";

    /**
     * @param $registrySubjectKey
     * @return $this
     */
    public function setRegistrySubjectKey($registrySubjectKey)
    {
        $this->_registrySubjectKey = $registrySubjectKey;
        return $this;
    }

    /**
     * @param $registryContentKey
     * @return $this
     */
    public function setRegistryContentKey($registryContentKey)
    {
        $this->_registryContentKey = $registryContentKey;
        return $this;
    }

    /**
     * @param $viewPort
     * @return $this
     */
    public function setViewPort($viewPort)
    {
        $this->_viewPort = $viewPort;
        return $this;
    }

    /**
     * @param $isFake
     * @return $this
     */
    public function setIsFake($isFake)
    {
        $this->_isFake = $isFake;
        return $this;
    }

    public function getProcessedTemplate(array $variables = array())
    {
        $html = parent::getProcessedTemplate($variables);

        if ($this->_viewPort){

            $viewPortMetaTag = '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
            if (strpos($html, "<head>") !== false){
                $html = str_replace("<head>", "<head>".$viewPortMetaTag, $html);
            } else {
                $html = str_replace("<html>", "<html><head>".$viewPortMetaTag."</head>", $html);
            }
        }

        return $html;
    }

    public function send($email, $name = null, array $variables = array())
    {
        if ($this->_isFake){

            $registryContentKey = $this->_registryContentKey;
            if (Mage::registry($registryContentKey)){
                Mage::unregister($registryContentKey);
            }

            $registrySubjectKey = $this->_registrySubjectKey;
            if (Mage::registry($registrySubjectKey)){
                Mage::unregister($registrySubjectKey);
            }

            $emails = array_values((array)$email);
            $names = is_array($name) ? $name : (array)$name;
            $names = array_values($names);
            foreach ($emails as $key => $email) {
                if (!isset($names[$key])) {
                    $names[$key] = substr($email, 0, strpos($email, '@'));
                }
            }

            $variables['email'] = reset($emails);
            $variables['name'] = reset($names);

            $this->setUseAbsoluteLinks(true);
            $content = $this->getProcessedTemplate($variables);
            $subject = $this->getProcessedTemplateSubject($variables);
            Mage::register($registryContentKey, $content, true);
            Mage::register($registrySubjectKey, $subject, true);
            return true;

        } else {

            return parent::send($email, $name, $variables);
        }
    }
}