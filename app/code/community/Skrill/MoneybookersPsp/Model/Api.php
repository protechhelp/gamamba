<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Skrill
 * @package    Skrill_MoneybookersPsp
 * @copyright  Copyright (c) 2012 Skrill Holdings Ltd. (http://www.skrill.com)
 */

class Skrill_MoneybookersPsp_Model_Api extends Varien_Object
{
    protected $_templatePath;
    protected $_xml;

    public function _construct()
    {
        $this->_templatePath = Mage::getModuleDir('etc', 'Skrill_MoneybookersPsp') . DS . 'requestTemplates' . DS ;
    }

     /**
     * Preauthorizes payment transaction
     *
     * @param array $params     transaction parameters
     * @return Varien_Object    transactionId and redirectUrl
     */
    public function preauthorize(Array $params)
    {
        $url = $this->getUrl();
        
        $this->_loadTemplate('general');
        $this->setData($params);
        $response = $this->_processRequest(
            $this->_getParsedXml(),
            $url
        );

        return $this->_processXmlResponse($response);
    }

    public function request($params)
    {
        $url = $this->getUrl();

        $this->_loadTemplate('general');
        $this->setData($params);
        $response = $this->_processRequest(
            $this->_getParsedXml(),
            $url
        );

        return $this->_processXmlResponse($response);
    }

    /**
     * Capture authorized payment
     *
     * @param array $params     capture parameters
     */
    public function capture(Array $params)
    {
        $url = $this->getUrl();
        
        $this->_loadTemplate('general');
        $this->setData($params);

        $response = $this->_processRequest(
            $this->_getParsedXml(),
            $url
        );

        $xml = @simplexml_load_string($response);
        if (!$xml) {
            Mage::throwException('moneybookerspsp transaction can not be captured. Error in XML response.');
        }

        return (string)$xml->Transaction['systemId'];
    }

    /**
     * CaptureCancel request
     *
     * @param array $params amount, currency and transaction id
     * @return string       transaction id
     */
    public function refund(Array $params)
    {
        $url = $this->getUrl();
        
        $this->_loadTemplate('general');
        $this->setData($params);

        $response = $this->_processRequest(
            $this->_getParsedXml(),
            $url
        );

        $xml = @simplexml_load_string($response);
        if (!$xml) {
            Mage::throwException('moneybookerspsp transaction can not be refunded. Error in XML response.');
        }

        return (string)$xml->ReferenceTransaction['systemId'];
    }

    /**
     * AuthorisationCancel requests
     *
     * @param string $transactionId
     * @return bool
     */
    public function void($transactionId)
    {
        $url = $this->getUrl('void');
        $this->_loadTemplate('void');
        $this->setData('transactionId', $transactionId);

        $response = $this->_processRequest(
            $this->_getParsedXml(),
            $url
        );

        $xml = @simplexml_load_string($response);
        if (!$xml) {
            Mage::throwException('moneybookerspsp transaction can not be canceled. Error in XML response.');
        }

        return true;
    }

    protected function _loadTemplate($fileName)
    {
        $fileName .= '.xml';
        if (!file_exists($this->_templatePath.$fileName)) {
            Mage::throwException('moneybookerspsp protocol file "'.$fileName.'" not found in "'.$this->_templatePath.'".');
        }
        $this->_xml = file_get_contents($this->_templatePath.$fileName);
    }

    protected function _getParsedXml()
    {
        if (isset($this->_xml)) {
            foreach ($this->getData() as $key => $value) {
                $this->_xml = str_replace('{{'.$key.'}}', htmlspecialchars($value), $this->_xml);
            }

            // remove empty placeholders
            $this->_xml = preg_replace('/\{\{[a-zA-Z0-9]+\}\}/', $value, $this->_xml);
        }

        return $this->_xml;
    }

    public function getUrl($type = 'xml')
    {
        $mode = $this->getConfigData('test_mode') ? 'test' : 'live';
        return (string)Mage::getConfig()->getNode("global/moneybookerspsp/urls/$type/$mode");
    }

    public function processWPFRequest($params)
    {
        $url = $this->getUrl('wpf');
        return $this->_processRequest($params, $url);
    }

    public function processRequest($url, $params = array(), $method = Zend_Http_Client::GET)
    {
        return $this->_processRequest($params, $url, $method);
    }

    protected function _processRequest($params, $url, $method = Zend_Http_Client::POST)
    {
        try {
            //$client = new Varien_Http_Client();
            $client = new Zend_Http_Client();
            if (!is_array($params)){
                $params = array('load' => $params);
            }
            $rawPostData = http_build_query($params, '', '&');
            $client->setUri($url)
                    ->setMethod($method)
                    ->setConfig(array('timeout'=>10))
                    //->setAuth($this->getConfigData('login'), $this->getConfigData('password'))
                    ->setHeaders(Zend_Http_Client::CONTENT_TYPE, 'application/x-www-form-urlencoded;charset=UTF-8')
                    //->setHeaders('Expect', '100-continue')
                    ->setHeaders('Accept-Charset', 'utf-8')
                    ->setParameterPost($params)
                    ->setRawData($rawPostData);

            $response = $client->request();

//            $response = $client->getAdapter()->read();
//             if (stripos($response, "HTTP/1.1 100 Continue\r\n\r\n") !== false) {
//                $response = str_ireplace("HTTP/1.1 100 Continue\r\n\r\n", '', $response);
//            }
//            $response = Zend_Http_Response::fromString($response);

            $responseBody = $response->getBody();
            //$responseBody = $response->getRawBody();

            if ($this->getConfigData('debug')) {
                Mage::log($url);
                Mage::log($params);
                Mage::log(print_r($response,1));
                Mage::log($responseBody);
            }

            if (empty($responseBody) || $response->getStatus() != 200){
                Mage::throwException(Mage::helper('moneybookerspsp')->__('moneybookerspsp API failure. The request has not been processed.'));
            }

        } catch (Exception $e) {
            Mage::logException($e);
            Mage::throwException(Mage::helper('moneybookerspsp')->__('moneybookerspsp API connection error. The request has not been processed.'));
        }

        return $responseBody;
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param   string $field
     * @return  mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->getStore();
        }
        $path = 'moneybookerspsp/settings/'.$field;
        
        if ((is_a($storeId, 'Mage_Core_Model_Store') ||
            is_a($storeId, 'Mage_Core_Model_Website'))
            && method_exists($storeId, 'getConfig'))
            return $storeId->getConfig($path);
        elseif (is_numeric($storeId))
            return Mage::getStoreConfig($path, $storeId);
        else
            //return Mage::getStoreConfig($path, $storeId);
            return (string) Mage::app()->getConfig()->getNode('default/' . $path);
    }

    /*
      Parse POST message returned by CTPE server.
    */
    public function parseWPFResponse($rawResult)
	{
		$rawResult=explode("&",$rawResult);
        $result = array();
		foreach($rawResult as $pair)
		{
			$pair=urldecode($pair);
			$pair=explode("=",$pair,2);
			$key=$pair[0];
			$value=$pair[1];
			$result[$key]=$value;
		}
		return $result;
    }

    public function generateHash($post)
    {
        $paramKeys = array(
            'PAYMENT.CODE',
            'IDENTIFICATION.TRANSACTIONID',
            'IDENTIFICATION.UNIQUEID',
            'PROCESSING.RETURN.CODE',
            'CLEARING.AMOUNT',
            'CLEARING.CURRENCY',
            'PROCESSING.RISK_SCORE',
            'TRANSACTION.MODE');
        $hash = array();
        foreach ($paramKeys as $key){
            $hash[$key] = isset($post[$key]) ? $post[$key] : '';
        }
        $hash[] = $this->_getApi()->getConfigData('secret');
        $hashString = implode('|',$hash);
        return md5($hashString);
    }

    public function processXmlResponse($response)
    {
        return $this->_processXmlResponse($response);
    }

    protected function _processXmlResponse($response)
    {
        $xml = @simplexml_load_string($response);
        if (!$xml) {
            Mage::throwException('moneybookerspsp transaction can not be initialized. Error in XML response.');
        }
        return $xml;
    }
}