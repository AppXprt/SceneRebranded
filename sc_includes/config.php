<?php

define('SC_URL_HOME', 'https://scene.vixiv.net:443/');

define('SC_DB_HOST', 'v19.cr9tjdjmhs0f.us-east-1.rds.amazonaws.com');
define('SC_DB_PORT', null);
define('SC_DB_USER', 'scene');
define('SC_DB_PASSWORD', '$3CuR3$C3N319');
define('SC_DB_NAME', 'scene');

define('SC_DB_PREFIX', 'sc_');

define('SC_DIR_USERFILES', SC_DIR_ROOT.'sc_userfiles'.DS);
define('SC_DIR_STATIC', SC_DIR_ROOT.'sc_static'.DS);
define('SC_URL_STATIC', SC_URL_HOME.'sc_static/');
define('SC_URL_USERFILES', SC_URL_HOME.'sc_userfiles/');
define('SC_DIR_PLUGINFILES', SC_DIR_ROOT.'sc_pluginfiles/');

define('SC_PASSWORD_SALT', 'eV1v2l1zOwAle6ab');

define('SC_DIR_CORE', SC_DIR_ROOT.'sc_core'.DS);
define('SC_DIR_INC', SC_DIR_ROOT.'sc_includes'.DS);
define('SC_DIR_LIB', SC_DIR_ROOT.'sc_libraries'.DS);
define('SC_DIR_UTIL', SC_DIR_ROOT.'sc_utilities'.DS);
define('SC_DIR_PLUGIN', SC_DIR_ROOT.'sc_plugins'.DS);
define('SC_DIR_THEME', SC_DIR_ROOT.'sc_themes'.DS);
define('SC_DIR_SYSTEM_PLUGIN', SC_DIR_ROOT.'sc_system_plugins'.DS);
define('SC_DIR_SMARTY', SC_DIR_ROOT.'sc_smarty'.DS);

define('SC_USE_CLOUDFILES', false);

if ( defined('SC_CRON') )
{
    define('SC_DEBUG_MODE', false);
    define('SC_DEV_MODE', false);
    define('SC_PROFILER_ENABLE', false);
}
else
{
    /**
    * Make changes in this block if you want to enable DEV mode and DEBUG mode
    */

    define('SC_DEBUG_MODE', true);
    define('SC_DEV_MODE', true);
    define('SC_PROFILER_ENABLE', false);
}
