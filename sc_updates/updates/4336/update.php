<?php

if ( !file_exists(SC_DIR_USERFILES . 'plugins' . DS . 'base' . DS . 'favicon.ico') )
{
    @copy(SC_DIR_STATIC . 'favicon.ico', SC_DIR_USERFILES . 'plugins' . DS . 'base' . DS . 'favicon.ico');
}


UPDATE_LanguageService::getInstance()->importPrefixFromZip(dirname(__FILE__) . DS . 'langs.zip', 'base');
