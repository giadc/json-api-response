<?php

namespace App;

use Giadc\JsonApiResponse\Interfaces\JsonApiResource;

class TestEntity implements JsonApiResource
{
    private int|string $id;

    private string $name;

    private string $title;

    public function __construct($id, string $name, string $title = 'asdf')
    {
        $this->id   = $id;
        $this->name = $name;
        $this->title = $title;
    }

    /**
     * {@inheritDoc}
     */
    public function id(): int|string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function title(): string
    {
        return $this->title;
    }

    /**
     * {@inheritDoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'title' => $this->title,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function getResourceKey(): string
    {
        return 'tests';
    }
}
