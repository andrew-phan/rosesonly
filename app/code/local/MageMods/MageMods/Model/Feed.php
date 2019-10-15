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

abstract class MageMods_MageMods_Model_Feed
{
    abstract public function processFeed($data);

    abstract public function getFeedUrl();

    abstract public function getFrequency();

    abstract public function getCheckCache();

    public function checkUpdate()
    {
        if (($this->getFrequency() + $this->getLastUpdate()) > time()) {
            return $this;
        }

        if ($xml = $this->getFeedData()) {
            $this->processFeed($xml);
        }

        $this->setLastUpdate();

        return $this;
    }

    public function getLastUpdate()
    {
        return Mage::app()->loadCache($this->getCheckCache());
    }

    public function setLastUpdate()
    {
        Mage::app()->saveCache(time(), $this->getCheckCache());

        return $this;
    }

    public function getFeedData()
    {
        $curl = new Varien_Http_Adapter_Curl();
        $curl->setConfig(array(
            'timeout' => 10
        ));

        $curl->write(Zend_Http_Client::GET, $this->getFeedUrl(), '1.0');

        $data = $curl->read();

        if ($data === false) {
            return false;
        }

        $data = preg_split('/^\r?$/m', $data, 2);
        $data = trim($data[1]);

        $curl->close();

        try {
            $xml = new SimpleXMLElement($data);
        } catch (Exception $e) {
            return false;
        }

        return $xml;
    }
}
