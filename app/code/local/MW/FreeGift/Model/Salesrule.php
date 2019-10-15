<?php

class MW_FreeGift_Model_Salesrule extends Mage_Rule_Model_Rule
{
	public function _construct()
    {
        parent::_construct();
        $this->_init('freegift/salesrule');
    }
	
    public function getConditionsInstance()
    {
        return Mage::getModel('salesrule/rule_condition_combine');
    }
	
	/**
     * Initialize rule model data from array
     *
     * @param   array $rule
     * @return  Mage_SalesRule_Model_Rule
     */
    public function loadPost(array $rule)
    {
        $arr = $this->_convertFlatToRecursive($rule);
        if (isset($arr['conditions'])) {
            $this->getConditions()->setConditions(array())->loadArray($arr['conditions'][1]);
        }
        if (isset($arr['actions'])) {
            $this->getActions()->setActions(array())->loadArray($arr['actions'][1], 'actions');
        }
        if (isset($rule['store_labels'])) {
            $this->setStoreLabels($rule['store_labels']);
        }
        return $this;
    }
    
	/**
     * Save rule labels after rule save and process product attributes used in actions and conditions
     *
     * @return Mage_SalesRule_Model_Rule
     */
    protected function _afterSave()
    {
        //Saving attributes used in rule
        $ruleProductAttributes = $this->_getUsedAttributes($this->getConditionsSerialized());
        if (count($ruleProductAttributes)) {
            $this->getResource()->setActualProductAttributes($this, $ruleProductAttributes);
        }
        return parent::_afterSave();
    }
    
	/**
     * Return all product attributes used on serialized action or condition
     *
     * @param string $serializedString
     * @return array
     */
    protected function _getUsedAttributes($serializedString)
    {
        $result = array();
        if (preg_match_all('~s:32:"salesrule/rule_condition_product";s:9:"attribute";s:\d+:"(.*?)"~s',
            $serializedString, $matches)){
            foreach ($matches[1] as $offset => $attributeCode) {
                $result[] = $attributeCode;
            }
        }
        return $result;
    }
}