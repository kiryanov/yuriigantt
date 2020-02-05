<?php

use \dokuwiki\Extension\ActionPlugin;
use \dokuwiki\plugin\yuriigantt\src\JsonRequest;
use dokuwiki\plugin\yuriigantt\src\Driver\Embedded;

class action_plugin_yuriigantt extends ActionPlugin
{
    /**
     * {@inheritdoc}
     */
    public function register(\Doku_Event_Handler $controller)
    {
        $controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE', $this, 'callback');
    }


    public function callback(Doku_Event $event, $param)
    {
        if ($event->data !== 'plugin_yuriigantt') {
            return;
        }

        /** @var DokuWiki_Auth_Plugin $auth */
        global $auth;

        //no other ajax call handlers needed
        $event->stopPropagation();
        $event->preventDefault();

        header('Content-Type: application/json');
        echo (new JsonRequest(new Embedded(), $GLOBALS['INPUT']->param('payload')))->handle();
    }
}
