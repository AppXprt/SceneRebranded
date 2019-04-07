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
class BASE_CTRL_SuspendedUser extends SC_ActionController
{

    public function index()
    {
        $this->assign('reason', BOL_UserService::getInstance()->getSuspendReason(SC::getUser()->getId()));
    }

    public function suspend( $params )
    {
        if ( !SC::getUser()->isAuthorized('base') || empty($params['id']) || empty($params['message']) )
        {
            exit;
        }

        $id = (int) $params['id'];
        $message = $params['message'];

        $userService = BOL_UserService::getInstance();
        $userService->suspend($id, $message);

        SC::getFeedback()->info(SC::getLanguage()->text('base', 'user_feedback_profile_suspended'));

        $this->redirect($_GET['backUrl']);
    }

    public function unsuspend( $params )
    {
        if ( !SC::getUser()->isAuthorized('base') || empty($params['id']) )
        {
            exit;
        }

        $id = (int) $params['id'];

        $userService = BOL_UserService::getInstance();
        $userService->unsuspend($id);

        SC::getFeedback()->info(SC::getLanguage()->text('base', 'user_feedback_profile_unsuspended'));

        $this->redirect($_GET['backUrl']);
    }

    public function ajaxRsp()
    {
        if ( !SC::getRequest()->isAjax() )
        {
            throw new Redirect403Exception();
        }

        $response = array();

        if ( empty($_GET['userId']) || empty($_GET['command']) )
        {
            echo json_encode($response);
            exit;
        }

        $userId = (int) $_GET['userId'];
        $command = $_GET['command'];

        switch ( $command )
        {
            case "suspend":
                BOL_UserService::getInstance()->suspend($userId);
                $response["info"] = SC::getLanguage()->text('base', 'user_feedback_profile_suspended');
                break;

            case "unsuspend":
                BOL_UserService::getInstance()->unsuspend($userId);
                $response["info"] = SC::getLanguage()->text('base', 'user_feedback_profile_unsuspended');
                break;
        }

        echo json_encode($response);
        exit;
    }
}