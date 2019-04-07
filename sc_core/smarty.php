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
//require_once(SC_DIR_LIB . 'smarty3' . DS . 'Smarty.class.php');


/**
 * Smarty class.
 *
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package sc_core
 * @since 1.0
 */
class SC_Smarty extends Smarty
{

    public function __construct()
    {
        parent::__construct();

        $this->compile_check = false;
        $this->force_compile = false;
        $this->caching = false;
        $this->debugging = false;

        if ( SC_DEV_MODE )
        {
            $this->compile_check = true;
            $this->force_compile = true;
        }

        $this->cache_dir = SC_DIR_SMARTY . 'cache' . DS;
        $this->compile_dir = SC_DIR_SMARTY . 'template_c' . DS;
        $this->addPluginsDir(SC_DIR_SMARTY . 'plugin' . DS);
        $this->enableSecurity('SC_Smarty_Security');
    }
}

class SC_Smarty_Security extends Smarty_Security
{

    public function __construct( $smarty )
    {
        parent::__construct($smarty);
        $this->secure_dir = array(SC_DIR_THEME, SC_DIR_SYSTEM_PLUGIN, SC_DIR_PLUGIN);
        $this->php_functions = array('array', 'list', 'isset', 'empty', 'count', 'sizeof', 'in_array', 'is_array', 'true', 'false', 'null', 'strstr');
        $this->php_modifiers = array('count');
        $this->allsc_constants = false;
        $this->allsc_super_globals = false;
        $this->static_classes = null;
    }
}