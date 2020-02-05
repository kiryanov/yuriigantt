<?php
namespace dokuwiki\plugin\yuriigantt\src\Driver\Embedded;

use dokuwiki\Extension\SyntaxPlugin;
use dokuwiki\plugin\yuriigantt\src\Driver\Embedded;
use dokuwiki\plugin\yuriigantt\src\Driver\Embedded\Handler;

/**
 * Simplified renderer version for internal usage only!
 */
final class Renderer
{
    public $doc = '';

    /**
     * Render data without change
     *
     * @param string $data
     */
    public function raw(string $data)
    {
        $this->doc .= $data;
    }


    /**
     * Returns the format produced by this renderer.
     *
     * Has to be overidden by sub classes
     *
     * @return string
     */
    public function getFormat()
    {
        return Embedded::MODE;
    }


    /**
     * Handle plugin rendering
     *
     * Most likely this needs NOT to be overwritten by sub classes
     *
     * @param string $name  Plugin name
     * @param mixed  $data  custom data set by handler
     * @param string $state matched state if any
     * @param string $match raw matched syntax
     */
    public function plugin($name, $data, $state = '', $match = '') {
        /** @var SyntaxPlugin $plugin */
        $plugin = plugin_load('syntax', $name);
        if($plugin != null) {
            $plugin->render($this->getFormat(), $this, $data);
        }
    }
}
