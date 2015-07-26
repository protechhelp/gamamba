<?php

class Magestore_Customercredit_Helper_Payment extends Mage_Payment_Helper_Data {

    public function getStoreMethods($store = null, $quote = null) {
        $res = array();
		if($quote !== null)
		{
			foreach ($this->getPaymentMethods($store) as $code => $methodConfig) {
				if ($quote->getGrandTotal() == 0) {
					if ($code == 'free') {
						$prefix = parent::XML_PATH_PAYMENT_METHODS . '/' . $code . '/';
						if (!$model = Mage::getStoreConfig($prefix . 'model', $store)) {
							continue;
						}
						$methodInstance = Mage::getModel($model);
						if (!$methodInstance) {
							continue;
						}
						$methodInstance->setStore($store);
						if (!$methodInstance->isAvailable($quote)) {
							/* if the payment method cannot be used at this time */
							continue;
						}
						$sortOrder = (int) $methodInstance->getConfigData('sort_order', $store);
						$methodInstance->setSortOrder($sortOrder);
						$res[] = $methodInstance;
					}
				} else {
					$prefix = parent::XML_PATH_PAYMENT_METHODS . '/' . $code . '/';
					if (!$model = Mage::getStoreConfig($prefix . 'model', $store)) {
						continue;
					}
					$methodInstance = Mage::getModel($model);
					if (!$methodInstance) {
						continue;
					}
					$methodInstance->setStore($store);
					if (!$methodInstance->isAvailable($quote)) {
						/* if the payment method cannot be used at this time */
						continue;
					}
					$sortOrder = (int) $methodInstance->getConfigData('sort_order', $store);
					$methodInstance->setSortOrder($sortOrder);
					$res[] = $methodInstance;
				}
			}
        } 
        usort($res, array($this, '_sortMethods'));
        return $res;   
    }

}