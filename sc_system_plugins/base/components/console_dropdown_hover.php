<?php

class BASE_CMP_ConsoleDropdownHover extends BASE_CMP_ConsoleDropdown
{
    protected $url = 'javascript://';

    public function __construct($label, $key = null)
    {
        parent::__construct($label, $key);

        $template = SC::getPluginManager()->getPlugin('BASE')->getCmpViewDir() . 'console_dropdown_hover.html';
        $this->setTemplate($template);

        $this->addClass('sc_console_dropdown_hover');
    }

    protected function initJs()
    {
        $js = UTIL_JsGenerator::newInstance();
        $js->addScript('SC.Console.addItem(new SC_ConsoleDropdownHover({$uniqId}, {$contentIniqId}), {$key});', array(
            'key' => $this->getKey(),
            'uniqId' => $this->consoleItem->getUniqId(),
            'contentIniqId' => $this->consoleItem->getContentUniqId()
        ));

        SC::getDocument()->addOnloadScript($js);
    }

    public function setUrl( $url )
    {
        $this->url = $url;
    }

    public function onBeforeRender()
    {
        parent::onBeforeRender();

        $this->assign('url', $this->url);
    }
}