<?php
/*------------------------------------------------------------------------
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class YtTheme {
	//Params will store in cookie for user select. Default: store all params
	var $_params_cookie = array(); 
	//Params will store in cookie for user select. Default: store all params
	var $_params = null; 
	var $template = '';

	function YtTheme ($template, $_param, $_params_cookie=null) {
		$this->template = $template;
		$this->_params = $_param;
		if( !$_params_cookie ){
			$_params_cookie = array(
							'theme_color',
							'layoutstyle',
							'menustyle'
			);
		}
		if($_params_cookie){
			foreach ($_params_cookie as $k) {
				$this->_params_cookie[$k] = $this->_params->get($k);
			}
		}
		$this->getUserNik();
	}

	function getUserNik(){
		$exp = time() + 60*60*24*355;
		if (isset($_COOKIE[$this->template.'_tpl']) && $_COOKIE[$this->template.'_tpl'] == $this->template){
			foreach($this->_params_cookie as $k=>$v) {
				$kc = $this->template."_".$k;
				if (isset($_GET[$k])){
					$v = $_GET[$k];
					setcookie ($kc, $v, $exp, '/');
				}else{
					if (isset($_COOKIE[$kc])){
						$v = $_COOKIE[$kc];
					}
				}
				$this->setParam($k, $v);
			}

		} else {
			@setcookie ($this->template.'_tpl', $this->template, $exp, '/');
		}
		return $this;
	}

	function getParam ($param, $default='') { 
		if (isset($this->_params_cookie[$param])) {
			return $this->_params_cookie[$param];
		}
		return $this->_params->get($param, $default);
	}

	function setParam ($param, $value) {
		$this->_params_cookie[$param] = $value;
	}
	
	function isOP () {
		return isset($_SERVER['HTTP_USER_AGENT']) &&
			preg_match('/opera/i',$_SERVER['HTTP_USER_AGENT']);
	}
	function isChrome () {
		return isset($_SERVER['HTTP_USER_AGENT']) &&
			preg_match('/chrome/i',$_SERVER['HTTP_USER_AGENT']);
	}
	function isSafari () {
		return isset($_SERVER['HTTP_USER_AGENT']) &&
			preg_match('/safari/i',$_SERVER['HTTP_USER_AGENT']);
	}
	function mobile_device_detect () {
		require_once ('mobile_device_detect.php');
		//bypass special browser:
		$special = array('jigs', 'w3c ', 'w3c-', 'w3c_');		
		if (in_array(strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4)), $special)) return false;
		return mobile_device_detect('iphone','android','opera','blackberry','palm','windows');
	}
	
	function mobile_device_detect_layout () {
		$ui = $this->getParam('ui');
		return $ui=='desktop'?false:(($ui=='mobile' && !$this->mobile_device_detect())?'iphone':$this->mobile_device_detect());
	}
	
	function baseurl(){
		return $this->getBaseURL();
	}
	function getBaseURL() {
		static $_baseURL = '';
		if (!$_baseURL) {
			// Determine if the request was over SSL (HTTPS)
			if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) {
				$https = 's://';
			} else {
				$https = '://';
			}

			/*
			 * Since we are assigning the URI from the server variables, we first need
			 * to determine if we are running on apache or IIS.  If PHP_SELF and REQUEST_URI
			 * are present, we will assume we are running on apache.
			 */
			if (!empty ($_SERVER['PHP_SELF']) && !empty ($_SERVER['REQUEST_URI'])) {

				/*
				 * To build the entire URI we need to prepend the protocol, and the http host
				 * to the URI string.
				 */
				$theURI = 'http' . $https . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

			/*
			 * Since we do not have REQUEST_URI to work with, we will assume we are
			 * running on IIS and will therefore need to work some magic with the SCRIPT_NAME and
			 * QUERY_STRING environment variables.
			 */
			}
			 else
			{
				// IIS uses the SCRIPT_NAME variable instead of a REQUEST_URI variable... thanks, MS
				$theURI = 'http' . $https . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

				// If the query string exists append it to the URI string
				if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
					$theURI .= '?' . $_SERVER['QUERY_STRING'];
				}
			}

			// Now we need to clean what we got since we can't trust the server var
			$theURI = urldecode($theURI);
			$theURI = str_replace('"', '&quot;',$theURI);
			$theURI = str_replace('<', '&lt;',$theURI);
			$theURI = str_replace('>', '&gt;',$theURI);
			$theURI = preg_replace('/eval\((.*)\)/', '', $theURI);
			$theURI = preg_replace('/[\\\"\\\'][\\s]*javascript:(.*)[\\\"\\\']/', '""', $theURI);	
			
			//Parse theURL
			$_parts = $this->_parseURL ($theURI);
			$_baseURL = '';
			$_baseURL .= (!empty($_parts['scheme']) ? $_parts['scheme'].'://' : '');
			$_baseURL .= (!empty($_parts['host']) ? $_parts['host'] : '');
			$_baseURL .= (!empty($_parts['port']) && $_parts['port']!=80 ? ':'.$_parts['port'] : '');

			if (strpos(php_sapi_name(), 'cgi') !== false && !empty($_SERVER['REQUEST_URI'])) {
				//Apache CGI
				$_path =  rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
			} else {
				//Others
				$_path =  rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
			}

			$_baseURL .= $_path;
		}
		return $_baseURL;
	}

	function _parseURL($uri)
	{
		$parts = array();
		if (version_compare( phpversion(), '4.4' ) < 0)
		{
			$regex = "<^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\\?([^#]*))?(#(.*))?>";
			$matches = array();
			preg_match($regex, $uri, $matches, PREG_OFFSET_CAPTURE);

			$authority = @$matches[4][0];
			if (strpos($authority, '@') !== false) {
				$authority = explode('@', $authority);
				@list($parts['user'], $parts['pass']) = explode(':', $authority[0]);
				$authority = $authority[1];
			}

			if (strpos($authority, ':') !== false) {
				$authority = explode(':', $authority);
				$parts['host'] = $authority[0];
				$parts['port'] = $authority[1];
			} else {
				$parts['host'] = $authority;
			}

			$parts['scheme'] = @$matches[2][0];
			$parts['path'] = @$matches[5][0];
			$parts['query'] = @$matches[7][0];
			$parts['fragment'] = @$matches[9][0];
		}
		else
		{
			$parts = @parse_url($uri);
		}
		return $parts;
	}
	
	function templateurl(){
		return Mage::getBaseDir('app')."/design/frontend/default/".$this->template;
	}

	function skinurl(){
		return Mage::getBaseUrl('skin')."frontend/default/".$this->template;
	}
	
	function isHomepage () {
		if (strpos(php_sapi_name(), 'cgi') !== false && !empty($_SERVER['REQUEST_URI'])) {
			//Apache CGI
			$_path =  rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		} else {
			//Others
			$_path =  rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
		}
		$uri = $_SERVER['REQUEST_URI'];
		if ($uri && $_path && strpos ($uri, $_path) === 0) {
			$uri = substr($uri, strlen ($_path));
		}
		$uri = strtolower($uri);
		if (in_array($uri, array('', '/', '/index.php','/index.php/', '/home', '/home/', '/default', '/default/', '/default/home', '/default/home/'))) return $uri;
		if (strpos($uri, '/home-')===0) return $uri;
		return FALSE;		
	}

	function windowversion() { //echo $_SERVER['HTTP_USER_AGENT']; die();
		preg_match('/Windows NT ([0-9]\.[0-9])/', $_SERVER['HTTP_USER_AGENT'], $reg);
		if(!isset($reg[1])) {
			return -1;
		} else {
			return floatval($reg[1]);
		}
	}

}

class ThemeParameter {
	var $_params;
	function ThemeParameter () {
		$this->_params = array();
	}
	function get ($key, $default = '') {
		return isset($this->_params[$key])?$this->_params[$key]:$default;
	}
	function set ($key, $value = '') {
		$this->_params[$key] = $value;
	}
}

