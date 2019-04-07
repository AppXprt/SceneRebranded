<?php

class BASE_MCMP_WidgetMenu extends SC_MobileComponent
{

    public function __construct( $items )
    {
        parent::__construct();

        $this->assign('items', $items);
        SC::getDocument()->addOnloadScript('SCM.initWidgetMenu(' . json_encode($items) . ')');
    }
}