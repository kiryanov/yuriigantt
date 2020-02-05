<?php
namespace dokuwiki\plugin\yuriigantt\src\Driver\Embedded;

use dokuwiki\Extension\SyntaxPlugin;
use dokuwiki\plugin\yuriigantt\src\Driver\Embedded\CallWriter;

/**
 * We mimic \Doku_Handler without unnecessary code and some changes
 */
final class Handler //extends \Doku_Handler
{

    protected $database;
    protected CallWriter $callWriter;

    public array $calls = [];


    public function __construct()
    {
        $this->callWriter = new CallWriter($this);
        $this->calls = [];
    }


    public function setDatabase($database)
    {
        //TODO: checks
        $this->database = $database;
    }


    public function getDatabase()
    {
        return $this->database;
    }


    /**
     * Processing function. Used by Lexer during parse.
     * We make sure other page content will be in place
     *
     * @param string $content
     * @param int $state
     * @param int $pos
     * @return bool
     */
    public function embedded(string $content, int $state, int $pos)
    {
        if ($state === DOKU_LEXER_UNMATCHED) {
            $this->callWriter->writeCall(['raw', [$content], $pos]);
            return true;
        }

        return false;
    }


    /**
     * Special plugin handler
     *
     * This handler is called for all modes starting with 'plugin_'.
     * An additional parameter with the plugin name is passed. The plugin's handle()
     * method is called here
     *
     * @author Andreas Gohr <andi@splitbrain.org>
     *
     * @param string $match matched syntax
     * @param int $state a LEXER_STATE_* constant
     * @param int $pos byte position in the original source file
     * @param string $pluginname name of the plugin
     * @return bool mode handled?
     */
    public function plugin($match, $state, $pos, $pluginname){
        $data = array($match);
        /** @var SyntaxPlugin $plugin */
        $plugin = plugin_load('syntax',$pluginname);
        if($plugin != null){
            $data = $plugin->handle($match, $state, $pos, $this);
        }
        if ($data !== false) {
            $this->addPluginCall($pluginname,$data,$state,$pos,$match);
        }
        return true;
    }


    /**
     * Similar to addCall, but adds a plugin call
     *
     * @param string $plugin name of the plugin
     * @param mixed $args arguments for this call
     * @param int $state a LEXER_STATE_* constant
     * @param int $pos byte position in the original source file
     * @param string $match matched syntax
     */
    public function addPluginCall($plugin, $args, $state, $pos, $match) {
        $call = array('plugin',array($plugin, $args, $state, $match), $pos);
        $this->callWriter->writeCall($call);
    }
}
