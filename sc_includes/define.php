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
define('SC_DIR_STATIC_PLUGIN', SC_DIR_STATIC . 'plugins' . DS);
define('SC_DIR_STATIC_THEME', SC_DIR_STATIC . 'themes' . DS);
define('SC_DIR_PLUGIN_USERFILES', SC_DIR_USERFILES . 'plugins' . DS);
define('SC_DIR_THEME_USERFILES', SC_DIR_USERFILES . 'themes' . DS);
define('SC_DIR_LOG', SC_DIR_ROOT . 'sc_log' . DS);

if ( defined('SC_URL_STATIC') )
{
    define('SC_URL_STATIC_THEMES', SC_URL_STATIC . 'themes/');
    define('SC_URL_STATIC_PLUGINS', SC_URL_STATIC . 'plugins/');
}

if ( defined('SC_URL_USERFILES') )
{
    define('SC_URL_PLUGIN_USERFILES', SC_URL_USERFILES . 'plugins/');
    define('SC_URL_THEME_USERFILES', SC_URL_USERFILES . 'themes/');
}

define("SC_DIR_LIB_VENDOR", SC_DIR_LIB . "vendor" . DS);

if ( !defined("SC_SQL_LIMIT_USERS_COUNT") )
{
    define("SC_SQL_LIMIT_USERS_COUNT", 10000);
}
