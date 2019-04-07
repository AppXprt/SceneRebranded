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
define('_SC_', true);

define('DS', DIRECTORY_SEPARATOR);

define('SC_DIR_ROOT', dirname(__FILE__) . DS);

require_once(SC_DIR_ROOT . 'sc_includes' . DS . 'init.php');

if ( !defined('SC_ERROR_LOG_ENABLE') || (bool) SC_ERROR_LOG_ENABLE )
{
    $logFilePath = SC_DIR_LOG . 'error.log';
    $logger = SC::getLogger('sc_core_log');
    $logger->setLogWriter(new BASE_CLASS_FileLogWriter($logFilePath));
    $errorManager->setLogger($logger);
}

if ( file_exists(SC_DIR_ROOT . 'sc_install' . DS . 'install.php') )
{
    include SC_DIR_ROOT . 'sc_install' . DS . 'install.php';
}

SC::getSession()->start();

$application = SC::getApplication();

if ( SC_PROFILER_ENABLE || SC_DEV_MODE )
{
    UTIL_Profiler::getInstance()->mark('before_app_init');
}

$application->init();

if ( SC_PROFILER_ENABLE || SC_DEV_MODE )
{
    UTIL_Profiler::getInstance()->mark('after_app_init');
}

$event = new SC_Event(SC_EventManager::ON_APPLICATION_INIT);

SC::getEventManager()->trigger($event);

$application->route();

$event = new SC_Event(SC_EventManager::ON_AFTER_ROUTE);

if ( SC_PROFILER_ENABLE || SC_DEV_MODE )
{
    UTIL_Profiler::getInstance()->mark('after_route');
}

SC::getEventManager()->trigger($event);

$application->handleRequest();

if ( SC_PROFILER_ENABLE || SC_DEV_MODE )
{
    UTIL_Profiler::getInstance()->mark('after_controller_call');
}

$event = new SC_Event(SC_EventManager::ON_AFTER_REQUEST_HANDLE);

SC::getEventManager()->trigger($event);

$application->finalize();

if ( SC_PROFILER_ENABLE || SC_DEV_MODE )
{
    UTIL_Profiler::getInstance()->mark('after_finalize');
}

$application->returnResponse();
