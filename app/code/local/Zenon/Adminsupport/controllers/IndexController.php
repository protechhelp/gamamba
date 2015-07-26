<?php
require_once 'AW/Helpdesk3/controllers/CustomerController.php';
class Zenon_Adminsupport_IndexController extends AW_Helpdesk3_CustomerController{
    public function IndexAction() {
      
	  $this->loadLayout();   
	  $this->getLayout()->getBlock("head")->setTitle($this->__("Help Desk"));
	        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
      $breadcrumbs->addCrumb("home", array(
                "label" => $this->__("Home Page"),
                "title" => $this->__("Home Page"),
                "link"  => Mage::getBaseUrl()
		   ));

      $breadcrumbs->addCrumb("help desk", array(
                "label" => $this->__("Help Desk"),
                "title" => $this->__("Help Desk")
		   ));

      $this->renderLayout(); 
	  
    }
	public function AddticketAction(){
	      $session = Mage::getSingleton('customer/session', array('name'=>'frontend'));
	      if ($formData = array_filter($this->getRequest()->getPost())) {
            $ticket = Mage::getModel('aw_hdu3/ticket');
			if($session->isLoggedIn()){
               $customer = Mage::getSingleton('customer/session')->getCustomer();
			   $cusemail = $customer->getEmail();
			   $cusfname = $customer->getFirstname();
			   $custlname = $customer->getLastname();
			   $custname = $customer->getFirstname() . ' ' . $customer->getLastname();
            }
			else{
			   $cusemail = $formData['email'];
			}
			$priorityId = AW_Helpdesk3_Model_Source_Ticket_Priority::TODO_VALUE;
            $orderId = $this->getRequest()->getParam('order', null);
            $orderIncrementId = null;
            if ($orderId) {
               // $order = Mage::getModel('sales/order')->load($orderId);
                $orderIncrementId = $orderId ;
            }
            if (array_key_exists('priority', $formData) && !empty($formData['priority'])) {
                $priorityId = $formData['priority'];
            }
            $departmentId = null;
            if (Mage::helper('aw_hdu3/config')->getDefaultDepartmentId()) {
                $departmentId = Mage::helper('aw_hdu3/config')->getDefaultDepartmentId();
            }
            if (array_key_exists('department', $formData) && !empty($formData['department'])) {
                $departmentId = $formData['department'];
            }
            try {
                $department = Mage::getModel('aw_hdu3/department')->load($departmentId);
                if (null === $department->getId()) {
                    $departmentCollection = Mage::getModel('aw_hdu3/department')->getCollection();
                    $departmentCollection
                        ->sortByOrder()
                        ->addFilterByStoreId(Mage::app()->getStore()->getId())
                        ->addActiveFilter()
                    ;
                    $department = $departmentCollection->getFirstItem();
                }
                $attachments = $this->_getAttachments();
                $ticket
                    ->setDepartmentId($department->getId())
                    ->setDepartmentAgentId($department->getPrimaryAgentId())
                    ->setCustomerEmail($cusemail)
                    ->setOrderIncrementId($orderIncrementId)
                    ->setCustomerName($custname)
                    ->setStatus(AW_Helpdesk3_Model_Source_Ticket_Status::NEW_VALUE)
                    ->setPriority($priorityId)
                    ->setStoreId(Mage::app()->getStore()->getId())
					->setKey($formData['key'])
                    ->setSubject($formData['subjectto'])
                    ->save()
                ;
                $ticket->addHistory(AW_Helpdesk3_Model_Ticket_History_Event_Message::TYPE,
                    array(
                        'content'     => $formData['message'],
                        'attachments' => $attachments
                    )
                );
                	 $data ='ok';
            } catch(Exception $e) {
                Mage::getSingleton('core/session')->addError($this->__($e->getMessage()));
            }
        }
	 $data ='ok';
	 echo json_encode($data);  
	 
	}
	    protected function _getAttachments()
    {
        $attachmentNeeded = $this->getRequest()->getParam('attachment_needed', null);
        $attachments = array();
        if (!array_key_exists('attachments', $_FILES) || empty($_FILES['attachments']['tmp_name'])) {
            return $attachments;
        }
        if (!Mage::helper('aw_hdu3/config')->isAllowCustomerToAttachFilesOnFrontend()) {
            throw new Exception('Attachments are not allowed');
        }
        foreach ($_FILES['attachments']['tmp_name'] as $key => $tmpName) {
            if (!$attachmentNeeded || !array_key_exists($key, $attachmentNeeded)) {
                continue;
            }
            if (Mage::helper('aw_hdu3')->validateAttach($_FILES['attachments']['name'][$key],
                file_get_contents($_FILES['attachments']['tmp_name'][$key]))
            ) {
                $attach = Mage::getModel('aw_hdu3/ticket_history_attachment');
                $attach->setFile($_FILES['attachments']['name'][$key], file_get_contents($_FILES['attachments']['tmp_name'][$key]));
                $attachments[] = $attach;
            }
        }
        return $attachments;
    }
	
	
}