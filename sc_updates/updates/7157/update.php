<?php

$tblPrefix = SC_DB_PREFIX;
$db = Updater::getDbo();
$logger = Updater::getLogger();

$queryList = array();
$queryList[] = "ALTER TABLE  `{$tblPrefix}base_mail` ADD  `sent` BOOLEAN NOT NULL DEFAULT  '0' ";

foreach ( $queryList as $query )
{
    try
    {
        $db->query($query);
    }
    catch ( Exception $e )
    {
        $logger->addEntry(json_encode($e));
    }
}

UPDATE_LanguageService::getInstance()->importPrefixFromZip(dirname(__FILE__) . DS . 'langs.zip', 'base');

if ( SC::getConfig()->configExists("base", "install_complete") )
{
    SC::getConfig()->saveConfig("base", "install_complete", 1);
}
else
{
    SC::getConfig()->addConfig("base", "install_complete", 1);
}