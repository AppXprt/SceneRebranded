<?php

trait SC_Singleton
{
    private static $instance;

    public static function getInstance()
    {
        if ( static::$instance == null )
        {
            try
            {
                static::$instance = SC::getClassInstance(static::class);
            }
            catch ( ReflectionException $ex )
            {
                static::$instance = new static();
            }
        }

        return static::$instance;
    }

    private function __construct()
    {
    }
}
