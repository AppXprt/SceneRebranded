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
 * The class is a gateway for auth. adapters and provides common API to authenticate users.
 *
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package sc_core
 * @since 1.0
 */
class SC_SessionAuthenticator implements SC_IAuthenticator
{
    const USER_ID_SESSION_KEY = 'userId';

    public function __construct()
    {
        
    }

    /**
     * Checks if current user is authenticated.
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        return ( SC::getSession()->isKeySet(self::USER_ID_SESSION_KEY) && $this->getUserId() > 0 );
    }

    /**
     * Returns current user id.
     * If user is not authenticated 0 returned.
     *
     * @return integer
     */
    public function getUserId()
    {
        return (int) SC::getSession()->get(self::USER_ID_SESSION_KEY);
    }

    /**
     * Logins user by provided user id.
     *
     * @param integer $userId
     */
    public function login( $userId )
    {
        SC::getSession()->set(self::USER_ID_SESSION_KEY, $userId);
    }

    /**
     * Logs out current user.
     */
    public function logout()
    {
        SC::getSession()->delete(self::USER_ID_SESSION_KEY);
    }

    /**
     * Returns auth id
     *
     * @return string
     */
    public function getId()
    {
        return session_id();
    }
}