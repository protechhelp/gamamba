<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @copyright  Copyright (c) 2012 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * SEO Suite extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoSuite
 * @author     MageWorx Dev Team
 */

class MageWorx_SeoSuite_Model_Rdfa 
{
    private $_product;
    protected $_xmlns = array(
        "rdf"       =>"http://www.w3.org/1999/02/22-rdf-syntax-ns#",
        "rdfs"      =>"http://www.w3.org/2000/01/rdf-schema#",
        "xsd"       =>"http://www.w3.org/2001/XMLSchema#",
        "dc"        =>"http://purl.org/dc/elements/1.1/",
        "owl"       =>"http://www.w3.org/2002/07/owl#",
        "vcard"     =>"http://www.w3.org/2006/vcard/ns#",
        "gr"        =>"http://purl.org/goodrelations/v1#",
        "product"   =>"http://schema.org/Product",
        "v"         =>"http://rdf.data-vocabulary.org/#",
        "foaf"      =>"http://xmlns.com/foaf/0.1/",
        "media"     =>"http://search.yahoo.com/searchmonkey/media/"
    );
    
    protected $_paymentMethods = array (
		"byBankTransferInAdvance"   => "http://purl.org/goodrelations/v1#ByBankTransferInAdvance",
		"byInvoice"                 => "http://purl.org/goodrelations/v1#ByInvoice",
		"cash"                      => "http://purl.org/goodrelations/v1#Cash",
		"checkinadvance"            => "http://purl.org/goodrelations/v1#CheckInAdvance",
		"cod"                       => "http://purl.org/goodrelations/v1#COD",
		"directdebit"               => "http://purl.org/goodrelations/v1#DirectDebit",
		"googleCheckout"            => "http://purl.org/goodrelations/v1#GoogleCheckout",
		"paypal"                    => "http://purl.org/goodrelations/v1#PayPal",
		"AE"                        => "http://purl.org/goodrelations/v1#AmericanExpress",
		"DI"                        => "http://purl.org/goodrelations/v1#Discover",
		"JCB"                       => "http://purl.org/goodrelations/v1#JCB",
		"MC"                        => "http://purl.org/goodrelations/v1#MasterCard",
		"VI"                        => "http://purl.org/goodrelations/v1#VISA",
	);
    
    protected $_deliveryMethods = array(
                "dhl"               => "http://purl.org/goodrelations/v1#DHL",
                "ups"               => "http://purl.org/goodrelations/v1#UPS",
                "mail"              => "http://purl.org/goodrelations/v1#DeliveryModeMail",
                "fedex"             => "http://purl.org/goodrelations/v1#FederalExpress",
                "directdownload"    => "http://purl.org/goodrelations/v1#DeliveryModeDirectDownload",
                "pickup"            => "http://purl.org/goodrelations/v1#DeliveryModePickUp",
                "vendorfleet"       => "http://purl.org/goodrelations/v1#DeliveryModeOwnFleet",
                "freight"           => "http://purl.org/goodrelations/v1#DeliveryModeFreight"
    );
    
    protected $_grSructure = array( "gr:Offering" => array(
                    'tag'   => 'typeof',
                    'childs'=> array(
                            'gr:validFrom'   => array('tag'=>'property','datatype'=>'xsd:dateTime','format'=>'datetime'),
                            'gr:validThrough'=> array('tag'=>'property','datatype'=>'xsd:dateTime','format'=>'datetime'),
                            'gr:name'        => array('tag'=>'property','xml:lang'=>'','format'=>'language'),
                            'gr:description' => array('tag'=>'property','xml:lang'=>'','format'=>'language'),
                            "gr:hasPriceSpecification"  => array(
                                                    'tag'   => 'rel',
                                                    'childs'=> array(
                                                                'v:Offer'=>array(
                                                                    'tag'   =>'typeof',
                                                                    'childs'=>array(
                                                                            'v:price'   => array('tag'=>'property','datatype'=>'xsd:float','format'=>'float'),
                                                                            'v:currency'=> array('tag'=>'property','datatype'=>'xsd:string','format'=>'string'),
                                                                            'v:availability'=> array('tag'=>'property','datatype'=>'xsd:string','format'=>'string'),
                                                                            'v:offerurl'=> array('tag'=>'property','datatype'=>'xsd:string','format'=>'string'),
                                                                            ),
                                                                ),
                                                                'v:Product'=>array(
                                                                    'tag'   =>'typeof',
                                                                    'childs'=>array(
                                                                            'v:image'           => array('tag'=>'property','resource'),
                                                                            'v:name'            => array('tag'=>'property','xml:lang'=>'','format'=>'language'),
                                                                            'v:description'     => array('tag'=>'property','xml:lang'=>'','format'=>'language'),
                                                                            ),
                                                                ),
                                                                
                                                                'gr:UnitPriceSpecification' => array(
                                                                                                    'tag'   => 'typeof',
                                                                                                    'childs'=> array(
                                                                                                                    'gr:valueAddedTaxIncluded'  => array('tag'=>'property','datatype'=>'xsd:boolean','format'=>'boolean'),
                                                                                                                    'gr:getPrice'               => array('tag'=>'property','datatype'=>'xsd:float','format'=>'float'),
                                                                                                                    'gr:hasCurrency'            => array('tag'=>'property','datatype'=>'xsd:string','format'=>'string'),
                                                                                                                )
                                                                                                )
                                                                )
                                                    ),
                            "gr:includesObject"         => array(
                                                           'tag'   => 'rel',
                                                           'childs'=> array(
                                                                       'gr:TypeAndQuantityNode' => array(
                                                                                                           'tag'   => 'typeof',
                                                                                                           'childs'=> array(
                                                                                                                       'gr:amountOfThisGood'   => array('tag'=>'property','datatype'=>'xsd:boolean','format'=>'float'),
                                                                                                                       'gr:typeOfGood'         => array('tag'=>'rel',
                                                                                                                                                        'childs'  => array(
                                                                                                                                                                        'gr:SomeItems'  => array(
                                                                                                                                                                                            'tag'   => 'typeof',
                                                                                                                                                                                            'childs'=> array(
                                                                                                                                                                                                            'gr:name'               => array('tag'=>'property','xml:lang'=>'','format'=>'language'),
                                                                                                                                                                                                            'gr:description'        => array('tag'=>'property','xml:lang'=>'','format'=>'language'),
                                                                                                                                                                                                            'gr:hasStockKeepingUnit'=> array('tag'=>'property','datatype'=>'xsd:string','format'=>'string'),
                                                                                                                                                                                                            'gr:color'              => array('tag'=>'property','xml:lang'=>'','format'=>'language'),
                                                                                                                                                                                                            'gr:hasManufacturer'    => array('tag'=>'rel','xml:lang'=>'','format'=>'language'),
                                                                                                                                                                                                            'gr:category'           => array('tag'=>'property','xml:lang'=>'','format'=>'language'),
                                                                                                                                                                                                            'foaf:depiction'        => array('tag'=>'rel','resource'),
                                                                                                                                                                                                        )
                                                                                                                                                                                            )
                                                                                                                                                                        )
                                                                                                                                                       )
                                                                                                                       )
                                                                                                       )
                                                                       )
                                                           ),
                           "gr:acceptedPaymentMethods"   => array(
                                                           'tag'   => 'rel',
                                                           'resource'=> true,
                                                           ),
                           "gr:availableDeliveryMethods" => array(
                                                           'tag'   => 'rel',
                                                           'resource'=> true,
                                                           ),
                           "gr:hasInventoryLevel"        => array(
                                    'tag'   => 'rel',
                                    'childs'=> array(
                                        'gr:QuantitativeValue' => array(
                                                                    'tag'   => 'typeof',
                                                                    'childs'=> array(
                                                                                'gr:hasMinValue'  => array('tag'=>'property','datatype'=>'xsd:float','format'=>'float'),
                                                                                'gr:hasMaxValue'  => array('tag'=>'property','datatype'=>'xsd:float','format'=>'float'),
                                                                                 )
                                                                    ),
                                        'foaf:maker'    => array('tag'=>'rel','resource'=>"urn:mageworx:seosuite_ultimate:v2.0.0.0"),
                                        'v:hasReview'   => array(
                                                    'tag'   =>'rel',
                                                    'childs'=>array(
                                                            'v:Review-aggregate'=>array(
                                                                    'tag'   =>'typeof',
                                                                    'childs'=>array(
                                                                            'v:rating' => array('tag'=>'property','datatype'=>'xsd:string','format'=>'string'),
                                                                            'v:count' => array('tag'=>'property','datatype'=>'xsd:string','format'=>'string'),
                                                                            ),
                                                                    ),
                                                            ),
                                                    ),                                                                                                                                                    


                                            ),
                                    ),
                            ),
                    ),
                                    
                             
                );

    public function getCcAvailableTypes($method)
    {
        $types = Mage::getSingleton('payment/config')->getCcTypes();
        if ($method) {
            $availableTypes = $method->getConfigData('cctypes');
            if ($availableTypes) {
                $availableTypes = explode(',', $availableTypes);
                foreach ($types as $code=>$name) {
                    if (!in_array($code, $availableTypes)) {
                        unset($types[$code]);
                    }
                }
            }
        }
        return $types;
    }
    
    public function getXmlns($part=false)
    {
        if($part) {
            if(isset($this->_xmlns[$part])) {
                return $this->_xmlns[$part];
            }
            return false;
        }
        return $this->_xmlns;
    }
    
    public function getStructure()
    {
        return $this->_grSructure;
    }
    
    public function getContent()
    {
        if (!Mage::getStoreConfig('mageworx_seo/seosuite/enable_rich_snippets')) return '';
        $this->_product = Mage::registry('current_product');
        
        $html = '<div xmlns="http://www.w3.org/1999/xhtml" ';
        foreach ($this->getXmlns() as $key=>$value) {
            $html .= "xmlns:".$key."='".$value."' ";
        }
        $html .= ">";
        $html .= $this->_generateContent();
        $html .= '</div>';
        return $html;
    }
    
    private function _generateContent($data = false)
    {
        $html = '';
        if(!$data)
        {
            $data = $this->getStructure();
        }
        
        foreach ($data as $key=>$item)
        {
            $html .= $this->_generate($key,$item);
        }
        return $html;
    }
    
    private function acceptedPaymentMethods()
    {
        $payments = Mage::getSingleton('payment/config')->getActiveMethods();
        $html = array();
        $methods = array();
        foreach ($payments as $paymentCode=>$paymentModel) {
            if(!isset($methods[$paymentCode])) {
                $methods[$paymentCode] = array();
            }
            if($paymentModel->canUseCheckout()==1) {
                $methods[$paymentCode]['label'] = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
                $methods[$paymentCode]['code'] = $paymentCode;
                if($paymentCode=='ccsave') {
                    $methods[$paymentCode]['cc'] = $this->getCcAvailableTypes($paymentModel);
                }
            }
            if(isset($methods[$paymentCode]['code'])) {
                switch ($methods[$paymentCode]['code']) {
                    case "ccsave":
                        foreach ($methods['ccsave']['cc'] as $cc) {
                            if(in_array($cc,array('AE','VI','MC','DI','JCB'))) {
                                $html[$cc] ="<div rel='gr:acceptedPaymentMethods' resource='".$this->_paymentMethods[$cc]."'></div>";
                            }
                        }
                        break;
                    case "checkmo":
                        $html['checkinadvance'] ="<div rel='gr:acceptedPaymentMethods' resource='".$this->_paymentMethods['checkinadvance']."'></div>";
                        $html['cash'] ="<div rel='gr:acceptedPaymentMethods' resource='".$this->_paymentMethods['cash']."'></div>";
                        break;
                    case "purchaseorder":
                        $html['byInvoice'] ="<div rel='gr:acceptedPaymentMethods' resource='".$this->_paymentMethods['byInvoice']."'></div>";
                        break;
                    case "banktransfer":
                        $html['byBankTransferInAdvance'] ="<div rel='gr:acceptedPaymentMethods' resource='".$this->_paymentMethods['byBankTransferInAdvance']."'></div>";
                        break;
                    case "cashondelivery":
                        $html['cod'] ="<div rel='gr:acceptedPaymentMethods' resource='".$this->_paymentMethods['cod']."'></div>";
                        break;

                    case "paypaluk_express":
                    case "paypaluk_direct":
                    case "paypal_direct":
                    case "payflow_link":
                    case "verisign":
                    case "payflow_advanced":
                    case "paypal_standard":
                    case "paypal_express":
                        $html['paypal'] ="<div rel='gr:acceptedPaymentMethods' resource='".$this->_paymentMethods['paypal']."'></div>";
                        break;
                    case "free":
                    case "authorizenet":
                        $sCC = Mage::getStoreConfig('payment/authorizenet/cctypes');
                        $aCC = explode(',',$sCC);
                        foreach($aCC as $cc) {
                            if(in_array($cc,array('AE','VI','MC','DI','JCB'))) {
                                $html[$cc] ="<div rel='gr:acceptedPaymentMethods' resource='".$this->_paymentMethods[$cc]."'></div>";
                            }
                        }
                        break;
                    case "authorizenet_directpost":
                            $sCC = Mage::getStoreConfig('payment/authorizenet_directpost/cctypes');
                            $aCC = explode(',',$sCC);
                            foreach($aCC as $cc) {
                                    if(in_array($cc,array('AE','VI','MC','DI','JCB'))) {
                                        $html[$cc] ="<div rel='gr:acceptedPaymentMethods' resource='".$this->_paymentMethods[$cc]."'></div>";
                                    }
                            }
                            break;
                    default :
                        break;
                }
            }
        }
        return join("\n",$html);
    }
    
    private function availableDeliveryMethods()
    {
        $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
        $options = array();
        $html =array();
        foreach($methods as $_code => $_method)
        {
            switch ($_code) {
                case "dhlint":
                    $html['dhl'] ="<div rel='gr:availableDeliveryMethods' resource='".$this->_deliveryMethods['dhl']."'></div>";
                    break;
                case "ups":
                    $html['ups'] ="<div rel='gr:availableDeliveryMethods' resource='".$this->_deliveryMethods['ups']."'></div>";
                    break;
                case "fedex":
                    $html['fedex'] ="<div rel='gr:availableDeliveryMethods' resource='".$this->_deliveryMethods['fedex']."'></div>";
                    break;
                case "usps":
                case "tablerate":
                case "freeshipping":
                case "flatrate":
                default :
                    $html['freight'] ="<div rel='gr:availableDeliveryMethods' resource='".$this->_deliveryMethods['freight']."'></div>";
            }
        }
        return join("\n",$html);
    }
    
    private function isExclTax()
    {
        $product = $this->_product;
        if ($product->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC &&
            $product->getPriceModel()->getIsPricesCalculatedByIndex() !== false) {
            return false;
        }
        return Mage::helper('tax')->displayBothPrices();
    }
    
    private function getPrice()
    {
        $product = $this->_product;
        $_priceModel  = $product->getPriceModel();
        if($product->getTypeInstance(true) instanceof Mage_Bundle_Model_Product_Type) {
            list($_minimalPriceTax, $_maximalPriceTax) = $_priceModel->getTotalPrices($product, null, null, false);
            list($_minimalPriceInclTax, $_maximalPriceInclTax) = $_priceModel->getTotalPrices($product, null, true, false);
            $min = $_minimalPriceTax;
            $max = $_maximalPriceTax;
            if(!$this->isExclTax()) {
                $min = $_minimalPriceInclTax;
                $max = $_maximalPriceInclTax;
            }
            $min = Mage::helper('core')->currency(number_format($min,2),false,false);
            $max = Mage::helper('core')->currency(number_format($max,2),false,false);
            $html = "<div property='gr:hasMinCurrencyValue' content='".round($min,2)."' datatype='xsd:float'></div>";
            $html .= "<div property='gr:hasMaxCurrencyValue' content='".round($max,2)."' datatype='xsd:float'></div>";
        }
        else {
            $_taxHelper  = Mage::helper('tax');
            $_finalPrice = $_taxHelper->getPrice($product, $product->getFinalPrice());
            $_finalPriceInclTax = $_taxHelper->getPrice($product, $product->getFinalPrice(), true);
            $price = $_finalPriceInclTax;
            if ($_taxHelper->displayBothPrices()) {
                $price = $_finalPrice;
            }
            $price = Mage::helper('core')->currency(number_format($price,2),false,false);
            $html = "<div property='gr:hasCurrencyValue' content='".round($price,2)."' datatype='xsd:float'></div>";
        }
        return $html;
    }
    
    private function _generate($key,$item)
    {
        $exceptedItems = array('gr:acceptedPaymentMethods','gr:availableDeliveryMethods','gr:getPrice');
        if(in_array($key, $exceptedItems))
        {
            list($type,$sKey) = explode(":", $key);
            return $this->$sKey($key,$item);
        }
        $content = '';
        switch ($item['tag']) {
            case 'rel':
                 if(isset($item['resource']) && $item['resource'])
                 {
                    $resource = 'http://purl.org/goodrelations/v1';
                    if($item['resource']!==true) {
                        $resource = $item['resource'];
                    }
                    $content = "resource='$resource'";
                 }
                 break;
            case 'typeof':
                list($type,$sKey) = explode(":", $key);
                $url = explode('?',Mage::getModel('core/url')->getUrl('*/*/*', array('_current' => true, '_use_rewrite' => true))); //fix
                $content = "about='" . $url[0] . "#".$sKey."_".$this->_product->getId()."'";
                //$content = "about='" . Mage::getModel('core/url')->getUrl('*/*/*', array('_current' => true, '_use_rewrite' => true)) . "#".$sKey."_".$this->_product->getId()."'";
                break;
            
            default :
                try {
                $content = "content=\"".$this->_getItemValue($key)."\""; 
                } catch (Exception $e) {
               //     echo $key; exit;
                }
                if(isset($item['datatype'])) {
                    $content .= " datatype='".$item['datatype']."'";
                }
                if(isset($item['xml:lang'])) {
                    $content .= " xml:lang='en'";
                }
        }
        
        $html = "<div ".$item['tag']."='".$key."' ".$content.">";
        if(isset($item['childs'])) {
            $html .= "\n".$this->_generateContent($item['childs']);
        }
        $html .= "</div>\n";
        return $html;
    }
    
    private function _getItemValue($key)
    {
        
        $product = $this->_product;
        $ratingData = Mage::getModel('review/review_summary')->setStoreId(Mage::app()->getStore()->getId())->load($product->getId());
        list($type,$sKey) = explode(":", $key);
        switch ($sKey) {
            case 'validFrom':
                $value = date('Y-m-d h:i:s', time());
                break;
            case 'validThrough':
                $value = date('Y-m-d h:i:s', time() + 31536000);
                break;
            case 'name':
                $value = $product->getName();
                break;
            case 'description':
                $value = $product->getDescription();
                $value = strip_tags($value);
                //$value = addslashes($value);
                break;
            case 'valueAddedTaxIncluded':
                $value = 'true';
                break;
            case 'price':
            case 'hasCurrencyValue':
                $value = (float)$product->getFinalPrice();
                if(!$value) {
                    $value = (float)$product->getPrice();
                }
                $value = Mage::helper('core')->currency(number_format($value,2),false,false);
                break;
            case 'currency':
            case 'hasCurrency':
                $value = Mage::app()->getStore()->getCurrentCurrencyCode();
                $value = strtoupper($value);
                break;
            case 'amountOfThisGood':
                $value = 1.0;
                break;
            case 'hasStockKeepingUnit':
                $value = strip_tags($product->getSku());
                break;
            case 'color':
                $value = $product->getData('color');
                break;
            case 'hasManufacturer':
                $value = $product->getData('manufacturer');
                if(!$value) {
                    $value = $product->getData('brand');
                }
                break;
            case 'category':
                $value = Mage::helper('catalog')->getBreadcrumbPath();
                if(is_array($value)){
                    $newVal = array();
                    foreach($value as $k => $cat){
                        if($k != 'product'){
                            $newVal[] = $cat['label'];
                        }
                    }
                    $value = join(' ', $newVal);
                }
                break;
            case 'image':
            case 'depiction':
                $value = $product->getImageUrl();
                break;
            case 'availability':
                $value = (float)$product->getStockItem()->getMinSaleQty() ?(float)$product->getStockItem()->getMinSaleQty():$product->isInStock()?Mage::helper('seosuite')->__('In stock'):Mage::helper('seosuite')->__('Out of stock');
                break;
            case 'hasMinValue':
                $value = (float)$product->getStockItem()->getMinSaleQty() ?(float)$product->getStockItem()->getMinSaleQty():$product->isInStock()?1:0;
                break;
            case 'hasMaxValue':
                $value = (float)$product->getStockItem()->getQty();
                break;
            case 'offerurl':
                $value = Mage::helper('core/url')->getCurrentUrl();
                break;
            case 'rating':
                
                $value = (float)$ratingData['rating_summary']/10/2;
                if(!$value)
                {
                    $value = 1;
                }
                break;
            case 'count':
                $value = $ratingData['reviews_count'];
                break;
        }
        $value = str_replace(array("\n","\r","\n\r"), ' ', $value);
        $value = str_replace('"', "'", $value);
        return $value;
    }
    
    
}