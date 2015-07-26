<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

/**PHP_COMMENT**/
class Sm_Megamenu_Helper_Test extends Mage_Core_Helper_Abstract {
	public function getColColorTest(){
		$html ='
			<div style="padding-left:500px">
				<div class="col_1 hv">col_1&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</div>
				<div class="col_2 hv">col_2&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</div>
				<div class="col_3 hv">col_3&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</div>
				<div class="col_4 hv">col_4&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</div>
				<div class="col_5 hv">col_5&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</div>
				<div class="col_6 hv">col_6&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</div>
				<div style="clear:both"></div>		
			</div>
		';
		return $html;
	}
	public function getColColorStyleTest(){
		$html = '
			<style>
				.col_1{
					background-color:rgb(34,177,76);
				}
				.col_2{
					background-color:rgb(0,162,232);
				}
				.col_3{
					background-color:rgb(63,72,204);
				}
				.col_4{
					background-color:rgb(181,230,29);
				}
				.col_5{
					background-color:rgb(153,217,234);
				}
				.col_6{
					background-color:rgb(112,146,190);
				}
				.hv {
					float: left;
					height: 30px;
					margin: 10px;
					padding-left: 4px;
					width: 35px;
				}
			</style>		
		';
		return $html;
	}

}
?>