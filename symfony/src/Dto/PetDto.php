<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class PetDto
{
    public function __construct(
        public int   $id,
        public string $name,
        public string $species
    )
    {}

    public function toArray(): array
    {
        return [
            'id'      => $this->id,
            'name'    => $this->name,
            'species' => $this->species,
        ];
    }

}
