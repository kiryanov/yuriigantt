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

class Task implements \JsonSerializable
{
    const DATE_FORMAT = 'd-m-Y H:i';

    /** @var int primary */
    public int $id;
    public string $text;
    public \DateTime $start_date;
    public int $duration;
    public float $progress;
    public int $parent;
    public bool $open;
    public int $order;
    public ?string $target;


    public function __construct(?\stdClass $data)
    {
        if (!$data) {
            return;
        }

        $this->id = $data->id;
        $this->text = $data->text;
        $this->start_date = \DateTime::createFromFormat(self::DATE_FORMAT, $data->start_date);
        $this->duration = $data->duration;
        $this->progress = $data->progress;
        $this->parent = (int)$data->parent;
        $this->open = (bool)($data->open ?? true);
        $this->order = (int)$data->order;
        $this->target = $data->target ?? null;
    }


    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $arr = (array)$this;
        $arr['start_date'] = $this->start_date->format(self::DATE_FORMAT);
        $arr['progress'] = round($this->progress, 4);

        return $arr;
    }
}
