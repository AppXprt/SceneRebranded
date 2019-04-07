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
 * @method static SC_MobileApplication getInstance()
 * @since 1.0
 */
class SC_MobileApplication extends SC_Application
{
    use SC_Singleton;

    /**
     * Constructor.
     */
    private function __construct()
    {
        $this->context = self::CONTEXT_MOBILE;
    }

    /**
     * ---------
     */
    public function handleRequest()
    {
        $baseConfigs = SC::getConfig()->getValues('base');

        //members only
        if ( (int) $baseConfigs['guests_can_view'] === BOL_UserService::PERMISSIONS_GUESTS_CANT_VIEW && !SC::getUser()->isAuthenticated() )
        {
            $attributes = array(
                SC_RequestHandler::CATCH_ALL_REQUEST_KEY_CTRL => 'BASE_MCTRL_User',
                SC_RequestHandler::CATCH_ALL_REQUEST_KEY_ACTION => 'standardSignIn'
            );

            SC::getRequestHandler()->setCatchAllRequestsAttributes('base.members_only', $attributes);
            $this->addCatchAllRequestsException('base.members_only_exceptions', 'base.members_only');
        }

        //splash screen
        if ( (bool) SC::getConfig()->getValue('base', 'splash_screen') && !isset($_COOKIE['splashScreen']) )
        {
            $attributes = array(
                SC_RequestHandler::CATCH_ALL_REQUEST_KEY_CTRL => 'BASE_MCTRL_BaseDocument',
                SC_RequestHandler::CATCH_ALL_REQUEST_KEY_ACTION => 'splashScreen',
                SC_RequestHandler::CATCH_ALL_REQUEST_KEY_REDIRECT => true,
                SC_RequestHandler::CATCH_ALL_REQUEST_KEY_JS => true,
                SC_RequestHandler::CATCH_ALL_REQUEST_KEY_ROUTE => 'base_page_splash_screen'
            );

            SC::getRequestHandler()->setCatchAllRequestsAttributes('base.splash_screen', $attributes);
            $this->addCatchAllRequestsException('base.splash_screen_exceptions', 'base.splash_screen');
        }

        // password protected
        if ( (int) $baseConfigs['guests_can_view'] === BOL_UserService::PERMISSIONS_GUESTS_PASSWORD_VIEW && !SC::getUser()->isAuthenticated() && !isset($_COOKIE['base_password_protection'])
        )
        {
            $attributes = array(
                SC_RequestHandler::CATCH_ALL_REQUEST_KEY_CTRL => 'BASE_MCTRL_BaseDocument',
                SC_RequestHandler::CATCH_ALL_REQUEST_KEY_ACTION => 'passwordProtection'
            );

            SC::getRequestHandler()->setCatchAllRequestsAttributes('base.password_protected', $attributes);
            $this->addCatchAllRequestsException('base.password_protected_exceptions', 'base.password_protected');
        }

        // maintenance mode
        if ( (bool) $baseConfigs['maintenance'] && !SC::getUser()->isAdmin() )
        {
            $attributes = array(
                SC_RequestHandler::CATCH_ALL_REQUEST_KEY_CTRL => 'BASE_MCTRL_BaseDocument',
                SC_RequestHandler::CATCH_ALL_REQUEST_KEY_ACTION => 'maintenance',
                SC_RequestHandler::CATCH_ALL_REQUEST_KEY_REDIRECT => true
            );

            SC::getRequestHandler()->setCatchAllRequestsAttributes('base.maintenance_mode', $attributes);
            $this->addCatchAllRequestsException('base.maintenance_mode_exceptions', 'base.maintenance_mode');
        }


        try
        {
            SC::getRequestHandler()->dispatch();
        }
        catch ( RedirectException $e )
        {
            $this->redirect($e->getUrl(), $e->getRedirectCode());
        }
        catch ( InterceptException $e )
        {
            SC::getRequestHandler()->setHandlerAttributes($e->getHandlerAttrs());
            $this->handleRequest();
        }
    }

    /**
     * Method called just before request responding.
     */
    public function finalize()
    {
        $document = SC::getDocument();

        $meassages = SC::getFeedback()->getFeedback();

        foreach ( $meassages as $messageType => $messageList )
        {
            foreach ( $messageList as $message )
            {
                $document->addOnloadScript("SCM.message(" . json_encode($message) . ", '" . $messageType . "');");
            }
        }

        $event = new SC_Event(SC_EventManager::ON_FINALIZE);
        SC::getEventManager()->trigger($event);
    }

    /**
     * System method. Don't call it!!!
     */
    public function onBeforeDocumentRender()
    {
        $document = SC::getDocument();

        $document->addStyleSheet(SC::getPluginManager()->getPlugin('base')->getStaticCssUrl() . 'mobile.css' . '?' . SC::getConfig()->getValue('base',
                'cachedEntitiesPostfix'), 'all', -100);
        $document->addStyleSheet(SC::getThemeManager()->getCssFileUrl(true) . '?' . SC::getConfig()->getValue('base',
                'cachedEntitiesPostfix'), 'all', (-90));

        if ( SC::getThemeManager()->getCurrentTheme()->getDto()->getCustomCssFileName() !== null )
        {
            $document->addStyleSheet(SC::getThemeManager()->getThemeService()->getCustomCssFileUrl(SC::getThemeManager()->getCurrentTheme()->getDto()->getKey(),
                    true));
        }

        $language = SC::getLanguage();

        if ( $document->getTitle() === null )
        {
            $document->setTitle($language->text('mobile', 'page_default_title'));
        }

        if ( $document->getDescription() === null )
        {
            $document->setDescription($language->text('mobile', 'page_default_description'));
        }

        if ( $document->getHeading() === null )
        {
            $document->setHeading($language->text('mobile', 'page_default_heading'));
        }

        $document->addMetaInfo('viewport', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no');
    }

    public function activateMenuItem()
    {
        
    }

    protected function newDocument()
    {
        $language = BOL_LanguageService::getInstance()->getCurrent();
        $document = new SC_HtmlDocument();
        $document->setTemplate(SC::getThemeManager()->getMasterPageTemplate('mobile_html_document'));
        $document->setCharset('UTF-8');
        $document->setMime('text/html');
        $document->setLanguage($language->getTag());

        if ( $language->getRtl() )
        {
            $document->setDirection('rtl');
        }
        else
        {
            $document->setDirection('ltr');
        }

        if ( (bool) SC::getConfig()->getValue('base', 'favicon') )
        {
            $document->setFavicon(SC::getPluginManager()->getPlugin('base')->getUserFilesUrl() . 'favicon.ico');
        }

        $document->addScript(SC::getPluginManager()->getPlugin('base')->getStaticJsUrl() . 'jquery.min.js',
            'text/javascript', (-100));
        $document->addScript(SC::getPluginManager()->getPlugin('base')->getStaticJsUrl() . 'mobile.js?' . SC::getConfig()->getValue('base',
                'cachedEntitiesPostfix'), 'text/javascript', (-50));
        SC::getEventManager()->bind(SC_EventManager::ON_AFTER_REQUEST_HANDLE, array($this, 'onBeforeDocumentRender'));

        return $document;
    }

    protected function initRequestHandler()
    {
        SC::getRequestHandler()->setStaticPageAttributes('BASE_MCTRL_BaseDocument', 'staticDocument');
    }

    protected function findAllStaticDocs()
    {
        return BOL_NavigationService::getInstance()->findAllMobileStaticDocuments();
    }

    protected function findFirstMenuItem( $availableFor )
    {
        return BOL_NavigationService::getInstance()->findFirstLocal($availableFor, SC_Navigation::MOBILE_TOP);
    }

    protected function getSiteRootRoute()
    {
        return new SC_Route('base_default_index', '/', 'BASE_MCTRL_WidgetPanel', 'index');
    }

    protected function getMasterPage()
    {
        return new SC_MobileMasterPage();
    }
}
