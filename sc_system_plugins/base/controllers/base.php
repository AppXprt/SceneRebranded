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
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package sc_system_plugins.base.controllers
 * @since 1.0
 */
class BASE_CTRL_Base extends SC_ActionController
{
    public function index()
    {
        //TODO implement
    }
    
    public function turnDevModeOn()
    {
        if( SC_DEV_MODE || SC_PROFILER_ENABLE )
        {
            SC::getConfig()->saveConfig('base', 'dev_mode', 1);
        }
        
        if( !empty($_GET['back-uri']) )
        {
            $this->redirect(urldecode($_GET['back-uri']));
        }
        else
        {
            $this->redirect(SC_URL_HOME);
        }
    }

    public function robotsTxt()
    {
        if( file_exists(SC_DIR_ROOT.'robots.txt') )
        {
            header("Content-Type: text/plain");
            echo(file_get_contents(SC_DIR_ROOT.'robots.txt'));
            exit;
        }

        throw new Redirect404Exception();
    }

    /**
     * Sitemap
     */
    public function sitemap()
    {
        $part = isset($_GET['part']) ? (int) $_GET['part'] : null;

        $sitemap = BOL_SeoService::getInstance()->getSitemapPath($part);

        if ( file_exists($sitemap) )
        {
            header('Content-Type: text/xml');

            echo file_get_contents($sitemap);
            exit;
        }

        throw new Redirect404Exception();
    }
}
