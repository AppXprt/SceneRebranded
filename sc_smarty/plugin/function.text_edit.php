<?php

function smarty_function_text_edit( $params, $smarty )
{
    $key = $params['key'];
    unset($params['key']);

    $key = explode('+', $key);

    if ( empty($key[0]) || empty($key[1]) )
    {
        return '_INVALID_KEY_';
    }
    
    $prefix = $key[0];
    $key = $key[1];
    
    $text = SC::getLanguage()->text($prefix, $key, $params);
    
    $keyDto = BOL_LanguageService::getInstance()->findKey($prefix, $key);
    
    if ( !$keyDto )
    {
        return '<span class="sc_red">' . $text . '</span>';
    }
    
    $script = '$("a.sc_text_edit").click(function(){
        var self=$(this), lang = this.rel.split("+");
        SC.editLanguageKey(lang[0],lang[1], function(e){
            self.text(e.value);     
        });
    });';
    
    SC::getDocument()->addOnloadScript($script);
    
    $rel = json_encode($prefix . '+' . $key);
    
    return '<a href="javascript://" rel='.$rel.' class="sc_text_edit">' . $text . '</a>';
}
