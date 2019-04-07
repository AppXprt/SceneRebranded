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
 * Edit user details
 *
 * @author Alex Ermashev <alexermashev@gmail.com>
 * @package sc_system_plugins.base.mobile.controllers
 * @since 1.8.6
 */
class BASE_MCTRL_Edit extends BASE_CTRL_Edit
{
    private $questionService;

    public function __construct()
    {
        parent::__construct();

        $this->questionService = BOL_QuestionService::getInstance();
    }

    /**
     * Index
     */
    public function index( $params )
    {
        $adminMode = false;
        $viewerId = SC::getUser()->getId();

        if (!SC::getUser()->isAuthenticated() || $viewerId === null) {
            throw new AuthenticateException();
        }

        // edit by a moderator
        if (!empty($params['userId']) && $params['userId'] != $viewerId) {

            if (SC::getUser()->isAdmin() || SC::getUser()->isAuthorized('base')) {
                $adminMode = true;
                $userId = (int)$params['userId'];
                $user = BOL_UserService::getInstance()->findUserById($userId);

                if (empty($user) || BOL_AuthorizationService::getInstance()->isSuperModerator($userId)) {
                    throw new Redirect404Exception();
                }

                $editUserId = $userId;
            } else {
                throw new Redirect403Exception();
            }
        } else {
            $editUserId = $viewerId;
            $user = SC::getUser()->getUserObject();
        }

        // get changes list
        $changeList = BOL_PreferenceService::getInstance()->getPreferenceValue(self::PREFERENCE_LIST_OF_CHANGES, $editUserId);

        if (empty($changeList)) {
            $changeList = '[]';
        }

        $this->assign('changeList', json_decode($changeList, true));

        $isEditedUserModerator = BOL_AuthorizationService::getInstance()->isModerator($editUserId)
            || BOL_AuthorizationService::getInstance()->isSuperModerator($editUserId);

        $accountType = $user->accountType;

        // display account type (allowed only for moderators)
        if (SC::getUser()->isAdmin() || SC::getUser()->isAuthorized('base')) {
            $accountType = !empty($_GET['accountType']) ? $_GET['accountType'] : $user->accountType;

            // get available account types from DB
            $accountTypes = BOL_QuestionService::getInstance()->findAllAccountTypes();

            $accounts = array();

            if (count($accountTypes) > 1) {
                /* @var $value BOL_QuestionAccount */
                foreach ($accountTypes as $key => $value) {
                    $accounts[$value->name] = SC::getLanguage()->text('base', 'questions_account_type_' . $value->name);
                }

                if (!in_array($accountType, array_keys($accounts))) {
                    if (in_array($user->accountType, array_keys($accounts))) {
                        $accountType = $user->accountType;
                    } else {
                        $accountType = BOL_QuestionService::getInstance()->getDefaultAccountType()->name;
                    }
                }

                $editAccountType = new Selectbox('accountType');
                $editAccountType->setId('accountType');
                $editAccountType->setLabel(SC::getLanguage()->text('base', 'questions_question_account_type_label'));
                $editAccountType->setRequired();
                $editAccountType->setOptions($accounts);
                $editAccountType->setHasInvitation(false);
            } else {
                $accountType = BOL_QuestionService::getInstance()->getDefaultAccountType()->name;
            }
        }

        $language = SC::getLanguage();

        $this->setPageTitle($language->text('mobile', 'edit_profile_page'));
        $this->setPageHeading($language->text('mobile', 'edit_profile_page'));

        // -- Edit form --

        $editForm = SC::getClassInstanceArray('EditQuestionForm', ['editForm', $editUserId]);
        $editForm->setId('editForm');
        $editForm->setEnctype('multipart/form-data');

        $this->assign('displayAccountType', false);

        // display account type changer
        if (!empty($editAccountType)) {
            $editAccountType->setValue($accountType);
            $editForm->addElement($editAccountType);

            SC::getDocument()->addOnloadScript(" $('#accountType').change(function() {

                var form = $(\"<form method='get'><input type='text' name='accountType' value='\" + $(this).val() + \"' /></form>\");
                $('body').append(form);
                $(form).submit();

            }  ); ");

            $this->assign('displayAccountType', true);
        }

        $userId = !empty($params['userId']) ? $params['userId'] : $viewerId;

        // add avatar field
        $avatarValidator = SC::getClassInstance("BASE_MCLASS_EditAvatarFieldValidator", false);
        $displayPhotoUpload = SC::getConfig()->getValue('base', 'join_display_photo_upload');

        if ($displayPhotoUpload == BOL_UserService::CONFIG_JOIN_DISPLAY_AND_SET_REQUIRED_PHOTO_UPLOAD) {
            $avatarValidator = SC::getClassInstance("BASE_MCLASS_EditAvatarFieldValidator", true, $userId);
        }

        $userAvatar = BOL_AvatarService::getInstance()->getAvatarUrl($userId, 1, null, true, false);

        $userPhoto = SC::getClassInstance("BASE_CLASS_JoinUploadPhotoField", 'userPhoto');
        $userPhoto->setLabel(SC::getLanguage()->text('base', 'questions_question_user_photo_label'));
        $userPhoto->addValidator($avatarValidator);
        $userPhoto->setValue($userAvatar);
        $editForm->addElement($userPhoto);

        $this->assign('avatarId', $editForm->getElement('userPhoto')->getId());

        $this->assign('avatarPreview', $userAvatar);
        $this->assign('requiredPhotoUpload', ($displayPhotoUpload == BOL_UserService::CONFIG_JOIN_DISPLAY_AND_SET_REQUIRED_PHOTO_UPLOAD));

        $isUserApproved = BOL_UserService::getInstance()->isApproved($editUserId);
        $this->assign('isUserApproved', $isUserApproved);

        // add submit button
        $editSubmit = new Submit('editSubmit');
        $editSubmit->addAttribute('class', 'sc_button sc_ic_save');

        $editSubmit->setValue($language->text('base', 'edit_button'));

        if ($adminMode && !$isUserApproved) {
            $editSubmit->setName('saveAndApprove');
            $editSubmit->setValue($language->text('base', 'save_and_approve'));
        }

        $editForm->addElement($editSubmit);

        // prepare question list
        $questions = $this->questionService->findEditQuestionsForAccountType($accountType);

        $section = null;
        $questionArray = array();
        $questionNameList = array();

        foreach ($questions as $sort => $question) {
            if ($section !== $question['sectionName']) {
                $section = $question['sectionName'];
            }

            $questionArray[$section][$sort] = $questions[$sort];
            $questionNameList[] = $questions[$sort]['name'];
        }

        $this->assign('questionArray', $questionArray);

        $questionData = $this->questionService->getQuestionData(array($editUserId), $questionNameList);

        $questionValues = $this->questionService->findQuestionsValuesByQuestionNameList($questionNameList);
        // add question to form
        $editForm->addQuestions($questions, $questionValues, !empty($questionData[$editUserId]) ? $questionData[$editUserId] : array());

        // process form
        if (SC::getRequest()->isPost()) {
            if (isset($_POST['editSubmit']) || isset($_POST['saveAndApprove'])) {
                $this->process($editForm, $user->id, $questionArray, $adminMode);
            }
        }

        $this->addForm($editForm);

        // add langs to js
        $language->addKeyForJs('base', 'join_error_username_not_valid');
        $language->addKeyForJs('base', 'join_error_username_already_exist');
        $language->addKeyForJs('base', 'join_error_email_not_valid');
        $language->addKeyForJs('base', 'join_error_email_already_exist');
        $language->addKeyForJs('base', 'join_error_password_not_valid');
        $language->addKeyForJs('base', 'join_error_password_too_short');
        $language->addKeyForJs('base', 'join_error_password_too_long');

        //include js
        $onLoadJs = " window.edit = new SC_BaseFieldValidators( " .
            json_encode(array(
                'formName' => $editForm->getName(),
                'responderUrl' => SC::getRouter()->urlFor("BASE_MCTRL_Edit", "ajaxResponder"))) . ",
                                                        " . UTIL_Validator::EMAIL_PATTERN . ", " . UTIL_Validator::USER_NAME_PATTERN . ", " . $editUserId . " ); ";

        $this->assign('validImageExtensions', json_encode(UTIL_File::$imageExtensions));
        $this->assign('isAdmin', SC::getUser()->isAdmin());
        $this->assign('isEditedUserModerator', $isEditedUserModerator);
        $this->assign('adminMode', $adminMode);
        $approveEnabled = SC::getConfig()->getValue('base', 'mandatory_user_approve');
        $this->assign('approveEnabled', $approveEnabled);

        SC::getDocument()->addOnloadScript($onLoadJs);

        $jsDir = SC::getPluginManager()->getPlugin("base")->getStaticJsUrl();
        SC::getDocument()->addScript($jsDir . "base_field_validators.js");

        $this->setTemplate(SC::getPluginManager()->getPlugin('base')->getMobileCtrlViewDir() . 'edit_index.html');
    }

    /**
     * Process form
     */
    private function process($editForm, $userId, $questionArray, $adminMode)
    {
        if ( $editForm->isValid($_POST) )
        {
            $language = SC::getLanguage();
            $data = $editForm->getValues();

            // process values
            foreach ( $questionArray as $section )
            {
                foreach ( $section as $key => $question )
                {
                    switch ( $question['presentation'] )
                    {
                        case 'multicheckbox':

                            if ( is_array($data[$question['name']]) )
                            {
                                $data[$question['name']] = array_sum($data[$question['name']]);
                            }
                            else
                            {
                                $data[$question['name']] = 0;
                            }

                            break;
                    }
                }
            }

            // save user data
            if ( !empty($userId) )
            {
                $displayPhotoUpload = SC::getConfig()->getValue('base', 'join_display_photo_upload');

                // delete old avatar
                if ( empty($_POST['userPhoto']) )
                {
                    if ( $displayPhotoUpload == BOL_UserService::CONFIG_JOIN_DISPLAY_AND_SET_REQUIRED_PHOTO_UPLOAD )
                    {
                        SC::getFeedback()->error($language->text('mobile', 'avatar_cannot_be_deleted'));

                        return;
                    }

                    BOL_AvatarService::getInstance()->deleteUserAvatar($userId);
                }

                // update avatar
                if ( !empty($_FILES['userPhoto']['tmp_name']) )
                {
                    if ( !UTIL_File::validateImage($_FILES['userPhoto']['name']) )
                    {
                        SC::getFeedback()->error($language->text('mobile', 'wrong_avatar_format'));

                        return;
                    }

                    $this->updateAvatar($userId);
                }

                $changesList = $this->questionService->getChangedQuestionList($data, $userId);
                if ( $this->questionService->saveQuestionsData($data, $userId) )
                {
                    if ( !$adminMode )
                    {
                        $isNeedToModerate = $this->questionService->isNeedToModerate($changesList);
                        $event = new SC_Event(SC_EventManager::ON_USER_EDIT, array('userId' => $userId, 'method' => 'native', 'moderate' => $isNeedToModerate));
                        SC::getEventManager()->trigger($event);

                        // saving changed fields
                        if ( BOL_UserService::getInstance()->isApproved($userId) )
                        {
                            $changesList = array();
                        }

                        BOL_PreferenceService::getInstance()->savePreferenceValue(self::PREFERENCE_LIST_OF_CHANGES, json_encode($changesList), $userId);
                        // ----

                        SC::getFeedback()->info($language->text('base', 'edit_successfull_edit'));
                        $this->redirect();
                    }
                    else
                    {
                        $event = new SC_Event(SC_EventManager::ON_USER_EDIT_BY_ADMIN, array('userId' => $userId));
                        SC::getEventManager()->trigger($event);

                        BOL_PreferenceService::getInstance()->savePreferenceValue(self::PREFERENCE_LIST_OF_CHANGES, json_encode(array()), $userId);

                        if ( !BOL_UserService::getInstance()->isApproved($userId) )
                        {
                            BOL_UserService::getInstance()->approve($userId);
                        }

                        SC::getFeedback()->info($language->text('base', 'edit_successfull_edit'));
                        $this->redirect(SC::getRouter()->urlForRoute('base_user_profile', array('username' => BOL_UserService::getInstance()->getUserName($userId))));
                    }
                }
                else
                {
                    SC::getFeedback()->info($language->text('base', 'edit_edit_error'));
                }
            }
            else
            {
                SC::getFeedback()->info($language->text('base', 'edit_edit_error'));
            }
        }
    }

    /**
     * Update avatar
     *
     * @param integer $userId
     * @return bool
     */
    protected function updateAvatar( $userId )
    {
        $avatarService = BOL_AvatarService::getInstance();

        $path = $_FILES['userPhoto']['tmp_name'];

        if ( !file_exists($path) )
        {
            return false;
        }

        $event = new SC_Event('base.before_avatar_change', array(
            'userId' => $userId,
            'avatarId' => null,
            'upload' => true,
            'crop' => false,
            'isModerable' => false
        ));
        SC::getEventManager()->trigger($event);

        $avatarSet = $avatarService->setUserAvatar($userId, $path, array('isModerable' => false, 'trackAction' => false ));

        if ( $avatarSet )
        {
            $avatar = $avatarService->findByUserId($userId);

            if ( $avatar )
            {
                $event = new SC_Event('base.after_avatar_change', array(
                    'userId' => $userId,
                    'avatarId' => $avatar->id,
                    'upload' => true,
                    'crop' => false
                ));
                SC::getEventManager()->trigger($event);
            }
        }

        return $avatarSet;
    }
}
