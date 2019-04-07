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
 * The class works with global menu system.
 * 
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package sc_core
 * @method static SC_Navigation getInstance()
 * @since 1.0
 */
class SC_Navigation
{
    const MAIN = BOL_NavigationService::MENU_TYPE_MAIN;
    const BOTTOM = BOL_NavigationService::MENU_TYPE_BOTTOM;
    
    const MOBILE_TOP = BOL_NavigationService::MENU_TYPE_MOBILE_TOP;
    const MOBILE_BOTTOM = BOL_NavigationService::MENU_TYPE_MOBILE_BOTTOM;
    const MOBILE_HIDDEN = BOL_NavigationService::MENU_TYPE_MOBILE_HIDDEN;
    
    const ADMIN_MOBILE = BOL_NavigationService::MENU_TYPE_MOBILE;
    const ADMIN_PLUGINS = BOL_NavigationService::MENU_TYPE_PLUGINS;
    const ADMIN_USERS = BOL_NavigationService::MENU_TYPE_USERS;
    const ADMIN_APPEARANCE = BOL_NavigationService::MENU_TYPE_APPEARANCE;
    const ADMIN_SETTINGS = BOL_NavigationService::MENU_TYPE_SETTINGS;
    const ADMIN_PAGES = BOL_NavigationService::MENU_TYPE_PAGES;
    const ADMIN_DASHBOARD = BOL_NavigationService::MENU_TYPE_ADMIN;
    

    const VISIBLE_FOR_GUEST = BOL_NavigationService::VISIBLE_FOR_GUEST;
    const VISIBLE_FOR_MEMBER = BOL_NavigationService::VISIBLE_FOR_MEMBER;
    const VISIBLE_FOR_ALL = BOL_NavigationService::VISIBLE_FOR_ALL;

    use SC_Singleton;
    
    /**
     * @var BOL_NavigationService
     */
    private $navService;

    /**
     * Constructor.
     */
    private function __construct()
    {
        $this->navService = BOL_NavigationService::getInstance();
    }

    /**
     * Adds menu items to global menu system.
     *
     * @param string $menuType
     * @param string $routeName
     * @param string $prefix
     * @param string $key
     * @param string $visibleFor
     */
    public function addMenuItem( $menuType, $routeName, $prefix, $key, $visibleFor = self::VISIBLE_FOR_ALL )
    {
        if ( empty($menuType) || empty($routeName) || empty($prefix) || empty($key) )
        {
            throw new InvalidArgumentException();
        }

        $menuType = trim($menuType);

        $order = $this->navService->findMaxSortOrderForMenuType($menuType);

        $menuItem = new BOL_MenuItem();
        $menuItem->setType($menuType);
        $menuItem->setRoutePath($routeName);
        $menuItem->setPrefix($prefix);
        $menuItem->setKey($key);
        $menuItem->setOrder(($order + 1));
        $menuItem->setVisibleFor($visibleFor);

        $this->navService->saveMenuItem($menuItem);
    }

    /**
     * Deletes menu item.
     *
     * @param string $prefix
     * @param string $key
     */
    public function deleteMenuItem( $prefix, $key )
    {
        $menuItem = $this->navService->findMenuItem($prefix, $key);

        if ( $menuItem !== null )
        {
            $this->navService->deleteMenuItem($menuItem);
        }
    }

    /**
     * Activates system menu items. 
     * 
     * @param string $menuType
     * @param string $prefix
     * @param string $key
     */
    public function activateMenuItem( $menuType, $prefix, $key )
    {
        if ( SC::getDocument()->getMasterPage() === null )
        {
            return;
        }

        $menu = SC::getDocument()->getMasterPage()->getMenu(trim($menuType));

        if ( $menu === null )
        {
            //trigger_error("Can't find menu in master page -  `" . $menuType . "`!", E_USER_WARNING);
            return;
        }

        $menuItem = $menu->getElement($key, $prefix);

        if ( $menuItem === null )
        {
            //trigger_error("Can't find menu item `" . $key . "` in menu `" . $menuType . "`!", E_USER_WARNING);
            return;
        }

        $menuItem->setActive(true);
    }

    /**
     * Deactivates all elements of provided menu.
     * @param string $menuType
     */
    public function deactivateMenuItems( $menuType )
    {
        $menu = SC::getDocument()->getMasterPage()->getMenu(trim($menuType));

        if ( $menu === null )
        {
            trigger_error("Can't find menu in master page -  `" . $menuType . "`!", E_USER_WARNING);
            return;
        }

        $menu->deactivateElements();
    }
}