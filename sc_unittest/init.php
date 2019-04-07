<?php

define('_SC_', true);
define('DS', DIRECTORY_SEPARATOR);
define('SC_DIR_ROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
define('SC_CRON', true);

require_once(SC_DIR_ROOT . 'sc_includes' . DS . 'init.php');

SC::getRouter()->setBaseUrl(SC_URL_HOME);

date_default_timezone_set(SC::getConfig()->getValue('base', 'site_timezone'));
SC_Auth::getInstance()->setAuthenticator(new SC_SessionAuthenticator());

SC::getPluginManager()->initPlugins();
$event = new SC_Event(SC_EventManager::ON_PLUGINS_INIT);
SC::getEventManager()->trigger($event);

SC::getThemeManager()->initDefaultTheme();

// setting current theme
$activeThemeName = SC::getConfig()->getValue('base', 'selectedTheme');

if ( $activeThemeName !== BOL_ThemeService::DEFAULT_THEME && SC::getThemeManager()->getThemeService()->themeExists($activeThemeName) )
{
    SC_ThemeManager::getInstance()->setCurrentTheme(BOL_ThemeService::getInstance()->getThemeObjectByKey(trim($activeThemeName)));
}