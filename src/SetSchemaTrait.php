<?php

namespace GustavoSantarosa\LaravelToolPack;

trait SetSchemaTrait
{
    protected function bootSetSchemaOnTable()
    {
        $this->setTable(strtolower(explode('\\', static::class)[3]).'.'.$this->getTable());
    }
}
