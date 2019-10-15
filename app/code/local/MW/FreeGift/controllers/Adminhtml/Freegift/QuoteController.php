<?php
class MW_FreeGift_Adminhtml_Freegift_QuoteController extends Mage_Adminhtml_Controller_Action
{
    protected function _initRule()
    {
        $this->_title($this->__('Promotions'))->_title($this->__('Shopping Cart Free Gift'));

        Mage::register('current_promo_quote_rule', Mage::getModel('salesrule/rule'));
        if ($id = (int) $this->getRequest()->getParam('id')) {
            Mage::registry('current_promo_quote_rule')
                ->load($id);
        }
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('mageworld/freegift/quote')
            ->_addBreadcrumb(Mage::helper('salesrule')->__('Free Gift'), Mage::helper('salesrule')->__('Free Gift'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Promotions'))->_title($this->__('Shopping Cart Free Gift Rules'));

        $this->_initAction()
            ->_addBreadcrumb(Mage::helper('salesrule')->__('Free Gift'), Mage::helper('salesrule')->__('Free Gift'))
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('freegift/salesrule');

        if ($id) {
            $model->load($id);
            if (! $model->getRuleId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('salesrule')->__('This rule no longer exists.'));
                $this->_redirect('*/*');
                return;
            }
        }

        $this->_title($model->getRuleId() ? $model->getName() : $this->__('New Rule'));

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');
        $model->getActions()->setJsFormObject('rule_actions_fieldset');

        Mage::register('current_freegift_quote_rule', $model);

        $this->_initAction()->getLayout()->getBlock('freegift_quote_edit')
             ->setData('action', $this->getUrl('*/*/save'));

        $this
            ->_addBreadcrumb($id ? Mage::helper('salesrule')->__('Edit Rule') : Mage::helper('salesrule')->__('New Rule'), $id ? Mage::helper('salesrule')->__('Edit Rule') : Mage::helper('salesrule')->__('New Rule'))
            ->renderLayout();

    }

    /**
     * Promo quote save action
     *
     */
    public function saveAction()
    {
    	if ($this->getRequest()->getPost()) {
            try {
                $model = Mage::getModel('freegift/salesrule');
                $data = $this->getRequest()->getPost();

                $data = $this->_filterDates($data, array('from_date', 'to_date'));
                
                $tmp = array();
                foreach(explode('&',$data['product_ids_tmp']) as $value){
                	$_value = explode('=', $value);
                	$tmp[] = $_value[0];
                }
				$data['gift_product_ids'] = implode(',', $tmp);
				
                $id = $this->getRequest()->getParam('rule_id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        Mage::throwException(Mage::helper('salesrule')->__('Wrong rule specified.'));
                    }
                }
				
                if($data['coupon_code']){
                	$testSalesRule = Mage::getModel('freegift/salesrule')->load($data['coupon_code'],'coupon_code');
                	if($testSalesRule->getId() && ($testSalesRule->getId() !=$id)){
                		Mage::throwException(Mage::helper('salesrule')->__('The coupon code is not available.'));
                	}
                }
                $session = Mage::getSingleton('adminhtml/session');

                $validateResult = $model->validateData(new Varien_Object($data));
                if ($validateResult !== true) {
                    foreach($validateResult as $errorMessage) {
                        $session->addError($errorMessage);
                    }
                    $session->setPageData($data);
                    $this->_redirect('*/*/edit', array('id'=>$model->getId()));
                    return;
                }

                if (isset($data['simple_action']) && $data['simple_action'] == 'by_percent' && isset($data['discount_amount'])) {
                    $data['discount_amount'] = min(100,$data['discount_amount']);
                }
                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }
                if (isset($data['rule']['actions'])) {
                    $data['actions'] = $data['rule']['actions'];
                }
                unset($data['rule']);
                if(!$data['from_date']) $data['from_date'] = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
                $model->loadPost($data);
                $model->setData('from_date',$data['from_date']);
                $model->setData('to_date',$data['to_date']);
                $session->setPageData($model->getData());
				
                $model->save();
				
				// Fix: can't save customer_group_ids and website_ids
				$customer_group_ids = "";
				$website_ids = "";
				if(isset($data["customer_group_ids"])){
					$customer_group_ids = implode(",",$data["customer_group_ids"]);
				}
				if(isset($data["website_ids"])){
					$website_ids = implode(",",$data["website_ids"]);
				}
				
				$conn = Mage::getModel('core/resource')->getConnection('core_write');
				$resource = Mage::getModel('freegift/salesrule')->getCollection();
				$sql = "UPDATE `{$resource->getTable('salesrule')}` SET `website_ids`='{$website_ids}',`customer_group_ids`='{$customer_group_ids}' WHERE rule_id='{$model->getId()}'";
				$conn->query($sql);
				
                $session->addSuccess(Mage::helper('salesrule')->__('The rule has been saved.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('catalogrule')->__('An error occurred while saving the rule data. Please review the log and try again.'));
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                 $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('rule_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('freegift/salesrule');
                $model->load($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('salesrule')->__('The rule has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('catalogrule')->__('An error occurred while deleting the rule. Please review the log and try again.'));
                Mage::logException($e);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('salesrule')->__('Unable to find a rule to delete.'));
        $this->_redirect('*/*/');
    }


    public function gridAction()
    {
        $this->_initRule()->loadLayout()->renderLayout();
    }

    /**
     * Chooser source action
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $chooserBlock = $this->getLayout()->createBlock('adminhtml/promo_widget_chooser', '', array(
            'id' => $uniqId
        ));
        $this->getResponse()->setBody($chooserBlock->toHtml());
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('promo/quote');
    }
    
    public function freeproductAction()
    {
    	$id = $this->getRequest()->getParam('id');
    	$model = Mage::getModel('freegift/salesrule')->load($id);
    	$freegifts = $this->getRequest()->getParam('freegifts');
    	Mage::register('current_freegift_quote_rule', $model);
    	$block = $this->getLayout()->createBlock('freegift/adminhtml_quote_edit_tab_actions_freegift','freegift_product_grid')->setFreeGifts($freegifts);
    	$this->getResponse()->setBody($block->toHtml());
    }
}