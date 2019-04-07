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
 * Widget
 *
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package sc_system_plugins.base.classes
 * @since 1.0
 */
abstract class BASE_CLASS_Widget extends SC_Component
{
    const ACCESS_GUEST = 'guest';
    const ACCESS_MEMBER = 'member';
    const ACCESS_ALL = 'all';

    const SETTING_TITLE = 'title';
    const SETTING_WRAP_IN_BOX = 'wrap_in_box';
    const SETTING_SHOW_TITLE = 'shsc_title';
    const SETTING_ICON = 'icon';
    const SETTING_TOOLBAR = 'toolbar';
    const SETTING_CAP_CONTENT = 'capContent';
    const SETTING_FREEZE = 'freeze';
    const SETTING_AVALIABLE_SECTIONS = 'avaliable_sections';
    const SETTING_ACCESS_RESTRICTIONS = 'access_restrictions';
    const SETTING_RESTRICT_VIEW = 'restrict_view';

    const PRESENTATION_NUMBER = 'number';
    const PRESENTATION_TEXT = 'text';
    const PRESENTATION_TEXTAREA = 'textarea';
    const PRESENTATION_CHECKBOX = 'checkbox';
    const PRESENTATION_SELECT = 'select';
    const PRESENTATION_HIDDEN = 'hidden';
    const PRESENTATION_CUSTOM = 'custom';

    const ICON_ADD = "sc_ic_add";
    const ICON_ALOUD = "sc_ic_aloud";
    const ICON_APP = "sc_ic_app";
    const ICON_ATTACH = "sc_ic_attach";
    const ICON_BIRTHDAY = "sc_ic_birthday";
    const ICON_BOOKMARK = "sc_ic_bookmark";
    const ICON_CALENDAR = "sc_ic_calendar";
    const ICON_CART = "sc_ic_cart";
    const ICON_CHAT = "sc_ic_chat";
    const ICON_CLOCK = "sc_ic_clock";
    const ICON_COMMENT = "sc_ic_comment";
    const ICON_CUT = "sc_ic_cut";
    const ICON_DASHBOARD = "sc_ic_dashboard";
    const ICON_DELETE = "sc_ic_delete";
    const ICON_DSCN_ARRSC = "sc_ic_down_arrow";
    const ICON_EDIT = "sc_ic_edit";
    const ICON_FEMALE = "sc_ic_female";
    const ICON_FILE = "sc_ic_file";
    const ICON_FILES = "sc_ic_files";
    const ICON_FLAG = "sc_ic_flag";
    const ICON_FOLDER = "sc_ic_folder";
    const ICON_FORUM = "sc_ic_forum";
    const ICON_FRIENDS = "sc_ic_friends";
    const ICON_GEAR_WHEEL = "sc_ic_gear_wheel";
    const ICON_HEART = "sc_ic_heart";
    const ICON_HELP = "sc_ic_help";
    const ICON_HOUSE = "sc_ic_house";
    const ICON_INFO = "sc_ic_info";
    const ICON_KEY = "sc_ic_key";
    const ICON_LEFT_ARRSC = "sc_ic_left_arrow";
    const ICON_LENS = "sc_ic_lens";
    const ICON_LINK = "sc_ic_link";
    const ICON_LOCK = "sc_ic_lock";
    const ICON_MAIL = "sc_ic_mail";
    const ICON_MALE = "sc_ic_male";
    const ICON_MOBILE = "sc_ic_mobile";
    const ICON_MODERATOR = "sc_ic_moderator";
    const ICON_MONITOR = "sc_ic_monitor";
    const ICON_MOVE = "sc_ic_move";
    const ICON_MUSIC = "sc_ic_music";
    const ICON_NEW = "sc_ic_new";
    const ICON_OK = "sc_ic_ok";
    const ICON_ONLINE = "sc_ic_online";
    const ICON_PICTURE = "sc_ic_picture";
    const ICON_PLUGIN = "sc_ic_plugin";
    const ICON_PUSH_PIN = "sc_ic_push_pin";
    const ICON_REPLY = "sc_ic_reply";
    const ICON_RIGHT_ARRSC = "sc_ic_right_arrow";
    const ICON_RSS = "sc_ic_rss";
    const ICON_SAVE = "sc_ic_save";
    const ICON_SCRIPT = "sc_ic_script";
    const ICON_SERVER = "sc_ic_server";
    const ICON_STAR = "sc_ic_star";
    const ICON_TAG = "sc_ic_tag";
    const ICON_TRASH = "sc_ic_trash";
    const ICON_UNLOCK = "sc_ic_unlock";
    const ICON_UP_ARRSC = "sc_ic_up_arrow";
    const ICON_UPDATE = "sc_ic_update";
    const ICON_USER = "sc_ic_user";
    const ICON_VIDEO = "sc_ic_video";
    const ICON_WARNING = "sc_ic_warning";
    const ICON_WRITE = "sc_ic_write";


    private static $placeData = array();

    final public static function getPlaceData()
    {
        return self::$placeData;
    }

    final public static function setPlaceData( $placeData )
    {
        self::$placeData = $placeData;
    }



    public static function getSettingList()
    {
        return array();
    }

    public static function validateSettingList( $settingList )
    {

    }

    public static function processSettingList( $settingList, $place, $isAdmin )
    {
        if ( isset($settingList['title']) )
        {
            $settingList['title'] = UTIL_HtmlTag::stripJs($settingList['title']);
        }

        return $settingList;
    }

    public static function getStandardSettingValueList()
    {
        return array();
    }

    public static function getAccess()
    {
        return self::ACCESS_ALL;
    }

    private $runtimeSettings = array();

    public function setSettingValue( $setting, $value )
    {
        $this->runtimeSettings[$setting] = $value;
    }

    public function getRunTimeSettingList()
    {
        return $this->runtimeSettings;
    }
}

class WidgetSettingValidateException extends Exception
{
    private $fieldName;

    public function __construct( $message, $fieldName = null )
    {
        parent::__construct($message);

        $this->fieldName = trim($fieldName);
    }

    public function getFieldName()
    {
        return $this->fieldName;
    }
}
