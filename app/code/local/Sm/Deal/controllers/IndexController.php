<?php
/*------------------------------------------------------------------------
 # SM Deal - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Deal_IndexController extends Mage_Core_Controller_Front_Action
{

	public function IndexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

}