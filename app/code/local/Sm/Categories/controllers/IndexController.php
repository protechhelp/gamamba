<?php
/*------------------------------------------------------------------------
 # SM Categories - Version 2.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Categories_IndexController extends Mage_Core_Controller_Front_Action
{

	public function IndexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

}