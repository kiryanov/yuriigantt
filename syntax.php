<?php

use \dokuwiki\Extension\SyntaxPlugin;
use dokuwiki\plugin\yuriigantt\src\Driver\Embedded;
use dokuwiki\plugin\yuriigantt\src\Driver\Embedded\Handler;

class syntax_plugin_yuriigantt extends SyntaxPlugin
{
    const VIEW = 'dhtmlxgantt';

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode)
    {
        if ($mode === 'base') {
            Embedded::addLexerPattern($this->Lexer, $mode);
        }
    }


    /**
     * {@inheritdoc}
     */
    public function getPType()
    {
        return 'block';
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'substition';
    }


    /**
     * {@inheritdoc}
     */
    public function getSort()
    {
        return 1;
    }


    /**
     * @param string $match
     * @param int $state
     * @param int $pos
     * @param Doku_Handler|Handler $handler
     * @return array
     */
    public function handle($match, $state, $pos, $handler)
    {
        global $ID;

        $data = mb_substr($match, mb_strpos($match, "\n") + 1);
        $data = mb_substr($data, 0, mb_strrpos($data, "\n"));
        $database = json_decode($data);

        if (empty($database) && !empty($ID)) {
            $database = Embedded::initDatabase($ID);
        }

        if (!empty($ID) && $ID !== $database->pageId) {
            $database->pageId = $ID;
        }

        //special case for embedded db
        if ($handler instanceof Handler) {
            $handler->setDatabase($database);
        }

        return $database;
    }


    /**
     * Handles the actual output creation.
     *
     * The function must not assume any other of the classes methods have been run
     * during the object's current life. The only reliable data it receives are its
     * parameters.
     *
     * The function should always check for the given output format and return false
     * when a format isn't supported.
     *
     * $renderer contains a reference to the renderer object which is
     * currently handling the rendering. You need to use it for writing
     * the output. How this is done depends on the renderer used (specified
     * by $format
     *
     * The contents of the $data array depends on what the handler() function above
     * created
     *
     * @param string $format output format being rendered
     * @param Doku_Renderer|\dokuwiki\plugin\yuriigantt\src\Driver\Embedded\Renderer $renderer the current renderer object
     * @param array $data data created by handler()
     * @return  boolean                 rendered correctly? (however, returned value is not used at the moment)
     */
    public function render($format, $renderer, $data)
    {
        if (strtolower($format) === 'xhtml') {
            return $this->renderXHtml($renderer, $data);
        } elseif (strtolower($format) === Embedded::MODE) {
            $renderer->raw(Embedded::embed($data));
            return true;
        }

        return false;
    }


    protected function renderXHtml(Doku_Renderer $renderer, $data)
    {
        if ($data->dsn !== Embedded::DSN) {
            // TODO: make another route
           // return true;
        }

        $html = $this->viewRender(self::VIEW, ['database' => $data, 'pluginName' => $this->getPluginName()]);
        $renderer->html($html);

        return true;
    }


    protected function viewRender($view, array $params = [])
    {
        ob_start();
        extract($params);
        require __DIR__ . "/src/Views/{$view}.php";
        return ob_get_clean();
    }

}
