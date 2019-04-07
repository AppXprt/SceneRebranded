<?php

/**
 * EXHIBIT A. Common Public Attribution License Version 1.0
 * The contents of this file are subject to the Common Public Attribution License Version 1.0 (the “License”);
 * you may not use this file except in compliance with the License. You may obtain a copy of the License at
 * http://www.scene.org/license. The License is based on the Mozilla Public License Version 1.1
 * but Sections 14 and 15 have been added to cover use of software over a computer network and provide for
 * limited attribution for the Original Developer. In addition, Exhibit A has been modified to be consistent
 * with Exhibit B. Software distributed under the License is distributed on an “AS IS” basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for the specific language
 * governing rights and limitations under the License. The Original Code is Scene software.
 * The Initial Developer of the Original Code is Scene Foundation (http://www.scene.org/foundation).
 * All portions of the code written by Scene Foundation are Copyright (c) 2011. All Rights Reserved.

 * EXHIBIT B. Attribution Information
 * Attribution Copyright Notice: Copyright 2011 Scene Foundation. All rights reserved.
 * Attribution Phrase (not exceeding 10 words): Powered by Scene community software
 * Attribution URL: http://www.scene.org/
 * Graphic Image as provided in the Covered Code.
 * Display of Attribution Information is required in Larger Works which are defined in the CPAL as a work
 * which combines Covered Code or portions thereof with code not governed by the terms of the CPAL.
 */

/**
 * The class works with default feedback system.
 * 
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package sc_core
 * @method static SC_Feedback getInstance()
 * @since 1.0
 */
class SC_Feedback
{
    /* feedback messages types */
    const TYPE_ERROR = 'error';
    const TYPE_INFO = 'info';
    const TYPE_WARNING = 'warning';

    use SC_Singleton;
    
    /**
     * @var array
     */
    private $feedback;

    /**
     * Constructor.
     */
    private function __construct()
    {
        $session = SC::getSession();

        if ( $session->isKeySet('sc_messages') )
        {
            $this->feedback = $session->get('sc_messages');
            $session->delete('sc_messages');
        }
        else
        {
            $this->feedback = array(
                'error' => array(),
                'info' => array(),
                'warning' => array()
            );
        }
    }

    /**
     * Adds message to feedback.
     *
     * @param string $message
     * @param string $type
     * @return SC_Feedback
     */
    private function addMessage( $message, $type = 'info' )
    {
        if ( $type !== self::TYPE_ERROR && $type !== self::TYPE_INFO && $type !== self::TYPE_WARNING )
        {
            throw new InvalidArgumentException('Invalid message type `' . $type . '`!');
        }

        $this->feedback[$type][] = $message;

        return $this;
    }

    /**
     * Adds error message to feedback.
     *
     * @param string $message
     */
    public function error( $message )
    {
        $this->addMessage($message, self::TYPE_ERROR);
    }

    /**
     * Adds info message to feedback.
     *
     * @param string $message
     */
    public function info( $message )
    {
        $this->addMessage($message, self::TYPE_INFO);
    }

    /**
     * Adds warning message to feedback.
     *
     * @param string $message
     */
    public function warning( $message )
    {
        $this->addMessage($message, self::TYPE_WARNING);
    }

    /**
     * Returns whole list of registered messages.
     *
     * @return array
     */
    public function getFeedback()
    {
        $feedback = $this->feedback;

        $this->feedback = null;

        return $feedback;
    }

    /**
     * System method. Don't call it.
     */
    public function __destruct()
    {
        if ( $this->feedback !== null )
        {
            SC::getSession()->set('sc_messages', $this->feedback);
        }
    }
}

