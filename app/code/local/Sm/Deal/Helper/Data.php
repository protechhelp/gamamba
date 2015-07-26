<?php
/*------------------------------------------------------------------------
 # SM Deal - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Deal_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * strips all tag, except a, em, strong
	 * @param string $text
	 * @return string
	 */
	public function _cleanText($text)
	{
		//$text = str_replace('<p>','', $text);
		//$text = str_replace('</p>','', $text);
		$text = strip_tags($text, '<a><b><blockquote><code><del><dd><dl><dt><em><h1><h2><h3><i><kbd><p><pre><s><sup><strong><strike><br><hr>');
		$text = trim($text);
		return $text;
	}

	public function _trimEncode($text)
	{
		$str = strip_tags($text);
		$str = preg_replace('/\s(?=\s)/', '', $str);
		$str = preg_replace('/[\n\r\t]/', '', $str);
		$str = str_replace(' ', '', $str);
		$str = trim($str, "\xC2\xA0\n");
		return $str;
	}

	/**
	 * Parse and build target attribute for links.
	 * @param string $value (_self, _blank, _windowopen, _modal)
	 * _blank    Opens the linked document in a new window or tab
	 * _self    Opens the linked document in the same frame as it was clicked (this is default)
	 * _parent    Opens the linked document in the parent frame
	 * _top    Opens the linked document in the full body of the window
	 * _windowopen  Opens the linked document in a Window
	 * _modal        Opens the linked document in a Modal Window
	 */
	public function parseTarget($type = '_self')
	{
		$target = '';
		switch ($type) {
			default:
			case '_self':
				break;
			case '_blank':
			case '_parent':
			case '_top':
				$target = 'target="' . $type . '"';
				break;
			case '_windowopen':
				$target = "onclick=\"window.open(this.href,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,false');return false;\"";
				break;
			case '_modal':
				// user process
				break;
		}
		return $target;
	}

	/**
	 * Truncate string by $length
	 * @param string $string
	 * @param int $length
	 * @param string $etc
	 * @return string
	 */
	public function truncate($string, $length, $etc = '.')
	{
		return defined('MB_OVERLOAD_STRING')
			? $this->_mb_truncate($string, $length, $etc)
			: $this->_truncate($string, $length, $etc);
	}

	/**
	 * Truncate string if it's size over $length
	 * @param string $string
	 * @param int $length
	 * @param string $etc
	 * @return string
	 */
	private function _truncate($string, $length, $etc = '...')
	{
		if ($length > 0 && $length < strlen($string)) {
			$buffer = '';
			$buffer_length = 0;
			$parts = preg_split('/(<[^>]*>)/', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
			$self_closing_tag = split(',', 'area,base,basefont,br,col,frame,hr,img,input,isindex,link,meta,param,embed');
			$open = array();

			foreach ($parts as $i => $s) {
				if (false === strpos($s, '<')) {
					$s_length = strlen($s);
					if ($buffer_length + $s_length < $length) {
						$buffer .= $s;
						$buffer_length += $s_length;
					} else if ($buffer_length + $s_length == $length) {
						if (!empty($etc)) {
							$buffer .= ($s[$s_length - 1] == ' ') ? $etc : " $etc";
						}
						break;
					} else {
						$words = preg_split('/([^\s]*)/', $s, -1, PREG_SPLIT_DELIM_CAPTURE);
						$space_end = false;
						foreach ($words as $w) {
							if ($w_length = strlen($w)) {
								if ($buffer_length + $w_length < $length) {
									$buffer .= $w;
									$buffer_length += $w_length;
									$space_end = (trim($w) == '');
								} else {
									if (!empty($etc)) {
										$more = $space_end ? $etc : " $etc";
										$buffer .= $more;
										$buffer_length += strlen($more);
									}
									break;
								}
							}
						}
						break;
					}
				} else {
					preg_match('/^<([\/]?\s?)([a-zA-Z0-9]+)\s?[^>]*>$/', $s, $m);
					//$tagclose = isset($m[1]) && trim($m[1])=='/';
					if (empty($m[1]) && isset($m[2]) && !in_array($m[2], $self_closing_tag)) {
						array_push($open, $m[2]);
					} else if (trim($m[1]) == '/') {
						$tag = array_pop($open);
						if ($tag != $m[2]) {
							// uncomment to to check invalid html string.
							// die('invalid close tag: '. $s);
						}
					}
					$buffer .= $s;
				}
			}
			// close tag openned.
			while (count($open) > 0) {
				$tag = array_pop($open);
				$buffer .= "</$tag>";
			}
			return $buffer;
		}
		return $string;
	}

	/**
	 * Truncate mutibyte string if it's size over $length
	 * @param string $string
	 * @param int $length
	 * @param string $etc
	 * @return string
	 */
	private function _mb_truncate($string, $length, $etc = '...')
	{
		$encoding = mb_detect_encoding($string);
		if ($length > 0 && $length < mb_strlen($string, $encoding)) {
			$buffer = '';
			$buffer_length = 0;
			$parts = preg_split('/(<[^>]*>)/', $string, -1, PREG_SPLIT_DELIM_CAPTURE);
			$self_closing_tag = explode(',', 'area,base,basefont,br,col,frame,hr,img,input,isindex,link,meta,param,embed');
			$open = array();

			foreach ($parts as $i => $s) {
				if (false === mb_strpos($s, '<')) {
					$s_length = mb_strlen($s, $encoding);
					if ($buffer_length + $s_length < $length) {
						$buffer .= $s;
						$buffer_length += $s_length;
					} else if ($buffer_length + $s_length == $length) {
						if (!empty($etc)) {
							$buffer .= ($s[$s_length - 1] == ' ') ? $etc : " $etc";
						}
						break;
					} else {
						$words = preg_split('/([^\s]*)/', $s, -1, PREG_SPLIT_DELIM_CAPTURE);
						$space_end = false;
						foreach ($words as $w) {
							if ($w_length = mb_strlen($w, $encoding)) {
								if ($buffer_length + $w_length < $length) {
									$buffer .= $w;
									$buffer_length += $w_length;
									$space_end = (trim($w) == '');
								} else {
									if (!empty($etc)) {
										$more = $space_end ? $etc : " $etc";
										$buffer .= $more;
										$buffer_length += mb_strlen($more);
									}
									break;
								}
							}
						}
						break;
					}
				} else {
					preg_match('/^<([\/]?\s?)([a-zA-Z0-9]+)\s?[^>]*>$/', $s, $m);
					//$tagclose = isset($m[1]) && trim($m[1])=='/';
					if (empty($m[1]) && isset($m[2]) && !in_array($m[2], $self_closing_tag)) {
						array_push($open, $m[2]);
					} else if (trim($m[1]) == '/') {
						$tag = array_pop($open);
						if ($tag != $m[2]) {
							// uncomment to to check invalid html string.
							// die('invalid close tag: '. $s);
						}
					}
					$buffer .= $s;
				}
			}
			// close tag openned.
			while (count($open) > 0) {
				$tag = array_pop($open);
				$buffer .= "</$tag>";
			}
			return $buffer;
		}
		return $string;
	}
}

?>