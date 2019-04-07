<?php

class BASE_CMP_InvitationItem extends BASE_CMP_ConsoleListIpcItem
{
    public function __construct()
    {
        parent::__construct();

        $plugin = SC::getPluginManager()->getPlugin('BASE');
        $this->setTemplate($plugin->getCmpViewDir() . 'console_list_ipc_item.html');

        $this->addClass('sc_invitation_item sc_cursor_default');
    }
}