<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class MWorks_Redeemvoucher_Block_Adminhtml_Track_Renderer_Expired extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
		$value =  $row->getData($this->getColumn()->getIndex());
		//Zend_Debug::dump($row->getData());die;
		$created_time = $row->getData('created_time');
		$num_voucher_expired = $row->getData('voucher_expiry');
		$diff = $this->getDiff($created_time);
		
		if(intval($diff)>=$num_voucher_expired){
			// Mage::getSingleton('checkout/session')->addError(' your voucher has expired !');	
			return 'Expired';
		}
		// end expired voucher		
		return intval($num_voucher_expired -$diff);
	}
	public function getDiff($created_time){
		//expired voucher
		$currentDate = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
		$d_start    = new DateTime($created_time);
		$d_end      = new DateTime($currentDate);
		$diff =floor( $this->GetDeltaTime($d_end ,$d_start)/24);
		return $diff;
	}
	public function getDateExpired($expiry){
		
	}
	public function GetDeltaTime($dtTime1, $dtTime2)
	{
	  $nUXDate1 = strtotime($dtTime1->format("Y-m-d H:i:s"));
	  $nUXDate2 = strtotime($dtTime2->format("Y-m-d H:i:s"));
	  $nUXDelta = $nUXDate1 - $nUXDate2;
	  $strDeltaTime = "" . $nUXDelta/60/60; // sec -> hour
	  $nPos = strpos($strDeltaTime, ".");
	  if (nPos !== false)
		$strDeltaTime = substr($strDeltaTime, 0, $nPos + 3);

	  return $strDeltaTime;
	}	
}