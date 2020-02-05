<?php

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

        return $arr;
    }
}
