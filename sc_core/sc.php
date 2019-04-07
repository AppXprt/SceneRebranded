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
 * @package sc_core
 * @since 1.0
 */
final class SC
{
    const CONTEXT_MOBILE = SC_Application::CONTEXT_MOBILE;
    const CONTEXT_DESKTOP = SC_Application::CONTEXT_DESKTOP;
    const CONTEXT_API = SC_Application::CONTEXT_API;
    const CONTEXT_CLI = SC_Application::CONTEXT_CLI;

    private static $context;

    private static function detectContext()
    {
        if ( self::$context !== null )
        {
            return;
        }

        if ( defined('SC_USE_CONTEXT') )
        {
            switch ( true )
            {
                case SC_USE_CONTEXT == 1:
                    self::$context = self::CONTEXT_DESKTOP;
                    return;

                case SC_USE_CONTEXT == 1 << 1:
                    self::$context = self::CONTEXT_MOBILE;
                    return;

                case SC_USE_CONTEXT == 1 << 2:
                    self::$context = self::CONTEXT_API;
                    return;

                case SC_USE_CONTEXT == 1 << 3:
                    self::$context = self::CONTEXT_CLI;
                    return;
            }
        }


        $context = self::CONTEXT_DESKTOP;

        try
        {
            $isSmart = UTIL_Browser::isSmartphone();
        }
        catch ( Exception $e )
        {
            return;
        }

        if ( defined('SC_CRON') )
        {
            $context = self::CONTEXT_DESKTOP;
        }
        else if ( self::getSession()->isKeySet(SC_Application::CONTEXT_NAME) )
        {
            $context = self::getSession()->get(SC_Application::CONTEXT_NAME);
        }
        else if ( $isSmart )
        {
            $context = self::CONTEXT_MOBILE;
        }

        if ( defined('SC_USE_CONTEXT') )
        {
            if ( (SC_USE_CONTEXT & 1 << 1) == 0 && $context == self::CONTEXT_MOBILE )
            {
                $context = self::CONTEXT_DESKTOP;
            }

            if ( (SC_USE_CONTEXT & 1 << 2) == 0 && $context == self::CONTEXT_API )
            {
                $context = self::CONTEXT_DESKTOP;
            }
        }

        if ( (bool) SC::getConfig()->getValue('base', 'disable_mobile_context') && $context == self::CONTEXT_MOBILE )
        {
            $context = self::CONTEXT_DESKTOP;
        }

        //temp API context detection
        //TODO remake
        $uri = UTIL_Url::getRealRequestUri(SC::getRouter()->getBaseUrl(), $_SERVER['REQUEST_URI']);


        if ( mb_strstr($uri, '/') )
        {
            if ( trim(mb_substr($uri, 0, mb_strpos($uri, '/'))) == 'api' )
            {
                $context = self::CONTEXT_API;
            }
        }
        else
        {
            if ( trim($uri) == 'api' )
            {
                $context = self::CONTEXT_API;
            }
        }

        self::$context = $context;
    }

    /**
     * Returns autoloader object.
     *
     * @return SC_Autoload
     */
    public static function getAutoloader()
    {
        return SC_Autoload::getInstance();
    }

    /**
     * Returns front controller object.
     *
     * @return SC_Application
     */
    public static function getApplication()
    {
        self::detectContext();

        switch ( self::$context )
        {
            case self::CONTEXT_MOBILE:
                return SC_MobileApplication::getInstance();

            case self::CONTEXT_API:
                return SC_ApiApplication::getInstance();

            case self::CONTEXT_CLI:
                return SC_CliApplication::getInstance();

            default:
                return SC_Application::getInstance();
        }
    }

    /**
     * Returns global config object.
     *
     * @return SC_Config
     */
    public static function getConfig()
    {
        return SC_Config::getInstance();
    }

    /**
     * Returns session object.
     *
     * @return SC_Session
     */
    public static function getSession()
    {
        return SC_Session::getInstance();
    }

    /**
     * Returns current web user object.
     *
     * @return SC_User
     */
    public static function getUser()
    {
        return SC_User::getInstance();
    }
    /**
     * Database object instance.
     *
     * @var SC_Database
     */
    private static $dboInstance;

    /**
     * Returns DB access object with default connection.
     *
     * @return SC_Database
     */
    public static function getDbo()
    {
        if ( self::$dboInstance === null )
        {
            $params = array(
                'host' => SC_DB_HOST,
                'username' => SC_DB_USER,
                'password' => SC_DB_PASSWORD,
                'dbname' => SC_DB_NAME
            );
            if ( defined('SC_DB_PORT') && (SC_DB_PORT !== null) )
            {
                $params['port'] = SC_DB_PORT;
            }
            if ( defined('SC_DB_SOCKET') )
            {
                $params['socket'] = SC_DB_SOCKET;
            }

            if ( SC_DEV_MODE || SC_PROFILER_ENABLE )
            {
                $params['profilerEnable'] = true;
            }

            if ( SC_DEBUG_MODE )
            {
                $params['debugMode'] = true;
            }

            self::$dboInstance = SC_Database::getInstance($params);
        }
        return self::$dboInstance;
    }

    /**
     * Returns system mailer object.
     *
     * 	@return SC_Mailer
     */
    public static function getMailer()
    {
        return SC_Mailer::getInstance();
    }

    /**
     * Returns responded HTML document object.
     *
     * @return SC_HtmlDocument
     */
    public static function getDocument()
    {
        return SC_Response::getInstance()->getDocument();
    }

    /**
     * Returns global request object.
     *
     * @return SC_Request
     */
    public static function getRequest()
    {
        return SC_Request::getInstance();
    }

    /**
     * Returns global response object.
     *
     * @return SC_Response
     */
    public static function getResponse()
    {
        return SC_Response::getInstance();
    }

    /**
     * Returns language object.
     *
     * @return SC_Language
     */
    public static function getLanguage()
    {
        return SC_Language::getInstance();
    }

    /**
     * Returns system router object.
     *
     * @return SC_Router
     */
    public static function getRouter()
    {
        return SC_Router::getInstance();
    }

    /**
     * Returns system plugin manager object.
     *
     * @return SC_PluginManager
     */
    public static function getPluginManager()
    {
        return SC_PluginManager::getInstance();
    }

    /**
     * Returns system theme manager object.
     *
     * @return SC_ThemeManager
     */
    public static function getThemeManager()
    {
        return SC_ThemeManager::getInstance();
    }

    /**
     * Returns system event manager object.
     *
     * @return SC_EventManager
     */
    public static function getEventManager()
    {
        return SC_EventManager::getInstance();
    }

    /**
     * @return SC_Registry
     */
    public static function getRegistry()
    {
        return SC_Registry::getInstance();
    }

    /**
     * Returns global feedback object.
     *
     * @return SC_Feedback
     */
    public static function getFeedback()
    {
        return SC_Feedback::getInstance();
    }

    /**
     * Returns global navigation object.
     *
     * @return SC_Navigation
     */
    public static function getNavigation()
    {
        return SC_Navigation::getInstance();
    }

    /**
     * @deprecated
     * @return SC_Dispatcher
     */
    public static function getDispatcher()
    {
        return SC_RequestHandler::getInstance();
    }

    /**
     * @return SC_RequestHandler
     */
    public static function getRequestHandler()
    {
        self::detectContext();

        switch ( self::$context )
        {
            case self::CONTEXT_API:
                return SC_ApiRequestHandler::getInstance();

            default:
                return SC_RequestHandler::getInstance();
        }
    }

    /**
     *
     * @return SC_CacheService
     */
    public static function getCacheService()
    {
        return BOL_DbCacheService::getInstance(); //TODO make configurable
    }
    private static $storage;

    /**
     *
     * @return SC_Storage
     */
    public static function getStorage()
    {
        if ( self::$storage === null )
        {
            self::$storage = SC::getEventManager()->call('core.get_storage');

            if ( self::$storage === null )
            {
                switch ( true )
                {
                    case defined('SC_USE_AMAZON_S3_CLOUDFILES') && SC_USE_AMAZON_S3_CLOUDFILES :
                        self::$storage = new BASE_CLASS_AmazonCloudStorage();
                        break;

                    case defined('SC_USE_CLOUDFILES') && SC_USE_CLOUDFILES :
                        self::$storage = new BASE_CLASS_CloudStorage();
                        break;

                    default :
                        self::$storage = new BASE_CLASS_FileStorage();
                        break;
                }
            }
        }

        return self::$storage;
    }

    public static function getLogger( $logType = 'sc' )
    {
        return SC_Log::getInstance($logType);
    }

    /**
     * @return SC_Authorization
     */
    public static function getAuthorization()
    {
        return SC_Authorization::getInstance();
    }

    /**
     * @return SC_CacheManager
     */
    public static function getCacheManager()
    {
        return SC_CacheManager::getInstance();
    }

    public static function getClassInstance( $className, $arguments = null )
    {
        $args = func_get_args();
        $constuctorArgs = array_splice($args, 1);

        return self::getClassInstanceArray($className, $constuctorArgs);
    }

    public static function getClassInstanceArray( $className, array $arguments = array() )
    {
        $params = array(
            'className' => $className,
            'arguments' => $arguments
        );

        $eventManager = SC::getEventManager();
        $eventManager->trigger(new SC_Event("core.performance_test", array("key" => "component_construct.start", "params" => $params)));

        $event = new SC_Event("class.get_instance." . $className, $params);
        $eventManager->trigger($event);
        $instance = $event->getData();

        if ( $instance !== null )
        {
            $eventManager->trigger(new SC_Event("core.performance_test", array("key" => "component_construct.end", "params" => $params)));
            return $instance;
        }

        $event = new SC_Event("class.get_instance", $params);

        $eventManager->trigger($event);
        $instance = $event->getData();

        if ( $instance !== null )
        {
            $eventManager->trigger(new SC_Event("core.performance_test", array("key" => "component_construct.end", "params" => $params)));
            return $instance;
        }

        $rClass = new ReflectionClass($className);
        $eventManager->trigger(new SC_Event("core.performance_test", array("key" => "component_construct.end", "params" => $params)));
        return $rClass->newInstanceArgs($arguments);
    }

    /**
     * Returns text search manager object.
     *
     * @return SC_TextSearchManager
     */
    public static function getTextSearchManager()
    {
        return SC_TextSearchManager::getInstance();
    }
}
