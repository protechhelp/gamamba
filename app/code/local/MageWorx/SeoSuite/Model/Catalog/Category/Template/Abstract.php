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
 * @copyright  Copyright (c) 2010 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * SEO Suite extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

abstract class MageWorx_SeoSuite_Model_Catalog_Category_Template_Abstract extends Varien_Object
{
    protected $_category = null;

    public function setCategory(Mage_Catalog_Model_Category $category)
    {
        $this->_category = $category;
        return $this;
    }

    protected function __parse($template)
    {
        $vars = array();
        preg_match_all('~(\[(.*?)\])~', $template, $matches, PREG_SET_ORDER);
        foreach ($matches as $match){
            preg_match('~^((?:(.*?)\{(.*?)\}(.*)|[^{}]*))$~', $match[2], $params);
            array_shift($params);

            if (count($params) == 1){
                $vars[$match[1]]['prefix'] = $vars[$match[1]]['suffix'] = '';
                $vars[$match[1]]['attributes'] = explode('|', $params[0]);
            } else {
                $vars[$match[1]]['prefix'] = $params[1];
                $vars[$match[1]]['suffix'] = $params[3];
                $vars[$match[1]]['attributes'] = explode('|', $params[2]);
            }
        }
        return $vars;
    }

    protected function __compile($template) {
        $vars = $this->__parse($template);

        foreach ($vars as $key => $params){
            foreach ($params['attributes'] as $n => $attribute){
                if (in_array($attribute, $this->_useDefault)){
                    $category = &$this->_defaultCategory;
                } else {
                    $category = &$this->_category;
                }

                $value = '';

                switch ($attribute){
                    case 'category':
                    case 'categories':
                    case 'price':
                         break;
                    default:
                        if ($_attr = $category->getResource()->getAttribute($attribute)){
                            $value = $_attr->getSource()->getOptionText($category->getData($attribute));
                        }
                        if (!$value){
                            $value = $category->getData($attribute);
                        }
                        if (is_array($value)) $value = implode(' ', $value);
                }
                
                if ($value){
                    $value = $params['prefix'] . $value . $params['suffix'];
                    break;
                }
            }
            $template = str_replace($key, $value, $template);
        }

        return $template;
    }
    public function getCompile($product,$template) {
        $this->_category = $product;
        return $this->__compile($template);
    }
}