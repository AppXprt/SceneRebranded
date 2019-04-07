<?php

/**
 * EXHIBIT A. Common Public Attribution License Version 1.0
 * The contents of this file are subject to the Common Public Attribution License Version 1.0 (the “License”);
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.scene.org/license. The License is based on the Mozilla Public License Version 1.1
 * but Sections 14 and 15 have been added to cover use of software over a computer network and provide for
 * limited attribution for the Original Developer. In addition, Exhibit A has been modified to be consistent
 * with Exhibit B. Software distributed under the License is distributed on an “AS IS” basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for the specific language
 * governing rights and limitations under the License. The Original Code is Scene software.
 * The Initial Developer of the Original Code is Scene Foundation (http://www.scene.org/foundation).
 * All portions of the code written by Scene Foundation are Copyright (c) 2011. All Rights Reserved.

 * EXHIBIT B. Attribution Information
 * Attribution Copyright Notice: Copyright 2011 Scene Foundation. All rights reserved.
 * Attribution Phrase (not exceeding 10 words): Powered by Scene community software
 * Attribution URL: http://www.scene.org/
 * Graphic Image as provided in the Covered Code.
 * Display of Attribution Information is required in Larger Works which are defined in the CPAL as a work
 * which combines Covered Code or portions thereof with code not governed by the terms of the CPAL.
 */

/**
 * Data Access Object for `base_component_place_cache` table.
 *
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package sc_system_plugins.base.bol
 * @since 1.0
 */
class BOL_ComponentPlaceCacheDao extends SC_BaseDao
{

    /**
     * Constructor.
     *
     */
    protected function __construct()
    {
        parent::__construct();
    }
    /**
     * Singleton instance.
     *
     * @var BOL_ComponentPlaceCacheDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return BOL_ComponentPlaceCacheDao
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     * @see SC_BaseDao::getDtoClassName()
     *
     */
    public function getDtoClassName()
    {
        return 'BOL_ComponentPlaceCache';
    }

    /**
     * @see SC_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return SC_DB_PREFIX . 'base_component_place_cache';
    }

    /**
     * 
     * @param $placeId
     * @param $entityId
     * @return SC_Example
     */
    private function getFilterExample( $placeId, $entityId = null )
    {
        $example = new SC_Example();
        $example->andFieldEqual('placeId', $placeId);

        if ( empty($entityId) )
        {
            $example->andFieldEqual('entityId', 0);
        }
        else
        {
            $example->andFieldEqual('entityId', $entityId);
        }

        return $example;
    }

    public function findCache( $placeId, $entityId = null )
    {
        $example = $this->getFilterExample($placeId, $entityId);

        return $this->findObjectByExample($example);
    }

    public function deleteCache( $placeId, $entityId = null )
    {
        $example = $this->getFilterExample($placeId, $entityId);

        return $this->deleteByExample($example);
    }

    public function deleteAllCache( $placeId = null )
    {
        if ( empty($placeId) )
        {
            $this->dbo->query('TRUNCATE TABLE `' . $this->getTableName() . '`');

            return;
        }

        $example = new SC_Example();
        $example->andFieldEqual('placeId', $placeId);

        return $this->deleteByExample($example);
    }
}