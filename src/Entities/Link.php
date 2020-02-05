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

namespace dokuwiki\plugin\yuriigantt\src\Entities;

class Link
{
    /** @var int primary */
    public $id;
    /** @var int|null */
    public $source;
    /** @var int|null */
    public $target;
    /** @var string */
    public $type;


    /**
     * Link constructor.
     * @param \stdClass|null $data
     */
    public function __construct($data)
    {
        if (!$data) {
            return;
        }

        $this->id = $data->id;
        $this->source = isset($data->source) ? $data->source : null;
        $this->target = isset($data->target) ? $data->target : null;
        $this->type = $data->type;
    }
}
