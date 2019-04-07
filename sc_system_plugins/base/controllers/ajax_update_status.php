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
 * @author Aybat Duyshokov <duyshokov@gmail.com>
 * @package sc_system_plugins.base.controllers
 * @since 1.0
 */
class BASE_CTRL_AjaxUpdateStatus extends SC_ActionController
{

    function update()
    {
        $service = UserStatusService::getInstance();

        $userId = SC::getUser()->getId();

        if ( empty($userId) || empty($_POST['status']) )
        {
            exit('{}');
        }

        if ( !($status = $service->findByUserId($userId)) )
        {
            $status = new UserStatus();
            $status->setUserId($userId);
        }

        $statusContent = htmlspecialchars($_POST['status']);
        $status->setStatus($statusContent);

        $service->save($status);

        if ( SC::getPluginManager()->isPluginActive('activity') && trim($status->getStatus()) !== '' )
        {
            $action = new ACTIVITY_BOL_Action();

            $data = array(
                'string' => SC::getLanguage()->text('user_status', 'activity_string',
                    array(
                        'actor' => BOL_UserService::getInstance()->getDisplayName($status->getUserId()),
                        'actorUrl' => BOL_UserService::getInstance()->getUserUrl($status->getUserId()),
                        'status' => $status->getStatus()
                    )
                ),
                'content_comment' => '',
            );

            $action->setUserId($status->getUserId())
                ->setTimestamp(time())
                ->setType('status-update')
                ->setEntityId($status->getUserId())
                ->setData($data);

            ACTIVITY_BOL_Service::getInstance()->addAction($action);
        }

        exit(json_encode(array(
                'result' => 'success',
                'js' => 'SC.info("' . SC::getLanguage()->text('user_status', 'updated') . '")'
            )));
    }
}