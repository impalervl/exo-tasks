<?php

declare(strict_types=1);

namespace App\Dto;

class DesignDto
{
    public function __construct(
        public int $id,
        public string $name,
    ){}

    public static function fromArray(array $data): self
    {
        return new self($data['designId'], $data['designName']);
    }
}
