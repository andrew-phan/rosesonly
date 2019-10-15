<?php
/*
 *  Created on Aug 30, 2012
 *  Author Ivan Proskuryakov - volgodark@gmail.com - Magazento.com
 *  Copyright Proskuryakov Ivan. Magazento.com © 2011. All Rights Reserved.
 *  Single Use, Limited Licence and Single Use No Resale Licence ["Single Use"]
 */
?>
<?php
class Magazento_Pdfinvoice_Model_Data extends Mage_Core_Model_Abstract
{
    
    public function getSettings($node)
    {
        $return = array();
        foreach (Mage::getStoreConfig("pdfinvoice/$node") as $k => $value) {
            
            
            $return[$node][$k] = Mage::getStoreConfig("pdfinvoice/$node/$k");
        }
        return $return;
    }
    public function saveSettings($node, $data)
    {
        foreach ($data as $k => $value) {
//            var_dump("pdfinvoice/$node/$k". ' > ' .$value );
            Mage::getModel('core/config')->saveConfig("pdfinvoice/$node/$k", $value );
        }
//        exit();
    }

}
