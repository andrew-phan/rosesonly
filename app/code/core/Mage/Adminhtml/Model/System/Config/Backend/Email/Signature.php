<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */class Mage_Adminhtml_Model_System_Config_Backend_Email_Signature extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        $value= $this->getValue();
        return $this;
    }
     public function afterLoad()
    {
        $value= $this->getValue();
        //$value= str_replace("</br>","\r\n",$value);
        $this->setValue($value);
        $this->_afterLoad();
    }
     public function save()
    {
        /**
         * Direct deleted items to delete method
         */
        if ($this->isDeleted()) {
            return $this->delete();
        }
        if (!$this->_hasModelChanged()) {
            return $this;
        }
        $this->_getResource()->beginTransaction();
        $dataCommited = false;
        try {
            $this->_beforeSave();
            if ($this->_dataSaveAllowed) {
                $value= $this->getValue();
                //$value= str_replace("\r\n","</br>",$value);
                $this->_getResource()->save($this->setValue($value));
                $this->_afterSave();
            }
            $this->_getResource()->addCommitCallback(array($this, 'afterCommitCallback'))
                ->commit();
            $this->_hasDataChanges = false;
            $dataCommited = true;
        } catch (Exception $e) {
            $this->_getResource()->rollBack();
            $this->_hasDataChanges = true;
            throw $e;
        }
        if ($dataCommited) {
            $this->_afterSaveCommit();
        }
        return $this;
    }
}

?>
