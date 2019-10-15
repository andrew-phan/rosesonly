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

class MageMods_MageMods_Model_Feed_Extension
    extends MageMods_MageMods_Model_Feed
{
    const FREQUENCY  = 86400;
    const FEEDURL    = 'http://www.magemods.co/extensions/feed';
    const CHECKCACHE = 'magemods_extension_lastcheck';
    const FEEDCACHE  = 'magemods_extension_feed';

    public function getCheckCache()
    {
        return self::CHECKCACHE;
    }

    public function getFeedUrl()
    {
        return self::FEEDURL;
    }

    public function getFrequency()
    {
        return self::FREQUENCY;
    }

    public function processFeed($data)
    {
        $array = array();

        foreach ($data->extension as $extension) {
            $array[] = array(
                'name'    => (string)$extension->name,
                'code'    => (string)$extension->code,
                'url'     => (string)$extension->url,
                'version' => (string)$extension->version
            );
        }

        Mage::app()->saveCache(serialize($array), self::FEEDCACHE);
    }
}
