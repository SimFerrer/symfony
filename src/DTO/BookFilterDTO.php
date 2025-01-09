<?php

namespace App\DTO;

class BookFilter
{
    public ?string $value = null;


    public static function fromRequest(array $query): self
    {
        $filter = new self();
        $filter->value = $query['search'] ?? null;
        return $filter;
    }
}
