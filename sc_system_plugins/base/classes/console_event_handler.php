<?php

class BASE_CLASS_ConsoleEventHandler
{
    /**
     * Class instance
     *
     * @var BASE_CLASS_ConsoleEventHandler
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return BASE_CLASS_ConsoleEventHandler
     */
    public static function getInstance()
    {
        if ( !isset(self::$classInstance) )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    public function collectItems( BASE_CLASS_ConsoleItemCollector $event )
    {
        $language = SC::getLanguage();
        $router = SC::getRouter();

        if ( SC::getUser()->isAuthenticated() )
        {
            // Admin menu
            if ( SC::getUser()->isAdmin() )
            {
                $item = new BASE_CMP_ConsoleDropdownMenu($language->text('admin', 'main_menu_admin'));
                $item->setUrl($router->urlForRoute('admin_default'));
                $item->addItem('head', array('label' => $language->text('admin', 'console_item_admin_dashboard'), 'url' => $router->urlForRoute('admin_default')));
                $item->addItem('main', array('label' => $language->text('admin', 'console_item_manage_theme'), 'url' => $router->urlForRoute('admin_themes_edit')));
                $item->addItem('main', array('label' => $language->text('admin', 'console_item_manage_users'), 'url' => $router->urlForRoute('admin_users_browse')));
                $item->addItem('main', array('label' => $language->text('admin', 'console_item_manage_pages'), 'url' => $router->urlForRoute('admin_pages_main')));
                $item->addItem('main', array('label' => $language->text('admin', 'console_item_manage_plugins'), 'url' => $router->urlForRoute('admin_plugins_installed')));

                $event->addItem($item, 1);
            }

            /**
             * My Profile Menu
             *
             * @var $item BASE_CMP_MyProfileConsoleItem
             */
            $item = SC::getClassInstance("BASE_CMP_MyProfileConsoleItem");
            $event->addItem($item, 2);
        }
        else
        {
            $buttonListEvent = new BASE_CLASS_EventCollector(BASE_CMP_ConnectButtonList::HOOK_REMOTE_AUTH_BUTTON_LIST);
            SC::getEventManager()->trigger($buttonListEvent);
            $buttonList = $buttonListEvent->getData();

            $iconListMarkup = '';

            foreach ( $buttonList as $button )
            {
                $iconListMarkup .= '<span class="sc_ico_signin ' . $button['iconClass'] . '"></span>';
            }

            $cmp = new BASE_CMP_SignIn(true);
            $signInMarkup = '<div style="display:none"><div id="base_cmp_floatbox_ajax_signin">' . $cmp->render() . '</div></div>';

            $item = new BASE_CMP_ConsoleItem();
            $item->setControl($signInMarkup . '<span class="sc_signin_label' . (empty($buttonList) ? '' : ' sc_signin_delimiter') . '">' . $language->text('base', 'sign_in_submit_label') . '</span>' . $iconListMarkup);
            $event->addItem($item, 2);

            SC::getDocument()->addOnloadScript("
                $('#".$item->getUniqId()."').click(function(){new SC_FloatBox({ \$contents: $('#base_cmp_floatbox_ajax_signin')});});
            ");

            $item = new BASE_CMP_ConsoleButton($language->text('base', 'console_item_sign_up_label'), SC::getRouter()->urlForRoute('base_join'));
            $event->addItem($item, 1);
        }

        $item = new BASE_CMP_ConsoleSwitchLanguage();
        $event->addItem($item, 0);
    }

    public function defaultPing( BASE_CLASS_ConsoleDataEvent $event )
    {
        $event->setItemData('console', array(
            'time' => time()
        ));
    }

    public function ping( SC_Event $originalEvent )
    {
        $data = $originalEvent->getParams();

        $event = new BASE_CLASS_ConsoleDataEvent('console.ping', $data, $data);
        $this->defaultPing($event);

        SC::getEventManager()->trigger($event);

        $data = $event->getData();
        $originalEvent->setData($data);
    }

    public function init()
    {
        SC::getEventManager()->bind(BASE_CTRL_Ping::PING_EVENT . '.consoleUpdate', array($this, 'ping'));
        SC::getEventManager()->bind('console.collect_items', array($this, 'collectItems'));
    }
}