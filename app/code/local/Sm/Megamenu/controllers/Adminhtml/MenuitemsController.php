<?php
/*------------------------------------------------------------------------
 # SM Mega Menu - Version 1.1
 # Copyright (c) 2013 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Megamenu_Adminhtml_MenuitemsController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('megamenu/menuitems')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Menu Items Manager'), Mage::helper('adminhtml')->__('Menu Items Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('megamenu/menuitems')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('menuitems_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('megamenu/menuitems');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('megamenu/adminhtml_menuitems_edit'))
				->_addLeft($this->getLayout()->createBlock('megamenu/adminhtml_menuitems_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('megamenu')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		// Zend_Debug::dump(Mage::app()->getRequest()->getParams());die;
		if ($data = $this->getRequest()->getPost()) {
		// Zend_Debug::dump($data);die;	
	  		$data['title'] = Mage::helper('megamenu/filter')->getFilterData	($data['title'],'text');
			$data['description'] = Mage::helper('megamenu/filter')->getFilterData	($data['description'],'text');
			 // Zend_Debug::dump($data);die;	
	  		$model = Mage::getModel('megamenu/menuitems');		
			
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			if(!$this->getRequest()->getParam('id')){	//save item new 
				Mage::dispatchEvent('megamenu_menuitems_setItemLR_before',array('menuitems'=>$model));	//set item's Left, Right, Depth to $model, Observer->setItemLR
			}
			else{	//edit item  and insert item exists to other parent
				Mage::dispatchEvent('megamenu_menuitems_setItemLRWhileEdit_before',array('menuitems'=>$model));
			}
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				// Zend_Debug::dump($data);die;
				$model->save();
				if(!$this->getRequest()->getParam('id')){
					Mage::dispatchEvent('megamenu_menuitems_updateItemsLR_after',array('menuitems'=>$model));	//update other items' Left, Right, Observer->updateItemsLR 
				}
				else{	//edit item  and insert item exists to other parent
					Mage::dispatchEvent('megamenu_menuitems_setItemLRWhileEdit_after',array('menuitems'=>$model));	
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('megamenu')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('megamenu')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				// $model = Mage::getModel('megamenu/menuitems');
				// $model->setId($this->getRequest()->getParam('id'))
					// ->delete();
				$menuitem = Mage::getModel('megamenu/menuitems')->load($this->getRequest()->getParam('id'));
				if($menuitem->getId()){
					//action delete node
					Mage::dispatchEvent('megamenu_menuitems_deleteItemLR_before',array('menuitems'=>$menuitem));
				}						 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $megamenuIds = $this->getRequest()->getParam('menuitems_param');
        if(!is_array($megamenuIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($megamenuIds as $megamenuId) {
                    $menuitem = Mage::getModel('megamenu/menuitems')->load($megamenuId);
					if($menuitem->getId()){
						//action delete node
						Mage::dispatchEvent('megamenu_menuitems_deleteItemLR_before',array('menuitems'=>$menuitem));
					}
                    // $menuitem->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($megamenuIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $megamenuIds = $this->getRequest()->getParam('menuitems_param');
        if(!is_array($megamenuIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($megamenuIds as $megamenuId) {
                    $megamenu = Mage::getSingleton('megamenu/menuitems')
                        ->load($megamenuId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($megamenuIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'megamenu.csv';
        $content    = $this->getLayout()->createBlock('megamenu/adminhtml_menuitems_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'megamenu.xml';
        $content    = $this->getLayout()->createBlock('megamenu/adminhtml_megamenu_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}