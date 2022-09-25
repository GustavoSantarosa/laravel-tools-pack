<?php

namespace GustavoSantarosa\LaravelToolPack;

trait SetSchema
{
    public function __construct()
    {
        $this->setSchema();
    }

    public function SetSchema()
    {
        $this->setTable(strtolower(explode('\\', static::class)[3]).'.'.$this->getTable());
    }
}
