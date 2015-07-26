<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Megamenu_Model_System_Config_Source_ListCategory
{
	public function toOptionArray($addEmpty = true)
    {
        $options = array();
               
        $collection = Mage::getResourceModel('catalog/category_collection');
        $collection->addAttributeToSelect('name')->addPathFilter('^1/[0-9/]+')->load();
        $cats = array();
        
        foreach ($collection as $category) {
        	$c = new stdClass();
        	$c->label = $category->getName();
        	$c->value = $category->getId();
        	$c->level = $category->getLevel();
        	$c->parentid = $category->getParentId();
            $cats[$c->value] = $c;
        }

        foreach($cats as $id => $c){
        	if (isset($cats[$c->parentid])){
        		if (!isset($cats[$c->parentid]->child)){
        			$cats[$c->parentid]->child = array();
        		}
        		$cats[$c->parentid]->child[] =& $cats[$id];
        	}
        }
        foreach($cats as $id => $c){
        	if (!isset($cats[$c->parentid])){
        		$stack = array($cats[$id]);
        		while( count($stack)>0 ){
        			$opt = array_pop($stack);
        			$option = array(
		    			'label' => ($opt->level>1 ? str_repeat('- - ', $opt->level-1) : '') . $opt->label,
		    			'value' => $opt->value
		    		);
        			array_push($options, $option);
        			if (isset($opt->child) && count($opt->child)){
        				foreach(array_reverse($opt->child) as $child){
        					array_push($stack, $child);
        				}
        			}
        		}
        	}
        }
        unset($cats);
        return $options;
    }
}