<?php

class BASE_CMP_ConsoleInvitations extends BASE_CMP_ConsoleDropdownList
{
    public function __construct()
    {
        $label = SC::getLanguage()->text('base', 'console_item_invitations_label');

        parent::__construct( $label, 'invitation' );

        $this->addClass('sc_invitation_list');
    }

    public function initJs()
    {
        parent::initJs();

        $js = UTIL_JsGenerator::newInstance();
        $js->addScript('SC.Invitation = new SC_Invitation({$key}, {$params});', array(
            'key' => $this->getKey(),
            'params' => array(
                'rsp' => SC::getRouter()->urlFor('BASE_CTRL_Invitation', 'ajax')
            )
        ));
        
        SC::getDocument()->addOnloadScript($js);
    }
}