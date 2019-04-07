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
 * Abstract controller class to work with the remote storage.
 *
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package sc_system_plugins.admin.controllers
 * @since 1.7.7
 */
abstract class ADMIN_CTRL_StorageAbstract extends ADMIN_CTRL_Abstract
{
    /**
     * @var BOL_PluginService
     */
    protected $pluginService;

    /**
     * @var BOL_StorageService
     */
    protected $storageService;

    /**
     * @var BOL_ThemeService
     */
    protected $themeService;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->pluginService = BOL_PluginService::getInstance();
        $this->storageService = BOL_StorageService::getInstance();
        $this->themeService = BOL_ThemeService::getInstance();
    }

    protected function getFtpConnection()
    {
        try
        {
            $ftp = $this->storageService->getFtpConnection();
        }
        catch ( LogicException $e )
        {
            SC::getFeedback()->error($e->getMessage());
            $this->redirect(SC::getRequest()->buildUrlQueryString(SC::getRouter()->urlFor("ADMIN_CTRL_Storage",
                        "ftpAttrs"),
                    array(BOL_StorageService::URI_VAR_BACK_URI => urlencode(SC::getRequest()->getRequestUri()))));
        }

        return $ftp;
    }

    protected function redirectToBackUri( $getParams )
    {
        if ( !isset($getParams[BOL_StorageService::URI_VAR_BACK_URI]) )
        {
            return;
        }

        $backUri = $getParams[BOL_StorageService::URI_VAR_BACK_URI];
        unset($getParams[BOL_StorageService::URI_VAR_BACK_URI]);

        if( isset($getParams[BOL_StorageService::URI_VAR_RETURN_RESULT]) && !$getParams[BOL_StorageService::URI_VAR_RETURN_RESULT] )
        {
            $getParams = array();
        }
        
        $this->redirect(SC::getRequest()->buildUrlQueryString(SC_URL_HOME . urldecode($backUri), $getParams));
    }

    protected function getTemDirPath()
    {
        return SC_DIR_PLUGINFILES . "ow" . DS;
    }
}