<?php

class ADMIN_CMP_MobileNavigationItem extends SC_Component
{
    public function __construct( $options ) 
    {
        parent::__construct();
        
        $this->assign("item", $options);
    }
}
