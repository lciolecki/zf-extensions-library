<?php

namespace Extlib\Mail\Message;

/**
 * Mail message interface
 *
 * @category    Extlib
 * @package     Extlib\Mail
 * @subpackage  Extlib\Mail\Message
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2011 Lukasz Ciolecki (mart)
 */
interface MessageInterface
{

    /**
     * Generate a content message for email
     */
    public function create($scriptName = null);
}
