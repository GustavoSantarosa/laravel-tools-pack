<?php

namespace App\Repositories;

class BaseModelRepository
{
    public function __construct(object|array $data)
    {
        $data = \is_object($data) ? (array) $data : $data;

        foreach ($data as $index => $value) {
            $this->$index = $value;
        }
    }
}
