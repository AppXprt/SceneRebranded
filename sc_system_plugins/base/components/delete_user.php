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
 * @package sc_system_plugins.base.components
 * @since 1.0
 */
class BASE_CMP_DeleteUser extends SC_Component
{

    /**
     * Constructor.
     */
    public function __construct( $params = array() )
    {
        parent::__construct();

        $userId = (int) $params['userId'];
        $showMessage = (bool) $params['showMessage'];

        $rspUrl = SC::getRouter()->urlFor('BASE_CTRL_User', 'deleteUser', array(
            'user-id' => $userId
        ));

        $rspUrl = SC::getRequest()->buildUrlQueryString($rspUrl, array(
            'showMessage' => (int) $showMessage
        ));

        $js = UTIL_JsGenerator::composeJsString('$("#baseDCButton").click(function()
        {
            var button = this;

            SC.inProgressNode(button);

            $.getJSON({$rsp}, function(r)
            {
                SC.activateNode(button);

                if ( _scope.floatBox )
                {
                    _scope.floatBox.close();
                }

                if ( _scope.deleteCallback )
                {
                    _scope.deleteCallback(r);
                }
            });
        });', array(
            'rsp' => $rspUrl
        ));

        SC::getDocument()->addOnloadScript($js);
    }
}