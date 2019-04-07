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
 * @author Nurlan Dzhumakaliev <nurlanj@live.com>
 * @package sc_cron
 * @since 1.0
 */
define('_SC_', true);

define('DS', DIRECTORY_SEPARATOR);

define('SC_DIR_ROOT', substr(dirname(__FILE__), 0, - strlen('sc_cron')));

define('SC_CRON', true);

require_once(SC_DIR_ROOT . 'sc_includes' . DS . 'init.php');

// set error log file
if ( !defined('SC_ERROR_LOG_ENABLE') || (bool) SC_ERROR_LOG_ENABLE )
{
    $logFilePath = SC_DIR_LOG . 'cron_error.log';
    $logger = SC::getLogger('sc_core_log');
    $logger->setLogWriter(new BASE_CLASS_FileLogWriter($logFilePath));
    $errorManager->setLogger($logger);
}

if ( !isset($_GET['ow-light-cron']) && !SC::getConfig()->getValue('base', 'cron_is_configured') )
{
    if ( SC::getConfig()->configExists('base', 'cron_is_configured') )
    {
        SC::getConfig()->saveConfig('base', 'cron_is_configured', 1);
    }
    else
    {
        SC::getConfig()->addConfig('base', 'cron_is_configured', 1);
    }
}

SC::getRouter()->setBaseUrl(SC_URL_HOME);

date_default_timezone_set(SC::getConfig()->getValue('base', 'site_timezone'));
SC_Auth::getInstance()->setAuthenticator(new SC_SessionAuthenticator());

SC::getPluginManager()->initPlugins();
$event = new SC_Event(SC_EventManager::ON_PLUGINS_INIT);
SC::getEventManager()->trigger($event);

//init cache manager
$beckend = SC::getEventManager()->call('base.cache_backend_init');

if ( $beckend !== null )
{
    SC::getCacheManager()->setCacheBackend($beckend);
    SC::getCacheManager()->setLifetime(3600);
    SC::getDbo()->setUseCashe(true);
}

SC::getThemeManager()->initDefaultTheme();

// setting current theme
$activeThemeName = SC::getConfig()->getValue('base', 'selectedTheme');

if ( $activeThemeName !== BOL_ThemeService::DEFAULT_THEME && SC::getThemeManager()->getThemeService()->themeExists($activeThemeName) )
{
    SC_ThemeManager::getInstance()->setCurrentTheme(BOL_ThemeService::getInstance()->getThemeObjectByKey(trim($activeThemeName)));
}

$plugins = BOL_PluginService::getInstance()->findActivePlugins();

foreach ( $plugins as $plugin )
{
    /* @var $plugin BOL_Plugin */
    $pluginRootDir = SC::getPluginManager()->getPlugin($plugin->getKey())->getRootDir();
    if ( file_exists($pluginRootDir . 'cron.php') )
    {
        include $pluginRootDir . 'cron.php';
        $className = strtoupper($plugin->getKey()) . '_Cron';
        $cron = new $className;

        $runJobs = array();
        $newRunJobDtos = array();

        foreach ( BOL_CronService::getInstance()->findJobList() as $runJob )
        {
            /* @var $runJob BOL_CronJob */
            $runJobs[$runJob->methodName] = $runJob->runStamp;
        }

        $jobs = $cron->getJobList();

        foreach ( $jobs as $job => $interval )
        {
            $methodName = $className . '::' . $job;
            $runStamp = ( isset($runJobs[$methodName]) ) ? $runJobs[$methodName] : 0;
            $currentStamp = time();
            if ( ( $currentStamp - $runStamp ) > ( $interval * 60 ) )
            {
                $runJobDto = new BOL_CronJob();
                $runJobDto->methodName = $methodName;
                $runJobDto->runStamp = $currentStamp;
                $newRunJobDtos[] = $runJobDto;

                BOL_CronService::getInstance()->batchSave($newRunJobDtos);

                $newRunJobDtos = array();

                $cron->$job();
            }
        }
    }
}
