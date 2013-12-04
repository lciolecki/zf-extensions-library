<?php

namespace Extlib\Cli;

/**
 * Abstract, base class of Console line interface
 * 
 * @category    Extlib
 * @package     Extlib\Cli
 * @author      Lukasz Ciolecki <ciolecki.lukasz@gmail.com>
 * @copyright   Copyright (c) 2013 Lukasz Ciolecki (mart)
 */
class Action extends CliAbstract
{

    /**
     * Action name
     *
     * @var string 
     */
    protected $action;

    /**
     * Initialized method
     * 
     * @return \Extlib\Cli\Action
     */
    public function init()
    {
        if (!$this->opts->action) {
            return $this->addError("Parameter 'action' is required.");
        } else {
            $actionName = strtolower($this->opts->action) . 'Action';
            if (!method_exists($this, $actionName)) {
                return $this->addError(sprintf("Action '$actionName' doesn't exists in '%s' cli script.", get_class($this)));
            }

            $this->action = $actionName;
        }

        $this->initialized = true;
        return $this;
    }

    /**
     * Show message in cli
     * 
     * @param string $message
     * @param boolean $eolBefor
     * @return \Extlib\Cli\Action
     */
    public function console($message, $eolBefor = false)
    {
        $befor = '';
        if ($eolBefor) {
            $befor = PHP_EOL;
        }

        echo $befor, $message, PHP_EOL;
        return $this;
    }

    /**
     * Run cli script
     * 
     * @return \Extlib\Cli\Action
     */
    public function run()
    {
        if ($this->initialized) {
            $this->console(sprintf("Start running '%s' method of script '%s'.", $this->opts->action, get_class($this)));
            $this->{$this->action}();
            $this->console(sprintf("End of running '%s' cli script.", get_class($this)), true);
        }

        return $this;
    }

}
