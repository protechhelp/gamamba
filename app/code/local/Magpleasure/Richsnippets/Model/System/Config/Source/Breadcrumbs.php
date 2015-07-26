<?php

/**
 * Magpleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE.txt
 *
 * @category   Magpleasure
 * @package    Magpleasure_Richsnippets
 * @copyright  Copyright (c) 2014-2015 Magpleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE.txt
 */
class Magpleasure_Richsnippets_Model_System_Config_Source_Breadcrumbs
{
    const NONE = 0;
    const DATA_VOCABULARY = 1;
    const SCHEMA = 2;

    /**
     * Helper
     *
     * @return Magpleasure_Richsnippets_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('richsnippets');
    }

    /**
     * Get options in 'key-value' format
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            self::NONE => $this->_helper()->__("Don't Add"),
            self::DATA_VOCABULARY => $this->_helper()->__('Based on data-vocabulary.org'),
            self::SCHEMA => $this->_helper()->__('Based on schema.org')
        );
    }
}