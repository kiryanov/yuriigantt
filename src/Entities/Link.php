<?php

namespace dokuwiki\plugin\yuriigantt\src\Entities;

class Link
{
    /** @var int primary */
    public int $id;
    public ?int $source;
    public ?int $target;
    public string $type;


    public function __construct(?\stdClass $data)
    {
        if (!$data) {
            return;
        }

        $this->id = $data->id;
        $this->source = $data->source ?? null;
        $this->target = $data->target ?? null;
        $this->type = $data->type;
    }
}
