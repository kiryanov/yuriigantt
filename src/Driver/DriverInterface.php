<?php

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
