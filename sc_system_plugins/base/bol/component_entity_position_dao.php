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
 * Data Access Object for `base_component_entity_position` table.
 *
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package sc_system_plugins.base.bol
 * @since 1.0
 */
class BOL_ComponentEntityPositionDao extends SC_BaseDao
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
     * @var BOL_ComponentEntitySectionDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return BOL_ComponentEntityPositionDao
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
        return 'BOL_ComponentEntityPosition';
    }

    /**
     * @see SC_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return SC_DB_PREFIX . 'base_component_entity_position';
    }

    public function findAllPositionList( $placeId, $entityId )
    {
        $componentPlaceDao = BOL_ComponentPlaceDao::getInstance();
        $componentEntityPlaceDao = BOL_ComponentEntityPlaceDao::getInstance();

        $query = '
        	SELECT `p`.* FROM `' . $this->getTableName() . '` AS `p`
        	INNER JOIN ( 
            	( SELECT `uniqName` FROM `' . $componentPlaceDao->getTableName() . '`
            		WHERE `placeId`=:placeId )
            	UNION
            	( SELECT `uniqName` FROM `' . $componentEntityPlaceDao->getTableName() . '`
            		WHERE `placeId`=:placeId AND `entityId`=:entityId )
        	) AS `c` ON `p`.`componentPlaceUniqName` = `c`.`uniqName` AND `p`.`entityId`=:entityId
        ';

        return $this->dbo->queryForObjectList($query,
            $this->getDtoClassName(),
            array('placeId' => $placeId, 'entityId' => $entityId));
    }

    public function findAllPositionIdList( $placeId, $entityId )
    {
        $positionDtoList = $this->findAllPositionList($placeId, $entityId);

        $idList = array();
        foreach ( $positionDtoList as $item )
        {
            $idList[] = $item->id;
        }

        return $idList;
    }

    public function findSectionPositionIdList( $placeId, $entityId, $section )
    {
        $componentPlaceDao = BOL_ComponentPlaceDao::getInstance();
        $componentEntityPlaceDao = BOL_ComponentEntityPlaceDao::getInstance();

        $query = '
        	SELECT `p`.`id` FROM `' . $this->getTableName() . '` AS `p`
        	INNER JOIN ( 
            	( SELECT `uniqName` FROM `' . $componentPlaceDao->getTableName() . '`
            		WHERE `placeId`=:placeId )
            	UNION
            	( SELECT `uniqName` FROM `' . $componentEntityPlaceDao->getTableName() . '`
            		WHERE `placeId`=:placeId AND `entityId`=:entityId )
        	) AS `c` ON `p`.`componentPlaceUniqName` = `c`.`uniqName` AND `section`=:section AND `p`.`entityId`=:entityId
        ';

        return $this->dbo->queryForColumnList($query,
            array('placeId' => $placeId, 'entityId' => $entityId, 'section' => $section));
    }

    public function deleteAllByUniqName( $uniqName )
    {
        $example = new SC_Example();
        $example->andFieldEqual('componentPlaceUniqName', $uniqName);

        return $this->deleteByExample($example);
    }

    public function deleteByUniqNameList( $entityId, $uniqNameList = array() )
    {
        $entityId = (int) $entityId;
        if ( !$entityId )
        {
            throw new InvalidArgumentException('Invalid argument $entityId');
        }

        if ( empty($uniqNameList) )
        {
            return false;
        }

        $example = new SC_Example();
        $example->andFieldEqual('entityId', $entityId);
        $example->andFieldInArray('componentPlaceUniqName', $uniqNameList);

        return $this->deleteByExample($example);
    }
}