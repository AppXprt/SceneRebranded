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
 * Data Access Object for `base_email_verify` table.
 *
 * @author Podyachev Evgeny <joker.SC2@gmail.com>
 * @package sc_system_plugins.base.bol
 * @since 1.0
 */
class BOL_EmailVerifyDao extends SC_BaseDao
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
     * @var BOL_EmailVerifiedDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return BOL_EmailVerifiedDao
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
            self::$classInstance = new self();

        return self::$classInstance;
    }

    /**
     * @see SC_BaseDao::getDtoClassName()
     *
     */
    public function getDtoClassName()
    {
        return 'BOL_EmailVerify';
    }

    /**
     * @see SC_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return SC_DB_PREFIX . 'base_email_verify';
    }

    /**
     * @param string $email
     * @return BOL_EmailVerified
     */
    public function findByEmail( $email, $type )
    {
        if ( $email === null || $type === null )
        {
            return null;
        }

        $example = new SC_Example();
        $example->andFieldEqual('email', trim($email));
        $example->andFieldEqual('type', trim($type));

        return $this->findObjectByExample($example);
    }

    public function findByEmailAndUserId( $email, $userId, $type )
    {
        if ( $email === null || $type === null || $userId === null )
        {
            return null;
        }

        $example = new SC_Example();
        $example->andFieldEqual('email', trim($email));
        $example->andFieldEqual('userId', (int) $userId);
        $example->andFieldEqual('type', trim($type));

        return $this->findObjectByExample($example);
    }

    /**
     * @param string $hash
     * @return BOL_EmailVerified
     */
    public function findByHash( $hash )
    {
        if ( $hash === null )
        {
            return null;
        }

        $hashlVal = trim($hash);

        $example = new SC_Example();
        $example->andFieldEqual('hash', $hashlVal);
        return $this->findObjectByExample($example);
    }

    /**
     * @param array $objects
     */
    public function batchReplace( $objects )
    {
        $this->dbo->batchInsertOrUpdateObjectList($this->getTableName(), $objects);
    }

    public function deleteByCreatedStamp( $stamp )
    {
        $timeStamp = (int) $stamp;

        $example = new SC_Example();
        $example->andFieldLessOrEqual('createStamp', $timeStamp);
        $this->deleteByExample($example);
    }

    public function deleteByUserId( $userId )
    {
//        $timeStamp = (int) $stamp;

        $example = new SC_Example();
        $example->andFieldEqual('userId', $userId);
        $this->deleteByExample($example);
    }
}
?>
