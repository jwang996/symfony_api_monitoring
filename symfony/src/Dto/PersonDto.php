<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class PersonDto
{
    public function __construct(
        public int $id,
        public string $firstName,
        public string $lastName,
        public string $email
    )
    {}

    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'firstName' => $this->firstName,
            'lastName'  => $this->lastName,
            'email'     => $this->email,
        ];
    }
}
