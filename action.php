<?php
/*
 * Yurii's Gantt Plugin
 *
 * Copyright (C) 2020 Yurii K.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses
 */

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
        if ($event->data !== 'plugin_' . $this->getPluginName()) {
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
