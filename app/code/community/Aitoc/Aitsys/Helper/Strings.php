<?php
/**
 * @copyright  Copyright (c) 2009 AITOC, Inc.
 */
class Aitoc_Aitsys_Helper_Strings extends Aitoc_Aitsys_Helper_Data
{
    //install license notifications
    const INSTALL_MYSQL_TIMEOUT_NOTIFICATION = 'Due to the small value (%s) of "wait_timeout" parameter an automatic installation of the extensions can result in error. If after clicking "Proceed to install" you will get an error, you might try to increase the value of the parameter to a higher one in the MySQL configuration and then attempt to install the license once again. Or you may contact our support team asking them to configure the manual license for you instead. In case of contacting our support service make sure you have also indicated an URL to the administrative area.';
    const INSTALL_TEST_CONNECTION_FAILED = 'An established connection with the AITOC server "%s" is required in order to proceed with the installation of the extension. Please check your firewall and server configurations in order to allow outbound SSL queries to the given server.';
    const INSTALL_TEST_SERVER_ERROR = 'AITOC server "%s" is not available at the moment and the installation cannot be completed. Please try again in 10 minutes. Sorry for the inconvenience.';

    //errors
    const ER_ENT_HASH = 'The extension isn\'t compatible with this edition of Magento. Please contact us at sales@aitoc.com for further instructions. Please note that our working hours are Mon-Fri, 8am - 5pm UTC.';

    private $_errorStringDelimiter = "|||";

    public function getString($const, $translate = true, $args = array())
    {
        if (!is_array($args)) {
            $args = array($args);
        }

        $string = constant('self::'.$const);
        $string = $translate ? $this->__($string) : $string;

        array_unshift($args, $string);

        return call_user_func_array('sprintf', $args);
    }

    public function parseErrorString($error)
    {
        return explode($this->_errorStringDelimiter, $error);
    }

    public function getModuleConflictString($module)
    {
        if (($conflictClasses = $module->getConflictClasses()) && ($conflictModules = $module->getConflictModules())) {
            $conflictString = array();
            foreach($conflictClasses as $aitClass=>$otherClass)
            {
                $conflictString[] = $aitClass.': '.implode(', ', $otherClass);
            }
            $string = Mage::helper('aitsys')->__('Please <a href="%s">enable</a> our Automatic conflict resolution system under <a href="%s">Manage Class rewrites</a> by clicking the Enable button ',Mage::getModel('adminhtml/url')->getUrl('*/rewriter/enable'),Mage::getModel('adminhtml/url')->getUrl('*/rewriter') );
            $string .= '<span class="noBold">'.Mage::helper('aitsys')->__('to resolve the following issues: %s has a conflict with %s in the following classes %s. ', implode(', ', $conflictModules), $module->getInfo()->getLabel(),  implode(', ', $conflictString));
            $string .= '<br>'.Mage::helper('aitsys')->__('Or if you are familiar with web development you can edit the affected classes yourself manually.');
            $string .= '<br>'.Mage::helper('aitsys')->__('If none of the above helps please submit a <a href="%s">support ticket</a>', Mage::helper('aitsys')->getModuleSupportLink($module, true)).'</span>';
            return $string;
        }
        return false;
    }
}