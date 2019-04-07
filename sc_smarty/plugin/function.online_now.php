<?php

function smarty_function_online_now( $params, $smarty )
{
    $chatNowMarkup = '';
    if ( SC::getUser()->isAuthenticated() && isset($params['userId']) && SC::getUser()->getId() != $params['userId'])
    {
        $allowChat = SC::getEventManager()->call('base.online_nsc_click', array('userId'=>SC::getUser()->getId(), 'onlineUserId'=>$params['userId']));

        if ($allowChat)
        {
            $chatNowMarkup = '<span id="sc_chat_nsc_'.$params['userId'].'" class="sc_lbutton sc_green" onclick="SC.trigger(\'base.online_nsc_click\', [ \'' . $params['userId'] . '\' ] );" >' . SC::getLanguage()->text('base', 'user_list_chat_now') . '</span><span id="sc_preloader_content_'.$params['userId'].'" class="sc_preloader_content sc_hidden"></span>';
        }
    }

    $buttonMarkup = '<div class="sc_miniic_live"><span class="sc_live_on"></span>'.$chatNowMarkup.'</div>';

    return $buttonMarkup;
}
?>
