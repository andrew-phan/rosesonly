<?php
/**
 * Copyright (C) 2011 MageMods.co <sales@magemods.co>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

class MageMods_MageMods_Block_Adminhtml_System_Config_Form_Fieldset_Extensionfeed
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);

        if ($modules = unserialize(Mage::app()->loadCache(MageMods_MageMods_Model_Feed_Extension::FEEDCACHE))) {
            foreach ($modules as $data) {
                $html .= $this->_getFieldHtml($element, $data);
            }
        }

        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    protected function _getFieldRenderer()
    {
        if (empty($this->_fieldRenderer)) {
            $this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
        }
        return $this->_fieldRenderer;
    }

    protected function _getFieldHtml($fieldset, $data)
    {
        $icon = $alt = '';

        if (!Mage::getConfig()->getNode('modules/' . $data['code'])) {
            $icon = $this->getSkinUrl('images/error_msg_icon.gif');
            $alt  = Mage::helper('magemods')->__('Not Installed');
        } elseif (version_compare(Mage::getConfig()->getNode('modules/' . $data['code'] . '/version'), $data['version'], '<') === true) {
            $icon = $this->getSkinUrl('images/warning_msg_icon.gif');
            $alt  = Mage::helper('magemods')->__('Update Available');
        } else {
            $icon = $this->getSkinUrl('images/success_msg_icon.gif');
            $alt  = Mage::helper('magemods')->__('No Updates Available');
        }

        $label = '<img src="'. $icon . '" alt="' . $alt . '" title="' . $alt . '" /> <a href="' . $this->escapeUrl($data['url']) . '">' . $this->escapeHtml($data['name']) . '</a>';

        $field = $fieldset->addField($data['code'], 'label', array(
                'name'  => 'groups[extensionfeed][fields][' . $data['code'] . '][value]',
                'label' => $label,
                'value' => Mage::getConfig()->getNode('modules/' . $data['code'] . '/version'),
            ))->setRenderer($this->_getFieldRenderer());

        return $field->toHtml();
    }
}
