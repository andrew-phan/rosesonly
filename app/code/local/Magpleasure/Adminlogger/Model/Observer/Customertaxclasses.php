<?php
/**
 * Magpleasure Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magpleasure.com/LICENSE.txt
 *
 * @category   Magpleasure
 * @package    Magpleasure_Adminlogger
 * @copyright  Copyright (c) 2012 Magpleasure Ltd. (http://www.magpleasure.com)
 * @license    http://www.magpleasure.com/LICENSE.txt
 */
class Magpleasure_Adminlogger_Model_Observer_Customertaxclasses extends Magpleasure_Adminlogger_Model_Observer
{

    public function CustomerTaxClassesLoad($event)
    {
        $this->createLogRecord(
            $this->getActionGroup('customertaxclasses')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Customertaxclasses::ACTION_CUSTOMER_TAX_CLASSES_LOAD,
            Mage::app()->getRequest()->getParam('id')
        );
    }

    public function CustomerTaxClassesSave($event)
    {
        if (Mage::app()->getRequest()->getPost('class_type') == Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER) {
            $taxClass = $event->getObject();

            $log = $this->createLogRecord(
                $this->getActionGroup('customertaxclasses')->getValue(),
                Magpleasure_Adminlogger_Model_Actiongroup_Customertaxclasses::ACTION_CUSTOMER_TAX_CLASSES_SAVE,
                $taxClass->getId()
            );

            $saveData = $taxClass->getData();
            $id = Mage::app()->getRequest()->getPost('class_id');
            if (!$taxClass->getOrigData() && $id) {
                $taxClass->load($id);
            }
            $taxClass->addData($saveData);
            if ($log){
                $log->addDetails(
                    $this->_helper()->getCompare()->diff($taxClass->getData(), $taxClass->getOrigData())
                );
            }
        }
    }

    public function CustomerTaxClassesDelete($event)
    {
        $this->createLogRecord(
            $this->getActionGroup('customertaxclasses')->getValue(),
            Magpleasure_Adminlogger_Model_Actiongroup_Customertaxclasses::ACTION_CUSTOMER_TAX_CLASSES_DELETE
        );
    }
}