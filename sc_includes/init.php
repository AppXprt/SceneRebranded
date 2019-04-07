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
require_once SC_DIR_ROOT . 'sc_includes/config.php';
require_once SC_DIR_ROOT . 'sc_includes/define.php';
require_once SC_DIR_UTIL . 'debug.php';
require_once SC_DIR_UTIL . 'string.php';
require_once SC_DIR_CORE . 'autoload.php';
require_once SC_DIR_CORE . 'exception.php';
require_once SC_DIR_INC . 'function.php';
require_once SC_DIR_CORE . 'sc.php';
require_once SC_DIR_CORE . 'plugin.php';
require_once SC_DIR_CORE . 'filter.php';

mb_internal_encoding('UTF-8');

if ( SC_DEBUG_MODE )
{
    ob_start();
}

spl_autoload_register(array('SC_Autoload', 'autoload'));
require_once SC_DIR_LIB_VENDOR . "autoload.php";

// adding standard package pointers
$autoloader = SC::getAutoloader();
$autoloader->addPackagePointer('SC', SC_DIR_CORE);
$autoloader->addPackagePointer('INC', SC_DIR_INC);
$autoloader->addPackagePointer('UTIL', SC_DIR_UTIL);
$autoloader->addPackagePointer('BOL', SC_DIR_SYSTEM_PLUGIN . 'base' . DS . 'bol');

// Force autoload of classes without package pointer
$classesToAutoload = array(
    'Form' => SC_DIR_CORE . 'form.php',
    'TextField' => SC_DIR_CORE . 'form_element.php',
    'HiddenField' => SC_DIR_CORE . 'form_element.php',
    'FormElement' => SC_DIR_CORE . 'form_element.php',
    'RequiredValidator' => SC_DIR_CORE . 'validator.php',
    'StringValidator' => SC_DIR_CORE . 'validator.php',
    'RegExpValidator' => SC_DIR_CORE . 'validator.php',
    'EmailValidator' => SC_DIR_CORE . 'validator.php',
    'UrlValidator' => SC_DIR_CORE . 'validator.php',
    'AlphaNumericValidator' => SC_DIR_CORE . 'validator.php',
    'IntValidator' => SC_DIR_CORE . 'validator.php',
    'InArrayValidator' => SC_DIR_CORE . 'validator.php',
    'FloatValidator' => SC_DIR_CORE . 'validator.php',
    'DateValidator' => SC_DIR_CORE . 'validator.php',
    'CaptchaValidator' => SC_DIR_CORE . 'validator.php',
    'RadioField' => SC_DIR_CORE . 'form_element.php',
    'CheckboxField' => SC_DIR_CORE . 'form_element.php',
    'Selectbox' => SC_DIR_CORE . 'form_element.php',
    'CheckboxGroup' => SC_DIR_CORE . 'form_element.php',
    'RadioField' => SC_DIR_CORE . 'form_element.php',
    'PasswordField' => SC_DIR_CORE . 'form_element.php',
    'Submit' => SC_DIR_CORE . 'form_element.php',
    'Button' => SC_DIR_CORE . 'form_element.php',
    'Textarea' => SC_DIR_CORE . 'form_element.php',
    'FileField' => SC_DIR_CORE . 'form_element.php',
    'TagsField' => SC_DIR_CORE . 'form_element.php',
    'SuggestField' => SC_DIR_CORE . 'form_element.php',
    'MultiFileField' => SC_DIR_CORE . 'form_element.php',
    'Multiselect' => SC_DIR_CORE . 'form_element.php',
    'CaptchaField' => SC_DIR_CORE . 'form_element.php',
    'InvitationFormElement' => SC_DIR_CORE . 'form_element.php',
    'Range' => SC_DIR_CORE . 'form_element.php',
    'WyswygRequiredValidator' => SC_DIR_CORE . 'validator.php',
    'DateField' => SC_DIR_CORE . 'form_element.php',
    'DateRangeInterface' => SC_DIR_CORE . 'form_element.php'
);

SC::getAutoloader()->addClassArray($classesToAutoload);

if ( defined("SC_URL_HOME") )
{
    SC::getRouter()->setBaseUrl(SC_URL_HOME);
}

if ( SC_PROFILER_ENABLE )
{
    UTIL_Profiler::getInstance();
}

require_once SC_DIR_SYSTEM_PLUGIN . 'base' . DS . 'classes' . DS . 'file_log_writer.php';
require_once SC_DIR_SYSTEM_PLUGIN . 'base' . DS . 'classes' . DS . 'db_log_writer.php';
require_once SC_DIR_SYSTEM_PLUGIN . 'base' . DS . 'classes' . DS . 'err_output.php';

$errorManager = SC_ErrorManager::getInstance(SC_DEBUG_MODE);
$errorManager->setErrorOutput(new BASE_CLASS_ErrOutput());
