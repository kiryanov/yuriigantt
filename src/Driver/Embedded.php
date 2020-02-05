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

use dokuwiki\Parsing\Lexer\Lexer;
use dokuwiki\plugin\yuriigantt\src\Driver\Embedded\Handler;
use dokuwiki\plugin\yuriigantt\src\Driver\Embedded\Renderer;
use dokuwiki\plugin\yuriigantt\src\Entities\Link;
use dokuwiki\plugin\yuriigantt\src\Entities\Task;

class Embedded implements DriverInterface
{
    public const DSN = ':embedded:';
    public const MODE = 'embedded';

    protected string $pageId;
    protected Handler $handler;
    protected Lexer $lexer;
    protected bool $isOpen = false;


    /**
     * Open DB connection for the page
     * @param string $pageId
     * @throws \Exception
     */
    public function open($pageId)
    {
        if ($this->isOpen && $this->pageId === $pageId) {
            return;
        }

        if ($this->isOpen && $this->pageId !== $pageId) {
            throw new \Exception('Already open for another page! Close first!');
        }

        $this->handler = new Handler();
        $this->lexer = new Lexer($this->handler, self::MODE);
        Embedded::addLexerPattern($this->lexer, self::MODE);

        $rawPage = rawWiki($pageId);
        $rawPage = $rawPage === false ? false : $this->lexer->parse($rawPage);

        if (!$rawPage) {
            throw new \Exception('Failed to open dataset for page ' . $pageId);
        }

        $this->pageId = $pageId;
        $this->isOpen = true;
    }


    /**
     * Close connection
     */
    public function close()
    {
        $this->handler = $this->lexer = $this->pageId = null;
        $this->isOpen = false;
    }


    protected function checkOpen()
    {
        if (!$this->isOpen) {
            throw new \Exception("Database MUST BE open first!");
        }
    }


    public static function emptyDatabase()
    {
        return <<<TXT
~~~~GANTT~~~~

~~~~~~~~~~~
TXT;
    }


    public static function initDatabase($pageId)
    {
        $database = (object)[
            'pageId' => $pageId,
            'version' => '1.0',
            'dsn' => Embedded::DSN,
            'increment' => [
                'task' => 1,
                'link' => 1,
            ],
            'gantt' => [
                'data' => [],
                'links' => [],
            ]
        ];

        if ($rawPage = io_readFile(wikiFN($pageId))) {
            $rawPage = str_replace(self::emptyDatabase(), self::embed($database), $rawPage);
            io_saveFile(wikiFN($pageId), $rawPage);
        }

        return $database;
    }


    protected function flush()
    {
        $this->checkOpen();
        $renderer = new Renderer();

        foreach ($this->handler->calls as $instruction) {
            call_user_func_array([&$renderer, $instruction[0]], $instruction[1] ? $instruction[1] : []);
        }

        file_put_contents(__DIR__ . '/test_page_output.txt', $renderer->doc);

        io_saveFile(wikiFN($this->pageId), $renderer->doc);
    }


    /**
     * Returns code ready for embedding into wiki Page
     *
     * @param \stdClass $database
     * @return string
     */
    public static function embed(\stdClass $database)
    {
        $embedded = json_encode($database, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return <<<CODE
~~~~GANTT~~~~
$embedded
~~~~~~~~~~~
CODE;
    }

    public static function addLexerPattern(Lexer $lexer, string $mode)
    {
        $lexer->addSpecialPattern('~~~~GANTT~~~~\n.*?\n~~~~~~~~~~~', $mode, 'plugin_yuriigantt');
    }


    protected function getDatabase()
    {
        $this->checkOpen();
        return $this->handler->getDatabase();
    }


    /**
     * {@inheritdoc}
     */
    public function updateLink(Link $link)
    {
        $this->checkOpen();
        $database =& $this->getDatabase();
        $links =& $database->gantt->links;

        for ($i = 0; $i < count($links); $i++) {
            if ($links[$i]->id == $link->id) {
                $links[$i] = $link;
                break;
            }
        }

        $this->flush();

        return $link;
    }


    /**
     * {@inheritdoc}
     */
    public function deleteLink($id)
    {
        $this->checkOpen();
        $database =& $this->getDatabase();
        $links =& $database->gantt->links;

        for ($i = 0; $i < count($links); $i++) {
            if ($links[$i]->id == $id) {
                unset($links[$i]);
                $links = array_values($links);
                break;
            }
        }

        $this->flush();
    }


    /**
     * {@inheritdoc}
     */
    public function addLink(Link $link)
    {
        $this->checkOpen();
        $database =& $this->getDatabase();
        $link->id = $database->increment->link++;
        $database->gantt->links[] = $link;

        $this->flush();

        return $link;
    }


    /**
     * {@inheritdoc}
     */
    public function updateTask(Task $task)
    {
        $this->checkOpen();
        $database =& $this->getDatabase();
        $tasks =& $database->gantt->data;

        for ($i = 0; $i < count($tasks); $i++) {
            if ($tasks[$i]->id == $task->id) {
                $tasks[$i] = $task;
                break;
            }
        }

        $this->flush();

        return $task;
    }


    /**
     * {@inheritdoc}
     */
    public function deleteTask($id)
    {
        $this->checkOpen();
        $database =& $this->getDatabase();
        $tasks =& $database->gantt->data;
        $tasks = array_column($tasks, null, 'id'); //re-index by ID
        $links =& $database->gantt->links;

        $deleteLinks = function ($taskId) use (&$links) {
            /** @var Link[] $links */
            foreach ($links as &$link) {
                if (in_array($taskId, [$link->source, $link->target])) {
                    $link = null;
                }
            }

            $links = array_values(array_filter($links));
        };

        $deleteChildren = function ($parentId) use (&$tasks, &$deleteChildren, $deleteLinks) {
            foreach ($tasks as &$task) {
                if ($task->parent == $parentId) {
                    $deleteChildren($task->id);
                    $deleteLinks($task->id);
                    $task = null;
                }
            }
        };

        $deleteChildren($id);
        $tasks[$id] = null;
        $tasks = array_values(array_filter($tasks));
        $deleteLinks($id);

        $this->flush();
    }


    /**
     * {@inheritdoc}
     */
    public function addTask(Task $task)
    {
        $this->checkOpen();
        $database =& $this->getDatabase();
        $task->id = $database->increment->task++;
        $database->gantt->data[] = $task;

        $this->flush();

        return $task;
    }

}
