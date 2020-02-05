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

namespace dokuwiki\plugin\yuriigantt\src\Driver\Embedded;

use dokuwiki\Extension\SyntaxPlugin;
use dokuwiki\plugin\yuriigantt\src\Driver\Embedded;

/**
 * Simplified renderer version for internal usage only!
 */
final class Renderer extends \Doku_Renderer // NOTE: remove extend when PHP 5.6-7.1 support is dropped
{
    public $doc = '';

    /**
     * Render data without change
     *
     * @param string $data
     */
    public function raw($data)
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
     * @param string $name Plugin name
     * @param mixed $data custom data set by handler
     * @param string $state matched state if any
     * @param string $match raw matched syntax
     */
    public function plugin($name, $data, $state = '', $match = '')
    {
        if ($plugin = plugin_load('syntax', $name)) {
            /** @var SyntaxPlugin $plugin */
            $plugin->render($this->getFormat(), $this, $data);
        }
    }
}
