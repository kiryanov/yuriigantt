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

namespace dokuwiki\plugin\yuriigantt\src\Driver;

use dokuwiki\plugin\yuriigantt\src\Entities\Link;
use dokuwiki\plugin\yuriigantt\src\Entities\Task;

interface DriverInterface
{
    /**
     * Open connection for the page
     * @param $pageId
     * @return void
     */
    public function open($pageId);

    /**
     * @param Link $link
     * @return Link
     */
    public function updateLink(Link $link);

    /**
     * @param $id link ID
     * @return void
     */
    public function deleteLink($id);

    /**
     * @param Link $link
     * @return Link
     */
    public function addLink(Link $link);

    /**
     * @param Task $task
     * @return Task
     */
    public function updateTask(Task $task);

    /**
     * @param $id
     * @return void
     */
    public function deleteTask($id);

    /**
     * @param Task $task
     * @return Task
     */
    public function addTask(Task $task);

}
