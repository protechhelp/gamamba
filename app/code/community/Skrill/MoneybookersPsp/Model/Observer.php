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

 class Skrill_MoneybookersPsp_Model_Observer
 {
    public function hookOrderSaveBefore ($observer)
    {
        $event = $observer->getEvent();
        $order = $event->getOrder();
        $payment = $order->getPayment();
        
        $state = $order->getState();
        if ((!$state ||
            $state === Mage_Sales_Model_Order::STATE_NEW ||
            $state === Mage_Sales_Model_Order::STATE_PROCESSING)
            && !$payment->getCcTransId())
        {
            $order->setState(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW);
            $order->save();
            
            if ($order->hasInvoices())
            {
                $invoices = $order->getInvoiceCollection();
                foreach ($invoices as $invoice)
                {
                    if ($invoice->getState() === Mage_Sales_Model_Order_Invoice::STATE_PAID)
                    {
                        $invoice->setState(Mage_Sales_Model_Order_Invoice::STATE_OPEN);
                        $invoice->save();
                    }
                }
            }

            return $this;
        }
        
        if ($order->hasInvoices())
        {
            $invoices = $order->getInvoiceCollection();
            foreach ($invoices as $invoice)
            {
                if ($invoice->getState() === Mage_Sales_Model_Order_Invoice::STATE_OPEN)
                {
                    $order->setState(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW);
                    break;
                }
            }
        }

        return $this;
    }
 }
 
