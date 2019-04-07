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
 * @author Sergei Kiselev <arrserg@gmail.com>
 * @package sc_system_plugins.admin.components
 * @since 1.7.5
 */
class ADMIN_CMP_UploadedFilesBulkOptions extends SC_Component
{

    public function __construct()
    {
        parent::__construct();

    }

    private function assignUniqidVar($name)
    {
        $showId = uniqid($name);
        $this->assign($name, $showId);
        return $showId;
    }

    public function onBeforeRender()
    {
        parent::onBeforeRender();
        $showId = $this->assignUniqidVar('showId');
        $deleteId = $this->assignUniqidVar('deleteId');
        $backId = $this->assignUniqidVar('backId');
        $containerId = $this->assignUniqidVar('containerId');

        SC::getDocument()->addOnloadScript("
            ;function exitBulkOptions(){
                $('#{$containerId}').fadeOut(function(){
                    $('#{$showId}').parent().parent().fadeIn();
                    $(this).parents('.sc_fw_menu').find('.sc_admin_date_filter').fadeIn();
                    $('.sc_photo_context_action').show();
                    $('.sc_photo_item .sc_photo_chekbox_area').hide();
                });
            }
            $('#{$deleteId}').click(function(){
                var deleteIds = [];

                $('.sc_photo_item.sc_photo_item_checked').each(function(){
                    deleteIds.push($(this).closest('.sc_photo_item_wrap').data('photoId'));
                });
                photoContextAction.deleteImages(deleteIds);
                exitBulkOptions();
            });
            $('#{$showId}').click(function(){
                $('.sc_photo_item.sc_photo_item_checked').toggleClass('sc_photo_item_checked');
                $(this).parents('.sc_fw_menu').find('.sc_admin_date_filter').fadeOut();
                $('#{$showId}').parent().parent().fadeOut(function(){
                    $('#{$containerId}').fadeIn();
                    $('.sc_photo_context_action').hide();
                    $('.sc_photo_item .sc_photo_chekbox_area').show();
                });
            });
            $('#{$backId}').click(function(){
                exitBulkOptions();
            });
            $('.sc_photo_list').on('click', '.sc_photo_checkbox, .sc_photo_chekbox_area', function(e){
                e.stopPropagation();
                $(this).parents('.sc_photo_item').toggleClass('sc_photo_item_checked');
            });"

        );
    }
}
