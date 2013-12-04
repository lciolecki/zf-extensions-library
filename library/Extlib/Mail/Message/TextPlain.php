<?php

namespace Extlib\Mail\Message;

/**
 * Mail message text plain type
 * 
 * @category    Extlib
 * @package     Extlib\Mail
 * @subpackage  Extlib\Mail\Message
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2011 Lukasz Ciolecki (mart)
 */
class TextPlain implements MessageInterface
{

    /**
     * Implementation Extlib\Mail\Message\MessageInterface returned text
     * 
     * @param string $scriptName
     * @return string|html 
     */
    public function create($scriptName = null)
    {
        
    }

}
