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
class BASE_MCTRL_BaseDocument extends SC_MobileActionController
{

    public function page404()
    {
        SC::getResponse()->setHeader('HTTP/1.0', '404 Not Found');
        SC::getResponse()->setHeader('Status', '404 Not Found');
        $this->setPageHeading(SC::getLanguage()->text('base', 'base_document_404_heading'));
        $this->setPageTitle(SC::getLanguage()->text('base', 'base_document_404_title'));
        $this->setDocumentKey('base_page404');
        $this->assign('message', SC::getLanguage()->text('mobile', 'page_is_not_available', array('url' => SC::getRouter()->urlForRoute('base.desktop_version'))));
    }

    public function page403( array $params )
    {
        $language = SC::getLanguage();
        SC::getResponse()->setHeader('HTTP/1.0', '403 Forbidden');
        SC::getResponse()->setHeader('Status', '403 Forbidden');
        $this->setPageHeading($language->text('base', 'base_document_403_heading'));
        $this->setPageTitle($language->text('base', 'base_document_403_title'));
        $this->setDocumentKey('base_page403');
        $this->assign('message', !empty($params['message']) ? $params['message'] : $language->text('base', 'base_document_403'));
    }

    public function redirectToDesktop()
    {
        $urlToRedirect = SC::getRouter()->getBaseUrl();

        if ( !empty($_GET['back-uri']) )
        {
            $urlToRedirect .= urldecode($_GET['back-uri']);
        }

        SC::getApplication()->redirect($urlToRedirect, SC::CONTEXT_DESKTOP);
    }

    public function staticDocument( $params )
    {
        $navService = BOL_NavigationService::getInstance();

        if ( empty($params['documentKey']) )
        {
            throw new Redirect404Exception();
        }

        $language = SC::getLanguage();
        $documentKey = $params['documentKey'];

        $document = $navService->findDocumentByKey($documentKey);
        
        if ( $document === null )
        {
            throw new Redirect404Exception();
        }

        $menuItem = $navService->findMenuItemByDocumentKey($document->getKey());

        if ( $menuItem !== null )
        {
            if ( !$menuItem->getVisibleFor() || ( $menuItem->getVisibleFor() == BOL_NavigationService::VISIBLE_FOR_GUEST && SC::getUser()->isAuthenticated() ) )
            {
                throw new Redirect403Exception();
            }

            if ( $menuItem->getVisibleFor() == BOL_NavigationService::VISIBLE_FOR_MEMBER && !SC::getUser()->isAuthenticated() )
            {
                throw new AuthenticateException();
            }
        }

        $settings = BOL_MobileNavigationService::getInstance()->getItemSettings($menuItem);
        $title = $settings[BOL_MobileNavigationService::SETTING_TITLE];


        $this->assign('content', $settings[BOL_MobileNavigationService::SETTING_CONTENT]);
        $this->setPageHeading($settings[BOL_MobileNavigationService::SETTING_TITLE]);
        $this->setPageTitle($settings[BOL_MobileNavigationService::SETTING_TITLE]);
        $this->setDocumentKey($document->getKey());

        //SC::getEventManager()->bind(SC_EventManager::ON_BEFORE_DOCUMENT_RENDER, array($this, 'setCustomMetaInfo'));
    }

    public function maintenance()
    {
        if ( !SC::getRequest()->isAjax() )
        {
            SC::getDocument()->getMasterPage()->setTemplate(SC::getThemeManager()->getMasterPageTemplate(SC_MobileMasterPage::TEMPLATE_BLANK));
        }
        else
        {
            exit('{}');
        }
    }

    public function splashScreen()
    {
        if ( isset($_GET['agree']) )
        {
            setcookie('splashScreen', 1, time() + 3600 * 24 * 30, '/');
            $url = SC_URL_HOME;
            $url .= isset($_GET['back_uri']) ? $_GET['back_uri'] : '';
            $this->redirect($url);
        }

        SC::getDocument()->getMasterPage()->setTemplate(SC::getThemeManager()->getMasterPageTemplate(SC_MobileMasterPage::TEMPLATE_BLANK));
        $this->assign('submit_url', SC::getRequest()->buildUrlQueryString(null, array('agree' => 1)));

        $leaveUrl = SC::getConfig()->getValue('base', 'splash_leave_url');

        if ( !empty($leaveUrl) )
        {
            $this->assign('leaveUrl', $leaveUrl);
        }
    }

    public function passwordProtection()
    {
        $language = SC::getLanguage();

        $form = new Form('password_protection');
        $form->setAjax(true);
        $form->setAction(SC::getRouter()->urlFor('BASE_CTRL_BaseDocument', 'passwordProtection'));
        $form->setAjaxDataType(Form::AJAX_DATA_TYPE_SCRIPT);

        $password = new PasswordField('password');
        $form->addElement($password);

        $submit = new Submit('submit');
        $submit->setValue(SC::getLanguage()->text('base', 'password_protection_submit_label'));
        $form->addElement($submit);
        $this->addForm($form);

        if ( SC::getRequest()->isAjax() && $form->isValid($_POST) )
        {
            $data = $form->getValues();
            $password = SC::getConfig()->getValue('base', 'guests_can_view_password');
            $cryptedPassword = crypt($data['password'], SC_PASSWORD_SALT);

            if ( !empty($data['password']) && $cryptedPassword === $password )
            {
                setcookie('base_password_protection', UTIL_String::getRandomString(), (time() + 86400 * 30), '/');
                echo "SC.info('" . SC::getLanguage()->text('base', 'password_protection_success_message') . "');window.location.reload();";
            }
            else
            {
                echo "SC.error('" . SC::getLanguage()->text('base', 'password_protection_error_message') . "');";
            }
            exit;
        }

        SC::getDocument()->setHeading($language->text('base', 'password_protection_text'));
        SC::getDocument()->getMasterPage()->setTemplate(SC::getThemeManager()->getMasterPageTemplate(SC_MobileMasterPage::TEMPLATE_BLANK));
    }

    public function notAvailable()
    {
        $this->assign('message', SC::getLanguage()->text('mobile', 'page_is_not_available', array('url' => SC::getRouter()->urlForRoute('base.desktop_version'))));
    }

    public function authorizationFailed( array $params )
    {
        $language = SC::getLanguage();
        $this->setPageHeading($language->text('base', 'base_document_auth_failed_heading'));
        $this->setPageTitle($language->text('base', 'base_document_auth_failed_heading'));
        $this->setTemplate(SC::getPluginManager()->getPlugin('base')->getMobileCtrlViewDir() . 'authorization_failed.html');
        $this->assign('message', !empty($params['message']) ? $params['message'] : null);
    }
}
