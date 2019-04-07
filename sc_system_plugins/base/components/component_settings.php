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
 * Widget Settings
 *
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package sc_system_plugins.base.components
 * @since 1.0
 */
class BASE_CMP_ComponentSettings extends SC_Component
{
    /**
     * Component default settings
     *
     * @var array
     */
    private $defaultSettingList = array();
    /**
     * Component default settings
     *
     * @var array
     */
    private $componentSettingList = array();
    private $standardSettingValueList = array();
    private $hiddenFieldList = array();
    private $access;

    private $uniqName;

    /**
     * Class constructor
     *
     * @param array $menuItems
     */
    public function __construct( $uniqName, array $componentSettings = array(), array $defaultSettings = array(), $access = null )
    {
        parent::__construct();

        $this->componentSettingList = $componentSettings;
        $this->defaultSettingList = $defaultSettings;
        $this->uniqName = $uniqName;
        $this->access = $access;
        
        $tpl = SC::getPluginManager()->getPlugin("base")->getCmpViewDir() . "component_settings.html";
        $this->setTemplate($tpl);
    }

    public function setStandardSettingValueList( $valueList )
    {
        $this->standardSettingValueList = $valueList;
    }

    protected function makeSettingList( $defaultSettingList )
    {
        $settingValues = $this->standardSettingValueList;
        foreach ( $defaultSettingList as $name => $value )
        {
            $settingValues[$name] = $value;
        }

        return $settingValues;
    }

    public function markAsHidden( $settingName )
    {
        $this->hiddenFieldList[] = $settingName;
    }

    /**
     * @see SC_Renderable::onBeforeRender()
     *
     */
    public function onBeforeRender()
    {
        $settingValues = $this->makeSettingList($this->defaultSettingList);

        $this->assign('values', $settingValues);

        $this->assign('avaliableIcons', IconCollection::allWithLabel());

        foreach ( $this->componentSettingList as $name => & $setting )
        {
            if ( $setting['presentation'] == BASE_CLASS_Widget::PRESENTATION_HIDDEN )
            {
                unset($this->componentSettingList[$name]);
                continue;
            }

            if ( isset($settingValues[$name]) )
            {
                $setting['value'] = $settingValues[$name];
            }

            if ( $setting['presentation'] == BASE_CLASS_Widget::PRESENTATION_CUSTOM )
            {
                $setting['markup'] = call_user_func($setting['render'], $this->uniqName, $name, empty($setting['value']) ? null : $setting['value']);
            }

            $setting['display'] = !empty($setting['display']) ? $setting['display'] : 'table';
        }

        $this->assign('settings', $this->componentSettingList);


        $authorizationService = BOL_AuthorizationService::getInstance();

        $roleList = array();
        $isModerator = SC::getUser()->isAuthorized('base');
        
        if ( $this->access == BASE_CLASS_Widget::ACCESS_GUEST || !$isModerator )
        {
            $this->markAsHidden(BASE_CLASS_Widget::SETTING_RESTRICT_VIEW);
        }
        else
        {
            $roleList = $authorizationService->findNonGuestRoleList();

            if ( $this->access == BASE_CLASS_Widget::ACCESS_ALL )
            {
                $guestRoleId = $authorizationService->getGuestRoleId();
                $guestRole = $authorizationService->getRoleById($guestRoleId);
                array_unshift($roleList, $guestRole);
            }
        }

        $this->assign('roleList', $roleList);

        $this->assign('hidden', $this->hiddenFieldList);
    }

}

class IconCollection
{
    private static $all = array(
        "sc_ic_add",
        "sc_ic_aloud",
        "sc_ic_app",
        "sc_ic_attach",
        "sc_ic_birthday",
        "sc_ic_bookmark",
        "sc_ic_calendar",
        "sc_ic_cart",
        "sc_ic_chat",
        "sc_ic_clock",
        "sc_ic_comment",
        "sc_ic_cut",
        "sc_ic_dashboard",
        "sc_ic_delete",
        "sc_ic_down_arrow",
        "sc_ic_edit",
        "sc_ic_female",
        "sc_ic_file",
        "sc_ic_files",
        "sc_ic_flag",
        "sc_ic_folder",
        "sc_ic_forum",
        "sc_ic_friends",
        "sc_ic_gear_wheel",
        "sc_ic_help",
        "sc_ic_heart",
        "sc_ic_house",
        "sc_ic_info",
        "sc_ic_key",
        "sc_ic_left_arrow",
        "sc_ic_lens",
        "sc_ic_link",
        "sc_ic_lock",
        "sc_ic_mail",
        "sc_ic_male",
        "sc_ic_mobile",
        "sc_ic_moderator",
        "sc_ic_monitor",
        "sc_ic_move",
        "sc_ic_music",
        "sc_ic_new",
        "sc_ic_ok",
        "sc_ic_online",
        "sc_ic_picture",
        "sc_ic_plugin",
        "sc_ic_push_pin",
        "sc_ic_reply",
        "sc_ic_right_arrow",
        "sc_ic_rss",
        "sc_ic_save",
        "sc_ic_script",
        "sc_ic_server",
        "sc_ic_star",
        "sc_ic_tag",
        "sc_ic_trash",
        "sc_ic_unlock",
        "sc_ic_up_arrow",
        "sc_ic_update",
        "sc_ic_user",
        "sc_ic_video",
        "sc_ic_warning",
        "sc_ic_write"
    );

    public static function all()
    {
        return self::$all;
    }

    public static function allWithLabel()
    {
        $out = array();

        foreach ( self::$all as $icon )
        {
            $item = array();
            $item['class'] = $icon;
            $item['label'] = ucfirst(str_replace('_', ' ', substr($icon, 6)));
            $out[] = $item;
        }

        return $out;
    }
}
