<?php
/*
* ModifyMage Solutions (http://ModifyMage.com)
* Serial Codes - Serial Numbers, Product Codes, PINs, and More
*
* NOTICE OF LICENSE
* This source code is owned by ModifyMage Solutions and distributed for use under the
* provisions, terms, and conditions of our Commercial Software License Agreement which
* is bundled with this package in the file LICENSE.txt. This license is also available
* through the world-wide-web at this URL: http://www.modifymage.com/software-license
* If you do not have a copy of this license and are unable to obtain it through the
* world-wide-web, please email us at license@modifymage.com so we may send you a copy.
*
* @category		Mmsmods
* @package		Mmsmods_Serialcodes
* @author		David Upson
* @copyright	Copyright 2013 by ModifyMage Solutions
* @license		http://www.modifymage.com/software-license
*/

class Mmsmods_Serialcodes_Model_Product_Email_Templates extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
	{
		if (!$this->_options)
		{
			$default = $this->getAttribute()->getDefaultValue();
			$this->_options[0]['value'] = $default;
			$this->_options[0]['label'] = 'Default Template from Locale';
			if ($template_collection = Mage::getResourceSingleton('core/email_template_collection'))
			{
				$i = 1;
				foreach($template_collection as $template)
				{
					$tempid = $template->getTemplateId();
					$tempcode = $template->getTemplateCode();
					if($temporig = $template->getData('orig_template_code'))
					{
						if($temporig == $default)
						{
							$this->_options[$i]['value'] = $tempid;
							$this->_options[$i]['label'] = $tempcode;
							$i++;
						}
					} else {
						$this->_options[$i]['value'] = $tempid;
						$this->_options[$i]['label'] = $tempcode;
						$i++;
					}
				}
			}
		}
		return $this->_options;
	}

	public function toOptionArray()
    {
		$_options = array();
		if ($this->getAllOptions())
		{
			foreach ($this->getAllOptions() as $option) {
				$_options[$option['value']] = $option['label'];
			}
		}
        return $_options;
    }
}