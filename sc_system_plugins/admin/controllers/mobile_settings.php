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
 * Admin index controller class.
 *
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package sc_system_plugins.admin.controllers
 * @since 1.0
 */
class ADMIN_CTRL_MobileSettings extends ADMIN_CTRL_Abstract
{

    public function index()
    {
        $language = SC::getLanguage();
        $config = SC::getConfig();

        SC::getDocument()->setHeading(SC::getLanguage()->text('admin', 'heading_mobile_settings'));
        SC::getDocument()->setHeadingIconClass('sc_ic_gear_wheel');
        $settingsForm = new Form('mobile_settings');

        $disableMobile = new CheckboxField('disable_mobile');
        $disableMobile->setLabel($language->text('admin', 'mobile_settings_mobile_context_disable_label'));
        $disableMobile->setDescription($language->text('admin', 'mobile_settings_mobile_context_disable_desc'));
        $settingsForm->addElement($disableMobile);
        
        $submit = new Submit('save');
        $submit->setValue($language->text('admin', 'save_btn_label'));
        $settingsForm->addElement($submit);
        
        $this->addForm($settingsForm);
        
        if ( SC::getRequest()->isPost() )
        {
            if ( $settingsForm->isValid($_POST) )
            {
                $data = $settingsForm->getValues();

                $config->saveConfig('base', 'disable_mobile_context', (bool) $data['disable_mobile']);
                SC::getFeedback()->info($language->text('admin', 'settings_submit_success_message'));
            }
            else
            {
                SC::getFeedback()->error('Error');
            }

            $this->redirect();
        }

        $disableMobile->setValue($config->getValue('base', 'disable_mobile_context'));
    }
}
