<?php

namespace GustavoSantarosa\LaravelToolPack;

trait SetSchema
{
    public function initializeSetSchema()
    {
        $this->setSchema();
    }

    public function setSchema()
    {
        $this->setTable(strtolower(explode('\\', static::class)[3]).'.'.$this->getTable());
    }
}
