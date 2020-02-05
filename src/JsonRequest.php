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

namespace dokuwiki\plugin\yuriigantt\src;

use dokuwiki\plugin\yuriigantt\src\Driver\DriverInterface;
use dokuwiki\plugin\yuriigantt\src\Entities\Link;
use dokuwiki\plugin\yuriigantt\src\Entities\Task;



class JsonRequest
{
    const PERMISSIONS = AUTH_EDIT | AUTH_UPLOAD | AUTH_ADMIN;

    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_CREATE = 'create';
    const ENTITY_TASK = 'task';
    const ENTITY_LINK = 'link';

    protected $payload;
    protected $driver;
    protected $csrf;


    public function __construct(DriverInterface $driver, ?string $csrf, ?string $payload)
    {
        $this->csrf = $csrf;
        $this->driver = $driver;
        $this->payload = json_decode($payload);
    }


    protected function checkCSRF(&$error)
    {
        if ($this->csrf !== getSecurityToken()) {
            $error = $this->error("Invalid CSRF token {$this->csrf}");
            return false;
        }

        return true;
    }


    public function handle()
    {
        if (!$this->checkCSRF($error)) {
            return json_encode($error);
        }

        if (!$this->validate($error)) {
            return json_encode($error);
        }

        $this->driver->open($this->payload->pageId);

        switch ($this->payload->action) {
            case self::ACTION_CREATE:
                if ($this->payload->entity === self::ENTITY_TASK) {
                    $task = $this->driver->addTask(new Task($this->payload->data));
                    $responseData = ['action' => 'inserted', 'tid' => $task->id];
                } elseif ($this->payload->entity === self::ENTITY_LINK) {
                    $link = $this->driver->addLink(new Link($this->payload->data));
                    $responseData = ['action' => 'inserted', 'tid' => $link->id];
                }
                break;

            case self::ACTION_DELETE:
                if ($this->payload->entity === self::ENTITY_TASK) {
                    $this->driver->deleteTask($this->payload->id);
                    $responseData = ['action' => 'deleted'];
                } elseif ($this->payload->entity === self::ENTITY_LINK) {
                    $this->driver->deleteLink($this->payload->id);
                    $responseData = ['action' => 'deleted'];
                }
                break;

            case self::ACTION_UPDATE:
                if ($this->payload->entity === self::ENTITY_TASK) {
                    $this->driver->updateTask(new Task($this->payload->data));
                    $responseData = ['action' => 'updated'];
                } elseif ($this->payload->entity === self::ENTITY_LINK) {
                    $this->driver->updateLink(new Link($this->payload->data));
                    $responseData = ['action' => 'updated'];
                }
                break;

            default:
                $responseData = $this->error('Unknown action');
                break;
        }

        return json_encode($responseData);
    }


    protected function validate(&$error)
    {
        if (!$this->payload) {
            $error = $this->error('no payload');
            return false;
        }

        if (empty($this->payload->action) || !in_array($this->payload->action, [self::ACTION_UPDATE, self::ACTION_CREATE, self::ACTION_DELETE])) {
            $error = $this->error('this action is not supported');
            return false;
        }

        if (empty($this->payload->action) || !in_array($this->payload->action, [self::ACTION_UPDATE, self::ACTION_CREATE, self::ACTION_DELETE])) {
            $error = $this->error('this action is not supported');
            return false;
        }

        if (empty($this->payload->pageId) || !page_exists($this->payload->pageId)) {
            $error = $this->error('invalid pageId');
            return false;
        }

        if (!(auth_quickaclcheck(cleanID($this->payload->pageId)) & self::PERMISSIONS)) {
            $error = $this->error('you don\'t have permissions');
            return false;
        }

        return true;
    }


    protected function error($msg)
    {
        return ['action' => 'error', 'msg' => $msg];
    }


}
